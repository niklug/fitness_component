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

jimport('joomla.application.component.view');

/**
 * View to edit
 */
class FitnessViewClient extends JView
{
	protected $state;
	protected $item;
	protected $form;

	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{
		$this->state	= $this->get('State');
		$this->item		= $this->get('Item');
		$this->form		= $this->get('Form');

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
            throw new Exception(implode("\n", $errors));
		}

		$this->addToolbar();
		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 */
	protected function addToolbar()
	{
		JFactory::getApplication()->input->set('hidemainmenu', true);

		$user		= JFactory::getUser();
		$isNew		= ($this->item->id == 0);
        if (isset($this->item->checked_out)) {
		    $checkedOut	= !($this->item->checked_out == 0 || $this->item->checked_out == $user->get('id'));
        } else {
            $checkedOut = false;
        }
		$canDo		= FitnessHelper::getActions();

		JToolBarHelper::title(JText::_('COM_FITNESS_TITLE_CLIENT'), 'client.png');

		// If not checked out, can save the item.
		if (!$checkedOut && ($canDo->get('core.edit')||($canDo->get('core.create'))))
		{

			JToolBarHelper::apply('client.apply', 'JTOOLBAR_APPLY');
			JToolBarHelper::save('client.save', 'JTOOLBAR_SAVE');
		}
		if (!$checkedOut && ($canDo->get('core.create'))){
			JToolBarHelper::custom('client.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
		}
		// If an existing item, can save to a copy.
		if (!$isNew && $canDo->get('core.create')) {
			JToolBarHelper::custom('client.save2copy', 'save-copy.png', 'save-copy_f2.png', 'JTOOLBAR_SAVE_AS_COPY', false);
		}
		if (empty($this->item->id)) {
			JToolBarHelper::cancel('client.cancel', 'JTOOLBAR_CANCEL');
		}
		else {
			JToolBarHelper::cancel('client.cancel', 'JTOOLBAR_CLOSE');
		}

	}
        
        /** multilevel select
         * 
         * @param type $item_id
         * @return string
         */
        function getInput($item_id) {
                    $db = &JFactory::getDbo();
                    $query = "SELECT id, username FROM #__users INNER JOIN #__user_usergroup_map ON #__user_usergroup_map.user_id=#__users.id WHERE #__user_usergroup_map.group_id=(SELECT id FROM #__usergroups WHERE title='Trainers')";
                    $db->setQuery($query);
                    $result = $db->loadObjectList();
                    $query = "SELECT other_trainers FROM #__fitness_clients WHERE id='$item_id'";
                    $db->setQuery($query);
                    $other_trainers = explode(',', $db->loadResult());

                    $drawField = '';
                    $drawField .= '<select id="jform[other_trainers][]" class="inputbox" multiple="multiple" name="jform[other_trainers][]">';
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
            return $db->loadResult();
        }
        
        
}
