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
    
    public function __construct() {

    }
    
    function primary_goals() {
        $model = $this -> getModel("goals");
        echo json_encode($model->primary_goals());
    }
    
    function mini_goals() {
        $model = $this -> getModel("goals");
        echo json_encode($model->mini_goals());
    }
    
   
}
