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
            case 'MenuPlan':
                return new MenuPlanEmail();
                break;
            case 'NutritionRecipe':
                return new NutritionRecipeEmail();
                break;
            
            case 'NutritionDiary':
                return new NutritionDiaryEmail();
                break;
            
            case 'ExerciseLibrary':
                return new ExerciseLibraryEmail();
                break;
            
            case 'NutritionDatabase':
                return new NutritionDatabaseEmail();
                break;
            
            case 'Period':
                return new PeriodEmail();
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
                    case 'ExerciseLibraryComment':
                        return new CommentExerciseLibraryEmail();
                    break;
                    case 'MenuPlanComment':
                        return new CommentMenuPlanEmail();
                    break;
                    case 'TargetsComment':
                        return new CommentTargetsCommentEmail();
                    break;
                    case 'MacrosComment':
                        return new CommentMacrosCommentEmail();
                    break;
                    case 'SupplementComment':
                        return new CommentSupplementCommentEmail();
                    break;
                    case 'ProgramComment':
                        return new CommentProgramEmail();
                    break;
                    case 'ProgramTemplateComment':
                        return new CommentProgramTemplateEmail();
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
        
        $sql = "SELECT id FROM #__fitness_email_reminder WHERE event_id='$event_id' AND client_id='$client_id'";
             
        $id = FitnessHelper::customQuery($sql, 0);
        
        if(!$id) {
            $query = "INSERT INTO #__fitness_email_reminder SET event_id='$event_id', client_id='$client_id', sent='1', confirmed='0'";
            $db->setQuery($query);
            if (!$db->query()) {
                throw new Exception($db->stderr());
            }
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
                if(!$contents[$i]) {
                    throw new Exception('Email Body is empty');
                }
                $send = $this->sendEmail($email, $this->subject, $contents[$i]);
            } else {
                if(!$contents) {
                    throw new Exception('Email Body is empty');
                }
                $send = $this->sendEmail($email, $this->subject, $contents);
            }

            if($send != '1') {
                throw new Exception('Email function error');
            }
            
            if ($this->data->method == 'Appointment') {//confirmation
                $this->setSentEmailStatus($this->data->id, $recipient_id);
            }
            
            $i++;
        }
        
        $emails = implode(', ', $emails);
        
        return $emails;
    }
    
    public function allowedUsersFilter($ids) {
        $allowed_users = $this->data->allowed_users;
        
        $allowed_users = split(",", $allowed_users);
        
        $parent_id = $this->data->parent_id;
        
        if($parent_id) {
            $table = '#__' . $this->data->table;
        
            $query = "SELECT created_by FROM $table WHERE id = '$parent_id'";

            $parent_created_by = $this->customQuery($query, 0);
            
            if($parent_created_by != $this->data->created_by)
            
            if($parent_created_by && ($parent_created_by != $this->data->created_by)) {
                $allowed_users[] = $parent_created_by;
            }
        }
        
        $ids =  array_intersect($ids, $allowed_users);
        return $ids;
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
                $send_to_client = true;
                $send_to_trainer = false;
                break;
            case 'GoalAssessing':
                $subject = 'Assess Primary Goal';
                $layout = 'email_goal_assessing';
                $goal_type = '1';
                $send_to_client = false;
                $send_to_trainer = true;
                break;
            case 'GoalScheduled':
                $subject = 'Primary Goal Scheduled';
                $layout = 'email_goal_scheduled';
                $goal_type = '1';
                $send_to_client = true;
                $send_to_trainer = false;
                break;

            //mini
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
                $send_to_client = true;
                $send_to_trainer = false;
                break;
            case 'GoalAssessingMini':
                $subject = 'Assess Mini Goal';
                $layout = 'email_goal_assessing_mini';
                $goal_type = '2';
                $send_to_client = false;
                $send_to_trainer = true;
                break;
            case 'GoalScheduledMini':
                $subject = 'Mini Goal Scheduled';
                $layout = 'email_goal_scheduled_mini';
                $goal_type = '2';
                $send_to_client = true;
                $send_to_trainer = false;
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
            case 'Appointment': //confirmation
                $subject = 'Appointment Confirmation';
                $layout = 'email_reminder';
                break;
            case 'Notify':
                $subject = 'Workout Info Available';
                $layout = 'email_notify';
                break;
            
            case 'NotifyA':
                $subject = 'Assessment Info Available';
                $layout = 'email_notify_a';
                break;
            
            case 'NotifyAssessment':
                $subject = 'Assessment Complete';
                $layout = 'email_notify_assessment';
                break;
            
            case 'Workout':
                $subject = 'Workout/Training Session';
                $layout = 'email_pdf_workout';
                break;
         
            
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
            case 'ProgramComplete':
                $subject = 'Workout Complete';
                $layout = 'email_program_complete';
                break;
            
            case 'ProgramIncomplete':
                $subject = 'Workout Incomplete';
                $layout = 'email_program_incomplete';
                break;
            
            case 'ProgramNotattempted':
                $subject = 'Workout Not Attempted';
                $layout = 'email_program_notattempted';
                break;
            
            case 'ProgramSheduled':
                $subject = 'Workout Scheduled';
                $layout = 'email_program_scheduled';
                break;
            
            case 'ProgramAssessing':
                $subject = 'New Workout Submitted';
                $layout = 'email_program_assessing';
                break;
            case 'ProgramAssessingT':
                $subject = 'New Workout Submitted';
                $layout = 'email_program_assessing_t';
                break;
            
            case 'AppointmentConfirmed':
                $subject = 'Appointment Confirmed';
                $layout = 'email_appointment_confirmed';
                break;
            
            // Assessment
            case 'AssessmentAssessing':
                $subject = 'Assessment Submitted';
                $layout = 'email_status_assessing';
                break;
            
            case 'AssessmentAssessingT':
                $subject = 'Assessment Submitted';
                $layout = 'email_status_assessing_t';
                break;
            
            case 'AssessmentAttended':
                $subject = 'Appointment Complete';
                $layout = 'email_status_attended';
                break;
              
            case 'AssessmentCancelled':
                $subject = 'Assessment Cancelled';
                $layout = 'email_status_cancelled';
                break;
            
            case 'AssessmentDistinction':
                $subject = 'Assessment Result';
                $layout = 'email_status_distinction';
                break;
            
            case 'AssessmentExcellent':
                $subject = 'Assessment Result';
                $layout = 'email_status_excellent';
                break;
            
            case 'AssessmentFail':
                $subject = 'Assessment Result';
                $layout = 'email_status_fail';
                break;
            
            case 'AssessmentImprove':
                $subject = 'Assessment Result';
                $layout = 'email_status_improve';
                break;
            
            case 'AssessmentPass':
                $subject = 'Assessment Result';
                $layout = 'email_status_pass';
                break;
            //
            case 'AssessmentBio':
                $subject = 'BioSignature Results';
                $layout = 'email_pdf_a_bio';
                break;
            case 'AssessmentStandard':
                $subject = 'Physical Assessment Results';
                $layout = 'email_pdf_a_standard';
                break;
            
            case 'AsAssessing':
                $subject = 'New Assessment Submitted';
                $layout = 'email_pdf_a_assessing';
                break;
            
            default:
                break;
        }
        
        $user_id = $this->data->user_id;
        
        if(!$user_id) {
            $user_id = JFactory::getUser()->id;
        }
       
        if($user_id) {
            //client changes status
            if(self::is_client($user_id)) {
                $send_to = 'trainers';
            }
       
            //send to one client 
            if(self::is_trainer($user_id)) {
                $send_to = 'client';
            }

            // send to all clients
            if(self::is_trainer($user_id) AND ($layout == 'email_reminder' OR $layout == 'email_notify' OR $layout == 'email_notify_a')) {
                $send_to = 'clients';
            }

            //client sends workout heself
            if(self::is_client($user_id) AND ($layout == 'email_pdf_workout' OR $layout == 'email_pdf_a_bio' OR $layout == 'email_pdf_a_standard')) {
                $send_to = 'client';
            }
        }
        
        //confirmed email
        if($layout == 'email_appointment_confirmed') {
            $send_to = 'trainers';
        }


        if($send_to) {
            $this->send_to = $send_to;
        }
         
        $this->subject = $subject;
        $this->layout = $layout;
    }

    protected function get_recipients_ids() {
        
        $ids = array();

        $item = $this->getAppointmentClientItem($this->data->id);
        
        $this->event_id = $item->event_id;
        
        $this->item = $item;

        if($this->send_to == 'client') {
            $ids[] = $item->client_id;
        }
      
        if($this->send_to == 'trainers') {
 
            $trainers_data = $this->getClientTrainers($item->client_id,  'all');
            
            $ids = $trainers_data['data'];
        }
        
        if($this->send_to == 'clients') {
            $this->event_id = $this->data->id;
            
            $event_clients = $this->getEventClients($this->data->id);
            $clients = array();
        
            foreach ($event_clients as $client) {
                
                $clients[] = $client->client_id;
            }
            
            $ids = $clients;
        }
        
                
        //client sends workout heself
        if(($this->send_to == 'client') AND ($this->layout == 'email_pdf_workout' OR $this->layout == 'email_pdf_a_bio' OR $this->layout == 'email_pdf_a_standard')) {
            $ids = array(JFactory::getUser()->id);
            $this->event_id = $this->data->id;
            
        }
        
        //trainer sends workout to client
        if($this->data->to_client_only) {
            $ids = array($this->data->client_id);
            $this->event_id = $this->data->id;
        }
        
        

        $this->recipients_ids = $ids;
    }
    
    protected function generate_contents(){
        $contents = array();

        foreach ($this->recipients_ids as $recipient_id) {
            if(!$recipient_id)  continue;
            $event_id = $this->event_id;
            $client_id = $this->item->client_id;
            
            if($this->send_to == 'clients') {
                $client_id = $recipient_id;
            }
            
            if($this->layout == 'email_pdf_workout'
                    OR $this->layout == 'email_pdf_a_bio'
                    OR $this->layout == 'email_pdf_a_standard'
            ){
                $client_id = $this->data->client_id;
            }

            $url = JURI::root() . 'index.php?option=com_multicalendar&view=pdf&layout=' . $this->layout . '&tpml=component&event_id=' . $event_id  . '&client_id=' . $client_id;
            
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
            case 'email_pdf_recipe':
                $subject = 'Nutrition Recipe Details';
                $layout = 'email_pdf_recipe';
                $this->client_id = JFactory::getUser()->id;
                break;
            
            case 'email_pdf_shopping_list':
                $subject = 'Shopping List';
                $layout = 'email_pdf_shopping_list';
                $this->client_id = JFactory::getUser()->id;
                break;
            
            
 
            default:
                break;
        }
        
        $this->url = JURI::root() . 'index.php?option=com_multicalendar&view=pdf&layout=' . $layout . '&tpml=component&id=' . $id;
        
        if($this->client_id) {
            $this->url .= '&client_id=' . $this->client_id;
        }
        
        if($data->checked) {
            $this->url .= '&checked=' . $data->checked;
        }
        
        $this->subject = $subject;

    }
    

    protected function get_recipients_ids() {
        $ids = array();
        
        if($this->data->method == 'Notify') {
            $client_id = $this->getClientIdByNutritionPlanId($this->data->id);
        }
        
        if($this->data->method == 'email_pdf_nutrition_plan_macros'
                OR $this->data->method == 'email_pdf_nutrition_plan_supplements' 
                OR $this->data->method == 'email_pdf_nutrition_guide'
                OR $this->data->method == 'email_pdf_recipe'
                OR $this->data->method == 'email_pdf_shopping_list'
        ) {
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
        if($this->data->table == 'fitness_mini_goal_comments'){
            $goal_type = '2';
            $layout = 'email_goal_comment_mini';
            $goal_table = '#__fitness_mini_goals';
        }

        $goal = $this->getGoal($this->data->item_id, $goal_table);

        $this->item = $goal['data'];

        $this->item_user_id = $this->item->user_id;

        $status = $this->item->status;

        if(($status == self::EVELUATING_GOAL_STATUS) OR ($status == self::ASSESSING_GOAL_STATUS) OR ($status == self::PENDING_GOAL_STATUS)) {
            return;
        }

        $send_to = 'all_trainers';

        if(self::is_trainer($this->data->created_by) OR self::is_superuser($this->data->created_by))  {
            $send_to = 'to_client'; 
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
        if($this->send_to == 'to_client') {
           //add client
            $ids[] = $this->item_user_id;

        }
        
        $ids = $this->allowedUsersFilter($ids);

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
        
        $ids = $this->allowedUsersFilter($ids);

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
                return;
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
        
        $ids = $this->allowedUsersFilter($ids);
        
        $this->recipients_ids = $ids;
    }
    
}





class CommentExerciseLibraryEmail extends FitnessEmail {
    
    protected function setParams($data) {
        
        $this->data = $data;
        $id = $data->id;
        if (!$id) {
            throw new Exception('Error: comment id');
        }

        $subject = 'New/Unread Message by ' . JFactory::getUser($this->data->created_by)->name;
        $layout = 'email_exercise_library_comment';

        $this->item = $this->getExerciseVideo($this->data->item_id);

        $this->item_created_by = $this->item->created_by;
        
        $this->comment_created_by = $this->data->created_by;

        // if item is global and comment created by superuser
        if((self::is_superuser($this->item_created_by) OR (self::is_trainer($this->item_created_by))) AND self::is_superuser($this->comment_created_by)) {
            $this->condition = 'global_superuser';
        }
        
        
        
        // if item is global and comment created by trainer
        if((self::is_superuser($this->item_created_by) OR (self::is_trainer($this->item_created_by))) AND self::is_trainer($this->comment_created_by)) {
            $this->condition = 'global_trainer';
        }
        
        
        
        // if item is global and comment created by client
        if((self::is_superuser($this->item_created_by) OR (self::is_trainer($this->item_created_by))) AND self::is_client($this->comment_created_by)) {
            $this->condition = 'global_client';
        }
        
        

        // if item is private and comment created by superuser
        if(self::is_client($this->item_created_by) AND self::is_superuser($this->comment_created_by)) {
            $this->condition = 'private_superuser';
        }
        
        
        // if item is private and comment created by trainer
        if(self::is_client($this->item_created_by) AND self::is_trainer($this->comment_created_by)) {
            $this->condition = 'private_trainer';
        }
        
        
        // if item is private and comment created by client
        if(self::is_client($this->item_created_by) AND self::is_client($this->comment_created_by)) {
            $this->condition = 'private_client';
        }
        
        $this->url = JURI::root() . 'index.php?option=com_multicalendar&view=pdf&layout=' . $layout . '&tpml=component&id=' . $this->data->item_id . '&comment_id=' . $this->data->id;
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
            case 'private_superuser': // comment email to client (item creator)
                $ids = array_merge($ids, array($this->item_created_by));
                break;
            case 'private_trainer': // comment email to client (item creator)
                //$ids = array_merge($ids, array($this->item_created_by));
                $ids = array_merge($ids, $this->getMyExerciseListClients($this->data->item_id));
                break;
            case 'private_client': // comment email to client's trainers
                $trainers_data = $this->getClientTrainers($this->comment_created_by,  'all');
                $ids = array_merge($ids, $trainers_data['data']);
                break;
            default:
                break;
        }
        
        $ids = $this->allowedUsersFilter($ids);

        $this->recipients_ids = $ids;
    }
    
    
    
    public function getMyExerciseListClients($id) {
        $query = "SELECT my_exercise_clients FROM #__fitness_exercise_library WHERE id='$id'";
        
        $clients = self::customQuery($query, 0);
        
        return split(",", $clients);
        
    }

}


class ExerciseLibraryEmail extends FitnessEmail {
    
    protected function setParams($data) {
        $this->data = $data;
        $id = $data->id;
        
        if (!$id) {
            throw new Exception('Error: no Exercise Library id');
        }

        switch ($data->method) {
            case 'Approved':
                $subject = 'Exercise Video Approved';
                $layout = 'email_exercise_library_approved';
                $this->send_to = 'send_to_client';
                break;
            case 'NotApproved':
                $subject = 'Exercise Video Not Approved';
                $layout = 'email_exercise_library_notapproved';
                $this->send_to = 'send_to_client';
                break;
            case 'NewExercise':
                $subject = 'New Exercise Video Created';
                $layout = 'email_exercise_library_submitted';
                $this->send_to = 'send_to_trainers';
                break;
 
            default:
                break;
        }
        
        $this->subject = $subject;

        $this->url = JURI::root() . 'index.php?option=com_multicalendar&view=pdf&layout=' . $layout . '&tpml=component&id=' . $id;
    }
    

    protected function get_recipients_ids() {
        $ids = array();
        
        $this->item = $this->getExerciseVideo($this->data->id);
        
        $client_id = $this->item->created_by;

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



class NutritionDatabaseEmail extends FitnessEmail {
    
    protected function setParams($data) {
        $this->data = $data;
        $id = $data->id;
        
        if (!$id) {
            throw new Exception('Error: no item id');
        }
        
        $subject = 'New Nutrition Database Item added';
        $layout = 'email_new_nd_item';

        
        $this->subject = $subject;

        $this->url = JURI::root() . 'index.php?option=com_multicalendar&view=pdf&layout=' . $layout . '&tpml=component&id=' . $id;
    }
    

    protected function get_recipients_ids() {
        $ids = array();
        
        $query = "SELECT user_id FROM #__user_usergroup_map WHERE group_id='8'";
        
        $ids = self::customQuery($query, 3);
                
        $this->recipients_ids = $ids;
    }
    
}



class MenuPlanEmail extends FitnessEmail {
    
    protected function setParams($data) {
        $this->data = $data;
        $id = $data->id;
        
        if (!$id) {
            throw new Exception('Error: no menu plan id');
        }

        switch ($data->method) {
            case 'menu_plan_pending':
                $subject = 'New Menu Plan Created';
                $layout = 'email_menu_plan_pending';
                break;
            case 'menu_plan_approved':
                $subject = 'Nutrition Plan: Menu Approved';
                $layout = 'email_menu_plan_approved';
                break;
            case 'menu_plan_notapproved':
                $subject = 'Nutrition Plan: Menu Not Approved';
                $layout = 'email_menu_plan_notapproved';
                break;
            case 'menu_plan_inprogress':
                $subject = 'Nutrition Plan: New Menu Created';
                $layout = 'email_menu_plan_inprogress';
                break;
            case 'menu_plan_submitted':
                $subject = 'Menu Plan Submitted';
                $layout = 'email_menu_plan_submitted';
                break;
            
            case 'menu_plan_resubmit':
                $subject = 'Nutrition Plan: Resubmit Your Menu';
                $layout = 'email_menu_plan_resubmit';
                break;

            default:
                break;
        }
        
        $this->url = JURI::root() . 'index.php?option=com_multicalendar&view=pdf&layout=' . $layout . '&tpml=component&id=' . $id;

        $this->subject = $subject;

    }
    

    protected function get_recipients_ids() {
        
        $this->item = $this->getMenuPlanData($this->data->id);
        
        $client_id = $this->getClientIdByNutritionPlanId($this->item->nutrition_plan_id);
        
        if (!$client_id) {
            throw new Exception('error: no client id');
        }
        
        $ids = array();
        
        // to client
        if($this->data->method == 'menu_plan_approved'
            OR $this->data->method == 'menu_plan_notapproved' 
            OR $this->data->method == 'menu_plan_resubmit'     
            OR $this->data->method == 'menu_plan_inprogress'  
        ) {

            $ids[] = $client_id;
        }
        
        // to trainers
        if($this->data->method == 'menu_plan_pending'
            OR $this->data->method == 'menu_plan_submitted' 
            OR $this->data->method == 'menu_plan_inprogress'     
        ) {
            $trainers_data = $this->getClientTrainers($client_id,  'all');

            $ids = array_merge($ids, $trainers_data['data']);
        }

        $this->recipients_ids = $ids;
    }

}

class CommentMenuPlanEmail extends FitnessEmail {
    
    protected function setParams($data) {
        
        $this->data = $data;
        $id = $data->id;
        if (!$id) {
            throw new Exception('Error: comment id');
        }

        $subject = 'New/Unread Message by ' . JFactory::getUser($this->data->created_by)->name;
        
        $layout = 'email_menu_plan_comment';

        $this->item = $this->getExampleDayMenu($this->data->item_id);
        
        $this->item_created_by = $this->item->created_by;
        
        $this->comment_created_by = $this->data->created_by;
        
        $status = $this->item->status;
        
        if($status == self::INPROGRESS_MENU_PLAN_STATUS) {
            if(self::is_client($this->comment_created_by)) {
                $send_to = 'all_trainers';
            }
            if(self::is_trainer($this->comment_created_by)) {
                $send_to = 'client_all_trainers';
            }
        }
        
        
        if($status == self::SUBMITTED_MENU_PLAN_STATUS) {
            if(self::is_client($this->comment_created_by)) {
                return;
            }
            if(self::is_trainer($this->comment_created_by)) {
                return;
            }
        }
        
        
        if($status == self::PENDING_MENU_PLAN_STATUS) {
            if(self::is_client($this->comment_created_by)) {
                return;
            }
            if(self::is_trainer($this->comment_created_by)) {
                return;
            }
        }
        
        
        if($status == self::RESUBMIT_MENU_PLAN_STATUS) {
            if(self::is_client($this->comment_created_by)) {
                $send_to = 'all_trainers';
            }
            if(self::is_trainer($this->comment_created_by)) {
                $send_to = 'client_all_trainers';
            }
        }
        
        
        if($status == self::APPROVED_MENU_PLAN_STATUS) {
            if(self::is_client($this->comment_created_by)) {
                $send_to = 'all_trainers';
            }
            if(self::is_trainer($this->comment_created_by)) {
                $send_to = 'client_all_trainers';
            }
        }
        
        
        if($status == self::NOTAPPROVED_MENU_PLAN_STATUS) {
            if(self::is_client($this->comment_created_by)) {
                $send_to = 'all_trainers';
            }
            if(self::is_trainer($this->comment_created_by)) {
                $send_to = 'client_all_trainers';
            }
        }
        
        if($send_to) {
            $this->send_to = $send_to;
        }
        
        $this->url = JURI::root() . 'index.php?option=com_multicalendar&view=pdf&layout=' . $layout . '&tpml=component&id=' . $this->data->item_id . '&comment_id=' . $this->data->id;
        $this->subject = $subject;
       
    }
    
    
   
    protected function get_recipients_ids() {
        
        $ids = array();
        
        if(!$this->send_to) {
            return;
        }
        
        $client_id = $this->getClientIdByNutritionPlanId($this->item->nutrition_plan_id);
        
        $trainers_data = $this->getClientTrainers($client_id,  'all');
        
        if (!$client_id) {
            throw new Exception('error: no client id');
        }
        
        if($this->send_to == 'all_trainers') {
            $ids = array_merge($ids, $trainers_data['data']);
        }
        
        if($this->send_to == 'client_all_trainers') {
            $ids[] = $client_id;
            
            $ids = array_merge($ids, $trainers_data['data']);
        }
        
        $ids = $this->allowedUsersFilter($ids);
        
        $this->recipients_ids = $ids;
    }
}





class CommentTargetsCommentEmail extends FitnessEmail {
    
    protected function setParams($data) {
        
        $this->data = $data;
        $id = $data->id;
        if (!$id) {
            throw new Exception('Error: comment id');
        }

        $subject = 'New/Unread Message by ' . JFactory::getUser($this->data->created_by)->name;
        
        $layout = 'email_targets_comment';

        $this->item = $this->getPlanData($this->data->item_id);
        
        $this->comment_created_by = $this->data->created_by;
        
        if(self::is_client($this->comment_created_by)) {
            $send_to = 'all_trainers';
        }
        
        if(self::is_trainer($this->comment_created_by)) {
            $send_to = 'client';
        }

        
        if($send_to) {
            $this->send_to = $send_to;
        }
        
        $this->url = JURI::root() . 'index.php?option=com_multicalendar&view=pdf&layout=' . $layout . '&tpml=component&id=' . $this->data->item_id . '&comment_id=' . $this->data->id;
        $this->subject = $subject;
       
    }
    
    
   
    protected function get_recipients_ids() {
        
        $ids = array();
        
        if(!$this->send_to) {
            return;
        }
        
        $client_id = $this->getClientIdByNutritionPlanId($this->item->id);
        
        $trainers_data = $this->getClientTrainers($client_id,  'all');
        
        if (!$client_id) {
            throw new Exception('error: no client id');
        }
        
        if($this->send_to == 'all_trainers') {
            $ids = array_merge($ids, $trainers_data['data']);
        }
        
        if($this->send_to == 'client') {
            $ids[] = $client_id;
        }
        
        $ids = $this->allowedUsersFilter($ids);
        
        $this->recipients_ids = $ids;
    }
}



class CommentMacrosCommentEmail extends FitnessEmail {
    
    protected function setParams($data) {
        
        $this->data = $data;
        $id = $data->id;
        if (!$id) {
            throw new Exception('Error: comment id');
        }

        $subject = 'New/Unread Message by ' . JFactory::getUser($this->data->created_by)->name;
        
        $layout = 'email_macros_comment';

        $this->item = $this->getPlanData($this->data->item_id);
        
        $this->comment_created_by = $this->data->created_by;
        
        if(self::is_client($this->comment_created_by)) {
            $send_to = 'all_trainers';
        }
        
        if(self::is_trainer($this->comment_created_by)) {
            $send_to = 'client';
        }

        
        if($send_to) {
            $this->send_to = $send_to;
        }
        
        $this->url = JURI::root() . 'index.php?option=com_multicalendar&view=pdf&layout=' . $layout . '&tpml=component&id=' . $this->data->item_id . '&comment_id=' . $this->data->id;
        $this->subject = $subject;
       
    }
    
    
   
    protected function get_recipients_ids() {
        
        $ids = array();
        
        if(!$this->send_to) {
            return;
        }
        
        $client_id = $this->getClientIdByNutritionPlanId($this->item->id);
        
        $trainers_data = $this->getClientTrainers($client_id,  'all');
        
        if (!$client_id) {
            throw new Exception('error: no client id');
        }
        
        if($this->send_to == 'all_trainers') {
            $ids = array_merge($ids, $trainers_data['data']);
        }
        
        if($this->send_to == 'client') {
            $ids[] = $client_id;
        }
        
        $ids = $this->allowedUsersFilter($ids);
        
        $this->recipients_ids = $ids;
    }
}





class CommentSupplementCommentEmail extends FitnessEmail {
    
    protected function setParams($data) {
        
        $this->data = $data;
        $id = $data->id;
        if (!$id) {
            throw new Exception('Error: comment id');
        }

        $subject = 'New/Unread Message by ' . JFactory::getUser($this->data->created_by)->name;
        
        $layout = 'email_supplement_comment';
        
        $this->item = $this->getPlanProtocol($this->data->sub_item_id);

        $this->comment_created_by = $this->data->created_by;
        
        if(self::is_client($this->comment_created_by)) {
            $send_to = 'all_trainers';
        }
        
        if(self::is_trainer($this->comment_created_by)) {
            $send_to = 'client';
        }

        
        if($send_to) {
            $this->send_to = $send_to;
        }
        
        $this->url = JURI::root() . 'index.php?option=com_multicalendar&view=pdf&layout=' . $layout . '&tpml=component&id=' . $this->data->sub_item_id . '&comment_id=' . $this->data->id;
        $this->subject = $subject;
       
    }
    
    
   
    protected function get_recipients_ids() {
        
        $ids = array();
        
        if(!$this->send_to) {
            return;
        }
        
        $client_id = $this->getClientIdByNutritionPlanId($this->item->nutrition_plan_id);
        
        $trainers_data = $this->getClientTrainers($client_id,  'all');
        
        if (!$client_id) {
            throw new Exception('error: no client id');
        }
        
        if($this->send_to == 'all_trainers') {
            $ids = array_merge($ids, $trainers_data['data']);
        }
        
        if($this->send_to == 'client') {
            $ids[] = $client_id;
        }
        
        $ids = $this->allowedUsersFilter($ids);
        
        $this->recipients_ids = $ids;
    }
}



class CommentProgramEmail extends FitnessEmail {
    
    protected function setParams($data) {
        
        $this->data = $data;
        $id = $data->id;
        if (!$id) {
            throw new Exception('Error: comment id');
        }

        $subject = 'New/Unread Message by ' . JFactory::getUser($this->data->created_by)->name;
        
        $layout = 'email_program_comment';
        
        $this->item = $this->getEvent($this->data->item_id);

        $this->comment_created_by = $this->data->created_by;
        
        //client makes a comment
        if(self::is_client($this->comment_created_by) && $this->item->published && $this->item->frontend_published) {
            $send_to = 'trainers_clients';
        }
        
        //trainer makes a comment
        if(self::is_trainer($this->comment_created_by) && $this->item->published && $this->item->frontend_published) {
            $send_to = 'clients';
        }

        if($send_to) {
            $this->send_to = $send_to;
        }
        
        $this->layout = $layout;

        $this->subject = $subject;
       
    }
    
    
   
    protected function get_recipients_ids() {
        $ids = array();
        
        if(!$this->send_to) {
            return;
        }
        
        $this->item->client_items = $this->getAppointmentClientItems($this->item->id);

        //client makes a comment send to other clients and client's trainers
        if($this->send_to == 'trainers_clients') {
            $clients = array();
        
            foreach ($this->item->client_items as $item) {
                $clients[] = $item->client_id;
            }
            
            $ids = $clients;
            
            $trainers_data = $this->getClientTrainers($this->comment_created_by,  'all');
            
            $ids = array_merge($ids, $trainers_data['data']);
            
        }
        
        //trainer makes a comment send to other clients
        if($this->send_to == 'clients') {
            foreach ($this->item->client_items as $item) {
                // if status is not ASSESSING
                if($item->status != '10') {
                    $ids[] = $item->client_id;
                }
            }
        }
        
        // send except cteator
        $ids = array_diff($ids, array($this->data->created_by));
        
        $ids = $this->allowedUsersFilter($ids);
        
        $this->recipients_ids = $ids;
    }
    
    protected function generate_contents(){
        $contents = array();
        foreach ($this->recipients_ids as $recipient_id) {
            if(!$recipient_id)  continue;
            $url = JURI::root() . 'index.php?option=com_multicalendar&view=pdf&layout=' . $this->layout . '&tpml=component&event_id=' . $this->data->item_id . '&comment_id=' . $this->data->id . '&client_id=' . $recipient_id;
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





class CommentProgramTemplateEmail extends FitnessEmail {
    
    protected function setParams($data) {
 
        $this->data = $data;
        $id = $data->id;
        if (!$id) {
            throw new Exception('Error: comment id');
        }

        $subject = 'New/Unread Message by ' . JFactory::getUser($this->data->created_by)->name;
        
        $layout = 'email_pr_temp_comment';

        $this->comment_created_by = $this->data->created_by;

        // only trainer can make a comment
        if(!self::is_trainer($this->comment_created_by)) {
            return;
        }

        $this->layout = $layout;
        
        $this->url = JURI::root() . 'index.php?option=com_multicalendar&view=pdf&layout=' . $layout . '&tpml=component&id=' . $this->data->item_id . '&comment_id=' . $this->data->id;

        $this->subject = $subject;
       
    }
    
    
   
    protected function get_recipients_ids() {
        $ids = array();
 
        // get all trainers who leave a comment
        
        $item_id = $this->data->item_id;
        $created_by = $this->comment_created_by;
        $query = "SELECT DISTINCT created_by FROM #__fitness_pr_temp_comments WHERE item_id='$item_id' AND created_by NOT IN ($created_by)";
        
        $ids = FitnessHelper::customQuery($query, 3);
     
        $ids = $this->allowedUsersFilter($ids);
        
        $this->recipients_ids = $ids;
    }
    

}


class PeriodEmail extends FitnessEmail {
    
    protected function setParams($data) {
        $this->data = $data;
        $id = $data->id;
        
        if (!$id) {
            throw new Exception('Error: no Nutrition Recipe id');
        }

        switch ($data->method) {
            case 'PeriodOverview':
                $subject = 'TRAINING PERIOD OVERVIEW';
                $layout = 'email_pdf_period';
                break;
 
            default:
                break;
        }
        
        $this->subject = $subject;

        $this->url = JURI::root() . 'index.php?option=com_multicalendar&view=pdf&layout=' . $layout . '&tpml=component&id=' . $id . '&client_id=' . $this->data->client_id;
    }
    

    protected function get_recipients_ids() {
        if($this->data->send_to == 'to_client') {
            $ids = array($this->data->client_id);
        }
        
        if($this->data->send_to == 'to_trainer') {
            $trainers_data = $this->getClientTrainers($this->data->client_id,  'primary');
            
            if(!$trainers_data['status']['success']) {
                throw new Exception($trainers_data['status']['message']);
            }
            
            $ids = array($trainers_data['data'][0]);
        }

        
        $this->recipients_ids = $ids;
    }
    
}