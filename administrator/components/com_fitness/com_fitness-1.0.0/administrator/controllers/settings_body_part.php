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

jimport('joomla.application.component.controllerform');

/**
 * Settings_body_part controller class.
 */
class FitnessControllerSettings_body_part extends JControllerForm
{

    function __construct() {
        $this->view_list = 'settings_body_parts';
        parent::__construct();
    }

}