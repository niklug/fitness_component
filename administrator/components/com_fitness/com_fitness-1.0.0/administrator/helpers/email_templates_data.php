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

class EmailTemplateData extends FitnessHelper
{

    public static function factory($params) {
        
        switch ($params['method']) {
            case 'Recipe':
                return new RecipeEmailTemplateData($params);
                break;
            case 'Goal':
                return new GoalEmailTemplateData($params);
                break;
            case 'Appointment':
                return new AppointmentEmailTemplateData($params);
                break;
            
            case 'Diary':
                return new DiaryEmailTemplateData($params);
                break;
            case 'NutritionPlan':
                return new NutritionPlanEmailTemplateData($params);
                break;
            case 'EmailPdfWorkout' : 
                return new EmailPdfWorkoutTemplateData($params);
                break;
            
            case 'EmailPdfNutritionPlanMacros' : 
                return new EmailPdfNutritionPlanMacros($params);
                break;
            
            case 'EmailPdfNutritionPlanSupplements' : 
                return new EmailPdfNutritionPlanSupplements($params);
                break;
            case 'EmailPdfNutritionGuide' : 
                return new EmailPdfNutritionGuide($params);
                break;
            
            case 'EmailPdfRecipe' : 
                return new EmailPdfRecipe($params);
                break;
            case 'EmailPdfShoppingList' : 
                return new EmailPdfShoppingList($params);
                break;
            case 'ExerciseLibrary' : 
                return new EmailPdfExerciseLibrary($params);
                break;
            case 'NutritionDatabase' : 
                return new NutritionDatabaseTemplateData($params);
                break;
            case 'MenuPlan' : 
                return new MenuPlanTemplateData($params);
                break;
            
            case 'Supplement':
                return new SupplementEmailTemplateData($params);
                break;
            
            case 'ProgramTemplate':
                return new ProgramTemplateEmailTemplateData($params);
                break;
            
            case 'EmailPdfPeriod':
                return new EmailPdfPeriod($params);
                break;

            default:
                break;
        }
    }
    
    protected function getBusinessProfileData() {
        if(!$this->business_profile_user) return;

        $business_profile_id = $this->getBusinessProfileId($this->business_profile_user);
        
        $business_profile = $this->getBusinessProfile($business_profile_id['data']);
        
        $business_profile = $business_profile['data'];
        
        $this->business_profile = $business_profile;
    }
    
       
    public function processing() {
        
        $this->getItemData();
        
        $this->getBusinessProfileData();
        
        $data = $this->setParams();

        return $data;
    }

}




class RecipeEmailTemplateData extends EmailTemplateData  {
    
    public function __construct($params) {
        $this->id = $params['id'];
        $this->comment_id = $params['comment_id'];
    }
    
    protected function getItemData() {
        $this->item = $this->getRecipeOriginalData($this->id);
  
        $this->business_profile_user = $this->item->created_by;
    }
    
        
    protected function setParams() {
        $data = new stdClass();
        
        $data->item = $this->item;
   
        $data->business_profile = $this->business_profile;
        
        $data->path = JUri::root() . 'components/com_multicalendar/views/pdf/tmpl/images/';
        
        $layout = &JRequest::getVar('layout');
        
        $data->sitelink = JUri::root() . 'index.php?option=com_multicalendar&view=pdf&layout=' . $layout . '&tpml=component&recipe_id=' . $this->id . '&comment_id=' . $this->comment_id;
        
        $data->open_link = JUri::root() . 'index.php/contact/nutrition-database#!/nutrition_database/nutrition_recipe/' . $this->id;
        
        $data->header_image  = JUri::root() . $data->business_profile->header_image;
        
        $date = JFactory::getDate($this->item->created);
        
        $data->created =  $date->toFormat('%A, %d %b %Y') . ' ' . $date->format('H:i');

        $data->user_name = JFactory::getUser($this->item->created_by)->name;
        
        $data->assessed_by = JFactory::getUser($this->item->assessed_by)->name;
        
        $data->created_by = JFactory::getUser($this->item->created_by)->name;
        
        
        //comments
        if($this->comment_id) {
            $comment = $this->getCommentData($this->comment_id, '#__fitness_nutrition_recipes_comments');
            
            $date = JFactory::getDate($comment->created);
        
            $data->comment->created =  $date->toFormat('%A, %d %b %Y') . ' ' . $date->format('H:i');

            $data->comment->created_by = JFactory::getUser($comment->created_by)->name;
            
            $data->comment->comment_text = $comment->comment;
        }
        
        return $data;
    }

    
}





class GoalEmailTemplateData extends EmailTemplateData  {
    
    public function __construct($params) {
        $this->id = $params['id'];
        $this->goal_type = $params['goal_type'];
        $this->layout = $params['layout'];
        $this->comment_id = $params['comment_id'];
    }
    
    protected function getItemData() {
        $this->item = $this->getGoalData($this->id, $this->goal_type);
        
        $this->business_profile_user = $this->item->user_id;
    }
    
   
    protected function setParams() {
        $data = new stdClass();
        
        $data->item = $this->item;
   
        $data->business_profile = $this->business_profile;
        
        $data->path = JUri::root() . 'components/com_multicalendar/views/pdf/tmpl/images/';
        
        $data->sitelink = JUri::root() . 'index.php?option=com_multicalendar&view=pdf&layout=' . $this->layout . '&goal_type=' . $this->goal_type . '&tpml=component&id=' . $this->id . '&comment_id=' . $this->comment_id;
        
        $data->open_link = JUri::root() . 'index.php/contact/goals-planning';
        
        $data->header_image  = JUri::root() . $data->business_profile->header_image;
        
        $user = &JFactory::getUser($this->item->user_id);
        $data->client_name = $user->name;

        $user = &JFactory::getUser($this->item->primary_trainer);
        $data->trainer_name =  $user->name;

        $date = JFactory::getDate($this->item->start_date);
        $data->date_created =  $date->toFormat('%A, %d %b %Y') ;

        $date = JFactory::getDate($this->item->deadline);
        $data->deadline =  $date->toFormat('%A, %d %b %Y') ;
        
        
        //comments
        if($this->comment_id) {
            
            $comment_table = '#__fitness_goal_comments';
            
            if($this->goal_type == '2') {
                $comment_table = '#__fitness_mini_goal_comments';
            }
            
            $comment = $this->getCommentData($this->comment_id, $comment_table);
            
            $date = JFactory::getDate($comment->created);
        
            $data->comment->created =  $date->toFormat('%A, %d %b %Y') . ' ' . $date->format('H:i');

            $data->comment->created_by = JFactory::getUser($comment->created_by)->name;
            
            $data->comment->comment_text = $comment->comment;
        }
        
               
        return $data;
    }
}



class AppointmentEmailTemplateData extends EmailTemplateData  {
    
    public function __construct($params) {
        $this->id = $params['id'];
        $this->client_id = $params['client_id'];
        $this->comment_id = $params['comment_id'];
        $this->layout = $params['layout'];

    }
    
    protected function getItemData() {
        $this->item = $this->getEvent($this->id);
        
        if (!$this->client_id) {
            $this->client_id = $this->item->client_id;
        }
        
        $this->business_profile_user = $this->client_id;
    }
    
   
    protected function setParams() {
        $data = new stdClass();
        
        $data->item = $this->item;
   
        $data->business_profile = $this->business_profile;
        
        $data->path = JUri::root() . 'components/com_multicalendar/views/pdf/tmpl/images/';

        $data->sitelink = JUri::root() . 'index.php?option=com_multicalendar&view=pdf&layout=' . $this->layout . '&tpml=component&event_id=' .  $this->id  . '&comment_id=' . $this->comment_id . '&client_id=' . $this->client_id;
        
        $data->open_link = JUri::root() . 'index.php?option=com_fitness&view=programs#!/item_view/' . $this->id;
        
        $data->header_image  = JUri::root() . $data->business_profile->header_image;
        
        $user = &JFactory::getUser($this->client_id);
        $data->client_name = $user->name;

        $user = &JFactory::getUser($this->item->trainer_id);
        $data->trainer_name =  $user->name;

        $date = JFactory::getDate($this->item->starttime);
        $data->start_date = $date->toFormat('%A, %d %b %Y');

        $date = JFactory::getDate($this->item->starttime);
   
        $data->start_time = $date->format('H:i');
        
        //comments
        if($this->comment_id) {
            $comment = $this->getCommentData($this->comment_id, '#__fitness_program_comments');
            
            $date = JFactory::getDate($comment->created);
        
            $data->comment->created =  $date->toFormat('%A, %d %b %Y') . ' ' . $date->format('H:i');

            $data->comment->created_by = JFactory::getUser($comment->created_by)->name;
            
            $data->comment->comment_text = $comment->comment;
        }
        
        return $data;
    }

}




class DiaryEmailTemplateData extends EmailTemplateData  {
    
    public function __construct($params) {
        $this->id = $params['id'];
        $this->layout = $params['layout'];
        $this->comment_id = $params['comment_id'];

    }
    
    protected function getItemData() {
        $this->item = $this->getDiary($this->id);
       
        $this->business_profile_user = $this->item->client_id;
    }
    
   
    protected function setParams() {
        $data = new stdClass();
        
        $data->item = $this->item;
   
        $data->business_profile = $this->business_profile;
        
        $data->path = JUri::root() . 'components/com_multicalendar/views/pdf/tmpl/images/';

        $data->sitelink = JUri::root() . 'index.php?option=com_multicalendar&view=pdf&layout=' . $this->layout . '&tpml=component&diary_id=' .  $this->id . '&comment_id=' . $this->comment_id;
        $data->open_link = JUri::root() . 'index.php/contact/nutrition-diary#!/item_view/' . $this->id . '&comment_id=' . $this->comment_id;
        
        $data->header_image  = JUri::root() . $data->business_profile->header_image;
        
        $user = &JFactory::getUser($this->item->client_id);
        $data->client_name = $user->name;

        $user = &JFactory::getUser($this->item->trainer_id);
        $data->trainer_name =  $user->name;
        
        $user = &JFactory::getUser($this->item->assessed_by);
        $data->assessed_by_name =  $user->name;
        
        $date = JFactory::getDate($this->item ->entry_date);
        $data->entry_date =  $date->toFormat('%A, %d %b %Y');

        $date = JFactory::getDate($this->item ->submit_date);
        $data->submit_date =  $date->toFormat('%A, %d %b %Y');

        $date = JFactory::getDate($this->item ->submit_date);
        $data->submit_time = $date->format('H:i');

        if($this->item->submit_date == '0000-00-00 00:00:00') {
            $data->submit_date = 'Not Submitted';
            $data->submit_time = '';
        }
        
        $data->submit_date = $data->submit_date . ' ' . $data->submit_time;
        
        //comments
        if($this->comment_id) {
            $comment = $this->getCommentData($this->comment_id, '#__fitness_nutrition_diary_comments');
            
            $date = JFactory::getDate($comment->created);
        
            $data->comment->created =  $date->toFormat('%A, %d %b %Y') . ' ' . $date->format('H:i');

            $data->comment->created_by = JFactory::getUser($comment->created_by)->name;
            
            $data->comment->comment_text = $comment->comment;
        }
        
        return $data;
    }

}





class NutritionPlanEmailTemplateData extends EmailTemplateData  {
    
    public function __construct($params) {
        $this->id = $params['id'];
        $this->layout = $params['layout'];
        $this->comment_id = $params['comment_id'];
    }
    
    protected function getItemData() {
        $this->item = $this->getPlanData($this->id);
        
        $this->business_profile_user = $this->item->client_id;
    }
    
   
    protected function setParams() {
        $data = new stdClass();
        
        $data->item = $this->item;
   
        $data->business_profile = $this->business_profile;
        
        $data->path = JUri::root() . 'components/com_multicalendar/views/pdf/tmpl/images/';

        $data->sitelink = JUri::root() .  'index.php?option=com_multicalendar&view=pdf&layout=email_notify_nutrition_plan&tpml=component&id=' . $this->id . '&comment_id=' . $this->comment_id;
        
        $data->open_link = JUri::root() . 'index.php?option=com_fitness&view=nutrition_planning';
        
        $data->header_image  = JUri::root() . $data->business_profile->header_image;
        
        $user = &JFactory::getUser($this->item->client_id );
        $data->client_name = $user->name;

        $user = &JFactory::getUser($this->item->trainer_id);
        $data->trainer_name =  $user->name;
        
        $date = JFactory::getDate($this->item ->primary_goal_start_date);
        $data->primary_goal_start_date =  $date->toFormat('%A, %d %b %Y');
        
        $date = JFactory::getDate($this->item ->primary_goal_deadline);
        $data->primary_goal_deadline =  $date->toFormat('%A, %d %b %Y');
        
        
        $date = JFactory::getDate($this->item ->mini_goal_start_date);
        $data->mini_goal_start_date =  $date->toFormat('%A, %d %b %Y');
        
        $date = JFactory::getDate($this->item ->mini_goal_deadline);
        $data->mini_goal_deadline =  $date->toFormat('%A, %d %b %Y');
        
        $table = '#__fitness_nutrition_plan_targets_comments';
        
        if($this->layout == 'email_macros_comment') {
            $table = '#__fitness_nutrition_plan_macronutrients_comments';
        }
        
        //comments
        if($this->comment_id) {
            $comment = $this->getCommentData($this->comment_id, $table);
            
            $date = JFactory::getDate($comment->created);
        
            $data->comment->created =  $date->toFormat('%A, %d %b %Y') . ' ' . $date->format('H:i');

            $data->comment->created_by = JFactory::getUser($comment->created_by)->name;
            
            $data->comment->comment_text = $comment->comment;
        }
        

        return $data;
    }

}


//PDF
class EmailPdfWorkoutTemplateData extends EmailTemplateData  {
    
    public function __construct($params) {
        $this->id = $params['id'];
        $this->client_id = $params['client_id'];
        $this->layout = $params['layout'];

    }
    
    protected function getItemData() {
        $this->item = $this->getEvent($this->id);
        $this->business_profile_user = $this->client_id;
        
        $this->item->photos = $this->getAssessmentPhotos($this->id);
        
        $this->item->client_item = $this->getACItemByEventAndClient($this->id, $this->client_id);
    }
    
   
    protected function setParams() {
        $data = new stdClass();
        
        $data->item = $this->item;
   
        $data->business_profile = $this->business_profile;
        
        $data->path = JUri::root() . 'components/com_multicalendar/views/pdf/tmpl/images/';

        $data->header_image  = JUri::root() . $data->business_profile->header_image;
        
        $user = &JFactory::getUser($this->client_id);
        $data->client_name = $user->name;

        $user = &JFactory::getUser($this->item->trainer_id);
        $data->trainer_name =  $user->name;

        $date = JFactory::getDate($this->item->starttime);
        $data->start_date = $date->toFormat('%A, %d %b %Y');

        $date = JFactory::getDate($this->item->starttime);
   
        $data->start_time = $date->format('H:i');
        
        $date = JFactory::getDate($this->item->endtime);
        $data->end_date = $date->toFormat('%A, %d %b %Y');

        $date = JFactory::getDate($this->item->endtime);
   
        $data->end_time = $date->format('H:i');
        
        $data->exercises = $this->getExercises($this->id);
        
        return $data;
    }

}





class EmailPdfNutritionPlanMacros extends EmailTemplateData  {
    
    public function __construct($params) {
        $this->id = $params['id'];
        $this->client_id = $params['client_id'];
        $this->layout = $params['layout'];

    }
    
    protected function getItemData() {
        $this->item = $this->getPlanData($this->id);
        $this->business_profile_user = $this->client_id;
    }
    
   
    protected function setParams() {
        $data = new stdClass();
        
        $data->item = $this->item;
   
        $data->business_profile = $this->business_profile;
        
        $data->path = JUri::root() . 'components/com_multicalendar/views/pdf/tmpl/images/';
        
        $data->header_image  = JUri::root() . $data->business_profile->header_image;

        $user = &JFactory::getUser($this->item->client_id );
        $data->client_name = $user->name;

        $user = &JFactory::getUser($this->item->trainer_id);
        $data->trainer_name =  $user->name;
        
        $date = JFactory::getDate($this->item ->primary_goal_start_date);
        $data->primary_goal_start_date =  $date->toFormat('%A, %d %b %Y');
        
        $date = JFactory::getDate($this->item ->primary_goal_deadline);
        $data->primary_goal_deadline =  $date->toFormat('%A, %d %b %Y');
        
        $date = JFactory::getDate($this->item ->mini_goal_start_date);
        $data->mini_goal_start_date =  $date->toFormat('%A, %d %b %Y');
        
        $date = JFactory::getDate($this->item ->mini_goal_deadline);
        $data->mini_goal_deadline =  $date->toFormat('%A, %d %b %Y');
        
        return $data;
    }

}


class EmailPdfNutritionPlanSupplements extends EmailPdfNutritionPlanMacros  {

    protected function getItemData() {
        $this->item = $this->getPlanData($this->id);
        
        $this->item->protocols = $this->getPlanProtocols($this->id);

        $this->business_profile_user = $this->item->client_id;
        
        $data->header_image  = JUri::root() . $data->business_profile->header_image;
    }

}


class EmailPdfNutritionGuide extends EmailTemplateData  {
    
    public function __construct($params) {
        $this->id = $params['id'];
        $this->client_id = $params['client_id'];
        $this->layout = $params['layout'];

    }
    
    protected function getItemData() {
        $example_days = 7;
        for($i = 1; $i <= $example_days; $i++) {
            $data[$i] = $this->getExampleDayMeals($this->id, $i);
        }
        
        $this->item = $data;
        
        $this->business_profile_user = $this->client_id;
    }
    
   
    protected function setParams() {
        $data = new stdClass();
        
        $data->item = $this->item;
   
        $data->business_profile = $this->business_profile;
        
        $data->path = JUri::root() . 'components/com_multicalendar/views/pdf/tmpl/images/';

        $data->header_image  = JUri::root() . $data->business_profile->header_image;
        
        return $data;
    }

}

class EmailPdfRecipe extends EmailTemplateData  {
    
    public function __construct($params) {
        $this->id = $params['id'];
        
    }
    
    protected function getItemData() {
        require_once  JPATH_BASE . DS . 'components' . DS . 'com_fitness' . DS .'models' . DS . 'recipe_database.php';
        
        $recipe_database_model = new FitnessModelrecipe_database();
        
        $table = '#__fitness_nutrition_recipes';
        
        $data = new stdClass();
        
        $data->id = $this->id;
        
        $data->status = 1;
        
        $recipe = $recipe_database_model->getRecipe($table, $data);
                
        $this->item = $recipe;
        
        $this->item->recipe_types_names = split(",", $this->item->recipe_types_names);
        foreach ($this->item->recipe_types_names as $recipe_types_name) {
            $recipe_types_names_html .= $recipe_types_name . "<br/>";

        }
        $this->item->recipe_types_names = $recipe_types_names_html;
        
        //
        
        $this->item->recipe_variations_names = split(",", $this->item->recipe_variations_names);
        foreach ($this->item->recipe_variations_names as $recipe_variation_name) {
            $recipe_variation_names_html .= $recipe_variation_name . "<br/>";

        }
        $this->item->recipe_variations_names = $recipe_variation_names_html;
        
        $this->business_profile_user = $this->item->created_by;
        
        require_once  JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_fitness' . DS .'models' . DS . 'nutrition_recipes.php';
        
        $nutrition_recipes_model = new FitnessModelnutrition_recipes();
        
        $this->item->status_html = $nutrition_recipes_model->status_html($this->item->id, $this->item->status, 'status_button');
    }
    
        
    protected function setParams() {
        $data = new stdClass();
        
        $data->item = $this->item;
   
        $data->business_profile = $this->business_profile;
        
        $data->path = JUri::root() . 'components/com_multicalendar/views/pdf/tmpl/images/';
        
        $layout = &JRequest::getVar('layout');
        
        $data->sitelink = JUri::root() . 'index.php?option=com_multicalendar&view=pdf&layout=' . $layout . '&tpml=component&recipe_id=' . $this->id . '&comment_id=' . $this->comment_id;
        
        $data->open_link = JUri::root() . 'index.php/contact/nutrition-database#!/nutrition_database/nutrition_recipe/' . $this->id;
        
        $data->header_image  = JUri::root() . $data->business_profile->header_image;
        
        $date = JFactory::getDate($this->item->created);
        
        $data->created =  $date->toFormat('%A, %d %b %Y') . ' ' . $date->format('H:i');

        $data->user_name = JFactory::getUser($this->item->created_by)->name;
        
        $data->assessed_by = JFactory::getUser($this->item->assessed_by)->name;
        
        $data->created_by = JFactory::getUser($this->item->created_by)->name;

        return $data;
    }
}
    
class EmailPdfShoppingList extends EmailTemplateData  {
    
    public function __construct($params) {
        $this->id = $params['id'];
        $this->client_id = $params['client_id'];
        
        $this->checked = explode(',', $params['checked']);
    }
    
    protected function getItemData() {
        $this->item = $this->getMenuPlanData($this->id);
        $this->item->categories = $this->getNutritionDatabaseCategories();
        $this->item->ingredients  = $this->getShoppingListIngredients($this->item->nutrition_plan_id, $this->id);
        $this->business_profile_user = $this->client_id;
    }
    
        
    protected function setParams() {
        $data = new stdClass();
        
        $data->item = $this->item;
        
        $data->checked = $this->checked;
   
        $data->business_profile = $this->business_profile;
        
        $data->path = JUri::root() . 'components/com_multicalendar/views/pdf/tmpl/images/';
        
        $data->header_image  = JUri::root() . $data->business_profile->header_image;
        
        $date = JFactory::getDate($this->item->start_date);
        
        $data->start_date =  $date->toFormat('%A, %d %b %Y');

        return $data;
    }
    
}


class EmailPdfExerciseLibrary extends EmailTemplateData  {
    
    public function __construct($params) {
        $this->id = $params['id'];
        $this->comment_id = $params['comment_id'];
    }
    
    protected function getItemData() {
        
        $this->item = $this->getExerciseVideo($this->id);

        $this->business_profile_user = $this->item->created_by;
        
        require_once  JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_fitness' . DS .'models' . DS . 'nutrition_recipes.php';
        
        $nutrition_recipes_model = new FitnessModelnutrition_recipes();
        
        $this->item->status_html = $nutrition_recipes_model->status_html($this->item->id, $this->item->status, 'status_button');
    }
    
        
    protected function setParams() {
        $data = new stdClass();
        
        $data->item = $this->item;
   
        $data->business_profile = $this->business_profile;
        
        $data->path = JUri::root() . 'components/com_multicalendar/views/pdf/tmpl/images/';
        
        $layout = &JRequest::getVar('layout');
        
        $data->sitelink = JUri::root() . 'index.php?option=com_multicalendar&view=pdf&layout=' . $layout . '&tpml=component&id=' . $this->id . '&comment_id=' . $this->comment_id;
        
        $data->open_link = JUri::root() . 'index.php/contact/exercise-library#!/item_view/' . $this->id;
        
        $data->header_image  = JUri::root() . $data->business_profile->header_image;
        
        $date = JFactory::getDate($this->item->created);
        
        $data->created =  $date->toFormat('%A, %d %b %Y') . ' ' . $date->format('H:i');

        $data->user_name = JFactory::getUser($this->item->created_by)->name;
        
        $data->assessed_by = JFactory::getUser($this->item->assessed_by)->name;
        
        $data->created_by = JFactory::getUser($this->item->created_by)->name;
        
        
        //comments
        if($this->comment_id) {
            $comment = $this->getCommentData($this->comment_id, '#__fitness_exercise_library_comments');
            
            $date = JFactory::getDate($comment->created);
        
            $data->comment->created =  $date->toFormat('%A, %d %b %Y') . ' ' . $date->format('H:i');

            $data->comment->created_by = JFactory::getUser($comment->created_by)->name;
            
            $data->comment->comment_text = $comment->comment;
        }

        return $data;
    }
}




class NutritionDatabaseTemplateData extends EmailTemplateData  {
    
    public function __construct($params) {
        $this->id = $params['id'];
        $this->layout = $params['layout'];

    }
    
    protected function getItemData() {
        $this->item = $this->getNutritionDatabaseItem($this->id);
    }
    
   
    protected function setParams() {
        $data = new stdClass();
        
        $data->item = $this->item;
        
        $data->path = JUri::root() . 'components/com_multicalendar/views/pdf/tmpl/images/';

        $data->sitelink = JUri::root() .  'index.php?option=com_multicalendar&view=pdf&layout=email_new_nd_item&tpml=component&id=' . $this->id;
        
        $data->open_link = JUri::root() . 'index.php?option=com_fitness&view=nutrition_planning';
        
        $data->header_image  = JUri::root() . $data->business_profile->header_image;

        return $data;
    }

}



class MenuPlanTemplateData extends EmailTemplateData  {
    
    public function __construct($params) {
        $this->id = $params['id'];
        $this->comment_id = $params['comment_id'];
    }
    
    protected function getItemData() {
        
        //comments
        if($this->comment_id) {
            $this->comment = $this->getCommentData($this->comment_id, '#__fitness_nutrition_plan_example_day_comments');
            
            $this->item = $this->getExampleDayMenu($this->comment->item_id);
        } else {
            $this->item = $this->getMenuPlanData($this->id);
        }
        

        $this->business_profile_user = $this->item->created_by;
        
        require_once  JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_fitness' . DS .'models' . DS . 'nutrition_plan.php';
        
        $model = new FitnessModelnutrition_plan();
        
        $this->item->status_html = $model->menu_plan_status_html($this->item->id, $this->item->status, 'status_button');
    }
    
        
    protected function setParams() {
        $data = new stdClass();
        
        $data->item = $this->item;
   
        $data->business_profile = $this->business_profile;
        
        $data->path = JUri::root() . 'components/com_multicalendar/views/pdf/tmpl/images/';
        
        $layout = &JRequest::getVar('layout');
        
        $data->sitelink = JUri::root() . 'index.php?option=com_multicalendar&view=pdf&layout=' . $layout . '&tpml=component&id=' . $this->id . '&comment_id=' . $this->comment_id;
        
        $data->open_link = JUri::root() . 'index.php/contact/nutrition-planning#!/menu_plan/' . $this->id;
        
        $data->header_image  = JUri::root() . $data->business_profile->header_image;
        
        $date = JFactory::getDate($this->item->start_date);
        
        $data->start_date =  $date->toFormat('%A, %d %b %Y');

        $data->user_name = JFactory::getUser($this->item->created_by)->name;
        
        $data->assessed_by = JFactory::getUser($this->item->assessed_by)->name;
        
        $data->created_by = JFactory::getUser($this->item->created_by)->name;
        
        $data->trainer_name = JFactory::getUser()->name;
        
        $client_id = $this->getClientIdByNutritionPlanId($this->item->nutrition_plan_id);
        
        $data->client_name = JFactory::getUser($client_id)->name;
        //comments
        if($this->comment_id) {
            $date = JFactory::getDate($this->comment->created);
        
            $data->comment->created =  $date->toFormat('%A, %d %b %Y') . ' ' . $date->format('H:i');

            $data->comment->created_by = JFactory::getUser($this->comment->created_by)->name;
            
            $data->comment->comment_text= $this->comment->comment;
        }
        
        return $data;
    }
}





class SupplementEmailTemplateData extends EmailTemplateData  {
    
    public function __construct($params) {
        $this->id = $params['id'];
        $this->layout = $params['layout'];
        $this->comment_id = $params['comment_id'];
    }
    
    protected function getItemData() {
        
        
        $protocol = $this->getPlanProtocol($this->id);

        
        $this->item = $this->getPlanData($protocol->nutrition_plan_id);
        
        $this->item->protocol = $protocol;
        
        $this->business_profile_user = $this->item->client_id;
    }
    
   
    protected function setParams() {
        $data = new stdClass();
        
        $data->item = $this->item;
   
        $data->business_profile = $this->business_profile;
        
        $data->path = JUri::root() . 'components/com_multicalendar/views/pdf/tmpl/images/';

        $data->sitelink = JUri::root() .  'index.php?option=com_multicalendar&view=pdf&layout=' . $this->layout . '&tpml=component&id=' . $this->id . '&comment_id=' . $this->comment_id;
        
        $data->open_link = JUri::root() . 'index.php?option=com_fitness&view=nutrition_planning#!/supplements';
        
        $data->header_image  = JUri::root() . $data->business_profile->header_image;
        
        $user = &JFactory::getUser($this->item->client_id );
        $data->client_name = $user->name;

        $user = &JFactory::getUser($this->item->trainer_id);
        $data->trainer_name =  $user->name;
        
        $date = JFactory::getDate($this->item ->primary_goal_start_date);
        $data->primary_goal_start_date =  $date->toFormat('%A, %d %b %Y');
        
        $date = JFactory::getDate($this->item ->primary_goal_deadline);
        $data->primary_goal_deadline =  $date->toFormat('%A, %d %b %Y');
        
        
        $date = JFactory::getDate($this->item ->mini_goal_start_date);
        $data->mini_goal_start_date =  $date->toFormat('%A, %d %b %Y');
        
        $date = JFactory::getDate($this->item ->mini_goal_deadline);
        $data->mini_goal_deadline =  $date->toFormat('%A, %d %b %Y');

        //comments
        if($this->comment_id) {
            $comment = $this->getCommentData($this->comment_id, '#__fitness_nutrition_plan_supplements_comments');
            
            $date = JFactory::getDate($comment->created);
        
            $data->comment->created =  $date->toFormat('%A, %d %b %Y') . ' ' . $date->format('H:i');

            $data->comment->created_by = JFactory::getUser($comment->created_by)->name;
            
            $data->comment->comment_text = $comment->comment;
        }
        

        return $data;
    }

}


class ProgramTemplateEmailTemplateData extends EmailTemplateData  {
    
    public function __construct($params) {
        $this->id = $params['id'];
        $this->layout = $params['layout'];
        $this->comment_id = $params['comment_id'];
    }
    
    protected function getItemData() {
        
        $this->item = $this->getProgramTemplate($this->id);

        $this->business_profile_user = $this->item->created_by;
    }
    
   
    protected function setParams() {
        $data = new stdClass();
        
        $data->item = $this->item;
   
        $data->business_profile = $this->business_profile;
        
        $data->path = JUri::root() . 'components/com_multicalendar/views/pdf/tmpl/images/';

        $data->sitelink = JUri::root() .  'index.php?option=com_multicalendar&view=pdf&layout=' . $this->layout . '&tpml=component&id=' . $this->id . '&comment_id=' . $this->comment_id;
        
        $data->open_link = JUri::root() . 'administrator/index.php?option=com_fitness&view=programs_templates#!/form_view/' . $this->id;
        
        $data->header_image  = JUri::root() . $data->business_profile->header_image;
        
        $date = JFactory::getDate($this->item ->created);
        $data->created =  $date->toFormat('%A, %d %b %Y');
        
        $user = &JFactory::getUser($this->item->created_by );
        $data->created_by_name = $user->name;


        //comments
        if($this->comment_id) {
            $comment = $this->getCommentData($this->comment_id, '#__fitness_pr_temp_comments');
            
            $date = JFactory::getDate($comment->created);
        
            $data->comment->created =  $date->toFormat('%A, %d %b %Y') . ' ' . $date->format('H:i');

            $data->comment->created_by = JFactory::getUser($comment->created_by)->name;
            
            $data->comment->comment_text = $comment->comment;
        }
        return $data;
    }
}


class EmailPdfPeriod extends EmailTemplateData  {
    
    public function __construct($params) {
        $this->id = $params['id'];
        $this->client_id = $params['client_id'];
        $this->layout = $params['layout'];
    }
    
    protected function getItemData() {
        $this->item = $this->getPeriod($this->id);
        
        $this->item->mini_goal = $this->getGoalData($this->item->mini_goal_id, 2);
        
        $this->item->primary_goal = $this->getGoalData($this->item->mini_goal->primary_goal_id, 1);
        
        $this->item->period_sessions = $this->getPeriodSessions($this->item->id);
        
        $this->item->primary_trainer = $this->getPrimaryTrainer($this->client_id);
        
        JRequest::setVar('secondary_only', 1);
        
        $this->item->secondary_trainers = $this->get_trainers();

        $this->business_profile_user = $this->client_id;
    }
    
   
    protected function setParams() {
        $data = new stdClass();
        
        $data->item = $this->item;
   
        $data->business_profile = $this->business_profile;
        
        $data->path = JUri::root() . 'components/com_multicalendar/views/pdf/tmpl/images/';
        
        $data->sitelink = JUri::root() . 'index.php?option=com_multicalendar&view=pdf&layout=' . $this->layout . '&goal_type=' . $this->goal_type . '&tpml=component&id=' . $this->id . '&comment_id=' . $this->comment_id;
        
        $data->header_image  = JUri::root() . $data->business_profile->header_image;
        
        $user = &JFactory::getUser($this->client_id);
        $data->client_name = $user->name;
        $data->client_email = $user->email;
        
        $date = JFactory::getDate($this->item->primary_goal->start_date);
        $data->start_date_primary =  $date->toFormat('%A, %d %b %Y') ;

        $date = JFactory::getDate($this->item->primary_goal->deadline);
        $data->deadline_primary =  $date->toFormat('%A, %d %b %Y') ;
        
        $date = JFactory::getDate($this->item->mini_goal->start_date);
        $data->start_date_mini =  $date->toFormat('%A, %d %b %Y') ;

        $date = JFactory::getDate($this->item->mini_goal->deadline);
        $data->deadline_mini =  $date->toFormat('%A, %d %b %Y') ;
   
        return $data;
    }
}