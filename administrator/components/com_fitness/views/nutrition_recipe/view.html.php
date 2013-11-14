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
class FitnessViewNutrition_recipe extends JView
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

		JToolBarHelper::title(JText::_('COM_FITNESS_TITLE_NUTRITION_RECIPE'), 'nutrition_recipe.png');

		// If not checked out, can save the item.
		if (!$checkedOut && ($canDo->get('core.edit')||($canDo->get('core.create'))))
		{

			JToolBarHelper::apply('nutrition_recipe.apply', 'JTOOLBAR_APPLY');
			JToolBarHelper::save('nutrition_recipe.save', 'JTOOLBAR_SAVE');
		}
		if (!$checkedOut && ($canDo->get('core.create'))){
			JToolBarHelper::custom('nutrition_recipe.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
		}
		// If an existing item, can save to a copy.
		if (!$isNew && $canDo->get('core.create')) {
			JToolBarHelper::custom('nutrition_recipe.save2copy', 'save-copy.png', 'save-copy_f2.png', 'JTOOLBAR_SAVE_AS_COPY', false);
		}
		if (empty($this->item->id)) {
			JToolBarHelper::cancel('nutrition_recipe.cancel', 'JTOOLBAR_CANCEL');
		}
		else {
			JToolBarHelper::cancel('nutrition_recipe.cancel', 'JTOOLBAR_CLOSE');
		}

	}
        
                function getRecipeTypeByName($id) {
            $db = JFactory::getDbo();
            $sql = "SELECT name FROM #__fitness_recipe_types WHERE id='$id' AND state='1'";
            $db->setQuery($sql);
            if(!$db->query()) {
                JError::raiseError($db->getErrorMsg());
            }
            return $db->loadResult();
        }
}
