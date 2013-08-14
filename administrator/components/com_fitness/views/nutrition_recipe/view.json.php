<?php

defined('_JEXEC') or die('Restricted access');
// import Joomla view library
jimport('joomla.application.component.view');

JSession::checkToken('get') or jexit(JText::_('JINVALID_TOKEN'));

//=======================================================
// AJAX View
//======================================================
class FitnessViewNutrition_recipe extends JView {
    
	function getSearchIngredients() {
	    $search_text = JRequest::getVar('search_text');
            $model = $this -> getModel("nutrition_recipe");
	    echo $model->getSearchIngredients($search_text);
	}
        
        
        function getIngredientData() {
	    $id = JRequest::getVar('id');
            $model = $this -> getModel("nutrition_recipe");
	    echo $model->getIngredientData($id);
	}
        
        function saveMeal() {
	    $ingredient_encoded = JRequest::getVar('ingredient_encoded');
            $model = $this -> getModel("nutrition_recipe");
	    echo $model->saveMeal($ingredient_encoded);
	}
        
        
         function deleteMeal() {
	    $id= JRequest::getVar('id');
            $model = $this -> getModel("nutrition_recipe");
	    echo $model->deleteMeal($id);
	}
        
                
         function populateTable() {
	    $recipe_id = JRequest::getVar('recipe_id');
            $model = $this -> getModel("nutrition_recipe");
	    echo $model->populateTable($recipe_id);
	}
        

}
