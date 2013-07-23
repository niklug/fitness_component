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
class FitnessViewAssessments extends JView {

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
        FitnessHelper::addSubmenu('Clients', 'clients');
        FitnessHelper::addSubmenu('Goals', 'goals');
        FitnessHelper::addSubmenu('Calendar', 'calendar');
        FitnessHelper::addSubmenu('Programs', 'programs');
        FitnessHelper::addSubmenu('Nutrition Plans', 'nutrition_plans');
        FitnessHelper::addSubmenu('Nutrition Diary', 'nutrition_diary');
        FitnessHelper::addSubmenu('Settings', 'settings');

        parent::display($tpl);
    }

    /**
     * Add the page title and toolbar.
     *
     * @since	1.6
     */
    protected function addToolbar() {
        require_once JPATH_COMPONENT . '/helpers/fitness.php';

        $state = $this->get('State');
        $canDo = FitnessHelper::getActions($state->get('filter.category_id'));

        JToolBarHelper::title(JText::_('COM_FITNESS_TITLE_ASSESSMENTS'), 'assessments.png');

        //Check if the form exists before showing the add/edit buttons
        $formPath = JPATH_COMPONENT_ADMINISTRATOR . '/views/assessment';
        if (file_exists($formPath)) {

            if ($canDo->get('core.create')) {
                JToolBarHelper::addNew('assessment.add', 'JTOOLBAR_NEW');
            }

            if ($canDo->get('core.edit') && isset($this->items[0])) {
                JToolBarHelper::editList('assessment.edit', 'JTOOLBAR_EDIT');
            }
        }

        if ($canDo->get('core.edit.state')) {

            if (isset($this->items[0]->published)) {
                JToolBarHelper::divider();
                JToolBarHelper::custom('assessments.publish', 'publish.png', 'publish_f2.png', 'JTOOLBAR_PUBLISH', true);
                JToolBarHelper::custom('assessments.unpublish', 'unpublish.png', 'unpublish_f2.png', 'JTOOLBAR_UNPUBLISH', true);
            } else if (isset($this->items[0])) {
                //If this component does not use state then show a direct delete button as we can not trash
                JToolBarHelper::deleteList('', 'assessments.delete', 'JTOOLBAR_DELETE');
            }

            if (isset($this->items[0]->state)) {
                JToolBarHelper::divider();
                JToolBarHelper::archiveList('assessments.archive', 'JTOOLBAR_ARCHIVE');
            }
            if (isset($this->items[0]->checked_out)) {
                JToolBarHelper::custom('programs.checkin', 'checkin.png', 'checkin_f2.png', 'JTOOLBAR_CHECKIN', true);
            }
        }

        //Show trash and delete for components that uses the state field
        if (isset($this->items[0]->published)) {
            if ($state->get('filter.published') == -2 && $canDo->get('core.delete')) {
                JToolBarHelper::deleteList('', 'assessments.delete', 'JTOOLBAR_EMPTY_TRASH');
                JToolBarHelper::divider();
            } else if ($canDo->get('core.edit.state')) {
                JToolBarHelper::trash('assessments.trash', 'JTOOLBAR_TRASH');
                JToolBarHelper::divider();
            }
        }

        if ($canDo->get('core.admin')) {
            JToolBarHelper::preferences('com_fitness');
        }
    }
    
    
    
    public function getGroupClients($event_id, $client_id) {
        
        $db = &JFactory::getDbo();
        $query = "SELECT client_id FROM #__fitness_appointment_clients WHERE event_id='$event_id'";
        $db->setQuery($query);
        if (!$db->query()) {
            JError::raiseError( $db->stderr());
        }
        $clients = $db->loadResultArray(0);
        if($client_id) {
            $clients = array_merge($clients, array($client_id));
        }
        $clients = array_unique($clients);
        foreach ($clients as $client) {
            $user = &JFactory::getUser($client);
            $html .= $user->name . "</br>";
        }
        return $html;
    }
    
        /**
     * 
     * @param type $goal_id
     * @param type $goal_status
     * @return string
     */   
    public function state_html($id, $status, $user_id) {
        $html = '';
        switch ($status) {
            case 1:
                $html .= '<a onclick="openSetBox(' . $id . ', ' . $status . ')" class="event_status_pending event_status__button" href="javascript:void(0)">pending</a>';
                break;
            case 2:
                $html .= '<a onclick="openSetBox(' . $id . ', ' . $status .  ')" class="event_status_attended event_status__button" href="javascript:void(0)">attended</a>';
                break;
            case 3:
                $html .= '<a onclick="openSetBox(' . $id . ', ' . $status . ')" class="event_status_cancelled event_status__button" href="javascript:void(0)">cancelled</a>';
                break;
            case 4:
                $html .= '<a onclick="openSetBox(' . $id . ', ' . $status . ')" class="event_status_latecancel event_status__button" href="javascript:void(0)">late cancel</a>';
                break;
            case 5:
                $html .= '<a onclick="openSetBox(' . $id . ', ' . $status . ')" class="event_status_noshow event_status__button" href="javascript:void(0)">no show</a>';
                break;

            default:
                $html .= '<a onclick="openSetBox(' . $id . ', ' . $status . ')" class="event_status_pending event_status__button" href="javascript:void(0)">pending</a>';
                break;
        }
        
        return $html;
    }

}
