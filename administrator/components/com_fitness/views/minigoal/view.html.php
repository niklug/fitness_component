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
class FitnessViewMinigoal extends JView
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
                $document -> addscript( JUri::base() . 'components' . DS . 'com_fitness' . DS .'assets'. DS .'js'. DS . 'lib' . DS . 'jquery.js');
                $document -> addscript( JUri::root() . 'administrator/components' . DS . 'com_fitness' . DS .'assets'. DS .'js'. DS . 'jquery-ui.js');
                $document -> addscript( JUri::root() . 'administrator/components' . DS . 'com_fitness' . DS .'assets'. DS .'js'. DS . 'jquerynoconflict.js');
                $document -> addscript( JUri::base() . 'components' . DS . 'com_fitness' . DS .'assets'. DS .'js'. DS . 'lib' . DS . 'comments_class.js');
                $document -> addscript( JUri::root() . 'administrator/components' . DS . 'com_fitness' . DS .'assets'. DS .'js'. DS . 'lib' . DS . 'ajax_call_function.js');
                
                $document->addStyleSheet(JUri::root() . 'administrator/components/com_fitness/assets/css/jquery-ui.css');
                
		$this->addToolbar();
                
                // connect backend list model
                require_once JPATH_COMPONENT_ADMINISTRATOR . DS .  'models' . DS . 'goals.php';
                $backend_list_model  = new FitnessModelgoals();
                
                $this->assign('backend_list_model', $backend_list_model);
                
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

		JToolBarHelper::title(JText::_('COM_FITNESS_TITLE_MINIGOAL'), 'minigoal.png');

		// If not checked out, can save the item.
		if (!$checkedOut && ($canDo->get('core.edit')||($canDo->get('core.create'))))
		{

			JToolBarHelper::apply('minigoal.apply', 'JTOOLBAR_APPLY');
			JToolBarHelper::save('minigoal.save', 'JTOOLBAR_SAVE');
		}
		if (!$checkedOut && ($canDo->get('core.create'))){
			JToolBarHelper::custom('minigoal.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
		}
		// If an existing item, can save to a copy.
		if (!$isNew && $canDo->get('core.create')) {
			JToolBarHelper::custom('minigoal.save2copy', 'save-copy.png', 'save-copy_f2.png', 'JTOOLBAR_SAVE_AS_COPY', false);
		}
		if (empty($this->item->id)) {
			JToolBarHelper::cancel('minigoal.cancel', 'JTOOLBAR_CANCEL');
		}
		else {
			JToolBarHelper::cancel('minigoal.cancel', 'JTOOLBAR_CLOSE');
		}
                
        
	}
        

}
