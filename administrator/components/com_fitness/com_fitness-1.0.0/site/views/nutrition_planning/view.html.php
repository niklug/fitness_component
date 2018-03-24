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
 * View class for a list of Fitness.
 */
class FitnessViewNutrition_planning extends JView
{
	protected $items;
	protected $pagination;
	protected $state;

	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{
		$this->state		= $this->get('State');
		$this->items		= $this->get('Items');
		$this->pagination	= $this->get('Pagination');

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			throw new Exception(implode("\n", $errors));
		}

                
                $document = &JFactory::getDocument();
                $document -> addscript( JUri::root() . 'administrator/components/com_fitness/assets/js/lib/require.js');

                include_once JPATH_COMPONENT_ADMINISTRATOR . DS .'assets'. DS .'js'. DS . 'underscore_templates.html';

                $document->addStyleSheet(JUri::root() . 'components/com_fitness/assets/css/fitness.css');
                
                $document->addStyleSheet(JUri::root() . 'administrator/components/com_fitness/assets/css/jquery-ui.css');
                $document->addStyleSheet(JUri::root() . 'administrator/components' . DS . 'com_fitness' . DS .'assets'. DS .'css'. DS . 'jquery.timepicker.css');
                
                $document->addStyleSheet(JURI::root() . 'components' . DS . 'com_fitness' . DS . 'assets' . DS . 'css' . DS . 'jquery.cleditor.css');
                
                         // connect list nutrition_diaryform model
                require_once JPATH_COMPONENT_SITE . DS .  'models' . DS . 'nutrition_diaryform.php';
                
                $nutrition_diaryform_model  = new FitnessModelNutrition_diaryForm();
             
                $active_plan_data = $nutrition_diaryform_model->getActivePlanData();
                
                $this->assign('active_plan_data', $active_plan_data);
                
                
		parent::display($tpl);
	}

}
