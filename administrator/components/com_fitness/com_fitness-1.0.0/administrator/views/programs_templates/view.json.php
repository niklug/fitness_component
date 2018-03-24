<?php

defined('_JEXEC') or die('Restricted access');
// import Joomla view library
jimport('joomla.application.component.view');

require_once  JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_fitness' . DS .'helpers' . DS . 'fitness.php';

if(!JSession::checkToken('get')) {
    $status['success'] = 0;
    $status['message'] = JText::_('JINVALID_TOKEN');
    $result = array( 'status' => $status);
    echo  json_encode($result);
    die();
}


class FitnessViewPrograms_templates extends JView {
    
    function programs_templates() {
        $model = $this -> getModel("programs_templates");
        echo json_encode($model -> programs_templates());
    }
    
    function copyProgramTemplate() {
        $model = $this -> getModel("programs_templates");
        echo json_encode($model -> copyProgramTemplate());
    }
    
    function pr_temp_clients() {
        $model = $this -> getModel("programs_templates");
        echo json_encode($model -> pr_temp_clients());
    }
    
    function pr_temp_exercises() {
        $model = $this -> getModel("programs_templates");
        echo json_encode($model -> pr_temp_exercises());
    }
    
    function import_pr_temp() {
        $model = $this -> getModel("programs_templates");
        echo json_encode($model -> import_pr_temp());
    }
    
    
}
