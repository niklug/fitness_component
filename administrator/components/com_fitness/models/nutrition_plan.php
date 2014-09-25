<?php
/**
 * @version     1.0.0
 * @package     com_fitness
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Nikolay Korban <niklug@ukr.net> - http://
 */

// No direct access.
defined('_JEXEC') or die;

jimport('joomla.application.component.modeladmin');
require_once  JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_fitness' . DS .'helpers' . DS . 'fitness.php';

/**
 * Fitness model.
 */
class FitnessModelnutrition_plan extends JModelAdmin
{
	/**
	 * @var		string	The prefix to use with controller messages.
	 * @since	1.6
	 */
	protected $text_prefix = 'COM_FITNESS';


	/**
	 * Returns a reference to the a Table object, always creating it.
	 *
	 * @param	type	The table type to instantiate
	 * @param	string	A prefix for the table class name. Optional.
	 * @param	array	Configuration array for model. Optional.
	 * @return	JTable	A database object
	 * @since	1.6
	 */
	public function getTable($type = 'Nutrition_plan', $prefix = 'FitnessTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}

	/**
	 * Method to get the record form.
	 *
	 * @param	array	$data		An optional array of data for the form to interogate.
	 * @param	boolean	$loadData	True if the form is to load its own data (default case), false if not.
	 * @return	JForm	A JForm object on success, false on failure
	 * @since	1.6
	 */
	public function getForm($data = array(), $loadData = true)
	{
		// Initialise variables.
		$app	= JFactory::getApplication();

		// Get the form.
		$form = $this->loadForm('com_fitness.nutrition_plan', 'nutrition_plan', array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form)) {
			return false;
		}

		return $form;
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return	mixed	The data for the form.
	 * @since	1.6
	 */
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState('com_fitness.edit.nutrition_plan.data', array());

		if (empty($data)) {
			$data = $this->getItem();
            
		}

		return $data;
	}

	/**
	 * Method to get a single record.
	 *
	 * @param	integer	The id of the primary key.
	 *
	 * @return	mixed	Object on success, false on failure.
	 * @since	1.6
	 */
	public function getItem($pk = null)
	{
		if ($item = parent::getItem($pk)) {

			//Do any procesing on fields here if needed

		}

		return $item;
	}

	/**
	 * Prepare and sanitise the table prior to saving.
	 *
	 * @since	1.6
	 */
	protected function prepareTable(&$table)
	{
		jimport('joomla.filter.output');

		if (empty($table->id)) {

			// Set ordering to the last item if not set
			if (@$table->ordering === '') {
				$db = JFactory::getDbo();
				$db->setQuery('SELECT MAX(ordering) FROM #__fitness_nutrition_plan');
				$max = $db->loadResult();
				$table->ordering = $max+1;
			}

		}
	}
        
        public function getClientPrimaryGoals($client_id) {
            $db = & JFactory::getDBO();
            $query = "SELECT g.id, c.name
                FROM #__fitness_goals AS g
                LEFT JOIN #__fitness_goal_categories AS c
                ON g.goal_category_id = c.id
                WHERE g.user_id='$client_id' AND g.state='1'";
            $db->setQuery($query);
            $status['success'] = 1;
            if (!$db->query()) {
                $status['success'] = 0;
                $status['message'] = $db->stderr();
            }
            $ids = $db->loadResultArray(0);
            $names = $db->loadResultArray(1);
            $result = array('status' => $status, 'data' => array_combine($ids, $names));
            return json_encode($result);
        }
        
        public function getGoalData($id, $nutrition_plan_id) {
            $db = & JFactory::getDBO();
            $query = "SELECT g.*
                FROM #__fitness_goals as g
                WHERE g.id='$id' AND g.state='1'";
            $db->setQuery($query);
            $status['success'] = 1;
            if (!$db->query()) {
                $status['success'] = 0;
                $status['message'] = $db->stderr();
            }
            $data = $db->loadObject();
            
            try {
                $data->minigoals = $this->getMiniGoals($data->id, $data->user_id, $nutrition_plan_id);
            } catch (Exception $e) {
                $status['success'] = 0;
                $status['message'] = '"' . $e->getMessage() . '"';
                return array( 'status' => $status);
            }
            
            
            $result = array('status' => $status, 'data' => $data);
            return json_encode($result); 
        }
        
        public function getMiniGoals($primary_goal_id, $client_id, $nutrition_plan_id) {
            $db = & JFactory::getDBO();
            $query = "SELECT mg.*, c.name AS minigoal_name, tp.name AS training_period_name FROM #__fitness_mini_goals AS mg
                LEFT JOIN #__fitness_mini_goal_categories AS c ON mg.mini_goal_category_id=c.id
                LEFT JOIN #__fitness_training_period AS tp ON tp.id=mg.training_period_id
                WHERE mg.primary_goal_id='$primary_goal_id'
                AND mg.state='1'";
            /*
            
                . " AND c.id NOT IN (SELECT mini_goal_category_id FROM #__fitness_mini_goals "
                . " WHERE id IN (SELECT DISTINCT mini_goal FROM #__fitness_nutrition_plan WHERE state='1' AND client_id='$client_id' AND id NOT IN ('$nutrition_plan_id'))"
                . " AND  state='1')";
             * 
             */

            $db->setQuery($query);
            if (!$db->query()) {
                throw new Exception($db->stderr());
            }
            return $db->loadObjectList();
        }
        
        public function resetAllForceActive() {
            $db = & JFactory::getDBO();
            $query = "UPDATE #__fitness_nutrition_plan SET force_active='0'";
            $db->setQuery($query);
            $status['success'] = 1;
            if (!$db->query()) {
                $status['success'] = 0;
                $status['message'] = $db->stderr();
            }
            $data = $db->loadObject();
            $result = array('status' => $status, 'data' => true);
            return json_encode($result); 
        }
        
        public function saveTargetsData($data_encoded) {
            $ret['success'] = 1;
            $db = JFactory::getDbo();
            
            $data = json_decode($data_encoded);

            $query = "SELECT id FROM #__fitness_nutrition_plan_targets WHERE nutrition_plan_id='$data->nutrition_plan_id' AND type='$data->type'";
            $db->setQuery($query);
            if (!$db->query()) {
                $ret['success'] = 0;
                $ret['message'] = $db->stderr();
                $result = array('status' => $ret);
                return json_encode($result);  
            }
            $exist = $db->loadResult();  

            if($exist) {
                $data->id = $exist;
                $insert = $db->updateObject('#__fitness_nutrition_plan_targets', $data, 'id');
            } else {
                $insert = $db->insertObject('#__fitness_nutrition_plan_targets', $data, 'id');
            }

            if (!$insert) {
                $ret['success'] = false;
                $ret['message'] = $db->stderr();
            }
            $result = array('status' => $ret);
            
            return json_encode($result);  
        }
        
        public function getTargetsData($data_encoded) {
            $ret['success'] = 1;
            $db = JFactory::getDbo();
            
            $data = json_decode($data_encoded);

            $query = "SELECT * FROM #__fitness_nutrition_plan_targets WHERE nutrition_plan_id='$data->nutrition_plan_id' AND type='$data->type'";
            $db->setQuery($query);
            if (!$db->query()) {
                $ret['success'] = 0;
                $ret['message'] = $db->stderr();
            }
            $data = $db->loadObject();
            $result = array('status' => $ret, 'data' => $data);
            return json_encode($result);  
        }
        
        public function saveIngredient($ingredient_encoded, $table) {
            $ret['success'] = 1;
            $db = JFactory::getDbo();
            
            $ingredient = json_decode($ingredient_encoded);
            
            if($ingredient->id) {
                $insert = $db->updateObject($table, $ingredient, 'id');
            } else {
                $insert = $db->insertObject($table, $ingredient, 'id');
            }

            if (!$insert) {
                $ret['success'] = false;
                $ret['message'] = $db->stderr();
            }
            
            
            $inserted_id = $db->insertid();
            if(!$inserted_id) {
                $inserted_id = $ingredient->id;
            }
            //$ret['success'] = 0;
            
            //$ret['message'] = print_r($ingredient, true);
            
            $result = array('status' => $ret, 'inserted_id' => $inserted_id);
            
            return json_encode($result);     
        }
        
        
        public function deleteIngredient($id, $table) {
            $ret['success'] = 1;
            $db = JFactory::getDbo();
            $query = "DELETE FROM $table WHERE id='$id'";
            $db->setQuery($query);
            if(!$db->query()) {
                $ret['success'] = 0;
                $ret['message'] =  $db->getErrorMsg();
            }
            $result = array('status' => $ret, 'id' => $id);
            
            return json_encode($result);  
        }
        
        
        public function populateItemDescription($data_encoded, $table) {
            $ret['success'] = 1;
            $db = JFactory::getDbo();
            
            $data = json_decode($data_encoded);
            
            $query = "SELECT * FROM $table ";
            
            if($data->nutrition_plan_id) {
                $query .= " WHERE  nutrition_plan_id='$data->nutrition_plan_id'";
            }
            
            if($data->recipe_id) {
                $query .= " WHERE  recipe_id='$data->recipe_id'";
            }
            
            if($data->example_day_id) {
                $query .= " AND  example_day_id='$data->example_day_id'";
            }
            
            if($data->type) {
                $query .= " AND  type='$data->type'";
            }
            
            
            $db->setQuery($query);
            if(!$db->query()) {
                $ret['success'] = 0;
                $ret['message'] =  $db->getErrorMsg();
            }
            $data = $db->loadObjectList();

            $result = array('status' => $ret, 'data' => $data);
            
            return json_encode($result);      
        }
        
        
        
        public function savePlanComment($data_encoded, $table) {
            $ret['success'] = 1;
            $db = JFactory::getDbo();
            
            $user = &JFactory::getUser();
            $obj = json_decode($data_encoded);
            $obj->created_by = $user->id;
            
            $helper = new FitnessHelper();
            $businessProfile = $helper->getBusinessProfileId($user->id);
            if(!$businessProfile['success']) {
                $ret['success'] = 0;
                $ret['message'] =  $businessProfile['message'];
            }
            $business_profile_id = $businessProfile['data'];
            
            $obj->business_profile_id = $business_profile_id;
            
            if($obj->id) {
                $insert = $db->updateObject($table, $obj, 'id');
            } else {
                $insert = $db->insertObject($table, $obj, 'id');
                $obj->id  = $db->insertid();
            }

            if (!$insert) {
                $ret['success'] = false;
                $ret['message'] = $db->stderr();
            }
            /*
            $ret['success'] = 0;
            
            $ret['message'] = print_r($obj, true);
            */
            $obj->user_name = $user->name;
  
            $result = array('status' => $ret, 'data' => $obj);
            
            return json_encode($result);     
        }
        
        
        
        public function deletePlanComment($id, $table) {
            $ret['success'] = 1;
            $db = JFactory::getDbo();
            $query = "DELETE FROM $table WHERE id='$id'";
            $db->setQuery($query);
            if(!$db->query()) {
                $ret['success'] = 0;
                $ret['message'] =  $db->getErrorMsg();
            }
            $result = array('status' => $ret, 'id' => $id);
            
            return json_encode($result);  
        }
        
        
        
        public function populatePlanComments($item_id, $sub_item_id, $table) {
            $user = &JFactory::getUser();
            $helper = new FitnessHelper();
            $user_id = $user->id;
            $ret['success'] = 1;
            $db = JFactory::getDbo();
            $query = "SELECT a.*, u.name AS user_name FROM $table AS a";
            $query .= " LEFT JOIN #__users AS u ON u.id=a.created_by";
      
            $query .= " WHERE a.item_id='$item_id'";
            $query .= " AND a.sub_item_id='$sub_item_id'";

            $businessProfile = $helper->getBusinessProfileId($user_id);
            
            if(!$businessProfile['success']) {
                $ret['success'] = 0;
                $ret['message'] =  $businessProfile['message'];
            }
            $business_profile_id = $businessProfile['data'];
            
           
            if(!FitnessHelper::is_superuser($user_id)) {
                $query .= " AND (a.business_profile_id='$business_profile_id' OR a.business_profile_id='0')";
            }
            
            $query .= "  ORDER BY CASE WHEN a.parent_id = '0' THEN  a.id ELSE  a.parent_id  END";

            $db->setQuery($query);
            if(!$db->query()) {
                $ret['success'] = 0;
                $ret['message'] =  $db->getErrorMsg();
            }
            $comments = $db->loadObjectList();

            $result = array('status' => $ret, 'comments' => $comments);
            
            return json_encode($result);      
        }
        
        
                
        public function importRecipe($data_encoded, $table) {
            $ret['success'] = 1;
            $db = JFactory::getDbo();
          
            $user = &JFactory::getUser();
            $obj = json_decode($data_encoded);
            
            $query = "SELECT * FROM #__fitness_nutrition_recipes_meals WHERE recipe_id='$obj->recipe_id'";
            $db->setQuery($query);
            if(!$db->query()) {
                throw new Exception($db->getErrorMsg());
                $ret['success'] = 0;
                $ret['message'] =  $db->getErrorMsg();
            }
            $ingredients = $db->loadObjectList();

            
            $coef = $obj->number_serves / $obj->number_serves_recipe;


            foreach ($ingredients as $ingredient) {
                $data = new stdClass();
                if($obj->nutrition_plan_id) {
                    $data->nutrition_plan_id = $obj->nutrition_plan_id;
                }
                if($obj->diary_id) {
                    $data->diary_id = $obj->diary_id;
                }
                
                if($obj->meal_entry_id) {
                    $data->meal_entry_id = $obj->meal_entry_id;
                }
                
                if($obj->meal_id) {
                    $data->meal_id = $obj->meal_id;
                }
                
                
                if($obj->menu_id) {
                    $data->menu_id = $obj->menu_id;
                }
                
                if($obj->recipe_id_created) {
                    $data->recipe_id = $obj->recipe_id_created;
                }
                $data->type = $obj->type;
                $data->ingredient_id = $ingredient->ingredient_id;
                $data->meal_name = $ingredient->meal_name;
                $data->quantity = $ingredient->quantity * $coef;
                $data->measurement = $ingredient->measurement;
                $data->protein = $ingredient->protein * $coef;
                $data->fats = $ingredient->fats * $coef;
                $data->carbs = $ingredient->carbs * $coef;
                $data->calories = $ingredient->calories * $coef;
                $data->energy = $ingredient->energy * $coef;
                $data->saturated_fat = $ingredient->saturated_fat * $coef;
                $data->total_sugars = $ingredient->total_sugars * $coef;
                $data->sodium = $ingredient->sodium * $coef;

                
                $insert = $db->insertObject($table, $data, 'id');
                if (!$insert) {
                    throw new Exception($db->getErrorMsg());
                    $ret['success'] = false;
                    $ret['message'] = $db->stderr();
                }
            }

            $result = array('status' => $ret, 'data'  => $data);
            
            return $result;     
        }
        
        
                
        public function saveShoppingItem($data_encoded) {
            $ret['success'] = 1;
            $db = JFactory::getDbo();
            
            $user = &JFactory::getUser();
            $obj = json_decode($data_encoded);
            $obj->created_by = $user->id;
            
            if($obj->id) {
                $insert = $db->updateObject('#__fitness_nutrition_plan_shopping_list', $obj, 'id');
            } else {
                $insert = $db->insertObject('#__fitness_nutrition_plan_shopping_list', $obj, 'id');
                $obj->id  = $db->insertid();
            }

            if (!$insert) {
                $ret['success'] = false;
                $ret['message'] = $db->stderr();
            }
            
            /*
            $ret['success'] = 0;
            
            $ret['message'] = print_r($obj, true);
            */

  
            $result = array('status' => $ret, 'data' => $obj);
            
            return json_encode($result);     
        }
        
        
        
        
        public function deleteShoppingItem($id) {
            $ret['success'] = 1;
            $db = JFactory::getDbo();
            $query = "DELETE FROM #__fitness_nutrition_plan_shopping_list WHERE id='$id'";
            $db->setQuery($query);
            if(!$db->query()) {
                $ret['success'] = 0;
                $ret['message'] =  $db->getErrorMsg();
            }
            $result = array('status' => $ret, 'id' => $id);
            
            return json_encode($result);  
        }
        
        
        public function getShoppingItemData($nutrition_plan_id) {
            $ret['success'] = 1;
            $db = JFactory::getDbo();
            $query = "SELECT * FROM #__fitness_nutrition_plan_shopping_list WHERE nutrition_plan_id='$nutrition_plan_id'";
            $db->setQuery($query);
            if(!$db->query()) {
                $ret['success'] = 0;
                $ret['message'] =  $db->getErrorMsg();
            }
            $comments = $db->loadObjectList();

            $result = array('status' => $ret, 'data' => $comments);
            
            return json_encode($result);      
        }
        
        public function nutrition_plan_protocol() {

            $method = JRequest::getVar('_method');
            
            if(!$method) {
                $method = $_SERVER['REQUEST_METHOD'];
            }
            
                       
            $model = json_decode(JRequest::getVar('model'));
         
            $table = '#__fitness_nutrition_plan_supplement_protocols';
            
            $helper = new FitnessHelper();
            
            switch ($method) {
                case 'GET': // Get Item(s)
                    $id = JRequest::getVar('id');
                    $nutrition_plan_id = JRequest::getVar('nutrition_plan_id');
                    
                    $query = "SELECT a.*,";
                    
                    $query .= " (SELECT user_id  FROM #__fitness_clients WHERE user_id=a.created_by LIMIT 1) created_by_client";
                    
                    $query .= " FROM $table AS a";
                    
                    $query .= " WHERE 1";
                    
                    if($id) {
                        $query .= " AND a.id='$id'";
                    }
                    if($nutrition_plan_id) {
                        $query .= " AND a.nutrition_plan_id='$nutrition_plan_id'";
                    }

                    $items = FitnessHelper::customQuery($query, 1);
                    return $items;
                    break;
                case 'PUT': // Update
                    $id = $helper->insertUpdateObj($model, $table);
                    break;
                case 'POST': // Create
                    $query = "SELECT client_id FROM #__fitness_nutrition_plan WHERE id='$model->nutrition_plan_id'";
                    $client_id = $helper->customQuery($query, 0);
                    $model->created_by_client = $client_id;
                    $id = $helper->insertUpdateObj($model, $table);
                    break;
                case 'DELETE': // Delete Item
                    $id = str_replace('/', '', end(array_keys($_GET)));
                    $id = $helper->deleteRow($id, $table);
                    break;

                default:
                    break;
            }
   
            $model->id = $id;
            
            return $model;
        }
        
        
        
        
        public function nutrition_plan_supplement() {

            $method = JRequest::getVar('_method');
            
            if(!$method) {
                $method = $_SERVER['REQUEST_METHOD'];
            }
            
                       
            $model = json_decode(JRequest::getVar('model'));
            
                        
            $table = '#__fitness_nutrition_plan_supplements';
            
            $helper = new FitnessHelper();
            
            switch ($method) {
                case 'GET': // Get Item(s)
                    
                    $id = JRequest::getVar('id');
                    $nutrition_plan_id = JRequest::getVar('nutrition_plan_id');
                    $protocol_id = JRequest::getVar('protocol_id');
                    
                    $query = "SELECT a.*,";
                    
                    $query .= " (SELECT user_id  FROM #__fitness_clients WHERE user_id=a.created_by LIMIT 1) created_by_client,";
                    
                    $query = "SELECT * FROM $table AS a";
                    
                    $query .= " WHERE 1";
                    
                    if($id) {
                        $query .= " AND id='$id'";
                    }
                    if($nutrition_plan_id) {
                        $query .= " AND a.nutrition_plan_id='$nutrition_plan_id'";
                    }
                    if($protocol_id) {
                        $query .= " AND a.protocol_id='$protocol_id'";
                    }
                    
                    $items = FitnessHelper::customQuery($query, 1);
                    return $items;
                    break;
                case 'PUT': // Update
                    $id = $helper->insertUpdateObj($model, $table);
                    break;
                case 'POST': // Create
                    $id = $helper->insertUpdateObj($model, $table);
                    break;
                case 'DELETE': // Delete Item
                    $id = str_replace('/', '', end(array_keys($_GET)));
                    $id = $helper->deleteRow($id, $table);
                    break;

                default:
                    break;
            }
   
            $model->id = $id;
            
            return $model;
        }
        
                
        public function getRecipeTypes() {

            $helper = new FitnessHelper();

            $recipeTypes = $helper->getRecipeTypes();

            if(!$recipeTypes['success']) {
                echo $recipeTypes['message'];
                header("HTTP/1.0 404 Not Found");
            }
            $data = $recipeTypes['data'];
            return $data;
        }

        
        public function nutrition_guide_recipes() {
            
            $method = JRequest::getVar('_method');
            
            if(!$method) {
                $method = $_SERVER['REQUEST_METHOD'];
            }
       
            $model = json_decode(JRequest::getVar('model'));

            $table = '#__fitness_nutrition_plan_example_day_recipes';
            $ingredients_table = '#__fitness_nutrition_plan_example_day_ingredients';
            $ingredients_table_WHERE = 'recipe_id=r.id';
            
            $helper = new FitnessHelper();
            
            
            switch ($method) {
                case 'GET': // Get Item(s)
                    $nutrition_plan_id = JRequest::getVar('nutrition_plan_id');
                    $menu_id = JRequest::getVar('menu_id');
                    $example_day_id = JRequest::getVar('example_day_id');
                    $sort_by = JRequest::getVar('sort_by');
                    $order_dirrection = JRequest::getVar('order_dirrection');
                    
                    $obj = new stdClass();
                    
                    $obj->nutrition_plan_id = $nutrition_plan_id;
                    $obj->menu_id = $menu_id;
                    $obj->example_day_id = $example_day_id;
                    $obj->sort_by = $sort_by;
                    $obj->order_dirrection = $order_dirrection;

                    $data = $this->getNutritionGuideRecipes($obj);

                    return $data;
                    break;
                case 'PUT': 
                    //update
                    $item_id = str_replace('/', '', $_GET['id']);
                        if($item_id) {
                            $model->id = $item_id;
                            $id = $helper->insertUpdateObj($model, $table);
                        }
                    break;
                case 'POST': // Create
                    
                    $id = $helper->insertUpdateObj($model, $table);
                    
                    if($id) {
                        $ingredient_obj = new stdClass();
                        $ingredient_obj->nutrition_plan_id = $model->nutrition_plan_id;
                        $ingredient_obj->menu_id = $model->menu_id;
                        $ingredient_obj->recipe_id = $model->original_recipe_id;
                        $ingredient_obj->recipe_id_created = $id;
                        
                        if(!$model->number_serves_new) {
                            $model->number_serves_new = $model->number_serves;
                        }
                        
                        $ingredient_obj->number_serves = $model->number_serves_new;
                        $ingredient_obj->number_serves_recipe = $model->number_serves;
                        $ingredient_obj_encoded = json_encode($ingredient_obj);

                        $this->importRecipe($ingredient_obj_encoded, $ingredients_table);
                        
                        $obj = new stdClass();
                        $obj->id = $id;
                        $obj->nutrition_plan_id = $model->nutrition_plan_id;
                        $obj->menu_id = $model->menu_id;
                        $obj->example_day_id = $model->example_day_id;
                        $obj->sort_by = 'time';
                        $obj->order_dirrection = 'ASC';

                        $data = $this->getNutritionGuideRecipes($obj);
                        
                                    
                        return $data;
                    }
                    break;
                case 'DELETE': // Delete Item
                    $id = str_replace('/', '', $_GET['id']);
                    $id = $helper->deleteRow($id, $table);
                    break;

                default:
                    break;
            }

            $model->id = $id;
            
            return $model;
        }
        
        public function getNutritionGuideRecipes($data) {
            $id = $data->id;
            $nutrition_plan_id = $data->nutrition_plan_id;
            $menu_id = $data->menu_id;
            $example_day_id = $data->example_day_id;;
            $sort_by =$data->sort_by;
            $order_dirrection = $data->order_dirrection;
            
            $table = '#__fitness_nutrition_plan_example_day_recipes';
            $ingredients_table = '#__fitness_nutrition_plan_example_day_ingredients';
            $ingredients_table_WHERE = 'recipe_id=r.id';
            
            if(!$example_day_id) return;

            $query = "SELECT * FROM $table WHERE 1";

            if($id) {
                $query .= " AND id='$id'";
            }

            $query = "SELECT r.*, a.*, r.number_serves AS number_serves, r.id AS id,";

            $query .= " (SELECT name FROM #__users WHERE id=a.created_by) author,";
            $query .= " (SELECT name FROM #__users WHERE id=a.assessed_by) trainer,";

            $query .= " (SELECT ROUND(SUM(protein),2) FROM "    . $ingredients_table . "  WHERE " . $ingredients_table_WHERE . ") AS protein,
               (SELECT ROUND(SUM(fats),2) FROM "                . $ingredients_table . "  WHERE " . $ingredients_table_WHERE . ") AS fats,
               (SELECT ROUND(SUM(carbs),2) FROM "               . $ingredients_table . "  WHERE " . $ingredients_table_WHERE . ") AS carbs,
               (SELECT ROUND(SUM(calories),2) FROM "            . $ingredients_table . "  WHERE " . $ingredients_table_WHERE . ") AS calories,
               (SELECT ROUND(SUM(energy),2) FROM "              . $ingredients_table . "  WHERE " . $ingredients_table_WHERE . ") AS energy,
               (SELECT ROUND(SUM(saturated_fat),2) FROM "       . $ingredients_table . "  WHERE " . $ingredients_table_WHERE . ") AS saturated_fat,
               (SELECT ROUND(SUM(total_sugars),2) FROM "        . $ingredients_table . "  WHERE " . $ingredients_table_WHERE . ") AS total_sugars,
               (SELECT ROUND(SUM(sodium),2) FROM "              . $ingredients_table . "  WHERE " . $ingredients_table_WHERE . ") AS sodium";

            $query .= " FROM $table AS r ";
            $query .= " LEFT JOIN  #__fitness_nutrition_recipes AS a ON a.id=r.original_recipe_id";

            $query .= " WHERE 1";
            
            if($id) {
                $query .= " AND r.id='$id'";
            }

            if($nutrition_plan_id) {
                $query .= " AND nutrition_plan_id='$nutrition_plan_id'";
            }
            
            if($menu_id) {
                $query .= " AND menu_id='$menu_id'";
            }

            if($example_day_id) {
                $query .= " AND example_day_id='$example_day_id'";
            }

            if($sort_by) {
                $query .= "  ORDER BY " . $sort_by;
            }

            if($order_dirrection) {
                $query .=  " " . $order_dirrection;
            }
            
            $query_type = 1;
            
            if($id) {
                $query_type = 2;
            }

            $data = FitnessHelper::customQuery($query, $query_type);
            
            return $data;
        }
        
        
        public function recipe_variations() {
            $helper = new FitnessHelper();
            return $helper->getRecipeVariations();
        }
        
        public function getRemoteImages($url) {
            $helper = new FitnessHelper();
            return $helper->getRemoteImages($url);
        }
        
        
        public function nutrition_plan_menu() {

            $method = JRequest::getVar('_method');
            
            if(!$method) {
                $method = $_SERVER['REQUEST_METHOD'];
            }
            
            $model = json_decode(JRequest::getVar('model'));
            
            $table = '#__fitness_nutrition_plan_menus';
            
            $helper = new FitnessHelper();
            
            switch ($method) {
                case 'GET': // Get Item(s)
                    
                    $id = JRequest::getVar('id', 0, '', 'INT');
                    
                    $nutrition_plan_id = JRequest::getVar('nutrition_plan_id');
                    
                    $query = "SELECT a.*, "
                            . " (SELECT name FROM #__users WHERE id=a.created_by) created_by_name,"
                            . " (SELECT name FROM #__users WHERE id=a.assessed_by) assessed_by_name,";
                            
                            
                    $query .= " (SELECT user_id  FROM #__fitness_clients WHERE user_id=a.client_id LIMIT 1) created_by_client";
                    
                    $query .=  " FROM $table AS a WHERE 1";
                    
                    if($id) {
                        $query .= " AND id='$id'";
                    }
                    if($nutrition_plan_id) {
                        $query .= " AND nutrition_plan_id='$nutrition_plan_id'";
                    }
                    
                    $query .= " ORDER BY a.start_date DESC";
                    
                    $query_type = 1;
                    
                    if($id) {
                        $query_type = 2;
                    }
                    
                    $items = FitnessHelper::customQuery($query, $query_type);
                    return $items;
                    break;
                case 'PUT': // Update
                    $id = $helper->insertUpdateObj($model, $table);
                    break;
                case 'POST': // Create
                    $query = "SELECT client_id FROM #__fitness_nutrition_plan WHERE id='$model->nutrition_plan_id'";
                    $client_id = $helper->customQuery($query, 0);
                    $model->client_id = $client_id;
                    $model->created_by_client = $client_id;
                    
                    $id = $helper->insertUpdateObj($model, $table);
                    break;
                case 'DELETE': // Delete Item
                    $id = str_replace('/', '', $_GET['id']);

                    $id = $helper->deleteRow($id, $table);
                    break;

                default:
                    break;
            }
   
            $model->id = $id;
            
            return $model;
        }
        
        
        public function shopping_list_ingredients() {

            $method = JRequest::getVar('_method');
            
            if(!$method) {
                $method = $_SERVER['REQUEST_METHOD'];
            }
            
            $model = json_decode(JRequest::getVar('model'));
            
            $table = '#__fitness_nutrition_plan_example_day_ingredients';
            
            $helper = new FitnessHelper();
            
            switch ($method) {
                case 'GET': // Get Item(s)

                    $nutrition_plan_id = JRequest::getVar('nutrition_plan_id');
                    $menu_id = JRequest::getVar('menu_id');
                    return $helper->getShoppingListIngredients($nutrition_plan_id, $menu_id);
                    break;
                case 'PUT': // Update
                    $id = $helper->insertUpdateObj($model, $table);
                    break;
                case 'POST': // Create
                    $id = $helper->insertUpdateObj($model, $table);
                    break;
                case 'DELETE': // Delete Item
                    $id = str_replace('/', '', $_GET['id']);

                    $id = $helper->deleteRow($id, $table);
                    break;

                default:
                    break;
            }
   
            $model->id = $id;
            
            return $model;
        }
        
        
        function menu_plan_status_html($item_id, $status, $button_class) {
            switch($status) {
                case '1' :
                    $class = 'menu_plan_status_pending';
                    $text = 'PENDING';
                    break;
                case '2' :
                    $class = 'status_approved';
                    $text = 'APPROVED';
                    break;
                case '3' :
                    $class = 'status_notapproved';
                    $text = 'NOT APPROVED';
                    break;
                case '4' :
                    $class = 'status_inprogress';
                    $text = 'IN PROGRESS';
                    break;
                case '5' :
                    $class = 'status_submitted';
                    $text = 'SUBMITTED';
                    break;
                case '6' :
                    $class = 'status_resubmit';
                    $text = 'RESUBMIT';
                    break;
                default :
                    $class = 'menu_plan_status_pending';
                    $text = 'PENDING';
                    break;
            }

            $html = '<a href="javascript:void(0)" data-item_id="' . $item_id . '" data-status_id="' . $status . '" class="' . $button_class . ' ' . $class . '">' . $text . '</a>';

            return $html;
        }
        
        public function copyExampleDay($data_encoded) {
            $obj = json_decode($data_encoded);
            $nutrition_plan_id = $obj->nutrition_plan_id;
            $current_example_day_id = $obj->current_example_day_id;
            $example_day_id = $obj->example_day_id;
            
            $status['success'] = 1;
            
            $db = & JFactory::getDBO();
            //copy recipes
            $query = "SELECT * FROM #__fitness_nutrition_plan_example_day_recipes";
            $query .= " WHERE 1";
            $query .= " AND nutrition_plan_id='$nutrition_plan_id'";
            $query .= " AND example_day_id='$current_example_day_id'";
            
            try {
                $recipes = FitnessHelper::customQuery($query, 1);
            } catch (Exception $e) {
                $status['success'] = 0;
                $status['message'] = '"' . $e->getMessage() . '"';
                return array( 'status' => $status);
            }
            
            foreach ($recipes as $recipe) {
                $original_recipe_id = $recipe->id;
                $recipe->id = null; 
                $recipe->example_day_id = $example_day_id;
                $insert = $db->insertObject('#__fitness_nutrition_plan_example_day_recipes', $recipe, 'id');
                if (!$insert) {
                    throw new Exception($db->getErrorMsg());
                    $status['success'] = false;
                    $status['message'] = $db->stderr();
                    return array( 'status' => $status);
                }
                
                $inserted_recipe_id = $db->insertid();
                //copy recipes ingredients
                $query1 = "SELECT * FROM #__fitness_nutrition_plan_example_day_ingredients";
                $query1 .= " WHERE 1";
                $query1 .= " AND recipe_id='$original_recipe_id'";
                
                try {
                    $ingredients = FitnessHelper::customQuery($query1, 1);
                } catch (Exception $e) {
                    $status['success'] = 0;
                    $status['message'] = '"' . $e->getMessage() . '"';
                    return array( 'status' => $status);
                }
                
                foreach ($ingredients as $ingredient) {
                    $ingredient->id = null; 
                    $ingredient->recipe_id = $inserted_recipe_id;
                    $insert = $db->insertObject('#__fitness_nutrition_plan_example_day_ingredients', $ingredient, 'id');
                    if (!$insert) {
                        throw new Exception($db->getErrorMsg());
                        $status['success'] = false;
                        $status['message'] = $db->stderr();
                        return array( 'status' => $status);
                    }
                }
                //end copy recipes ingredients
            }
            //end copy recipes
     
            $data = $items;
    
            return array('status' => $status, 'data' => $data);
        }
        
}