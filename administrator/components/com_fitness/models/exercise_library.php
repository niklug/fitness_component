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
require_once  JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_fitness' . DS .'helpers' . DS . 'fitness.php';

/**
 * Methods supporting a list of Fitness records.
 */
class FitnessModelExercise_library extends JModelList {

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
                'exercise_name', 'a.exercise_name',
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

        

        // Load the parameters.
        $params = JComponentHelper::getParams('com_fitness');
        $this->setState('params', $params);

        // List state information.
        parent::populateState('a.id', 'asc');
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
                        'list.select', 'a.*'
                )
        );
        $query->from('`#__fitness_exercise_library` AS a');

        

        
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
                
            }
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
    
    
    public function select_filter() {
        $table = JRequest::getVar('table');
        $by_business_profile = JRequest::getVar('by_business_profile');
        $user_id = JFactory::getUser()->id;
        
        $helper = new FitnessHelper();
        
        $data =  $helper->select_filter($table, $user_id, $by_business_profile);
        
        return $data;
    }
    
    public function exercise_library() {
            
        $method = JRequest::getVar('_method');

        if(!$method) {
            $method = $_SERVER['REQUEST_METHOD'];
        }

        $model = json_decode(JRequest::getVar('model'));
        
        $id = JRequest::getVar('id', 0, '', 'INT');

        $table = '#__fitness_exercise_library';

        $helper = new FitnessHelper();

        switch ($method) {
            case 'GET': // Get Item(s)
                $data = new stdClass();
                $data->id = $id;  
                $data->exercise_name = JRequest::getVar('exercise_name'); 
                $data->client_name = JRequest::getVar('client_name'); 
                $data->sort_by = JRequest::getVar('sort_by'); 
                $data->order_dirrection = JRequest::getVar('order_dirrection'); 
                $data->page = JRequest::getVar('page'); 
                $data->limit = JRequest::getVar('limit'); 
                $data->state = JRequest::getVar('state'); 
                $data->exercise_type = JRequest::getVar('exercise_type', '0'); 
                $data->force_type = JRequest::getVar('force_type', '0'); 
                $data->mechanics_type = JRequest::getVar('mechanics_type', '0'); 
                $data->body_part = JRequest::getVar('body_part', '0'); 
                $data->target_muscles = JRequest::getVar('target_muscles', '0'); 
                $data->equipment_type = JRequest::getVar('equipment_type', '0'); 
                $data->difficulty = JRequest::getVar('difficulty', '0'); 
                $data->business_profiles = JRequest::getVar('business_profiles'); 
                $data->current_page = JRequest::getVar('current_page'); 
                $data->created_by_name = JRequest::getVar('created_by_name'); 

                $data = $this->getExerciseVideos($table, $data);
                
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
    
    public function getExerciseVideos($table, $data) {
        $user_id = JFactory::getUser()->id;
        $helper = new FitnessHelper();
        $business_profile_id = $helper->getBusinessProfileId($user_id);
        $business_profile_id = $business_profile_id['data'];
        $super_user_group = FitnessHelper::SUPERUSER_GROUP_ID;
       
        $id = $data->id;
        
        $sort_by = $data->sort_by;
        $order_dirrection = $data->order_dirrection;
        
        $page = $data->page;
        $limit = $data->limit;
        
        $start = ($page - 1) * $limit;
        
        $search_exercise_name = $data->exercise_name;
        $search_client_name = $data->client_name;
        
        //get rid of empty element
        $exercise_type = array_filter(explode(",", $data->exercise_type));
        $force_type = array_filter(explode(",", $data->force_type));
        $mechanics_type = array_filter(explode(",", $data->mechanics_type));
        $body_part = array_filter(explode(",", $data->body_part));
        $target_muscles = array_filter(explode(",", $data->target_muscles));
        $equipment_type = array_filter(explode(",", $data->equipment_type));
        $difficulty = array_filter(explode(",", $data->difficulty));
        
        $business_profiles = array_filter(explode(",", $data->business_profiles));
        //
        
        $state = $data->state;
        
        $current_page = $data->current_page;
        
        $query .= " SELECT a.*, ";
        
        $query .= " (SELECT user_id  FROM #__fitness_clients WHERE user_id=a.created_by LIMIT 1) created_by_client,";
        //get total number
        if(!$id) {
            $query .= " (SELECT COUNT(*) FROM $table AS a ";

            if ($current_page == 'my_favourites') {
                $query .= " LEFT JOIN #__fitness_exercise_library_favourites AS mf ON mf.item_id=a.id";
            }

            $query .= " LEFT JOIN #__fitness_clients AS c ON c.user_id = a.created_by ";


            $query .= " WHERE a.state='$state' ";


            if (!empty($search_exercise_name)) {
                $query .= " AND a.exercise_name LIKE '%$search_exercise_name%' ";
            }

            if (!empty($search_client_name)) {
                $sql = " SELECT id FROM #__users WHERE name LIKE '%$search_client_name%' ";

                $client_ids = FitnessHelper::customQuery($sql, 3);


                if($client_ids) {
                    $query .= " AND ( FIND_IN_SET('$client_ids[0]', a.my_exercise_clients) ";

                    foreach ($client_ids as $filter_option) {
                        $query .= " OR FIND_IN_SET('$filter_option', a.my_exercise_clients)";
                    }
                    $query .= ")";
                }
            }

            //1
            if($exercise_type) {
                $query .= " AND ( FIND_IN_SET('$exercise_type[0]', a.exercise_type) ";

                foreach ($exercise_type as $filter_option) {
                    $query .= " OR FIND_IN_SET('$filter_option', a.exercise_type)";
                }
                $query .= ")";
            }
            //2
            if($force_type) {
                $query .= " AND ( FIND_IN_SET('$force_type[0]', a.force_type) ";

                foreach ($force_type as $filter_option) {
                    $query .= " OR FIND_IN_SET('$filter_option', a.force_type)";
                }
                $query .= ")";
            }
            //3
            if($mechanics_type) {
                $query .= " AND ( FIND_IN_SET('$mechanics_type[0]', a.mechanics_type) ";

                foreach ($mechanics_type as $filter_option) {
                    $query .= " OR FIND_IN_SET('$filter_option', a.mechanics_type)";
                }
                $query .= ")";
            }
            //4
            if($body_part) {
                $query .= " AND ( FIND_IN_SET('$body_part[0]', a.body_part) ";

                foreach ($body_part as $filter_option) {
                    $query .= " OR FIND_IN_SET('$filter_option', a.body_part)";
                }
                $query .= ")";
            }
            //5
            if($target_muscles) {
                $query .= " AND ( FIND_IN_SET('$target_muscles[0]', a.target_muscles) ";

                foreach ($target_muscles as $filter_option) {
                    $query .= " OR FIND_IN_SET('$filter_option', a.target_muscles)";
                }
                $query .= ")";
            }
            //6
            if($equipment_type) {
                $query .= " AND ( FIND_IN_SET('$equipment_type[0]', a.equipment_type) ";

                foreach ($equipment_type as $filter_option) {
                    $query .= " OR FIND_IN_SET('$filter_option', a.equipment_type)";
                }
                $query .= ")";
            }
            //7
            if($difficulty) {
                $query .= " AND ( FIND_IN_SET('$difficulty[0]', a.difficulty) ";

                foreach ($difficulty as $filter_option) {
                    $query .= " OR FIND_IN_SET('$filter_option', a.difficulty)";
                }
                $query .= ")";
            }
            
            if (!empty($data->created_by_name)) {
                $sql = " SELECT GROUP_CONCAT(id) FROM #__users WHERE name LIKE '%$data->created_by_name%' ";

                $owner_ids = FitnessHelper::customQuery($sql, 0);

                if($owner_ids) {
                    $query .= " AND a.created_by IN ($owner_ids)";
                }
            }

            //by business 
            if(FitnessHelper::is_trainer($user_id)) {
                $query .= " AND (a.created_by='$user_id' "
                        . " OR c.primary_trainer='$user_id' "
                        . " OR  FIND_IN_SET('$user_id' , c.other_trainers) OR ugm.group_id='$super_user_group' "
                        . " OR a.user_view_permission LIKE '%\"$business_profile_id\":\"1\"%' )";
            }

            if($business_profiles) {
                $query .= " AND ( FIND_IN_SET('$business_profiles[0]', a.business_profiles) ";

                foreach ($business_profiles as $filter_option) {
                    $query .= " OR FIND_IN_SET('$filter_option', a.business_profiles)";
                }
                $query .= ")";
            }

            //frontend Exercise database
            if($data->current_page == 'exercise_database') {
                $query .= " AND  FIND_IN_SET('$business_profile_id', a.business_profiles) ";
                $query .= " AND  a.user_view_permission LIKE '%\"$business_profile_id\":\"1\"%' ";
            }

            //frontend My exercises
            if($data->current_page == 'my_exercises') {
                $query .= " AND  FIND_IN_SET('$user_id', a.my_exercise_clients) ";
            }


            if($current_page == 'my_favourites') {
                $query .= " AND mf.client_id='$user_id'";
            }

            $query .= " ) items_total, ";
        }
        //end get total number
        
        
        $query .= " (SELECT name FROM #__users WHERE id=a.assessed_by) assessed_by_name, ";
        $query .= " (SELECT name FROM #__users WHERE id=a.created_by) created_by_name, ";
        $query .= " (SELECT title FROM #__usergroups WHERE id=ugm.group_id) user_group_name, ";
        
        
        $query .=  " (SELECT GROUP_CONCAT(name) FROM #__fitness_settings_exercise_type WHERE "
                . " FIND_IN_SET(id, (SELECT exercise_type FROM #__fitness_exercise_library WHERE id =a.id))) exercise_type_names, ";
        
        $query .=  " (SELECT GROUP_CONCAT(name) FROM #__fitness_settings_force_type WHERE "
                . " FIND_IN_SET(id, (SELECT force_type FROM #__fitness_exercise_library WHERE id =a.id))) force_type_names, ";
        
        $query .=  " (SELECT GROUP_CONCAT(name) FROM #__fitness_settings_mechanics_type WHERE "
                . " FIND_IN_SET(id, (SELECT mechanics_type FROM #__fitness_exercise_library WHERE id =a.id))) mechanics_type_names, ";
        
        $query .=  " (SELECT GROUP_CONCAT(name) FROM #__fitness_settings_body_part WHERE "
                . " FIND_IN_SET(id, (SELECT body_part FROM #__fitness_exercise_library WHERE id =a.id))) body_part_names, ";
        
        $query .=  " (SELECT GROUP_CONCAT(name) FROM #__fitness_settings_target_muscles WHERE "
                . " FIND_IN_SET(id, (SELECT target_muscles FROM #__fitness_exercise_library WHERE id =a.id))) target_muscles_names, ";
        
        $query .=  " (SELECT GROUP_CONCAT(name) FROM #__fitness_settings_equipment WHERE "
                . " FIND_IN_SET(id, (SELECT equipment_type FROM #__fitness_exercise_library WHERE id =a.id))) equipment_type_names, ";
        
        $query .=  " (SELECT GROUP_CONCAT(name) FROM #__fitness_settings_difficulty WHERE "
                . " FIND_IN_SET(id, (SELECT difficulty FROM #__fitness_exercise_library WHERE id =a.id))) difficulty_names, ";
        
        
        $query .=  " (SELECT GROUP_CONCAT(name) FROM #__fitness_business_profiles WHERE "
                . " FIND_IN_SET(id, (SELECT business_profiles FROM #__fitness_exercise_library WHERE id =a.id))) business_profiles_names, ";
       
       
        //My Exercises List data
        $query .=  " (SELECT GROUP_CONCAT(name) FROM #__users WHERE  ";
       
        if(FitnessHelper::is_trainer($user_id)) {
            $query .=  " id IN (SELECT user_id FROM #__fitness_clients WHERE business_profile_id='$business_profile_id') AND ";
        }
       
        $query .= " FIND_IN_SET(id, (SELECT my_exercise_clients FROM #__fitness_exercise_library WHERE id =a.id))) my_exercise_clients_names, ";
        //
       
        $query .= " (SELECT id FROM #__fitness_exercise_library_favourites WHERE item_id=a.id AND client_id='$user_id') AS is_favourite, ";  
        
        $query .= " (SELECT user_id FROM #__user_usergroup_map WHERE user_id=a.created_by AND group_id='$super_user_group') AS created_by_superuser, "; 
        
        
        $query .= " (SELECT user_id FROM #__fitness_clients WHERE (primary_trainer='$user_id' OR FIND_IN_SET('$user_id', other_trainers)) AND user_id=a.created_by ) AS is_client_of_trainer,";
        
        
        $query .= " (SELECT GROUP_CONCAT(user_id) FROM #__fitness_clients WHERE primary_trainer='$user_id' OR FIND_IN_SET('$user_id', other_trainers)) AS clients_of_trainer ";
       
        $query .= " FROM $table AS a ";

        $query .= " LEFT JOIN #__user_usergroup_map AS ugm ON a.created_by=ugm.user_id";
        
        if ($current_page == 'my_favourites') {
            $query .= " LEFT JOIN #__fitness_exercise_library_favourites AS mf ON mf.item_id=a.id";
        }
        
        $query .= " LEFT JOIN #__fitness_clients AS c ON c.user_id = a.created_by ";

        $query .= " WHERE a.state='$state'";
        
        if (!empty($search_exercise_name)) {
            $query .= " AND a.exercise_name LIKE '%$search_exercise_name%' ";
        }

        if (!empty($search_client_name)) {
            if($client_ids) {
               
                $query .= " AND ( FIND_IN_SET('$client_ids[0]', a.my_exercise_clients) ";

                foreach ($client_ids as $filter_option) {
                    $query .= " OR FIND_IN_SET('$filter_option', a.my_exercise_clients)";
                }
                $query .= ")";
            }
        }
        
        //filters
        //1
        if($exercise_type) {
            $query .= " AND ( FIND_IN_SET('$exercise_type[0]', a.exercise_type) ";
            
            foreach ($exercise_type as $filter_option) {
                $query .= " OR FIND_IN_SET('$filter_option', a.exercise_type)";
            }
            $query .= ")";
        }
        //2
        if($force_type) {
            $query .= " AND ( FIND_IN_SET('$force_type[0]', a.force_type) ";
            
            foreach ($force_type as $filter_option) {
                $query .= " OR FIND_IN_SET('$filter_option', a.force_type)";
            }
            $query .= ")";
        }
        //3
        if($mechanics_type) {
            $query .= " AND ( FIND_IN_SET('$mechanics_type[0]', a.mechanics_type) ";
            
            foreach ($mechanics_type as $filter_option) {
                $query .= " OR FIND_IN_SET('$filter_option', a.mechanics_type)";
            }
            $query .= ")";
        }
        //4
        if($body_part) {
            $query .= " AND ( FIND_IN_SET('$body_part[0]', a.body_part) ";
            
            foreach ($body_part as $filter_option) {
                $query .= " OR FIND_IN_SET('$filter_option', a.body_part)";
            }
            $query .= ")";
        }
        //5
        if($target_muscles) {
            $query .= " AND ( FIND_IN_SET('$target_muscles[0]', a.target_muscles) ";
            
            foreach ($target_muscles as $filter_option) {
                $query .= " OR FIND_IN_SET('$filter_option', a.target_muscles)";
            }
            $query .= ")";
        }
        //6
        if($equipment_type) {
            $query .= " AND ( FIND_IN_SET('$equipment_type[0]', a.equipment_type) ";
            
            foreach ($equipment_type as $filter_option) {
                $query .= " OR FIND_IN_SET('$filter_option', a.equipment_type)";
            }
            $query .= ")";
        }
        //7
        if($difficulty) {
            $query .= " AND ( FIND_IN_SET('$difficulty[0]', a.difficulty) ";
            
            foreach ($difficulty as $filter_option) {
                $query .= " OR FIND_IN_SET('$filter_option', a.difficulty)";
            }
            $query .= ")";
        }
        
        if (!empty($data->created_by_name)) {
                $sql = " SELECT GROUP_CONCAT(id) FROM #__users WHERE name LIKE '%$data->created_by_name%' ";

                $owner_ids = FitnessHelper::customQuery($sql, 0);

                if($owner_ids) {
                    $query .= " AND a.created_by IN ($owner_ids)";
                }
            }
        
        //by business
        if(FitnessHelper::is_trainer($user_id)) {
            $query .= " AND (a.created_by='$user_id' "
                    . " OR c.primary_trainer='$user_id'"
                    . " OR  FIND_IN_SET('$user_id' , c.other_trainers) "
                    . " OR ugm.group_id='$super_user_group'"
                    . " OR a.user_view_permission LIKE '%\"$business_profile_id\":\"1\"%' ) ";
        }

            

        if($business_profiles) {
            $query .= " AND ( FIND_IN_SET('$business_profiles[0]', a.business_profiles) ";
            
            foreach ($business_profiles as $filter_option) {
                $query .= " OR FIND_IN_SET('$filter_option', a.business_profiles)";
            }
            $query .= ")";
        }
        //end filters
        
        
        //frontend Exercise database
        if($current_page == 'exercise_database') {
            $query .= " AND  FIND_IN_SET('$business_profile_id', a.business_profiles) ";
            $query .= " AND  a.user_view_permission LIKE '%\"$business_profile_id\":\"1\"%' ";
        }
        
        
        //frontend My Exercises
        if($current_page == 'my_exercises') {
            $query .= " AND  FIND_IN_SET('$user_id', a.my_exercise_clients) ";
        }
        
        if($current_page == 'my_favourites') {
            $query .= " AND mf.client_id='$user_id'";
        }
        
        $query_type = 1;

        if($id) {
            $query .= " AND a.id='$id' ";
            $query_type = 2;
        }

        
        if($sort_by) {
            $query .= " ORDER BY " . $sort_by;
        }

        if($order_dirrection) {
            $query .=  " " . $order_dirrection;
        }

        if($limit) {
            $query .= " LIMIT $start, $limit";
        }

        $data = FitnessHelper::customQuery($query, $query_type);
        
        
        
        return $data;

    }
    
    public function getClients() {
        $user_id = JFactory::getUser()->id;
        
        $query = "SELECT a.*, ";
        

        $query .= " (SELECT user_id FROM #__fitness_clients WHERE (primary_trainer='$user_id' OR FIND_IN_SET('$user_id', other_trainers)) AND user_id=a.user_id ) is_client_of_trainer,";
  
        
        $query .= " u.name as name ";
        
        $query .= " FROM #__fitness_clients AS a";
        
        $query .= " LEFT JOIN #__users AS u ON a.user_id=u.id";

        $query .= " WHERE a.state='1'";
        
        $query .= " ORDER BY u.name ASC";
        
        return FitnessHelper::customQuery($query, 1);
    }

}
