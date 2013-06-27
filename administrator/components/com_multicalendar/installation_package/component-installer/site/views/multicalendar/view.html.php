<?php
/**
* @Copyright Copyright (C) 2010 CodePeople, www.codepeople.net
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*
* This file is part of Multi Calendar for Joomla <www.joomlacalendars.com>.
* 
* Multi Calendar for Joomla is free software: you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation, either version 3 of the License, or
* (at your option) any later version.
*
* Multi Calendar for Joomla  is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License
* along with Multi Calendar for Joomla.  If not, see <http://www.gnu.org/licenses/>.
*
**/

defined('_JEXEC') or die( 'Restricted access' );

jimport( 'joomla.application.component.view');

class multicalendarViewmulticalendar extends JView
{
	function display($tpl = null)
	{
	    $task = JRequest::getVar( 'task' );
		if ($task=="load"){
			$tpl = "load";
			parent::display($tpl);
		}
		else if ($task=="editevent"){
			$tpl = "editevent";
			parent::display($tpl);
		}
		else
		{
		    $mainframe = JFactory::getApplication();
		
		    $document =& JFactory::getDocument();
		    
		    // Adds parameter handling
		    $params = $mainframe->getParams();
            
		    //Set page title information
		    $menus	= $mainframe->getMenu();
		    $menu	= $menus->getActive();
            $this->assignRef('params',	$params);
            if($menu)
		    {
		    	$this->params->def('page_heading', $this->params->get('page_title', $menu->title));
		    } else {
		    	$this->params->def('page_heading', JText::_('COM_MULTICALENDAR_DEFAULT_PAGE_TITLE'));
		    }
		    $title = $this->params->get('page_title', '');
		    if (empty($title)) {
		    	$title = $mainframe->getCfg('sitename');
		    }
		    elseif ($mainframe->getCfg('sitename_pagetitles', 0)) {
		    	$title = JText::sprintf('JPAGETITLE', $mainframe->getCfg('sitename'), $title);
		    }
		    $this->document->setTitle($title);
            
		    $multicalendar_id = $this->params->get('id', 1);
		    
		    $multicalendar =& JTable::getInstance('multicalendar', 'Table');
		    $multicalendar->load( $multicalendar_id );
            
		    $this->params->set('multicalendar_id',	$multicalendar_id);
		    $this->assignRef('multicalendar',	$multicalendar);
		    
		    parent::display($tpl);
		}    
	}
}
?>