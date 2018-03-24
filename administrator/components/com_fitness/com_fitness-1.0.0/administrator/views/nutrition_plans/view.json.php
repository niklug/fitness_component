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
class FitnessViewNutrition_plans extends JView {
    
    
    function nutrition_plans() {
        $model = $this -> getModel("nutrition_plans");
        echo json_encode($model->nutrition_plans());
    }

    function nutrition_plan_targets() {
        $model = $this -> getModel("nutrition_plans");
        echo json_encode($model->nutrition_plan_targets());
    }
    
}
