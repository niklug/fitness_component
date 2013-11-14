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

class FitnessEmail extends FitnessHelper
{

    
    public static function factory($view) {
        
        switch ($view) {
            case 'Goal':
                return new GoalStatusEmail();
                break;
            case 'Assessment':
                return new AppointmentEmail();
                break;
            
            case 'Programs':
                return new AppointmentEmail();
                break;
            
            case 'NutritionPlan':
                return new NutritionPlanEmail();
                break;
            case 'NutritionRecipe':
                return new NutritionRecipeEmail();
                break;
            
            case 'NutritionDiary':
                return new NutritionDiaryEmail();
                break;
            
            case 'Comment':
                return new CommentEmail();
                break;

            default:
                break;
        }
    }
    
    
    
    protected function setSentEmailStatus($event_id, $client_id) {
        $db = & JFactory::getDBO();
        $query = "INSERT INTO #__fitness_email_reminder SET event_id='$event_id', client_id='$client_id', sent='1', confirmed='0'";
        $db->setQuery($query);
        if (!$db->query()) {
            throw new Exception($db->stderr());
        }
    }
    
    
    protected function generate_contents(){
        $contents = $this->getContentCurl($this->url);
        $this->contents = $contents['data'];
    }
    
    protected function send_mass_email() {
        $emails = array();

        $i = 0;
        foreach ($this->recipients_ids as $recipient_id) {
            
            if(!$recipient_id) continue;
            
            $email = &JFactory::getUser($recipient_id)->email;
            
            $emails[] = $email;
            
            $contents = $this->contents;
 
            if(is_array($contents)) {
                $send = $this->sendEmail($email, $this->subject, $contents[$i]);
            } else {
                $send = $this->sendEmail($email, $this->subject, $contents);
            }

            if($send != '1') {
                throw new Exception('Email function error');
            }
            
            if ($data->method == 'Appointment') {//confirmation
                $this->setSentEmailStatus($this->data->id, $recipient_id);
            }
            
            $i++;
        }
        
        $emails = implode(', ', $emails);
        
        return $emails;
    }
    
    public function processing($data) {

        $this->setParams($data);
        
        $this->generate_contents();

        $this->get_recipients_ids();
        
        $data = $this->send_mass_email();
        
        return $data;
    }

}



class GoalStatusEmail extends FitnessEmail {
    
    protected function setParams($data) {
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
    
    
    protected function get_recipients_ids() {
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
    
}


class AppointmentEmail extends FitnessEmail {
    
    protected function setParams($data) {
        $this->data = $data;
        $id = $data->id;
        if (!$id) {
            throw new Exception('Error: no id');
        }
        
        //default
        $send_to_client = true;
        $send_to_trainer = false;

        switch ($data->method) {
            //status
            case 'AppointmentAttended':
                $subject = 'Appointment Complete';
                $layout = 'email_status_attended';
                break;
            case 'AppointmentCancelled':
                $subject = 'Appointment Cancelled';
                $layout = 'email_status_cancelled';
                break;
            case 'AppointmentLatecancel':
                $subject = 'Late Appointment Cancellation';
                $layout = 'email_status_late_cancel';
                break;
            case 'AppointmentNoshow':
                $subject = 'You Missed Your Appointment';
                $layout = 'email_status_no_show';
                break;
            //
            case 'Appointment': //confirmation
                $subject = 'Appointment Confirmation';
                $layout = 'email_reminder';
                break;
            case 'Notify':
                $subject = 'Review Your Feedback';
                $layout = 'email_notify';
                break;
            
            case 'NotifyAssessment':
                $subject = 'Assessment Complete';
                $layout = 'email_notify_assessment';
                break;
            
            case 'Workout':
                $subject = 'Workout/Training Session';
                $layout = 'email_workout';
                break;
            
            default:
                break;
        }
        
        $this->subject = $subject;
        $this->layout = $layout;
    }

    protected function get_recipients_ids() {
        $ids = $this->getClientsByEvent($this->data->id);
        $this->recipients_ids = $ids;
    }
    
    protected function generate_contents(){
        $contents = array();
        foreach ($this->recipients_ids as $recipient_id) {
            if(!$recipient_id)  continue;
            $url = JURI::root() . 'index.php?option=com_multicalendar&view=pdf&layout=' . $this->layout . '&tpml=component&event_id=' . $this->data->id . '&client_id=' . $recipient_id;
            $result = $this->getContentCurl($url);
            $contents[] = $result['data'];
        }
        $this->contents = $contents;
    }
    
    public function processing($data) {
        
        $this->setParams($data);
        
        $this->get_recipients_ids();
        
        $this->generate_contents();
        
        $data = $this->send_mass_email();

        return $data;
    }

}



class NutritionPlanEmail extends FitnessEmail {
    
    protected function setParams($data) {
        $this->data = $data;
        $id = $data->id;
        
        if (!$id) {
            throw new Exception('Error: no Nutrition Plan id');
        }

        switch ($data->method) {
            case 'Notify':
                $subject = 'Nutrition Plan Available';
                $layout = 'email_notify_nutrition_plan';
                break;
 
            default:
                break;
        }
        
        $this->subject = $subject;

        $this->url = JURI::root() . 'index.php?option=com_multicalendar&view=pdf&layout=' . $layout . '&tpml=component&nutrition_plan_id=' . $id;
    }
    

    protected function get_recipients_ids() {
        $ids = array();
        
        $client_id = $this->getClientIdByNutritionPlanId($this->data->id);

        if (!$client_id) {
            throw new Exception('error: no client id');
        }

        $ids[] = $client_id;
        
        $this->recipients_ids = $ids;
    }

}


class NutritionRecipeEmail extends FitnessEmail {
    
    protected function setParams($data) {
        $this->data = $data;
        $id = $data->id;
        
        if (!$id) {
            throw new Exception('Error: no Nutrition Recipe id');
        }

        switch ($data->method) {
            case 'Approved':
                $subject = 'Recipe Approved';
                $layout = 'email_recipe_approved';
                break;
            case 'NotApproved':
                $subject = 'Recipe Approved';
                $layout = 'email_recipe_notapproved';
                break;
 
            default:
                break;
        }
        
        $this->subject = $subject;

        $this->url = JURI::root() . 'index.php?option=com_multicalendar&view=pdf&layout=' . $layout . '&tpml=component&recipe_id=' . $id;
    }
    

    protected function get_recipients_ids() {
        $ids = array();
        
        $client_id = $this->getUserIdByNutritionRecipeId($this->data->id);

        if (!$client_id) {
            throw new Exception('error: no client id');
        }

        $ids[] = $client_id;
        
        $this->recipients_ids = $ids;
    }
    
}


class NutritionDiaryEmail extends FitnessEmail {
    
    protected function setParams($data) {
        $this->data = $data;
        $id = $data->id;
        
        if (!$id) {
            throw new Exception('Error: no Nutrition Diary id');
        }

        switch ($data->method) {
            case 'DiaryPass':
                $subject = 'Nutrition Diary Results';
                $layout = 'email_diary_pass';
                break;
            case 'DiaryFail':
                $subject = 'Nutrition Diary Results';
                $layout = 'email_diary_fail';
                break;
            case 'DiarySubmitted':
                $subject = 'Nutrition Diary Results';
                $layout = 'email_diary_submitted';
                break;
 
            default:
                break;
        }
        
        $this->subject = $subject;

        $this->url = JURI::root() . 'index.php?option=com_multicalendar&view=pdf&layout=' . $layout . '&tpml=component&diary_id=' . $id;
    }
    

    
    protected function get_recipients_ids() {
        $ids = array();
        
        $client_id = $this->getUserIdByDiaryId($this->data->id);

        if (!$client_id) {
            throw new Exception('error: no client id');
        }

        $ids[] = $client_id;
        
        $this->recipients_ids = $ids;
    }
    
}



class CommentEmail extends FitnessEmail {
    
    protected function setParams($data) {
        
        $this->data = $data;
        $id = $data->id;
        if (!$id) {
            throw new Exception('Error: no id');
        }

        switch ($data->method) {
            case 'GoalComment':
                $subject = 'New/Unread Message by ' . JFactory::getUser($this->data->created_by)->name;
                $layout = 'email_goal_comment';
                $goal_type = '1';
                $goal_table = '#__fitness_goals';
                if($this->data->table == '#__fitness_mini_goal_comments'){
                    $goal_type = '2';
                    $layout = 'email_goal_comment_mini';
                    $goal_table = '#__fitness_mini_goals';
                }
                
                $goal = $this->getGoal($this->data->item_id, $goal_table);
                
                $this->item = $goal['data'];
                
                $this->item_creator = $this->item->user_id;
                
                $status = $this->item->status;
                
                if((($status == self::EVELUATING_GOAL_STATUS)) OR (($status == self::ASSESSING_GOAL_STATUS))) {
                    return;
                }
                
                $user_type = $this->getUserGroup($this->data->created_by);
                
                $send_to = 'all_trainers';
                
                if(($user_type['data'] == self::getTrainersGroupId()) OR ($user_type['data'] == self::ADMINISTRATOR_USERGROUP)) {
                    $send_to = 'client_and_other_trainers'; 
                }
                
                $this->send_to = $send_to;
                $this->goal_type = $goal_type;
                $this->goal_table = $goal_table;
                $this->url = JURI::root() . 'index.php?option=com_multicalendar&view=pdf&layout=' . $layout . '&tpml=component&id=' . $this->data->item_id . '&goal_type=' . $this->goal_type . '&comment_id=' . $this->data->id;
                break;
                
            case 'RecipeComment':
                $subject = 'New/Unread Message by ' . JFactory::getUser($this->data->created_by)->name;
                $layout = 'email_recipe_comment';
                
                $this->item = $this->getRecipeOriginalData($this->data->item_id);
                
                $this->item_creator = $this->item->created_by;
                
                $user_type = $this->getUserGroup($this->data->created_by);
                
                $send_to = 'all_trainers';
                
                if(($user_type['data'] == self::getTrainersGroupId()) OR ($user_type['data'] == self::ADMINISTRATOR_USERGROUP)) {
                    $send_to = 'client_and_other_trainers'; 
                }
                
                $this->send_to = $send_to;
                $this->url = JURI::root() . 'index.php?option=com_multicalendar&view=pdf&layout=' . $layout . '&tpml=component&id=' . $this->data->item_id . '&comment_id=' . $this->data->id;
                break;
                
                
            
            default:
                return;
                break;
        }
        
        $this->subject = $subject;
       
    }
    
    
   
    protected function get_recipients_ids() {
        $ids = array();
        
        if($this->send_to == 'all_trainers') {
            
            $trainers_data = $this->getClientTrainers($this->item_creator,  'all');
            
            $ids = array_merge($ids, $trainers_data['data']);
        }
        
        if($this->send_to == 'client_and_other_trainers') {
            
            $ids[] = $this->item_creator;
            
            $all_trainers = $this->getClientTrainers($this->item_creator,  'all');
            
            $other_trainers = array_diff($all_trainers['data'], array($this->data->created_by));
            
            $ids = array_merge($ids, $other_trainers);
        }
        
        $this->recipients_ids = $ids;
    }
    
}