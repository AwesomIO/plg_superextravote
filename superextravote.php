<?php
/**
 * @package    superExtraVote
 *
 * @author     Artem Vasilev <kern.usr@gmail.com>
 * @copyright  A copyright
 * @license    GNU General Public License version 3 or later; see LICENSE.txt
 * @link       https://awesomio.org
 */

use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Plugin\PluginHelper;

defined('_JEXEC') or die;

/**
 * SuperExtraVote plugin.
 *
 * @package  superExtraVote
 * @since    3.8.0
 */
class plgContentSuperExtraVote extends CMSPlugin
{
	/**
	 * @var    \JApplication
     * @since   3.8.0
	 */
	protected $app;

	/**
	 * @var    \JDatabaseDriver
     * @since    3.8.0
	 */
	protected $db;

	/**
	 * @var    boolean
     * @since    3.8.0
	 */
	protected $autoloadLanguage = true;

    /**
     * @var    string
     * @since    3.8.0
     */
    protected $votingPosition;

    /**
     * @var    int
     * @since    3.8.0
     */
    protected $article_id;

    /**
     * @var    \JHtmlUser
     * @since    3.8.0
     */
    protected $user;

    /**
     * @var    \Joomla\CMS\Document\HtmlDocument
     * @since    3.8.0
     */
    protected $doc;

    public function __construct(&$subject, $config)
    {
        parent::__construct($subject, $config);

        $this->votingPosition = $this->params->get('position', 'top');
        $this->app = Factory::getApplication();
        $this->db = Factory::getDbo();
        $this->user = Factory::getUser();
        $this->doc = Factory::getDocument();
    }

    /**
     * @param   string   $context  The context of the content being passed to the plugin
     * @param   object   &$row     The article object
     * @param   object   &$params  The article params
     * @param   integer  $page     The 'page' number
     *
     * @return  string|boolean  HTML string containing code for the votes if in com_content else boolean false
     *
     * @since   3.8.0
     */
    public function onContentAfterTitle($context, &$row, &$params, $page = 0)
    {
        if ($this->votingPosition !== 'afterTitle')
        {
            return '';
        }

        return $this->displayVotingData($context, $row, $params, $page);
    }

    /**
     * @param   string   $context  The context of the content being passed to the plugin
     * @param   object   &$row     The article object
     * @param   object   &$params  The article params
     * @param   integer  $page     The 'page' number
     *
     * @return  string|boolean  HTML string containing code for the votes if in com_content else boolean false
     *
     * @since   3.8.0
     */
    public function onContentBeforeDisplay($context, &$row, &$params, $page = 0)
    {
        if ($this->votingPosition !== 'beforeContent')
        {
            return '';
        }

        return $this->displayVotingData($context, $row, $params, $page);
    }

    /**
     * @param   string   $context  The context of the content being passed to the plugin
     * @param   object   &$row     The article object
     * @param   object   &$params  The article params
     * @param   integer  $page     The 'page' number
     *
     * @return  string|boolean  HTML string containing code for the votes if in com_content else boolean false
     *
     * @since   3.8.0
     */
    public function onContentAfterDisplay($context, &$row, &$params, $page = 0)
    {
        if ($this->votingPosition !== 'afterContent')
        {
            return '';
        }

        return $this->displayVotingData($context, $row, $params, $page);
    }

    /**
     * @param   string   $context  The context of the content being passed to the plugin
     * @param   object   &$row     The article object
     * @param   object   &$params  The article params
     * @param   integer  $page     The 'page' number
     *
     * @return  string|boolean  HTML string containing code for the votes if in com_content else boolean false
     *
     * @since   3.8.0
     */
    private function displayVotingData($context, &$row, &$params, $page)
    {
        $this->article_id = intval($row->id);

        $parts = explode('.', $context);
        if ($parts[0] !== 'com_content' || ($parts[0] == 'com_content' && $parts[1] == 'categories'))
        {
            return false;
        }

        if (empty($params) || !$params->get('show_vote', null))
        {
            return '';
        }

        $vote = $this->prepareVotingData();

        $path = PluginHelper::getLayoutPath('content', 'superextravote', 'vote');

        // Render the layout
        $this->doc->addStyleSheet(JURI::root(true).'/plugins/content/superextravote/assets/superextravote-style.css');
        $this->doc->addScript(JURI::root(true).'/plugins/content/superextravote/assets/superextravote-script.js');

        $this->doc->addScriptDeclaration("
				var ev_basefolder = '".JURI::base(true)."';
			");

        ob_start();
        include $path;
        $html = ob_get_clean();

        return $html;
    }

    private function prepareVotingData(){

        $query=$this->db->getQuery(true);
        $query->select(
            $this->db->quoteName(array('rating_sum', 'rating_count', 'content_id'))
        )
            ->from($this->db->quoteName('#__content_rating'))
            ->where($this->db->quoteName('content_id') .' = '. $this->db->quote($this->article_id));

        $this->db->setQuery($query);
        $vote=$this->db->loadObject();
        $query->clear();

        if(!$vote){
            $vote = (object) array(
                'rating_sum' => 0,
                'rating_count' => 0,
                'content_id' => $this->article_id
            );
        }

        $vote->rating_sum = intval($vote->rating_sum);
        $vote->rating_count = intval($vote->rating_count);
        $vote->access = false;

        if($this->user->id){
            $query->select('COUNT(*)')
                ->from($this->db->quoteName('#__content_superextravote'))
                ->where(
                    $this->db->quoteName('user_id') .'='. $this->user->id .' AND '.
                    $this->db->quoteName('content_id') .'='. $this->article_id
                );
            $this->db->setQuery($query);
            $count = $this->db->loadResult();
            $query->clear();
            $vote->access = $count ? false : true;
        }

        if(!$vote->rating_count){
            $vote->rating = 0;
        }
        else{
            $vote->rating = ceil(($vote->rating_sum / $vote->rating_count)/0.5)*0.5;
        }


        return $vote;
    }
}
