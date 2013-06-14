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
 * Methods supporting a list of Fitness_goals records.
 */
class FitnessModelgoals extends JModelList {

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
                'user_id', 'a.user_id',
                'trainer_id', 'a.trainer_id',
                'category_id', 'a.category_id',
                'deadline', 'a.deadline',
                'details', 'a.details',
                'comments', 'a.comments',
                'completed', 'a.completed',
                'state', 'a.state',
                'created', 'a.created',
                'modified', 'a.modified',
                'u.name'

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

        
        //Filtering deadline
        $this->setState('filter.deadline.from', $app->getUserStateFromRequest($this->context.'.filter.deadline.from', 'filter_from_deadline', '', 'string'));
        $this->setState('filter.deadline.to', $app->getUserStateFromRequest($this->context.'.filter.deadline.to', 'filter_to_deadline', '', 'string'));
        
        // Filter by primary trainer
        $primary_trainer = $app->getUserStateFromRequest($this->context . '.filter.primary_trainer', 'filter_primary_trainer', '', 'string');
        $this->setState('filter.primary_trainer', $primary_trainer);
        
                
        // Filter by group
        $group = $app->getUserStateFromRequest($this->context . '.filter.group', 'filter_group', '', 'string');
        $this->setState('filter.group', $group);
        
        // Filter by goal status
        $goal_status = $app->getUserStateFromRequest($this->context . '.filter.goal_status', 'filter_goal_status', '', 'string');
        $this->setState('filter.goal_status', $goal_status);
        
        // Filter by created
        $created = $app->getUserStateFromRequest($this->context . '.filter.created', 'filter_created', '', 'string');
        $this->setState('filter.created', $created);
        
                
        // Filter by modified
        $modified = $app->getUserStateFromRequest($this->context . '.filter.modified', 'filter_modified', '', 'string');
        $this->setState('filter.modified', $modified);


        // Load the parameters.
        $params = JComponentHelper::getParams('com_fitness');
        $this->setState('params', $params);

        // List state information.
        parent::populateState('a.user_id', 'asc');
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
        $id.= ':' . $this->getState('filter.primary_trainer');
        $id.= ':' . $this->getState('filter.group');
        $id.= ':' . $this->getState('filter.goal_status');
        $id.= ':' . $this->getState('filter.created');
        $id.= ':' . $this->getState('filter.modified');

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
                        'list.select', 'a.*,  ug.title as usergroup'
                )
        );
        $query->from('`#__fitness_goals` AS a');
                
        $query->leftJoin('#__users AS u ON u.id = a.user_id');
        
        $query->leftJoin('#__user_usergroup_map AS g ON u.id = g.user_id');
        
        $query->leftJoin('#__usergroups AS ug ON ug.id = g.group_id');

        

        
        // Filter by published state
        $published = $this->getState('filter.state');
        if (is_numeric($published)) {
            $query->where('a.state = '.(int) $published);
        } else if ($published === '') {
            $query->where('(a.state IN (0, 1))');
        }

    
    
        // Filter by primary trainer
        $primary_trainer = $this->getState('filter.primary_trainer');
        if (is_numeric($primary_trainer)) {
            $query->where('a.primary_trainer = '.(int) $primary_trainer);
        } 


        // Filter by group
        $group = $this->getState('filter.group');
        if (is_numeric($group)) {
            $query->where('g.group_id = '.(int) $group);
        } 
        
        
        // Filter by goal status

        $goal_status = $this->getState('filter.goal_status');

        if ($goal_status) {
            $query->where('a.completed = ' . (int) $goal_status);
        } 
        
        // Filter by created
        $created = $this->getState('filter.created');
        $created = $db->Quote('%' . $db->escape($created, true) . '%');
        

        if ($created) {
            $query->where('a.created LIKE '.$created);
        } 
        
        
         // Filter by created
        $modified = $this->getState('filter.modified');
        $modified = $db->Quote('%' . $db->escape($modified, true) . '%');

        if ($modified) {
            $query->where('a.modified LIKE '.$modified);
        } 
        
        

        // Filter by search in title
        $search = $this->getState('filter.search');
        if (!empty($search)) {
            if (stripos($search, 'id:') === 0) {
                $query->where('a.id = ' . (int) substr($search, 3));
            } else {
                $search = $db->Quote('%' . $db->escape($search, true) . '%');
                $query->where('( a.user_id LIKE '.$search.'
                    OR  a.primary_trainer LIKE '.$search.' 
                    OR  a.category_id LIKE '.$search.' 
                    OR  a.deadline LIKE '.$search.' 
                    OR  a.created LIKE '.$search.' 
                    OR  a.modified LIKE '.$search.'
                    OR  u.username LIKE '.$search.' 
                    OR  u.name LIKE '.$search.' 
                    OR  u.email LIKE '.$search.'   
                    OR  ug.title LIKE '.$search.' 
                              
                 )');
            }
        }

        

		//Filtering deadline
		$filter_deadline_from = $this->state->get("filter.deadline.from");
		if ($filter_deadline_from) {
			$query->where("a.deadline >= '".$db->escape($filter_deadline_from)."'");
		}
		$filter_deadline_to = $this->state->get("filter.deadline.to");
		if ($filter_deadline_to) {
			$query->where("a.deadline <= '".$db->escape($filter_deadline_to)."'");
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
