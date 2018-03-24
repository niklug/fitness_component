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

class FitnessModelassessments extends JModelList {

    public function __construct($config = array()) {
        parent::__construct($config);
    }

    protected function populateState($ordering = null, $direction = null) {
       
    }


    protected function getStoreId($id = '') {

    }

    protected function getListQuery() {
        
    }

    public function getItems() {

    }
    
    public function assessment_photos() {
            
        $method = JRequest::getVar('_method');

        if(!$method) {
            $method = $_SERVER['REQUEST_METHOD'];
        }

        $model = json_decode(JRequest::getVar('model'));
        
        $id = JRequest::getVar('id', 0, '', 'INT');
        
        $item_id = JRequest::getVar('item_id', 0, '', 'INT');
  
        
        $table = '#__fitness_assessments_photos';
        
        

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
    
}
