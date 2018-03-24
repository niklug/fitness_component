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
//require_once JPATH_COMPONENT_ADMINISTRATOR . DS . 'models' . DS . 'goals.php';

require_once  JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_fitness' . DS .'helpers' . DS . 'fitness.php';

class FitnessModelGoals extends JModelList {

    public function __construct($config = array()) {
        parent::__construct($config);
    }


    protected function populateState($ordering = null, $direction = null) {
        
    }


    protected function getStoreId($id = '') {
        return parent::getStoreId($id);
    }

    protected function getListQuery() {

    }

    public function getItems() {
        $items = parent::getItems();
        
        return $items;
    }
    
    public function primary_goals() {
            
        $method = JRequest::getVar('_method');

        if(!$method) {
            $method = $_SERVER['REQUEST_METHOD'];
        }
       
        $model = json_decode(JRequest::getVar('model','','','',JREQUEST_ALLOWHTML));

        $id = JRequest::getVar('id', 0, '', 'INT');
        
        $table = '#__fitness_goals';

        $helper = new FitnessHelper();

        switch ($method) {
            case 'GET': // Get Item(s)
                $data = new stdClass();
                $data->id = $id;  
                $data->sort_by = JRequest::getVar('sort_by'); 
                $data->order_dirrection = JRequest::getVar('order_dirrection'); 
                $data->page = JRequest::getVar('page'); 
                $data->limit = JRequest::getVar('limit'); 
                $data->status = JRequest::getVar('status'); 
                $data->state = JRequest::getVar('state'); 
                $data->list_type = JRequest::getVar('list_type'); 
                $data->user_id = JRequest::getVar('user_id'); 
                
                $data->start_from = JRequest::getVar('start_from'); 
                $data->start_to = JRequest::getVar('start_to'); 
                $data->end_from = JRequest::getVar('end_from'); 
                $data->end_to = JRequest::getVar('end_to'); 

                $data = $this->getPrimaryGoals($data);
                
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
    
    public function getPrimaryGoals($data) {
        $helper = new FitnessHelper();
        
        $table = '#__fitness_goals';
        
        $page = $data->page;
        
        $limit = $data->limit;
        
        $start = ($page - 1) * $limit;
        
        $sort_by = $data->sort_by;
        
        $order_dirrection = $data->order_dirrection;
        
        $state = $data->state;
         
        $status = $data->status;
        
        $start_from = $data->start_from ;
        $start_to = $data->start_to ;
        $end_from = $data->end_from ;
        $end_to = $data->end_to ;
        
        
        $id = $data->id;
       
        $list_type = $data->list_type;
        
        $user_id = $data->user_id;
   
        $current_date = $helper->getDateCreated();

        $db = &JFactory::getDBo();
        
        $query = "SELECT pg.*,"; 
        
        $query .= " (SELECT user_id  FROM #__fitness_clients WHERE user_id=pg.user_id LIMIT 1) created_by_client,";
        
        //get total number
        if(!$id) {
            $query .= " (SELECT COUNT(*) FROM $table ";

            $query .= " WHERE 1 ";

            if($user_id) {
                $query .= " AND user_id='$user_id'";
            }

            if(!empty($state) && $state != '*') {
                $query .= " AND state='$state'";
            }

            if($list_type == 'previous') {
                $query .= " AND ( deadline < " . $db->quote($current_date);
                $query .= " OR (start_date <= " . $db->quote($current_date);
                $query .= " AND deadline > " . $db->quote($current_date) . " )) ";
            }

            if($list_type == 'current') {
                $query .= " AND deadline > " . $db->quote($current_date);
            }

            if($list_type == 'current_primary_goal') {
                $query .= " AND start_date <= " . $db->quote($current_date);
                $query .= " AND deadline > " . $db->quote($current_date);
            }
            
            if($status) {
                $query .= " AND status='$status'";
            }
            
            if($start_from) {
                $query .= " AND start_date >= " . $db->quote($start_from);
            }
            if($start_to) {
                $query .= " AND start_date <= " . $db->quote($start_to);
            }
            if($end_from) {
                $query .= " AND deadline >= " . $db->quote($end_from);
            }
            if($end_to) {
                $query .= " AND deadline <= " . $db->quote($end_to);
            }

            $query .= " ) items_total, ";
        }
        //end get total number
        
        $query .= " (SELECT name FROM #__users WHERE id=pg.user_id) client_name,";
        
        $query .= " (SELECT name FROM #__fitness_goal_categories WHERE id=pg.goal_category_id) primary_goal_name";
        
        
        
        $query .= " FROM  #__fitness_goals AS pg";
        
        $query .= " WHERE 1";
        
        if($user_id) {
            $query .= " AND pg.user_id='$user_id'";
        }
        
        if(!empty($state) && $state != '*') {
            $query .= " AND pg.state='$state'";
        }

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
        
        if($status) {
            $query .= " AND pg.status='$status'";
        }
        
        if($start_from) {
            $query .= " AND pg.start_date >= " . $db->quote($start_from);
        }
        if($start_to) {
            $query .= " AND pg.start_date <= " . $db->quote($start_to);
        }
        if($end_from) {
            $query .= " AND pg.deadline >= " . $db->quote($end_from);
        }
        if($end_to) {
            $query .= " AND pg.deadline <= " . $db->quote($end_to);
        }
            
        
        $query_type = 1;
        if($id) {
            $query .= " AND pg.id='$id' ";
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
         
        return $items;
    }
    
    public function mini_goals() {
        $method = JRequest::getVar('_method');

        if(!$method) {
            $method = $_SERVER['REQUEST_METHOD'];
        }
       
        $model = json_decode(JRequest::getVar('model','','','',JREQUEST_ALLOWHTML));

        $id = JRequest::getVar('id', 0, '', 'INT');
        
        $table = '#__fitness_mini_goals';

        $helper = new FitnessHelper();

        switch ($method) {
            case 'GET': // Get Item(s)
                $data = new stdClass();
                $data->id = $id;  
                $data->user_id = JRequest::getVar('user_id'); 
                $data->primary_goal_id = JRequest::getVar('primary_goal_id'); 
                $data->state = JRequest::getVar('state', '1'); 
                $data = $this->getMiniGoals($data);
                
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
    
    
    public function getMiniGoals($data) {
        $helper = new FitnessHelper();
        
        $table = '#__fitness_mini_goals';

        $id = $data->id;

        $user_id = $data->user_id;
        
        $state = $data->state;
        
        $primary_goal_id = $data->primary_goal_id;
   
        $current_date = $helper->getDateCreated();

        $db = &JFactory::getDBo();
        
        $query = "SELECT mg.*,";
        
        $query .= " (SELECT user_id  FROM #__fitness_clients WHERE user_id=mg.user_id LIMIT 1) created_by_client,";
        
        $query .= " (SELECT name FROM #__users WHERE id=mg.user_id) client_name,";
        $query .= " (SELECT name FROM #__fitness_mini_goal_categories WHERE id=mg.mini_goal_category_id) mini_goal_name,";
        $query .= " (SELECT color FROM #__fitness_training_period WHERE id=mg.training_period_id) training_period_color,";
        $query .= " mg.start_date AS start_date,";
        $query .= " tp.color AS training_period_color,";
        $query .= " tp.name AS training_period_name";

        $query .= " FROM  #__fitness_mini_goals AS mg";
        
        $query .= " LEFT JOIN #__fitness_training_period AS tp ON tp.id=mg.training_period_id";
         
        $query .= " WHERE 1";
        
        if($user_id) {
            $query .= " AND mg.user_id='$user_id'";
        }
  
        
        if(!empty($state) && $state != '*') {
            $query .= " AND mg.state='$state'";
        }
        
        if($list_type == 'previous') {
            $query .= " AND ( mg.deadline < " . $db->quote($current_date);
            $query .= " OR (mg.start_date <= " . $db->quote($current_date);
            $query .= " AND mg.deadline > " . $db->quote($current_date) . " )) ";
        }
        
        if($list_type == 'current') {
            $query .= " AND mg.deadline > " . $db->quote($current_date);
        }
        
        if($primary_goal_id) {
            $query .= " AND mg.primary_goal_id='$primary_goal_id'";
        }
        
        $query_type = 1;
        if($id) {
            $query .= " AND mg.id='$id' ";
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
        
        return $items;
    }
    
   
    
}
