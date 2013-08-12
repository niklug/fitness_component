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
 
}
