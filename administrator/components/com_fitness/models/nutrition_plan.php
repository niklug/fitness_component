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
}