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

/**
 * Fitness helper.
 */
class FitnessHelper
{
    const PENDING_GOAL_STATUS = '1';
    const COMPLETE_GOAL_STATUS = '2';
    const INCOMPLETE_GOAL_STATUS = '3';
    const EVELUATING_GOAL_STATUS = '4';
    const INPROGRESS_GOAL_STATUS = '5';
    const ASSESSING_GOAL_STATUS = '6';
    
    const TRAINERS_USERGROUP = 'Trainers';
    const CLIENTS_USERGROUP = 'Registered';
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
            $status['success'] = 0;
            $status['message'] = 'No trainers assigned to this client.';
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
                WHERE mg.id='$id' AND pg.state='1'
            ";
        }
        $db->setQuery($query);
        if (!$db->query()) {
            $result['success'] = false;
            $result['message'] = $db->stderr();
            return $result;
        }
        $client_id = $db->loadResultArray(0);
        $trainer_id = $db->loadResultArray(1);

        $result['data'] = array('client_id' => $client_id[0], 'trainer_id' => $trainer_id[0]);
        
        return $result;
    }
    
    public function getGoal($id) {
        $ret['success'] = true;
        $db = &JFactory::getDBo();
        $query = "SELECT * FROM #__fitness_goals WHERE id='$id'";
        $db->setQuery($query);
        if (!$db->query()) {
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
        
        foreach ($trainers as $trainer_id) {
            if(!$trainer_id) continue;
            
            $trainer_email = &JFactory::getUser($trainer_id)->email;
                
            $send = $this->sendEmail($trainer_email, $subject, $contents);
            
            if($send != '1') {
                $ret['success'] = false;
                $ret['message'] =  'Email function error';
                return $ret;
            }
        }
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
        return $ret;
    }
         
         
}

