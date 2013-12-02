<?php

/**
 * @version     1.0.0
 * @package     com_fitness
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Nikolay Korban <niklug@ukr.net> - http://
 */
defined('_JEXEC') or die;
require_once  JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_fitness' . DS .'helpers' . DS . 'fitness.php';
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
                'force_active', 'a.force_active',
                'primary_goal', 'a.primary_goal',
                'nutrition_focus', 'a.nutrition_focus',
                'business_name', 'business_name',
                'created', 'a.created',
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
                
                // Filter by force active
                $force_active = $app->getUserStateFromRequest($this->context . '.filter.force_active', 'filter_force_active', '', 'string');
                $this->setState('filter.force_active', $force_active);
                
                // Filter by goal category
                $goal_category = $app->getUserStateFromRequest($this->context . '.filter.goal_category', 'filter_goal_category', '', 'string');
                $this->setState('filter.goal_category', $goal_category);
                
                
                // Filter by nutrition focus
                $nutrition_focus = $app->getUserStateFromRequest($this->context . '.filter.nutrition_focus', 'filter_nutrition_focus', '', 'string');
                $this->setState('filter.nutrition_focus', $nutrition_focus);
                
                 // Filter by business profile
                $business_profile_id = $app->getUserStateFromRequest($this->context . '.filter.business_profile_id', 'filter_business_profile_id', '', 'string');
                $this->setState('filter.business_profile_id', $business_profile_id);

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
                        'list.select', 'a.*, 
                         (SELECT name FROM #__fitness_goal_categories WHERE id=gc.goal_category_id) primary_goal_name,
                         nf.name AS nutrition_focus_name,
                         (SELECT calories FROM #__fitness_nutrition_plan_targets WHERE nutrition_plan_id = a.id AND type='  . $db->quote('heavy') .  ') calories,
                         (SELECT protein FROM #__fitness_nutrition_plan_targets WHERE nutrition_plan_id = a.id AND type='  . $db->quote('heavy') .  ') protein,
                         (SELECT fats FROM #__fitness_nutrition_plan_targets WHERE nutrition_plan_id = a.id AND type='  . $db->quote('heavy') .  ') fats,
                         (SELECT carbs FROM #__fitness_nutrition_plan_targets WHERE nutrition_plan_id = a.id AND type='  . $db->quote('heavy') .  ') carbs,'
                        . 'bp.name AS business_name'
                )
        );
        $query->from('#__fitness_nutrition_plan AS a');
        
        $query->leftJoin('#__users AS u ON u.id = a.client_id');
        
        $query->leftJoin('#__fitness_goals AS gc ON gc.id = a.primary_goal');
        
        $query->leftJoin('#__fitness_goal_categories AS gn ON gn.id = gc.goal_category_id');
        
        $query->leftJoin('#__fitness_nutrition_focus AS nf ON nf.id = a.nutrition_focus');
        
        $query->leftJoin('#__fitness_clients AS c ON c.user_id = a.client_id');
        
        $query->leftJoin('#__fitness_business_profiles AS bp ON bp.id = c.business_profile_id');
        

        
        if(FitnessHelper::is_primary_administrator() || FitnessHelper::is_secondary_administrator()) {
            $trainers_group_id = FitnessHelper::getTrainersGroupId();
            $query->where('bp.group_id = '.(int) $trainers_group_id);
        }
        
        // 
        $user = &JFactory::getUser();
        
        if(!FitnessHelper::is_primary_administrator() && !FitnessHelper::is_secondary_administrator() && FitnessHelper::is_trainer()) {
            $other_trainers = $db->Quote('%' . $db->escape($user->id, true) . '%');
            $query->where('(c.primary_trainer = ' . (int) $user->id . ' OR c.other_trainers LIKE ' . $other_trainers . ' )');
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
        


        // Filter by primary trainer
        $primary_trainer = $this->getState('filter.primary_trainer');
        if (is_numeric($primary_trainer)) {
            $query->where('a.trainer_id = '.(int) $primary_trainer);
        } 
                
        // Filter by goal category
        $goal_category = $this->getState('filter.goal_category');
        if (is_numeric($goal_category)) {
            $query->where('gn.id = '.(int) $goal_category);
        }    
        
        
        
        // Filter by nutrition focus
        $nutrition_focus = $this->getState('filter.nutrition_focus');
        if (is_numeric($nutrition_focus)) {
            $query->where('a.nutrition_focus = '.(int) $nutrition_focus);
        }  
        
                
        // Filter by force active
        $force_active = $this->getState('filter.force_active');
        if (is_numeric($force_active)) {
            if($force_active == '1') {
            $query->where('a.force_active = 1');
            } else {
                $query->where('a.force_active = 0');
            }
        }  
        
        
        // Filter by  active
        $active = $this->getState('filter.active');
        if (is_numeric($active)) {
            $db = JFactory::getDBO();
            $sql1 = "SELECT DISTINCT client_id FROM #__fitness_nutrition_plan";
            $db->setQuery($sql1);
            if(!$db->query()) {
                JError::raiseError($db->getErrorMsg());
            }
            $clients =  $db->loadResultArray();
            foreach ($clients as $client) {
                $ids[] =  $this->getUserActivePlanId($client);
            }

            $ids = implode(',', $ids);
            if($active == '1') {
                $query->where('a.id IN ('. $ids . ')');
            } else {
                $query->where('a.id NOT IN ('. $ids . ')');
            }
        }  
        
        
        // Filter by business profile
        $business_profile_id = $this->getState('filter.business_profile_id');
        if (is_numeric($business_profile_id)) {
            $query->where('c.business_profile_id = '.(int) $business_profile_id);
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
    
     private function getUserGroup($user_id) {
        $db = JFactory::getDBO();
        $query = "SELECT title FROM #__usergroups WHERE id IN 
            (SELECT group_id FROM #__user_usergroup_map WHERE user_id='$user_id')";
        $db->setQuery($query);
        if(!$db->query()) {
            JError::raiseError($db->getErrorMsg());
        }
        return $db->loadResult();
    }
    
    public function getCurrentDate() {
        $config = JFactory::getConfig();
        $date = new DateTime();
        $date->setTimezone(new DateTimeZone($config->getValue('config.offset')));
        return $date->format('Y-m-d H:i:s');
    }

    
    public function getUserActivePlanId($client_id) {
        $current_date = $this->getCurrentDate();
        $db = JFactory::getDBO();
        $query = "SELECT id, force_active, created  FROM #__fitness_nutrition_plan  WHERE 
            " . $db->quote($current_date) . "
            BETWEEN active_start AND active_finish
            AND client_id='$client_id'
            AND state='1'
        ";
        
        $result = FitnessHelper::customQuery($query, 1);
        
        foreach ($result as $item) {
            if($item->force_active == '1') {
                return $item->id;
            }
            $created[] = $item->created;
        }
        
        foreach ($result as $item) {
            if($item->created == max($created)) {
                return $item->id;
            }
        }
    }

}
