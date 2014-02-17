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

require_once  JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_fitness' . DS .'models' . DS . 'exercise_library.php';

//=======================================================
// AJAX View
//======================================================
class FitnessViewExercise_library extends JView {
    
    public function __construct() {
        $this->admin_exercise_library_model = new FitnessModelExercise_library();
    }
    
    function select_filter() {
        echo json_encode($this->admin_exercise_library_model->select_filter());
    }

    function exercise_library() {
        echo json_encode($this->admin_exercise_library_model->exercise_library());
    }

    function business_profiles() {
        echo json_encode($this->admin_exercise_library_model->business_profiles());
    }

    function clients() {
        echo json_encode($this->admin_exercise_library_model->clients());
    }
    
    function favourite_exercise() {
        $model = $this -> getModel("exercise_library");
        echo json_encode($model->favourite_exercise());
    }
   
}
