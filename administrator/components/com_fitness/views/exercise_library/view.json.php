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


class FitnessViewExercise_library extends JView {

    function select_filter() {
        $model = $this -> getModel("exercise_library");
        echo json_encode($model -> select_filter());
    }
    
    function exercise_library() {
        $model = $this -> getModel("exercise_library");
        echo json_encode($model -> exercise_library());
    }
}
