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

class multicalendarViewconfiguration extends JView
{
	
	function display( $tpl = null )
	{
	    $db		=& JFactory::getDBO();
		$id		= JRequest::getVar( 'id', 0, '', 'int' );
		if (JRequest::getVar( 'task') == 'ajax')
		{
	        switch(JRequest::getVar( 'tab'))
		    {
		    	case '1':
		    	{
		    		$value = array();
		    		$value["views"] = JRequest::getVar( 'views');
		    		$value["viewdefault"] = JRequest::getVar( 'viewdefault');
		    		$value["language"] = JRequest::getVar( 'language');
		    		$value["start_weekday"] = JRequest::getVar( 'start_weekday');
		    		$value["cssStyle"] = JRequest::getVar( 'cssStyle');
		    		$value["paletteColor"] = JRequest::getVar( 'paletteColor');
		    		$value["btoday"] = JRequest::getVar( 'btoday');
		    		$value["bnavigation"] = JRequest::getVar( 'bnavigation');
		    		$value["brefresh"] = JRequest::getVar( 'brefresh');
		    		$value["numberOfMonths"] = JRequest::getVar( 'numberOfMonths');
		    		$value["sample0"] = JRequest::getVar('sample0');
		    		$value["sample1"] = JRequest::getVar('sample1');
		    		$value["sample2"] = JRequest::getVar('sample2');
		    		$value["sample3"] = JRequest::getVar('sample3');
		    		$value["sample4"] = JRequest::getVar('sample4');
		    		$query = "update #__dc_mv_configuration set administration=".$db->Quote(serialize($value))." where id=1";
		    		$db->setQuery( $query );
		    		if (!$db->query())
		            {
		                JError::raiseError(500, $db->getErrorMsg() );
		            }				
		    	} 
		    	break;
		    	case '2':
		    	{
		    		$value = JRequest::getVar( 'items');		    		
		    		$query = "update #__dc_mv_configuration set palettes=".$db->Quote(serialize($value))." where id=1";
		    		$db->setQuery( $query );
		    		if (!$db->query())
		            {
		                JError::raiseError(500, $db->getErrorMsg() );
		            }				
		    	} 
		    	break;		
		    }
		    jexit();
	    }
		else
		{
		
		
		    $query = 'SELECT * '
            . ' FROM #__dc_mv_configuration'
            . ' WHERE id = 1';
            $db->setQuery( $query );
            $rows = $db->loadObjectList();
		    
		    $this->assignRef('configuration',	$rows);
		    parent::display($tpl);
	    }
	}
}