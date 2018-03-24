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

        $this->helper = new FitnessHelper();

        parent::__construct($config);
    }

    /**
     * Method to auto-populate the model state.
     *
     * Note. Calling getState in this method will result in recursion.
     */
    protected function populateState($ordering = null, $direction = null) {

    }

    protected function getStoreId($id = '') {

    }

    protected function getListQuery() {

    }

    public function getItems() {
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
    public function getUsersByGroup($group_id) {
        $status['success'] = 1;
        
        $helper = $this->helper;
        
        $users = $helper->getUsersByGroup($group_id);
        
        if(!$users['success']) {
            $status['success'] = 0;
            $status['message'] = $users['message'];
        }

        $result = array( 'status' => $status, 'data' => $users['data']);
        return  json_encode($result);

    }
    
    public function getUsersByBusiness($data_encoded) {
        $status['success'] = 1;
        
        $helper = $this->helper;
        
        $data = json_decode($data_encoded);
        
        $business_profile_id = $data->business_profile_id;
        
        $user_group_data = $helper->getUserGroupByBusiness($business_profile_id);
        
        if(!$user_group_data['success']) {
            $status['success'] = 0;
            $status['message'] = $user_group_data['message'];
            $result = array( 'status' => $status);
            return  json_encode($result);
        }
        
        $user_group_data = $user_group_data['data'];
        
        $group_id = $user_group_data->group_id;
        
        return $this->getUsersByGroup($group_id);

    }
    
    public function getClientsByBusiness($data_encoded) {
        $status['success'] = 1;
        
        $helper = $this->helper;
        
        $data = json_decode($data_encoded);
        
        $business_profile_id = $data->business_profile_id;
        
        //logged user
        $user_id = $data->user_id;
     
        $clients =  $helper->getClientsByBusiness($business_profile_id, $user_id);

        if(!$clients['success']) {
            $status['success'] = 0;
            $status['message'] = $clients['message'];
            $result = array( 'status' => $status);
            return  json_encode($result);
        }
        
        $result = array( 'status' => $status, 'data' => $clients['data']);

        
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
    function getGraphData($client_id, $data_encoded) {
        $data = json_decode($data_encoded);

        // primary goals
        $primary_goals = $this->getPrimaryGoalsGraphData($client_id, $data);
        if($primary_goals['status']['success'] == false) {
            $ret['success'] = 0;
            $ret['message'] = $primary_goals['status']['message'];
            return  json_encode(array('status' => $ret));
        }
        
        //mini goals
        $mini_goals = $this->getMiniGoalsGraphData($client_id, $data);
        if($mini_goals['status']['success'] == false) {
            $ret['success'] = 0;
            $ret['message'] = $mini_goals['status']['message'];
            return  json_encode(array('status' => $ret));
        }

        
        // appointment data
        $personal_training = $this->getAppointmentsGraphData($client_id, '1');//'Personal Training'
       
        if($personal_training['status']['success'] == false) {
            $ret['success'] = 0;
            $ret['message'] = $personal_training['status']['message'];
            return  json_encode(array('status' => $ret));
        }
        
        // Semi-Private Training
        $semi_private = $this->getAppointmentsGraphData($client_id, '2');//'Semi-Private Training'
        if($semi_private['status']['success'] == false) {
            $ret['success'] = 0;
            $ret['message'] = $semi_private['status']['message'];
            return  json_encode(array('status' => $ret));
        }
        
          
        // Resistance Workout
        $resistance_workout = $this->getAppointmentsGraphData($client_id, '3');//'Resistance Workout'
        if($resistance_workout['status']['success'] == false) {
            $ret['success'] = 0;
            $ret['message'] = $resistance_workout['status']['message'];
            return  json_encode(array('status' => $ret));
        }      

        // Cardio Workout
        $cardio_workout = $this->getAppointmentsGraphData($client_id, '4');//'Cardio Workout'
        if($cardio_workout['status']['success'] == false) {
            $ret['success'] = 0;
            $ret['message'] = $cardio_workout['status']['message'];
            return  json_encode(array('status' => $ret));
        }  
 
        // Assessment
        $assessment = $this->getAppointmentsGraphData($client_id, '5');//'Assessment'
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
    function getPrimaryGoalsGraphData($client_id, $data) {
        $list_type = $data->list_type;
        $config = JFactory::getConfig();
        $date = new DateTime($time_created);
        $date->setTimezone(new DateTimeZone($config->getValue('config.offset')));
        $current_date = $date->format('Y-m-d');
        
        $db = &JFactory::getDBo();
        $query = "SELECT pg.*, u.name AS client_name, pname.name AS primary_goal_name
            FROM  #__fitness_goals AS pg
            LEFT JOIN #__fitness_goal_categories AS pname on pname.id=pg.goal_category_id
            LEFT JOIN #__users AS u ON  u.id=pg.user_id
            WHERE pg.user_id='$client_id' AND pg.state='1'";
        
        if($list_type == 'previous') {
            $query .= " AND ( pg.deadline < " . $db->quote($current_date);
            $query .= " OR (pg.start_date <= " . $db->quote($current_date);
            $query .= " AND pg.deadline > " . $db->quote($current_date) . " )) ";
        }
        
        if($list_type == 'current') {
            $query .= " AND pg.deadline > " . $db->quote($current_date);
        }
        
        if($list_type == 'current_primary_goal') {
            $query .= " AND pg.start_date <= " . $db->quote($current_date);
            $query .= " AND pg.deadline > " . $db->quote($current_date);
        }
       

                
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
     function getMiniGoalsGraphData($client_id, $data) {
        $list_type = $data->list_type;
        $config = JFactory::getConfig();
        $date = new DateTime($time_created);
        $date->setTimezone(new DateTimeZone($config->getValue('config.offset')));
        $current_date = $date->format('Y-m-d');
        $db = &JFactory::getDBo();
        $query = "SELECT mg.*, u.name AS client_name, mname.name AS mini_goal_name, mg.start_date AS start_date, tp.color AS training_period_color, tp.name AS training_period_name, tp.name AS training_period_name
            FROM  #__fitness_mini_goals AS mg
            LEFT JOIN #__fitness_mini_goal_categories AS mname on mname.id=mg.mini_goal_category_id
            LEFT JOIN #__fitness_goals AS pg ON mg.primary_goal_id=pg.id
            LEFT JOIN #__users AS u ON  u.id=pg.user_id
            LEFT JOIN #__fitness_training_period AS tp ON tp.id=mg.training_period_id
            WHERE pg.user_id='$client_id' AND mg.state='1'";
        
        
        if($list_type == 'previous') {
            $query .= " AND ( mg.deadline < " . $db->quote($current_date);
            $query .= " OR (mg.start_date <= " . $db->quote($current_date);
            $query .= " AND mg.deadline > " . $db->quote($current_date) . " )) ";
        }
        
        if($list_type == 'current') {
            $query .= " AND mg.deadline > " . $db->quote($current_date);
        }
        

        
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
        $query = "SELECT 
            e.*,
            (SELECT name FROM #__fitness_session_type WHERE id=e.session_type) AS session_type_name, 
            (SELECT name FROM #__fitness_session_focus WHERE id=e.session_focus) AS session_focus_name, 
            (SELECT name FROM #__fitness_locations WHERE id=e.location) AS location_name, 
            (SELECT name FROM #__users WHERE id=e.trainer_id) AS trainer_name, 
            (SELECT color FROM #__fitness_categories WHERE id=e.title) AS color
            FROM #__dc_mv_events AS e
            WHERE e.id IN (SELECT  DISTINCT event_id FROM #__fitness_appointment_clients WHERE client_id='$client_id')
                AND e.title='$title' AND e.published='1'";
        $db->setQuery($query);
        $ret['success'] = 1;
        if (!$db->query()) {
            $ret['success'] = 0;
            $ret['message'] = $db->stderr();
        }
        $result = array('status' => $ret, 'data' => $db->loadObjectList());
        return  $result;
    }
    
    
    function status_html($item_id, $status, $button_class) {
        switch($status) {
            case '1' :
                $class = 'goal_status_pending';
                $text = 'PENDING';
                break;
            case '2' :
                $class = 'goal_status_complete';
                $text = 'COMPLETE';
                break;
            case '3' :
                $class = 'goal_status_incomplete';
                $text = 'INCOMPLETE';
                break;
            case '4' :
                $class = 'goal_status_evaluating';
                $text = 'EVALUATING';
                break;
            case '5' :
                $class = 'goal_status_inprogress';
                $text = 'IN PROGRESS';
                break;
            case '6' :
                $class = 'goal_status_assessing';
                $text = 'ASSESSING';
                break;
            default :
                $class = 'goal_status_evaluating';
                $text = 'EVALUATING';
                break;
        }

        $html = '<a href="javascript:void(0)" data-item_id="' . $item_id . '" data-status_id="' . $status . '" class="' . $button_class . ' ' . $class . '">' . $text . '</a>';

        return $html;
    }
    
    
    
    function getMiniGoalName($mini_goal_category_id) {
            $db = JFactory::getDbo();
            $sql = "SELECT name FROM #__fitness_mini_goal_categories WHERE id='$mini_goal_category_id'";
            $db->setQuery($sql);
            if(!$db->query()) {
                JError::raiseError($db->getErrorMsg());
            }
            $result = $db->loadResult();
            return $result;
    }
    
    function getTrainingPeriodName($id) {
            $db = JFactory::getDbo();
            $sql = "SELECT name FROM #__fitness_training_period WHERE id='$id'";
            $db->setQuery($sql);
            if(!$db->query()) {
                JError::raiseError($db->getErrorMsg());
            }
            $result = $db->loadResult();
            return $result;
    }
        
    
    function getMiniGoalsList($primary_goal_id, $type) {
        $db = JFactory::getDbo();
        $sql = "SELECT DISTINCT id, mini_goal_category_id, training_period_id, start_date, deadline, status FROM #__fitness_mini_goals WHERE primary_goal_id='$primary_goal_id' AND state='1'";
        $db->setQuery($sql);
        if(!$db->query()) {
            JError::raiseError($db->getErrorMsg());
        }
        $ids = $db->loadResultArray(0);
        $mini_goal_category_ids = $db->loadResultArray(1);
        $training_period_id = $db->loadResultArray(2);
        $start_date = $db->loadResultArray(3);
        $deadlines = $db->loadResultArray(4);
        $status = $db->loadResultArray(5);
        
        if($type == 'status')  return $status;
        
        if($type == 'id')  return $ids;
        
       if($type == 'training_period') {
            foreach ($training_period_id as $value) {
                $html .= $this->getTrainingPeriodName($value) . "<br>";
            }
            return $html;
        }
        
        if($type == 'start_date') {
            foreach ($start_date as $value) {
                $html .= $value . "<br>";
            }
            return $html;
        }
        
        if($type == 'deadline') {
            foreach ($deadlines as $value) {
                $html .= $value . "<br>";
            }
            return $html;
        }

        foreach ($mini_goal_category_ids as $value) {
            $html .= $this->getMiniGoalName($value) . "<br>";
        }
        
        return $html;
    }
    
    function getMinigoalsStatusHtml($primary_goal_id) {
        $statuses = $this->getMiniGoalsList($primary_goal_id, 'status');
        $ids = $this->getMiniGoalsList($primary_goal_id, 'id');
        $i = 0;
        foreach ($statuses as $status) {
            $html .= '<div id="status_button_place_mini_' . $ids[$i] .'">';
            $html .= $this->status_html($ids[$i], $status, 'status_button_mini');
            $html .= '</div>';
            
            $i++;
        }
        return $html;
    }
    
    /**
     * 
     * @param type $data_encoded {start_date, end_date, start_date_column, end_date_column}
     * @param type $table
     * @return bool
     */
    function checkOverlapDate($data_encoded, $table) {
        $data = json_decode($data_encoded);
        
        $item_id = $data->item_id;
        
        $where_column = $data->where_column;
        $where_value = $data->where_value;
        
        $start_date = $data->start_date;
        $end_date = $data->end_date;
        
        $start_date_column = $data->start_date_column;
        $end_date_column = $data->end_date_column;
        
        $db = &JFactory::getDBo();
        
        $query = "SELECT * FROM $table WHERE " 
            . " ( ( " . $start_date_column . "  <=  "  . $db->quote($start_date) . " AND "
            . $end_date_column . "  >=  "  . $db->quote($start_date) . " ) OR "
                
            . " ( " . $start_date_column . "  <=  "  . $db->quote($end_date) . " AND "
            . $end_date_column . "  >=  "  . $db->quote($end_date) . " ) OR "  
            
            . " ( " . $start_date_column . "  >=  "  . $db->quote($start_date) . " AND " 
            . $end_date_column . "  <=  "  . $db->quote($end_date) . " ) ) " .
                
            "  AND state='1' ";
        
        if($where_value && $where_column) {
            $query .= " AND $where_column =" . $db->quote($where_value);
        }
        
        $query .= " AND id NOT IN ('$item_id')";
       
        $db->setQuery($query);
        $ret['success'] = 1;
        if (!$db->query()) {
            $ret['success'] = 0;
            $ret['message'] = $db->stderr();
        }
        $result = array('status' => $ret, 'data' => $db->loadResult());
        return  json_encode($result);
    }
    
    
    
    //TRAINING PERIODIZATION
    
    public function training_periods() {
            
        $method = JRequest::getVar('_method');

        if(!$method) {
            $method = $_SERVER['REQUEST_METHOD'];
        }
       
        $model = json_decode(JRequest::getVar('model','','','',JREQUEST_ALLOWHTML));

        $id = JRequest::getVar('id', 0, '', 'INT');
        
        $table = '#__fitness_training_periodalization';

        $helper = new FitnessHelper();

        switch ($method) {
            case 'GET': // Get Item(s)
                $data = new stdClass();
                $mini_goal_id = JRequest::getVar('mini_goal_id'); 

                $query = " SELECT a.*";
                $query .= " FROM $table AS a";
                $query .= " WHERE 1";
                
                if($mini_goal_id) {
                    $query .= " AND a.mini_goal_id='$mini_goal_id'";
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
    
    
    public function training_sessions() {
            
        $method = JRequest::getVar('_method');

        if(!$method) {
            $method = $_SERVER['REQUEST_METHOD'];
        }
       
        $model = json_decode(JRequest::getVar('model','','','',JREQUEST_ALLOWHTML));

        $id = JRequest::getVar('id', 0, '', 'INT');
        
        $table = '#__fitness_training_sessions';

        $helper = new FitnessHelper();

        switch ($method) {
            case 'GET': // Get Item(s)
                $data = new stdClass();
                $period_id = JRequest::getVar('period_id'); 

                $query = " SELECT a.*, ";
                
                $query .= " (SELECT name FROM #__fitness_categories WHERE id=a.appointment_type_id) appointment_name,";
                
                $query .= " (SELECT name FROM #__fitness_session_type WHERE id=a.session_type) session_type_name,";
                
                $query .= " (SELECT name FROM #__fitness_session_focus WHERE id=a.session_focus) session_focus_name,";
                
                $query .= " (SELECT name FROM #__fitness_locations WHERE id=a.location) location_name,";
                
                $query .= " (SELECT name FROM #__fitness_programs_templates WHERE id=a.pr_temp_id) pr_temp_name";
                
                $query .= " FROM $table AS a";
                $query .= " WHERE 1";
                
                if($period_id) {
                    $query .= " AND a.period_id='$period_id'";
                }
                
                $query .= " ORDER BY a.starttime ASC";
                
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
    
    public function scheduleSession() {
        $helper = new FitnessHelper();
        $db = JFactory::getDbo();
        $data_encoded = JRequest::getVar('data_encoded');
        $session = json_decode($data_encoded);
        $ret['success'] = 1;
        
        
        //get trainer_id
        try {
            $trainer = $helper->getPrimaryTrainer($session->client_id);
        } catch (Exception $exc) {
            $ret['success'] = 0;
            $ret['message'] = $exc->getMessage();
            return array( 'status' => $ret);
        }

        //get client's business profile
        $business_profile = $helper->getBusinessProfileId($session->client_id);
        if(!$business_profile['success']) {
            $ret['success'] = 0;
            $ret['message'] = $business_profile['message'];
            return array( 'status' => $ret);
        }
        $business_profile_id = $business_profile['data'];
        
        //insert Event
        $session->id = null;
        $session->description = '';
        $session->comments = '';
        $session->trainer_id = $trainer->id;
        $session->business_profile_id = $business_profile_id;
        $session->title = $session->appointment_type_id;
        $session->published = '1';
        $insert = $helper->insertUpdateObj($session, '#__dc_mv_events');
        if (!$insert) {
            $ret['success'] = 0;
            $ret['message'] = $db->stderr();
            return array( 'status' => $ret);
        }
        $inserted_event_id = $db->insertid();
        
        //insert client
        $client  = new stdClass();
        $client->id = null;
        $client->client_id = $session->client_id;
        $client->event_id = $inserted_event_id;
        $client->status = '1';
        $insert = $db->insertObject('#__fitness_appointment_clients', $client, 'id');
        if (!$insert) {
            $ret['success'] = 1;
            $ret['message'] = $db->stderr();
            return array( 'status' => $ret);
        }
        
        // import Program template exercises
        $pr_temp_id = $session->pr_temp_id;
        if($pr_temp_id) {
            $query = "SELECT a.* FROM #__fitness_pr_temp_exercises AS a  WHERE a.item_id='$pr_temp_id'";
            
             try {
                $exercises =  FitnessHelper::customQuery($query, 1);
            } catch (Exception $exc) {
                $ret['success'] = 0;
                $ret['message'] = $exc->getMessage();
                return array( 'status' => $ret);
            }

            foreach ($exercises as $exercise) {
                $exercise->id = null;
                $exercise->item_id = $inserted_event_id;

                $insert = $this->insertExercise($exercise, null, $exercise->item_id, '#__fitness_events_exercises');

                if (!$insert) {
                    $status['success'] = 0;
                    $status['message'] = $db->stderr();
                    return array( 'status' => $status);
                }
            }
        }
        return array('status' => $ret, 'data' => print_r($session, true));
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
    
    public function copySessionPeriod() {
        $helper = new FitnessHelper();
        $db = JFactory::getDbo();
        $data_encoded = JRequest::getVar('data_encoded');
        $data= json_decode($data_encoded);
        $ret['success'] = 1;
        
        $period_id = $data->id;
        $advance_period = $data->advance_period;
        
        //copy period
        $query = "SELECT * FROM #__fitness_training_periodalization WHERE id='$period_id'";
        
        try {
            $period =  FitnessHelper::customQuery($query, 2);
        } catch (Exception $exc) {
            $ret['success'] = 0;
            $ret['message'] = $exc->getMessage();
            return array( 'status' => $ret);
        }
        
        $period->id = null;
        
        $insert = $db->insertObject('#__fitness_training_periodalization', $period, 'id');
        if (!$insert) {
            $ret['success'] = 1;
            $ret['message'] = $db->stderr();
            return array( 'status' => $ret);
        }
        
        //copy sessions
        $new_period_id = $db->insertid();
        $query = "SELECT a.* FROM #__fitness_training_sessions AS a  WHERE a.period_id='$period_id'";

        try {
            $sessions =  FitnessHelper::customQuery($query, 1);
        } catch (Exception $exc) {
            $ret['success'] = 0;
            $ret['message'] = $exc->getMessage();
            return array( 'status' => $ret);
        }

        foreach ($sessions as $session) {
            $session->id = null;
            $session->period_id = $new_period_id;
            
            if($advance_period) {
                $start_date = new JDate($session->starttime);
                $unix_start_date = $start_date->toUnix() + 24*60*60*$advance_period;
                $advance_start_date = JFactory::getDate($unix_start_date);  
                $session->starttime = $advance_start_date->format("Y-m-d H:i:s");
                
                $end_date = new JDate($session->endtime);
                $unix_end_date = $end_date->toUnix() + 24*60*60*$advance_period;
                $advance_end_date = JFactory::getDate($unix_end_date);  
                $session->endtime = $advance_end_date->format("Y-m-d H:i:s");
            }

            $insert = $db->insertObject('#__fitness_training_sessions', $session, 'id');

            if (!$insert) {
                $status['success'] = 0;
                $status['message'] = $db->stderr();
                return array( 'status' => $status);
            }
        }
        
        return array('status' => $ret, 'data' => print_r($session, true));
    }
    
    
    public function addPlan() {
        $helper = new FitnessHelper();
        $db = JFactory::getDbo();
        $data_encoded = JRequest::getVar('data_encoded');
        $goal = json_decode($data_encoded);
        
        $ret['success'] = 1;

        $helper = new FitnessHelper();
        $obj = new stdClass();
        $obj->id = $goal->id;
        $obj->primary_goal_id = $goal->primary_goal_id;
        $obj->start_date = $goal->start_date;
        $obj->deadline = $goal->deadline;
        $obj->created_by = $goal->created_by;

        try {
            $plan_data = $helper->goalToPlanDecorator($obj);
            $helper->addNutritionPlan($plan_data);
        } catch (Exception $exc) {
            $ret['success'] = 0;
            $ret['message'] = $exc->getMessage();
            return array( 'status' => $ret);
        }

        return array('status' => $ret, 'data' => print_r($plan_data, true));
    }
}
