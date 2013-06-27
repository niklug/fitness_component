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
class multicalendarViewinsert extends JView
{
	
	function display( $tpl = null )
	{
		$db		=& JFactory::getDBO();
		$id		= JRequest::getVar( 'id', 0, '', 'int' );
		$query = 'SELECT a.id, a.title'
		. ' FROM #__dc_mv_calendars AS a'
		. ' WHERE a.published = 1'
		. ' ORDER BY a.title'
		;
		$db->setQuery( $query );
		
		$rows = $db->loadObjectList();
		
		$this->assignRef('insert',	$rows);
		$db->setQuery("select palettes from #__dc_mv_configuration where id=1");

		$p = $db->loadObjectList();
		$p = unserialize($p[0]->palettes);
		
		$this->assignRef('palette',	$p);
		parent::display($tpl);
		jexit();
	}
}