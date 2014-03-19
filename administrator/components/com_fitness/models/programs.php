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
        if (empty($config['filter_fields'])) {
            $config['filter_fields'] = array(
                                'id', 'a.id',
                'starttime', 'a.starttime',
                'client_id', 'a.client_id',
                'primary_trainer', 'a.trainer_id',
                'location', 'a.location',
                'category', 'a.title',
                'session_type', 'a.session_type',
                'session_focus', 'a.session_focus',
                'event_status', 'a.status',
                'frontend_published', 'a.frontend_published',
                'published', 'a.published',
                'calid', 'a.calid',
                'endtime', 'a.endtime',
                'description', 'a.description',
                'isalldayevent', 'a.isalldayevent',
                'color', 'a.color',
                'owner', 'a.owner',
                'rrule', 'a.rrule',
                'uid', 'a.uid',
                'exdate', 'a.exdate',
                'business_name', 'business_name',

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

        // Filter by published
        $published = $app->getUserStateFromRequest($this->context . '.filter.published', 'filter_published', '', 'string');
        $this->setState('filter.published', $published);
        
        // Filter by frontend published
        $frontend_published = $app->getUserStateFromRequest($this->context . '.filter.frontend_published', 'filter_frontend_published', '', 'string');
        $this->setState('filter.frontend_published', $frontend_published);

        //Filtering date
        $this->setState('filter.date.from', $app->getUserStateFromRequest($this->context.'.filter.date.from', 'filter_from_date', '', 'string'));
        $this->setState('filter.date.to', $app->getUserStateFromRequest($this->context.'.filter.date.to', 'filter_to_date', '', 'string'));

        // Filter by primary trainer
        $primary_trainer = $app->getUserStateFromRequest($this->context . '.filter.primary_trainer', 'filter_primary_trainer', '', 'string');
        $this->setState('filter.primary_trainer', $primary_trainer);
        
        // Filter by location
        $location = $app->getUserStateFromRequest($this->context . '.filter.location', 'filter_location', '', 'string');
        $this->setState('filter.location', trim($location));
        
        // Filter by category
        $category = $app->getUserStateFromRequest($this->context . '.filter.category', 'filter_category', '', 'string');
        $this->setState('filter.category', $category);

        // Filter by session type
        $session_type = $app->getUserStateFromRequest($this->context . '.filter.session_type', 'filter_session_type', '', 'string');
        $this->setState('filter.session_type', $session_type);
        
        // Filter by session focus
        $session_focus = $app->getUserStateFromRequest($this->context . '.filter.session_focus', 'filter_session_focus', '', 'string');
        $this->setState('filter.session_focus', $session_focus);
        
        // Filter by event status
        $event_status = $app->getUserStateFromRequest($this->context . '.filter.event_status', 'filter_event_status', '', 'string');
        $this->setState('filter.event_status', $event_status);
        
        // Filter by business profile
        $business_profile_id = $app->getUserStateFromRequest($this->context . '.filter.business_profile_id', 'filter_business_profile_id', '', 'string');
        $this->setState('filter.business_profile_id', $business_profile_id);
        
                 
        // Load the parameters.
        $params = JComponentHelper::getParams('com_fitness');
        $this->setState('params', $params);

        // List state information.
        parent::populateState('a.starttime', 'asc');
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
        $id.= ':' . $this->getState('filter.published');

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
                        'list.select', 'a.*, bp.name AS business_name'
                )
        );
        $query->from('`#__dc_mv_events` AS a');
        
        $query->leftJoin('#__users AS u ON u.id = a.client_id');
        
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
        
        // Filter by business profile
        $business_profile_id = $this->getState('filter.business_profile_id');
        if (is_numeric($business_profile_id)) {
            $query->where('c.business_profile_id = '.(int) $business_profile_id);
        } 

     

        // Filter by search in title
        $search = $this->getState('filter.search');
        if (!empty($search)) {
            if (stripos($search, 'id:') === 0) {
                $query->where('a.id = ' . (int) substr($search, 3));
            } else {
                $search = $db->Quote('%' . $db->escape($search, true) . '%');

                $query->where('
                    u.name LIKE ' . $search . '
                        or a.id IN
                        ( 
                            SELECT  DISTINCT event_id FROM #__fitness_appointment_clients WHERE client_id IN 
                                (
                                    SELECT id FROM #__users WHERE name LIKE ' . $search . '
                                )
                        ) 
                ');
            }
        }
        
        
        

        $query->where("a.title NOT IN ('5')"); //IN Assessment
         
        //Filtering date
        $filter_date_from = $this->state->get("filter.date.from");
        if ($filter_date_from) {
                $query->where("a.starttime >= '".$db->escape($filter_date_from)."'");
        }
        $filter_date_to = $this->state->get("filter.date.to");
        if ($filter_date_to) {
                $query->where("a.starttime <= '".$db->escape($filter_date_to)."'");
        }
        
        // Filter by primary trainer
        $primary_trainer = $this->getState('filter.primary_trainer');
        if (is_numeric($primary_trainer)) {
            $query->where('a.trainer_id = '.(int) $primary_trainer);
        } 
        
                
        // Filter by location
        $location = $this->getState('filter.location');
        if ($location) {
           $query->where("a.location = ".$db->Quote($location));
        } 
        
        // Filter by category
        $category= $this->getState('filter.category');
        if ($category) {
           $query->where("a.title = ".$db->Quote($category));
        } 
          
        
        // Filter by session type
        $session_type = $this->getState('filter.session_type');
        if ($session_type) {
           $query->where("a.session_type = ".$db->Quote($session_type));
        } 
             
        // Filter by session focus
        $session_focus = $this->getState('filter.session_focus');
        if ($session_focus) {
           $query->where("a.session_focus = ".$db->Quote($session_focus));
        } 
        
        
        // Filter by event status
        $event_status = $this->getState('filter.event_status');
        if (is_numeric($event_status)) {
            $query->where('a.status = '.(int) $event_status);
        } 
        
        // Filter by event published
        $published = $this->getState('filter.published');
        if (is_numeric($published)) {
            $query->where('a.published = '.(int) $published);
        } 
        
        
        // Filter by event frontend published
        $frontend_published = $this->getState('filter.frontend_published');
        if (is_numeric($frontend_published)) {
            $query->where('a.frontend_published = '.(int) $frontend_published);
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
    

    function status_html($item_id, $status, $button_class) {
        switch($status) {
            case '1' :
                $class = 'event_status_pending';
                $text = 'PENDING';
                break;
            case '2' :
                $class = 'event_status_attended';
                $text = 'ATTENDED';
                break;
            case '3' :
                $class = 'event_status_cancelled';
                $text = 'CANCELLED';
                break;
            case '4' :
                $class = 'event_status_latecancel';
                $text = 'LATE CANCEL';
                break;
            case '5' :
                $class = 'event_status_noshow';
                $text = 'NO SHOW';
                break;
            case '6' :
                $class = 'event_status_complete';
                $text = 'COMPLETE';
                break;
            default :
                $class = 'event_status_pending';
                $text = 'PENDING';
                break;
        }

        $html = '<a href="javascript:void(0)" data-item_id="' . $item_id . '" data-status_id="' . $status . '" class="' . $button_class . ' ' . $class . '">' . $text . '</a>';

        return $html;
    }
    
    
    
    public function programs() {
            
        $method = JRequest::getVar('_method');

        if(!$method) {
            $method = $_SERVER['REQUEST_METHOD'];
        }

        $model = json_decode(JRequest::getVar('model'));
        
        $id = JRequest::getVar('id', 0, '', 'INT');

        $table = '#__dc_mv_events';

        $helper = new FitnessHelper();

        switch ($method) {
            case 'GET': // Get Item(s)
                $data = new stdClass();
                $data->id = $id;  
                $data->title = JRequest::getVar('title'); 
                $data->sort_by = JRequest::getVar('sort_by'); 
                $data->order_dirrection = JRequest::getVar('order_dirrection'); 
                $data->page = JRequest::getVar('page'); 
                $data->limit = JRequest::getVar('limit'); 
                $data->published = JRequest::getVar('published'); 
                $data->frontend_published = JRequest::getVar('frontend_published'); 
                
                $data->title = JRequest::getVar('title', '0'); 
                $data->location = JRequest::getVar('location', '0'); 
                $data->session_type = JRequest::getVar('session_type', '0'); 
                $data->session_focus = JRequest::getVar('session_focus', '0'); 
                $data->status = JRequest::getVar('status', '0'); 
                
                $data->date_from = JRequest::getVar('date_from', '0'); 
                $data->date_to = JRequest::getVar('date_to', '0'); 
                $data->client_name = JRequest::getVar('client_name'); 
                $data->trainer_name = JRequest::getVar('trainer_name'); 
                $data->created_by_name = JRequest::getVar('created_by_name'); 
                

                $data->business_profile_id = JRequest::getVar('business_profile_id'); 
                $data->current_page = JRequest::getVar('current_page'); 

                $data = $this->getPrograms($table, $data);
                
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
            //5
            if($data->status) {
                $query .= " AND a.status IN ($data->status)";
            }

            if (!empty($data->date_from)) {
                $query .= " AND a.starttime >= '$data->date_from'";
            }

            if (!empty($data->date_to)) {
                $query .= " AND a.endtime <= '$data->date_to'";
            }

            //search by client name
            if (!empty($data->client_name)) {
                $sql = " SELECT GROUP_CONCAT(id) FROM #__users WHERE name LIKE '%$data->client_name%' ";

                $client_ids = FitnessHelper::customQuery($sql, 0);

                if($client_ids) {
                    $query .= " AND (a.client_id IN ($client_ids) OR a.id IN (SELECT  DISTINCT event_id FROM #__fitness_appointment_clients WHERE client_id IN ($client_ids)))";
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

            $query .= " ) items_total, ";
        }
        //end get total number
        
        $query .= " (SELECT name FROM #__users WHERE id=a.trainer_id) trainer_name, ";
        
        $query .= " (SELECT name FROM #__users WHERE id=a.owner) created_by_name, ";
        
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
        //5
        if($data->status) {
            $query .= " AND a.status IN ($data->status)";
        }
        
        
        if (!empty($data->date_from)) {
            $query .= " AND a.starttime >= '$data->date_from'";
        }
        
        if (!empty($data->date_to)) {
            $query .= " AND a.endtime <= '$data->date_to'";
        }
        
        //search by client name
        if (!empty($data->client_name)) {
            $sql = " SELECT GROUP_CONCAT(id) FROM #__users WHERE name LIKE '%$data->client_name%' ";

            $client_ids = FitnessHelper::customQuery($sql, 0);

            if($client_ids) {
                $query .= " AND (a.client_id IN ($client_ids) OR a.id IN (SELECT  DISTINCT event_id FROM #__fitness_appointment_clients WHERE client_id IN ($client_ids)))";
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
                $group_clients_data = $this->getGroupClientsData($item->id, $item->client_id);
                $items[$i]->group_clients_data = $group_clients_data;
                $i++;
            }
        } else {
            $group_clients_data = $this->getGroupClientsData($items->id, $items->client_id);
            $items->group_clients_data = $group_clients_data;
        }

        return $items;

    }
    
    
    public function getGroupClientsData($event_id, $client_id) {

        $query = "SELECT client_id FROM #__fitness_appointment_clients WHERE event_id='$event_id'";
        
        $clients = FitnessHelper::customQuery($query, 3);

        if($client_id) {
            $clients = array_merge($clients, array($client_id));
        }
        
        $clients = array_unique($clients);

        $data = array();
        $i = 0;
        foreach ($clients as $client) {
            $user = &JFactory::getUser($client);
            
            $sentConfirmEmailData = $this->getSentConfirmEmailData($event_id, $client);
            
            $data[$i]->client_name = $user->name;
            
            $data[$i]->sent = $sentConfirmEmailData->sent;
            
            $data[$i]->confirmed = $sentConfirmEmailData->confirmed;
                    
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
        
        //copy event
        $query = "SELECT * FROM #__dc_mv_events WHERE id='$id'";
        
        $event =  FitnessHelper::customQuery($query, 2);
        
        if($event->id) {
            $event->id = null;
            $event->status = 1;
            $event->owner = JFactory::getUser()->id;
            $insert = $db->insertObject('#__dc_mv_events', $event, 'id');
            
            if (!$insert) {
                $status['success'] = 1;
                $status['message'] = $db->stderr();
            }

            $inserted_event_id = $db->insertid();
        }
        
        
        
        //copy exercises
        $query = "SELECT * FROM #__fitness_events_exercises WHERE event_id='$id'";

        $exercises =  FitnessHelper::customQuery($query, 1);

        foreach ($exercises as $exercise) {
            $exercise->id = null;
            $exercise->event_id = $inserted_event_id;
            $insert = $db->insertObject('#__fitness_events_exercises', $exercise, 'id');
            if (!$insert) {
                $status['success'] = 1;
                $status['message'] = $db->stderr();
            }
        }
        
        //copy clients
        $query = "SELECT * FROM #__fitness_appointment_clients WHERE event_id='$id'";

        $clients =  FitnessHelper::customQuery($query, 1);

        foreach ($clients as $client) {
            $client->id = null;
            $client->event_id = $inserted_event_id;
            $insert = $db->insertObject('#__fitness_appointment_clients', $client, 'id');
            if (!$insert) {
                $status['success'] = 1;
                $status['message'] = $db->stderr();
            }
        }
        
        return array( 'status' => $status);
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
                $query .= "  FROM #__fitness_appointment_clients AS a";
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

}
