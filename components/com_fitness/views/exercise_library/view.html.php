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

jimport('joomla.application.component.view');

/**
 * View class for a list of Fitness.
 */
class FitnessViewExercise_library extends JView {

    protected $items;
    protected $pagination;
    protected $state;
    protected $params;

    /**
     * Display the view
     */
    public function display($tpl = null) {
        $app = JFactory::getApplication();

        $this->state = $this->get('State');
        $this->items = $this->get('Items');
        $this->pagination = $this->get('Pagination');
        $this->params = $app->getParams('com_fitness');

        // Check for errors.
        if (count($errors = $this->get('Errors'))) {

            throw new Exception(implode("\n", $errors));
        }

        $document = &JFactory::getDocument();

        $document->addscript(JUri::root() . 'administrator/components/com_fitness/assets/js/lib/require.js');

        $document->addscript(JUri::root() . 'administrator/components' . DS . 'com_fitness' . DS . 'assets' . DS . 'js' . DS . 'lib' . DS . 'underscore-min.js');
        include_once JPATH_COMPONENT_ADMINISTRATOR . DS . 'assets' . DS . 'js' . DS . 'underscore_templates.html';

        $document->addStyleSheet(JUri::root() . 'components/com_fitness/assets/css/fitness.css');

        $document->addStyleSheet(JUri::root() . 'administrator/components/com_fitness/assets/css/jquery-ui.css');

        parent::display($tpl);
    }

}
