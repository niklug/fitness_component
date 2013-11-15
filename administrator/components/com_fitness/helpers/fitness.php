<?php
/**
 * @version     1.0.0
 * @package     com_fitness
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Nikolay Korban <niklug@ukr.net> - http://
 */

// No direct access
defined('_JEXEC') or die;

class FitnessFactory {
    
    const SUPERUSER_GROUP_ID = 8;
    const MANAGER_GROUP_ID = 6;
    const REGISTERED_GROUP_ID = 2;
    
    public static $trainers_group_id = null;
    public static $group_id = null;
    public static $is_superuser = null;
    public static $is_trainer = null;
    public static $is_client = null;
    public static $is_primary_administrator = null;
    public static $is_secondary_administrator = null;
    
    
   
    public static function getTrainersGroupId($user_id) {
        
        if (!self::$trainers_group_id) {
            self::$trainers_group_id = self::createTrainersGroupId($user_id);
        }

        return self::$trainers_group_id;
    }
    
    
    public static function getTrainersGroupIdByUser($user_id) {

        return self::createTrainersGroupId($user_id);
    }
    
    
     public static function getCurrentGroupId($user_id) {
        
        if (!self::$group_id) {
            self::$group_id = self::createCurrentGroupId($user_id);
        }

        return self::$group_id;
    }
    
    
    public static function is_superuser($user_id) {
        if(self::createCurrentGroupId($user_id) == self::SUPERUSER_GROUP_ID) {
            return true;
        }
        return false;
    }
    
    
    /* trainer or primary administrator or secondary administrator of Business
     * 
     */
    public static function is_trainer($user_id) {
        
        $is_trainer = false;
        $group_id = self::createCurrentGroupId($user_id);
        $parent_group_id =  self::MANAGER_GROUP_ID;
        $is_trainer = self::isChildGroup($group_id, $parent_group_id);
        return $is_trainer;


    }
    
    
    /* only trainer, not administrator of Business
     * 
     */
    public static function is_simple_trainer($user_id) {
        
        if(!self::is_primary_administrator($user_id) && !self::is_secondary_administrator($user_id) && self::is_trainer($user_id)) {
            return true;
        }
        return false;
    }
    
    
    /* is trainer-administrator of Business
     * 
     */
    public static function is_trainer_administrator($user_id) {
        
        if(self::is_primary_administrator($user_id) || self::is_secondary_administrator($user_id)) {
            return true;
        }
        return false;
    }
    
    
    public static function is_client($user_id) {
        if(self::$is_client == null) {
            $group_id = self::getCurrentGroupId($user_id);
            $parent_group_id =  self::REGISTERED_GROUP_ID;
            self::$is_client = self::isChildGroup($group_id, $parent_group_id);
            return self::$is_client;
        }
        return self::$is_client;
    }
    
    
    public static function is_primary_administrator($user_id) {
        
        if(self::$is_primary_administrator == null) {
            if(!$user_id) {
                $user_id = &JFactory::getUser()->id;
            }
            $primary_administrator_id = self::getAdministratorId('primary_administrator', $user_id);

            if($user_id == $primary_administrator_id) {
                self::$is_primary_administrator = $primary_administrator_id;
            } else {
                self::$is_primary_administrator = false;
            }
            return self::$is_primary_administrator;
        }
        return self::$is_primary_administrator;
    }
    
    
    public static function is_secondary_administrator($user_id) {
   
        if(self::$is_secondary_administrator == null) {
            
            if(!$user_id) {
                $user_id = &JFactory::getUser()->id;
            }
            $secondary_administrator_id = self::getAdministratorId('secondary_administrator', $user_id);
            
            if($user_id == $secondary_administrator_id) {
                self::$is_secondary_administrator = $secondary_administrator_id;
            } else {
                self::$is_secondary_administrator = false;
            }
            return self::$is_secondary_administrator;
        }
        return self::$is_secondary_administrator;
    }
    
    
    public function getAdministratorId($administrator_type, $user_id) {
        
        $group_id = self::getCurrentGroupId($user_id);
        $query = "SELECT $administrator_type FROM #__fitness_business_profiles WHERE group_id='$group_id'  AND state='1'";
        return self::customQuery($query, 0);
    }
    
    
    public static function createTrainersGroupId($user_id) {
        
        $user = &JFactory::getUser($user_id);
        
        $groups = $user->get('groups');
        $user_group_id = array_shift(array_values($groups));

        $query = "SELECT bp.group_id AS trainers_group_id from #__fitness_user_groups AS ug "
                . " INNER JOIN #__fitness_business_profiles AS bp ON bp.id=ug.business_profile_id "
                . " WHERE ug.group_id = '$user_group_id'"
                . " AND ug.state='1'"
                . " AND bp.state='1'";

        $trainers_group_id = self::customQuery($query, 0);
        
        if (!$trainers_group_id) {
            $trainers_group_id = $user_group_id;
        }
        
        if (!$trainers_group_id) {
            JError::raiseWarning( 100, 'No Trainers Group assigned!' );
        }

        return $trainers_group_id;
    }
    
    
    public static function createCurrentGroupId($user_id) {
        
        if(!$user_id) {
            $user_id = &JFactory::getUser()->id;
        }

        $query = "SELECT group_id FROM #__user_usergroup_map WHERE user_id='$user_id'";

        $group_id = self::customQuery($query, 0);
        
        if (!$group_id) {
            JError::raiseWarning( 100, 'User Group not found!' );
        }
        return $group_id;
    }
    
      
    
    public static function isChildGroup($group_id, $parent_group_id) {
        if(!$user_id) {
            $user_id = &JFactory::getUser()->id;
        }

        $query = "SELECT id FROM #__usergroups WHERE (id='$group_id'  AND parent_id='$parent_group_id') OR (id='$group_id'  AND id='$parent_group_id')";

        $group_id = self::customQuery($query, 0);
        
        return $group_id;
    }
    
    
    public static function customQuery($query, $type) {
	$db = & JFactory::getDBO();
        $db->setQuery($query);

        if (!$db->query()) {
            throw new Exception($db->getErrorMsg());
            JError::raiseError($db->getErrorMsg());
        }

        switch ($type) {
            case 0:
                $result = $db->loadResult();
                break;
            case 1:
                $result = $db->loadObjectList();
                break;
            case 2:
                $result = $db->loadObject();
                break;
            case 3:
                $result = $db->loadResultArray();
                break;
            case 4:
                $result = $db->loadRow();
                break;
            case 5:
                $result = $db->query();
                break;
            case 6:
                $result = $db->loadAssocList();
                break;
            default:
                return false;
                break;
        }
        return $result;
     }
     
     public static function getTimeCreated(){
        $config = JFactory::getConfig();
        $date = new DateTime();
        $date->setTimezone(new DateTimeZone($config->getValue('config.offset')));
        return $date->format('Y-m-d H:i:s');
     }

}

/**
 * Fitness helper.
 */
class FitnessHelper extends FitnessFactory
{
    const PENDING_GOAL_STATUS = '1';
    const COMPLETE_GOAL_STATUS = '2';
    const INCOMPLETE_GOAL_STATUS = '3';
    const EVELUATING_GOAL_STATUS = '4';
    const INPROGRESS_GOAL_STATUS = '5';
    const ASSESSING_GOAL_STATUS = '6';

    const ADMINISTRATOR_USERGROUP = 'Super Users';
    /**
     * Configure the Linkbar.
     */
    public static function addSubmenu($vName = '', $view)
    {
        if($view == 'calendar') {
            JSubMenuHelper::addEntry(
                $vName,
                'index.php?option=com_multicalendar&view=admin&task=admin',
                $vName == $vName
            );
            return;
        }
        JSubMenuHelper::addEntry(
            $vName,
            'index.php?option=com_fitness&view='. $view,
            $vName == $vName
        );

    }

    /**
     * Gets a list of the actions that can be performed.
     *
     * @return	JObject
     * @since	1.6
     */
    public static function getActions()
    {
            $user	= JFactory::getUser();
            $result	= new JObject;

            $assetName = 'com_fitness';

            $actions = array(
                    'core.admin', 'core.manage', 'core.create', 'core.edit', 'core.edit.own', 'core.edit.state', 'core.delete'
            );

            foreach ($actions as $action) {
                    $result->set($action, $user->authorise($action, $assetName));
            }

            return $result;
    }


    ////////////////////////////////////////////////////////////////////////
    public function sendEmail($recipient, $Subject, $body) {

        $mailer = & JFactory::getMailer();

        $config = new JConfig();

        $sender = array($config->mailfrom, $config->fromname);

        $mailer->setSender($sender);

        //$recipient = 'npkorban@mail.ru';

        $mailer->addRecipient($recipient);

        $mailer->setSubject($Subject);

        $mailer->isHTML(true);

        $mailer->setBody($body);

        $send = & $mailer->Send();

        return $send;
     }


    function getContentCurl($url) {
        $ret['success'] = true;
        if(!function_exists('curl_version')) {
            throw new Exception('cURL not anabled');
            $ret['success'] = false;
            $ret['message'] = 'cURL not anabled';
            return $ret;
        }

        $ch = curl_init();
        curl_setopt($ch,CURLOPT_USERAGENT,'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $contents = curl_exec ($ch);
        curl_close ($ch);
        $ret['data'] = $contents;
        return $ret;
    }

    /**
     * 
     * @param type $client_id
     * @param type $type :  primary, secondary, all
     * @return type
     */
    function  getClientTrainers($client_id, $type) {
        $status['success'] = 1;
        $db = & JFactory::getDBO();
        $user = &JFactory::getUser();

        $query = "SELECT primary_trainer, other_trainers FROM #__fitness_clients WHERE user_id='$client_id' AND state='1'";
        $db->setQuery($query);
        
        if (!$db->query()) {
            throw new Exception($db->stderr());
            $status['success'] = 0;
            $status['message'] = $db->stderr();
            return $status;
        }
        $primary_trainer= $db->loadResultArray(0);
        $other_trainers = $db->loadResultArray(1);
        $other_trainers = explode(',', $other_trainers[0]);
        $all_trainers_id = array_unique(array_merge($primary_trainer, $other_trainers));

        if($type == 'secondary') {
            $all_trainers_id = $other_trainers;
        }
        
        if($type == 'primary') {
            $all_trainers_id = $primary_trainer;
        }

        if(!$all_trainers_id) {
            $msg = 'No trainers assigned to this client.';
            throw new Exception($msg);
            $status['success'] = 0;
            $status['message'] = $msg;
        }

        $result = array( 'status' => $status, 'data' => $all_trainers_id);
        
        return $result;
    }
    
    /**
     * 
     * @param type $id
     * @param type $goal_type : 1 -primary, 2 - mini
     * @return array
     */
    function getClientIdByGoalId($id, $goal_type) {
        $result['success'] = true;
        $db = & JFactory::getDBO();
        $query = "SELECT user_id, (SELECT primary_trainer FROM #__fitness_clients WHERE user_id=#__fitness_goals.user_id ) trainer_id FROM #__fitness_goals WHERE id='$id' AND state='1'";
        if($goal_type == '2') {
            $query = "SELECT pg.user_id, c.primary_trainer AS trainer_id FROM #__fitness_mini_goals AS mg
                LEFT JOIN #__fitness_goals AS pg ON pg.id=mg.primary_goal_id
                LEFT JOIN #__fitness_clients AS c ON c.user_id=pg.user_id
                WHERE mg.id='$id' AND mg.state='1'
            ";
        }
        $db->setQuery($query);
        if (!$db->query()) {
            throw new Exception($db->stderr());
            $result['success'] = false;
            $result['message'] = $db->stderr();
            return $result;
        }
        $client_id = $db->loadResultArray(0);
        $trainer_id = $db->loadResultArray(1);

        $result['data'] = array('client_id' => $client_id[0], 'trainer_id' => $trainer_id[0]);
        
        return $result;
    }
    
    public function getGoal($id, $table) {
        $ret['success'] = true;
        $db = &JFactory::getDBo();
        $query = "SELECT a.* FROM $table AS a ";
        if($table != '#__fitness_goals') {
            $query = "SELECT a.*, pg.user_id AS user_id FROM $table AS a ";
            $query .= " LEFT JOIN #__fitness_goals AS pg ON pg.id=a.primary_goal_id";
        }
        $query .=  " WHERE a.id='$id'";
        
        $db->setQuery($query);
        if (!$db->query()) {
            throw new Exception($db->stderr());
            $ret['success'] = false;
            $ret['message'] = $db->stderr();
            return $ret;
        }
        $ret['data'] = $db->loadObject();
        return $ret;
    }
    
    public function getUserGroup($user_id) {
        $ret['success'] = true;
        if(!$user_id) {
            $user_id = &JFactory::getUser()->id;
        }
        $db = JFactory::getDBO();
        $query = "SELECT title FROM #__usergroups WHERE id IN 
            (SELECT group_id FROM #__user_usergroup_map WHERE user_id='$user_id')";
        $db->setQuery($query);
        if (!$db->query()) {
            throw new Exception($db->stderr());
            $ret['success'] = false;
            $ret['message'] = $db->stderr();
            return $ret;
        }
        $ret['data'] = $db->loadResult();
        return $ret;
    }
    
    
    public function sendEmailToTrainers($client_id, $type, $subject, $contents) {
        $ret['success'] = 1;
        $trainers_data = $this->getClientTrainers($client_id, $type);
        if(!$trainers_data['status']['success']) {
            $ret['success'] = 0;
            $ret['message'] = $trainers_data['status']['message'];
            return $ret;
        }
        $trainers = $trainers_data['data'];
        
        $emails = array();
        
        foreach ($trainers as $trainer_id) {
            if(!$trainer_id) continue;
            
            $trainer_email = &JFactory::getUser($trainer_id)->email;
            
            $emails[] = $trainer_email;
                
            $send = $this->sendEmail($trainer_email, $subject, $contents);
            
            if($send != '1') {
                $ret['success'] = false;
                $ret['message'] =  'Email function error';
                return $ret;
            }
        }
        
        $ret['message'] =  $emails;
        
        return $ret;
    }
    
    public function sendEmailToOtherTrainers($client_id, $user_id, $subject, $contents) {
        $ret['success'] = 1;
        $all_trainers = $this->getClientTrainers($client_id, 'all');
        if(!$all_trainers['status']['success']) {
            $ret['success'] = 0;
            $ret['message'] = $all_trainers['status']['message'];
            return $ret;
        }
        $all_trainers = $all_trainers['data'];

        $other_trainers = array_diff($all_trainers, array($user_id));

        foreach ($other_trainers as $trainer_id) {
            if(!$trainer_id) continue;

            $trainer_email = &JFactory::getUser($trainer_id)->email;

            $send = $this->sendEmail($trainer_email, $subject, $contents);

            if($send != '1') {
                $ret['success'] = 0;
                $ret['message'] =  'Email function error';
                return $ret;
            }
        }
        return $ret;
    }
    
    
    public function sendEmailToClient($client_id, $subject, $contents) {
        $ret['success'] = 1;
        $client_email = &JFactory::getUser($client_id)->email;

        $send = $this->sendEmail($client_email, $subject, $contents);

        if($send != '1') {
            $ret['success'] = false;
            $ret['message'] = 'Email function error';
            return $ret;
        }
        
        $ret['message'] = array($client_email);
                
        return $ret;
    }
    
     public function checkUniqueTableItem($table, $column, $value) {
        $ret['success'] = 1;
        $db = JFactory::getDBO();
        $query = "SELECT * FROM $table WHERE $column='$value'";
        $db->setQuery($query);
        if (!$db->query()) {
            $ret['success'] = 0;
            $ret['message'] = $db->stderr();
        }
        $ret['data'] = $db->loadResult();
        return $ret;
    }
    
    
    public function getUsersByGroup($group_id) {
        $db = &JFactory::getDBo();
        $query = "SELECT u.id FROM #__users AS u 
            INNER JOIN #__user_usergroup_map AS g ON g.user_id=u.id WHERE g.group_id='$group_id' AND u.block='0'";
        $db->setQuery($query);
        $ret['success'] = 1;
        if (!$db->query()) {
            $ret['success'] = 0;
            $ret['message'] = $db->stderr();
            return $ret;
        }

        $clients= $db->loadResultArray(0);

        if(!$clients) {
            $ret['success'] = 0;
            $ret['message'] = 'No users assigned to this usergroup.';
            return $ret;
        }


        foreach ($clients as $user_id) {
            if($user_id) {
                $user = &JFactory::getUser($user_id);
                $clients_name[] = $user->name;
            }
        }
        
        $ret['data'] = array_combine($clients, $clients_name);
        
        return $ret;
    }
    
    public function getClientsByBusiness($business_profile_id, $user_id) {
            
        $db = &JFactory::getDBo();
        $query = "SELECT DISTINCT user_id FROM #__fitness_clients WHERE business_profile_id='$business_profile_id'";
        
        $user = JFactory::getUser($user_id);
   
        // if simple trainer
        if(!FitnessHelper::is_primary_administrator($user->id) && !FitnessHelper::is_secondary_administrator($user->id) && FitnessHelper::is_trainer($user->id)) {
            $other_trainers = $db->Quote('%' . $db->escape($user->id, true) . '%');
            $query .= ' AND ( primary_trainer = ' . (int) $user->id . ' OR other_trainers LIKE ' . $other_trainers . ' ) ';
        }
        $db->setQuery($query);
        $ret['success'] = 1;
        if (!$db->query()) {
            $ret['success'] = 0;
            $ret['message'] = $db->stderr();
            return $ret;
        }

        $clients= $db->loadResultArray(0);

        if(!$clients) {
            $ret['success'] = 0;
            $ret['message'] = 'No clients assigned to this Business Profile.';
            return $ret;
        }

        foreach ($clients as $user_id) {
            if($user_id) {
                $user = &JFactory::getUser($user_id);
                $clients_name[] = $user->name;
            }
        }
        
        $ret['data'] = array_combine($clients, $clients_name);
        
        return $ret;
    }
    
    
    public function getTrainersByUsergroup($trainers_group_id) {
        
        if(!$trainers_group_id) {
            $trainers_group_id = self::getTrainersGroupId();
        }
        $db = &JFactory::getDBo();
        $query = "SELECT id AS value, username AS text FROM #__users "
                . "INNER JOIN #__user_usergroup_map ON #__user_usergroup_map.user_id=#__users.id"
                . " WHERE #__user_usergroup_map.group_id='$trainers_group_id'";
        $db->setQuery($query);
        if (!$db->query()) {
            JError::raiseError($db->getErrorMsg());
        }
        $trainers = $db->loadObjectList();
        
        return $trainers;
    }
    
    
    
    public function getTrainersClientsTable($trainers_group_id) {
        
        if(!$trainers_group_id) {
            $trainers_group_id = self::getTrainersGroupId();
        }
        $db = &JFactory::getDBo();
        $query = "SELECT DISTINCT c.primary_trainer AS value, u.username AS text FROM #__fitness_clients AS c"
                . " LEFT JOIN #__users AS u on u.id=c.primary_trainer"
                . " INNER JOIN #__user_usergroup_map AS m ON m.user_id=u.id"
                . " WHERE c.state='1'";
        
        if(!self::is_superuser()) {
            $query .= " AND m.group_id='$trainers_group_id'";
        }


        $db->setQuery($query);
        if (!$db->query()) {
            JError::raiseError($db->getErrorMsg());
        }
        $trainers = $db->loadObjectList();
        
        return $trainers;
    }
    
    
    /**
     * 
     * @param type $items - options array
     * @param type $name - select tag name
     * @param type $id - select tag id
     * @param type $selected - option selected value
     * @param type $select - empty option name
     * @param type $required - 'true' if is field requered
     * @param type $class - select tag class
     * @return string
     */
    public function generateSelect($items, $name, $id, $selected, $select, $required, $class) {
 
        $html = '<select ';
        
        $html .= ' name="' . $name . '" ';
        
        $html .= ' id="' . $id . '" ';
        
        $html .= ' class="' . $class . '" ';
        
        $html .= ' required="required" ';
        
        if($required) {
            $html .= 'required="required"';
        }
        
        $html .=  '>';
        
        $html .= '<option value="">-Select ' . $select . '-</option>';
        $html .= JHtml::_('select.options', $items , 'value', 'text', $selected, true);
        $html .= '</select>';
        return $html;
    }
    
    function generateMultipleSelect($items, $name, $id, $selected, $select, $required, $class) {

        $selected = explode(',', $selected);
        $html = '<select size="10" id="' . $id . '" class="' . $class . '" multiple="multiple" name="' . $name . '[]">';
        $html .= '<option>-Select-</option>';
        if(isset($items)) {
            foreach ($items as $item) {
                if(in_array($item->id, $selected)){
                    $selected_option = 'selected="selected"';
                } else {
                    $selected_option = '';
                }
                $html .= '<option ' . $selected_option . ' value="' . $item->id . '">' . $item->name . ' </option>';
            }
        }
        $html .= '</select>';
        
        return $html;
    }
    
    function getOtherTrainersSelect($item_id, $table, $trainers_group_id) {
        if(!$trainers_group_id) {
            $trainers_group_id = self::getTrainersGroupId();
        }
        $db = &JFactory::getDbo();
        $query = "SELECT id, username FROM #__users"
                . " INNER JOIN #__user_usergroup_map ON #__user_usergroup_map.user_id=#__users.id "
                . "WHERE #__user_usergroup_map.group_id='$trainers_group_id'";
        $db->setQuery($query);
        $result = $db->loadObjectList();
        $query = "SELECT other_trainers FROM $table WHERE id='$item_id'";
        $db->setQuery($query);
        if(!$db->query()) {
            JError::raiseError($db->getErrorMsg());
        }
        $other_trainers = explode(',', $db->loadResult());
        $drawField = '<select size="10" id="jform_other_trainers" class="inputbox" multiple="multiple" name="jform[other_trainers][]">';
        $drawField .= '<option value="">none</option>';
        if(isset($result)) {
            foreach ($result as $item) {
                if(in_array($item->id, $other_trainers)){
                    $selected = 'selected="selected"';
                } else {
                    $selected = '';
                }
                $drawField .= '<option ' . $selected . ' value="' . $item->id . '">' . $item->username . ' </option>';
            }
        }
        $drawField .= '</select>';
        
         return $drawField;
    }
    
        
    public function getGroupList() {
        $db = JFactory::getDbo();
        $sql = 'SELECT id AS value, title AS text'. ' FROM #__usergroups' . ' ORDER BY id';
        $db->setQuery($sql);
        if(!$db->query()) {
            JError::raiseError($db->getErrorMsg());
        }
        $grouplist = $db->loadObjectList();
        return $grouplist;
    }
    
    public function getBusinessProfileList($user_id) {
        $db = JFactory::getDbo();
        $sql = "SELECT id AS value, name AS text FROM #__fitness_business_profiles WHERE state='1' ";
        
        if(self::is_trainer($user_id)) {
            $trainers_group_id = self::getTrainersGroupIdByUser($user_id);
            $sql .= "  AND group_id='$trainers_group_id'";
        }
        
        $sql .= "  ORDER BY id";
        
        $db->setQuery($sql);
        if(!$db->query()) {
            JError::raiseError($db->getErrorMsg());
        }
        $result = $db->loadObjectList();
        return $result;
    }
    
    public function getBusinessProfile($id) {
        $ret['success'] = 1;
        $db = JFactory::getDbo();
        $sql = "SELECT * FROM #__fitness_business_profiles WHERE id='$id' AND state='1'";
        $db->setQuery($sql);
        if(!$db->query()) {
            $ret['success'] = 0;
            $ret['message'] = $db->getErrorMsg();
        }
        $ret['data'] = $db->loadObject();
        
        return $ret;
    }
    
    public function getUserGroupByBusiness($business_profile_id) {
        $ret['success'] = 1;
        $db = JFactory::getDbo();
        $sql = "SELECT * FROM #__fitness_user_groups WHERE business_profile_id='$business_profile_id' AND state='1'";
        $db->setQuery($sql);
        if(!$db->query()) {
            $ret['success'] = 0;
            $ret['message'] = $db->getErrorMsg();
        }
        $ret['data'] = $db->loadObject();
        
        return $ret;
    }
    
    
    public function getBusinessByUserGroup($group_id) {
        $ret['success'] = 1;
        $db = JFactory::getDbo();
        $sql = "SELECT * FROM #__fitness_user_groups WHERE group_id='$group_id' AND state='1'";
        $db->setQuery($sql);
        if(!$db->query()) {
            $ret['success'] = 0;
            $ret['message'] = $db->getErrorMsg();
        }
        $ret['data'] = $db->loadObject();
        
        return $ret;
    }
    
    public function getRecipeTypes() {
        $ret['success'] = 1;
        $db = JFactory::getDbo();
        $sql = "SELECT id, name, id AS value, name AS text FROM #__fitness_recipe_types WHERE state='1' ORDER BY name ASC";
        $db->setQuery($sql);
        if(!$db->query()) {
            $ret['success'] = 0;
            $ret['message'] = $db->getErrorMsg();
        }
        $ret['data'] = $db->loadObjectList();
        
        return $ret;
    }
    
    public function getRecipe($id, $state) {
        $user = &JFactory::getUser();
        $user_id = $user->id;
        
        $query = "SELECT a.*,"
                . " (SELECT name FROM #__users WHERE id=a.created_by) author,"
                . " (SELECT name FROM #__users WHERE id=a.reviewed_by) trainer,";
                
        $query .= " (SELECT ROUND(SUM(protein),2) FROM #__fitness_nutrition_recipes_meals WHERE recipe_id=a.id) AS protein,
                   (SELECT ROUND(SUM(fats),2) FROM #__fitness_nutrition_recipes_meals WHERE recipe_id=a.id) AS fats,
                   (SELECT ROUND(SUM(carbs),2) FROM #__fitness_nutrition_recipes_meals WHERE recipe_id=a.id) AS carbs,
                   (SELECT ROUND(SUM(calories),2) FROM #__fitness_nutrition_recipes_meals WHERE recipe_id=a.id) AS calories,
                   (SELECT ROUND(SUM(energy),2) FROM #__fitness_nutrition_recipes_meals WHERE recipe_id=a.id) AS energy,
                   (SELECT ROUND(SUM(saturated_fat),2) FROM #__fitness_nutrition_recipes_meals WHERE recipe_id=a.id) AS saturated_fat,
                   (SELECT ROUND(SUM(total_sugars),2) FROM #__fitness_nutrition_recipes_meals WHERE recipe_id=a.id) AS total_sugars,
                   (SELECT ROUND(SUM(sodium),2) FROM #__fitness_nutrition_recipes_meals WHERE recipe_id=a.id) AS sodium,";
        
        $query .= " (SELECT id FROM #__fitness_nutrition_recipes_favourites WHERE recipe_id=a.id AND client_id='$user_id') AS is_favourite ";       
        
        $query .=  " FROM #__fitness_nutrition_recipes AS a"
                . " "
                . "WHERE a.id='$id' ";
               // . "AND a.state='$state'";

        $item = self::customQuery($query, 2);
        
        return $item;
    }
    
    public function getRecipeOriginalData($id) {

        $query = "SELECT a.*";


        $query .=  " FROM #__fitness_nutrition_recipes AS a"
                . " "
                . "WHERE a.id='$id' "
                . "AND a.state='1'";

        $item = self::customQuery($query, 2);
        
        return $item;
    }
    
    public function getRecipeMeals($recipe_id) {

        $query = "SELECT * FROM #__fitness_nutrition_recipes_meals WHERE recipe_id='$recipe_id'";

        $recipe_meals = self::customQuery($query, 1);

        return $recipe_meals;      
    }
    
    public function getClient($client_id) {
        $ret['success'] = 1;
        $db = JFactory::getDbo();
        $sql = "SELECT * FROM #__fitness_clients WHERE user_id='$client_id' AND state='1'";
        $db->setQuery($sql);
        if(!$db->query()) {
            $ret['success'] = 0;
            $ret['message'] = $db->getErrorMsg();
        }
        $ret['data'] = $db->loadObject();
        
        return $ret;
    }
    
    public function getBubinessIdByClientId($client_id) {
        $client_data = $this->getClient($client_id);
        if(!$client_data['success']) {
            JError::raiseError($client_data['message']);
        }
        $client_data = $client_data["data"];
        $business_profile_id = $client_data->business_profile_id;
        return $business_profile_id;
    }
    
    public function JErrorFromAjaxDecorator($respond){
        if(!$respond['success']) {
            JError::raiseError($respond['message']);
        }
        return $respond['data'];
    }
    
    public function getBusinessProfileId($user_id) {
        $ret['success'] = 1;
        // if admimistrator trainer or superuser
        if(self::is_trainer_administrator($user_id)) {
            $group_id = $this->getTrainersGroupIdByUser($user_id);
        }

        // if simple trainer
        if(self::is_simple_trainer($user_id)) {
            $user = &JFactory::getUser($user_id);
            $groups = $user->get('groups');
            $group_id = array_shift(array_values($groups));
        }
        
        $business_profile = $this->getBusinessByTrainerGroup($group_id);
         
        
        if(!$business_profile['success']) {
            $ret['success'] = 0;
            $ret['message'] = $business_profile['message'];
        }
        
        
        $business_profile = $business_profile['data'];
       
        
        $ret['data'] = $business_profile->id;
        
        // if is client
        if(self::is_client($user_id)) {
            $user = &JFactory::getUser($user_id);
            $groups = $user->get('groups');
            $group_id = array_shift(array_values($groups));
            
            $BusinessProfile = $this->getBusinessByUserGroup($group_id);
            if(!$BusinessProfile['success']) {
                $ret['success'] = 0;
                $ret['message'] = $BusinessProfile['message'];
            }
            
            $business_profile = $BusinessProfile['data'];
            
            $business_profile_id = $business_profile->business_profile_id;
            
            $ret['data']  = $business_profile_id;
            
        }
        
        

        return $ret;
    }
    
    public function getBusinessByTrainerGroup($group_id) {
        $ret['success'] = 1;
        $db = JFactory::getDbo();
        $sql = "SELECT * FROM #__fitness_business_profiles WHERE group_id='$group_id' AND state='1'";
        $db->setQuery($sql);
        if(!$db->query()) {
            $ret['success'] = 0;
            $ret['message'] = $db->getErrorMsg();
        }
        $ret['data'] = $db->loadObject();
        
        return $ret;
    }
    
    public function insertUpdateObj($obj, $table) {
        $db = JFactory::getDbo();

        if($obj->id) {
            $insert = $db->updateObject($table, $obj, 'id');
        } else {
            $insert = $db->insertObject($table, $obj, 'id');
        }

        if (!$insert) {
            throw new Exception($db->stderr());
        }

        $inserted_id = $db->insertid();

        if(!$inserted_id) {
            $inserted_id = $obj->id;
        }

        return $inserted_id;     
    }
    
    public function getClientsByEvent($event_id) {
        $db = & JFactory::getDBO();
        $query = "SELECT DISTINCT client_id FROM #__dc_mv_events WHERE id='$event_id' AND client_id !='0'";
        $query .= " UNION ";
        $query .= "SELECT DISTINCT client_id FROM #__fitness_appointment_clients WHERE event_id='$event_id' AND client_id !='0'";

        $db->setQuery($query);
        if (!$db->query()) {
            throw new Exception($db->stderr());
            $ret['success'] = false;
            $ret['message'] = $db->stderr();
            return $ret;
        }
        $client_ids = $db->loadResultArray(0);
        $client_ids = array_unique($client_ids);
        return $client_ids;
    }
    
    function getClientIdByNutritionPlanId($nutrition_plan_id) {
        $query = "SELECT client_id FROM #__fitness_nutrition_plan WHERE id='$nutrition_plan_id' AND state='1'";
        $user_id = self::customQuery($query, 0);
        return $user_id;
    }
    
    function getUserIdByNutritionRecipeId($recipe_id) {
        $query = "SELECT created_by FROM #__fitness_nutrition_recipes WHERE id='$recipe_id' AND state='1'";
        $user_id = self::customQuery($query, 0);
        return $user_id;
    }
    
    function getUserIdByDiaryId($id) {
        $query = "SELECT client_id FROM #__fitness_nutrition_diary WHERE id='$id'";
        $user_id = self::customQuery($query, 0);
        return $user_id;
    }
 
}

