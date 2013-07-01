<?php
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.view');
class multicalendarViewcalendar extends JView
{
	function display($tpl = null)
	{
		$mainframe  =& JFactory::getApplication();
		$db		=& JFactory::getDBO();
		$user 	=& JFactory::getUser();

		$cid 	= JRequest::getVar( 'cid', array(0), '', 'array' );
		$option = JRequest::getCmd( 'option');
		$uid 	= (int) @$cid[0];
		$edit=JRequest::getVar( 'edit', true );

		$calendar =& JTable::getInstance('multicalendar', 'Table');
		// load the row from the db table
		if($edit)
		$calendar->load( $uid );

		// fail if checked out not by 'me'
		if ($calendar->isCheckedOut( $user->get('id') )) {
			$msg = JText::sprintf( 'DESCBEINGEDITTED', JText::_( 'The calendar' ), $calendar->title );
			$this->setRedirect( 'index.php?option='. $option, $msg );
		}

		if ($calendar->id == 0)
		{
			// defaults
			$row->published	= 1;
		}


		$this->assignRef('calendar',	$calendar);
		$query = 'SELECT id AS value, name AS text'
        . ' FROM #__users'
        . ' WHERE block = 0'
        . ' ORDER BY id'
        ;
        $db->setQuery( $query );
        $rows = $db->loadObjectList();
        
        array_unshift($rows, JHtml::_('select.option', '0', JText::_('None')));
		$this->assignRef('users',	$rows);
		
		$db->setQuery(
			'SELECT a.id AS value, a.title AS text, COUNT(DISTINCT b.id) AS level' .
			' FROM #__usergroups AS a' .
			' LEFT JOIN `#__usergroups` AS b ON a.lft > b.lft AND a.rgt < b.rgt' .
			' GROUP BY a.id' .
			' ORDER BY a.lft ASC'
		);
		
        $rows1 = $db->loadObjectList();
        for ($i=0,$n=count($rows1); $i < $n; $i++) {
			$rows1[$i]->text = str_repeat('- ',$rows1[$i]->level).$rows1[$i]->text;
		}
        $groupsadd = $rows1;
        array_unshift($groupsadd, JHtml::_('select.option', '0', JText::_('None')));
		$this->assignRef('groupsAdd',	$groupsadd);
		array_unshift($rows1, JHtml::_('select.option', 'owner', JText::_('Event owner (creator)')));
        array_unshift($rows1, JHtml::_('select.option', '0', JText::_('None')));
		$this->assignRef('groups',	$rows1);
		
		$document	= & JFactory::getDocument();
		$document->addStyleSheet('components/com_multicalendar/css/styles.css');
		
		parent::display($tpl);

	}
}