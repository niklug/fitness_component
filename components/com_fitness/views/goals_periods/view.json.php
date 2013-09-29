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
class FitnessViewGoals_periods extends JView {
    
    function addGoal() {
        $table = JRequest::getVar('table');
        $data_encoded = JRequest::getVar('data_encoded','','POST','STRING',JREQUEST_ALLOWHTML);
        $model = $this -> getModel("goals_periods");
        echo $model->addGoal($table, $data_encoded);
    }
    
    function populateGoals() {
        $data_encoded = JRequest::getVar('data_encoded','','POST','STRING',JREQUEST_ALLOWHTML);
        $model = $this -> getModel("goals_periods");
        echo $model->populateGoals($data_encoded);
    }
    
    function checkOverlapDate() {
        $table = JRequest::getVar('table');
        $data_encoded = JRequest::getVar('data_encoded','','POST','STRING',JREQUEST_ALLOWHTML);
        $model = $this -> getModel("goals_periods");
        echo $model->checkOverlapDate($data_encoded, $table);
    }
    
    function commentEmail() {
        $table= JRequest::getVar('table');
        $data_encoded = JRequest::getVar('data_encoded','','POST','STRING',JREQUEST_ALLOWHTML);
        $model = $this -> getModel("goals_periods");
        echo json_encode($model->commentEmail($data_encoded, $table));
    }
}
