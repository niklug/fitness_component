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
class FitnessModelnutrition_diary extends JModelAdmin
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
	public function getTable($type = 'Nutrition_diary', $prefix = 'FitnessTable', $config = array())
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
		$form = $this->loadForm('com_fitness.nutrition_diary', 'nutrition_diary', array('control' => 'jform', 'load_data' => $loadData));
        
        
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
		$data = JFactory::getApplication()->getUserState('com_fitness.edit.nutrition_diary.data', array());

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
				$db->setQuery('SELECT MAX(ordering) FROM #__fitness_nutrition_diary');
				$max = $db->loadResult();
				$table->ordering = $max+1;
			}

		}
	}
        
        function  get_client_trainers($user_id) {
            $db = & JFactory::getDBO();
            $query = "SELECT  other_trainers FROM #__fitness_clients WHERE user_id='$user_id' AND state='1'";
            $db->setQuery($query);
            if(!$db->query()) {
                JError::raiseError($db->getErrorMsg());
            }
            $other_trainers = $db->loadResultArray(0);
            
            $all_trainers_id = explode(',', $other_trainers[0]);
            if(!$all_trainers_id[0]) return;
            foreach ($all_trainers_id as $user_id) {
                $user = &JFactory::getUser($user_id);
                $all_trainers_name[] = $user->name;
            }

            $result = array_combine($all_trainers_id, $all_trainers_name);
            return $result;
        }
        
        public function updateStatus($data_encoded, $table){
            $ret['success'] = 1;
            $db = JFactory::getDbo();
       
            $obj = json_decode($data_encoded);
                   
            $obj = $db->updateObject($table, $obj, 'id');
    

            if (!$obj) {
                $ret['success'] = false;
                $ret['message'] = $db->stderr();
            }
            
            $result = array('status' => $ret, 'data' => $obj->status);
            
            return json_encode($result);   
        }

}