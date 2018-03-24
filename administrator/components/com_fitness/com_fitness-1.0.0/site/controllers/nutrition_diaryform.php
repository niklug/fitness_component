<?php
/**
 * @version     1.0.0
 * @package     com_fitness
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Nikolay Korban <niklug@ukr.net> - http://
 */

// No direct access
defined('_JEXEC') or die;

require_once JPATH_COMPONENT.'/controller.php';

require_once JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_fitness' . DS . 'helpers' . DS . 'email.php';

/**
 * Nutrition_diary controller class.
 */
class FitnessControllerNutrition_diaryForm extends FitnessController
{

	/**
	 * Method to check out an item for editing and redirect to the edit form.
	 *
	 * @since	1.6
	 */
	public function edit()
	{
            
		$app			= JFactory::getApplication();

		// Get the previous edit id (if any) and the current edit id.
		$previousId = (int) $app->getUserState('com_fitness.edit.nutrition_diary.id');
		$editId	= JFactory::getApplication()->input->getInt('id', null, 'array');

		// Set the user id for the user to edit in the session.
		$app->setUserState('com_fitness.edit.nutrition_diary.id', $editId);

		// Get the model.
		$model = $this->getModel('Nutrition_diaryForm', 'FitnessModel');

		// Check out the item
		if ($editId) {
            $model->checkout($editId);
		}

		// Check in the previous user.
		if ($previousId) {
            $model->checkin($previousId);
		}

		// Redirect to the edit screen.
		$this->setRedirect(JRoute::_('index.php?option=com_fitness&view=nutrition_diary&layout=edit', false));
	}

	/**
	 * Method to save a user's profile data.
	 *
	 * @return	void
	 * @since	1.6
	 */
	public function save()
	{
		// Check for request forgeries.
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		// Initialise variables.
		$app	= JFactory::getApplication();
		$model = $this->getModel('Nutrition_diaryForm', 'FitnessModel');

		// Get the user data.
		$data = JFactory::getApplication()->input->get('jform', array(), 'array');

		// Validate the posted data.
		$form = $model->getForm();
		if (!$form) {
			JError::raiseError(500, $model->getError());
			return false;
		}

		// Validate the posted data.
		$data = $model->validate($form, $data);

		// Check for errors.
		if ($data === false) {
			// Get the validation messages.
			$errors	= $model->getErrors();

			// Push up to three validation messages out to the user.
			for ($i = 0, $n = count($errors); $i < $n && $i < 3; $i++) {
				if ($errors[$i] instanceof Exception) {
					$app->enqueueMessage($errors[$i]->getMessage(), 'warning');
				} else {
					$app->enqueueMessage($errors[$i], 'warning');
				}
			}

			// Save the data in the session.
			$app->setUserState('com_fitness.edit.nutrition_diary.data', JRequest::getVar('jform'),array());

			// Redirect back to the edit screen.
			$id = (int) $app->getUserState('com_fitness.edit.nutrition_diary.id');
			$this->setRedirect(JRoute::_('index.php?option=com_fitness&view=nutrition_diaryform&layout=edit&id='.$id, false));
			return false;
		}
                
                $submit_task =&JRequest::getVar('submit');
                
                if($submit_task) {
                    $config = JFactory::getConfig();
                    $date = new DateTime();
                    $date->setTimezone(new DateTimeZone($config->getValue('config.offset')));
                    $time_created = $date->format('Y-m-d H:i:s');
 
                    $data['submit_date'] = $time_created;
                    $data['status'] = '5';

                }

		// Attempt to save the data.
		$return	= $model->save($data);
                
                $id = $return;
                
                if($submit_task) {
                     $obj = new NutritionDiaryEmail();
                     try {

                        $data_obj = new stdClass();
                        $data_obj->id = $id;
                        $data_obj->method = 'DiarySubmitted';
                        $emails  .= ' ' .$obj->processing($data_obj);
                    } catch (Exception $exc) {
                       echo $exc->getMessage();
                    }
                }


		// Check for errors.
		if ($return === false) {
			// Save the data in the session.
			$app->setUserState('com_fitness.edit.nutrition_diary.data', $data);

			// Redirect back to the edit screen.
			$id = (int)$app->getUserState('com_fitness.edit.nutrition_diary.id');
			$this->setMessage(JText::sprintf('Save failed', $model->getError()), 'warning');
			$this->setRedirect(JRoute::_('index.php?option=com_fitness&view=nutrition_diaryform&layout=edit&id='.$id, false));
			return false;
		}

            
        // Check in the profile.
        if ($return) {
            $model->checkin($return);
        }

        // Clear the profile id from the session.
        $app->setUserState('com_fitness.edit.nutrition_diary.id', null);

        // Redirect to the list screen.
        
        $message_text = JText::_('Entry saved successfully');
        if($submit_task) {
            $message_text =  JText::_('Entry submitted successfully');
        }
        
        $this->setMessage($message_text);
        $menu = & JSite::getMenu();
        $item = $menu->getActive();
        
        $save_close =&JRequest::getVar('save_close');
        
        $delete =&JRequest::getVar('delete');
        
        if($delete) {
            $this->remove();
            return;
        }

        if($save_close) {
            $this->setRedirect(JRoute::_($item->link, false));
        } else {

            $this->setRedirect(JRoute::_('index.php?option=com_fitness&task=nutrition_diary.edit&id='.$id, false));
        }

		// Flush the data from the session.
		$app->setUserState('com_fitness.edit.nutrition_diary.data', null);
	}
    
    
    function cancel() {
		$menu = & JSite::getMenu();
        $item = $menu->getActive();
        $this->setRedirect(JRoute::_($item->link, false));
    }
    
	public function remove()
	{
		// Check for request forgeries.
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		// Initialise variables.
		$app	= JFactory::getApplication();
		$model = $this->getModel('Nutrition_diaryForm', 'FitnessModel');

		// Get the user data.
		$data = JFactory::getApplication()->input->get('jform', array(), 'array');

		// Validate the posted data.
		$form = $model->getForm();
		if (!$form) {
			JError::raiseError(500, $model->getError());
			return false;
		}

		// Validate the posted data.
		$data = $model->validate($form, $data);

		// Check for errors.
		if ($data === false) {
			// Get the validation messages.
			$errors	= $model->getErrors();

			// Push up to three validation messages out to the user.
			for ($i = 0, $n = count($errors); $i < $n && $i < 3; $i++) {
				if ($errors[$i] instanceof Exception) {
					$app->enqueueMessage($errors[$i]->getMessage(), 'warning');
				} else {
					$app->enqueueMessage($errors[$i], 'warning');
				}
			}

			// Save the data in the session.
			$app->setUserState('com_fitness.edit.nutrition_diary.data', $data);

			// Redirect back to the edit screen.
			$id = (int) $app->getUserState('com_fitness.edit.nutrition_diary.id');
			$this->setRedirect(JRoute::_('index.php?option=com_fitness&view=nutrition_diary&layout=edit&id='.$id, false));
			return false;
		}

		// Attempt to save the data.
		$return	= $model->delete($data);

		// Check for errors.
		if ($return === false) {
			// Save the data in the session.
			$app->setUserState('com_fitness.edit.nutrition_diary.data', $data);

			// Redirect back to the edit screen.
			$id = (int)$app->getUserState('com_fitness.edit.nutrition_diary.id');
			$this->setMessage(JText::sprintf('Delete failed', $model->getError()), 'warning');
			$this->setRedirect(JRoute::_('index.php?option=com_fitness&view=nutrition_diary&layout=edit&id='.$id, false));
			return false;
		}

            
        // Check in the profile.
        if ($return) {
            $model->checkin($return);
        }
        
        // Clear the profile id from the session.
        $app->setUserState('com_fitness.edit.nutrition_diary.id', null);

        // Redirect to the list screen.
        $this->setMessage(JText::_('Entry deleted successfully'));
        $menu = & JSite::getMenu();
        $item = $menu->getActive();
        $this->setRedirect(JRoute::_($item->link, false));

		// Flush the data from the session.
		$app->setUserState('com_fitness.edit.nutrition_diary.data', null);
	}
    
    
}