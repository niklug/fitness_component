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
class FitnessModelprograms extends JModelList {

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
    
    public function programs() {
            
        $method = JRequest::getVar('_method');

        if(!$method) {
            $method = $_SERVER['REQUEST_METHOD'];
        }

        
        $model = json_decode(JRequest::getVar('model','','','',JREQUEST_ALLOWHTML));
         

        $id = JRequest::getVar('id', 0, '', 'INT');
        
        $table = '#__dc_mv_events';

        $helper = new FitnessHelper();

        switch ($method) {
            case 'GET': // Get Item(s)
                $data = new stdClass();
                $data->id = $id;  
                $data->sort_by = JRequest::getVar('sort_by'); 
                $data->order_dirrection = JRequest::getVar('order_dirrection'); 
                $data->page = JRequest::getVar('page'); 
                $data->limit = JRequest::getVar('limit'); 
                $data->published = JRequest::getVar('published'); 
                $data->frontend_published = JRequest::getVar('frontend_published'); 
                
                $data->title = JRequest::getVar('title'); 
                $data->location = JRequest::getVar('location'); 
                $data->session_type = JRequest::getVar('session_type'); 
                $data->session_focus = JRequest::getVar('session_focus'); 
                
                $data->date_from = JRequest::getVar('date_from', '0'); 
                $data->date_to = JRequest::getVar('date_to', '0'); 
                $data->client_id = JRequest::getVar('client_id'); 
                $data->client_name = JRequest::getVar('client_name'); 
                $data->trainer_name = JRequest::getVar('trainer_name'); 
                $data->created_by_name = JRequest::getVar('created_by_name'); 
                

                $data->business_profile_id = JRequest::getVar('business_profile_id'); 
                $data->current_page = JRequest::getVar('current_page'); 
                
                $data->appointment_types = JRequest::getVar('appointment_types'); 

                $data = $this->getPrograms($table, $data);
                
                return $data;
                break;
            case 'PUT': 
                //update
                $id = $helper->insertUpdateObj($model, $table);
                break;
            case 'POST': // Create
                $id = $helper->insertUpdateObj($model, $table);
                
                if($model->client_id) {
                    $table = '#__fitness_appointment_clients';
                    $data = new stdClass();
                    $data->event_id = $id;
                    $data->client_id = $model->client_id;
                    $helper->insertUpdateObj($data, $table);
                }
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
    
    
    
    public function getPrograms($table, $data) {
        
        $helper = new FitnessHelper();
        
        $table = '#__dc_mv_events';
        
        $page = $data->page;
        
        $limit = $data->limit;
        
        $start = ($page - 1) * $limit;
        
        $sort_by = $data->sort_by;
        
        $order_dirrection = $data->order_dirrection;
        
        $id = $data->id;
        
        $user_id = JFactory::getUser()->id;

        $query = " SELECT a.*,";
        
        $query .= " (SELECT user_id  FROM #__fitness_clients WHERE user_id=a.owner LIMIT 1) created_by_client,";
        
        $query .= " t.name AS appointment_name,";
        
        $query .= " l.name AS location_name,";
        
        $query .= " st.name AS session_type_name,";
        
        $query .= " sf.name AS session_focus_name,";
        
        
        //get total number
        if(!$id) {
            $query .= " (SELECT COUNT(*) FROM $table AS a ";

            $query .= " WHERE 1 ";

            if(isset($data->published) AND $data->published != '*') {
                $query .= " AND a.published='$data->published' ";
            }


            //1
            if($data->title) {
                $query .= " AND a.title IN ($data->title)";
            }
            //2
            if($data->location) {
                $query .= " AND a.location IN ($data->location)";
            }
            //3
            if($data->session_type) {
                $query .= " AND a.session_type IN ($data->session_type)";
            }
            //4
            if($data->session_focus) {
                $query .= " AND a.session_focus IN ($data->session_focus)";
            }

            if (!empty($data->date_from)) {
                $query .= " AND a.starttime >= '$data->date_from'";
            }

            if (!empty($data->date_to)) {
                $query .= " AND a.endtime <= '$data->date_to'";
            }
            
            if($data->client_id) {
                $query .= " AND a.id IN (SELECT  event_id FROM #__fitness_appointment_clients WHERE client_id IN ($data->client_id))";
            }

            //search by client name
            if (!empty($data->client_name)) {
                $sql = " SELECT GROUP_CONCAT(id) FROM #__users WHERE name LIKE '%$data->client_name%' ";

                $client_ids = FitnessHelper::customQuery($sql, 0);

                if($client_ids) {
                    $query .= " AND a.id IN (SELECT  DISTINCT event_id FROM #__fitness_appointment_clients WHERE client_id IN ($client_ids))";
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

            //search by created_by name
            if (!empty($data->created_by_name)) {
                $sql = " SELECT GROUP_CONCAT(id) FROM #__users WHERE name LIKE '%$data->created_by_name%' ";

                $owner_ids = FitnessHelper::customQuery($sql, 0);

                if($owner_ids) {
                    $query .= " AND a.owner IN ($owner_ids)";
                }
            }

            //filter by business profile
            if (!empty($data->business_profile_id)) {
                $query .= " AND a.business_profile_id IN ($data->business_profile_id)";
            }

            if (isset($data->frontend_published)  AND $data->frontend_published != '2') {
                $query .= " AND a.frontend_published IN ('$data->frontend_published')";
            }
            
            //frontend client logged
            if($data->current_page == 'my_workouts' AND FitnessHelper::is_client($user_id)) {
                $query .= " AND a.owner='$user_id'";
            }
            
            if(($data->current_page == 'workout_programs' OR $data->current_page == 'assessments' OR $data->current_page == 'self_assessments') AND FitnessHelper::is_client($user_id)) {
                $query .= " AND a.id IN (SELECT event_id FROM #__fitness_appointment_clients WHERE event_id=a.id AND client_id='$user_id')";
                $query .= " AND a.owner NOT IN ('$user_id')";
            }
            
            if($data->current_page == 'assessments') {
                $trainer_assessment = FitnessHelper::TRAINER_ASSESSMENT;
                $query .= " AND a.session_type='$trainer_assessment'";
            }
            
            if($data->current_page == 'self_assessments') {
                $self_assessment = FitnessHelper::SELF_ASSESSMENT;
                $query .= " AND a.session_type='$self_assessment'";
            }
            
            if ($data->current_page  == 'my_favourites') {
                $query .= " AND a.id IN (SELECT item_id FROM #__fitness_appointments_favourites WHERE client_id='$user_id')";
            }
            
            if($data->appointment_types) {
                $query .= " AND  a.title IN ($data->appointment_types)";
            }
        
            $query .= " ) items_total, ";
        }
        //end get total number
        
        $query .= " (SELECT name FROM #__users WHERE id=a.trainer_id) trainer_name, ";
        
        $query .= " (SELECT name FROM #__users WHERE id=a.owner) created_by_name, ";
        
        //frontend client logged
        if(FitnessHelper::is_client($user_id)) {
            $query .= " (SELECT id FROM #__fitness_appointment_clients WHERE event_id=a.id AND client_id='$user_id' LIMIT 1) client_item_id,";
            $query .= " (SELECT status FROM #__fitness_appointment_clients WHERE event_id=a.id AND client_id='$user_id' LIMIT 1) status,";
        }
        
        $query .= " (SELECT id FROM #__fitness_appointments_favourites WHERE item_id=a.id AND client_id='$user_id') is_favourite, "; 
        
        // if logged simple trainer associated to the event's clients 
        $query .= "  (SELECT GROUP_CONCAT(id) FROM #__fitness_appointment_clients WHERE client_id IN  (SELECT user_id FROM #__fitness_clients WHERE (primary_trainer='$user_id' OR FIND_IN_SET('$user_id', other_trainers)) AND state='1') AND event_id=a.id) is_associated_trainer";
        
        
        
        $query .= "  FROM $table AS a";
        
        
        $query .= " LEFT JOIN #__fitness_categories AS t ON t.id = a.title ";
        
        $query .= " LEFT JOIN #__fitness_locations AS l ON l.id = a.location ";
        
        $query .= " LEFT JOIN #__fitness_session_type AS st ON st.id = a.session_type ";
        
        $query .= " LEFT JOIN #__fitness_session_focus AS sf ON sf.id = a.session_focus ";
        
        $query .= " WHERE 1 ";

        if(isset($data->published) AND $data->published != '*') {
            $query .= " AND a.published='$data->published' ";
        }
        

        //1
        if($data->title) {
            $query .= " AND a.title IN ($data->title)";
        }
        //2
        if($data->location) {
            $query .= " AND a.location IN ($data->location)";
        }
        //3
        if($data->session_type) {
            $query .= " AND a.session_type IN ($data->session_type)";
        }
        //4
        if($data->session_focus) {
            $query .= " AND a.session_focus IN ($data->session_focus)";
        }
        
      
        if (!empty($data->date_from)) {
            $query .= " AND a.starttime >= '$data->date_from'";
        }
        
        if (!empty($data->date_to)) {
            $query .= " AND a.endtime <= '$data->date_to'";
        }
        
        if($data->client_id) {
            $query .= " AND a.id IN (SELECT  event_id FROM #__fitness_appointment_clients WHERE client_id IN ($data->client_id))";
        }
        
        //search by client name
        if (!empty($data->client_name)) {
            $sql = " SELECT GROUP_CONCAT(id) FROM #__users WHERE name LIKE '%$data->client_name%' ";

            $client_ids = FitnessHelper::customQuery($sql, 0);

            if($client_ids) {
                $query .= " AND a.id IN (SELECT  DISTINCT event_id FROM #__fitness_appointment_clients WHERE client_id IN ($client_ids))";
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
        
        //search by created_by name
        if (!empty($data->created_by_name)) {
            $sql = " SELECT GROUP_CONCAT(id) FROM #__users WHERE name LIKE '%$data->created_by_name%' ";

            $owner_ids = FitnessHelper::customQuery($sql, 0);

            if($owner_ids) {
                $query .= " AND a.owner IN ($owner_ids)";
            }
        }
        
        
        //filter by business profile
        if (!empty($data->business_profile_id)) {
            $query .= " AND a.business_profile_id IN ($data->business_profile_id)";
        }
        

        if (isset($data->frontend_published) AND $data->frontend_published != '2') {
            $query .= " AND a.frontend_published IN ('$data->frontend_published')";
        }
        
        //frontend client logged
        if($data->current_page == 'my_workouts' AND FitnessHelper::is_client($user_id)) {
            $query .= " AND a.owner='$user_id'";
        }
        
        if(($data->current_page == 'workout_programs' OR $data->current_page == 'assessments' OR $data->current_page == 'self_assessments') AND FitnessHelper::is_client($user_id)) {
            $query .= " AND a.id IN (SELECT event_id FROM #__fitness_appointment_clients WHERE event_id=a.id AND client_id='$user_id')";
            $query .= " AND a.owner NOT IN ('$user_id')";
        }
        
        if($data->current_page == 'assessments') {
            $trainer_assessment = FitnessHelper::TRAINER_ASSESSMENT;
            $query .= " AND a.session_type='$trainer_assessment'";
        }
        
        if($data->current_page == 'self_assessments') {
            $self_assessment = FitnessHelper::SELF_ASSESSMENT;
            $query .= " AND a.session_type='$self_assessment'";
        }
        
        if ($data->current_page  == 'my_favourites') {
            $query .= " AND a.id IN (SELECT item_id FROM #__fitness_appointments_favourites WHERE client_id='$user_id')";
        }


        if($data->appointment_types) {
            $query .= " AND  a.title IN ($data->appointment_types)";
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
    
    
    public function getGroupClientsData($event_id) {

        $query = "SELECT * FROM #__fitness_appointment_clients WHERE event_id='$event_id'";
        
        $clients = FitnessHelper::customQuery($query, 1);

        $data = array();
        $i = 0;
        foreach ($clients as $client) {
            $user = &JFactory::getUser($client->client_id);
            
            $sentConfirmEmailData = $this->getSentConfirmEmailData($event_id, $client->client_id);
            
            $data[$i]->id = $client->id;
            
            $data[$i]->client_id = $client->client_id;
            
            $data[$i]->client_name = $user->name;
            
            $data[$i]->sent = $sentConfirmEmailData->sent;
            
            $data[$i]->confirmed = $sentConfirmEmailData->confirmed;
            
            $data[$i]->status = $client->status;
                    
            $i++;
            
        }
       
        return $data;
    }
    
    
    public function getSentConfirmEmailData($event_id, $client_id) {

        $query = "SELECT sent, confirmed
            FROM  #__fitness_email_reminder
            WHERE event_id='$event_id'
            AND client_id='$client_id'
            LIMIT 1
         ";
        
        return FitnessHelper::customQuery($query, 2);
    }
    
    public function copyEvent() {
        $status['success'] = 1;
        
        $db = JFactory::getDbo();
        
        $helper = new FitnessHelper();
        
        $data = json_decode(JRequest::getVar('data_encoded'));
        
        $id = $data->id;
        
        $client_id = $data->client_id;
        //copy event
        $query = "SELECT * FROM #__dc_mv_events WHERE id='$id'";
        
        $event =  FitnessHelper::customQuery($query, 2);
        
        if($event->id) {
            $event->id = null;
            $user_id = JFactory::getUser()->id;
            if($user_id) {
                $event->owner = $user_id;
            }
            $insert = $db->insertObject('#__dc_mv_events', $event, 'id');
            
            if (!$insert) {
                $status['success'] = 1;
                $status['message'] = $db->stderr();
            }

            $inserted_event_id = $db->insertid();
        }
        
        
        
        //copy exercises
        $query = "SELECT * FROM #__fitness_events_exercises WHERE item_id='$id'";

        $exercises =  FitnessHelper::customQuery($query, 1);

        foreach ($exercises as $exercise) {
            $exercise->id = null;
            $exercise->item_id = $inserted_event_id;
            
            $insert = $db->insertObject('#__fitness_events_exercises', $exercise, 'id');
            if (!$insert) {
                $status['success'] = 1;
                $status['message'] = $db->stderr();
            }
        }
        
        //copy clients
        // from admin
        if(!$client_id) {
            $query = "SELECT * FROM #__fitness_appointment_clients WHERE event_id='$id'";

            $clients =  FitnessHelper::customQuery($query, 1);

            foreach ($clients as $client) {
                $client->id = null;
                $client->event_id = $inserted_event_id;
                $client->status = '1';
                $insert = $db->insertObject('#__fitness_appointment_clients', $client, 'id');
                if (!$insert) {
                    $status['success'] = 1;
                    $status['message'] = $db->stderr();
                }
            }
        // copy by client from frontend
        } else {
            $client->id = null;
            $client->event_id = $inserted_event_id;
            $client->client_id = $client_id;
            $client->status = '1';
            $insert = $db->insertObject('#__fitness_appointment_clients', $client, 'id');
            if (!$insert) {
                $status['success'] = 1;
                $status['message'] = $db->stderr();
            }
        }
        
        return array( 'status' => $status, 'data' => $inserted_event_id);
    }
    
    
    
    public function event_clients() {
            
        $method = JRequest::getVar('_method');

        if(!$method) {
            $method = $_SERVER['REQUEST_METHOD'];
        }

        $model = json_decode(JRequest::getVar('model'));
        
        $id = JRequest::getVar('id', 0, '', 'INT');
        
        $event_id = JRequest::getVar('event_id', 0, '', 'INT');

        $table = '#__fitness_appointment_clients';

        $helper = new FitnessHelper();

        switch ($method) {
            case 'GET': // Get Item(s)
                $query .= "SELECT  a.*, ";
                $query .= " (SELECT name FROM #__users WHERE id=a.client_id) name";
                $query .= "  FROM $table AS a";
                $query .= "  WHERE 1";
                
                if($event_id) {
                    $query .= " AND a.event_id='$event_id' ";
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
    
    public function event_exercises() {
            
        $method = JRequest::getVar('_method');

        if(!$method) {
            $method = $_SERVER['REQUEST_METHOD'];
        }

        $model = json_decode(JRequest::getVar('model'));
        
        $id = JRequest::getVar('id', 0, '', 'INT');
        
        $item_id = JRequest::getVar('item_id', 0, '', 'INT');

        $table = '#__fitness_events_exercises';

        $helper = new FitnessHelper();

        switch ($method) {
            case 'GET': // Get Item(s)
                return $this->getExercises(null, $item_id, $table) ;
                break;
            case 'PUT': 
                //update
                $id = $helper->insertUpdateObj($model, $table);
                break;
            case 'POST': // Create
                
                $id = $this->insertExercise($model, null, $model->item_id, $table);
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
    
    public function getExercises($id, $item_id, $table) {
        
        $query .= "SELECT  a.* ";
        $query .= "  FROM $table AS a";
        $query .= "  WHERE 1";
        
        if($id) {
            $query .= " AND a.id='$id' ";
        }

        if($item_id) {
            $query .= " AND a.item_id='$item_id' ";
        }

        $query .= "  ORDER BY a.order ASC";
        
        $type = 1;
        
        if($id) {
            $type = 2;
        }

        return FitnessHelper::customQuery($query, $type);
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
    
    public function copyProgramExercises() {
        $status['success'] = 1;
        
        $data = json_decode(JRequest::getVar('data_encoded'));
        
        $table = $data->db_table;
        
        $item_id = $data->item_id;
        
        $items = explode(",", $data->items);
       
        foreach ($items as $id) {
            
            $exercise  = $this->getExercises($id, null, $table);
            
            $exercise->id = null;
            $this->insertExercise($exercise, null, $item_id, $table);
        }
       
        return array( 'status' => $status, 'data' => $items);
        
    }
    
    public function rest_data() {
            
        $method = JRequest::getVar('_method');

        if(!$method) {
            $method = $_SERVER['REQUEST_METHOD'];
        }

        $model = json_decode(JRequest::getVar('model'));
        
        $id = JRequest::getVar('id', 0, '', 'INT');
        
        $item_id = JRequest::getVar('item_id', 0, '', 'INT');
  
        
        $table = JRequest::getVar('db_table');
        
        if(!$table) {
            $table = $model->db_table;
        }
        
        if(!$table) {
            throw new Exception('Error: no db_table');
        }


        $helper = new FitnessHelper();

        switch ($method) {
            case 'GET': // Get Item(s)
                $query = "SELECT a.* FROM $table AS a WHERE 1";
                
                if($id) {
                    $query .= " AND a.id='$id'";
                }
                
                if($item_id) {
                    $query .= " AND a.item_id='$item_id'";
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

    public function saveAsTemplate() {
        $status['success'] = 1;
        
        $db = JFactory::getDbo();
        
        $helper = new FitnessHelper();
        
        $data = json_decode(JRequest::getVar('data_encoded'));
        
        $id = $data->id;
 
        //copy event
        $query = "SELECT * FROM #__dc_mv_events WHERE id='$id'";
        
        $event =  FitnessHelper::customQuery($query, 2);
        
        if($event->id) {
            $event->id = null;
            $event->appointment_id = $event->title;
            $event->created_by = JFactory::getUser()->id;
            $event->name = '';
            $event->created = FitnessHelper::getDateCreated();
            
            $insert = $helper->insertUpdateObj($event, '#__fitness_programs_templates');
            
            if (!$insert) {
                $status['success'] = 0;
                $status['message'] = $db->stderr();
            }

            $inserted_event_id = $db->insertid();
        }
        
        
        
        //copy exercises
        $query = "SELECT * FROM #__fitness_events_exercises WHERE item_id='$id'";

        $exercises =  FitnessHelper::customQuery($query, 1);

        foreach ($exercises as $exercise) {
            $exercise->id = null;
            $exercise->item_id = $inserted_event_id;
            
            $insert = $db->insertObject('#__fitness_pr_temp_exercises', $exercise, 'id');
            if (!$insert) {
                $status['success'] = 0;
                $status['message'] = $db->stderr();
            }
        }
        
        //copy clients

        $query = "SELECT * FROM #__fitness_appointment_clients WHERE event_id='$id'";

        $clients =  FitnessHelper::customQuery($query, 1);
        
        

        foreach ($clients as $client) {
            $client->id = null;
            $client->item_id = $inserted_event_id;
            $client->status = '1';
            $insert = $helper->insertUpdateObj($client, '#__fitness_pr_temp_clients');
            if (!$insert) {
                $status['success'] = 0;
                $status['message'] = $db->stderr();
            }
        }

        return array( 'status' => $status, 'data' => $inserted_event_id);
    }

}
