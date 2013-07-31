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
                'start_date', 'a.start_date',
                'details', 'a.details',
                'comments', 'a.comments',
                'completed', 'a.completed',
                'state', 'a.state',
                'created', 'a.created',
                'modified', 'a.modified',
                'u.name', 'gc.name', 'gf.name'

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
        $this->setState('filter.start_date.from', $app->getUserStateFromRequest($this->context.'.filter.start_date.from', 'filter_from_start_date', '', 'string'));
        $this->setState('filter.start_date.to', $app->getUserStateFromRequest($this->context.'.filter.start_date.to', 'filter_to_start_date', '', 'string'));
        
        
        //Filtering deadline
        $this->setState('filter.deadline.from', $app->getUserStateFromRequest($this->context.'.filter.deadline.from', 'filter_from_deadline', '', 'string'));
        $this->setState('filter.deadline.to', $app->getUserStateFromRequest($this->context.'.filter.deadline.to', 'filter_to_deadline', '', 'string'));
        
        // Filter by goal category
        $goal_category = $app->getUserStateFromRequest($this->context . '.filter.goal_category', 'filter_goal_category', '', 'string');
        $this->setState('filter.goal_category', $goal_category);
        
       // Filter by goal focus
        $training_period = $app->getUserStateFromRequest($this->context . '.filter.training_period', 'filter_training_period', '', 'string');
        $this->setState('filter.training_period', $training_period);
                
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
        $id.= ':' . $this->getState('filter.training_period');
        $id.= ':' . $this->getState('filter.goal_category');
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
                        'list.select', 'a.*,  ug.title as usergroup, gc.name as goal_category_name, gf.name as training_period'
                )
        );
        $query->from('`#__fitness_goals` AS a');
                
        $query->leftJoin('#__users AS u ON u.id = a.user_id');
        
  
        $query->leftJoin('#__user_usergroup_map AS g ON u.id = g.user_id');
        
        $query->leftJoin('#__usergroups AS ug ON ug.id = g.group_id');
        
        $query->leftJoin('#__fitness_goal_categories AS gc ON gc.id = a.goal_category_id');
        $query->leftJoin('#__fitness_training_period AS gf ON gf.id = a.training_period_id');

        
        // filter only for Super Users
        $user = &JFactory::getUser();
        if ($this->getUserGroup($user->id) != 'Super Users') {

            $other_trainers = $db->Quote('%' . $db->escape($user->id, true) . '%');
            $query->where('a.user_id IN (SELECT DISTINCT user_id FROM #__fitness_clients WHERE primary_trainer=' .  (int) $user->id .  ' OR other_trainers LIKE ' . $other_trainers .  ' )');
        }

        
        // Filter by published state
        $published = $this->getState('filter.state');
        if (is_numeric($published)) {
            $query->where('a.state = '.(int) $published);
        } else if ($published === '') {
            $query->where('(a.state IN (0, 1))');
        }

    
    
        // Filter by goal category
        $goal_category = $this->getState('filter.goal_category');
        if (is_numeric($goal_category)) {
            $query->where('gc.id = '.(int) $goal_category);
        } 
        
        // Filter by goal focus
        $training_period = $this->getState('filter.training_period');
        if (is_numeric($training_period)) {
            $query->where('gf.id = '.(int) $training_period);
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
                    OR  gc.name LIKE '.$search.' 
                    OR  gf.name LIKE '.$search.' 
                   
                    OR  u.username LIKE '.$search.' 
                    OR  u.name LIKE '.$search.' 
                             
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
    
    /**
     * 
     * @param type $goal_id
     * @param type $goal_status_id
     * @param type $user_id
     * @return type
     */
    public function setGoalStatus($goal_id, $goal_status_id, $goal_type) {
        // $goal_type: 1-> Primary Goal; 2 -> Mini Goal
        $ret['IsSuccess'] = true;
        $table = '#__fitness_goals';

        if($goal_type == '2') $table = '#__fitness_mini_goals';

        $db = &JFactory::getDBo();
        $query = "UPDATE $table SET completed='$goal_status_id' WHERE id='$goal_id'";
        if (!$db->query()) {
            $ret['IsSuccess'] = false;
            $ret['Msg'] = $db->stderr();
        }
        $db->setQuery($query);
        $db->query();
        
        $ret['Msg'] = $goal_status_id;
        $ret = json_encode($ret);

        return $ret;
    }
    
    public function sendGoalEmail($goal_id, $goal_status_id, $user_id) {
        $goal = $this->getGoal($goal_id);
        $trainer = JFactory::getUser($goal->primary_trainer);
        return $this->sendEmail($trainer->email, 'Goal email', 'Test');
    }
    
    
    
    private function sendEmail($recipient, $Subject, $body) {
        
        $mailer = & JFactory::getMailer();

        $config = new JConfig();

        $sender = array($config->mailfrom, $config->fromname);

        $mailer->setSender($sender);

        //$recipient = 'npkorban@mail.ru';

        $mailer->addRecipient($recipient);

        $mailer->setSubject($Subject);

        $mailer->isHTML(true);

        $mailer->setBody($body);

        $send = & $mailer->Send();
        
        if ($send == '1') {
            return 'Email  sent';
        } else {
            return $send;
        }
    }
    
    
    public function getGoal($goal_id) {
        $db = &JFactory::getDBo();
        $query = "SELECT * FROM #__fitness_goals WHERE id='$goal_id'";
        $db->setQuery($query);
        $result = $db->loadObject();
        return $result;
    }
    
    
    
    
    // clients view
    public function getClientsByGroup($user_group) {
        $db = &JFactory::getDBo();
        $query = "SELECT u.id FROM #__users AS u 
            INNER JOIN #__user_usergroup_map AS g ON g.user_id=u.id WHERE g.group_id='$user_group'";
        $db->setQuery($query);
        $status['success'] = 1;
        if (!$db->query()) {
            $status['success'] = 0;
            $status['message'] = $db->stderr();
        }

        $clients= $db->loadResultArray(0);

        if(!$clients) {
            $status['success'] = 0;
            $status['message'] = 'No clients assigned to this usergroup.';
        }


        foreach ($clients as $user_id) {
            $user = &JFactory::getUser($user_id);
            $clients_name[] = $user->name;
        }

        $result = array( 'status' => $status, 'data' => array_combine($clients, $clients_name));
        return  json_encode($result);

    }
    
    function getUserGroup($user_id) {
        $db = JFactory::getDBO();
        $query = "SELECT title FROM #__usergroups WHERE id IN 
            (SELECT group_id FROM #__user_usergroup_map WHERE user_id='$user_id')";
        $db->setQuery($query);
        return $db->loadResult();
    }
    
    
    
    // programs view
    function setFrontendPublished($event_id, $status) {
        if($status) {
            $status = '0';
        } else {
            $status = '1';
        }

        $db = &JFactory::getDBo();
        $query = "UPDATE #__dc_mv_events SET frontend_published='$status' WHERE id='$event_id'";
        $db->setQuery($query);
        $ret['success'] = 1;
        if (!$db->query()) {
            $ret['success'] = 0;
            $ret['message'] = $db->stderr();
        }
        $result = array( 'status' => $ret, 'data' => array('event_id' => $event_id, 'status' => $status));
        return  json_encode($result);
    }
    
    
    
    /**
     * 
     * @param type $client_id
     * @return type
     */
    function getGraphData($client_id) {

        // primary goals
        $primary_goals = $this->getPrimaryGoalsGraphData($client_id);
        if($primary_goals['status']['success'] == false) {
            $ret['success'] = 0;
            $ret['message'] = $primary_goals['status']['message'];
            return  json_encode(array('status' => $ret));
        }
        
        //mini goals
        $mini_goals = $this->getMiniGoalsGraphData($client_id);
        if($mini_goals['status']['success'] == false) {
            $ret['success'] = 0;
            $ret['message'] = $mini_goals['status']['message'];
            return  json_encode(array('status' => $ret));
        }
        
        
        // appointment data
        $personal_training = $this->getAppointmentsGraphData($client_id, 'Personal Training');
        if($personal_training['status']['success'] == false) {
            $ret['success'] = 0;
            $ret['message'] = $appointments_data['status']['message'];
            return  json_encode(array('status' => $ret));
        }
        
        // Semi-Private Training
        $semi_private = $this->getAppointmentsGraphData($client_id, 'Semi-Private Training');
        if($semi_private['status']['success'] == false) {
            $ret['success'] = 0;
            $ret['message'] = $semi_private['status']['message'];
            return  json_encode(array('status' => $ret));
        }
        
          
        // Resistance Workout
        $resistance_workout = $this->getAppointmentsGraphData($client_id, 'Resistance Workout');
        if($resistance_workout['status']['success'] == false) {
            $ret['success'] = 0;
            $ret['message'] = $resistance_workout['status']['message'];
            return  json_encode(array('status' => $ret));
        }      

        // Cardio Workout
        $cardio_workout = $this->getAppointmentsGraphData($client_id, 'Cardio Workout');
        if($cardio_workout['status']['success'] == false) {
            $ret['success'] = 0;
            $ret['message'] = $cardio_workout['status']['message'];
            return  json_encode(array('status' => $ret));
        }  
 
        // Assessment
        $assessment = $this->getAppointmentsGraphData($client_id, 'Assessment');
        if($assessment['status']['success'] == false) {
            $ret['success'] = 0;
            $ret['message'] = $assessment['status']['message'];
            return  json_encode(array('status' => $ret));
        }  
        
        $ret['success'] = 1;
        
        $result = array('status' => $ret, 
            'data' => array(
                'primary_goals' => $primary_goals['data'], 
                'mini_goals' => $mini_goals['data'],
                'personal_training' => $personal_training['data'],
                'semi_private' => $semi_private['data'],
                'resistance_workout' => $resistance_workout['data'],
                'cardio_workout' => $cardio_workout['data'],
                'assessment' => $assessment['data']
             ) 
        );
                
        return  json_encode($result);
    }
    
    
    /**
     * 
     * @param type $client_id
     * @return type
     */
    function getPrimaryGoalsGraphData($client_id) {
        $db = &JFactory::getDBo();
        $query = "SELECT pg.*, u.name AS client_name, pname.name AS primary_goal_name, tp.color AS training_period_color
            FROM  #__fitness_goals AS pg
            LEFT JOIN #__fitness_goal_categories AS pname on pname.id=pg.goal_category_id
            LEFT JOIN #__fitness_training_period AS tp ON tp.id=pg.training_period_id
            LEFT JOIN #__users AS u ON  u.id=pg.user_id
            WHERE pg.user_id='$client_id'";
        $db->setQuery($query);
        $ret['success'] = 1;
        if (!$db->query()) {
            $ret['success'] = 0;
            $ret['message'] = $db->stderr();
        }
        $primary_goals = array('status' => $ret, 'data' => $db->loadObjectList());
        return  $primary_goals;
    }
    
    /** 
     * 
     * @param type $client_id
     * @return type
     */
     function getMiniGoalsGraphData($client_id) {
        $db = &JFactory::getDBo();
        $query = "SELECT mg.*, u.name AS client_name, mname.name AS mini_goal_name, pg.start_date AS start_date
            FROM  #__fitness_mini_goals AS mg
            LEFT JOIN #__fitness_mini_goal_categories AS mname on mname.id=mg.mini_goal_category_id
            LEFT JOIN #__fitness_goals AS pg ON mg.primary_goal_id=pg.id
            LEFT JOIN #__users AS u ON  u.id=pg.user_id
            WHERE pg.user_id='$client_id'";
        $db->setQuery($query);
        $ret['success'] = 1;
        if (!$db->query()) {
            $ret['success'] = 0;
            $ret['message'] = $db->stderr();
        }
        $mini_goals = array('status' => $ret, 'data' => $db->loadObjectList());
        return  $mini_goals;
    }
    
    
    function getAppointmentsGraphData($client_id, $title) {
        $db = &JFactory::getDBo();
        $query = "SELECT e.*, u.name AS trainer_name FROM #__dc_mv_events AS e
            LEFT JOIN #__users AS u ON  e.trainer_id=u.id
            WHERE (e.client_id='$client_id' OR e.id IN (SELECT  DISTINCT event_id FROM #__fitness_appointment_clients WHERE client_id='$client_id'))
                AND title='$title' AND status='1'";
        $db->setQuery($query);
        $ret['success'] = 1;
        if (!$db->query()) {
            $ret['success'] = 0;
            $ret['message'] = $db->stderr();
        }
        $result = array('status' => $ret, 'data' => $db->loadObjectList());
        return  $result;
    }

}
