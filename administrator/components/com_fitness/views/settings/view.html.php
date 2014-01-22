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

require_once  JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_fitness' . DS .'helpers' . DS . 'fitness.php';


/**
 * View class for a list of Fitness.
 */
class FitnessViewSettings extends JView {

    protected $items;
    protected $pagination;
    protected $state;

    /**
     * Display the view
     */
    public function display($tpl = null) {

        $this->state = $this->get('State');
        $this->items = $this->get('Items');
        $this->pagination = $this->get('Pagination');
        $document = &JFactory::getDocument();
        $document->addStyleSheet(JURI::base() . 'components' . DS . 'com_fitness' . DS . 'assets' . DS . 'css' . DS . 'fitness.css');

        // Check for errors.
        if (count($errors = $this->get('Errors'))) {
            throw new Exception(implode("\n", $errors));
        }

        $this->addToolbar();

        $input = JFactory::getApplication()->input;
        $view = $input->getCmd('view', '');
        FitnessHelper::addSubmenu('Dashboard', 'dashboard');
        FitnessHelper::addSubmenu('Appointments', 'categories');
        FitnessHelper::addSubmenu('Primary Goals', 'primarygoals');
        FitnessHelper::addSubmenu('Training Periods', 'trainingperiods');
        FitnessHelper::addSubmenu('Locations', 'locations');
        FitnessHelper::addSubmenu('Recipe Types', 'recipe_types');
        FitnessHelper::addSubmenu('Nutrition Focuses', 'nutrition_focuses');
        
        if(FitnessHelper::is_superuser()){
            FitnessHelper::addSubmenu('Nutrition Database Categories', 'database_categories');
            FitnessHelper::addSubmenu('User Groups', 'user_groups');
            FitnessHelper::addSubmenu('Business Profiles', 'business_profiles');
        }


        parent::display($tpl);
    }

    /**
     * Add the page title and toolbar.
     *
     * @since	1.6
     */
    protected function addToolbar() {
        JToolBarHelper::title(JText::_('COM_FITNESS_TITLE_SETTINGS'), 'settings.png');
    }

}
