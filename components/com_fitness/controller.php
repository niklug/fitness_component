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

jimport('joomla.application.component.controller');

class FitnessController extends JController {

    public function __construct() {
        parent::__construct();

        //connect administrator models
        require_once JPATH_COMPONENT_ADMINISTRATOR . DS . 'models' . DS . 'nutrition_plan.php';
        $this->admin_nutrition_plan_model = new FitnessModelnutrition_plan();
        
        require_once JPATH_COMPONENT_ADMINISTRATOR . DS . 'models' . DS . 'nutrition_recipe.php';
        $this->admin_nutrition_recipe_model = new FitnessModelnutrition_recipe();
    }

    public function display($tpl = null) {
        $user = JFactory::getUser();

        if ($user->guest) {
            $this->setRedirect(JRoute::_(JURI::base() . 'index.php', false));
            $this->setMessage('Login please to proceed');
            return false;
        }
        parent::display();
    }
    
    //nutrition_recipe
    function getSearchIngredients() {
        $search_text = JRequest::getVar('search_text');
        
        echo $this->admin_nutrition_recipe_model->getSearchIngredients($search_text);
    }


    function getIngredientData() {
        $id = JRequest::getVar('id');
        
        echo $this->admin_nutrition_recipe_model->getIngredientData($id);
    }

    function saveMeal() {
        $ingredient_encoded = JRequest::getVar('ingredient_encoded');
        
        echo $this->admin_nutrition_recipe_model->saveMeal($ingredient_encoded);
    }


     function deleteMeal() {
        $id= JRequest::getVar('id');
        
        echo $this->admin_nutrition_recipe_model->deleteMeal($id);
    }


     function populateTable() {
        $recipe_id = JRequest::getVar('recipe_id');
        
        echo $this->admin_nutrition_recipe_model->populateTable($recipe_id);
    }
    // end nutrition_recipe

    // nutrition plan

    function getTargetsData() {
        $data_encoded = JRequest::getVar('data_encoded');
        echo $this->admin_nutrition_plan_model->getTargetsData($data_encoded);
    }

    function saveIngredient() {
        $table = JRequest::getVar('table');
        $ingredient_encoded = JRequest::getVar('ingredient_encoded');
        
        echo $this->admin_nutrition_plan_model->saveIngredient($ingredient_encoded, $table);
    }

    function deleteIngredient() {
        $table = JRequest::getVar('table');
        $id = JRequest::getVar('id');
        
        echo $this->admin_nutrition_plan_model->deleteIngredient($id, $table);
    }

    function populateItemDescription() {
        $table = JRequest::getVar('table');
        $nutrition_plan_id = JRequest::getVar('nutrition_plan_id');
        $meal_id = JRequest::getVar('meal_id');
        $type = JRequest::getVar('type');
        
        echo $this->admin_nutrition_plan_model->populateItemDescription($nutrition_plan_id, $meal_id, $type, $table);
    }

    function savePlanMeal() {
        $table = JRequest::getVar('table');
        $meal_encoded = JRequest::getVar('meal_encoded');
        
        echo $this->admin_nutrition_plan_model->savePlanMeal($meal_encoded, $table);
    }

    function deletePlanMeal() {
        $table = JRequest::getVar('table');
        $id = JRequest::getVar('id');
        
        echo $this->admin_nutrition_plan_model->deletePlanMeal($id, $table);
    }

    function populatePlanMeal() {
        $table = JRequest::getVar('table');
        $nutrition_plan_id = JRequest::getVar('nutrition_plan_id');
        
        echo $this->admin_nutrition_plan_model->populatePlanMeal($nutrition_plan_id, $table);
    }

    function savePlanComment() {
        $table = JRequest::getVar('table');
        $data_encoded = JRequest::getVar('data_encoded','','POST','STRING',JREQUEST_ALLOWHTML);
        echo $this->admin_nutrition_plan_model->savePlanComment($data_encoded, $table);
    }

    function deletePlanComment() {
        $table = JRequest::getVar('table');
        $id = JRequest::getVar('id');
        echo $this->admin_nutrition_plan_model->deletePlanComment($id, $table);
    }

    function populatePlanComments() {
        $table = JRequest::getVar('table');
        $item_id = JRequest::getVar('item_id');
        $sub_item_id = JRequest::getVar('sub_item_id');
        echo $this->admin_nutrition_plan_model->populatePlanComments($item_id, $sub_item_id, $table);
    }

    function importRecipe() {
        $table = JRequest::getVar('table');
        $data_encoded = JRequest::getVar('data_encoded');
        
        echo $this->admin_nutrition_plan_model->importRecipe($data_encoded, $table);
    }

    function saveShoppingItem() {
        $data_encoded = JRequest::getVar('data_encoded');
        
        echo $this->admin_nutrition_plan_model->saveShoppingItem($data_encoded);
    }

    function deleteShoppingItem() {
        $id = JRequest::getVar('id');
        
        echo $this->admin_nutrition_plan_model->deleteShoppingItem($id);
    }

    function getShoppingItemData() {
        $nutrition_plan_id = JRequest::getVar('nutrition_plan_id');
        
        echo $this->admin_nutrition_plan_model->getShoppingItemData($nutrition_plan_id);
    }
    // end nutrition plan
    
    
    // goals
    function addGoal() {
        $view = $this -> getView('goals_periods', 'json');
        $view->setModel($this->getModel('goals_periods'));
        $view -> addGoal();
    }
    
    function populateGoals() {
        $view = $this -> getView('goals_periods', 'json');
        $view->setModel($this->getModel('goals_periods'));
        $view -> populateGoals(); 
    }
}