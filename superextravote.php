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
use Joomla\Registry\Registry;

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
     * @var    Registry
     * @since    3.8.0
     */
    protected $vote;



    public function __construct(&$subject, $config)
    {
        parent::__construct($subject, $config);

        $this->votingPosition = $this->params->get('position', 'top');
        $this->app = Factory::getApplication();
        $this->db = Factory::getDbo();
        $this->user = Factory::getUser();
        $this->vote = new Registry();
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

        if ($parts[0] !== 'com_content')
        {
            return false;
        }

        if (empty($params) || !$params->get('show_vote', null)  || $context == 'com_content.categories')
        {
            return '';
        }

        if(!($this->prepareVoting()))
        {
            return '';
        }

        // Load plugin language files only when needed (ex: they are not needed if show_vote is not active).
        $this->loadLanguage();

        // Render the layout
        ob_start();
        $html = ob_get_clean();
        // Get the path for the voting form layout file
        $path = PluginHelper::getLayoutPath('content', 'superextravote', 'vote');
        // Render the layout
        ob_start();
        include $path;
        $html .= ob_get_clean();
        return $html;
    }

    private function prepareVoting(){
        if(!$this->article_id){
            return false;
        }
        $rating_count=$rating_sum=$rating_mean=0;
        $user_voted = false;

        $query=$this->db->getQuery(true);
        $query->select($this->db->quoteName(array('rating_sum', 'rating_count')))
            ->from($this->db->quoteName('#__content_rating'))
            ->where($this->db->quoteName('content_id') .' = '. $this->db->quote($this->article_id));
        $this->db->setQuery($query);


        $vote=$this->db->loadObject();


        $query->clear();

        if($vote)
        {
            $rating_sum = intval($vote->rating_sum);
            $rating_count = intval($vote->rating_count);
            if( $rating_count!=0 )
            {
                $rating_mean = ceil(($rating_sum / $rating_count) / 0.5) * 0.5;
            }
        }

        $this->vote->set('rating_count', $rating_count);
        $this->vote->set('rating_sum', $rating_sum);
        $this->vote->set('rating_mean', $rating_mean);


        if($this->user->id)
        {
            $query->select('COUNT(*)')
                ->from($this->db->quoteName('#__content_superextravote'))
                ->where(
                    $this->db->quoteName('user_id') .'='. $this->user->id .' AND '.
                    $this->db->quoteName('content_id') .'='. $this->article_id
                );
            $this->db->setQuery($query);
            $user_voted=$this->db->getNumRows() ? true : false;
        }

        $this->vote->set('user_voted', $user_voted);

        return true;
    }
}
