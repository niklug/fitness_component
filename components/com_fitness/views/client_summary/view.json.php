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
class FitnessViewClient_summary extends JView {
    
    public function __construct() {
 
    }
    
    function notifications() {
        $model = $this -> getModel("client_summary");
        echo json_encode($model->notifications());
    }
    
   
}
