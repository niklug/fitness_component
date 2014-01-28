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
    
    
    public function getRecipes($table, $data) {
        $helper = $this->helper;
 
        $sort_by = $data->sort_by;
        $order_dirrection = $data->order_dirrection;
        
        $page = $data->page;
        $limit = $data->limit;
        
        $start = ($page - 1) * $limit;
        
        $filter_options = $data->filter_options;
        
        //get rid of empty element
        $filter_options = array_filter(explode(",",$filter_options));
        
        $recipe_variations_filter_options = $data->recipe_variations_filter_options;
        
        //get rid of empty element
        $recipe_variations_filter_options = array_filter(explode(",",$recipe_variations_filter_options));
        
        $current_page = $data->current_page;
        
        $state = $data->state;
                
        $user = &JFactory::getUser();
        $user_id = $user->id;
        
        $query = "SELECT a.*,";
        
        //get total number
        $query .= " (SELECT COUNT(*) FROM #__fitness_nutrition_recipes AS a ";

        $query .= " LEFT JOIN #__user_usergroup_map AS um ON um.user_id=a.created_by";
        $query .= " LEFT JOIN #__usergroups AS ug ON ug.id=um.group_id";
        
        if ($current_page == 'my_favourites') {
            $query .= " LEFT JOIN #__fitness_nutrition_recipes_favourites AS mf ON mf.recipe_id=a.id";
        }
        
        
        $query .= " WHERE a.state='$state' ";
        
        $filter_option1 = $filter_options[0];
        if($filter_options) {
            $query .= " AND ( FIND_IN_SET('$filter_option1', a.recipe_type) ";
            
            foreach ($filter_options as $filter_option) {
                $query .= " OR FIND_IN_SET('$filter_option', a.recipe_type)";
            }
            $query .= ")";
        }
        
        
        $recipe_variations_filter_options1 = $recipe_variations_filter_options[0];        
        if($recipe_variations_filter_options) {
            $query .= " AND ( FIND_IN_SET('$recipe_variations_filter_options1', a.recipe_variation) ";
            
            foreach ($recipe_variations_filter_options as $filter_option) {
                $query .= " OR FIND_IN_SET('$filter_option', a.recipe_variation)";
            }
            $query .= ")";
        }
        
        if(($current_page == 'my_recipes') OR ($current_page == 'trash_list')) {
            $query .= " AND a.created_by = '$user_id'";
        } else if ($current_page == 'my_favourites') {
            $query .= " AND mf.client_id='$user_id'";
        } else  {
            // except recipes created not by another clients
            $query .= " AND (um.group_id !='2' AND um.group_id NOT IN (SELECT id FROM #__usergroups WHERE parent_id='2'))";
            // by Business Profile 
            $trainers_group_id = FitnessHelper::getTrainersGroupId();
            $SUPERUSER_GROUP_ID = FitnessHelper::SUPERUSER_GROUP_ID;
            $query .= " AND (um.group_id ='$trainers_group_id' OR um.group_id ='$SUPERUSER_GROUP_ID')";
        }

          
        $query .= " ) items_total, ";
        //
        
        $query .= " (SELECT name FROM #__users WHERE id=a.created_by) author,"
                . " (SELECT name FROM #__users WHERE id=a.assessed_by) trainer,";
      
        $query .= " (SELECT ROUND(SUM(protein),2) FROM #__fitness_nutrition_recipes_meals WHERE recipe_id=a.id) AS protein,
                   (SELECT ROUND(SUM(fats),2) FROM #__fitness_nutrition_recipes_meals WHERE recipe_id=a.id) AS fats,
                   (SELECT ROUND(SUM(carbs),2) FROM #__fitness_nutrition_recipes_meals WHERE recipe_id=a.id) AS carbs,
                   (SELECT ROUND(SUM(calories),2) FROM #__fitness_nutrition_recipes_meals WHERE recipe_id=a.id) AS calories,
                   (SELECT ROUND(SUM(energy),2) FROM #__fitness_nutrition_recipes_meals WHERE recipe_id=a.id) AS energy,
                   (SELECT ROUND(SUM(saturated_fat),2) FROM #__fitness_nutrition_recipes_meals WHERE recipe_id=a.id) AS saturated_fat,
                   (SELECT ROUND(SUM(total_sugars),2) FROM #__fitness_nutrition_recipes_meals WHERE recipe_id=a.id) AS total_sugars,
                   (SELECT ROUND(SUM(sodium),2) FROM #__fitness_nutrition_recipes_meals WHERE recipe_id=a.id) AS sodium,";
                
        $query .= " (SELECT id FROM #__fitness_nutrition_recipes_favourites WHERE recipe_id=a.id AND client_id='$user_id') AS is_favourite";       
                
        $query .= " FROM  #__fitness_nutrition_recipes AS a";

        $query .= " LEFT JOIN #__user_usergroup_map AS um ON um.user_id=a.created_by";
        $query .= " LEFT JOIN #__usergroups AS ug ON ug.id=um.group_id";
        
        
        if ($current_page == 'my_favourites') {
            $query .= " LEFT JOIN #__fitness_nutrition_recipes_favourites AS mf ON mf.recipe_id=a.id";
        }
        
        $query .= " WHERE a.state='$state'";
        
        
        if($filter_options) {
            $query .= " AND ( FIND_IN_SET('$filter_option1', a.recipe_type) ";
            
            foreach ($filter_options as $filter_option) {
                $query .= " OR FIND_IN_SET('$filter_option', a.recipe_type)";
            }
            $query .= ")";
        }
        
        if($recipe_variations_filter_options) {
            $query .= " AND ( FIND_IN_SET('$recipe_variations_filter_options1', a.recipe_variation) ";
            
            foreach ($recipe_variations_filter_options as $filter_option) {
                $query .= " OR FIND_IN_SET('$filter_option', a.recipe_variation)";
            }
            $query .= ")";
        }
        
        
        
        if(($current_page == 'my_recipes') OR ($current_page == 'trash_list')) {
            $query .= " AND a.created_by = '$user_id'";
        } else if ($current_page == 'my_favourites') {
            $query .= " AND mf.client_id='$user_id'";
        } else {
            // except recipes created not by another clients
            $query .= " AND (um.group_id !='2' AND um.group_id NOT IN (SELECT id FROM #__usergroups WHERE parent_id='2'))";
            // by Business Profile 
            $trainers_group_id = FitnessHelper::getTrainersGroupId();
            $SUPERUSER_GROUP_ID = FitnessHelper::SUPERUSER_GROUP_ID;
            $query .= " AND (um.group_id ='$trainers_group_id' OR um.group_id ='$SUPERUSER_GROUP_ID')";
        }
        
        
        $query .= "  ORDER BY a." . $sort_by . " " . $order_dirrection 
                . " LIMIT $start, $limit";


        $data = FitnessHelper::customQuery($query, 1);

        //recipe types
        $i = 0;
        foreach ($data as $recipe) {
            if(!empty($recipe->recipe_type)) {
                $recipe_types_names = $helper->getRecipeNames($recipe->recipe_type);
                $data[$i]->recipe_types_names = $recipe_types_names;
                $i++;
            }
        }
        
        //recipe variation
        
        $i = 0;
        foreach ($data as $recipe) {
            if(!empty($recipe->recipe_variation)) {
                $recipe_variations_names = $helper->getRecipeVariationNames($recipe->recipe_variation);
                $data[$i]->recipe_variations_names = $recipe_variations_names;
                $i++;
            }
        }

        return $data;
    }
    
    
    public function getRecipeTypes() {
        $status['success'] = 1;
        
        $helper = $this->helper;
        
        $recipeTypes = $helper->getRecipeTypes();
        
        if(!$recipeTypes['success']) {
            $status['success'] = 0;
            $status['message'] = $recipeTypes['message'];
            return array( 'status' => $status);
        }
        
        $data = $recipeTypes['data'];

        $result = array( 'status' => $status, 'data' => $data);
        
        return $result;
    }
    
    public function getRecipeVariations() {
        
        $helper = $this->helper;
        
        return $helper->getRecipeVariations();
    }
    
    
    public function getRecipe($table, $data) {

        $helper = $this->helper;
        
        $id = $data->id;
        
        $state = $data->state;
        
        // get recipe 
        $data = $helper->getRecipe($id, $state);

        if(!$data->id){
            throw new Exception('error: no id');
        }
        
        // recipe types name
        if(!empty($data->recipe_type)) {
            $recipe_types_names = $helper->getRecipeNames($data->recipe_type);

            $data->recipe_types_names = $recipe_types_names;
        }
        
        // recipe variations name
        if(!empty($data->recipe_variation)) {
            $recipe_variations_names = $helper->getRecipeVariationNames($data->recipe_variation);

            $data->recipe_variations_names = $recipe_variations_names;
        }
        // recipe meals
        
        $recipe_meals = $helper->getRecipeMeals($id);
        
        $data->recipe_meals = $recipe_meals;

        return $data;
    }
    
    public function copyRecipe($table, $data_encoded) {
        $status['success'] = 1;
        
        $helper = $this->helper;
        
        $data = json_decode($data_encoded);
        
        $id = $data->id;
        
        $user = &JFactory::getUser();

        // get recipe 
        try {
            $recipe = $helper->getRecipeOriginalData($id);
        } catch (Exception $e) {
            $status['success'] = 0;
            $status['message'] = '"' . $e->getMessage() . '"';
            return array( 'status' => $status);
        }

        // save recipe 
        $created = FitnessHelper::getTimeCreated();
            
        $recipe->id = null;
        $recipe->status = '1';
        $recipe->created_by = $user->id;
        $recipe->created = $created;
        $recipe->assessed_by = null;
        
        try {
            $new_recipe_id = $helper->insertUpdateObj($recipe, '#__fitness_nutrition_recipes');
        } catch (Exception $e) {
            $status['success'] = 0;
            $status['message'] = '"' . $e->getMessage() . '"';
            return array( 'status' => $status);
        }

        
        // get recipe meals
        try {
            $recipe_meals = $helper->getRecipeMeals($id);
        } catch (Exception $e) {
            $status['success'] = 0;
            $status['message'] = '"' . $e->getMessage() . '"';
            return array( 'status' => $status);
        }
  
        
        
        
        // save recipe meals

        foreach ($recipe_meals as $meal) {
            try {
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
    

    
    
    public function favourite_recipe() {
            
        $method = JRequest::getVar('_method');

        if(!$method) {
            $method = $_SERVER['REQUEST_METHOD'];
        }

        $model = json_decode(JRequest::getVar('model'));
        
        $user = &JFactory::getUser();

        
        $data = new stdClass();

        
        $data->recipe_id = $model->id;   
        
        $data->client_id = $user->id;


        $table = '#__fitness_nutrition_recipes_favourites';

        $helper = new FitnessHelper();


        switch ($method) {
            case 'GET': // Get Item(s)

                break;
            case 'PUT': 
                //update
                $query = "SELECT id FROM $table WHERE client_id='$data->client_id' AND recipe_id='$data->recipe_id'";
                $exists = FitnessHelper::customQuery($query, 0);
                if($exists) return;
                
                $inserted_id = $helper->insertUpdateObj($data, $table);
                return $inserted_id;
                break;
            case 'POST': // Create
                
                break;
            case 'DELETE': // Delete Item
                $db = JFactory::getDBO();
                $id = JRequest::getVar('id', 0, '', 'INT');
                $query = "DELETE FROM $table WHERE client_id='$data->client_id' AND recipe_id='$id'";
                $db->setQuery($query);
                if (!$db->query()) {
                    throw new Exception($e->getMessage());
                }
                break;

            default:
                break;
        }
        return $model;
    }
    
    
    public function deleteRecipe($table, $data_encoded) {
        $status['success'] = 1;

        $data = json_decode($data_encoded);

        $db = JFactory::getDBO();
        
        $query = "DELETE FROM $table WHERE id='$data->id'";
        
        $db->setQuery($query);
        
        if (!$db->query()) {
            $status['success'] = 0;
            $status['message'] = '"' . $e->getMessage() . '"';
        }
        
        $this->deleteRecipeMedia($data->id);
        
        $result = array( 'status' => $status, 'data' => $data->id);
        
        return $result;
    }
    
    private function deleteRecipeMedia($id) {
        $image_upload_folder = JPATH_ROOT . DS . 'images' . DS . 'Recipe_Images' . DS;   
        $video_upload_folder = JPATH_ROOT . DS . 'images' . DS . 'Recipe_Videos' . DS;  
        
        array_map('unlink', glob($image_upload_folder . $id . ".*"));
        
        array_map('unlink', glob($video_upload_folder . $id . ".*"));
        
    }
    
    public function updateRecipe($table, $data_encoded) {
        $status['success'] = 1;

        $helper = $this->helper;
        
        $data = json_decode($data_encoded);
        
        $data->instructions = html_entity_decode(urldecode($data->instructions), ENT_COMPAT, "UTF-8");
        
        $db = JFactory::getDBO();
        
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
    
   

    public function getIngredients($table, $data_encoded) {
        $status['success'] = 1;

        $helper = $this->helper;
        
        $data = json_decode($data_encoded);
        
        $page = $data->page;
        $limit = $data->limit;
        
        $start = ($page - 1) * $limit;
        
        $db = JFactory::getDBO();
        
        if($data->search) {

            $search = $db->Quote('%' . $db->escape($data->search, true) . '%');

            $query .= "SELECT a.*";

            $query .= " FROM $table AS a "
                    . " WHERE a.ingredient_name LIKE $search"
                    . " AND a.state='1'"
                    . "  ORDER BY a.ingredient_name" 
                    . " LIMIT $start, $limit";


            try {
                $ingredients = FitnessHelper::customQuery($query, 1);
            } catch (Exception $e) {
                $status['success'] = 0;
                $status['message'] = '"' . $e->getMessage() . '"';
                return array( 'status' => $status);
            }
            
        
            $query = "SELECT COUNT(id) ";

            $query .= " FROM $table  WHERE ingredient_name LIKE $search AND state='1'";

            try {
                $items_total = FitnessHelper::customQuery($query, 0);
            } catch (Exception $e) {
                $status['success'] = 0;
                $status['message'] = '"' . $e->getMessage() . '"';
                return array( 'status' => $status);
            }
        }
        
        //$items_total = 100;
        
        $data = array('ingredients' => $ingredients, 'items_total' => $items_total);

        $result = array( 'status' => $status, 'data' => $data);
        
        return $result;
    }
    
     public function updateIngredient($table, $data_encoded) {
        $status['success'] = 1;

        $helper = $this->helper;
        
        $data = json_decode($data_encoded);
        
        $data->description = html_entity_decode(urldecode($data->description), ENT_COMPAT, "UTF-8");
        
        $db = JFactory::getDBO();
        
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
    
    
    public function recipes() {
            
        $method = JRequest::getVar('_method');

        if(!$method) {
            $method = $_SERVER['REQUEST_METHOD'];
        }

        $model = json_decode(JRequest::getVar('model'));
        
        $id = JRequest::getVar('id', 0, '', 'INT');
        
        $data = new stdClass();
        $data->id = $id;   
        $data->sort_by = JRequest::getVar('sort_by'); 
        $data->order_dirrection = JRequest::getVar('order_dirrection'); 
        $data->page = JRequest::getVar('page'); 
        $data->limit = JRequest::getVar('limit'); 
        $data->state = JRequest::getVar('state'); 
        $data->filter_options = JRequest::getVar('filter_options'); 
        $data->recipe_variations_filter_options = JRequest::getVar('recipe_variations_filter_options'); 
        $data->current_page= JRequest::getVar('current_page'); 


        $table = '#__fitness_nutrition_recipes';

        $helper = new FitnessHelper();


        switch ($method) {
            case 'GET': // Get Item(s)
                if($id) {
                    $data = $this->getRecipe($table, $data);
                    return $data;
                }
                $data = $this->getRecipes($table, $data);
                return $data;
                break;
            case 'PUT': 
                //update
                break;
            case 'POST': // Create

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
    
    
}
