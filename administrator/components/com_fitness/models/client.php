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
class FitnessModelclient extends JModelAdmin
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
	public function getTable($type = 'Client', $prefix = 'FitnessTable', $config = array())
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
		$form = $this->loadForm('com_fitness.client', 'client', array('control' => 'jform', 'load_data' => $loadData));
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
		$data = JFactory::getApplication()->getUserState('com_fitness.edit.client.data', array());

		if (empty($data)) {
			$data = $this->getItem();
            
		}

                $data->other_trainers = explode(',',$data->other_trainers);
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
				$db->setQuery('SELECT MAX(ordering) FROM #__fitness_clients');
				$max = $db->loadResult();
				$table->ordering = $max+1;
			}

		}
	}
        
        
        
        /** multilevel select
         * 
         * @param type $item_id
         * @return string
         */
        function getInput($item_id, $table) {
                    $db = &JFactory::getDbo();
                    $query = "SELECT id, username FROM #__users INNER JOIN #__user_usergroup_map ON #__user_usergroup_map.user_id=#__users.id WHERE #__user_usergroup_map.group_id=(SELECT id FROM #__usergroups WHERE title='Trainers')";
                    $db->setQuery($query);
                    $result = $db->loadObjectList();
                    $query = "SELECT other_trainers FROM $table WHERE id='$item_id'";
                    $db->setQuery($query);
                    if(!$db->query()) {
                        JError::raiseError($db->getErrorMsg());
                    }
                    $other_trainers = explode(',', $db->loadResult());

                    $drawField = '';
                    $drawField .= '<select size="10" id="other_trainers" class="inputbox" multiple="multiple" name="jform[other_trainers][]">';
                    $drawField .= '<option value="">none</option>';
                    if(isset($result)) {
                        foreach ($result as $item) {
                            if(in_array($item->id, $other_trainers)){
                                $selected = 'selected="selected"';
                            } else {
                                $selected = '';
                            }

                            $drawField .= '<option ' . $selected . ' value="' . $item->id . '">' . $item->username . ' </option>';

                        }
                    }
                    $drawField .= '</select>';
                    return $drawField;
        }
        
        function getUserGroup($user_id) {
            $db = JFactory::getDBO();
            $query = "SELECT title FROM #__usergroups WHERE id IN 
            (SELECT group_id FROM #__user_usergroup_map WHERE user_id='$user_id')";
            $db->setQuery($query);
            if(!$db->query()) {
                JError::raiseError($db->getErrorMsg());
            }
            return $db->loadResult();
        }

}