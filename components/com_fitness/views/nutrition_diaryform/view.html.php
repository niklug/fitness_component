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
class FitnessViewNutrition_diaryform extends JView {

    protected $state;
    protected $item;
    protected $form;
    protected $params;

    /**
     * Display the view
     */
    public function display($tpl = null) {
        
        $app	= JFactory::getApplication();
        $user		= JFactory::getUser();
        
        
        $this->state = $this->get('State');
        $this->item = $this->get('Data');

        $this->params = $app->getParams('com_fitness');
   		$this->form		= $this->get('Form');

        // Check for errors.
        if (count($errors = $this->get('Errors'))) {
            throw new Exception(implode("\n", $errors));
        }
        
        
            
        $authorised = $user->authorise('core.create', 'com_fitness');

        if ($authorised !== true) {
           throw new Exception(JText::_('JERROR_ALERTNOAUTHOR'));
        }    
        
        $nutrition_plan_id = $this->_prepareDocument();
        
        if(!$nutrition_plan_id) {
            JError::raiseNotice( 100, 'Please contact your Trainer and discuss your current Nutrition Plan before proceeding with your Nutrition Diary' );
            ?>
            <a href="<?php echo JRoute::_('index.php?option=com_fitness&task=nutrition_diary.cancel'); ?>" title="">Back</a>
            <?php
            return;
        }

        parent::display($tpl);
    }


	/**
	 * Prepares the document
	 */
	protected function _prepareDocument()
	{
		$app	= JFactory::getApplication();
		$menus	= $app->getMenu();
		$title	= null;

		// Because the application sets a default page title,
		// we need to get it from the menu item itself
		$menu = $menus->getActive();
		if($menu)
		{
			$this->params->def('page_heading', $this->params->get('page_title', $menu->title));
		} else {
			$this->params->def('page_heading', JText::_('com_fitness_DEFAULT_PAGE_TITLE'));
		}
		$title = $this->params->get('page_title', '');
		if (empty($title)) {
			$title = $app->getCfg('sitename');
		}
		elseif ($app->getCfg('sitename_pagetitles', 0) == 1) {
			$title = JText::sprintf('JPAGETITLE', $app->getCfg('sitename'), $title);
		}
		elseif ($app->getCfg('sitename_pagetitles', 0) == 2) {
			$title = JText::sprintf('JPAGETITLE', $title, $app->getCfg('sitename'));
		}
		$this->document->setTitle($title);

		if ($this->params->get('menu-meta_description'))
		{
			$this->document->setDescription($this->params->get('menu-meta_description'));
		}

		if ($this->params->get('menu-meta_keywords'))
		{
			$this->document->setMetadata('keywords', $this->params->get('menu-meta_keywords'));
		}

		if ($this->params->get('robots'))
		{
			$this->document->setMetadata('robots', $this->params->get('robots'));
		}

                $document = JFactory::getDocument();
                $document->addStyleSheet('components/com_fitness/assets/css/fitness.css');
                $document -> addscript( JUri::root() . 'administrator/components' . DS . 'com_fitness' . DS .'assets'. DS .'js'. DS . 'jquery.js');
                $document -> addscript( JUri::root() . 'administrator/components' . DS . 'com_fitness' . DS .'assets'. DS .'js'. DS . 'jquerynoconflict.js');
                $document -> addscript( JUri::root() . 'administrator/components' . DS . 'com_fitness' . DS .'assets'. DS .'js'. DS . 'underscore-min.js');
                include_once JPATH_COMPONENT_ADMINISTRATOR . DS .'assets'. DS .'js'. DS . 'underscore_templates.html';
                $document -> addscript( JUri::root() . 'administrator/components' . DS . 'com_fitness' . DS .'assets'. DS .'js'. DS . 'backbone-min.js');
                echo '<!--[if IE]><script type="text/javascript" src="' . JUri::base() . 'administrator/components' . DS . 'com_fitness' . DS .'assets'. DS .'js'. DS . 'excanvas.js"></script><![endif]-->';
                $document -> addscript( JUri::root() . 'administrator/components' . DS . 'com_fitness' . DS .'assets'. DS .'js'. DS . 'jquery.flot.js');
                $document -> addscript( JUri::root() . 'administrator/components' . DS . 'com_fitness' . DS .'assets'. DS .'js'. DS . 'jquery.flot.pie.js');
                $document -> addscript( JUri::root() . 'administrator/components' . DS . 'com_fitness' . DS .'assets'. DS .'js'. DS . 'flot_pie_class.js');
                $document -> addscript( JUri::root() . 'administrator/components' . DS . 'com_fitness' . DS .'assets'. DS .'js'. DS . 'dayly_targets_class.js');
                $document -> addscript( JUri::root() . 'administrator/components' . DS . 'com_fitness' . DS .'assets'. DS .'js'. DS . 'plan_summary_class.js');
                $document -> addscript( JUri::root() . 'administrator/components' . DS . 'com_fitness' . DS .'assets'. DS .'js'. DS . 'comments_class.js');
                $document -> addscript( JUri::root() . 'administrator/components' . DS . 'com_fitness' . DS .'assets'. DS .'js'. DS . 'meal_description_class.js');
                $document -> addscript( JUri::root() . 'administrator/components' . DS . 'com_fitness' . DS .'assets'. DS .'js'. DS . 'nutrition_meal_class.js');
                
                
                
                $document -> addscript( JUri::base() . 'administrator/components' . DS . 'com_fitness' . DS .'assets'. DS .'js'. DS . 'jquery.timepicker.min.js');
                $document -> addscript( JUri::base() . 'administrator/components' . DS . 'com_fitness' . DS .'assets'. DS .'js'. DS . 'jquery-ui.js');
                
                $document -> addscript( JUri::root() . 'administrator/components' . DS . 'com_fitness' . DS .'assets'. DS .'js'. DS . 'gredient_graph.js');

                $document->addStyleSheet( JUri::base() . 'administrator/components' . DS . 'com_fitness' . DS .'assets'. DS .'css'. DS . 'jquery.timepicker.css');
                
                $document->addStyleSheet(JUri::root() . 'administrator/components/com_fitness/assets/css/jquery-ui.css');
                

                $model = $this->getModel();
                
                $user = &JFactory::getUser();
                require_once  JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_fitness' . DS .'helpers' . DS . 'fitness.php';
                
                $helper = new FitnessHelper();
                
                
                $secondary_trainers = $helper->get_client_trainers_names($user->id, 'secondary');
                
                $active_plan_data = $model->getActivePlanData();
                
                // connect list frontend form model
                require_once JPATH_COMPONENT_SITE . DS .  'models' . DS . 'nutrition_diaries.php';
                
                $frontend_list_model  = new FitnessModelNutrition_diaries();
                
                
                
                $nutrition_plan_id = $this->item->nutrition_plan_id ? $this->item->nutrition_plan_id : $active_plan_data->id;
                
                $this->assign('model', $model);
                $this->assign('frontend_list_model', $frontend_list_model);
                $this->assign('secondary_trainers', $secondary_trainers['data']);
                $this->assign('active_plan_data', $active_plan_data);
                $this->assign('nutrition_plan_id', $nutrition_plan_id);
                
                return $nutrition_plan_id;
	}        
    
}
