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
                'primary_goal', 'gn.id',
                'mini_goal', 'mgc.id',
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
                
                // Filter by primary goal
                $primary_goal = $app->getUserStateFromRequest($this->context . '.filter.primary_goal', 'filter_primary_goal', '', 'string');
                $this->setState('filter.primary_goal', $primary_goal);
                
                
                // Filter by mini goal
                $mini_goal = $app->getUserStateFromRequest($this->context . '.filter.mini_goal', 'filter_mini_goal', '', 'string');

                $this->setState('filter.mini_goal', $mini_goal);
                
                
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
                        . 'bp.name AS business_name, mgc.name AS mini_goal_name'
                )
        );
        $query->from('#__fitness_nutrition_plan AS a');
        
        $query->leftJoin('#__users AS u ON u.id = a.client_id');
        
        $query->leftJoin('#__fitness_goals AS gc ON gc.id = a.primary_goal');
        
        $query->leftJoin('#__fitness_goal_categories AS gn ON gn.id = gc.goal_category_id');
        

        $query->leftJoin('#__fitness_mini_goals AS mg ON mg.id = a.mini_goal');
        
        $query->leftJoin('#__fitness_mini_goal_categories AS mgc ON mg.mini_goal_category_id=mgc.id');
        
        
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
            $query->where('(c.primary_trainer = ' . (int) $user->id . ' OR FIND_IN_SET(' . $user->id . ' , c.other_trainers) )');
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
                
        // Filter by primary goal
        $primary_goal = $this->getState('filter.primary_goal');
        if (is_numeric($primary_goal)) {
            $query->where('gn.id = '.(int) $primary_goal);
        }    
        
        // Filter by mini goal
        $mini_goal = $this->getState('filter.mini_goal');
        //var_dump($mini_goal);
        if (is_numeric($mini_goal)) {
            $query->where('mgc.id = '.(int) $mini_goal);
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
        $sql = "SELECT id, force_active, created  FROM #__fitness_nutrition_plan  WHERE 
            client_id='$client_id'
            AND state='1'
        ";
        
        $result1 = FitnessHelper::customQuery($sql, 1);
        
        foreach ($result1 as $item) {
            if($item->force_active == '1') {
                return $item->id;
            }
        }
        
        $query = "SELECT id, force_active, created  FROM #__fitness_nutrition_plan  WHERE 
            " . $db->quote($current_date) . "
            BETWEEN CONCAT(active_start, " . $db->quote(' 00:00:00') . ") AND CONCAT(active_finish, " . $db->quote(' 23:59:59') . ")
            AND client_id='$client_id'
            AND state='1'
        ";
        
        $result2 = FitnessHelper::customQuery($query, 1);
        
        foreach ($result2 as $item) {
            $created[] = $item->created;
        }
        
        foreach ($result2 as $item) {
            if($item->created == max($created)) {
                return $item->id;
            }
        }
    }
    
    
    
    public function nutrition_plans() {
            
        $method = JRequest::getVar('_method');

        if(!$method) {
            $method = $_SERVER['REQUEST_METHOD'];
        }

        $model = json_decode(JRequest::getVar('model','','','',JREQUEST_ALLOWHTML));
         

        $id = JRequest::getVar('id', 0, '', 'INT');
        
        $table = '#__fitness_nutrition_plan';

        $helper = new FitnessHelper();

        switch ($method) {
            case 'GET': // Get Item(s)
                $data = new stdClass();
                $data->id = $id;  
                $data->sort_by = JRequest::getVar('sort_by'); 
                $data->order_dirrection = JRequest::getVar('order_dirrection'); 
                $data->page = JRequest::getVar('page'); 
                $data->limit = JRequest::getVar('limit'); 
                $data->state = JRequest::getVar('state'); 
                
                $data->active_start_from = JRequest::getVar('active_start_from', '0'); 
                $data->active_start_to = JRequest::getVar('active_start_to', '0'); 
                $data->active_finish_from = JRequest::getVar('active_finish_from', '0'); 
                $data->active_finish_to = JRequest::getVar('active_finish_to', '0'); 
                
                $data->active_plan = JRequest::getVar('active_plan'); 
                $data->force_active = JRequest::getVar('force_active'); 
                $data->primari_goal = JRequest::getVar('primari_goal'); 
                $data->mini_goal = JRequest::getVar('mini_goal'); 
                $data->nutrition_focus = JRequest::getVar('nutrition_focus'); 
                
                $data->client_id = JRequest::getVar('client_id'); 
                $data->client_name = JRequest::getVar('client_name'); 
                $data->trainer_name = JRequest::getVar('trainer_name'); 
                $data->created_by_name = JRequest::getVar('created_by_name'); 
                

                $data->business_profile_id = JRequest::getVar('business_profile_id'); 


                $data = $this->getPlans($table, $data);
                
                return $data;
                break;
            case 'PUT': 
                //update
                $id = $helper->insertUpdateObj($model, $table);
                break;
            case 'POST': // Create
                $id = $helper->insertUpdateObj($model, $table);
                 break;
            case 'DELETE': // Delete Item
                $id = JRequest::getVar('id', 0, '', 'INT');
                $id = $helper->deleteRow($id, $table);
                break;

            default:
                break;
        }

        $model->id = $id;

        return $model;
    }
    
    
    public function getPlans($table, $data) {
        
        $page = $data->page;
        
        $limit = $data->limit;
        
        $start = ($page - 1) * $limit;
        
        $sort_by = $data->sort_by;
        
        $order_dirrection = $data->order_dirrection;
        
        $id = $data->id;
        
        $db = JFactory::getDbo();
        
        $query = "SELECT a.*,";
        
        $query .= " (SELECT user_id  FROM #__fitness_clients WHERE user_id=a.client_id LIMIT 1) created_by_client,";
        
        $query .= " pg.start_date AS start_date_primary,";
        
        $query .= " pg.deadline AS deadline_primary,";
        
        $query .= " mg.start_date AS start_date_mini,";
        
        $query .= " mg.deadline AS deadline_mini,";
        
        //get total number
        if(!$id) {
            $query .= " (SELECT COUNT(*) FROM $table AS a ";

            $query .= " WHERE 1 ";

            if($data->client_id) {
                $query .= " AND a.client_id='$data->client_id'";
            }

            if($data->trainer_id) {
                $query .= " AND a.trainer_id='$data->trainer_id'";
            }

            //search by client name
            if (!empty($data->client_name)) {
                $sql = " SELECT GROUP_CONCAT(id) FROM #__users WHERE name LIKE '%$data->client_name%' ";

                $client_ids = FitnessHelper::customQuery($sql, 0);

                if($client_ids) {
                    $query .= " AND a.client_id IN ($client_ids)";
                }
            }

            //search by trainer name
            if (!empty($data->trainer_name)) {
                $sql = " SELECT GROUP_CONCAT(id) FROM #__users WHERE name LIKE '%$data->trainer_name%' ";

                $trainer_ids = FitnessHelper::customQuery($sql, 0);

                if($trainer_ids) {
                    $query .= " AND a.trainer_id IN ($trainer_ids)";
                }
            }

            if (!empty($data->active_start_from)) {
                $query .= " AND a.active_start >= '$data->active_start_from'";
            }

            if (!empty($data->active_start_to)) {
                $query .= " AND a.active_start <= '$data->active_start_to'";
            }

            if (!empty($data->active_finish_from)) {
                $query .= " AND a.active_finish >= '$data->active_finish_from'";
            }

            if (!empty($data->active_finish_to)) {
                $query .= " AND a.active_finish <= '$data->active_finish_to'";
            }

            if ($data->force_active != '' AND $data->force_active != '*') {
                $query .= " AND a.force_active='$data->force_active'";
            }

            if($data->primari_goal) {
                $query .= " AND a.primari_goal='$data->primari_goal'";
            }

            if($data->mini_goal) {
                $query .= " AND a.mini_goal='$data->mini_goal'";
            }

            if($data->nutrition_focus) {
                $query .= " AND a.nutrition_focus='$data->nutrition_focus'";
            }

            if($data->state != '' AND $data->state != '*') {
                $query .= " AND a.state='$data->state'";
            }
            
            $query .= " ) items_total, ";
        }
        //end get total number

        $query .= "
             (SELECT name FROM #__fitness_goal_categories WHERE id=gc.goal_category_id) primary_goal_name,
             mgn.name AS mini_goal_name,
             nf.name AS nutrition_focus_name,
             (SELECT name FROM #__fitness_training_period WHERE id=mg.training_period_id) training_period_name,
             (SELECT name FROM #__users WHERE id=a.trainer_id) trainer_name,
             (SELECT name FROM #__users WHERE id=a.client_id) client_name,
             (SELECT calories FROM #__fitness_nutrition_plan_targets WHERE nutrition_plan_id = a.id AND type="  . $db->quote('heavy') .  ") calories,
             (SELECT protein FROM #__fitness_nutrition_plan_targets WHERE nutrition_plan_id = a.id AND type="  . $db->quote('heavy') .  ") protein,
             (SELECT fats FROM #__fitness_nutrition_plan_targets WHERE nutrition_plan_id = a.id AND type="  . $db->quote('heavy') .  ") fats,
             (SELECT carbs FROM #__fitness_nutrition_plan_targets WHERE nutrition_plan_id = a.id AND type="  . $db->quote('heavy') .  ") carbs
                         
            FROM $table AS a
                
            LEFT JOIN #__fitness_goals AS gc ON gc.id = a.primary_goal
            LEFT JOIN #__fitness_goal_categories AS gn ON gn.id = gc.goal_category_id
            
            LEFT JOIN #__fitness_mini_goals AS mgc ON mgc.id = a.mini_goal
            LEFT JOIN #__fitness_mini_goal_categories AS mgn ON mgn.id = mgc.mini_goal_category_id

            LEFT JOIN #__fitness_nutrition_focus AS nf ON nf.id = a.nutrition_focus
            
            LEFT JOIN #__fitness_goals AS pg ON pg.id = a.primary_goal
            
            LEFT JOIN #__fitness_mini_goals AS mg ON mg.id = a.mini_goal

            WHERE 1";
        
        if($data->client_id) {
            $query .= " AND a.client_id='$data->client_id'";
        }
        
        if($data->trainer_id) {
            $query .= " AND a.trainer_id='$data->trainer_id'";
        }
        
        //search by client name
        if (!empty($data->client_name)) {
            $sql = " SELECT GROUP_CONCAT(id) FROM #__users WHERE name LIKE '%$data->client_name%' ";

            $client_ids = FitnessHelper::customQuery($sql, 0);

            if($client_ids) {
                $query .= " AND a.client_id IN ($client_ids)";
            }
        }
        
        //search by trainer name
        if (!empty($data->trainer_name)) {
            $sql = " SELECT GROUP_CONCAT(id) FROM #__users WHERE name LIKE '%$data->trainer_name%' ";

            $trainer_ids = FitnessHelper::customQuery($sql, 0);

            if($trainer_ids) {
                $query .= " AND a.trainer_id IN ($trainer_ids)";
            }
        }
        
        if (!empty($data->active_start_from)) {
            $query .= " AND a.active_start >= '$data->active_start_from'";
        }

        if (!empty($data->active_start_to)) {
            $query .= " AND a.active_start <= '$data->active_start_to'";
        }
        
        if (!empty($data->active_finish_from)) {
            $query .= " AND a.active_finish >= '$data->active_finish_from'";
        }
        
        if (!empty($data->active_finish_to)) {
            $query .= " AND a.active_finish <= '$data->active_finish_to'";
        }
            
        if ($data->force_active != '' AND $data->force_active != '*') {
            $query .= " AND a.force_active='$data->force_active'";
        }
        
        if($data->primari_goal) {
            $query .= " AND a.primari_goal='$data->primari_goal'";
        }
        
        if($data->mini_goal) {
            $query .= " AND a.mini_goal='$data->mini_goal'";
        }
        
        if($data->nutrition_focus) {
            $query .= " AND a.nutrition_focus='$data->nutrition_focus'";
        }

        if($data->state != '' AND $data->state != '*') {
            $query .= " AND a.state='$data->state'";
        }
        
        $query_type = 1;
        
        if($id) {
            $query .= " AND a.id='$id' ";
            $query_type = 2;
        }
        
        
        if($sort_by) {
            $query .= " ORDER BY " . $sort_by;
        }
        
        if($order_dirrection && $sort_by) {
            $query .=  " " . $order_dirrection;
        }
        
        if($limit) {
            $query .= " LIMIT $start, $limit";
        }

        $data = FitnessHelper::customQuery($query, $query_type);
        
        $helper = new FitnessHelper();
        
        if(!$id) {
            $i = 0;
            foreach ($data as $item) {
                $active_plan_id = $this->getUserActivePlanId($item->client_id);
                $data[$i]->active_plan_id = $active_plan_id;
                
                $client_trainers = $helper->get_client_trainers_names($item->client_id);

                $data[$i]->secondary_trainers = $client_trainers;
            
                $i++;
            }
        } else {
            $active_plan_id = $this->getUserActivePlanId($data->client_id);
            $data->active_plan_id = $active_plan_id;
            
            $client_trainers = $helper->get_client_trainers_names($data->client_id);

            $data->secondary_trainers = $client_trainers;
        }

        return  $data;
    }
    
    public function nutrition_plan_targets() {
            
        $method = JRequest::getVar('_method');

        if(!$method) {
            $method = $_SERVER['REQUEST_METHOD'];
        }

        $model = json_decode(JRequest::getVar('model','','','',JREQUEST_ALLOWHTML));

        $id = JRequest::getVar('id', 0, '', 'INT');
        $nutrition_plan_id = JRequest::getVar('nutrition_plan_id', 0, '', 'INT');
        
        $table = '#__fitness_nutrition_plan_targets';

        $helper = new FitnessHelper();

        switch ($method) {
            case 'GET': // Get Item(s)
                
                $query = "SELECT a.* ";
                
                
                $query .= " FROM $table AS a";
                
                $query .= " WHERE 1";

                $query_type = 1;
        
                if($id) {
                    $query .= " AND a.id='$id' ";
                    $query_type = 2;
                }
                
                if($nutrition_plan_id) {
                    $query .= " AND a.nutrition_plan_id='$nutrition_plan_id' ";
                    $query_type = 2;
                }
                
                $data = FitnessHelper::customQuery($query, $query_type);
                
                return $data;
                break;
            case 'PUT': 
                //update
                $id = $helper->insertUpdateObj($model, $table);
                break;
            case 'POST': // Create
                $id = $helper->insertUpdateObj($model, $table);
                 break;
            case 'DELETE': // Delete Item
                 $id = $helper->deleteRow($id, $table);
                break;

            default:
                break;
        }

        $model->id = $id;

        return $model;
    }

}
