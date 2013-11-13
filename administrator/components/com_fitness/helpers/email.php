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

require_once  JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_fitness' . DS .'helpers' . DS . 'fitness.php';

/**
 * Fitness helper.
 */
class FitnessEmail extends FitnessHelper
{

    
    public static function factory($view) {
        
        switch ($view) {
            case 'Goal':
                return new GoalEmail();
                break;

            default:
                break;
        }

    }

}



class GoalEmail extends FitnessEmail {
    
    public function setParams($data) {
        $this->data = $data;
        $id = $data->id;
        if (!$id) {
            throw new Exception('Error: no goal id');
        }
        
        //default
        $send_to_client = true;
        $send_to_trainer = false;

        switch ($data->method) {
            //primary
            case 'GoalPenging':
                $subject = 'New Primary Goal';
                $layout = 'email_goal_pending';
                $goal_type = '1';
                break;
            case 'GoalComplete':
                $subject = 'Primary Goal Complete';
                $layout = 'email_goal_complete';
                $goal_type = '1';
                break;
            case 'GoalIncomplete':
                $subject = 'Primary Goal Incomplete';
                $layout = 'email_goal_incomplete';
                $goal_type = '1';
                break;
            case 'GoalEvaluating':
                $subject = 'Evaluate Primary Goal';
                $layout = 'email_goal_evaluating';
                $goal_type = '1';
                $send_to_client = false;
                $send_to_trainer = true;
                break;
            case 'GoalInprogress':
                $subject = 'Primary Goal Scheduled';
                $layout = 'email_goal_inprogress';
                $goal_type = '1';
                $send_to_trainer = true;
                break;
            case 'GoalAssessing':
                $subject = 'Assess Primary Goal';
                $layout = 'email_goal_assessing';
                $goal_type = '1';
                $send_to_client = false;
                $send_to_trainer = true;
                break;

            //mini
            case 'GoalPengingMini':
                $subject = 'New Mini Goal';
                $layout = 'email_goal_pending_mini';
                $goal_type = '2';
                break;
            case 'GoalCompleteMini':
                $subject = 'Mini Goal Complete';
                $layout = 'email_goal_complete_mini';
                $goal_type = '2';
                break;
            case 'GoalIncompleteMini':
                $subject = 'Mini Goal Incomplete';
                $layout = 'email_goal_incomplete_mini';
                $goal_type = '2';
                break;
            case 'GoalEvaluatingMini':
                $subject = 'Evaluate Mini Goal';
                $layout = 'email_goal_evaluating_mini';
                $goal_type = '2';
                break;
            case 'GoalInprogressMini':
                $subject = 'Mini Goal Scheduled';
                $layout = 'email_goal_inprogress_mini';
                $goal_type = '2';
                $send_to_trainer = true;
                break;
            case 'GoalAssessingMini':
                $subject = 'Assess Mini Goal';
                $layout = 'email_goal_assessing_mini';
                $goal_type = '2';
                $send_to_client = false;
                $send_to_trainer = true;
                break;
            default:
                break;
        }
        
        $this->subject = $subject;
        $this->goal_type = $goal_type;
        $this->send_to_client = $send_to_client;
        $this->send_to_trainer = $send_to_trainer;
            
        $this->url = JURI::root() . 'index.php?option=com_multicalendar&view=pdf&layout=' . $layout . '&tpml=component&id=' . $id . '&goal_type=' . $goal_type;
    }
    
    
    public function generate_contents(){
        $contents = $this->getContentCurl($this->url);
        $this->contents = $contents['data'];
    }
    
    
    public function get_recipients_ids() {
        $ids = array();
        
        $client = $this->getClientIdByGoalId($this->data->id , $this->goal_type);

        $client_id = $client['data']['client_id'];

        if (!$client_id) {
            throw new Exception('error: no client id');
        }
        
        if($this->send_to_client) {

            $ids[] = $client_id;
        }
        
        if($this->send_to_trainer) {
            $trainers_data = $this->getClientTrainers($client_id,  'all');

            $ids = array_merge($ids, $trainers_data['data']);
        }
        
        $this->recipients_ids = $ids;
    }
    
    public function send_mass_email() {
        $emails = array();
        
        foreach ($this->recipients_ids as $recipient_id) {
            
            if(!$recipient_id) continue;
            
            $email = &JFactory::getUser($recipient_id)->email;
            
            $emails[] = $email;
                
            $send = $this->sendEmail($email, $this->subject, $this->contents);

            if($send != '1') {
                throw new Exception('Email function error');
            }
        }
        
        $emails = implode(', ', $emails);
        
        return $emails;
    }
}

