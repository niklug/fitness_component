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
class FitnessViewGoals extends JView {
    
	function setGoalStatus() {
	    $goal_id = JRequest::getVar('goal_id');
            $goal_status_id = JRequest::getVar('goal_status_id');
            $goal_type = JRequest::getVar('goal_type');
          
            //die($goal_id . ' ' . $goal_status_id);
            $model = $this -> getModel("goals");
	    echo $model->setGoalStatus($goal_id, $goal_status_id, $goal_type);
	}

        
        // clients view
        function getUsersByGroup() {
	    $user_group = JRequest::getVar('user_group');
            $model = $this -> getModel("goals");
	    echo $model->getUsersByGroup($user_group);
	}
        
        function getUsersByBusiness() {
	    $data_encoded = JRequest::getVar('data_encoded','','POST','STRING',JREQUEST_ALLOWHTML);
            $model = $this -> getModel("goals");
	    echo $model->getUsersByBusiness($data_encoded);
	}
        
        function getClientsByBusiness() {
	    $data_encoded = JRequest::getVar('data_encoded','','POST','STRING',JREQUEST_ALLOWHTML);
            $model = $this -> getModel("goals");
	    echo $model->getClientsByBusiness($data_encoded);
	}
        
        
        // programs view
        function setFrontendPublished() {
            $event_id = JRequest::getVar('event_id');
	    $status = JRequest::getVar('status');
            $model = $this -> getModel("goals");
	    echo $model->setFrontendPublished($event_id, $status);
	}
        
        // Goals Graph
        function getGraphData() {
            $client_id = JRequest::getVar('client_id');
            $data_encoded = JRequest::getVar('data_encoded','','POST','STRING',JREQUEST_ALLOWHTML);
	    $status = JRequest::getVar('status');
            $model = $this -> getModel("goals");
	    echo $model->getGraphData($client_id, $data_encoded);
	}
        
        // Goal view
        function checkOverlapDate() {
            $table= JRequest::getVar('table');
            $data_encoded = JRequest::getVar('data_encoded','','POST','STRING',JREQUEST_ALLOWHTML);
            $model = $this -> getModel("goals");
	    echo $model->checkOverlapDate($data_encoded, $table);
	}
        
        function getTrainingPeriod() {
             $helper = new FitnessHelper();
             echo json_encode($helper->getTrainingPeriod());
        }
        
        function populateGoals() {
            require_once  JPATH_SITE . DS . 'components' . DS . 'com_fitness' . DS .'models' . DS . 'goals_periods.php';
            $data_encoded = JRequest::getVar('data_encoded','','POST','STRING',JREQUEST_ALLOWHTML);
            $model = new FitnessModelgoals_periods();
            echo $model->populateGoals($data_encoded);
        }
        
        function training_periods() {
            $model = $this -> getModel("goals");
            echo json_encode($model->training_periods());
        }
        
        function training_sessions() {
            $model = $this -> getModel("goals");
            echo json_encode($model->training_sessions());
        }
        
        function scheduleSession() {
            $model = $this -> getModel("goals");
            echo json_encode($model->scheduleSession());
        }
        
        function copySessionPeriod() {
            $model = $this -> getModel("goals");
            echo json_encode($model->copySessionPeriod());
        }
        
        function addPlan() {
            $model = $this -> getModel("goals");
            echo json_encode($model->addPlan());
        }
}
