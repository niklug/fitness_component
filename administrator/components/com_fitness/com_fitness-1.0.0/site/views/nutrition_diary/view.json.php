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
class FitnessViewNutrition_diary extends JView {
    
  
    function updateStatus() {
        $data_encoded = JRequest::getVar('data_encoded');
        $table = JRequest::getVar('table');
        require_once JPATH_COMPONENT_ADMINISTRATOR . DS . 'models' . DS . 'nutrition_diary.php';
        $model = new FitnessModelnutrition_diary();
        echo $model -> updateStatus($data_encoded, $table);
    }
   
}
