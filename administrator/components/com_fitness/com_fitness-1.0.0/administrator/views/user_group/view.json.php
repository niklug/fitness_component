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
class FitnessViewUser_group extends JView {
    
  
        function onBusinessNameChange() {
            
            $table= JRequest::getVar('table');
            $data_encoded = JRequest::getVar('data_encoded','','POST','STRING',JREQUEST_ALLOWHTML);
            $model = $this -> getModel("user_group");
	    echo json_encode($model->onBusinessNameChange($data_encoded, $table));
	}
}
