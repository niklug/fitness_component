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
            $user_id = JRequest::getVar('user_id');
          
            //die($goal_id . ' ' . $goal_status_id);
            $model = $this -> getModel("goals");
	    echo $model->setGoalStatus($goal_id, $goal_status_id, $user_id);
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
        
      
}
