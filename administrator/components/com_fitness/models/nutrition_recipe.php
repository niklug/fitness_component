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
class FitnessModelnutrition_recipe extends JModelAdmin
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
	public function getTable($type = 'Nutrition_recipe', $prefix = 'FitnessTable', $config = array())
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
		$form = $this->loadForm('com_fitness.nutrition_recipe', 'nutrition_recipe', array('control' => 'jform', 'load_data' => $loadData));
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
		$data = JFactory::getApplication()->getUserState('com_fitness.edit.nutrition_recipe.data', array());

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
				$db->setQuery('SELECT MAX(ordering) FROM #__fitness_nutrition_recipes');
				$max = $db->loadResult();
				$table->ordering = $max+1;
			}

		}
	}
        
        
        
        public function getSearchIngredients($search_text) {
            $ret['IsSuccess'] = 1;
            
            $search_text_array = preg_split("/[\s,]+/", $search_text);
            
            $db = JFactory::getDbo();
            $query = "SELECT id, ingredient_name FROM #__fitness_nutrition_database WHERE ";

            
            foreach ($search_text_array as $search_text_part) {
                $query .= "   ingredient_name LIKE '%$search_text_part%' AND ";
            }
            
            $query .= " state='1'";
            
            $db->setQuery($query);
            if(!$db->query()) {
                $ret['IsSuccess'] = 0;
                $ret['Msg'] =  $db->getErrorMsg();
            }
            $ingredients = $db->loadObjectList();

            foreach ($ingredients as $ingredient) {
                
                $html .= '<option value="' . $ingredient->id . '" >' . $ingredient->ingredient_name . '</option>';
            }
            
               
            $result = array('status' => $ret, 'html' => $html, 'count' => count($ingredients));
            
            return json_encode($result);      
        }
        
        
        public function getIngredientData($id) {
            $ret['IsSuccess'] = 1;
            $db = JFactory::getDbo();
            $query = "SELECT * FROM #__fitness_nutrition_database WHERE id='$id'";
            $db->setQuery($query);
            if(!$db->query()) {
                $ret['IsSuccess'] = 0;
                $ret['Msg'] =  $db->getErrorMsg();
            }
            $ingredient = $db->loadObject();

            $result = array('status' => $ret, 'ingredient' => $ingredient);
            
            return json_encode($result);      
        }
        
        public function saveMeal($ingredient_encoded) {
            $ret['IsSuccess'] = 1;
            $db = JFactory::getDbo();
            
            $ingredient = json_decode($ingredient_encoded);
            
            if($ingredient->id) {
                $insert = $db->updateObject('#__fitness_nutrition_recipes_meals', $ingredient, 'id');
            } else {
                $insert = $db->insertObject('#__fitness_nutrition_recipes_meals', $ingredient, 'id');
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
        
        
        public function deleteMeal($id) {
            $ret['IsSuccess'] = 1;
            $db = JFactory::getDbo();
            $query = "DELETE FROM #__fitness_nutrition_recipes_meals WHERE id='$id'";
            $db->setQuery($query);
            if(!$db->query()) {
                $ret['IsSuccess'] = 0;
                $ret['Msg'] =  $db->getErrorMsg();
            }
            $result = array('status' => $ret);
            
            return json_encode($result);  
        }
        
        public function populateTable($recipe_id) {
            $ret['IsSuccess'] = 1;
            $db = JFactory::getDbo();
            $query = "SELECT * FROM #__fitness_nutrition_recipes_meals WHERE recipe_id='$recipe_id'";
            $db->setQuery($query);
            if(!$db->query()) {
                $ret['IsSuccess'] = 0;
                $ret['Msg'] =  $db->getErrorMsg();
            }
            $recipe_meals = $db->loadObjectList();

            $result = array('status' => $ret, 'recipe_meals' => $recipe_meals);
            
            return json_encode($result);      
        }

        
        public function saveComment($id, $comment_text, $recipe_id, $created) {
            $ret['IsSuccess'] = 1;
            $db = JFactory::getDbo();
            
            $user = &JFactory::getUser();
            
            $obj = new stdClass();
            $obj->id = $id;
            $obj->recipe_id = $recipe_id;
            $obj->comment = $comment_text;
            $obj->created_by = $user->id;
            $obj->created = $created;
            
            if($id) {
                $insert = $db->updateObject('#__fitness_nutrition_recipes_comments', $obj, 'id');
            } else {
                $insert = $db->insertObject('#__fitness_nutrition_recipes_comments', $obj, 'id');
                $obj->id  = $db->insertid();
            }

            if (!$insert) {
                $ret['IsSuccess'] = false;
                $ret['Msg'] = $db->stderr();
            }
            
            //$ret['IsSuccess'] = 0;
            
            //$ret['Msg'] = print_r($obj, true);

            $obj->user_name = $user->name;
  
            $result = array('status' => $ret, 'data' => $obj);
            
            return json_encode($result);     
        }
        
        public function deleteComment($id) {
            $ret['IsSuccess'] = 1;
            $db = JFactory::getDbo();
            $query = "DELETE FROM #__fitness_nutrition_recipes_comments WHERE id='$id'";
            $db->setQuery($query);
            if(!$db->query()) {
                $ret['IsSuccess'] = 0;
                $ret['Msg'] =  $db->getErrorMsg();
            }
            $result = array('status' => $ret);
            
            return json_encode($result);  
        }
        
        public function populateComments($recipe_id) {
            $ret['IsSuccess'] = 1;
            $db = JFactory::getDbo();
            $query = "SELECT c.*, u.name AS user_name FROM #__fitness_nutrition_recipes_comments AS c
                LEFT JOIN #__users AS u ON u.id=c.created_by
                WHERE recipe_id='$recipe_id'";
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