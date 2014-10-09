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
class FitnessModelClient_summary extends JModelList {

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

       
    }

    public function getItems() {
        return parent::getItems();
    }
   
    
    public function notifications() {
        $method = JRequest::getVar('_method');

        if(!$method) {
            $method = $_SERVER['REQUEST_METHOD'];
        }

        $model = json_decode(JRequest::getVar('model'));
        
        $id = JRequest::getVar('id', 0, '', 'INT');
        
        $table = '#__fitness_notifications';

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
                $data->user_id = JRequest::getVar('user_id'); 
                $data->created_by = JRequest::getVar('created_by'); 
                $data->readed = JRequest::getVar('readed'); 
                $data->date_from = JRequest::getVar('date_from'); 
                $data->date_to = JRequest::getVar('date_to'); 

                
                $data = $this->getNotifications($data);

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
    
    public function getNotifications($data) {
        $id = $data->id; 
        $sort_by = $data->sort_by; 
        $order_dirrection = $data->order_dirrection; 
        $page = $data->page; 
        $limit = $data->limit; 
        
        $user_id = $data->user_id; 
        $created_by = $data->created_by; 
        $readed = $data->readed; 
        
        
        $helper = new FitnessHelper();
        
        $start = ($page - 1) * $limit;
        
        $table = '#__fitness_notifications';
        
        $query .= "SELECT a.*, ";
        
        $query .= " (SELECT name FROM #__users WHERE id=a.created_by) created_by_name,";
        $query .= " (SELECT name FROM #__users WHERE id=a.user_id) user_name,";

        //get total number
        $query .= " (SELECT COUNT(*) FROM $table AS a ";
        $query .= " WHERE 1 ";
        
        if($user_id) {
            $query .= " AND a.user_id='$user_id' ";
        }
        
        if (!empty($data->date_from)) {
            $query .= " AND a.created >= '$data->date_from'";
        }

        if (!empty($data->date_to)) {
            $query .= " AND a.created <= '$data->date_to'";
        }
        
        $query .= " ) items_total ";
        
        //end het items total
   
        $query .= "  FROM $table AS a";

        $query .= " WHERE 1 ";

        if($id) {
            $query .= " AND a.id='$id' ";
        }
        
        if($user_id) {
            $query .= " AND a.user_id='$user_id' ";
        }
        
        if (!empty($data->date_from)) {
            $query .= " AND a.created >= '$data->date_from'";
        }

        if (!empty($data->date_to)) {
            $query .= " AND a.created <= '$data->date_to'";
        }

        $query_method = 1;

        if($id) {
            $query_method = 2;
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
        $data = FitnessHelper::customQuery($query, $query_method);

        return $data;
    }
}
