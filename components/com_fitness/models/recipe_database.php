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
        
        $sort_by = $data->sort_by;
        $order_dirrection = $data->order_dirrection;
        
        $page = $data->page;
        $limit = $data->limit;
        
        $start = ($page - 1) * $limit;
        
        $filter_options = $data->filter_options;
        
        //get rid of empty element
        $filter_options = array_filter(explode(",",$filter_options));
        
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
        

        if($filter_options) {
            $query .= " AND ( FIND_IN_SET('$filter_option1', a.recipe_type) ";
            
            foreach ($filter_options as $filter_option) {
                $query .= " OR FIND_IN_SET('$filter_option', a.recipe_type)";
            }
            $query .= ")";
        }
        
        if(($current_page == 'my_recipes') OR ($current_page == 'trash_list')) {
            $query .= " AND a.created_by = '$user_id'";
        } else if ($current_page == 'my_favourites') {
            $query .= " AND mf.client_id='$user_id'";
        } else  {
            $query .= " AND (um.group_id !='2' AND um.group_id NOT IN (SELECT id FROM #__usergroups WHERE parent_id='2'))";
        }

          
        $query .= " ) items_total, ";
        //
        
        $query .= " (SELECT name FROM #__users WHERE id=a.created_by) author,"
                . " (SELECT name FROM #__users WHERE id=a.reviewed_by) trainer,";
      
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
        
        $filter_option1 = $filter_options[0];
        if($filter_options) {
            $query .= " AND ( FIND_IN_SET('$filter_option1', a.recipe_type) ";
            
            foreach ($filter_options as $filter_option) {
                $query .= " OR FIND_IN_SET('$filter_option', a.recipe_type)";
            }
            $query .= ")";
        }
        
        
        
        if(($current_page == 'my_recipes') OR ($current_page == 'trash_list')) {
            $query .= " AND a.created_by = '$user_id'";
        } else if ($current_page == 'my_favourites') {
            $query .= " AND mf.client_id='$user_id'";
        } else {
            $query .= " AND (um.group_id !='2' AND um.group_id NOT IN (SELECT id FROM #__usergroups WHERE parent_id='2'))";
        }
        
        
                
        $query .= "  ORDER BY a." . $sort_by . " " . $order_dirrection 
                . " LIMIT $start, $limit";

                
        try {
            $data = FitnessHelper::customQuery($query, 1);
        } catch (Exception $e) {
            $status['success'] = 0;
            $status['message'] = '"' . $e->getMessage() . '"';
            return array( 'status' => $status);
        }
        
        if($recipe->recipe_type) {
            $i = 0;
            foreach ($data as $recipe) {
                try {
                    $recipe_types_names = $this->getRecipeNames($recipe->recipe_type);
                } catch (Exception $e) {
                    $status['success'] = 0;
                    $status['message'] = '"' . $e->getMessage() . '"';
                    return array( 'status' => $status);
                }
                $data[$i]->recipe_types_names = $recipe_types_names;
                $i++;
            }
        }
        $result = array( 'status' => $status, 'data' => $data);
        
        return $result;
    }
    
    

    function getRecipeNames($ids) {
        $query = "SELECT name FROM #__fitness_recipe_types WHERE id IN ($ids) AND state='1'";
        return FitnessHelper::customQuery($query, 3);
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
    
    
    public function getRecipe($table, $data_encoded) {
        $status['success'] = 1;
        
        $helper = $this->helper;
        
        $data = json_decode($data_encoded);
        
        $id = $data->id;
        
        $state = $data->state;
        
        // get recipe 
        try {
            $data = $helper->getRecipe($id, $state);
        } catch (Exception $e) {
            $status['success'] = 0;
            $status['message'] = '"' . $e->getMessage() . '"';
            return array( 'status' => $status);
        }
        
        if(!$data->id){
            return array( 'status' => $status, 'data' => null);
        }
        
        // recipe types name
        if($data->recipe_type) {
            try {
                $recipe_types_names = $this->getRecipeNames($data->recipe_type);
            } catch (Exception $e) {
                $status['success'] = 0;
                $status['message'] = '"' . $e->getMessage() . '"';
                return array( 'status' => $status);
            }
            $data->recipe_types_names = $recipe_types_names;
        }
        // recipe meals
        
        try {
            $recipe_meals = $helper->getRecipeMeals($id);
        } catch (Exception $e) {
            $status['success'] = 0;
            $status['message'] = '"' . $e->getMessage() . '"';
            return array( 'status' => $status);
        }
        
        $data->recipe_meals = $recipe_meals;
        
        $result = array( 'status' => $status, 'data' => $data);
        
        return $result;
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
        $recipe->reviewed_by = null;
        
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
    

    public function addFavourite($table, $data_encoded) {
        $status['success'] = 1;
        
        $helper = $this->helper;
        
        $data = json_decode($data_encoded);
        
        $user = &JFactory::getUser();
          
        $recipe->client_id = $user->id;
        $recipe->recipe_id = $data->recipe_id;
        
        //check if exists
        $query = "SELECT id FROM $table WHERE client_id='$recipe->client_id' AND recipe_id='$recipe->recipe_id'";

        try {
            $exists = FitnessHelper::customQuery($query, 0);
        } catch (Exception $e) {
            $status['success'] = 0;
            $status['message'] = '"' . $e->getMessage() . '"';
            return array( 'status' => $status);
        }
        
        if($exists) {
            $status['success'] = 0;
            $status['message'] =  "This recipe has already been added to your Favourites";
            return array( 'status' => $status);
        }
        //
        
        try {
            $inserted_id = $helper->insertUpdateObj($recipe, $table);
        } catch (Exception $e) {
            $status['success'] = 0;
            $status['message'] = '"' . $e->getMessage() . '"';
            return array( 'status' => $status);
        }
        
        $result = array( 'status' => $status, 'data' => $inserted_id);
        
        return $result;
    }
    
    
    public function removeFavourite($table, $data_encoded) {
        $status['success'] = 1;
        
        $data = json_decode($data_encoded);
        
        $user = &JFactory::getUser();
            
        $recipe->client_id = $user->id;
        $recipe->recipe_id = $data->recipe_id;
        
        $db = JFactory::getDBO();
        
        $query = "DELETE FROM $table WHERE client_id='$recipe->client_id' AND recipe_id='$recipe->recipe_id'";
        
        $db->setQuery($query);
        
        if (!$db->query()) {
            $status['success'] = 0;
            $status['message'] = '"' . $e->getMessage() . '"';
        }

        $result = array( 'status' => $status, 'data' => $data->recipe_id);
        
        return $result;
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
        
        $result = array( 'status' => $status, 'data' => $data->id);
        
        return $result;
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
    
    public function uploadImage() {
        error_reporting(E_ALL);

        $filename = $_FILES['file']['name'];

        $upload_folder = $_GET['upload_folder'];

        $task = $_POST['task'];

        if($task == 'clear') {
            $filename = $_POST['filename'];
            unlink($upload_folder. '/' . $filename);
            echo $filename;
            return false;
        }


        if($_FILES['file']['size']/1024 > 5024) {
            echo 'too big file'; 
            header('HTTP', true, 500);
            return false;
        }

        $fileType="";

        if(strstr($_FILES['file']['type'],"jpeg")) $fileType="jpg";

        if(strstr($_FILES['file']['type'],"png")) $fileType="png";

        if(strstr($_FILES['file']['type'],"gif")) $fileType="gif";

        if(strstr($_FILES['file']['type'],"gif")) $fileType="bmp";

        if(strstr($_FILES['file']['type'],"gif")) $fileType="jpeg";


        if (!$fileType) {
            echo 'Invalid file type';
            header('HTTP', true, 500);
            return false;
        } 

        if (file_exists('uploads/' .$filename) && $filename) {
            echo 'Image with such name already exists!';
           
            return false;
         }

        if (move_uploaded_file($_FILES['file']['tmp_name'], $upload_folder. '/' . $filename)) {
            echo "ok";
        } else {
            header('HTTP', true, 500);
        }

    }
}
