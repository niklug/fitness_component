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

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport( 'joomla.application.component.controller' );


class MultiCalendarController extends JController
{
	/**
	 * Custom Constructor
	 */
	function __construct( $default = array())
	{
		parent::__construct( $default );

		$this->registerTask( 'apply', 		'save');
		$this->registerTask( 'unpublish', 	'publish');
		$this->registerTask( 'edit', 		'display');
		$this->registerTask( 'add' , 		'display' );
		$this->registerTask( 'load' , 		'display' );
		$this->registerTask( 'admin' , 		'display' );
	}

	function display( )
	{
		
		switch($this->getTask())
		{
			case 'add'     :
			{
				JRequest::setVar( 'hidemainmenu', 1 );
				JRequest::setVar( 'view', 'calendar'  );
				JRequest::setVar( 'edit', false  );
			} break;
			case 'edit'    :
			{
				JRequest::setVar( 'hidemainmenu', 1 );
				JRequest::setVar( 'view', 'calendar'  );
				JRequest::setVar( 'edit', true  );
			} break;
			case 'load'    :
			{
				JRequest::setVar( 'hidemainmenu', 1 );
				JRequest::setVar( 'layout', 'ajax'  );
				JRequest::setVar( 'view', 'admin'  );
			} break;
			case 'editevent'    :
			{
				JRequest::setVar( 'hidemainmenu', 1 );
				JRequest::setVar( 'layout', 'layout'  );
				JRequest::setVar( 'view', 'admin'  );
			} break;
			case 'admin'    :
			{
				JRequest::setVar( 'hidemainmenu', 1 );
				JRequest::setVar( 'view', 'admin'  );
			} break;
			case 'insert'    :
			{
				JRequest::setVar( 'hidemainmenu', 1 );
				JRequest::setVar( 'view', 'insert'  );
			} break;
			case 'images'    :
			{
				JRequest::setVar( 'hidemainmenu', 1 );
				JRequest::setVar( 'view', 'images'  );
			} break;
			
		}

		//Set the default view, just in case
		$view = JRequest::getCmd('view');
		if(empty($view)) {
			JRequest::setVar('view', 'calendars');
		};

		parent::display();
	}

	/**
	 * Saves a new or edited calendar main fields:
	 * it cincludes the calendar name and the calendar published flag
	 */
	function save()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		$db		=& JFactory::getDBO();

		// save the poll parent information
		$row	=& JTable::getInstance('multicalendar', 'Table');
		$post	= JRequest::get( 'post' );
		if (!$row->bind( $post ))
		{
			JError::raiseError(500, $row->getError() );
		}
		$isNew = ($row->id == 0);

		if (!$row->check())
		{
			JError::raiseError(500, $row->getError() );
		}

		if (!$row->store())
		{
			JError::raiseError(500, $row->getError() );
		}
		$row->checkin();

		switch ($this->_task)
		{
			case 'apply':
				$msg = JText::_( 'Changes to Calendar saved' );
				$link = 'index.php?option=com_multicalendar&view=poll&task=edit&cid[]='. $row->id .'';
				break;

			case 'save':
			default:
				$msg = JText::_( 'Calendar saved' );
				$link = 'index.php?option=com_multicalendar';
				break;
		}

		$this->setRedirect($link);
	}

	/**
	 * Deletes a calendar 
	 */
	function remove()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		$db		=& JFactory::getDBO();
		$cid	= JRequest::getVar( 'cid', array(), '', 'array' );

		JArrayHelper::toInteger($cid);
		$msg = '';

		for ($i=0, $n=count($cid); $i < $n; $i++)
		{
			$poll =& JTable::getInstance('multicalendar', 'Table');
			if (!$poll->delete( $cid[$i] ))
			{
				$msg .= $poll->getError();
			}
		}
		$this->setRedirect( 'index.php?option=com_multicalendar', $msg );
	}

	/**
	* Publishes or Unpublishes one or more records
	* @param array An array of unique category id numbers
	* @param integer 0 if unpublishing, 1 if publishing
	* @param string The current url option
	*/
	function publish()
	{
		$mainframe  =& JFactory::getApplication();
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		$db 	=& JFactory::getDBO();
		$user 	=& JFactory::getUser();

		$cid		= JRequest::getVar( 'cid', array(), '', 'array' );
		$publish	= ( $this->getTask() == 'publish' ? 1 : 0 );

		JArrayHelper::toInteger($cid);

		if (count( $cid ) < 1)
		{
			$action = $publish ? 'publish' : 'unpublish';
			JError::raiseError(500, JText::_( 'Select an item to' .$action, true ) );
		}

		$cids = implode( ',', $cid );

		$query = 'UPDATE #__dc_mv_calendars'
		. ' SET published = ' . (int) $publish
		. ' WHERE id IN ( '. $cids .' )'
		;
		$db->setQuery( $query );
		if (!$db->query())
		{
			JError::raiseError(500, $db->getErrorMsg() );
		}

		
		$mainframe->redirect( 'index.php?option=com_multicalendar' );
	}
	
	/**
	 * Updates the calendar content with the new data received by an AJAX request
	 * @return int|string
	 */
	function update() {
		function rquote ($str) {  	 
            if (get_magic_quotes_gpc () == 1 && (strpos($str, "'") === false || !(strpos($str, "'") === false)))
	          return $str;
	        else
              return addslashes($str);
        }
        $db 	=& JFactory::getDBO();
		$id		= JRequest::getVar( 'id' );
		if (substr($id,0,3)=="cal")
	        $id = substr($id,3);
		$multis		= JRequest::getVar( 'multis', '', 'POST', 'string', JREQUEST_ALLOWHTML );
		$dates = explode("\n*-*\n", $multis);
		$act		= JRequest::getVar( 'act' );
		if ($act=='del')
        {
            $sqlId = JRequest::getVar( 'sqlId' );
            $query ="delete from #__dc_mv_events where multi_calendar_id=".$id." and id=".$sqlId;
            $db->setQuery( $query );
            if (!$db->query())
		    {
		        JError::raiseError(500, $db->getErrorMsg() );
		    }
            
        }
        else if ($act=='edit')
        {
            $data = preg_split("/\n/",$multis);
            $dn = preg_split("/-/",$data[0]);
	        $d1 = preg_split("/\\//",$dn[0]);
	        $d2 = preg_split("/\\//",$dn[1]);
            $dfrom = $d1[2]."-".$d1[0]."-".$d1[1];
            $dto = $d2[2]."-".$d2[0]."-".$d2[1];
            $title = $data[1];
            $config = $data[count($data)-1];
            $img = $data[count($data)-2];
            $description = "";
            for ($j=2;$j<count($data)-2;$j++)
            {
                $description .= $data[$j];
                if ($j!=count($data)-3)
                    $description .= "\n";
            }
            $sqlId = JRequest::getVar( 'sqlId' );
            $query ="update  #__dc_mv_events set startdate='".$dfrom."',enddate='".$dto."',text=".$db->Quote($title).",img=".$db->Quote($img).",description=".$db->Quote($description).",config='".$config."'  where multi_calendar_id=".$id." and id=".$sqlId;
            $db->setQuery( $query );
            if (!$db->query())
		    {
		        JError::raiseError(500, $db->getErrorMsg() );
		    }
        }
        else if ($act=='add')
        {
            $data = preg_split("/\n/",$multis);
            $dn = preg_split("/-/",$data[0]);
	        $d1 = preg_split("/\//",$dn[0]);
	        $d2 = preg_split("/\//",$dn[1]);
            $dfrom = $d1[2]."-".$d1[0]."-".$d1[1];
            $dto = $d2[2]."-".$d2[0]."-".$d2[1];
            $title = $data[1];
            $config = $data[count($data)-1];
            $img = $data[count($data)-2];
            $description = "";
            for ($j=2;$j<count($data)-2;$j++)
            {
                $description .= $data[$j];
                if ($j!=count($data)-3)
                    $description .= "\n";
            }
            $query ="insert into #__dc_mv_events(multi_calendar_id,startdate,enddate,text,img,description,config) values(".$id.",'".$dfrom."','".$dto."',".$db->Quote($title).",".$db->Quote($img).",".$db->Quote($description).",'".$config."') "; 
            $db->setQuery( $query );
            if (!$db->query())
		    {
		        JError::raiseError(500, $db->getErrorMsg() );
		    }
            echo $db->insertid();
            
        }
            
		jexit();
	}

	function cancel()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		$id		= JRequest::getVar( 'id', 0, '', 'int' );
		$db		=& JFactory::getDBO();
		$row	=& JTable::getInstance('multicalendar', 'Table');

		$row->checkin( $id );
		$this->setRedirect( 'index.php?option=com_multicalendar' );
	}
}