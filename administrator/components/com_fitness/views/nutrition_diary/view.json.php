<?php

defined('_JEXEC') or die('Restricted access');
// import Joomla view library
jimport('joomla.application.component.view');

if(!JSession::checkToken('get')) {
    $status['success'] = 0;
    $status['message'] = JText::_('JINVALID_TOKEN');
    $result = array( 'status' => $status);
    //echo  json_encode($result);
    //die();
}

//=======================================================
// AJAX View
//======================================================
class FitnessViewNutrition_diary extends JView {
    

    
    function updateDiaryStatus() {
        $data_encoded = JRequest::getVar('data_encoded');
        $table = JRequest::getVar('table');
        $model = $this -> getModel("nutrition_diary");
        echo $model -> updateDiaryStatus($data_encoded, $table);
    }
    

    
}
