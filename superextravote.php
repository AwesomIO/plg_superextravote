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

    public function __construct(&$subject, $config)
    {
        parent::__construct($subject, $config);

        $this->votingPosition = $this->params->get('position', 'top');
        $this->app = Factory::getApplication();
        $this->db = Factory::getDbo();
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
        $parts = explode('.', $context);

        //var_dump($row->catid);
        //var_dump($row->id);

        if ($parts[0] !== 'com_content')
        {
            return false;
        }

        if (empty($params) || !$params->get('show_vote', null))
        {
            return '';
        }

        // Load plugin language files only when needed (ex: they are not needed if show_vote is not active).
        $this->loadLanguage();

        $html = '*-*-*-*-*';

        return $html;
    }
}
