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
        
    }
    
    protected function getItemData() {
        $this->item = $this->getRecipeOriginalData($this->id);
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
        
        $data->sitelink = JUri::root() . 'index.php?option=com_multicalendar&view=pdf&layout=email_recipe_approved&tpml=component&recipe_id=' . $this->id;
        
        $date = JFactory::getDate($this->item->created);
        
        $data->created =  $date->toFormat('%A, %d %b %Y') . ' ' . $date->format('H:i');;

        $data->user_name = JFactory::getUser($this->item->created_by)->name;
        
        $data->reviewed_by = JFactory::getUser($this->item->reviewed_by)->name;
        
        return $data;
    }

    
}

