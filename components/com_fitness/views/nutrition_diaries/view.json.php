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
class FitnessViewNutrition_diaries extends JView {
    
    function diaries() {
        $model = $this -> getModel("nutrition_diaries");
        echo json_encode($model->diaries());
    }
    
    function updateDiary() {
        $table = JRequest::getVar('table');
        $data_encoded = JRequest::getVar('data_encoded','','POST');
        $model = $this -> getModel("nutrition_diaries");
        echo json_encode($model->updateDiary($table, $data_encoded));
    }
    
    function deleteDiary() {
        $table = JRequest::getVar('table');
        $data_encoded = JRequest::getVar('data_encoded','','POST');
        $model = $this -> getModel("nutrition_diaries");
        echo json_encode($model->deleteDiary($table, $data_encoded));
    }
    
    function getDiaryDays() {
        $table = JRequest::getVar('table');
        $data_encoded = JRequest::getVar('data_encoded','','POST');
        $model = $this -> getModel("nutrition_diaries");
        echo json_encode($model->getDiaryDays($table, $data_encoded));
    }
    
    
    function getActivePlanData() {
        $model = $this -> getModel("nutrition_diaries");
        echo json_encode($model->getActivePlanData());
    }
    
    function getNutritionTarget() {
        $table = JRequest::getVar('table');
        $data_encoded = JRequest::getVar('data_encoded','','POST');
        $model = $this -> getModel("nutrition_diaries");
        echo json_encode($model->getNutritionTarget($table, $data_encoded));
    }
    
    
    function updateDiaryItem() {
        $table = JRequest::getVar('table');
        $data_encoded = JRequest::getVar('data_encoded','','POST');
        $model = $this -> getModel("nutrition_diaries");
        echo json_encode($model->updateDiaryItem($table, $data_encoded));
    }
    
    
    function getDiaryItem() {
        $table = JRequest::getVar('table');
        $data_encoded = JRequest::getVar('data_encoded','','POST');
        $model = $this -> getModel("nutrition_diaries");
        echo json_encode($model->getDiaryItem($table, $data_encoded));
    }
    
    function saveAsRecipe() {
        $table = JRequest::getVar('table');
        $data_encoded = JRequest::getVar('data_encoded','','POST');
        $model = $this -> getModel("nutrition_diaries");
        echo json_encode($model->saveAsRecipe($table, $data_encoded));
    }

   
}
