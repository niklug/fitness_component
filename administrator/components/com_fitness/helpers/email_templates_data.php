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

            default:
                break;
        }
    }
    
    protected function getBusinessProfileData() {

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
        if($this->item->recipe_type) {
            $recipe_types_names = $this->getRecipeNames($this->item->recipe_type);
            foreach ($recipe_types_names as $recipe_types_name) {
                $recipe_types_names_html .= $recipe_types_name . "<br/>";

            }
        }
        $this->item->recipe_types_names = $recipe_types_names_html;
        
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
        
        $data->reviewed_by = JFactory::getUser($this->item->reviewed_by)->name;
        
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


