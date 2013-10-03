<?php
/**
 * @copyright	Copyright (C) 2013 Geraint Brown - MindYourBizOnline, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;

// Import CORE joomla functionality & Helpers
jimport('joomla.utilities.date'); //Date Functionality
jimport( 'joomla.user.helper' ); //User helper
jimport( 'joomla.plugin.helper' ); //Plugin Helper


/**
 * Custom Fitness User Plugin.
 *
 * @package		Joomla.Plugin
 * @subpackage	User.profile
 * @version		1.6
 */
class plgUserFitness extends JPlugin
{
    /**
     * Constructor
     *
     * @access      protected
     * @param       object  $subject The object to observe
     * @param       array   $config  An array that holds the plugin configuration
     * @since       1.5
     */
    public function __construct(& $subject, $config)
    {
            parent::__construct($subject, $config);
   
    }

    
    function onUserAfterSave($user, $isnew, $success, $msg){
        return $this -> addUserToFitnessComponent($user, $isnew, $success, $msg);
    }
    
    
    function addUserToFitnessComponent($user, $isnew, $success, $msg){
        $db = JFactory::getDbo();
        
        $gid = $user['groups'][0];
        
        $query = "SELECT * FROM #__fitness_user_groups WHERE gid='$gid' AND state='1'";
        $db->setQuery($query);
        if(!$db->query()) {
            JError::raiseError($db->getErrorMsg());
        }
        $group_trainers = $db->loadObject();
        
        $data = new stdClass();
        $data->user_id = $user['id'];
        $data->primary_trainer = $group_trainers->primary_trainer;
        $data->other_trainers = $group_trainers->other_trainers;
        $data->state = '1';
        
        $insert = $db->insertObject('#__fitness_clients', $data, 'id');
        if (!$insert) {
            JError::raiseError($db->getErrorMsg());
        }
        return true;
    }
    
    
}
