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
class FitnessViewBusiness_profile extends JView {
    
  
        function checkUniqueGroup() {
            $table= JRequest::getVar('table');
            $data_encoded = JRequest::getVar('data_encoded','','POST','STRING',JREQUEST_ALLOWHTML);
            $model = $this -> getModel("business_profile");
	    echo json_encode($model->checkUniqueGroup($data_encoded, $table));
	}
}
