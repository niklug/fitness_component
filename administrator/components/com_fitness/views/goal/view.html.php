<?php
/**
 * @version     1.0.0
 * @package     com_fitness_goals
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
class FitnessViewGoal extends JView
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
                
                $document = JFactory::getDocument();
                $document -> addscript( JUri::base() . 'components' . DS . 'com_fitness' . DS .'assets'. DS .'js'. DS . 'jquery.js');
                $document -> addscript( JUri::root() . 'administrator/components' . DS . 'com_fitness' . DS .'assets'. DS .'js'. DS . 'jquerynoconflict.js');
                $document -> addscript( JUri::base() . 'components' . DS . 'com_fitness' . DS .'assets'. DS .'js'. DS . 'comments_class.js');
                $document -> addscript( JUri::root() . 'administrator/components' . DS . 'com_fitness' . DS .'assets'. DS .'js'. DS . 'ajax_call_function.js');
                $document->addStyleSheet('components/com_fitness/assets/css/fitness.css');
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

		JToolBarHelper::title(JText::_('COM_FITNESS_GOALS_TITLE_GOAL'), 'goal.png');

		// If not checked out, can save the item.
		if (!$checkedOut && ($canDo->get('core.edit')||($canDo->get('core.create'))))
		{

			JToolBarHelper::apply('goal.apply', 'JTOOLBAR_APPLY');
			JToolBarHelper::save('goal.save', 'JTOOLBAR_SAVE');
		}
		if (!$checkedOut && ($canDo->get('core.create'))){
			JToolBarHelper::custom('goal.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
		}
		// If an existing item, can save to a copy.
		if (!$isNew && $canDo->get('core.create')) {
			JToolBarHelper::custom('goal.save2copy', 'save-copy.png', 'save-copy_f2.png', 'JTOOLBAR_SAVE_AS_COPY', false);
		}
		if (empty($this->item->id)) {
			JToolBarHelper::cancel('goal.cancel', 'JTOOLBAR_CANCEL');
		}
		else {
			JToolBarHelper::cancel('goal.cancel', 'JTOOLBAR_CLOSE');
		}

	}
        
        function getUserGroup($user_id) {
            $db = JFactory::getDBO();
            $query = "SELECT title FROM #__usergroups WHERE id IN 
            (SELECT group_id FROM #__user_usergroup_map WHERE user_id='$user_id')";
            $db->setQuery($query);
            return $db->loadResult();
        }
}
