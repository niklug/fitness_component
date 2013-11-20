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

            default:
                break;
        }
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
        $helper = new FitnessHelper();
        if($this->item->recipe_type) {
            $recipe_types_names = $helper->getRecipeNames($this->item->recipe_type);
            foreach ($recipe_types_names as $recipe_types_name) {
                $recipe_types_names_html .= $recipe_types_name . "<br/>";

            }
        }
        $this->item->recipe_types_names = $recipe_types_names_html;
    }
    
    protected function getBusinessProfileData() {
        
        
        $business_profile_id = $this->getBubinessIdByClientId($this->item->created_by);
        
        $business_profile = $this->getBusinessProfile($business_profile_id);
        
        $business_profile = $business_profile['data'];
        
        $this->business_profile = $business_profile;
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

