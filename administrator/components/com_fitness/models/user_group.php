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
class FitnessModeluser_group extends JModelAdmin
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
	public function getTable($type = 'User_group', $prefix = 'FitnessTable', $config = array())
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
		$form = $this->loadForm('com_fitness.user_group', 'user_group', array('control' => 'jform', 'load_data' => $loadData));
        
        
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
		$data = JFactory::getApplication()->getUserState('com_fitness.edit.user_group.data', array());

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
				$db->setQuery('SELECT MAX(ordering) FROM #__fitness_user_groups');
				$max = $db->loadResult();
				$table->ordering = $max+1;
			}

		}
	}
        
        public function onBusinessNameChange($data_encoded, $table) {
            $result['status']['success'] = 1;
            
            $helper = new FitnessHelper();
            
            $data = json_decode($data_encoded);
            
            $business_profile_id = $data->business_profile_id;
            
            //logged user
            $user_id = $data->user_id;
            
            $business_profile = $helper->getBusinessProfile($business_profile_id);
            
            if(!$business_profile['success']) {
                $result['status']['success'] = 0;
                $result['status']['message'] = $business_profile['message'];
                return $result;
            }
            
            $business_profile = $business_profile['data'];
            
            $trainers_group_id =  $business_profile->group_id;
            
            $user = &JFactory::getUser($user_id);
            
            // is simple trainer
            if(!FitnessHelper::is_primary_administrator($user->id) && !FitnessHelper::is_secondary_administrator($user->id) && FitnessHelper::is_trainer($user->id)) {
                
                
                $result['data'] = array($user->name => $user->id);
            
                return  $result;
            }
            
            $trainers = $helper->getUsersByGroup($trainers_group_id);
            
            
            if(!$trainers['success']) {
                $result['status']['success'] = 0;
                $result['status']['message'] = $trainers['message'];
                return $result;
            }
            
            $result['data'] = $trainers['data'];
            
            return  $result;
        }
        

}