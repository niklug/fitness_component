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

        
}
