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
class FitnessViewNutrition_plan extends JView {
    
    function getClientPrimaryGoals() {
        $client_id = JRequest::getVar('client_id');
        $model = $this -> getModel("nutrition_plan");
        echo $model->getClientPrimaryGoals($client_id);
    }
    
    function getGoalData() {
        $id = JRequest::getVar('id');
        $model = $this -> getModel("nutrition_plan");
        echo $model->getGoalData($id);
    }
        
 }
