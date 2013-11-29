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
    
    function getDiaries() {
        $table = JRequest::getVar('table');
        $data_encoded = JRequest::getVar('data_encoded','','POST');
        $model = $this -> getModel("nutrition_diaries");
        echo json_encode($model->getDiaries($table, $data_encoded));
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
   
}
