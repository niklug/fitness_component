<?php

defined('_JEXEC') or die('Restricted access');
// import Joomla view library
jimport('joomla.application.component.view');

require_once  JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_fitness' . DS .'helpers' . DS . 'fitness.php';

if(!JSession::checkToken('get')) {
    $status['success'] = 0;
    $status['message'] = JText::_('JINVALID_TOKEN');
    $result = array( 'status' => $status);
    echo  json_encode($result);
    die();
}


class FitnessViewPrograms extends JView {

    function select_filter() {
        $model = $this -> getModel("exercise_library");
        echo json_encode($model -> select_filter());
    }
    
    function programs() {
        $model = $this -> getModel("programs");
        echo json_encode($model -> programs());
    }
    
    function event_exercises() {
        $model = $this -> getModel("programs");
        echo json_encode($model -> event_exercises());
    }
    
    function business_profiles() {
        $helper = new FitnessHelper();
        echo json_encode($helper->getBusinessProfiles());
    }
    
    function copyEvent() {
        $model = $this -> getModel("programs");
        echo json_encode($model -> copyEvent());
    }
    
    function get_trainers() {
        $business_profile_id = JRequest::getVar('business_profile_id');
        $helper = new FitnessHelper();
        echo json_encode($helper->get_trainers($business_profile_id));
    }
    
    function get_trainer_clients() {
        $business_profile_id = JRequest::getVar('business_profile_id');
        $helper = new FitnessHelper();
        echo json_encode($helper->get_trainer_clients($business_profile_id));
    }
    
    function event_clients() {
        $model = $this -> getModel("programs");
        echo json_encode($model -> event_clients());
    }
    
}
