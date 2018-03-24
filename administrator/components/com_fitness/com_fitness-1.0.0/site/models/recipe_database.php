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
        
        $status = $data->status;
        
        $recipe_name = $data->recipe_name;
        
        $created_by_name = $data->created_by_name;

        $user_id = $data->user_id;
        
        //
        $is_superuser = FitnessHelper::is_superuser($user_id);
        
        $is_simple_trainer = FitnessHelper::is_simple_trainer($user_id);
        
        $is_trainer_administrator = FitnessHelper::is_trainer_administrator($user_id);
        
        $is_simple_trainer = FitnessHelper::is_simple_trainer($user_id);
        
        $is_client = FitnessHelper::is_client($user_id);
        
        $business_profile = $helper->getBusinessProfileId($user_id);
        
        $business_profile_id = $business_profile['data'];
        //

        
        $SUPERUSER_GROUP_ID = FitnessHelper::SUPERUSER_GROUP_ID;
        
        $query = "SELECT a.*,";
        $query .= " (SELECT user_id  FROM #__fitness_clients WHERE user_id=a.created_by LIMIT 1) created_by_client,";
        //get total number
        $query .= " (SELECT COUNT(*) FROM $table AS a ";
        $query .= " LEFT JOIN #__user_usergroup_map AS um ON um.user_id=a.created_by";
        $query .= " LEFT JOIN #__usergroups AS ug ON ug.id=um.group_id";
        
        if ($current_page == 'my_favourites') {
            $query .= " LEFT JOIN #__fitness_nutrition_recipes_favourites AS mf ON mf.item_id=a.id";
        }
        
        
        $query .= " WHERE 1 ";
        
        if($state != '*') {
            $query .= " AND a.state='$state' ";
        }
        
        if($status) {
            $query .= " AND a.status='$status' ";
        }
        
        if($recipe_name) {
            $query .= " AND a.recipe_name LIKE '%$recipe_name%' ";
        }
        
        //search by created_by name
        if ($created_by_name) {
            $sql = " SELECT GROUP_CONCAT(id) FROM #__users WHERE name LIKE '%$created_by_name%' ";

            $owner_ids = FitnessHelper::customQuery($sql, 0);

            if($owner_ids) {
                $query .= " AND a.created_by IN ($owner_ids)";
            }
        }
        
        if (!empty($data->date_from)) {
            $query .= " AND a.created >= '$data->date_from'";
        }
        
        if (!empty($data->date_to)) {
            $query .= " AND a.created <= '$data->date_to'";
        }
        
        if(!empty($data->business_profile_id)) {
            $query .= " AND a.business_profile_id='$data->business_profile_id' ";
        }
        
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
        
        
        if($is_client) {
            if(($current_page == 'my_recipes') OR ($current_page == 'trash_list')) {
                $query .= " AND a.created_by = '$user_id'";
            } else if ($current_page == 'my_favourites') {
                $query .= " AND mf.client_id='$user_id'";
            } else if($current_page == 'meal_recipes') {
                // by Business Profile 
                $query .= " AND (a.business_profile_id ='$business_profile_id' OR a.created_by = '$user_id')";
            } else {

                // except recipes created  by another clients
                $query .= " AND (um.group_id !='2' AND um.group_id NOT IN (SELECT id FROM #__usergroups WHERE parent_id='2'))";
                // by Business Profile 
                $query .= " AND (a.business_profile_id ='$business_profile_id')";
            }
        }
        
        /*
         * if a 'Business Admin' or 'Simple Trainer' is logged-in, they can see Recipes created by...
            - Super Users
            - Any other Business Admin or Trainer (from their own business)
        */
        /*
         * If a Client has created a Recipe, only his Primary/Secondary Trainer can see the Recipe
         */
        
        
        if($is_trainer_administrator) {
            $query .= " AND (a.business_profile_id='$business_profile_id' OR um.group_id ='$SUPERUSER_GROUP_ID')";
            
        }
        
        if($is_simple_trainer) {
            $query .= " AND (a.business_profile_id='$business_profile_id' OR um.group_id ='$SUPERUSER_GROUP_ID')";
            
            $query .= " AND (a.created_by IN (SELECT user_id FROM #__fitness_clients WHERE primary_trainer='$user_id' OR FIND_IN_SET('$user_id' , other_trainers)) OR um.group_id ='$SUPERUSER_GROUP_ID' OR a.created_by='$user_id')";
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
                
        $query .= " (SELECT id FROM #__fitness_nutrition_recipes_favourites WHERE item_id=a.id AND client_id='$user_id') AS is_favourite,"; 
        $query .= " (SELECT user_id FROM #__user_usergroup_map WHERE user_id=a.created_by AND group_id='$SUPERUSER_GROUP_ID') AS created_by_superuser,";  
        // if recipe created by client logged associated trainer
        $query .= " (SELECT user_id FROM #__fitness_clients WHERE (primary_trainer='$user_id' OR FIND_IN_SET('$user_id', other_trainers)) AND user_id=a.created_by AND state='1') is_associated_trainer,";
        
        $query .=  " (SELECT GROUP_CONCAT(name) FROM #__fitness_recipe_types WHERE "
                . " FIND_IN_SET(id, (SELECT recipe_type FROM $table WHERE id =a.id))) recipe_types_names, ";
        
        $query .=  " (SELECT GROUP_CONCAT(name) FROM #__fitness_recipe_variations WHERE "
                . " FIND_IN_SET(id, (SELECT recipe_variation FROM $table WHERE id =a.id))) recipe_variations_names ";
        
                
        $query .= " FROM  $table AS a";
        $query .= " LEFT JOIN #__user_usergroup_map AS um ON um.user_id=a.created_by";
        $query .= " LEFT JOIN #__usergroups AS ug ON ug.id=um.group_id";
        
        
        if ($current_page == 'my_favourites') {
            $query .= " LEFT JOIN #__fitness_nutrition_recipes_favourites AS mf ON mf.item_id=a.id";
        }
        
        $query .= " WHERE 1 ";
        
        if($state != '*') {
            $query .= " AND a.state='$state' ";
        }
        
        if($status) {
            $query .= " AND a.status='$status' ";
        }
        
        if($recipe_name) {
            $query .= " AND a.recipe_name LIKE '%$recipe_name%' ";
        }
        
        //search by created_by name
        if ($created_by_name) {
            $sql = " SELECT GROUP_CONCAT(id) FROM #__users WHERE name LIKE '%$created_by_name%' ";

            $owner_ids = FitnessHelper::customQuery($sql, 0);

            if($owner_ids) {
                $query .= " AND a.created_by IN ($owner_ids)";
            }
        }
        
        if (!empty($data->date_from)) {
            $query .= " AND a.created >= '$data->date_from'";
        }
        
        if (!empty($data->date_to)) {
            $query .= " AND a.created <= '$data->date_to'";
        }
        
        if(!empty($data->business_profile_id)) {
            $query .= " AND a.business_profile_id='$data->business_profile_id' ";
        }
        
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
        
        
        if($is_client) {
            if(($current_page == 'my_recipes') OR ($current_page == 'trash_list')) {
                $query .= " AND a.created_by = '$user_id'";
            } else if ($current_page == 'my_favourites') {
                $query .= " AND mf.client_id='$user_id'";
            } else if($current_page == 'meal_recipes') {
                // by Business Profile 
                $query .= " AND (a.business_profile_id ='$business_profile_id' OR a.created_by = '$user_id')";
            } else {
                // except recipes created not by another clients
                $query .= " AND (um.group_id !='2' AND um.group_id NOT IN (SELECT id FROM #__usergroups WHERE parent_id='2'))";
                // by Business Profile 
                $query .= " AND (a.business_profile_id ='$business_profile_id')";
            }
        }

        if($is_trainer_administrator) {
            $query .= " AND (a.business_profile_id='$business_profile_id' OR um.group_id ='$SUPERUSER_GROUP_ID')";
            
        }

        if($is_simple_trainer) {
            $query .= " AND (a.business_profile_id='$business_profile_id' OR um.group_id ='$SUPERUSER_GROUP_ID')";
            
            $query .= " AND (a.created_by IN (SELECT user_id FROM #__fitness_clients WHERE primary_trainer='$user_id' OR FIND_IN_SET('$user_id' , other_trainers)) OR um.group_id ='$SUPERUSER_GROUP_ID' OR a.created_by='$user_id')";
        }
        
        
        $query .= "  ORDER BY " . $sort_by . " " . $order_dirrection 
                . " LIMIT $start, $limit";


        $data = FitnessHelper::customQuery($query, 1);
        
        $i = 0;
        foreach ($data as $recipe) {
            $recipe_meals = $helper->getRecipeMeals($recipe->id);
            $data[$i]->recipe_meals = $recipe_meals;
            $i++;
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
        
        if(!$id) {
            throw new Exception("Error: no id");
        }
        
        $user = &JFactory::getUser();
        
        $user_id = $user->id;

        $recipe = $helper->getRecipeOriginalData($id);

        // save recipe 
        $created = FitnessHelper::getTimeCreated();
            
        $recipe->id = null;
        $recipe->status = '1';
        $recipe->created_by = $user_id;
        $recipe->created = $created;
        $recipe->assessed_by = null;
        
        $business_profile = $helper->getBusinessProfileId($user_id);
        $business_profile_id = $business_profile['data'];
        $recipe->business_profile_id =  $business_profile_id;
        
        $new_recipe_id = $helper->insertUpdateObj($recipe, '#__fitness_nutrition_recipes');
        
        // get recipe meals
        $recipe_meals = $helper->getRecipeMeals($id);

        // save recipe meals
        foreach ($recipe_meals as $meal) {
            $meal->id = null;
            $meal->recipe_id = $new_recipe_id;
            $inserted_id = $helper->insertUpdateObj($meal, '#__fitness_nutrition_recipes_meals');
        }
        
        $result = array( 'status' => $status, 'data' => $recipe);
        
        return $result;
    }
    

    
    private function deleteRecipeMedia($id) {
        $image_upload_folder = JPATH_ROOT . DS . 'images' . DS . 'Recipe_Images' . DS;   
        $video_upload_folder = JPATH_ROOT . DS . 'images' . DS . 'Recipe_Videos' . DS;  
        
        array_map('unlink', glob($image_upload_folder . $id . ".*"));
        
        array_map('unlink', glob($video_upload_folder . $id . ".*"));
        
    }

    
    public function recipes() {
            
        $method = JRequest::getVar('_method');

        if(!$method) {
            $method = $_SERVER['REQUEST_METHOD'];
        }

        $model = json_decode(JRequest::getVar('model'));
        
        $id = JRequest::getVar('id', 0, '', 'INT');

        $table = '#__fitness_nutrition_recipes';

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
                $data->filter_options = JRequest::getVar('filter_options'); 
                $data->recipe_variations_filter_options = JRequest::getVar('recipe_variations_filter_options'); 
                $data->current_page= JRequest::getVar('current_page'); 
                $data->recipe_name= JRequest::getVar('recipe_name');
                $data->created_by_name= JRequest::getVar('created_by_name');
                $data->date_from = JRequest::getVar('date_from'); 
                $data->date_to = JRequest::getVar('date_to'); 
                $data->business_profile_id = JRequest::getVar('business_profile_id'); 
                
                
                $user_id = JRequest::getVar('client_id');
 
                if(!$user_id) {
                    $user_id = JFactory::getUser()->id;
                }

                $data->user_id = $user_id;

                if($id) {
                    $data = $this->getRecipe($table, $data);
                    return $data;
                }
                $data = $this->getRecipes($table, $data);
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
    
    
    public function ingredients() {
            
        $method = JRequest::getVar('_method');

        if(!$method) {
            $method = $_SERVER['REQUEST_METHOD'];
        }

        $model = json_decode(JRequest::getVar('model'));
        $id = JRequest::getVar('id', 0, '', 'INT');
        
        $table = '#__fitness_nutrition_database';

        $helper = new FitnessHelper();


        switch ($method) {
            case 'GET': // Get Item(s)
                $search = JRequest::getVar('search'); 
                
                $page = JRequest::getVar('page'); 
                $limit = JRequest::getVar('limit'); 
                $start = ($page - 1) * $limit;

                if(!$search) return;
                
                $db = JFactory::getDBO();

                $search = $db->Quote('%' . $db->escape($search, true) . '%');

                $query .= "SELECT a.*, "
                        . " (SELECT COUNT(id) FROM $table  WHERE ingredient_name LIKE $search AND state='1') items_total";

                $query .= " FROM $table AS a "
                        . " WHERE a.ingredient_name LIKE $search"
                        . " AND a.state='1'"
                        . "  ORDER BY a.ingredient_name" 
                        . " LIMIT $start, $limit";

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
                $id = $helper->deleteRow($id, $table);
                break;

            default:
                break;
        }

        $model->id = $id;

        return $model;
    }
    
    public function ingredient_categories() {
        $helper = $this->helper;
        return $helper->getNutritionDatabaseCategories();
    }
    
    public function recipe_ingredients() {
        $method = JRequest::getVar('_method');

        if(!$method) {
            $method = $_SERVER['REQUEST_METHOD'];
        }

        $model = json_decode(JRequest::getVar('model'));
        
        $id = JRequest::getVar('id', 0, '', 'INT');
        

        $table = '#__fitness_nutrition_recipes_meals';
     

        $helper = new FitnessHelper();

        switch ($method) {
            case 'GET': // Get Item(s)
                $recipe_id = JRequest::getVar('recipe_id');
                
                $query .= "SELECT a.* FROM $table AS a";
                
                $query .= " WHERE 1 ";
   
                if($id) {
                    $query .= " AND a.id='$id' ";
                }
            
                
                if($recipe_id) {
                    $query .= " AND a.recipe_id='$recipe_id' ";
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
    
}
