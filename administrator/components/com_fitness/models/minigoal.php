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
class FitnessModelminigoal extends JModelAdmin
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
	public function getTable($type = 'Minigoal', $prefix = 'FitnessTable', $config = array())
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
		$form = $this->loadForm('com_fitness.minigoal', 'minigoal', array('control' => 'jform', 'load_data' => $loadData));
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
		$data = JFactory::getApplication()->getUserState('com_fitness.edit.minigoal.data', array());

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
				$db->setQuery('SELECT MAX(ordering) FROM #__fitness_mini_goals');
				$max = $db->loadResult();
				$table->ordering = $max+1;
			}

		}
	}
        
        
        
    public function save($data)	{
       
	$id = (!empty($data['id'])) ? $data['id'] : (int)$this->getState('minigoal.id');
        $state = (!empty($data['state'])) ? 1 : 0;
        $user = JFactory::getUser();

        if($id) {
            //Check the user can edit this item
            $authorised = $user->authorise('core.edit', 'com_fitness') || $authorised = $user->authorise('core.edit.own', 'com_fitness');
            if($user->authorise('core.edit.state', 'com_fitness') !== true && $state == 1){ //The user cannot edit the state of the item.
                $data['state'] = 0;
            }
        } else {
            //Check the user can create new items in this section
            $authorised = $user->authorise('core.create', 'com_fitness');
            if($user->authorise('core.edit.state', 'com_fitness') !== true && $state == 1){ //The user cannot edit the state of the item.
                $data['state'] = 0;
            }
        }

        if ($authorised !== true) {
            JError::raiseError(403, JText::_('JERROR_ALERTNOAUTHOR'));
            return false;
        }
        
        $db = JFactory::getDbo();

        $table = '#__fitness_mini_goals';
        
        $object = new stdClass();

        foreach ($data as $key => $value)
        {
            $object->$key = $value;
        }

        if($object->id) {
            $insert = $db->updateObject($table, $object, 'id');
        } else {
            $insert = $db->insertObject($table, $object, 'id');
        }
        
        if (!$insert) {
            JError::raiseError($db->getErrorMsg());
        }
        
        $inserted_id = $db->insertid();
        
        if(!$inserted_id) {
            $inserted_id = $data['id'];
        }
        
        $object->id = $inserted_id;


            if ($inserted_id) {
                return $object;
            } else {
                return false;
            }
   
	}
    
    public function addPlan($goal) {
        require_once  JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_fitness' . DS .'helpers' . DS . 'fitness.php';
        $helper = new FitnessHelper();
       
        $obj = new stdClass();
        $obj->id = $goal->id;
        $obj->primary_goal_id = $goal->primary_goal_id;
        $obj->start_date = $goal->start_date;
        $obj->deadline = $goal->deadline;

        $plan_data = $helper->goalToPlanDecorator($obj);
        $helper->addNutritionPlan($plan_data);
    }

}