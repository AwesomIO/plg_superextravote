<?php
/**
 * @package     ${NAMESPACE}
 * @subpackage
 *
 * @copyright   A copyright
 * @license     A "Slug" license name e.g. GPL2
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;

class plgContentSuperExtraVoteInstallerScript
{
    function install($parent) {
        echo JText::_('PLG_CONTENT_SUPEREXTRAVOTE_ENABLED_0');
    }


    function update($parent) {
        echo JText::_('PLG_CONTENT_SUPEREXTRAVOTE_ENABLED_'.plgContentSuperExtraVoteInstallerScript::isEnabled());
    }

    function isEnabled() {
        $db = Factory::getDbo();
        $query = $db->getQuery(true)
            ->select($db->quoteName('enabled'))
            ->from($db->quoteName('#__extensions'))
            ->where($db->quoteName('element') . ' = ' . $db->quote('superextravote'))
            ->where($db->quoteName('folder') . ' = ' . $db->quote('content'));
        $db->setQuery($query);

        return $db->loadResult();
    }
}