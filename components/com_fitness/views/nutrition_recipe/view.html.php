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
                
                $document = &JFactory::getDocument();
                $document -> addscript( JUri::root() . 'administrator/components' . DS . 'com_fitness' . DS .'assets'. DS .'js'. DS . 'jquery.js');
                $document->addStyleSheet(JUri::root() . 'administrator/components/com_fitness/assets/css/fitness.css');
                $document->addStyleSheet(JUri::root() . 'administrator/templates/system/css/system.css');
                $document->addStyleSheet(JUri::root() . 'administrator/templates/bluestork/css/template.css');
   

		parent::display($tpl);
                
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
