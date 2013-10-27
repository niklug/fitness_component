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
 * Methods supporting a list of Fitness_goals records.
 */
class FitnessModelrecipe_database extends JModelList {

    public function __construct($config = array()) {
        
        $this->helper = new FitnessHelper();
        
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
    
    /**********************************************************************/
    
    
    public function getRecipes($table, $data_encoded) {
        $status['success'] = 1;
        
        $helper = $this->helper;
        
        $data = json_decode($data_encoded);
        
        $page = $data->page;
        $limit = $data->limit;
        
        $start = ($page - 1) * $limit;
        
        $query = "SELECT a.*,"
                . " (SELECT COUNT(*) FROM #__fitness_nutrition_recipes  WHERE state='1') items_total, "
                . " (SELECT name FROM #__users WHERE id=a.created_by) author,"
                . " (SELECT name FROM #__users WHERE id=a.reviewed_by) trainer"
                . " FROM  #__fitness_nutrition_recipes AS a"
                . " WHERE a.state='1'"
                . " ORDER BY a.recipe_name"
                . " LIMIT $start, $limit";

                
        try {
            $data = FitnessHelper::customQuery($query, 1);
        } catch (Exception $e) {
            $status['success'] = 0;
            $status['message'] = '"' . $e->getMessage() . '"';
        }
        

        $i = 0;
        foreach ($data as $recipe) {
            try {
                $recipe_types_names = $this->getRecipeNames($recipe->recipe_type);
            } catch (Exception $e) {
                $status['success'] = 0;
                $status['message'] = '"' . $e->getMessage() . '"';
            }
            $data[$i]->recipe_types_names = $recipe_types_names;
            $i++;
        }

        $result = array( 'status' => $status, 'data' => $data);
        
        return $result;
    }
    
    

    function getRecipeNames($ids) {
        $query = "SELECT name FROM #__fitness_recipe_types WHERE id IN ($ids) AND state='1'";
        return FitnessHelper::customQuery($query, 3);
    }
    
    
    
}
