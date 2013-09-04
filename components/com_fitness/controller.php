<?php
/**
 * @version     1.0.0
 * @package     com_fitness
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Nikolay Korban <niklug@ukr.net> - http://
 */
 
// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controller');

class FitnessController extends JController
{
     public function display($tpl = null) {
         $user		= JFactory::getUser();

         if($user->guest) {
             $this->setRedirect(JRoute::_(JURI::base() . 'index.php', false));
             $this->setMessage('Login please to proceed');
             return false;
      
         }
         parent::display();
     }
}