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
class FitnessModelNutrition_diaries extends JModelList {

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
     *
     * @since	1.6
     */
    protected function populateState($ordering = null, $direction = null) {

        // Initialise variables.
        $app = JFactory::getApplication();

        // List state information
        $limit = $app->getUserStateFromRequest('global.list.limit', 'limit', $app->getCfg('list_limit'));
        $this->setState('list.limit', $limit);

        $limitstart = JFactory::getApplication()->input->getInt('limitstart', 0);
        $this->setState('list.start', $limitstart);
        
        $published = $app->getUserStateFromRequest($this->context . '.filter.state', 'filter_published', '', 'string');
        $this->setState('filter.state', $published);
        

        // List state information.
        parent::populateState($ordering, $direction);
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

        $query->from('`#__fitness_nutrition_diary` AS a');

        // Filter by published state
        $published = $this->getState('filter.state');
        if(($published != '0') && ($published != '1') ) $published = '1';
        $query->where('a.state = '.(int) $published);
        
        $user = &JFactory::getUser();

        $query->where('a.client_id = '.(int) $user->id);
        
        $query->order('a.entry_date DESC');

        return $query;
    }

    public function getItems() {
        return parent::getItems();
    }
    
    function status_html($status) {
        switch($status) {
            case '1' :
                $class = 'status_inprogress';
                $text = 'IN PROGRESS';
                break;
            case '2' :
                $class = 'status_pass';
                $text = 'PASS';
                break;
            case '3' :
                $class = 'status_fail';
                $text = 'FAIL';
                break;
            case '4' :
                $class = 'status_distinction';
                $text = 'DISTINCTION';
                break;
            case '5' :
                $class = 'status_submitted';
                $text = 'SUBMITTED';
                break;
            default :
                $class = 'status_inprogress';
                $text = 'IN PROGRESS';
                break;
        }

        $html = '<div class="status_button ' . $class . '">' . $text . '</div>';

        return $html;
    }
    
     function status_html_stamp($status) {
        switch($status) {
            case '2' :
                $class = 'status_pass_stamp';
                $text = 'PASS';
                break;
            case '3' :
                $class = 'status_fail_stamp';

                break;
            case '4' :
                $class = 'status_distinction_stamp';
                break;
            case '5' :
                $class = 'status_submitted_stamp';
                break;
            default :
                break;
        }

        $html = '<div class=" status_button_stamp ' . $class . '"></div>';

        return $html;
    }
    
    
    public function updateDiary($table, $data_encoded) {
        $status['success'] = 1;

        $helper = $this->helper;
        
        $data = json_decode($data_encoded);
        
        $id_list = $data->ids;
        
        $ids = explode(",", $id_list);
        
        unset($data->ids);

        foreach ($ids as $id) {
            $data->id = $id;
            try {
                $helper->insertUpdateObj($data, $table);
            } catch (Exception $e) {
                $status['success'] = 0;
                $status['message'] = '"' . $e->getMessage() . '"';
                return array( 'status' => $status);
            }
    
        }
        
        $result = array( 'status' => $status, 'data' => $id_list);
        
        return $result;
    }
    
    public function deleteDiary($table, $data_encoded) {
        $status['success'] = 1;
        
        $data = json_decode($data_encoded);
        
        $id_list = $data->ids;
        
        $db = $this->getDbo();
        
        $query = "DELETE FROM $table WHERE id IN ($id_list)";
        
        $db->setQuery($query);
        if (!$db->query()) {
            $status['success'] = false;
            $status['message'] = $db->stderr();
            return $ret;
        }
        
        $result = array( 'status' => $status, 'data' => $id_list);
        
        return $result;
    }
    
    public function getDiaryDays() {

        $helper = $this->helper;
        
        $user = &JFactory::getUser();
 
        $user_id = $user->id;
        
        $client_id = JRequest::getVar('client_id');
        
        if(!$client_id) {
            $client_id = $user_id;
        }
        
        if(!$client_id) {
            throw new Exception('No client_id'); 
        }
        
        $query = "SELECT entry_date FROM #__fitness_nutrition_diary WHERE client_id='$client_id' AND state='1'";
        
        $data = FitnessHelper::customQuery($query, 3);
        
        return $data;
    }
    
    
    public function getActivePlanData() {
        $user = &JFactory::getUser();
 
        $user_id = $user->id;
        
        $client_id = JRequest::getVar('client_id');
        
        if(!$client_id) {
            $client_id = $user_id;
        }
        
        if(!$client_id) {
            throw new Exception('No client_id'); 
        }
        
        require_once JPATH_COMPONENT_ADMINISTRATOR .  '/models/nutrition_plans.php';
        $nutrition_plans_model  = new FitnessModelnutrition_plans();
        
        $active_plan_id = $nutrition_plans_model->getUserActivePlanId($user_id);

        
        if(!$active_plan_id) {
            throw new Exception('No Active Plan'); 
        }

        $helper = $this->helper;
        
        $active_plan_data = $helper->getPlanData($active_plan_id);

        return $active_plan_data;
    }
    
    
    function getNutritionTarget($table, $data_encoded) {
        $status['success'] = 1;
        
        $data = json_decode($data_encoded);
        
        $nutrition_plan_id = $data->nutrition_plan_id;
        $type = $data->type;
        
        $query = "SELECT * FROM #__fitness_nutrition_plan_targets WHERE
            nutrition_plan_id='$nutrition_plan_id'
            AND type='$type'";
  
        
        try {
            $data = FitnessHelper::customQuery($query, 2);
        } catch (Exception $e) {
            $status['success'] = 0;
            $status['message'] = '"' . $e->getMessage() . '"';
            return array( 'status' => $status);
        }
        
        $result = array( 'status' => $status, 'data' => $data);
        
        return $result;
    }
    
    public function updateDiaryItem($table, $data_encoded) {
        $status['success'] = 1;

        $helper = $this->helper;
        
        $data = json_decode($data_encoded);
          
        try {
            $helper->insertUpdateObj($data, $table);
        } catch (Exception $e) {
            $status['success'] = 0;
            $status['message'] = '"' . $e->getMessage() . '"';
            return array( 'status' => $status);
        }
        
        $result = array( 'status' => $status, 'data' => $data->id);
        
        return $result;
    }
    
    
    public function getDiaryItem($table, $data_encoded) {
        $status['success'] = 1;

        $data = json_decode($data_encoded);
        
        $id = $data->id;
        
        $query = "SELECT a.*,"
                . " (SELECT name FROM #__users WHERE id=a.client_id) client_name,"
                . " (SELECT name FROM #__users WHERE id=a.trainer_id) trainer_name,"
                . " (SELECT name FROM #__users WHERE id=a.assessed_by) assessed_by_name,"
                . " (SELECT name FROM #__fitness_nutrition_focus WHERE id=a.nutrition_focus) nutrition_focus_name"
                . " FROM $table AS a"
                . " WHERE id='$id'";
     

        try {
            $item = FitnessHelper::customQuery($query, 2);
        } catch (Exception $e) {
            $status['success'] = 0;
            $status['message'] = '"' . $e->getMessage() . '"';
            return array( 'status' => $status);
        }
        
        $helper = $this->helper;
        
        
        try {
            $secondary_trainers = $helper->get_client_trainers_names($item->client_id, 'secondary');
            $item->secondary_trainers = $secondary_trainers;
        } catch (Exception $e) {
            $status['success'] = 0;
            $status['message'] = '"' . $e->getMessage() . '"';
            return array( 'status' => $status);
        }

        $result = array( 'status' => $status, 'data' => $item);
        
        return $result;
    }
    
    
     public function saveAsRecipe($table, $data_encoded) {
        $status['success'] = 1;
        
        $helper = $this->helper;
        
        $data = json_decode($data_encoded);

        $meal_id = $data->meal_id;
        
        $user = &JFactory::getUser();


        // save recipe 
        $created = FitnessHelper::getTimeCreated();
            
        $recipe->id = null;
        $recipe->status = '1';
        $recipe->created_by = $user->id;
        $recipe->created = $created;
        $recipe->assessed_by = null;
        
 
        $recipe->recipe_name = $data->recipe_name;
        $recipe->recipe_type = $data->recipe_type;
        $recipe->recipe_variation = $data->recipe_variation;
        $recipe->number_serves = $data->number_serves;
   
        
        try {
            $new_recipe_id = $helper->insertUpdateObj($recipe, '#__fitness_nutrition_recipes');
        } catch (Exception $e) {
            $status['success'] = 0;
            $status['message'] = '"' . $e->getMessage() . '"';
            return array( 'status' => $status);
        }
        
        
        

        
        // get recipe meals
        try {
            $recipe_meals = $helper->getDiaryIngredients($meal_id, '');
        } catch (Exception $e) {
            $status['success'] = 0;
            $status['message'] = '"' . $e->getMessage() . '"';
            return array( 'status' => $status);
        }
  

        // save recipe meals

        foreach ($recipe_meals as $meal) {
            try {
                unset($meal->nutrition_plan_id);
                unset($meal->meal_id);
                unset($meal->type);
                $meal->id = null;
                $meal->recipe_id = $new_recipe_id;
                $inserted_id = $helper->insertUpdateObj($meal, '#__fitness_nutrition_recipes_meals');
            } catch (Exception $e) {
                $status['success'] = 0;
                $status['message'] = '"' . $e->getMessage() . '"';
                return array( 'status' => $status);
            }
        }
        
        $result = array( 'status' => $status, 'data' => $recipe);
        
        return $result;
    }
    
    public function diaries() {
            
        $method = JRequest::getVar('_method');

        if(!$method) {
            $method = $_SERVER['REQUEST_METHOD'];
        }

        $model = json_decode(JRequest::getVar('model'));
        
        $id = JRequest::getVar('id', 0, '', 'INT');

        $table = '#__fitness_nutrition_diary';

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
                $data->status = JRequest::getVar('status'); 
                $data->client_id = JRequest::getVar('client_id'); 
                $data->client_name = JRequest::getVar('client_name'); 
                $data->trainer_name = JRequest::getVar('trainer_name'); 
                $data->assessed_by_name = JRequest::getVar('assessed_by_name'); 
                $data->final_score_from = JRequest::getVar('final_score_from'); 
                $data->final_score_to = JRequest::getVar('final_score_to'); 
                $data->entry_date_from = JRequest::getVar('entry_date_from'); 
                $data->entry_date_to = JRequest::getVar('entry_date_to'); 
                $data->submit_date_from = JRequest::getVar('submit_date_from'); 
                $data->submit_date_to = JRequest::getVar('submit_date_to'); 
                $data->nutrition_focus = JRequest::getVar('nutrition_focus'); 
                $data->primary_goal = JRequest::getVar('primary_goal'); 
                $data->mini_goal = JRequest::getVar('mini_goal'); 
             

                $data = $this->getDiaries($data);

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
    
    public function getDiaries($data) {
        $id = $data->id; 
        $sort_by = $data->sort_by; 
        $order_dirrection = $data->order_dirrection; 
        $page = $data->page; 
        $limit = $data->limit; 
        $state = $data->state;
        $status = $data->status ;
        $client_id = $data->client_id; 
        $client_name = $data->client_name; 
        $trainer_name = $data->trainer_name; 
        $assessed_by_name = $data->assessed_by_name;
        $final_score_from = $data->final_score_from; 
        $final_score_to = $data->final_score_to; 
        $entry_date_from = $data->entry_date_from; 
        $entry_date_to = $data->entry_date_to; 
        $submit_date_from = $data->submit_date_from;
        $submit_date_from = $data->submit_date_from;
        $submit_date_to = $data->submit_date_to; 
        $nutrition_focus= $data->nutrition_focus; 
        $primary_goal= $data->primary_goal;
        $mini_goal = $data->mini_goal;
        
        $helper = new FitnessHelper();
        
        $start = ($page - 1) * $limit;
        
        $table = '#__fitness_nutrition_diary';

        $query = " SELECT a.*, u.name AS assessed_by_name,"

        . " (SELECT name FROM #__users WHERE id=a.client_id) client_name,"
        . " (SELECT name FROM #__users WHERE id=a.trainer_id) trainer_name,"
        . " (SELECT name FROM #__users WHERE id=a.assessed_by) assessed_by_name,"
        . " (SELECT name FROM #__fitness_nutrition_focus WHERE id=np.nutrition_focus) nutrition_focus_name, "
        . " (SELECT name FROM #__fitness_goal_categories WHERE id=pg.goal_category_id) primary_goal_name, "
        . " (SELECT name FROM #__fitness_mini_goal_categories WHERE id=mg.mini_goal_category_id) mini_goal_name, ";
        
        $query .= " (SELECT user_id  FROM #__fitness_clients WHERE user_id=a.created_by LIMIT 1) created_by_client,";

        //get total number
        $query .= " (SELECT COUNT(*) FROM $table AS a ";
        $query .= " WHERE 1 ";
        
        if($client_id) {
            $query .= " AND a.client_id='$client_id' ";
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
        
        //search by assessed by name 
        if (!empty($data->assessed_by_name)) {
            $sql = " SELECT GROUP_CONCAT(id) FROM #__users WHERE name LIKE '%$data->assessed_by_name%' ";

            $assessed_by_ids = FitnessHelper::customQuery($sql, 0);

            if($assessed_by_ids) {
                $query .= " AND a.assessed_by IN ($assessed_by_ids)";
            }
        }
        
        if (!empty($final_score_from)) {
            $query .= " AND a.score >= $final_score_from";
        }
        
        if (!empty($final_score_to)) {
            $query .= " AND a.score <= $final_score_to";
        }
        
        
        if (!empty($data->entry_date_from)) {
            $query .= " AND a.entry_date >= '$data->entry_date_from'";
        }

        if (!empty($data->entry_date_to)) {
            $query .= " AND a.entry_date <= '$data->entry_date_to'";
        }

        if (!empty($data->submit_date_from)) {
            $query .= " AND a.submit_date >= '$data->submit_date_from'";
        }

        if (!empty($data->submit_date_to)) {
            $query .= " AND a.submit_date <= '$data->submit_date_to'";
        }


        if($data->state != '' AND $data->state != '*') {
            $query .= " AND a.state='$data->state'";
        }
        
        if($status) {
            $query .= " AND a.status='$status'";
        }
        
        $query .= " ) items_total ";
        //
        $query .= " FROM $table AS a";
        $query .= " LEFT JOIN #__users AS u ON u.id=a.assessed_by";
        $query .= " LEFT JOIN #__fitness_nutrition_plan AS np ON np.id=a.nutrition_plan_id";
        $query .= " LEFT JOIN #__fitness_goals AS pg ON pg.id=np.primary_goal";
        $query .= " LEFT JOIN #__fitness_mini_goals AS mg ON mg.id=np.mini_goal";
        $query .= " WHERE 1 ";
        
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
        
        //search by assessed by name 
        if (!empty($data->assessed_by_name)) {
            $sql = " SELECT GROUP_CONCAT(id) FROM #__users WHERE name LIKE '%$data->assessed_by_name%' ";

            $assessed_by_ids = FitnessHelper::customQuery($sql, 0);

            if($assessed_by_ids) {
                $query .= " AND a.assessed_by IN ($assessed_by_ids)";
            }
        }
        
        if (!empty($final_score_from)) {
            $query .= " AND a.score >= $final_score_from";
        }
        
        if (!empty($final_score_to)) {
            $query .= " AND a.score <= $final_score_to";
        }
        
        if (!empty($data->entry_date_from)) {
            $query .= " AND a.entry_date >= '$data->entry_date_from'";
        }

        if (!empty($data->entry_date_to)) {
            $query .= " AND a.entry_date <= '$data->entry_date_to'";
        }

        if (!empty($data->submit_date_from)) {
            $query .= " AND a.submit_date >= '$data->submit_date_from'";
        }

        if (!empty($data->submit_date_to)) {
            $query .= " AND a.submit_date <= '$data->submit_date_to'";
        }


        if($id) {
            $query .= " AND a.id='$id' ";
        }
        
        if($client_id) {
            $query .= " AND a.client_id='$client_id' ";
        }

        if($data->state != '' AND $data->state != '*') {
            $query .= " AND a.state='$data->state'";
        }
        
        if($status) {
            $query .= " AND a.status='$status'";
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

        $query_method = 1;

        if($id) {
            $query_method = 2;
        }

        $data = FitnessHelper::customQuery($query, $query_method);


        if(!$id) {
            $i = 0;
            foreach ($data as $item) {
                $client_trainers = $helper->get_client_trainers_names($data->client_id, 'secondary');

                $data[$i]->secondary_trainers = $client_trainers;

                $i++;
            }
        } else {
            $client_trainers = $helper->get_client_trainers_names($data->client_id, 'secondary');

            $data->secondary_trainers = $client_trainers;
        }
        
        return $data;

    }
    
    public function meal_entries() {
        $method = JRequest::getVar('_method');

        if(!$method) {
            $method = $_SERVER['REQUEST_METHOD'];
        }

        $model = json_decode(JRequest::getVar('model'));
        
        $id = JRequest::getVar('id', 0, '', 'INT');

        $table = '#__fitness_nutrition_diary_meal_entries';

        $helper = new FitnessHelper();

        switch ($method) {
            case 'GET': // Get Item(s)
                $nutrition_plan_id = JRequest::getVar('nutrition_plan_id'); 
                $diary_id = JRequest::getVar('diary_id'); 
                
                $query .= "SELECT a.* FROM $table AS a";
                
                $query .= " WHERE 1 ";
   
                if($id) {
                    $query .= " AND a.id='$id' ";
                }
                
                if($nutrition_plan_id) {
                    $query .= " AND a.nutrition_plan_id='$nutrition_plan_id' ";
                }
                
                if($diary_id) {
                    $query .= " AND a.diary_id='$diary_id' ";
                }
                
                $query .= " ORDER BY a.meal_time";
               
                $query_method = 1;
                
                if($id) {
                    $query_method = 2;
                }
                
                $data = FitnessHelper::customQuery($query, $query_method);

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
    
    public function diary_meals() {
        $method = JRequest::getVar('_method');

        if(!$method) {
            $method = $_SERVER['REQUEST_METHOD'];
        }

        $model = json_decode(JRequest::getVar('model'));
        
        $id = JRequest::getVar('id', 0, '', 'INT');

        $table = '#__fitness_nutrition_diary_meals';

        $helper = new FitnessHelper();

        switch ($method) {
            case 'GET': // Get Item(s)
                $nutrition_plan_id = JRequest::getVar('nutrition_plan_id'); 
                $diary_id = JRequest::getVar('diary_id'); 
                $meal_entry_id = JRequest::getVar('meal_entry_id'); 
                
                $query .= "SELECT a.* FROM $table AS a";
                
                $query .= " WHERE 1 ";
   
                if($id) {
                    $query .= " AND a.id='$id' ";
                }
                
                if($nutrition_plan_id) {
                    $query .= " AND a.nutrition_plan_id='$nutrition_plan_id' ";
                }
                
                if($diary_id) {
                    $query .= " AND a.diary_id='$diary_id' ";
                }
                
                if($meal_entry_id) {
                    $query .= " AND a.meal_entry_id='$meal_entry_id' ";
                }

               
                $query_method = 1;
                
                if($id) {
                    $query_method = 2;
                }
                
                $data = FitnessHelper::customQuery($query, $query_method);

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
    
    
    public function meal_ingredients() {
        $method = JRequest::getVar('_method');

        if(!$method) {
            $method = $_SERVER['REQUEST_METHOD'];
        }

        $model = json_decode(JRequest::getVar('model'));
        
        $id = JRequest::getVar('id', 0, '', 'INT');
        

        $table = '#__fitness_nutrition_diary_ingredients';
     

        $helper = new FitnessHelper();

        switch ($method) {
            case 'GET': // Get Item(s)
                $nutrition_plan_id = JRequest::getVar('nutrition_plan_id'); 
                $diary_id = JRequest::getVar('diary_id'); 
                $meal_entry_id = JRequest::getVar('meal_entry_id'); 
                $meal_id = JRequest::getVar('meal_id'); 
                
                $query .= "SELECT a.* FROM $table AS a";
                
                $query .= " WHERE 1 ";
   
                if($id) {
                    $query .= " AND a.id='$id' ";
                }
                
                if($nutrition_plan_id) {
                    $query .= " AND a.nutrition_plan_id='$nutrition_plan_id' ";
                }
                
                if($diary_id) {
                    $query .= " AND a.diary_id='$diary_id' ";
                }
                
                if($meal_entry_id) {
                    $query .= " AND a.meal_entry_id='$meal_entry_id' ";
                }
                
                if($meal_id) {
                    $query .= " AND a.meal_id='$meal_id' ";
                }
                
                $query_method = 1;
                
                if($id) {
                    $query_method = 2;
                }
                
                $data = FitnessHelper::customQuery($query, $query_method);

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
    
    
    
    public function copyMealEntry($data_encoded) {
        $status['success'] = 1;
        
        $helper = new FitnessHelper();
        
        $data = json_decode($data_encoded);
        
        $meal_entry_id = $data->id;
        
        if(!$meal_entry_id) {
            throw new Exception("Error: no meal_entry_id");
        }
        
        $meal_entries_table = '#__fitness_nutrition_diary_meal_entries';
        
        $diary_meals_table = '#__fitness_nutrition_diary_meals';
        
        $ingredients_table = '#__fitness_nutrition_diary_ingredients';
        
        $query1 = " SELECT * FROM $meal_entries_table WHERE id='$meal_entry_id'";
        
        $meal_entry = FitnessHelper::customQuery($query1, 2);
        
        $meal_entry->id = null;
        
        $new_meal_entry_id = $helper->insertUpdateObj($meal_entry, $meal_entries_table);
        
        //
        
        $query2 = " SELECT * FROM $diary_meals_table WHERE meal_entry_id='$meal_entry_id'";
        
        $diary_meals = FitnessHelper::customQuery($query2, 1);
      
        foreach ($diary_meals as $diary_meal) {
            
            $meal_id = $diary_meal->id;
            
            $diary_meal->id = null;
            $diary_meal->meal_entry_id = $new_meal_entry_id;
            $new_meal_id = $helper->insertUpdateObj($diary_meal, $diary_meals_table);
               
            $query3 = " SELECT * FROM $ingredients_table WHERE meal_id='$meal_id'";
            
            $ingredients = FitnessHelper::customQuery($query3, 1);
            
            foreach ($ingredients as $ingredient) {
                $ingredient->id = null;
                $ingredient->meal_entry_id = $new_meal_entry_id;
                $ingredient->meal_id = $new_meal_id;
                $helper->insertUpdateObj($ingredient, $ingredients_table);
            }
        }
        
        //

        $result = array( 'status' => $status, 'data' => $diary_meals);
        
        return $result;
    }
    
    public function copyDiaryMeal($data_encoded) {
        $status['success'] = 1;
        
        $helper = new FitnessHelper();
        
        $data = json_decode($data_encoded);
        
        $meal_id = $data->id;
        
        if(!$meal_id) {
            throw new Exception("Error: no meal_id");
        }
        
        
        $diary_meals_table = '#__fitness_nutrition_diary_meals';
        
        $ingredients_table = '#__fitness_nutrition_diary_ingredients';

        
        $query2 = " SELECT * FROM $diary_meals_table WHERE id='$meal_id'";
        
        $diary_meal = FitnessHelper::customQuery($query2, 2);
      
            
        $diary_meal->id = null;
        $diary_meal->meal_entry_id = $diary_meal->meal_entry_id;
        $new_meal_id = $helper->insertUpdateObj($diary_meal, $diary_meals_table);

        $query3 = " SELECT * FROM $ingredients_table WHERE meal_id='$meal_id'";

        $ingredients = FitnessHelper::customQuery($query3, 1);

        foreach ($ingredients as $ingredient) {
            $ingredient->id = null;
            $ingredient->meal_entry_id = $diary_meal->meal_entry_id;
            $ingredient->meal_id = $new_meal_id;
            $helper->insertUpdateObj($ingredient, $ingredients_table);
        }
        
        //

        $result = array( 'status' => $status, 'data' => $diary_meal);
        
        return $result;
    }
    
    public function comments() {
        $method = JRequest::getVar('_method');

        if(!$method) {
            $method = $_SERVER['REQUEST_METHOD'];
        }

        $model = json_decode(JRequest::getVar('model'));
        
        $id = JRequest::getVar('id', 0, '', 'INT');

        $table = '#__' . JRequest::getVar('db_table');
        
        if(!$table) {
            throw new Exception("Error: no db_table");
        }

        $helper = new FitnessHelper();

        switch ($method) {
            case 'GET': // Get Item(s)

                $query .= "SELECT a.*, ";
                
                $query .= " (SELECT name FROM #__users WHERE id=a.created_by) created_by_name,";
                
                $query .= " (SELECT user_id  FROM #__fitness_clients WHERE user_id=a.created_by LIMIT 1) created_by_client";
                
                $query .= "  FROM $table AS a";
                
                $query .= " WHERE 1 ";
   
                if($id) {
                    $query .= " AND a.id='$id' ";
                }
   
                $query .= " ORDER BY a.created";
               
                $query_method = 1;
                
                if($id) {
                    $query_method = 2;
                }
                
                $data = FitnessHelper::customQuery($query, $query_method);

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
                
                $db = JFactory::getDbo();
                
                $query = "SELECT id FROM $table WHERE parent_id = '$id'";
                
                $items = FitnessHelper::customQuery($query, 1);
                
                foreach ($items as $item) {
                    $helper->deleteRow($item->id, $table);
                }
                
                $id = $helper->deleteRow($id, $table);
                
                
                break;

            default:
                break;
        }

        $model->id = $id;

        return $model;
    }
    
    
    public function users_names() {
        $ids = JRequest::getVar('ids');
        
        $query = "SELECT id, name FROM #__users";
        
        $query .= " WHERE 1 ";
        
        $query .= " AND id IN($ids) ";
        
        $query .= " ORDER BY name ";

        $data = FitnessHelper::customQuery($query, 1);
        
        return $data;
    }
    
}
