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

require_once  JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_fitness' . DS .'helpers' . DS . 'fitness.php';
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
        
        $group_id = $user['groups'][0];
        
        $user_id = $user['id'];
        
        
        
        if(FitnessHelper::is_trainer($user_id)) {
            return true;
        }
        
        if(FitnessHelper::is_superuser($user_id)) {
            return true;
        }
        
        $helper  = new FitnessHelper();
        
        $user_group_data = $helper->getBusinessByUserGroup($group_id);
        
        if(!$user_group_data['success']) {
            JError::raiseError($user_group_data['message']);
        }
        $user_group_data = $user_group_data['data'];
        
        $query = "SELECT * FROM #__fitness_user_groups WHERE group_id='$group_id' AND state='1'";
        $db->setQuery($query);
        if(!$db->query()) {
            JError::raiseError($db->getErrorMsg());
        }
        $group_trainers = $db->loadObject();
        
        $data = new stdClass();
        $data->user_id = $user['id'];
        $data->business_profile_id = $user_group_data->business_profile_id;
        $data->primary_trainer = $group_trainers->primary_trainer;
        $data->other_trainers = $group_trainers->other_trainers;
        $data->state = '1';
        
        //check if user exists in fitness clients table
        $query = "SELECT id FROM #__fitness_clients WHERE user_id='$user_id'";
        $db->setQuery($query);
        if(!$db->query()) {
            JError::raiseError($db->getErrorMsg());
        }
        $data->id = $db->loadResult();
        
        if($data->id) {
            $insert = $db->updateObject('#__fitness_clients', $data, 'id');
        } else {
            $insert = $db->insertObject('#__fitness_clients', $data, 'id');
        }
        
        
        if (!$insert) {
            JError::raiseError($db->getErrorMsg());
        }
        return true;
    }
    
    
}
