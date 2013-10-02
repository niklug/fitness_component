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
class FitnessViewMinigoals extends JView
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
        FitnessHelper::addSubmenu('Client Planning', 'goals');
        
        $document = &JFactory::getDocument();
        $document -> addscript( JUri::base() . 'components' . DS . 'com_fitness' . DS .'assets'. DS .'js'. DS . 'jquery.js');
        $document -> addscript( JUri::root() . 'administrator/components' . DS . 'com_fitness' . DS .'assets'. DS .'js'. DS . 'jquerynoconflict.js');
        $document -> addscript( JUri::root() . 'administrator/components' . DS . 'com_fitness' . DS .'assets'. DS .'js'. DS . 'underscore-min.js');
        include_once JPATH_COMPONENT_ADMINISTRATOR . DS .'assets'. DS .'js'. DS . 'underscore_templates.html';
        $document -> addscript( JUri::root() . 'administrator/components' . DS . 'com_fitness' . DS .'assets'. DS .'js'. DS . 'status_class.js');
        
        // connect backend goals model
        require_once JPATH_COMPONENT_ADMINISTRATOR . DS .  'models' . DS . 'goals.php';
        $backend_goals_model  = new FitnessModelgoals();
        
        $this->assign('goals_model', $backend_goals_model);

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

		JToolBarHelper::title(JText::_('COM_FITNESS_TITLE_MINIGOALS'), 'minigoals.png');

        //Check if the form exists before showing the add/edit buttons
        $formPath = JPATH_COMPONENT_ADMINISTRATOR.'/views/minigoal';
        if (file_exists($formPath)) {

            if ($canDo->get('core.create')) {
			    JToolBarHelper::addNew('minigoal.add','JTOOLBAR_NEW');
		    }

		    if ($canDo->get('core.edit') && isset($this->items[0])) {
			    JToolBarHelper::editList('minigoal.edit','JTOOLBAR_EDIT');
		    }

        }

		if ($canDo->get('core.edit.state')) {

            if (isset($this->items[0]->state)) {
			    JToolBarHelper::divider();
			    JToolBarHelper::custom('minigoals.publish', 'publish.png', 'publish_f2.png','JTOOLBAR_PUBLISH', true);
			    JToolBarHelper::custom('minigoals.unpublish', 'unpublish.png', 'unpublish_f2.png', 'JTOOLBAR_UNPUBLISH', true);
            } else if (isset($this->items[0])) {
                //If this component does not use state then show a direct delete button as we can not trash
                JToolBarHelper::deleteList('', 'minigoals.delete','JTOOLBAR_DELETE');
            }

            if (isset($this->items[0]->state)) {
			    JToolBarHelper::divider();
			    JToolBarHelper::archiveList('minigoals.archive','JTOOLBAR_ARCHIVE');
            }
            if (isset($this->items[0]->checked_out)) {
            	JToolBarHelper::custom('minigoals.checkin', 'checkin.png', 'checkin_f2.png', 'JTOOLBAR_CHECKIN', true);
            }
		}
        
        //Show trash and delete for components that uses the state field
        if (isset($this->items[0]->state)) {
		    if ($state->get('filter.state') == -2 && $canDo->get('core.delete')) {
			    JToolBarHelper::deleteList('', 'minigoals.delete','JTOOLBAR_EMPTY_TRASH');
			    JToolBarHelper::divider();
		    } else if ($canDo->get('core.edit.state')) {
			    JToolBarHelper::trash('minigoals.trash','JTOOLBAR_TRASH');
			    JToolBarHelper::divider();
		    }
        }

		if ($canDo->get('core.admin')) {
			JToolBarHelper::preferences('com_fitness');
		}


	}
        
        function getPrimaryGoalName($id) {
            $db = JFactory::getDbo();
            $sql = "SELECT goal_category_id FROM #__fitness_goals WHERE id='$id'";
            $db->setQuery($sql);
            if(!$db->query()) {
                JError::raiseError($db->getErrorMsg());
            }
            $id = $db->loadResult();
         
            
            $sql = "SELECT name FROM #__fitness_goal_categories WHERE id='$id'";
            $db->setQuery($sql);
            if(!$db->query()) {
                JError::raiseError($db->getErrorMsg());
            }
            $result = $db->loadResult();
            return $result;
        }
        
        function getMiniGoalName($mini_goal_category_id) {
            $db = JFactory::getDbo();
            $sql = "SELECT name FROM #__fitness_mini_goal_categories WHERE id='$mini_goal_category_id' AND state='1'";
            $db->setQuery($sql);
            if(!$db->query()) {
                JError::raiseError($db->getErrorMsg());
            }
            $result = $db->loadResult();
            return $result;
        }
        

}
