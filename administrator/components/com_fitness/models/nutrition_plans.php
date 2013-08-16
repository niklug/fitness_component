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
class FitnessModelnutrition_plans extends JModelList {

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
                'client_id', 'a.client_id',
                'trainer_id', 'a.trainer_id',
                'active_start', 'a.active_start',
                'active_finish', 'a.active_finish',
                'active', 'a.active',
                'force_active', 'a.force_active',
                'primary_goal', 'a.primary_goal',
                'nutrition_focus', 'a.nutrition_focus',
                'state', 'a.state',

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

        
		//Filtering active_start
		$this->setState('filter.active_start.from', $app->getUserStateFromRequest($this->context.'.filter.active_start.from', 'filter_from_active_start', '', 'string'));
		$this->setState('filter.active_start.to', $app->getUserStateFromRequest($this->context.'.filter.active_start.to', 'filter_to_active_start', '', 'string'));

		//Filtering active_finish
		$this->setState('filter.active_finish.from', $app->getUserStateFromRequest($this->context.'.filter.active_finish.from', 'filter_from_active_finish', '', 'string'));
		$this->setState('filter.active_finish.to', $app->getUserStateFromRequest($this->context.'.filter.active_finish.to', 'filter_to_active_finish', '', 'string'));

                // Filter by primary trainer
                $primary_trainer = $app->getUserStateFromRequest($this->context . '.filter.primary_trainer', 'filter_primary_trainer', '', 'string');
                $this->setState('filter.primary_trainer', $primary_trainer);
                
                // Filter by active
                $active = $app->getUserStateFromRequest($this->context . '.filter.active', 'filter_active', '', 'string');
                $this->setState('filter.active', $active);
                
                // Filter by goal category
                $goal_category = $app->getUserStateFromRequest($this->context . '.filter.goal_category', 'filter_goal_category', '', 'string');
                $this->setState('filter.goal_category', $goal_category);
                
                
                // Filter by nutrition focus
                $nutrition_focus = $app->getUserStateFromRequest($this->context . '.filter.nutrition_focus', 'filter_nutrition_focus', '', 'string');
                $this->setState('filter.nutrition_focus', $nutrition_focus);

        // Load the parameters.
        $params = JComponentHelper::getParams('com_fitness');
        $this->setState('params', $params);

        // List state information.
        parent::populateState('a.client_id', 'asc');
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
                        'list.select', 'a.*, gc.name AS primary_goal_name, nf.name AS nutrition_focus_name'
                )
        );
        $query->from('`#__fitness_nutrition_plan` AS a');
        
        $query->leftJoin('#__users AS u ON u.id = a.client_id');
        

        $query->leftJoin('#__fitness_goal_categories AS gc ON gc.id = a.primary_goal');
        
        $query->leftJoin('#__fitness_nutrition_focus AS nf ON nf.id = a.nutrition_focus');
        
        // filter only for Super Users
        $user = &JFactory::getUser();
        if ($this->getUserGroup($user->id) != 'Super Users') {
            $other_trainers = $db->Quote('%' . $db->escape($user->id, true) . '%');
            $query->where('(a.trainer_id = ' . (int) $user->id .' )');
        }
        
        // filter only for Super Users
        $user = &JFactory::getUser();
        if ($this->getUserGroup($user->id) != 'Super Users') {

            $other_trainers = $db->Quote('%' . $db->escape($user->id, true) . '%');
            $query->where('a.client_id IN (SELECT DISTINCT user_id FROM #__fitness_clients WHERE primary_trainer=' .  (int) $user->id .  ' OR other_trainers LIKE ' . $other_trainers .  ' )');
        }
        

        
    // Filter by published state
    $published = $this->getState('filter.state');
    if (is_numeric($published)) {
        $query->where('a.state = '.(int) $published);
    } else if ($published === '') {
        $query->where('(a.state IN (0, 1))');
    }
    

        // Filter by search in title
        $search = $this->getState('filter.search');
        if (!empty($search)) {
            if (stripos($search, 'id:') === 0) {
                $query->where('a.id = ' . (int) substr($search, 3));
            } else {
                $search = $db->Quote('%' . $db->escape($search, true) . '%');
                $query->where('( a.client_id LIKE '.$search.'   OR  u.name LIKE '.$search.' )');
            }
        }

        

        //Filtering active_start
        $filter_active_start_from = $this->state->get("filter.active_start.from");
        if ($filter_active_start_from) {
                $query->where("a.active_start >= '".$db->escape($filter_active_start_from)."'");
        }
        $filter_active_start_to = $this->state->get("filter.active_start.to");
        if ($filter_active_start_to) {
                $query->where("a.active_start <= '".$db->escape($filter_active_start_to)."'");
        }

        //Filtering active_finish
        $filter_active_finish_from = $this->state->get("filter.active_finish.from");
        if ($filter_active_finish_from) {
                $query->where("a.active_finish >= '".$db->escape($filter_active_finish_from)."'");
        }
        $filter_active_finish_to = $this->state->get("filter.active_finish.to");
        if ($filter_active_finish_to) {
                $query->where("a.active_finish <= '".$db->escape($filter_active_finish_to)."'");
        }
        
        // Filter by active
        $active = $this->getState('filter.active');
        if (is_numeric($active)) {
            $query->where('a.active = '.(int) $active);
        }    

        // Filter by primary trainer
        $primary_trainer = $this->getState('filter.primary_trainer');
        if (is_numeric($primary_trainer)) {
            $query->where('a.trainer_id = '.(int) $primary_trainer);
        } 
                
        // Filter by goal category
        $goal_category = $this->getState('filter.goal_category');
        if (is_numeric($goal_category)) {
            $query->where('gc.id = '.(int) $goal_category);
        }    
        
        
        
        // Filter by nutrition focus
        $nutrition_focus = $this->getState('filter.nutrition_focus');
        if (is_numeric($goal_category)) {
            $query->where('a.nutrition_focus = '.(int) $nutrition_focus);
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
    
     function getUserGroup($user_id) {
        $db = JFactory::getDBO();
        $query = "SELECT title FROM #__usergroups WHERE id IN 
            (SELECT group_id FROM #__user_usergroup_map WHERE user_id='$user_id')";
        $db->setQuery($query);
        return $db->loadResult();
    }

}
