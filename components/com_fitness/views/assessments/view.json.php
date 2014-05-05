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

require_once  JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_fitness' . DS .'models' . DS . 'programs.php';


//=======================================================
// AJAX View
//======================================================
class FitnessViewAssessments extends JView {
    
    public function __construct() {
        $this->admin_programs_model = new FitnessModelprograms();
    }
    

    
   
}
