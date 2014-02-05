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
class FitnessViewExercise_library extends JView
{
	protected $items;
	protected $pagination;
	protected $state;

	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{
		$this->state		= $this->get('State');
		$this->items		= $this->get('Items');
		$this->pagination	= $this->get('Pagination');

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			throw new Exception(implode("\n", $errors));
		}
        
		$this->addToolbar();
        
        $input = JFactory::getApplication()->input;
        FitnessHelper::addSubmenu('Dashboard', 'dashboard');
        FitnessHelper::addSubmenu('Clients', 'clients');
        FitnessHelper::addSubmenu('Client Planning', 'goals');
        FitnessHelper::addSubmenu('Assessments', 'assessments');
        FitnessHelper::addSubmenu('Calendar', 'calendar');
        FitnessHelper::addSubmenu('Programs', 'programs');
        
        FitnessHelper::addSubmenu('Nutrition Plans', 'nutrition_plans');
        FitnessHelper::addSubmenu('Nutrition Diary', 'nutrition_diaries');
        FitnessHelper::addSubmenu('Recipe Database', 'nutrition_recipes');
        FitnessHelper::addSubmenu('Settings', 'settings');
        
        $document = &JFactory::getDocument();
                
        $document -> addscript( JUri::root() . 'administrator/components/com_fitness/assets/js/lib/require.js');

        $document -> addscript( JUri::root() . 'administrator/components' . DS . 'com_fitness' . DS .'assets'. DS .'js'. DS . 'lib' . DS . 'underscore-min.js');
        
        include_once JPATH_COMPONENT_ADMINISTRATOR . DS .'assets'. DS .'js'. DS . 'underscore_templates.html';

        
		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @since	1.6
	 */
	protected function addToolbar()
	{
		require_once JPATH_COMPONENT.'/helpers/fitness.php';

		$state	= $this->get('State');
		$canDo	= FitnessHelper::getActions($state->get('filter.category_id'));

		JToolBarHelper::title(JText::_('COM_FITNESS_TITLE_EXERCISE_LIBRARY'), '');

        
	}
}
