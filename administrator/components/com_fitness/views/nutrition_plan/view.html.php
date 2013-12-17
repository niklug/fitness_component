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
class FitnessViewNutrition_plan extends JView
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
                $document = &JFactory::getDocument();
                $document -> addscript( JUri::base() . 'components' . DS . 'com_fitness' . DS .'assets'. DS .'js'. DS . 'jquery.js');
                $document -> addscript( JUri::root() . 'administrator/components' . DS . 'com_fitness' . DS .'assets'. DS .'js'. DS . 'jquerynoconflict.js');
                $document -> addscript( JUri::root() . 'administrator/components' . DS . 'com_fitness' . DS .'assets'. DS .'js'. DS . 'underscore-min.js');
                include_once JPATH_COMPONENT_ADMINISTRATOR . DS .'assets'. DS .'js'. DS . 'underscore_templates.html';
                $document -> addscript( JUri::root() . 'administrator/components' . DS . 'com_fitness' . DS .'assets'. DS .'js'. DS . 'backbone-min.js');
                $document -> addscript( JUri::root() . 'administrator/components' . DS . 'com_fitness' . DS .'assets'. DS .'js'. DS . 'ajax_call_function.js');
                $document -> addscript( JUri::root() . 'administrator/components' . DS . 'com_fitness' . DS .'assets'. DS .'js'. DS . 'fitness_helper.js');
                $document -> addscript( JUri::base() . 'components' . DS . 'com_fitness' . DS .'assets'. DS .'js'. DS . 'plan_summary_class.js');
                $document -> addscript( JUri::base() . 'components' . DS . 'com_fitness' . DS .'assets'. DS .'js'. DS . 'nutrition_plan_class.js');
                $document -> addscript( JUri::base() . 'components' . DS . 'com_fitness' . DS .'assets'. DS .'js'. DS . 'dayly_targets_class.js');
                $document -> addscript( JUri::base() . 'components' . DS . 'com_fitness' . DS .'assets'. DS .'js'. DS . 'comments_class.js');
                $document -> addscript( JUri::base() . 'components' . DS . 'com_fitness' . DS .'assets'. DS .'js'. DS . 'meal_description_class.js');
                $document -> addscript( JUri::base() . 'components' . DS . 'com_fitness' . DS .'assets'. DS .'js'. DS . 'nutrition_meal_class.js');
                $document -> addscript( JUri::base() . 'components' . DS . 'com_fitness' . DS .'assets'. DS .'js'. DS . 'shopping_list_class.js');
                $document -> addscript( JUri::base() . 'components' . DS . 'com_fitness' . DS .'assets'. DS .'js'. DS . 'jquery.timepicker.min.js');
                $document -> addscript( JUri::base() . 'components' . DS . 'com_fitness' . DS .'assets'. DS .'js'. DS . 'jquery-ui.js');
                $document -> addscript( JUri::root() . 'administrator/components' . DS . 'com_fitness' . DS .'assets'. DS .'js'. DS . 'backbone-validation-min.js');
                $document -> addscript( JUri::root() . 'administrator/components' . DS . 'com_fitness' . DS .'assets'. DS .'js'. DS . 'backbone.syphon.min.js');
                $document -> addscript( JUri::root() . 'administrator/components' . DS . 'com_fitness' . DS .'assets'. DS .'js'. DS . 'nutrition_plan_supplements.js');
                
                $document->addStyleSheet('components/com_fitness/assets/css/fitness.css');
                $document->addStyleSheet( JUri::base() . 'components' . DS . 'com_fitness' . DS .'assets'. DS .'css'. DS . 'jquery.timepicker.css');
                
                $document->addStyleSheet(JUri::root() . 'administrator/components/com_fitness/assets/css/jquery-ui.css');
		
                
                // connect backend list model
                require_once JPATH_COMPONENT_ADMINISTRATOR . DS .  'models' . DS . 'nutrition_plans.php';
                $backend_list_model  = new FitnessModelnutrition_plans();
                
                $this->assign('backend_list_model', $backend_list_model);
                
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

		JToolBarHelper::title(JText::_('COM_FITNESS_TITLE_NUTRITION_PLAN'), 'nutrition_plan.png');

		// If not checked out, can save the item.
		if (!$checkedOut && ($canDo->get('core.edit')||($canDo->get('core.create'))))
		{

			JToolBarHelper::apply('nutrition_plan.apply', 'JTOOLBAR_APPLY');
			JToolBarHelper::save('nutrition_plan.save', 'JTOOLBAR_SAVE');
		}
		if (!$checkedOut && ($canDo->get('core.create'))){
			JToolBarHelper::custom('nutrition_plan.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
		}
		// If an existing item, can save to a copy.
		if (!$isNew && $canDo->get('core.create')) {
			JToolBarHelper::custom('nutrition_plan.save2copy', 'save-copy.png', 'save-copy_f2.png', 'JTOOLBAR_SAVE_AS_COPY', false);
		}
		if (empty($this->item->id)) {
			JToolBarHelper::cancel('nutrition_plan.cancel', 'JTOOLBAR_CANCEL');
		}
		else {
			JToolBarHelper::cancel('nutrition_plan.cancel', 'JTOOLBAR_CLOSE');
		}

	}
        
        function getPrimaryGoalName($id) {
            $db = JFactory::getDbo();
     
            $sql = "SELECT name FROM #__fitness_goal_categories WHERE id='$id' AND state='1'";
            $db->setQuery($sql);
            if(!$db->query()) {
                JError::raiseError($db->getErrorMsg());
            }
            $result = $db->loadResult();
            return $result;
        }
        public function showActiveStatus($id, $active_id) {
            $html = '<span style="color:red;">INACTIVE</span>';
            if((int)$id == (int)$active_id) {
                $html = '<span style="color:green;">ACTIVE</span>';
            } 
            $html = '<span style="color:red;">INACTIVE</span>';
            return $html; 
        }
}
