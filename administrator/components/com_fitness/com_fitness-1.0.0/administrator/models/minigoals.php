<?php

/**
 * @version     1.0.0
 * @package     com_fitness
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Nikolay Korban <niklug@ukr.net> - http://
 */
defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');

/**
 * Methods supporting a list of Fitness records.
 */
class FitnessModelminigoals extends JModelList {

    /**
     * Constructor.
     *
     * @param    array    An optional associative array of configuration settings.
     * @see        JController
     * @since    1.6
     */
    public function __construct($config = array()) {
        if (empty($config['filter_fields'])) {
            $config['filter_fields'] = array(
                                'id', 'a.id',
                'primary_goal_id', 'a.primary_goal_id',
                'mini_goal_category_id', 'a.mini_goal_category_id',
                'deadline', 'a.deadline',
                'start_date', 'a.start_date',
                'details', 'a.details',
                'comments', 'a.comments',
                'completed', 'a.completed',
                'state', 'a.state', 'gf.name'

            );
        }

        parent::__construct($config);
    }

    /**
     * Method to auto-populate the model state.
     *
     * Note. Calling getState in this method will result in recursion.
     */
    protected function populateState($ordering = null, $direction = null) {
        // Initialise variables.
        $app = JFactory::getApplication('administrator');

        // Load the filter state.
        $search = $app->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
        $this->setState('filter.search', $search);

        $published = $app->getUserStateFromRequest($this->context . '.filter.state', 'filter_published', '', 'string');
        $this->setState('filter.state', $published);
        
        // Filter by training period
        $training_period = $app->getUserStateFromRequest($this->context . '.filter.training_period', 'filter_training_period', '', 'string');
        $this->setState('filter.training_period', $training_period);
        
        //Filtering start date
        $this->setState('filter.start_date.from', $app->getUserStateFromRequest($this->context.'.filter.start_date.from', 'filter_from_start_date', '', 'string'));
        $this->setState('filter.start_date.to', $app->getUserStateFromRequest($this->context.'.filter.start_date.to', 'filter_to_start_date', '', 'string'));
        
        
        //Filtering deadline
        $this->setState('filter.deadline.from', $app->getUserStateFromRequest($this->context.'.filter.deadline.from', 'filter_from_deadline', '', 'string'));
        $this->setState('filter.deadline.to', $app->getUserStateFromRequest($this->context.'.filter.deadline.to', 'filter_to_deadline', '', 'string'));
        

        

        // Load the parameters.
        $params = JComponentHelper::getParams('com_fitness');
        $this->setState('params', $params);

        // List state information.
        parent::populateState('a.primary_goal_id', 'asc');
    }

    /**
     * Method to get a store id based on model configuration state.
     *
     * This is necessary because the model is used by the component and
     * different modules that might need different sets of data or different
     * ordering requirements.
     *
     * @param	string		$id	A prefix for the store id.
     * @return	string		A store id.
     * @since	1.6
     */
    protected function getStoreId($id = '') {
        // Compile the store id.
        $id.= ':' . $this->getState('filter.search');
        $id.= ':' . $this->getState('filter.state');

        return parent::getStoreId($id);
    }

    /**
     * Build an SQL query to load the list data.
     *
     * @return	JDatabaseQuery
     * @since	1.6
     */
    protected function getListQuery() {
        // Create a new query object.
        $db = $this->getDbo();
        $query = $db->getQuery(true);

        // Select the required fields from the table.
        $query->select(
                $this->getState(
                        'list.select', 'a.*, gf.name as training_period'
                )
        );
        $query->from('`#__fitness_mini_goals` AS a');
        
        $query->leftJoin('#__fitness_training_period AS gf ON gf.id = a.training_period_id');
        
        $session = &JFactory::getSession();
        $primary_goal_id = $session->get('primary_goal_id');
        if(JRequest::getVar('id')) $primary_goal_id = JRequest::getVar('id');
        $query->where('a.primary_goal_id = '.(int) $primary_goal_id);

        

        
        // Filter by published state
        $published = $this->getState('filter.state');
        if (is_numeric($published)) {
            $query->where('a.state = '.(int) $published);
        } else if ($published === '') {
            $query->where('(a.state IN (0, 1))');
        }

            // Filter by goal focus
        $training_period = $this->getState('filter.training_period');
        if (is_numeric($training_period)) {
            $query->where('gf.id = '.(int) $training_period);
        } 

        // Filter by search in title
        $search = $this->getState('filter.search');
        if (!empty($search)) {
            if (stripos($search, 'id:') === 0) {
                $query->where('a.id = ' . (int) substr($search, 3));
            } else {
                $search = $db->Quote('%' . $db->escape($search, true) . '%');
                $query->where('( a.primary_goal_id LIKE '.$search.'  OR  a.mini_goal_category_id LIKE '.$search.'  OR  a.deadline LIKE '.$search.'  OR  a.state LIKE '.$search.' )');
            }
        }
        
        
        //Filtering start date
        $filter_start_date_from = $this->state->get("filter.start_date.from");
        if ($filter_start_date_from) {
                $query->where("a.start_date >= '".$db->escape($filter_start_date_from)."'");
        }
        $filter_start_date_to = $this->state->get("filter.start_date.to");
        if ($filter_deadline_to) {
                $query->where("a.start_date <= '".$db->escape($filter_start_date_to)."'");
        }

        


        // Add the list ordering clause.
        $orderCol = $this->state->get('list.ordering');
        $orderDirn = $this->state->get('list.direction');
        if ($orderCol && $orderDirn) {
            $query->order($db->escape($orderCol . ' ' . $orderDirn));
        }

        return $query;
    }

    public function getItems() {
        $items = parent::getItems();
        
        return $items;
    }

}
