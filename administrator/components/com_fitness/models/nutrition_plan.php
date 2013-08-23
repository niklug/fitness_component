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
        
        public function getGoalData($id) {
            $db = & JFactory::getDBO();
            $query = "SELECT g.*, p.name AS training_period_name
                FROM #__fitness_goals as g
                LEFT JOIN #__fitness_training_period AS p
                ON g.training_period_id = p.id
                WHERE g.id='$id' AND g.state='1'";
            $db->setQuery($query);
            $status['success'] = 1;
            if (!$db->query()) {
                $status['success'] = 0;
                $status['message'] = $db->stderr();
            }
            $data = $db->loadObject();
            $result = array('status' => $status, 'data' => $data);
            return json_encode($result); 
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
        
        public function saveIngredient($ingredient_encoded) {
            $ret['IsSuccess'] = 1;
            $db = JFactory::getDbo();
            
            $ingredient = json_decode($ingredient_encoded);
            
            if($ingredient->id) {
                $insert = $db->updateObject('#__fitness_nutrition_plan_ingredients', $ingredient, 'id');
            } else {
                $insert = $db->insertObject('#__fitness_nutrition_plan_ingredients', $ingredient, 'id');
            }

            if (!$insert) {
                $ret['IsSuccess'] = false;
                $ret['Msg'] = $db->stderr();
            }
            
            
            $inserted_id = $db->insertid();
            if(!$inserted_id) {
                $inserted_id = $ingredient->id;
            }
            //$ret['IsSuccess'] = 0;
            
            //$ret['Msg'] = print_r($ingredient, true);
            
            $result = array('status' => $ret, 'inserted_id' => $inserted_id);
            
            return json_encode($result);     
        }
        
        
        public function deleteIngredient($id) {
            $ret['IsSuccess'] = 1;
            $db = JFactory::getDbo();
            $query = "DELETE FROM #__fitness_nutrition_plan_ingredients WHERE id='$id'";
            $db->setQuery($query);
            if(!$db->query()) {
                $ret['IsSuccess'] = 0;
                $ret['Msg'] =  $db->getErrorMsg();
            }
            $result = array('status' => $ret, 'id' => $id);
            
            return json_encode($result);  
        }
        
        
        public function populateItemDescription($nutrition_plan_id, $meal_id, $type) {
            $ret['IsSuccess'] = 1;
            $db = JFactory::getDbo();
            $query = "SELECT * FROM #__fitness_nutrition_plan_ingredients WHERE nutrition_plan_id='$nutrition_plan_id' AND  meal_id='$meal_id' AND type='$type' ";
            $db->setQuery($query);
            if(!$db->query()) {
                $ret['IsSuccess'] = 0;
                $ret['Msg'] =  $db->getErrorMsg();
            }
            $data = $db->loadObjectList();

            $result = array('status' => $ret, 'data' => $data);
            
            return json_encode($result);      
        }
        
        
        public function savePlanMeal($meal_encoded) {
            $ret['IsSuccess'] = 1;
            $db = JFactory::getDbo();
            
            $meal = json_decode($meal_encoded);
            
            if($meal->id) {
                $insert = $db->updateObject('#__fitness_nutrition_plan_meals', $meal, 'id');
            } else {
                $insert = $db->insertObject('#__fitness_nutrition_plan_meals', $meal, 'id');
            }

            if (!$insert) {
                $ret['IsSuccess'] = false;
                $ret['Msg'] = $db->stderr();
            }
            
            
            $inserted_id = $db->insertid();
            if(!$inserted_id) {
                $inserted_id = $meal->id;
            }
            //$ret['IsSuccess'] = 0;
            
            //$ret['Msg'] = print_r($ingredient, true);
            
            $result = array('status' => $ret, 'inserted_id' => $inserted_id);
            
            return json_encode($result);     
        }
        
        public function deletePlanMeal($id) {
            $ret['IsSuccess'] = 1;
            $db = JFactory::getDbo();
            $query = "DELETE FROM #__fitness_nutrition_plan_meals WHERE id='$id'";
            $db->setQuery($query);
            if(!$db->query()) {
                $ret['IsSuccess'] = 0;
                $ret['Msg'] =  $db->getErrorMsg();
            }
            $result = array('status' => $ret, 'id' => $id);
            
            return json_encode($result);  
        }
        
        
        public function populatePlanMeal($nutrition_plan_id) {
            $ret['IsSuccess'] = 1;
            $db = JFactory::getDbo();
            $query = "SELECT * FROM #__fitness_nutrition_plan_meals WHERE nutrition_plan_id='$nutrition_plan_id'";
            $db->setQuery($query);
            if(!$db->query()) {
                $ret['IsSuccess'] = 0;
                $ret['Msg'] =  $db->getErrorMsg();
            }
            $data = $db->loadObjectList();

            $result = array('status' => $ret, 'data' => $data);
            
            return json_encode($result);      
        }
        
        
        public function savePlanComment($data_encoded) {
            $ret['IsSuccess'] = 1;
            $db = JFactory::getDbo();
            
            $user = &JFactory::getUser();
            $obj = json_decode($data_encoded);
            $obj->created_by = $user->id;
            
            if($obj->id) {
                $insert = $db->updateObject('#__fitness_nutrition_plan_comments', $obj, 'id');
            } else {
                $insert = $db->insertObject('#__fitness_nutrition_plan_comments', $obj, 'id');
                $obj->id  = $db->insertid();
            }

            if (!$insert) {
                $ret['IsSuccess'] = false;
                $ret['Msg'] = $db->stderr();
            }
            /*
            $ret['IsSuccess'] = 0;
            
            $ret['Msg'] = print_r($obj, true);
            */
            $obj->user_name = $user->name;
  
            $result = array('status' => $ret, 'data' => $obj);
            
            return json_encode($result);     
        }
        
        
        
        public function deletePlanComment($id) {
            $ret['IsSuccess'] = 1;
            $db = JFactory::getDbo();
            $query = "DELETE FROM #__fitness_nutrition_plan_comments WHERE id='$id'";
            $db->setQuery($query);
            if(!$db->query()) {
                $ret['IsSuccess'] = 0;
                $ret['Msg'] =  $db->getErrorMsg();
            }
            $result = array('status' => $ret, 'id' => $id);
            
            return json_encode($result);  
        }
        
        
        
        public function populatePlanComments($nutrition_plan_id, $meal_id) {
            $ret['IsSuccess'] = 1;
            $db = JFactory::getDbo();
            $query = "SELECT c.*, u.name AS user_name FROM #__fitness_nutrition_plan_comments AS c
                LEFT JOIN #__users AS u ON u.id=c.created_by
                WHERE nutrition_plan_id='$nutrition_plan_id' 
                AND meal_id='$meal_id'";
            $db->setQuery($query);
            if(!$db->query()) {
                $ret['IsSuccess'] = 0;
                $ret['Msg'] =  $db->getErrorMsg();
            }
            $comments = $db->loadObjectList();

            $result = array('status' => $ret, 'comments' => $comments);
            
            return json_encode($result);      
        }
        
}