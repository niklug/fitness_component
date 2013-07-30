<?php
/**
 * @version     1.0.0
 * @package     com_fitness_goals
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Nikolay Korban <niklug@ukr.net> - http://
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

/**
 * View class for a list of Fitness_goals.
 */
class FitnessViewGoals extends JView
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
        $view = $input->getCmd('view', '');
        FitnessHelper::addSubmenu('Dashboard', 'dashboard');
        FitnessHelper::addSubmenu('Clients', 'clients');
        FitnessHelper::addSubmenu('Calendar', 'calendar');
        FitnessHelper::addSubmenu('Programs', 'programs');
        FitnessHelper::addSubmenu('Nutrition Plans', 'nutrition_plans');
        FitnessHelper::addSubmenu('Nutrition Diary', 'nutrition_diary');
        FitnessHelper::addSubmenu('Assessments', 'assessments');
        FitnessHelper::addSubmenu('Settings', 'settings');
        
        $document = &JFactory::getDocument();
	$document -> addscript( JUri::base() . 'components' . DS . 'com_fitness' . DS .'assets'. DS .'js'. DS . 'jquery.js');
        $document -> addscript( JUri::base() . 'components' . DS . 'com_fitness' . DS .'assets'. DS .'js'. DS . 'jquery.flot.js');
        $document -> addscript( JUri::base() . 'components' . DS . 'com_fitness' . DS .'assets'. DS .'js'. DS . 'jquery.flot.time.js');
        
        
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

		JToolBarHelper::title(JText::_('Client Planning'), 'goals.png');

        //Check if the form exists before showing the add/edit buttons
        $formPath = JPATH_COMPONENT_ADMINISTRATOR.'/views/goal';
        if (file_exists($formPath)) {

            if ($canDo->get('core.create')) {
			    JToolBarHelper::addNew('goal.add','JTOOLBAR_NEW');
		    }

		    if ($canDo->get('core.edit') && isset($this->items[0])) {
			    JToolBarHelper::editList('goal.edit','JTOOLBAR_EDIT');
		    }

        }

		if ($canDo->get('core.edit.state')) {

            if (isset($this->items[0]->state)) {
			    JToolBarHelper::divider();
			    JToolBarHelper::custom('goals.publish', 'publish.png', 'publish_f2.png','JTOOLBAR_PUBLISH', true);
			    JToolBarHelper::custom('goals.unpublish', 'unpublish.png', 'unpublish_f2.png', 'JTOOLBAR_UNPUBLISH', true);
            } else if (isset($this->items[0])) {
                //If this component does not use state then show a direct delete button as we can not trash
                JToolBarHelper::deleteList('', 'goals.delete','JTOOLBAR_DELETE');
            }

            if (isset($this->items[0]->state)) {
			    JToolBarHelper::divider();
			    JToolBarHelper::archiveList('goals.archive','JTOOLBAR_ARCHIVE');
            }
            if (isset($this->items[0]->checked_out)) {
            	JToolBarHelper::custom('goals.checkin', 'checkin.png', 'checkin_f2.png', 'JTOOLBAR_CHECKIN', true);
            }
		}
        
        //Show trash and delete for components that uses the state field
        if (isset($this->items[0]->state)) {
		    if ($state->get('filter.state') == -2 && $canDo->get('core.delete')) {
			    JToolBarHelper::deleteList('', 'goals.delete','JTOOLBAR_EMPTY_TRASH');
			    JToolBarHelper::divider();
		    } else if ($canDo->get('core.edit.state')) {
			    JToolBarHelper::trash('goals.trash','JTOOLBAR_TRASH');
			    JToolBarHelper::divider();
		    }
        }

		if ($canDo->get('core.admin')) {
			JToolBarHelper::preferences('com_fitness');
		}


	}
        
    /**
     * 
     * @param type $goal_id
     * @param type $goal_status
     * @return string
     */   
    public function goal_state_html($goal_id, $goal_status, $goal_type) {
        $html = '<div id="goal_status_button_' . $goal_id . '_' . $goal_type . '" >';
        switch ($goal_status) {
            case 1:
                $html .= '<a data-status="' . $goal_status .  '" onclick="openSetGoalBox(' . $goal_id . ', ' . $goal_status .  ', ' . $goal_type .  ')" class="goal_status_pending goal_status__button" href="javascript:void(0)">pending</a>';
                break;
            case 2:
                $html .= '<a data-status="' . $goal_status .  '"  onclick="openSetGoalBox(' . $goal_id . ', ' . $goal_status . ', ' . $goal_type .  ')" class="goal_status_complete goal_status__button" href="javascript:void(0)">complete</a>';
                break;
            case 3:
                $html .= '<a data-status="' . $goal_status .  '"  onclick="openSetGoalBox(' . $goal_id . ', ' . $goal_status . ', ' . $goal_type .  ')" class="goal_status_incomplete goal_status__button" href="javascript:void(0)">incomplete</a>';
                break;


            default:
                $html .= '<a data-status="' . $goal_status .  '"  onclick="openSetGoalBox(' . $goal_id . ', ' . $goal_status .  ', ' . $goal_type .  ')" class="goal_status_pending goal_status__button" href="javascript:void(0)">pending</a>';
                break;
        }
        $html .= '</div>';
        
        return $html;
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
        
    
    function getMiniGoalsList($primary_goal_id, $type) {
        $db = JFactory::getDbo();
        $sql = "SELECT DISTINCT id, mini_goal_category_id, deadline, completed FROM #__fitness_mini_goals WHERE primary_goal_id='$primary_goal_id' AND state='1'";
        $db->setQuery($sql);
        if(!$db->query()) {
            JError::raiseError($db->getErrorMsg());
        }
        $ids = $db->loadResultArray(0);
        $mini_goal_category_ids = $db->loadResultArray(1);
        $deadlines = $db->loadResultArray(2);
        $completed = $db->loadResultArray(3);
        
        if($type == 'status')  return $completed;
        
        if($type == 'id')  return $ids;
        
        if($type == 'deadline') {
            foreach ($deadlines as $value) {
                $html .= $value . "<br>";
            }
            return $html;
        }

        foreach ($mini_goal_category_ids as $value) {
            $html .= $this->getMiniGoalName($value) . "<br>";
        }
        
        return $html;
    }
    
    function getMinigoalsStatusHtml($primary_goal_id) {
        $statuses = $this->getMiniGoalsList($primary_goal_id, 'status');
        $ids = $this->getMiniGoalsList($primary_goal_id, 'id');
        $i = 0;
        foreach ($statuses as $status) {
            
            $html .= $this->goal_state_html($ids[$i], $status, '2'); // 2 -> Mini Goal
            
            $i++;
        }
        return $html;
    }
    
    
    
}
