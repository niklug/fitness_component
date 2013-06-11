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
class FitnessViewNotifications extends JView
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
                
                $document = &JFactory::getDocument();
                $document -> addStyleSheet(JURI::base() . 'components' . DS. 'com_fitness' . DS .'assets' . DS . 'css' . DS . 'fitness.css');

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			throw new Exception(implode("\n", $errors));
		}
        
		
        
        $input = JFactory::getApplication()->input;
        $view = $input->getCmd('view', '');

        FitnessHelper::addSubmenu('Notifications', 'notifications');
        FitnessHelper::addSubmenu('Clients', 'clients');
        FitnessHelper::addSubmenu('Goals', 'goals');
        FitnessHelper::addSubmenu('Calendar', 'calendar');
        FitnessHelper::addSubmenu('Programs', 'programs');
        FitnessHelper::addSubmenu('Nutrition Plans', 'nutrition_plans');
        FitnessHelper::addSubmenu('Nutrition Diary', 'nutrition_diary');
        FitnessHelper::addSubmenu('Assessments', 'assessments');
        FitnessHelper::addSubmenu('Settings', 'settings');
        
        $this->addToolbar();
        
		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @since	1.6
	 */
	protected function addToolbar()
	{
            require_once JPATH_COMPONENT . '/helpers/fitness.php';

            $state = $this->get('State');
            $canDo = FitnessHelper::getActions($state->get('filter.category_id'));

            JToolBarHelper::title(JText::_('Fitness Control Panel'), 'notificationss.png');

            //Check if the form exists before showing the add/edit buttons
            $formPath = JPATH_COMPONENT_ADMINISTRATOR . '/views/notifications';
            if (file_exists($formPath)) {

                if ($canDo->get('core.create')) {
                   // JToolBarHelper::addNew('notifications.add', 'JTOOLBAR_NEW');
                }

                if ($canDo->get('core.edit') && isset($this->items[0])) {
                    JToolBarHelper::editList('notifications.edit', 'JTOOLBAR_EDIT');
                }
            }

            if ($canDo->get('core.edit.state')) {

                if (isset($this->items[0]->state)) {
                    JToolBarHelper::divider();
                    JToolBarHelper::custom('notificationss.publish', 'publish.png', 'publish_f2.png', 'JTOOLBAR_PUBLISH', true);
                    JToolBarHelper::custom('notificationss.unpublish', 'unpublish.png', 'unpublish_f2.png', 'JTOOLBAR_UNPUBLISH', true);
                } else if (isset($this->items[0])) {
                    //If this component does not use state then show a direct delete button as we can not trash
                    JToolBarHelper::deleteList('', 'notificationss.delete', 'JTOOLBAR_DELETE');
                }

                if (isset($this->items[0]->state)) {
                    JToolBarHelper::divider();
                    JToolBarHelper::archiveList('notificationss.archive', 'JTOOLBAR_ARCHIVE');
                }
                if (isset($this->items[0]->checked_out)) {
                    JToolBarHelper::custom('notificationss.checkin', 'checkin.png', 'checkin_f2.png', 'JTOOLBAR_CHECKIN', true);
                }
            }

            //Show trash and delete for components that uses the state field
            if (isset($this->items[0]->state)) {
                if ($state->get('filter.state') == -2 && $canDo->get('core.delete')) {
                    JToolBarHelper::deleteList('', 'notificationss.delete', 'JTOOLBAR_EMPTY_TRASH');
                    JToolBarHelper::divider();
                } else if ($canDo->get('core.edit.state')) {
                    JToolBarHelper::trash('notificationss.trash', 'JTOOLBAR_TRASH');
                    JToolBarHelper::divider();
                }
            }

            if ($canDo->get('core.admin')) {
                JToolBarHelper::preferences('com_fitness');
            }
        }
	
}
