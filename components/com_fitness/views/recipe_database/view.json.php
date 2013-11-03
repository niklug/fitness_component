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
    
    function copyRecipe() {
        $table = JRequest::getVar('table');
        $data_encoded = JRequest::getVar('data_encoded','','POST');
        $model = $this -> getModel("recipe_database");
        echo json_encode($model->copyRecipe($table, $data_encoded));
    }
    
    function addFavourite() {
        $table = JRequest::getVar('table');
        $data_encoded = JRequest::getVar('data_encoded','','POST');
        $model = $this -> getModel("recipe_database");
        echo json_encode($model->addFavourite($table, $data_encoded));
    }
    
    function removeFavourite() {
        $table = JRequest::getVar('table');
        $data_encoded = JRequest::getVar('data_encoded','','POST');
        $model = $this -> getModel("recipe_database");
        echo json_encode($model->removeFavourite($table, $data_encoded));
    }
    
}