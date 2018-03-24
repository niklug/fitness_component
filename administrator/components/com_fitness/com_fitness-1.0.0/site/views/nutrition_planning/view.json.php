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
class FitnessViewNutrition_planning extends JView {
    

    function nutrition_plan() {
        $id = JRequest::getVar('id');
        $model = $this -> getModel("goals_periods");
        echo json_encode($model->nutrition_plan($id));
    }
    
    function nutrition_targets() {
        $nutrition_plan_id = JRequest::getVar('id');
        $model = $this -> getModel("goals_periods");
        echo json_encode($model->nutrition_targets($nutrition_plan_id));
    }
}
