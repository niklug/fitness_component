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
                $document -> addscript( JUri::root() . 'administrator/components' . DS . 'com_fitness' . DS .'assets'. DS .'js'. DS . 'jquery.js');
                $document -> addscript( JUri::root() . 'administrator/components' . DS . 'com_fitness' . DS .'assets'. DS .'js'. DS . 'jquery-ui.js');
                $document -> addscript( JUri::root() . 'administrator/components' . DS . 'com_fitness' . DS .'assets'. DS .'js'. DS . 'jquery.validate.min.js');
                $document -> addscript( JUri::root() . 'administrator/components' . DS . 'com_fitness' . DS .'assets'. DS .'js'. DS . 'jquerynoconflict.js');
                $document -> addscript( JUri::root() . 'administrator/components' . DS . 'com_fitness' . DS .'assets'. DS .'js'. DS . 'ajax_call_function.js');
                $document -> addscript( JUri::root() . 'administrator/components' . DS . 'com_fitness' . DS .'assets'. DS .'js'. DS . 'underscore-min.js');
                include_once JPATH_COMPONENT_ADMINISTRATOR . DS .'assets'. DS .'js'. DS . 'underscore_templates.html';
                $document -> addscript( JUri::root() . 'administrator/components' . DS . 'com_fitness' . DS .'assets'. DS .'js'. DS . 'backbone-min.js');
                $document -> addscript( JUri::root() . 'administrator/components' . DS . 'com_fitness' . DS .'assets'. DS .'js'. DS . 'moment.min.js');
                $document -> addscript( JUri::root() . 'administrator/components' . DS . 'com_fitness' . DS .'assets'. DS .'js'. DS . 'comments_class.js');
                $document -> addscript( JUri::root() . 'administrator/components' . DS . 'com_fitness' . DS .'assets'. DS .'js'. DS . 'status_class.js');
                $document -> addscript( JUri::root() . 'administrator/components' . DS . 'com_fitness' . DS .'assets'. DS .'js'. DS . 'jquery.flot.js');
                $document -> addscript( JUri::root() . 'administrator/components' . DS . 'com_fitness' . DS .'assets'. DS .'js'. DS . 'jquery.flot.time.js');
                $document -> addscript( JUri::root() . 'administrator/components' . DS . 'com_fitness' . DS .'assets'. DS .'js'. DS . 'jquerynoconflict.js');
                echo '<!--[if IE]><script type="text/javascript" src="' . JUri::base() . 'administrator/components' . DS . 'com_fitness' . DS .'assets'. DS .'js'. DS . 'excanvas.js"></script><![endif]-->';
                $document -> addscript( JUri::root() . 'administrator/components' . DS . 'com_fitness' . DS .'assets'. DS .'js'. DS . 'jquery.flot.pie.js');
                $document -> addscript( JUri::root() . 'administrator/components' . DS . 'com_fitness' . DS .'assets'. DS .'js'. DS . 'flot_pie_class.js');
                echo '<!--[if IE]><script type="text/javascript" src="' . JUri::root() . 'administrator/components' . DS . 'com_fitness' . DS .'assets'. DS .'js'. DS . 'excanvas.js"></script><![endif]-->';
        
                $document->addStyleSheet(JUri::root() . 'components/com_fitness/assets/css/fitness.css');
                
                $document->addStyleSheet(JUri::root() . 'administrator/components/com_fitness/assets/css/jquery-ui.css');
                
                $document -> addscript( JUri::root() . 'administrator/components' . DS . 'com_fitness' . DS .'assets'. DS .'js'. DS . 'goals_frontend.js');
                
                // connect list nutrition_diaryform model
                require_once JPATH_COMPONENT_SITE . DS .  'models' . DS . 'nutrition_diaryform.php';
                
                $nutrition_diaryform_model  = new FitnessModelNutrition_diaryForm();
                
                // connect list nutrition_diaryform model
                require_once JPATH_COMPONENT_SITE . DS .  'models' . DS . 'goals_periods.php';
                
                $goals_periods_model  = new FitnessModelgoals_periods();
             
                $active_plan_data = $nutrition_diaryform_model->getActivePlanData();
                
                $user = &JFactory::getUser();
                $secondary_trainers = $nutrition_diaryform_model->get_client_trainers($user->id);
                if(!$secondary_trainers['status']) {
                    echo $secondary_trainers['message'];
                }
       
                $this->assign('nutrition_diaryform_model', $nutrition_diaryform_model);

                $this->assign('active_plan_data', $active_plan_data);
                
                $this->assign('secondary_trainers', $secondary_trainers['data']);
                
                $this->assign('goals_periods_model', $goals_periods_model);
                
		parent::display($tpl);
	}

}
