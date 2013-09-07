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

class FitnessController extends JController
{
	/**
	 * Method to display a view.
	 *
	 * @param	boolean			$cachable	If true, the view output will be cached
	 * @param	array			$urlparams	An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
	 *
	 * @return	JController		This object to support chaining.
	 * @since	1.5
	 */
	public function display($cachable = false, $urlparams = false)
	{
		require_once JPATH_COMPONENT.'/helpers/fitness.php';
 		$view		= JFactory::getApplication()->input->getCmd('view', 'dashboard');
                JFactory::getApplication()->input->set('view', $view);

		parent::display($cachable, $urlparams);

		return $this;
	}
        
        //------------------------------------------------------
	function setGoalStatus() {
            
 		$view = $this -> getView('goals', 'json');
                $view->setModel($this->getModel('goals'));
   		$view -> setGoalStatus();
	}
        
        //------------------------------------------------------
	function sendGoalEmail() {
            
 		$view = $this -> getView('goals', 'json');
                $view->setModel($this->getModel('goals'));
   		$view -> sendGoalEmail();
	}
        
        
        //clients view
        //------------------------------------------------------
	function getClientsByGroup() {
            
 		$view = $this -> getView('goals', 'json');
                $view->setModel($this->getModel('goals'));
   		$view -> getClientsByGroup();
	}
        
        // programs view
        //------------------------------------------------------
	function setFrontendPublished() {
            
 		$view = $this -> getView('goals', 'json');
                $view->setModel($this->getModel('goals'));
   		$view -> setFrontendPublished();
	}
        
        // Goals Graph
        //------------------------------------------------------
	function getGraphData() {
            
 		$view = $this -> getView('goals', 'json');
                $view->setModel($this->getModel('goals'));
   		$view -> getGraphData();
	}
        
        
        // Recipes
        //------------------------------------------------------
	function getSearchIngredients() {
            $view = $this -> getView('nutrition_recipe', 'json');
            $view->setModel($this->getModel('nutrition_recipe'));
            $view -> getSearchIngredients();
	}
        
        function getIngredientData() {
            $view = $this -> getView('nutrition_recipe', 'json');
            $view->setModel($this->getModel('nutrition_recipe'));
            $view -> getIngredientData();
	}
        
        function saveMeal() {
            $view = $this -> getView('nutrition_recipe', 'json');
            $view->setModel($this->getModel('nutrition_recipe'));
            $view -> saveMeal();
	}
        
        function deleteMeal() {
            $view = $this -> getView('nutrition_recipe', 'json');
            $view->setModel($this->getModel('nutrition_recipe'));
            $view -> deleteMeal();
	}
        
        function populateTable() {
            $view = $this -> getView('nutrition_recipe', 'json');
            $view->setModel($this->getModel('nutrition_recipe'));
            $view -> populateTable();
	}
        
        function saveComment() {
            $view = $this -> getView('nutrition_recipe', 'json');
            $view->setModel($this->getModel('nutrition_recipe'));
            $view -> saveComment();
	}
        
        function deleteComment() {
            $view = $this -> getView('nutrition_recipe', 'json');
            $view->setModel($this->getModel('nutrition_recipe'));
            $view -> deleteComment();
	}
        
        function populateComments() {
            $view = $this -> getView('nutrition_recipe', 'json');
            $view->setModel($this->getModel('nutrition_recipe'));
            $view -> populateComments();
	}
        
        // nutrition plan
        function getClientPrimaryGoals() {
            $view = $this -> getView('nutrition_plan', 'json');
            $view->setModel($this->getModel('nutrition_plan'));
            $view -> getClientPrimaryGoals();
	}
        
        function getGoalData() {
            $view = $this -> getView('nutrition_plan', 'json');
            $view->setModel($this->getModel('nutrition_plan'));
            $view -> getGoalData();
	}
        
        function resetAllForceActive() {
            $view = $this -> getView('nutrition_plan', 'json');
            $view->setModel($this->getModel('nutrition_plan'));
            $view -> resetAllForceActive();
	}
        
        function saveTargetsData() {
            $view = $this -> getView('nutrition_plan', 'json');
            $view->setModel($this->getModel('nutrition_plan'));
            $view -> saveTargetsData();
	}
        
        function getTargetsData() {
            $view = $this -> getView('nutrition_plan', 'json');
            $view->setModel($this->getModel('nutrition_plan'));
            $view -> getTargetsData();
	}
        
        function saveIngredient() {
            $view = $this -> getView('nutrition_plan', 'json');
            $view->setModel($this->getModel('nutrition_plan'));
            $view -> saveIngredient();
	}
        
        function deleteIngredient() {
            $view = $this -> getView('nutrition_plan', 'json');
            $view->setModel($this->getModel('nutrition_plan'));
            $view -> deleteIngredient();
	}
        
        function populateItemDescription() {
            $view = $this -> getView('nutrition_plan', 'json');
            $view->setModel($this->getModel('nutrition_plan'));
            $view -> populateItemDescription();
	}
        
        function savePlanMeal() {
            $view = $this -> getView('nutrition_plan', 'json');
            $view->setModel($this->getModel('nutrition_plan'));
            $view -> savePlanMeal();
	}
        
        function deletePlanMeal() {
            $view = $this -> getView('nutrition_plan', 'json');
            $view->setModel($this->getModel('nutrition_plan'));
            $view -> deletePlanMeal();
	}
        
        function populatePlanMeal() {
            $view = $this -> getView('nutrition_plan', 'json');
            $view->setModel($this->getModel('nutrition_plan'));
            $view -> populatePlanMeal();
	}
        
        function savePlanComment() {
            $view = $this -> getView('nutrition_plan', 'json');
            $view->setModel($this->getModel('nutrition_plan'));
            $view -> savePlanComment();
	}
        
        function deletePlanComment() {
            $view = $this -> getView('nutrition_plan', 'json');
            $view->setModel($this->getModel('nutrition_plan'));
            $view -> deletePlanComment();
	}
        
        function populatePlanComments() {
            $view = $this -> getView('nutrition_plan', 'json');
            $view->setModel($this->getModel('nutrition_plan'));
            $view -> populatePlanComments();
	}
        
        function importRecipe() {
            $view = $this -> getView('nutrition_plan', 'json');
            $view->setModel($this->getModel('nutrition_plan'));
            $view -> importRecipe();
	}
        
        function saveShoppingItem() {
            $view = $this -> getView('nutrition_plan', 'json');
            $view->setModel($this->getModel('nutrition_plan'));
            $view -> saveShoppingItem();
	}
        
        function deleteShoppingItem() {
            $view = $this -> getView('nutrition_plan', 'json');
            $view->setModel($this->getModel('nutrition_plan'));
            $view -> deleteShoppingItem();
	}
        
        function getShoppingItemData() {
            $view = $this -> getView('nutrition_plan', 'json');
            $view->setModel($this->getModel('nutrition_plan'));
            $view -> getShoppingItemData();
	}
        
        // nutrition diary
        
        function updateDiaryStatus() {
            $view = $this -> getView('nutrition_diary', 'json');
            $view->setModel($this->getModel('nutrition_diary'));
            $view -> updateDiaryStatus();
	}

  
        
}
