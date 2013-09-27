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
                $UTC = '10';
                $offset = $UTC * 60 * 60;
                $this->offset = $offset;
                $dateFormat = "Y-m-d H:i:s";
                
                $time = "H:i:s";

                $this->current_date =  gmdate($dateFormat, time() + $offset);
                
                $this->current_date_28 =  gmdate($dateFormat, time() + $offset + 60*60*28);
                
                $this->current_date_36 =  gmdate($dateFormat, time() + $offset + 60*60*36);
                
                $this->current_time =  gmdate($time, time() + $offset);
                
                $this->administrator_email = 'npkorban@mail.ru'; 
                
                require_once  JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_fitness' . DS .'helpers' . DS . 'fitness.php';
                
                $helper = new FitnessHelper();
                $this->helper = $helper;
                
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
                    $this->inprogressStatusController('');
                    $this->inprogressStatusController('mini');
                    $this->assessingStatusController('');
                    $this->assessingStatusController('mini');
                }
                
                if ($task == 'confirm_email') {
                    $this->confirmEmail();
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
                'Personal Training',
                'Semi-Private Training', 
                'Assessment',
                'Consultation',
                'Special Event') 
            ";
            $db->setQuery($query);
            try {
                $db->query();
            } catch (Exception $e) {
                $message = $e->getMessage();
                
                $this->sendEmail($this->administrator_email, 'email reminder error', $message);

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

            $client_ids = getClientsByEvent($event_id); 
            
            $subject = 'Appointment Confirmation';
            foreach ($client_ids as $client_id) {
                if(!$client_id) continue;

                $url = JURI::base() .'index.php?option=com_multicalendar&view=pdf&layout=email_reminder&tpml=component&event_id=' . $event_id . '&client_id=' . $client_id;

                $contents = $this->getContentCurl($url);

                $email = JFactory::getUser($client_id)->email;

                $send = sendEmail($email, $subject, $contents);

                if($send == '1') {
                    $this->setSentEmailStatus($event_id, $email, $client_id);
                } else {
                    $this->sendEmail($this->administrator_email, 'email reminder error', $send);
                    echo $send . "<br/>";
                }
            }

        }
        
        
        public function getContentCurl($url) {
                if(!function_exists('curl_version')) {
                    $this->sendEmail($this->administrator_email, 'email reminder error', 'cURL not anabled');
                    die('cURL not anabled');
                }
                $ch = curl_init();
                curl_setopt($ch,CURLOPT_USERAGENT,'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');
                curl_setopt($ch, CURLOPT_URL,$url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                $contents = curl_exec ($ch);
                curl_close ($ch);
                return $contents;
        }

        
        /** get client email be event id
         * 
         * @param type $event_id
         * @return type
         */
        public function getClientsByEvent($event_id) {

            $db = & JFactory::getDBO();
            $query = "SELECT DISTINCT client_id FROM #__dc_mv_events WHERE id='$event_id' AND client_id !='0'";
            $query .= " UNION ";
            $query .= "SELECT DISTINCT client_id FROM #__fitness_appointment_clients WHERE event_id='$event_id' AND client_id !='0'";

            $db->setQuery($query);
            try {
                $db->query();
            } catch (Exception $e) {
                $message = $e->getMessage();
                
                $this->sendEmail($this->administrator_email, 'email reminder error', $message);

                echo $message;

                return false;
            } 
            $client_ids = $db->loadResultArray(0);
            $client_ids = array_unique($client_ids);
            return $client_ids;
        }

        /**
         * standard send email function
         * @param type $recipient
         * @param type $Subject
         * @param type $body
         */
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
                
                $this->sendEmail($this->administrator_email, 'email reminder error', $message);

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

                $this->sendEmail($this->administrator_email, 'email reminder error', $message);

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

                $this->sendEmail($this->administrator_email, 'email reminder error', $message);

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
        private function inprogressStatusController($goals_type) {
            
            $table = '#__fitness_goals';
            $layout = 'sendGoalInprogressEmail';
            $data = new stdClass();
            $data->status = $this->inprogress_status;
            
            if($goals_type == 'mini') {
                $table = '#__fitness_mini_goals';
                $layout = 'sendGoalInprogressMiniEmail';
            }
                    
            $db = & JFactory::getDBO();
            $query = "SELECT id  FROM  $table
                WHERE (status=". $db->quote($this->evaluating_status) ." OR status='' OR status=NULL)
                AND start_date <= " . $db->quote($this->current_date) . "
                AND state='1'
            ";
                
            $db->setQuery($query);
            try {
                $db->query();
                $goals = $db->loadObjectList();
                var_dump($goals);
                foreach ($goals as $goal) {
                    if(!$goal) continue;
                    $data->id = $goal->id;
                    var_dump($data);
                    $updated = $db->updateObject($table, $data, 'id');
                    if (!$updated) {
                         die($db->stderr());
                    }
                    $url = JURI::root() .'index.php?option=com_multicalendar&view=pdf&layout=' . $layout . '&tpml=component';
                    $contents .= $this->getContentCurl($url);
                }
                echo $contents;
                
            } catch (Exception $e) {

                $message .= "<br/> <br/> <strong style='color:red'>" . $e->getMessage() . "</strong><br/>";

                $this->sendEmail($this->administrator_email, 'email reminder error', $message);

                echo $message;

                return false;
            }
            die();
    }
    
    
    private function assessingStatusController($goals_type) {
            
            $table = '#__fitness_goals';
            $layout = 'sendGoalAssessingEmail';
            $data = new stdClass();
            $data->status = $this->assessing_status;
            
            if($goals_type == 'mini') {
                $table = '#__fitness_mini_goals';
                $layout = 'sendGoalAssessingMiniEmail';
            }
                    
            $db = & JFactory::getDBO();
            $query = "SELECT id  FROM  $table
                WHERE (status=". $db->quote($this->inprogress_status) .")
                AND deadline <= " . $db->quote($this->current_date) . "
                AND state='1'
            ";
                
            $db->setQuery($query);
            try {
                $db->query();
                $goals = $db->loadObjectList();
                var_dump($goals);
                foreach ($goals as $goal) {
                    if(!$goal) continue;
                    $data->id = $goal->id;
                    var_dump($data);
                    $updated = $db->updateObject($table, $data, 'id');
                    if (!$updated) {
                         die($db->stderr());
                    }
                    $url = JURI::root() .'index.php?option=com_multicalendar&view=pdf&layout=' . $layout . '&tpml=component';
                    $contents .= $this->getContentCurl($url);
                }
                echo $contents;
                
            } catch (Exception $e) {

                $message .= "<br/> <br/> <strong style='color:red'>" . $e->getMessage() . "</strong><br/>";

                $this->sendEmail($this->administrator_email, 'email reminder error', $message);

                echo $message;

                return false;
            }
            die();
    }
		
}


?>
