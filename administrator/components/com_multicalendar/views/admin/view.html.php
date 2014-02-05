<?php
/**
* @version		$Id: view.html.php 10381 2008-06-01 03:35:53Z pasamio $
* @package		Joomla
* @subpackage	Config
* @copyright	Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.view');

/**
 * HTML View class for the Poll component
 *
 * @static
 * @package		Joomla
 * @subpackage	Poll
 * @since 1.0
 */
class multicalendarViewadmin extends JView
{
	
	function display( $tpl = null )
	{
		$task = JRequest::getVar( 'task' );
		$document	= & JFactory::getDocument();
		$document->addStyleSheet('components/com_multicalendar/css/styles.css');
                
   
         
      
                $this->addToolbar();
                
                ?>
                    
                        <ul id="submenu">
                            <li><a class="active" href="index.php?option=com_fitness&view=dashboard">Dashboard</a></li>
                            <li><a class="active" href="index.php?option=com_fitness&view=clients">Clients</a></li>
                            <li><a class="active" href="index.php?option=com_fitness&view=goals">Client Planning</a></li>
                            <li><a class="active" href="index.php?option=com_fitness&view=assessments">Assessments</a></li>
                            <li><a class="active" href="index.php?option=com_fitness&view=programs">Programs</a></li>
                            <li><a class="active" href="index.php?option=com_fitness&view=exercise_library">Exercise Library</a></li>
                            <li><a class="active" href="index.php?option=com_fitness&view=nutrition_plans">Nutrition Plans</a></li>
                            <li><a class="active" href="index.php?option=com_fitness&view=nutrition_diaries">Nutrition Diary</a></li>
                            <li><a class="active" href="index.php?option=com_fitness&view=nutrition_recipes">Recipe Database</a></li>
                            
                            <li><a class="active" href="index.php?option=com_fitness&view=settings">Settings</a></li>
                        </ul>
                        <div class="clr"></div>
                        </br>
                    
                
                <?php
		
		if ($task=="load"){
			$tpl = "load";
			parent::display($tpl);
		}
		else if ($task=="editevent"){
			$tpl = "editevent";
			parent::display($tpl);
		}
		else
		    parent::display($tpl);
	}
        
        	protected function addToolbar()
	{
      

            JToolBarHelper::title(JText::_('Calendar'), 'calendar.png');

            
        }
}