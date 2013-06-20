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

defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.application.component.controller');


class MultiCalendarController extends JController
{
    function __construct($config = array())
	{
		if(JRequest::getCmd('view') === 'insert') {
			$config['base_path'] = JPATH_COMPONENT_ADMINISTRATOR;
		}
		parent::__construct($config);
	}
	function display()
	{
		$task = JRequest::getVar( 'task' );
		$rView = JRequest::getVar( 'view' );
		if ($task=="load")
		{
			JRequest::setVar( 'layout', 'ajax'  );
			JRequest::setVar( 'view', 'multicalendar'  );
		} 
		else if ($task=="editevent")
		{
			JRequest::setVar( 'layout', 'layout'  );
			JRequest::setVar( 'view', 'multicalendar'  );
		}
		else if ($rView!='insert') {
			switch ($rView) {
				default:
					JRequest::setVar('view','multicalendar'); // force it to be the multicalendar view;
				break;
			}
		    
		}
		parent::display();
	}
		
}
?>