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

require_once  JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_fitness' . DS .'models' . DS . 'programs.php';

//=======================================================
// AJAX View
//======================================================
class FitnessViewPrograms extends JView {
    
    public function __construct() {
        $this->admin_programs_model = new FitnessModelprograms();
    }
    
    function programs() {
        echo json_encode($this->admin_programs_model->programs());
    }
    

    function event_exercises() {
        echo json_encode($this->admin_programs_model->event_exercises());
    }

    function copyEvent() {
        echo json_encode($this->admin_programs_model->copyEvent());
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
         echo json_encode($this->admin_programs_model->event_clients());
    }
   
}
