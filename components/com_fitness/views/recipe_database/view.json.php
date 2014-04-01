<?php

defined('_JEXEC') or die('Restricted access');
// import Joomla view library
jimport('joomla.application.component.view');

if(!JSession::checkToken('get')) {
    $status['success'] = 0;
    $status['message'] = JText::_('JINVALID_TOKEN');
    $result = array( 'status' => $status);
    echo  json_encode($result);
    die();
}

//=======================================================
// AJAX View
//======================================================
class FitnessViewRecipe_database extends JView {
    
    function getRecipes() {
        $table = JRequest::getVar('table');
        $data_encoded = JRequest::getVar('data_encoded','','POST');
        $model = $this -> getModel("recipe_database");
        echo json_encode($model->getRecipes($table, $data_encoded));
    }
    
    function recipes() {
        $model = $this -> getModel("recipe_database");
        echo json_encode($model->recipes());
    }
    
    function ingredient_categories() {
        $model = $this -> getModel("recipe_database");
        echo json_encode($model->ingredient_categories());
    }
    
    function getRecipe() {
        $table = JRequest::getVar('table');
        $data_encoded = JRequest::getVar('data_encoded','','POST');
        $model = $this -> getModel("recipe_database");
        echo json_encode($model->getRecipe($table, $data_encoded));
    }

    function getRecipeTypes() {
        $table = JRequest::getVar('table');
        $data_encoded = JRequest::getVar('data_encoded','','POST');
        $model = $this -> getModel("recipe_database");
        echo json_encode($model->getRecipeTypes($table, $data_encoded));
    }
    
    function getRecipeVariations() {
        $model = $this -> getModel("recipe_database");
        echo json_encode($model->getRecipeVariations());
    }
    
    function copyRecipe() {
        $table = JRequest::getVar('table');
        $data_encoded = JRequest::getVar('data_encoded','','POST');
        $model = $this -> getModel("recipe_database");
        echo json_encode($model->copyRecipe($table, $data_encoded));
    }
    
    function favourite_recipe() {
        $helper = new FitnessHelper();
        $table = '#__fitness_nutrition_recipes_favourites';
        echo json_encode($helper->favourite_item($table));
    }
    

    function ingredients() {
        $model = $this -> getModel("recipe_database");
        echo json_encode($model->ingredients());
    }
    

}
