<?php

defined('_JEXEC') or die('Restricted access');
// import Joomla view library
jimport('joomla.application.component.view');

//=======================================================
// AJAX View
//======================================================
class FitnessViewGoals extends JView {
    
	function setGoalStatus() {
            JSession::checkToken('get') or jexit(JText::_('JINVALID_TOKEN'));
	    $goal_id = JRequest::getVar('goal_id');
            $goal_status_id = JRequest::getVar('goal_status_id');
            $goal_type = JRequest::getVar('goal_type');
          
            //die($goal_id . ' ' . $goal_status_id);
            $model = $this -> getModel("goals");
	    echo $model->setGoalStatus($goal_id, $goal_status_id, $goal_type);
	}
        
        function sendGoalEmail() {
            JSession::checkToken('get') or jexit(JText::_('JINVALID_TOKEN'));
	    $goal_id = JRequest::getVar('goal_id');
            $goal_status_id = JRequest::getVar('goal_status_id');
            $user_id = JRequest::getVar('user_id');
            //die($goal_id . ' ' . $goal_status_id);
            $model = $this -> getModel("goals");
	    echo $model->sendGoalEmail($goal_id, $goal_status_id, $user_id);
	}
        
        // clients view
        function getUsersByGroup() {
            JSession::checkToken('get') or jexit(JText::_('JINVALID_TOKEN'));
	    $user_group = JRequest::getVar('user_group');
            $model = $this -> getModel("goals");
	    echo $model->getUsersByGroup($user_group);
	}
        
        function getUsersByBusiness() {
            JSession::checkToken('get') or jexit(JText::_('JINVALID_TOKEN'));
	    $business_id = JRequest::getVar('business_id');
            $model = $this -> getModel("goals");
	    echo $model->getUsersByBusiness($business_id);
	}
        
        
        // programs view
        function setFrontendPublished() {
            JSession::checkToken('get') or jexit(JText::_('JINVALID_TOKEN'));
            $event_id = JRequest::getVar('event_id');
	    $status = JRequest::getVar('status');
            $model = $this -> getModel("goals");
	    echo $model->setFrontendPublished($event_id, $status);
	}
        
        // Goals Graph
        function getGraphData() {
            JSession::checkToken('get') or jexit(JText::_('JINVALID_TOKEN'));
            $client_id = JRequest::getVar('client_id');
            $data_encoded = JRequest::getVar('data_encoded','','POST','STRING',JREQUEST_ALLOWHTML);
	    $status = JRequest::getVar('status');
            $model = $this -> getModel("goals");
	    echo $model->getGraphData($client_id, $data_encoded);
	}
        
        // Goal view
        function checkOverlapDate() {
            JSession::checkToken('get') or jexit(JText::_('JINVALID_TOKEN'));
            $table= JRequest::getVar('table');
            $data_encoded = JRequest::getVar('data_encoded','','POST','STRING',JREQUEST_ALLOWHTML);
            $model = $this -> getModel("goals");
	    echo $model->checkOverlapDate($data_encoded, $table);
	}
        
        function commentEmail() {
            JSession::checkToken('get') or jexit(JText::_('JINVALID_TOKEN'));
            $table= JRequest::getVar('table');
            $data_encoded = JRequest::getVar('data_encoded','','POST','STRING',JREQUEST_ALLOWHTML);
            $model = $this -> getModel("goals");
	    echo json_encode($model->commentEmail($data_encoded, $table));
	}
      
}
