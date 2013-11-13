<?php

defined('_JEXEC') or die('Restricted access');
// import Joomla view library
jimport('joomla.application.component.view');

require_once  JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_fitness' . DS .'helpers' . DS . 'email.php';

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
class FitnessViewEmail extends JView {
    
    function run() {
        $status['success'] = 1;
    
        $data_encoded = JRequest::getVar('data_encoded','','POST');
        
        $data = json_decode($data_encoded);
        
        try {
            $obj = FitnessEmail::factory($data->view);
            
            $data = $obj->processing($data);
            
        } catch (Exception $exc) {
            $status['success'] = 0;
            $status['message'] = $exc->getMessage();
        }
        
        $result = array( 'status' => $status, 'data' => $data);
        
        echo  json_encode($result);
        
        die();
        
        $email->send();
        
        
    }
}

