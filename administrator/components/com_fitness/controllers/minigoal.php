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

jimport('joomla.application.component.controllerform');

/**
 * Minigoal controller class.
 */
class FitnessControllerMinigoal extends JControllerForm
{

    function __construct() {
        $this->view_list = 'minigoals';
        parent::__construct();
    }
    
    
    public function save() {
        // Check for request forgeries.
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        // Initialise variables.
        $app	= JFactory::getApplication();
        $model = $this->getModel('minigoal', 'FitnessModel');
                
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
                $app->setUserState('com_fitness.edit.minigoal.data', JRequest::getVar('jform'),array());

                // Redirect back to the edit screen.
                $id = (int) $app->getUserState('com_fitness.edit.minigoal.id');
                $this->setRedirect(JRoute::_('index.php?option=com_fitness&view=minigoal&layout=edit&id='.$id, false));
                return false;
        }
        
        
        $id = JRequest::getVar('jform_id');
        
        $data['id'] = $id;
        
        // Attempt to save the data.
        $saved_data = $model->save($data);
        
               
        if($saved_data->id) {
            $model->addPlan($saved_data);
        }
        
        $id = $saved_data->id;
        
        $return = $saved_data;
        
        // Check for errors.
        if ($return === false) {
                // Save the data in the session.
                $app->setUserState('com_fitness.edit.minigoal.data', $data);

                // Redirect back to the edit screen.
                $id = (int)$app->getUserState('com_fitness.edit.minigoal.id');
                $this->setMessage(JText::sprintf('Save failed', $model->getError()), 'warning');
                $this->setRedirect(JRoute::_('index.php?option=com_fitness&view=minigoal&layout=edit&id='.$id, false));
                return false;
        }


        // Clear the profile id from the session.
        $app->setUserState('com_fitness.edit.minigoal.id', null);

        // Redirect to the list screen.
        
        $message_text = JText::_('Entry saved successfully');
        
        $this->setMessage($message_text);
        
        $input = JFactory::getApplication()->input;
        $task = $input->getString('task', '');
        
        if($task == 'save'){
            $this->setRedirect(JRoute::_('index.php?option=com_fitness&view=minigoals', false));
        }
        
        if($task == 'apply'){
            $this->setRedirect(JRoute::_('index.php?option=com_fitness&task=minigoal.edit&id='.$id, false));
        }
        
        if($task == 'save2new'){
            $this->setRedirect(JRoute::_('index.php?option=com_fitness&view=minigoal&layout=edit', false));
        }

        // Flush the data from the session.
        $app->setUserState('com_fitness.edit.minigoal.data', null);
    }
	

}