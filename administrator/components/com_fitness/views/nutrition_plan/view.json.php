<?php

defined('_JEXEC') or die('Restricted access');
// import Joomla view library
jimport('joomla.application.component.view');

JSession::checkToken('get') or jexit(JText::_('JINVALID_TOKEN'));

//=======================================================
// AJAX View
//======================================================
class FitnessViewNutrition_plan extends JView {
    

        
 }
