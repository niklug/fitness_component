<?php
/**
* @Copyright Copyright (C) 2010 CodePeople, www.codepeople.net
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*
* This file is part of Multi Calendar for Joomla <www.joomlacalendars.com>.
* 
* Multi Calendar for Joomla is free software: you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation, either version 3 of the License, or
* (at your option) any later version.
*
* Multi Calendar for Joomla  is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License
* along with Multi Calendar for Joomla.  If not, see <http://www.gnu.org/licenses/>.
*
**/

defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.application.component.controller');



class MultiCalendarController extends JController
{
    function __construct($config = array())
	{
            require_once  JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_fitness' . DS .'helpers' . DS . 'fitness.php';

            $helper = new FitnessHelper();
            $this->helper = $helper;
            
            $current_date = $this->helper->getTimeCreated();
            
            $date = new JDate($current_date);
            
                
            $unix_time_28 = $date->toUnix() + 24*60*60*28;
            $current_date_28 = JFactory::getDate($unix_time_28);   
            
            
            $unix_time_36 = $date->toUnix() + 24*60*60*36;
            $current_date_36 = JFactory::getDate($unix_time_36);   


            $this->current_date =  $date->format("Y-m-d");

            $this->current_date_28 = $current_date_28->format("Y-m-d");

            $this->current_date_36 =  $current_date_36->format("Y-m-d");

            $this->current_time =  $date->format("H:i:s");

            $this->administrator_email = 'npkorban@mail.ru'; 

            $this->evaluating_status = $helper::EVELUATING_GOAL_STATUS;
            $this->inprogress_status = $helper::INPROGRESS_GOAL_STATUS;
            $this->assessing_status = $helper::ASSESSING_GOAL_STATUS;


            if(JRequest::getCmd('view') === 'insert') {
                    $config['base_path'] = JPATH_COMPONENT_ADMINISTRATOR;
            }
            parent::__construct($config);
	}
	function display()
	{
		$task = JRequest::getVar( 'task' );
		$rView = JRequest::getVar( 'view' );
                 if ($rView=="pdf")
		{
                    JRequest::setVar( 'view', 'pdf');
                    parent::display('default');
		}
                
                if ($task == 'cron') {
                    $this->emailReminderCron();
                }
                
                if ($task == 'status_cron') {
                    $this->inprogressStatusController('1');
                    $this->inprogressStatusController('2');
                    $this->assessingStatusController('1');
                    $this->assessingStatusController('2');
                }
                
                if ($task == 'confirm_email') {
                    $this->confirmEmail();
                }
                
                if ($task == 'cron_auto_publish') {
                    $this->autoPublishCron();
                }
                
		if ($task=="load")
		{
			JRequest::setVar( 'layout', 'ajax'  );
			JRequest::setVar( 'view', 'multicalendar'  );
		} 
		else if ($task=="editevent")
		{
			JRequest::setVar( 'layout', 'layout'  );
			JRequest::setVar( 'view', 'multicalendar'  );
		}
		else if ($rView!='insert') {
			switch ($rView) {
				default:
					JRequest::setVar('view','multicalendar'); // force it to be the multicalendar view;
				break;
			}
		    
		} 
                
		parent::display();
	}
        
        /*
         * 
         */
        public function emailReminderCron() {
            $db = & JFactory::getDBO();
            $query = "SELECT id FROM #__dc_mv_events WHERE 
             id NOT IN (SELECT event_id FROM #__fitness_email_reminder WHERE sent='1') AND ";
            
            if(($this->current_time > '00:00') AND ($this->current_time <'12:00')) {
                $query .= " starttime BETWEEN" . $db->quote($this->current_date) . "AND" . $db->quote($this->current_date_28);
            } else {
                $query .= " starttime BETWEEN" . $db->quote($this->current_date) . "AND" . $db->quote($this->current_date_36);
            }
             $query .= "
                AND title  IN (
                '1', 
                '2', 
                '5',
                '6',
                '7') 
            ";
             
             // IN 'Personal Training', 'Semi-Private Training', 'Assessment', 'Consultation', 'Special Event'
             
            $db->setQuery($query);
            try {
                $db->query();
            } catch (Exception $e) {
                $message = $e->getMessage();
                
                $this->helper->sendEmail($this->administrator_email, 'email reminder error', $message);

                echo $message;

                return false;
            }  
            $result = $db->loadObjectList();

            foreach ($result as $event) {
  
                $this->send_appointment_email($event->id);
            }
            
            die();
        }
        
        /**
         * 
         * @param type $event_id
         */
        public function send_appointment_email($event_id) {

            $client_ids = $this->getClientEmailByEvent($event_id); 
            
            $subject = 'Appointment Confirmation';
            foreach ($client_ids as $client_id) {
                if(!$client_id) continue;

                $url = JURI::base() .'index.php?option=com_multicalendar&view=pdf&layout=email_reminder&tpml=component&event_id=' . $event_id . '&client_id=' . $client_id;

                $contents = $this->helper->getContentCurl($url);
                $contents = $contents['data'];
                if(!$contents['success']) {
                    echo $contents['message'];
                    $this->helper->sendEmail($this->administrator_email, 'email reminder error', $contents['message']);
                    return;
                }

                $email = JFactory::getUser($client_id)->email;

                $send = sendEmail($email, $subject, $contents);

                if($send == '1') {
                    $this->setSentEmailStatus($event_id, $email, $client_id);
                } else {
                    $this->helper->sendEmail($this->administrator_email, 'email reminder error', $send);
                    echo $send . "<br/>";
                }
            }

        }

        
        /** get client email be event id
         * 
         * @param type $event_id
         * @return type
         */
        public function getClientsByEvent($event_id) {

            $db = & JFactory::getDBO();
            $query = "SELECT DISTINCT client_id FROM #__fitness_appointment_clients WHERE event_id='$event_id' AND client_id !='0'";

            $db->setQuery($query);
            try {
                $db->query();
            } catch (Exception $e) {
                $message = $e->getMessage();
                
                $this->helper->sendEmail($this->administrator_email, 'email reminder error', $message);

                echo $message;

                return false;
            } 
            $client_ids = $db->loadResultArray(0);
            $client_ids = array_unique($client_ids);
            return $client_ids;
        }

        
        /**
         * 
         * @param type $event_id
         * @return type
         */
        public function getClientEmailByEvent($event_id) {
            $db = & JFactory::getDBO();
            $query = "SELECT client_id FROM #__dc_mv_events WHERE id='$event_id'";
            $db->setQuery($query);
            try {
                $db->query();
            } catch (Exception $e) {
                $message = $e->getMessage();
                
                $this->helper->sendEmail($this->administrator_email, 'email reminder error', $message);

                echo $message;

                return false;
            }  
            $client_id = $db->loadResult();
            $user = &JFactory::getUser($client_id);
            return $user->email;
        }
        
        /*
         * 
         */
        public function setSentEmailStatus($event_id, $client_email, $client_id) {
             $db = & JFactory::getDBO();
             $query = "INSERT INTO #__fitness_email_reminder SET event_id='$event_id', client_id='$client_id', sent='1', confirmed='0'";
             $db->setQuery($query);
             try {
                $db->query();
                return true;

             } catch (Exception $e) {

                $message = "<br/> " . 'email status not set as SET TRUE FOR event id = ' . $event_id . ' client email = ' . $client_email;

                $message .= "<br/> <br/> <strong style='color:red'>" .$e->getMessage() . "</strong><br/>";

                $this->helper->sendEmail($this->administrator_email, 'email reminder error', $message);

                echo $message;

                return false;
             }  
        }
        
        
                /*
         * 
         */
        public function setSentConfirmEmail($event_id, $client_id) {
             $db = & JFactory::getDBO();
             $query = "UPDATE #__fitness_email_reminder SET confirmed='1' WHERE event_id='$event_id' AND client_id='$client_id' AND sent='1'";
             $db->setQuery($query);
             try {
                $db->query();
                return true;

             } catch (Exception $e) {

                $message = "<br/> " . 'email was not set status CONFIRM event id = ' . $event_id;

                $message .= "<br/> <br/> <strong style='color:red'>" .$e->getMessage() . "</strong><br/>";

                $this->helper->sendEmail($this->administrator_email, 'email reminder error', $message);

                echo $message;

                return false;
             }  
        }
        
        public function confirmEmail() {
            $event_id = base64_decode(JRequest::getVar('event_id'));
            $client_id = base64_decode(JRequest::getVar('client_id'));
            $confirm = $this->setSentConfirmEmail($event_id, $client_id);
            if($confirm) {
                echo("Thank you, your appointment is confirmed!");
            }
            
            die();
        } 
        
        // status
        private function inprogressStatusController($goal_type) {
            $db = & JFactory::getDBO();
            $table = '#__fitness_goals';
            $layout = 'email_goal_inprogress';
            $subject = 'Primary Goal Scheduled';
            $data = new stdClass();
            $data->status = $this->inprogress_status;
            
            $query = "SELECT id, user_id  FROM  $table
                WHERE (status=". $db->quote($this->evaluating_status) ." OR status='' OR status=NULL)
                AND start_date <= " . $db->quote($this->current_date) . "
                AND state='1'
            ";
            
            if($goal_type == '2') {
                $table = '#__fitness_mini_goals';
                $layout = 'email_goal_inprogress_mini';
                $subject = 'Mini Goal Scheduled';
                $query = "SELECT mg.id, pg.user_id  FROM  $table AS mg
                    LEFT JOIN #__fitness_goals AS pg ON mg.primary_goal_id=pg.id
                    WHERE (mg.status=". $db->quote($this->evaluating_status) ." OR mg.status='' OR mg.status=NULL)
                    AND mg.start_date <= " . $db->quote($this->current_date) . "
                    AND mg.state='1'
                ";
            }
                    
            $db->setQuery($query);
            try {
                $db->query();
                $goals = $db->loadObjectList();
                
                foreach ($goals as $goal) {
                    if(!$goal) continue;
                    $data->id = $goal->id;
                    $updated = $db->updateObject($table, $data, 'id');
                    if (!$updated) {
                         die($db->stderr());
                    }
                    $url = JURI::root() .'index.php?option=com_multicalendar&view=pdf&layout=' . $layout . '&tpml=component&id=' . $goal->id . '&goal_type=' . $goal_type;
                    $contents = $this->helper->getContentCurl($url);
                    $contents = $contents['data'];
                    if(!$contents['success']) {
                        echo $contents['message'];
                        $this->helper->sendEmail($this->administrator_email, 'email status error', $contents['message']);
                        return;
                    }
                    // send to client
                    $client_sent = $this->helper->sendEmailToClient($goal->user_id, $subject, $contents);
                    if(!$client_sent['success']) {
                        echo  $client_sent['message'];
                        $this->helper->sendEmail($this->administrator_email, 'email to client status error', $client_sent['message']);
                        return;
                    }
                    echo "<br/>" .  'Sent to client: ' .  implode(',', $client_sent['message']) . "<br/>"; 

                    //sent to all trainers
                    $trainers_sent = $this->helper->sendEmailToTrainers($goal->user_id, 'all', $subject, $contents);
                    if(!$trainers_sent['success']) {
                        echo $trainers_sent['message'];
                        $this->helper->sendEmail($this->administrator_email, 'email to trainer status error', $trainers_sent['message']);
                    }
                    echo 'Sent to trainers: ' .  implode(',', $trainers_sent['message']) . "<br/>"; 
                }
                
            } catch (Exception $e) {

                $message .= "<br/> <br/> <strong style='color:red'>" . $e->getMessage() . "</strong><br/>";

                $this->helper->sendEmail($this->administrator_email, 'Status Controller error', $message);

                echo $message;

                return false;
            }
            die();
    }
    
    
    private function assessingStatusController($goal_type) {
            $db = & JFactory::getDBO();
            $table = '#__fitness_goals';
            $layout = 'email_goal_assessing';
            $subject = 'Assess Primary Goal';
            $data = new stdClass();
            $data->status = $this->assessing_status;
            
            $query = "SELECT id, user_id  FROM  $table
                WHERE (status=". $db->quote($this->inprogress_status) .")
                AND deadline <= " . $db->quote($this->current_date) . "
                AND state='1'
            ";
            
            if($goal_type == '2') {
                $table = '#__fitness_mini_goals';
                $layout = 'email_goal_assessing_mini';
                $subject = 'Assess Mini Goal';
                $query = "SELECT mg.id, pg.user_id  FROM  $table AS mg
                    LEFT JOIN #__fitness_goals AS pg ON mg.primary_goal_id=pg.id
                    WHERE (mg.status=". $db->quote($this->inprogress_status) .")
                    AND mg.deadline <= " . $db->quote($this->current_date) . "
                    AND mg.state='1'
                ";
            }
                    
            $db->setQuery($query);
            try {
                $db->query();
                $goals = $db->loadObjectList();
                var_dump($goals);
                foreach ($goals as $goal) {
                    if(!$goal) continue;
                    $data->id = $goal->id;
                    $updated = $db->updateObject($table, $data, 'id');
                    if (!$updated) {
                         die($db->stderr());
                    }
                    $url = JURI::root() .'index.php?option=com_multicalendar&view=pdf&layout=' . $layout . '&tpml=component&id=' . $goal->id . '&goal_type=' . $goal_type;
                    $contents = $this->helper->getContentCurl($url);
                    $contents = $contents['data'];
                    if(!$contents['success']) {
                        echo $contents['message'];
                        $this->helper->sendEmail($this->administrator_email, 'email status error', $contents['message']);
                        return;
                    }
                    // send to client
                    $client_sent = $this->helper->sendEmailToClient($goal->user_id, $subject, $contents);
                    if(!$client_sent['success']) {
                        echo  $client_sent['message'];
                        $this->helper->sendEmail($this->administrator_email, 'email to client status error', $client_sent['message']);
                        return;
                    }
                    echo "<br/>" .  'Sent to client: ' .  implode(',', $client_sent['message']) . "<br/>"; 

                    //sent to all trainers
                    $trainers_sent = $this->helper->sendEmailToTrainers($goal->user_id, 'all', $subject, $contents);
                    if(!$trainers_sent['success']) {
                        echo $trainers_sent['message'];
                        $this->helper->sendEmail($this->administrator_email, 'email to trainer status error', $trainers_sent['message']);
                    }
                    echo 'Sent to trainers: ' .  implode(',', $trainers_sent['message']) . "<br/>"; 
                }
                
            } catch (Exception $e) {

                $message .= "<br/> <br/> <strong style='color:red'>" . $e->getMessage() . "</strong><br/>";

                $this->helper->sendEmail($this->administrator_email, 'Status Controller error', $message);

                echo $message;

                return false;
            }
            die();
    }
    
    public function autoPublishCron() {
        $db = JFactory::getDbo();
        
        $table = '#__dc_mv_events';
        
        $query = "SELECT * FROM $table";
        
        $events = FitnessHelper::customQuery($query, 1);
        
        echo $this->current_date;
        echo "<br/>";
        
        foreach ($events as $event) {
            // update publish workout
            if($event->auto_publish_workout AND $this->current_date >= $event->auto_publish_workout) {
                $event->frontend_published = '1';
                $update = $db->updateObject($table, $event, 'id');
                if (!$update) {
                    throw new Exception($db->stderr());
                }
                echo "<br/>";
                echo $event->id . '  workout published';
             }
             
             // update publish event
            if($event->auto_publish_event AND $this->current_date >= $event->auto_publish_event) {
                $event->published = '1';
                $update = $db->updateObject($table, $event, 'id');
                if (!$update) {
                    throw new Exception($db->stderr());
                }
                echo "<br/>";
                echo $event->id . '  event published';
             }
        }
        
        die();
    }
		
}


?>
