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
class FitnessModelprograms_templates extends JModelList {

    /**
     * Constructor.
     *
     * @param    array    An optional associative array of configuration settings.
     * @see        JController
     * @since    1.6
     */
    public function __construct($config = array()) {

        parent::__construct($config);
    }

    /**
     * Method to auto-populate the model state.
     *
     * Note. Calling getState in this method will result in recursion.
     */
    protected function populateState($ordering = null, $direction = null) {
        
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

        return parent::getStoreId($id);
    }

    /**
     * Build an SQL query to load the list data.
     *
     * @return	JDatabaseQuery
     * @since	1.6
     */
    protected function getListQuery() {
       
        return $query;
    }

    public function getItems() {
        $items = parent::getItems();
        
        return $items;
    }
    
    public function programs_templates() {
            
        $method = JRequest::getVar('_method');

        if(!$method) {
            $method = $_SERVER['REQUEST_METHOD'];
        }

        $model = json_decode(JRequest::getVar('model'));
        
        $id = JRequest::getVar('id', 0, '', 'INT');
        
        $table = '#__fitness_programs_templates';

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

                $data->name = JRequest::getVar('name');
                $data->appointment_id = JRequest::getVar('appointment_id'); 
                $data->session_type = JRequest::getVar('session_type'); 
                $data->session_focus = JRequest::getVar('session_focus'); 
                
                $data->date_from = JRequest::getVar('date_from', '0'); 
                $data->date_to = JRequest::getVar('date_to', '0'); 
                $data->client_name = JRequest::getVar('client_name'); 
                $data->created_by_name = JRequest::getVar('created_by_name'); 
                

                $data->business_profile_id = JRequest::getVar('business_profile_id'); 
                $data->client_id = JRequest::getVar('client_id');
                $data->current_page = JRequest::getVar('current_page'); 

                $data = $this->getProgramTemplate($table, $data);
                
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
    
    
    
    public function getProgramTemplate($table, $data) {
        
        $helper = new FitnessHelper();
        
        $table = '#__fitness_programs_templates';
        
        $page = $data->page;
        
        $limit = $data->limit;
        
        $start = ($page - 1) * $limit;
        
        $sort_by = $data->sort_by;
        
        $order_dirrection = $data->order_dirrection;
        
        $id = $data->id;
        
        $user_id = JFactory::getUser()->id;

        $query = " SELECT a.*,";
        
        $query .= " (SELECT user_id  FROM #__fitness_clients WHERE user_id=a.created_by LIMIT 1) created_by_client,";
        
        $query .= " t.name AS appointment_name,";

        $query .= " st.name AS session_type_name,";
        
        $query .= " sf.name AS session_focus_name,";    
        
        $query .= " bp.name AS business_profile_name,"; 
        
        //get total number
        if(!$id) {
            $query .= " (SELECT COUNT(*) FROM $table AS a ";

            $query .= " WHERE 1 ";
            
            //1
            if(isset($data->name)) {
                $query .= " AND a.name LIKE '%$data->name%'  ";
            }
            
            //2
            if(isset($data->state) AND $data->state != '*') {
                $query .= " AND a.state='$data->state' ";
            }

            //3
            if($data->appointment_id) {
                $query .= " AND a.appointment_id IN ($data->appointment_id)";
            }

            //4
            if($data->session_type) {
                $query .= " AND a.session_type IN ($data->session_type)";
            }
            //5
            if($data->session_focus) {
                $query .= " AND a.session_focus IN ($data->session_focus)";
            }
            //6
            if (!empty($data->date_from)) {
                $query .= " AND a.created >= '$data->date_from'";
            }
            //7
            if (!empty($data->date_to)) {
                $query .= " AND a.created <= '$data->date_to'";
            }
            //8
            //search by created_by name
            if (!empty($data->created_by_name)) {
                $sql = " SELECT GROUP_CONCAT(id) FROM #__users WHERE name LIKE '%$data->created_by_name%' ";

                $owner_ids = FitnessHelper::customQuery($sql, 0);

                if($owner_ids) {
                    $query .= " AND a.created_by IN ($owner_ids)";
                }
            }
            //9
            //

            // 10 search by client name
            if (!empty($data->client_name)) {
                $sql = " SELECT GROUP_CONCAT(id) FROM #__users WHERE name LIKE '%$data->client_name%' ";

                $client_ids = FitnessHelper::customQuery($sql, 0);

                if($client_ids) {
                    $query .= " AND a.id IN (SELECT  DISTINCT item_id FROM #__fitness_pr_temp_clients WHERE client_id IN ($client_ids))";
                }
            }
            // 11 filter by business profile
            if (!empty($data->business_profile_id)) {
                $query .= " AND a.business_profile_id IN ($data->business_profile_id)";
            }
            
            //12
            if ($data->client_id) {
                $query .= " AND a.id IN (SELECT item_id  FROM #__fitness_pr_temp_clients WHERE client_id='$data->client_id' )";
            }


            $query .= " ) items_total, ";
        }
        //end get total number
        
        $query .= " (SELECT name FROM #__users WHERE id=a.created_by) created_by_name, ";
        
        $query .= " (SELECT GROUP_CONCAT(name) FROM #__users WHERE id IN (SELECT client_id FROM #__fitness_pr_temp_clients WHERE item_id=a.id)) group_clients_names ";
        
        $query .= "  FROM $table AS a";
        
        $query .= " LEFT JOIN #__fitness_categories AS t ON t.id = a.appointment_id ";
        
        $query .= " LEFT JOIN #__fitness_session_type AS st ON st.id = a.session_type ";
        
        $query .= " LEFT JOIN #__fitness_session_focus AS sf ON sf.id = a.session_focus ";
        
        $query .= " LEFT JOIN #__fitness_business_profiles AS bp ON bp.id = a.business_profile_id ";
        
        $query .= " WHERE 1 ";
        
        //1
        if(isset($data->name)) {
            $query .= " AND a.name LIKE '%$data->name%'  ";
        }
            
        //2
        if(isset($data->state) AND $data->state != '*') {
            $query .= " AND a.state='$data->state' ";
        }
        //3
        if($data->appointment_id) {
            $query .= " AND a.appointment_id IN ($data->appointment_id)";
        }

        //4
        if($data->session_type) {
            $query .= " AND a.session_type IN ($data->session_type)";
        }
        //5
        if($data->session_focus) {
            $query .= " AND a.session_focus IN ($data->session_focus)";
        }
        //6
        if (!empty($data->date_from)) {
            $query .= " AND a.created >= '$data->date_from'";
        }
        //7
        if (!empty($data->date_to)) {
            $query .= " AND a.created <= '$data->date_to'";
        }
        //8
        //search by created_by name
        if (!empty($data->created_by_name)) {
            $sql = " SELECT GROUP_CONCAT(id) FROM #__users WHERE name LIKE '%$data->created_by_name%' ";

            $owner_ids = FitnessHelper::customQuery($sql, 0);

            if($owner_ids) {
                $query .= " AND a.created_by IN ($owner_ids)";
            }
        }
        //9

        //// 10 search by client name
            if (!empty($data->client_name)) {
                $sql = " SELECT GROUP_CONCAT(id) FROM #__users WHERE name LIKE '%$data->client_name%' ";

                $client_ids = FitnessHelper::customQuery($sql, 0);

                if($client_ids) {
                    $query .= " AND a.id IN (SELECT  DISTINCT item_id FROM #__fitness_pr_temp_clients WHERE client_id IN ($client_ids))";
                }
            }
            
        // 11 filter by business profile
        if (!empty($data->business_profile_id)) {
            $query .= " AND a.business_profile_id IN ($data->business_profile_id)";
        }
        
        //12
        if ($data->client_id) {
            $query .= " AND a.id IN (SELECT item_id  FROM #__fitness_pr_temp_clients WHERE client_id='$data->client_id' )";
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
       


        $items = FitnessHelper::customQuery($query, $query_type);
        
        if(!$id) {
            $i = 0;
            foreach ($items as $item) {
                $group_clients_data = $this->getGroupClientsData($item->id);
                $items[$i]->group_clients_data = $group_clients_data;
                $i++;
            }
        } else {
            $group_clients_data = $this->getGroupClientsData($items->id);
            $items->group_clients_data = $group_clients_data;
            
            $secondary_trainers = $helper->get_client_trainers_names($user_id, 'secondary');
            $items->secondary_trainers = $secondary_trainers;
            $items->client_name = JFactory::getUser($user_id)->name;
        }

        return $items;

    }
    
    public function getGroupClientsData($item_id) {

        $query = "SELECT * FROM #__fitness_pr_temp_clients WHERE item_id='$item_id'";
        
        $clients = FitnessHelper::customQuery($query, 1);

        $data = array();
        $i = 0;
        foreach ($clients as $client) {
            $user = &JFactory::getUser($client->client_id);
             
            $data[$i]->id = $client->id;
            
            $data[$i]->client_id = $client->client_id;
            
            $data[$i]->client_name = $user->name;
                    
            $i++;
            
        }
       
        return $data;
    }
    

    public function copyProgramTemplate() {
        $status['success'] = 1;
        
        $db = JFactory::getDbo();
        
        $helper = new FitnessHelper();
        
        $data = json_decode(JRequest::getVar('data_encoded'));
        
        $id = $data->id;
        
        $user_id = JFactory::getUser()->id;

        //copy item
        $query = "SELECT * FROM #__fitness_programs_templates WHERE id='$id'";
        
        $item =  FitnessHelper::customQuery($query, 2);
        
        
        $created_by = $item->created_by;
        
        if($item->id) {
            $item->id = null;
            
            if($created_by != $user_id) {
                $item->trainer_id = $user_id;
            }
            
            $item->created_by = $user_id;

            $item->created = FitnessHelper::getDateCreated();
            $insert = $db->insertObject('#__fitness_programs_templates', $item, 'id');
            
            if (!$insert) {
                $status['success'] = 1;
                $status['message'] = $db->stderr();
            }

            $inserted_item_id = $db->insertid();
        }
        
        
        
        //copy exercises
        $query = "SELECT * FROM #__fitness_pr_temp_exercises WHERE item_id='$id'";

        $exercises =  FitnessHelper::customQuery($query, 1);

        foreach ($exercises as $exercise) {
            $exercise->id = null;
            $exercise->item_id = $inserted_item_id;
            
            $insert = $db->insertObject('#__fitness_pr_temp_exercises', $exercise, 'id');
            if (!$insert) {
                $status['success'] = 0;
                $status['message'] = $db->stderr();
            }
        }
        
        //copy clients
        if($created_by == $user_id) {
            $query = "SELECT * FROM #__fitness_pr_temp_clients WHERE item_id='$id'";

            $clients =  FitnessHelper::customQuery($query, 1);

            foreach ($clients as $client) {
                $client->id = null;
                $client->item_id = $inserted_item_id;
                $insert = $db->insertObject('#__fitness_pr_temp_clients', $client, 'id');
                if (!$insert) {
                    $status['success'] = 0;
                    $status['message'] = $db->stderr();
                }
            }
        }

      
        
        return array( 'status' => $status, 'data' => $inserted_item_id);
    }
    
    
    
    public function pr_temp_clients() {
            
        $method = JRequest::getVar('_method');

        if(!$method) {
            $method = $_SERVER['REQUEST_METHOD'];
        }

        $model = json_decode(JRequest::getVar('model'));
        
        $id = JRequest::getVar('id', 0, '', 'INT');
        
        $item_id = JRequest::getVar('item_id', 0, '', 'INT');

        $table = '#__fitness_pr_temp_clients';

        $helper = new FitnessHelper();

        switch ($method) {
            case 'GET': // Get Item(s)
                $query .= "SELECT  a.*, ";
                $query .= " (SELECT name FROM #__users WHERE id=a.client_id) name";
                $query .= "  FROM $table AS a";
                $query .= "  WHERE 1";
                
                if($item_id) {
                    $query .= " AND a.item_id='$item_id' ";
                }
                
                $data = FitnessHelper::customQuery($query, 1);
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
    
    public function pr_temp_exercises() {
            
        $method = JRequest::getVar('_method');

        if(!$method) {
            $method = $_SERVER['REQUEST_METHOD'];
        }

        $model = json_decode(JRequest::getVar('model'));
        
        $id = JRequest::getVar('id', 0, '', 'INT');
        
        $item_id = JRequest::getVar('item_id', 0, '', 'INT');

        $table = '#__fitness_pr_temp_exercises';

        $helper = new FitnessHelper();

        switch ($method) {
            case 'GET': // Get Item(s)
                $query .= "SELECT  a.* ";
                $query .= "  FROM $table AS a";
                $query .= "  WHERE 1";
                
                if($item_id) {
                    $query .= " AND a.item_id='$item_id' ";
                }
                
                $query .= "  ORDER BY a.order ASC";
                
                $data = FitnessHelper::customQuery($query, 1);
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
    
    public function import_pr_temp() {
        $data = json_decode(JRequest::getVar('data_encoded'));
        $id = $data->id;
        $item_id = $data->item_id;
        
        //copy item (description)
        $query = "SELECT * FROM #__fitness_programs_templates WHERE id='$id'";
        
        $item =  FitnessHelper::customQuery($query, 2);
        
        if($item->id) {
            $model = new stdClass();
            $model->id = $item_id;
            $model->description = $item->description;
            $helper = new FitnessHelper();
            $insert = $helper->insertUpdateObj($model, '#__dc_mv_events');
            
            if (!$insert) {
                $status['success'] = 0;
                $status['message'] = $db->stderr();
                return array( 'status' => $status);
            }
        }
        //
        
        //copy exercises
        $query = "SELECT a.* FROM #__fitness_pr_temp_exercises AS a  WHERE a.item_id='$id'";

        $exercises =  FitnessHelper::customQuery($query, 1);
        
        $db = JFactory::getDbo();
        $status['success'] = 1;
        foreach ($exercises as $exercise) {
            $exercise->id = null;
            $exercise->item_id = $item_id;
    
            $insert = $this->insertExercise($exercise, null, $exercise->item_id, '#__fitness_events_exercises');

            if (!$insert) {
                $status['success'] = 0;
                $status['message'] = $db->stderr();
                return array( 'status' => $status);
            }
        }
        return array( 'status' => $status);
    }
    
    public function insertExercise($model, $id, $item_id, $table) {
        $helper = new FitnessHelper();
        
        $query = "SELECT max(a.order) FROM $table AS a WHERE 1";
                
        if($id) {
            $query .= " AND  a.id='$id'";
        }

        if($item_id) {
            $query .= " AND  a.item_id='$item_id'";
        }

        $order = FitnessHelper::customQuery($query, 0);

        $model->order = (int)$order + 1;

        $id = $helper->insertUpdateObj($model, $table);
        
        return $id;
    }


}
