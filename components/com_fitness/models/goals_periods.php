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

// connect backend model
require_once JPATH_COMPONENT_ADMINISTRATOR . DS . 'models' . DS . 'goals.php';
/**
 * Methods supporting a list of Fitness_goals records.
 */
class FitnessModelgoals_periods extends JModelList {

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
        // Initialise variables.
        $app = JFactory::getApplication('administrator');

        // Load the filter state.
        $search = $app->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
        $this->setState('filter.search', $search);

        $published = $app->getUserStateFromRequest($this->context . '.filter.state', 'filter_published', '', 'string');
        $this->setState('filter.state', $published);
        
        //Filtering start date
        $this->setState('filter.start_date.from', $app->getUserStateFromRequest($this->context.'.filter.start_date.from', 'filter_from_start_date', '', 'string'));
        $this->setState('filter.start_date.to', $app->getUserStateFromRequest($this->context.'.filter.start_date.to', 'filter_to_start_date', '', 'string'));
        
        
        //Filtering deadline
        $this->setState('filter.deadline.from', $app->getUserStateFromRequest($this->context.'.filter.deadline.from', 'filter_from_deadline', '', 'string'));
        $this->setState('filter.deadline.to', $app->getUserStateFromRequest($this->context.'.filter.deadline.to', 'filter_to_deadline', '', 'string'));
        
        // Filter by goal category
        $goal_category = $app->getUserStateFromRequest($this->context . '.filter.goal_category', 'filter_goal_category', '', 'string');
        $this->setState('filter.goal_category', $goal_category);
        
                
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
        $query->from('`#__fitness_goals` AS a');

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
    
    public function addGoal($table, $data_encoded) {
        $ret['success'] = 1;
        $db = JFactory::getDbo();

        $user = &JFactory::getUser();
        $obj = json_decode($data_encoded);
        
        if(!$obj->primary_goal_id){
            $obj->user_id = $user->id;
        }

        if($obj->id) {
            $insert = $db->updateObject($table, $obj, 'id');
        } else {
            $insert = $db->insertObject($table, $obj, 'id');
        }

        if (!$insert) {
            $ret['success'] = false;
            $ret['message'] = $db->stderr();
        }
        
        $inserted_id = $db->insertid();
        if(!$inserted_id) {
            $inserted_id = $obj->id;
        }
 

        $result = array('status' => $ret, 'data' => $obj);

        return json_encode($result); 
    }
    
    
    
    public function populateGoals($data_encoded) {
        $model_backend = new FitnessModelgoals();

        $user = &JFactory::getUser();

        $data = $model_backend->getGraphData($user->id, $data_encoded);

        return $data; 
    }
    
    public function checkOverlapDate($data_encoded, $table) {

        $model_backend = new FitnessModelgoals();

        $data = $model_backend->checkOverlapDate($data_encoded, $table);

        return $data; 
    }
    
    
    public function commentEmail($data_encoded, $table) {

        $model_backend = new FitnessModelgoals();

        $data = $model_backend->commentEmail($data_encoded, $table);

        return $data; 
    }
    
    
    function getTrainingPeriods() {
        // Training Period List
        $db = JFactory::getDbo();
        $sql = "SELECT * FROM #__fitness_training_period WHERE state='1'";
        $db->setQuery($sql);
        if(!$db->query()) {
                JError::raiseError($db->getErrorMsg());
            }
        $training_periods = $db->loadObjectList();

        foreach ($training_periods as $item) {
            $color = '<div style="float:left;margin-right:5px;width:15px; height:15px;background-color:' . $item->color . '" ></div>';
            $name = '<div class="grey_title"> ' . $item->name . '</div>';
            $html .= $color . $name ;
        }
        return $html;
    }
    
    public function getPlanShoppingList($id) {
        $db = JFactory::getDbo();
        $sql = "SELECT * FROM #__fitness_nutrition_plan_shopping_list WHERE nutrition_plan_id='$id'";
        $db->setQuery($sql);
        if(!$db->query()) {
            JError::raiseError($db->getErrorMsg());
        }
        $result = $db->loadObjectList();
        return $result;
    }
    
    public function getUserPans($client_id, $nutrition_plan_id) {
        $db = JFactory::getDbo();
        $sql = "SELECT a.*, 
             (SELECT name FROM #__fitness_goal_categories WHERE id=gc.goal_category_id) primary_goal_name,
             mgn.name AS mini_goal_name,
             nf.name AS nutrition_focus_name,
             (SELECT calories FROM #__fitness_nutrition_plan_targets WHERE nutrition_plan_id = a.id AND type="  . $db->quote('heavy') .  ") calories,
             (SELECT protein FROM #__fitness_nutrition_plan_targets WHERE nutrition_plan_id = a.id AND type="  . $db->quote('heavy') .  ") protein,
             (SELECT fats FROM #__fitness_nutrition_plan_targets WHERE nutrition_plan_id = a.id AND type="  . $db->quote('heavy') .  ") fats,
             (SELECT carbs FROM #__fitness_nutrition_plan_targets WHERE nutrition_plan_id = a.id AND type="  . $db->quote('heavy') .  ") carbs
                         
            FROM #__fitness_nutrition_plan AS a
            LEFT JOIN #__fitness_goals AS gc ON gc.id = a.primary_goal
            LEFT JOIN #__fitness_goal_categories AS gn ON gn.id = gc.goal_category_id
            
            LEFT JOIN #__fitness_mini_goals AS mgc ON mgc.id = a.mini_goal
            LEFT JOIN #__fitness_mini_goal_categories AS mgn ON mgn.id = mgc.mini_goal_category_id
            

            LEFT JOIN #__fitness_nutrition_focus AS nf ON nf.id = a.nutrition_focus
           

            WHERE a.client_id='$client_id' AND  a.state='1'";
        
        if($nutrition_plan_id) {
            $sql .= " AND a.id <> '$nutrition_plan_id'";
        }
        
        $db->setQuery($sql);
        if(!$db->query()) {
            JError::raiseError($db->getErrorMsg());
        }
        $result = $db->loadObjectList();
        return $result;
    }
    
    public function populatePlan($data_encoded) {
        $status['success'] = 1;
        $data = json_decode($data_encoded);
        $id = $data->id;

        require_once JPATH_COMPONENT_SITE .  '/models/nutrition_diaryform.php';
        $nutrition_diaryform_model  = new FitnessModelNutrition_diaryForm();
       
        $plan_data = $nutrition_diaryform_model->getPlanData($id);
       
        if(!$plan_data['status']) {
            $status['success'] = 0;
            $status['message'] = $plan_data['message'];
            $result = array('status' => $status);
            return $result;
        }
        
        $plan_data = $plan_data['data'];
        
        $client_id = $plan_data->client_id;
        
        $client_trainers = $nutrition_diaryform_model->get_client_trainers($client_id);
        
        if(!$client_trainers['status']) {
            $status['success'] = 0;
            $status['message'] = $client_trainers['message'];
            $result = array('status' => $status);
            return $result;
        }
        $client_trainers = $client_trainers['data'];
        
        $plan_data->secondary_trainers = $client_trainers;

        $result = array('status' => $status, 'data' => $plan_data);
        return $result;
    }
    
    


}
