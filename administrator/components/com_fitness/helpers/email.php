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

    
    public static function factory($data) {
        
        switch ($data->view) {
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
                switch ($data->method) {
                    case 'GoalComment':
                        return new CommentGoalEmail();
                    break;
                    case 'RecipeComment':
                        return new CommentRecipeEmail();
                    break;
                    case 'DiaryComment':
                        return new CommentDiaryEmail();
                    break;
                    default:
                        return;
                    break;
                }

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
                $send_to_client = false;
                $send_to_trainer = true;
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
                $layout = 'email_pdf_workout';
                break;
            
            default:
                break;
        }
        
        $this->subject = $subject;
        $this->layout = $layout;
    }

    protected function get_recipients_ids() {
        
        
        if((int)$this->data->appointment_client_id) {
            $client_id = $this->getClientIdByAppointmentClientId($this->data->appointment_client_id);
            $ids =  array($client_id);
        } else {
            $ids = $this->getClientsByEvent($this->data->id);
        }

        
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
            case 'email_pdf_nutrition_plan_macros':
                $subject = 'Nutrition Plan: Allowed Macronutrients';
                $layout = 'email_pdf_nutrition_plan_macros';
                $this->client_id = JFactory::getUser()->id;
                break;
            case 'email_pdf_nutrition_plan_supplements':
                $subject = 'Nutrition Plan: Supplement Protocols';
                $layout = 'email_pdf_nutrition_plan_supplements';
                $this->client_id = JFactory::getUser()->id;
                break;
            case 'email_pdf_nutrition_guide':
                $subject = 'Nutrition Plan: Daily Nutrition Guide';
                $layout = 'email_pdf_nutrition_guide';
                $this->client_id = JFactory::getUser()->id;
                break;
 
            default:
                break;
        }
        
        $this->url = JURI::root() . 'index.php?option=com_multicalendar&view=pdf&layout=' . $layout . '&tpml=component&id=' . $id;
        
        if($this->client_id) {
            $this->url .= '&client_id=' . $this->client_id;
        }
        
        $this->subject = $subject;

    }
    

    protected function get_recipients_ids() {
        $ids = array();
        
        if($data->method == 'Notify') {
            $client_id = $this->getClientIdByNutritionPlanId($this->data->id);
        }
        
        if($this->data->method == 'email_pdf_nutrition_plan_macros' OR $this->data->method == 'email_pdf_nutrition_plan_supplements' OR $this->data->method == 'email_pdf_nutrition_guide') {
            $client_id = $this->client_id;
        }

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
                $this->send_to = 'send_to_client';
                break;
            case 'NotApproved':
                $subject = 'Recipe Not Approved';
                $layout = 'email_recipe_notapproved';
                $this->send_to = 'send_to_client';
                break;
            case 'NewRecipe':
                $subject = 'New Recipe Created';
                $layout = 'email_new_recipe';
                $this->send_to = 'send_to_trainers';
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
        
        if($this->send_to == 'send_to_client') {
            $ids[] = $client_id;
        }
        
        if($this->send_to == 'send_to_trainers') {
            
            $trainers_data = $this->getClientTrainers($client_id,  'all');
            
            $ids = array_merge($ids, $trainers_data['data']);
        }
        
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
                $subject = 'Nutrition Diary Submitted';
                $layout = 'email_diary_submitted';
                break;
            case 'DiaryDistinction':
                $subject = 'Nutrition Diary Results';
                $layout = 'email_diary_distinction';
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



class CommentGoalEmail extends FitnessEmail {
    
    protected function setParams($data) {
        
        $this->data = $data;
        $id = $data->id;
        if (!$id) {
            throw new Exception('Error: no comment id');
        }

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

        $this->item_user_id = $this->item->user_id;

        $status = $this->item->status;

        if((($status == self::EVELUATING_GOAL_STATUS)) OR (($status == self::ASSESSING_GOAL_STATUS))) {
            return;
        }

        $send_to = 'all_trainers';

        if(self::is_trainer($this->data->created_by) OR self::is_superuser($this->data->created_by))  {
            $send_to = 'client_and_other_trainers'; 
        }

        $this->send_to = $send_to;
        $this->goal_type = $goal_type;
        $this->goal_table = $goal_table;
        $this->url = JURI::root() . 'index.php?option=com_multicalendar&view=pdf&layout=' . $layout . '&tpml=component&id=' . $this->data->item_id . '&goal_type=' . $this->goal_type . '&comment_id=' . $this->data->id;
        
        $this->subject = $subject;
       
    }
    
    
   
    protected function get_recipients_ids() {
        $ids = array();
        //client makes comment
        if($this->send_to == 'all_trainers') {
            
            $trainers_data = $this->getClientTrainers($this->item_user_id,  'all');
            
            $ids = array_merge($ids, $trainers_data['data']);
        }
        // trainer  makes comment
        if($this->send_to == 'client_and_other_trainers') {
           //add client
            $ids[] = $this->item_user_id;
            
            $all_trainers = $this->getClientTrainers($this->item_user_id,  'all');
            //add client trainers, except trainer who created a comment
            $other_trainers = array_diff($all_trainers['data'], array($this->data->created_by));
            
            $ids = array_merge($ids, $other_trainers);
        }

        $this->recipients_ids = $ids;
    }
    
}




class CommentRecipeEmail extends FitnessEmail {
    
    protected function setParams($data) {
        
        $this->data = $data;
        $id = $data->id;
        if (!$id) {
            throw new Exception('Error: comment id');
        }

        $subject = 'New/Unread Message by ' . JFactory::getUser($this->data->created_by)->name;
        $layout = 'email_recipe_comment';

        $this->item = $this->getRecipeOriginalData($this->data->item_id);

        $this->recipe_created_by = $this->item->created_by;
        
        $this->comment_created_by = $this->data->created_by;
        
        // $this->item - recipe object
        // $this->data - comment object
        
        
        // if recipe is global and comment created by superuser
        if((self::is_superuser($this->recipe_created_by) OR (self::is_trainer($this->recipe_created_by))) AND self::is_superuser($this->comment_created_by)) {
            $this->condition = 'global_superuser';
        }
        
        
        
        // if recipe is global and comment created by trainer
        if((self::is_superuser($this->recipe_created_by) OR (self::is_trainer($this->recipe_created_by))) AND self::is_trainer($this->comment_created_by)) {
            $this->condition = 'global_trainer';
        }
        
        
        
        // if recipe is global and comment created by client
        if((self::is_superuser($this->recipe_created_by) OR (self::is_trainer($this->recipe_created_by))) AND self::is_client($this->comment_created_by)) {
            $this->condition = 'global_client';
        }
        
        

        // if recipe is private and comment created by superuser
        if(self::is_client($this->recipe_created_by) AND self::is_superuser($this->comment_created_by)) {
            $this->condition = 'private_superuser';
        }
        
        
        // if recipe is private and comment created by trainer
        if(self::is_client($this->recipe_created_by) AND self::is_trainer($this->comment_created_by)) {
            $this->condition = 'private_trainer';
        }
        
        
        // if recipe is private and comment created by client
        if(self::is_client($this->recipe_created_by) AND self::is_client($this->comment_created_by)) {
            $this->condition = 'private_client';
        }
        
        $this->url = JURI::root() . 'index.php?option=com_multicalendar&view=pdf&layout=' . $layout . '&tpml=component&recipe_id=' . $this->data->item_id . '&comment_id=' . $this->data->id;
        $this->subject = $subject;
       
    }
    
    
   
    protected function get_recipients_ids() {
        
        $ids = array();
        
        switch ($this->condition) {
            case 'global_superuser': // no comment email
                // nobody
                break;
            case 'global_trainer': // comment email to trainer's clients
                $trainer_clients = $this->getTrainerClients($this->comment_created_by);
                $ids = array_merge($ids, $trainer_clients);
                break;
            case 'global_client': // comment email to client's trainers
                $trainers_data = $this->getClientTrainers($this->comment_created_by,  'all');
                $ids = array_merge($ids, $trainers_data['data']);
                break;
            case 'private_superuser': // comment email to client (recipe creator)
                $ids = array_merge($ids, array($this->recipe_created_by));
                break;
            case 'private_trainer': // comment email to client (recipe creator)
                $ids = array_merge($ids, array($this->recipe_created_by));
                break;
            case 'private_client': // comment email to client's trainers
                $trainers_data = $this->getClientTrainers($this->comment_created_by,  'all');
                $ids = array_merge($ids, $trainers_data['data']);
                break;
            default:
                break;
        }

        $this->recipients_ids = $ids;
    }
    
}



class CommentDiaryEmail extends FitnessEmail {
    
    protected function setParams($data) {
        
        $this->data = $data;
        $id = $data->id;
        if (!$id) {
            throw new Exception('Error: no comment id');
        }

        $subject = 'New/Unread Message by ' . JFactory::getUser($this->data->created_by)->name;
        $layout = 'email_diary_comment';

        $diary = $this->getDiary($this->data->item_id);

        $this->item = $diary;
 

        $this->item_user_id = $this->item->client_id;

        $status = $this->item->status;

        //
        if($status == self::INPROGRESS_DIARY_STATUS OR $status == '0') {
           
            if(self::is_client($this->data->created_by)) {
                return;
            }
            
            if(self::is_trainer($this->data->created_by))  {
                $send_to = 'client'; 
            }
        }
        
        //
        if($status == self::SUBMITTED_DIARY_STATUS) {
            
            if(self::is_client($this->data->created_by)) {
                return;
            }
            
            if(self::is_trainer($this->data->created_by))  {
                return;
            }
            
        }
        
        //
        if($status == self::PASS_DIARY_STATUS OR $status == self::FAIL_DIARY_STATUS OR $status == self::DISTINCTION_DIARY_STATUS) {
            
            if(self::is_client($this->data->created_by)) {
                $send_to = 'all_trainers';
            }
            
            if(self::is_trainer($this->data->created_by))  {
                $send_to = 'client'; 
            }
        }

        $this->send_to = $send_to;

        $this->url = JURI::root() . 'index.php?option=com_multicalendar&view=pdf&layout=' . $layout . '&tpml=component&diary_id=' . $this->data->item_id . '&comment_id=' . $this->data->id;
        
        $this->subject = $subject;
       
    }
    
    
   
    protected function get_recipients_ids() {
        $ids = array();
        //client makes a comment
        if($this->send_to == 'all_trainers') {
            
            $trainers_data = $this->getClientTrainers($this->item_user_id,  'all');
            
            $ids = array_merge($ids, $trainers_data['data']);
        }
        // trainer  makes a comment
        if($this->send_to == 'client') {
           //add client
            $ids[] = $this->item_user_id;
        }

        $this->recipients_ids = $ids;
    }
    
}


