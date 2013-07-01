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
                $UTC = '3';
                $offset = $UTC * 60 * 60;
                $this->offset = $offset;
                $dateFormat = "Y-m-d H:i:s";
                
                $time = "H:i:s";

                $this->current_date =  gmdate($dateFormat, time() + $offset);
                
                $this->current_date_24 =  gmdate($dateFormat, time() + $offset + 60*60*24);
                
                $this->current_date_36 =  gmdate($dateFormat, time() + $offset + 60*60*36);
                
                $this->current_time =  gmdate($time, time() + $offset);
                
                
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
        
        
        public function emailReminderCron() {
            $db = & JFactory::getDBO();
            $query = "SELECT id FROM #__dc_mv_events WHERE";
            
            if(($this->current_time > '00:00') AND ($this->current_time <'12:00')) {
                $query .= " starttime BETWEEN" . $db->quote($this->current_date) . "AND" . $db->quote($this->current_date_24);
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
            $result = $db->loadResultArray();
            var_dump($result);
            
            foreach ($result as $client_id) {
                $this->send_appointment_email($client_id);
            }
            
            die();
        }
        
        
        public function send_appointment_email($event_id) {

            $url = JURI::base() .'index.php?option=com_multicalendar&view=pdf&tpml=component&event_id=' . $event_id;

            $ch = curl_init();
            curl_setopt($ch,CURLOPT_USERAGENT,'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');
            curl_setopt($ch, CURLOPT_URL,$url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $contents = curl_exec ($ch);
            curl_close ($ch);

            $client_email = $this->getClientEmailByEvent($event_id);
            $this->sendEmail($client_email, 'Appointment details, elitefit.com.au', $contents);
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

            if($send == '1') {
                echo 'Email  sent';
            } else {
                echo $send;
            }
            die();
        }
        
        
        
        public function getClientEmailByEvent($event_id) {
            $db = & JFactory::getDBO();
            $query = "SELECT client_id FROM #__dc_mv_events WHERE id='$event_id'";
            $db->setQuery($query);
            if (!$db->query()) {
                echo $db->stderr();
            }
            $client_id = $db->loadResult();
            $user = &JFactory::getUser($client_id);
            return $user->email;
        }
		
}
?>