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

defined('_JEXEC') or die('Restricted access');
require_once( JPATH_COMPONENT.'/DC_MultiViewCal/php/functions.php' );
require_once( JPATH_BASE.'/components/com_multicalendar/DC_MultiViewCal/php/list.inc.php' );

$db 	=& JFactory::getDBO();
header('Content-type:text/javascript;charset=UTF-8');
$method = JRequest::getVar( 'method' );
$calid = JRequest::getVar( 'calid' );

switch ($method) {
    case "add":
        $ret = addCalendar($calid, JRequest::getVar("CalendarStartTime"), JRequest::getVar("CalendarEndTime"), JRequest::getVar("CalendarTitle"), JRequest::getVar("IsAllDayEvent"), JRequest::getVar("location"));
        break;
    case "list":
        //$ret = listCalendar(JRequest::getVar("showdate"), JRequest::getVar("viewtype"));

        $d1 = js2PhpTime(JRequest::getVar("startdate"));
        $d2 = js2PhpTime(JRequest::getVar("enddate"));

        $d1 = mktime(0, 0, 0,  date("m", $d1), date("d", $d1), date("Y", $d1));
        $d2 = mktime(0, 0, 0, date("m", $d2), date("d", $d2), date("Y", $d2))+24*60*60-1;
        $ret = listCalendarByRange($calid, ($d1),($d2));

        break;
    case "update":
        $ret = updateCalendar(JRequest::getVar("calendarId"), JRequest::getVar("CalendarStartTime"), JRequest::getVar("CalendarEndTime"));
        break;
    case "remove":
        $ret = removeCalendar( JRequest::getVar("calendarId"),JRequest::getVar("rruleType"));
        break;
    case "adddetails":

        $st = JRequest::getVar("stpartdatelast") . " " . JRequest::getVar("stparttimelast");
        $et = JRequest::getVar("etpartdatelast") . " " . JRequest::getVar("etparttimelast");
        if(JRequest::getVar("id")!=""){

            $ret = updateDetailedCalendar(JRequest::getVar("id"), $st, $et,
                JRequest::getVar("Subject"), (JRequest::getVar("IsAllDayEvent")==1)?1:0, JRequest::getVar('Description','','POST','STRING',JREQUEST_ALLOWHTML) ,
                JRequest::getVar("Location"), JRequest::getVar("colorvalue"), JRequest::getVar("rrule"),JRequest::getVar("rruleType"), JRequest::getVar("timezone"));
        }else{

            $ret = addDetailedCalendar($calid, $st, $et,JRequest::getVar("Subject"), (JRequest::getVar("IsAllDayEvent")==1)?1:0, JRequest::getVar('Description','','POST','STRING',JREQUEST_ALLOWHTML) ,
                JRequest::getVar("Location"), JRequest::getVar("colorvalue"), JRequest::getVar("rrule"),0, JRequest::getVar("timezone"));
        }
        break;


}
echo json_encode($ret);
function checkIfOverlappingThisEvent($id, $st, $et)
{
    $db 	=& JFactory::getDBO();
    $sql = "select * from `".DC_MV_CAL."` where id=".$id;

    $db->setQuery( $sql );
    $rows = $db->loadObjectList();
    if (count($rows)>0)
        return checkIfOverlapping($rows[0]->calid, $st, $et, $rows[0]->title, $rows[0]->location,$id);
    else
        return true;
}
function checkIfOverlapping($calid, $st, $et, $sub, $loc,$id)
{
    $db 	=& JFactory::getDBO();
    $sd = date("Y-m-d H:i:s",js2PhpTime($st));
    $ed = date("Y-m-d H:i:s",js2PhpTime($et));
    $condition = "";
    if (JC_NO_OVERLAPPING_TIME)
        $condition .= " and ( (`".DC_MV_CAL_FROM."` > '"
      .($sd)."' and `".DC_MV_CAL_FROM."` < '". ($ed)."') or (`".DC_MV_CAL_TO."` > '"
      .($sd)."' and `".DC_MV_CAL_TO."` < '". ($ed)."') or (`".DC_MV_CAL_FROM."` <= '"
      .($sd)."' and `".DC_MV_CAL_TO."` >= '". ($ed)."') )   ";
    if (JC_NO_OVERLAPPING_SUBJECT)
        $condition .= " and ( `".DC_MV_CAL_TITLE."` = '". $sub."' )   ";
    if (JC_NO_OVERLAPPING_LOCATION)
        $condition .= " and ( `".DC_MV_CAL_LOCATION."` = '". $loc."' )   ";
    if ($condition=="")
        $condition = " and 1=0";
    $sql = "select * from `".DC_MV_CAL."` where ".DC_MV_CAL_IDCAL."=".$calid.$condition;

    $db->setQuery( $sql );

    $rows = $db->loadObjectList();
    if (count($rows)==0 || (count($rows)==1 && $rows[0]->id==$id))
        return true;
    else
        return false;

}
function getMessageOverlapping()
{
    $ret = array();
    $ret['IsSuccess'] = false;
    $ret['Msg'] = "OVERLAPPING";
    return $ret;
}
function addCalendar($calid, $st, $et, $sub, $ade, $loc){
  $ret = array();
  $db 	=& JFactory::getDBO();
  $user =& JFactory::getUser();
  try{
    if (checkIfOverlapping($calid, $st, $et,$sub, $loc,0))
    {
    $sql = "insert into `".DC_MV_CAL."` (`".DC_MV_CAL_IDCAL."`,`".DC_MV_CAL_TITLE."`, `".DC_MV_CAL_FROM."`, `".DC_MV_CAL_TO."`, `".DC_MV_CAL_ISALLDAY."`, `".DC_MV_CAL_LOCATION."`, `owner`, `published`) values (".$calid.","
      .$db->Quote($sub).", '"
      .php2MySqlTime(js2PhpTime($st))."', '"
      .php2MySqlTime(js2PhpTime($et))."', "
      .$db->Quote($ade).", "
      .$db->Quote($loc).", ".$user->id.",1)";

    $db->setQuery( $sql );
    if (!$db->query()){
      $ret['IsSuccess'] = false;
      $ret['Msg'] = $db->stderr();
    }else{
      $ret['IsSuccess'] = true;
      $ret['Msg'] = 'add success';
      $ret['Data'] = $db->insertid();
    }
    }
    else
     $ret = getMessageOverlapping();

	}catch(Exception $e){
     $ret['IsSuccess'] = false;
     $ret['Msg'] = $e->getMessage();
  }

  return $ret;
}


function addDetailedCalendar($calid, $st, $et, $sub, $ade, $dscr, $loc, $color, $rrule,$uid,$tz){
  $ret = array();

  $db 	=& JFactory::getDBO();
  $user =& JFactory::getUser();
  try{
    if (checkIfOverlapping($calid, $st, $et,$sub, $loc,0))
    {
    $sql = "insert into `".DC_MV_CAL."` (`".DC_MV_CAL_IDCAL."`,`".DC_MV_CAL_TITLE."`, `".DC_MV_CAL_FROM."`, `".DC_MV_CAL_TO."`, `".DC_MV_CAL_ISALLDAY."`, `".DC_MV_CAL_DESCRIPTION."`, `".DC_MV_CAL_LOCATION."`, `".DC_MV_CAL_COLOR."`,`rrule`,`uid`,`owner`, `published`) values (".$calid.","
      .$db->Quote($sub).", '"
      .php2MySqlTime(js2PhpTime($st))."', '"
      .php2MySqlTime(js2PhpTime($et))."', "
      .$db->Quote($ade).", "
      .$db->Quote($dscr).", "
      .$db->Quote($loc).", "
      .$db->Quote($color).", ".$db->Quote($rrule).", ".$db->Quote($uid).", ".$user->id.",1 )";

    $db->setQuery( $sql );
    if (!$db->query()){
      $ret['IsSuccess'] = false;
      $ret['Msg'] = $db->stderr();
    }else{
      $ret['IsSuccess'] = true;
      $ret['Msg'] = 'add success';
      $ret['Data'] = $db->insertid();
    }
    }
    else
     $ret = getMessageOverlapping();
	}catch(Exception $e){
     $ret['IsSuccess'] = false;
     $ret['Msg'] = $e->getMessage();
  }
  return $ret;
}

function listCalendarByRange($calid,$sd, $ed){
  $ret = array();
  $ret['events'] = array();
  $ret["issort"] =true;
  $ret["start"] = php2JsTime($sd);
  $ret["end"] = php2JsTime($ed);
  $ret['error'] = null;
  $db 	=& JFactory::getDBO();
  try{
    $sql = "select * from `".DC_MV_CAL."` where ".DC_MV_CAL_IDCAL."=".$calid." and ( (`".DC_MV_CAL_FROM."` between '"
      .php2MySqlTime($sd)."' and '". php2MySqlTime($ed)."') or (`".DC_MV_CAL_TO."` between '"
      .php2MySqlTime($sd)."' and '". php2MySqlTime($ed)."') or (`".DC_MV_CAL_FROM."` <= '"
      .php2MySqlTime($sd)."' and `".DC_MV_CAL_TO."` >= '". php2MySqlTime($ed)."') or rrule<>'') order by uid desc,  ".DC_MV_CAL_FROM."  ";

    $db->setQuery( $sql );
    if (!$db->query()){
          $ret['IsSuccess'] = false;
          $ret['Msg'] = $db->stderr();
    }
    $rows = $db->loadObjectList();


    $str = "";
    for ($i=0;$i<count($rows);$i++)
    {
        $row = $rows[$i];
        if (strlen($row->exdate)>0)
            $row->rrule .= ";exdate=".$row->exdate;
        $ev = array(
            $row->id,
            $row->title,
            php2JsTime(mySql2PhpTime($row->starttime)),
            php2JsTime(mySql2PhpTime($row->endtime)),
            $row->isalldayevent,
            0, //more than one day event
            //$row->InstanceType,
            ((is_numeric($row->uid) && $row->uid>0)?$row->uid:$row->rrule),//Recurring event rule,
            $row->color,
            1,//editable
            $row->location,
            '',//$attends
            $row->description,
            $row->owner,
            $row->published
        );
        $ret['events'][] = $ev;
    }
	}catch(Exception $e){
     $ret['error'] = $e->getMessage();
  }
  return $ret;
}
function listCalendar($day, $type){
  $phpTime = js2PhpTime($day);
  //echo $phpTime . "+" . $type;
  switch($type){
    case "month":
      $st = mktime(0, 0, 0, date("m", $phpTime), 1, date("Y", $phpTime));
      $et = mktime(0, 0, -1, date("m", $phpTime)+1, 1, date("Y", $phpTime));
      break;
    case "week":
      //suppose first day of a week is monday
      $monday  =  date("d", $phpTime) - date('N', $phpTime) + 1;
      //echo date('N', $phpTime);
      $st = mktime(0,0,0,date("m", $phpTime), $monday, date("Y", $phpTime));
      $et = mktime(0,0,-1,date("m", $phpTime), $monday+7, date("Y", $phpTime));
      break;
    case "day":
      $st = mktime(0, 0, 0, date("m", $phpTime), date("d", $phpTime), date("Y", $phpTime));
      $et = mktime(0, 0, -1, date("m", $phpTime), date("d", $phpTime)+1, date("Y", $phpTime));
      break;
  }
  //echo $st . "--" . $et;
  return listCalendarByRange($st, $et);
}

function updateCalendar($id, $st, $et){
  $ret = array();
  $db 	=& JFactory::getDBO();
  try{
    if (checkIfOverlappingThisEvent($id, $st, $et))
    {
        $sql = "update `".DC_MV_CAL."` set"
          . " `".DC_MV_CAL_FROM."`='" . php2MySqlTime(js2PhpTime($st)) . "', "
          . " `".DC_MV_CAL_TO."`='" . php2MySqlTime(js2PhpTime($et)) . "' "
          . "where `id`=" . $id;
        $db->setQuery( $sql );
        if (!$db->query()){
          $ret['IsSuccess'] = false;
          $ret['Msg'] = $db->stderr();
        }else{
          $ret['IsSuccess'] = true;
          $ret['Msg'] = 'Succefully';
        }
    }
    else
         $ret = getMessageOverlapping();
	}catch(Exception $e){
     $ret['IsSuccess'] = false;
     $ret['Msg'] = $e->getMessage();
  }
  return $ret;
}

function updateDetailedCalendar($id, $st, $et, $sub, $ade, $dscr, $loc, $color, $rrule,$rruleType,$tz){
  $ret = array();
  $db 	=& JFactory::getDBO();

  try{
    if (checkIfOverlapping(JRequest::getVar( 'calid' ), $st, $et,$sub,$loc,$id))
    {
        if ($rruleType=="only")
        {
            return addDetailedCalendar(JRequest::getVar( 'calid' ), $st, $et, $sub, $ade, $dscr, $loc, $color, "",$id,$tz);   
        }        
        else if ($rruleType=="all")
        {
            $sql = "update `".DC_MV_CAL."` set"
              . " `".DC_MV_CAL_FROM."`='" . php2MySqlTime(js2PhpTime($st)) . "', "
              . " `".DC_MV_CAL_TO."`='" . php2MySqlTime(js2PhpTime($et)) . "', "
              . " `".DC_MV_CAL_TITLE."`=" . $db->Quote($sub) . ", "
              . " `".DC_MV_CAL_ISALLDAY."`=" . $db->Quote($ade) . ", "
              . " `".DC_MV_CAL_DESCRIPTION."`=" . $db->Quote($dscr) . ", "
              . " `".DC_MV_CAL_LOCATION."`=" . $db->Quote($loc) . ", "
              . " `".DC_MV_CAL_COLOR."`=" . $db->Quote($color) . ", "
              . " `rrule`=" . $db->Quote($rrule) . " "
              . "where `id`=" . $id;
            $db->setQuery( $sql );
            if (!$db->query()){
              $ret['IsSuccess'] = false;
              $ret['Msg'] = $db->stderr();
            }else{
              $ret['IsSuccess'] = true;
              $ret['Msg'] = 'Succefully';
            }
        }        
        else if (substr($rruleType,0,5)=="UNTIL")
        {
            $sql = "select * from `".DC_MV_CAL."` where id=".$id;

            $db->setQuery( $sql );
            $rows = $db->loadObjectList();
            $pre_rrule = $rows[0]->rrule;
            //remove until
            $tmp = explode(";UNTIL=",$pre_rrule);
            if (count($tmp)>1)
            {
                $pre_rrule = $tmp[0];
                $tmp2 = explode(";",$tmp[1]); 
                if (count($tmp2)>1)
                    $pre_rrule .= ";".$tmp2[1]; 
            }
            //add
            $pre_rrule .= ";".$rruleType;
            $sql = "update `".DC_MV_CAL."` set"
              . " `rrule`=" . $db->Quote($pre_rrule) . " "
              . "where `id`=" . $id;
            $db->setQuery( $sql );
            $db->query();
            return addDetailedCalendar(JRequest::getVar( 'calid' ), $st, $et, $sub, $ade, $dscr, $loc, $color, $rrule,0,$tz);
        }
        else 
        {
            $sql = "update `".DC_MV_CAL."` set"
              . " `".DC_MV_CAL_FROM."`='" . php2MySqlTime(js2PhpTime($st)) . "', "
              . " `".DC_MV_CAL_TO."`='" . php2MySqlTime(js2PhpTime($et)) . "', "
              . " `".DC_MV_CAL_TITLE."`=" . $db->Quote($sub) . ", "
              . " `".DC_MV_CAL_ISALLDAY."`=" . $db->Quote($ade) . ", "
              . " `".DC_MV_CAL_DESCRIPTION."`=" . $db->Quote($dscr) . ", "
              . " `".DC_MV_CAL_LOCATION."`=" . $db->Quote($loc) . ", "
              . " `".DC_MV_CAL_COLOR."`=" . $db->Quote($color) . ", "
              . " `rrule`=" . $db->Quote($rrule) . " "
              . "where `id`=" . $id;
            $db->setQuery( $sql );
            if (!$db->query()){
              $ret['IsSuccess'] = false;
              $ret['Msg'] = $db->stderr();
            }else{
              $ret['IsSuccess'] = true;
              $ret['Msg'] = 'Succefully';
            }
        }
    }
    else
         $ret = getMessageOverlapping();
	}catch(Exception $e){
     $ret['IsSuccess'] = false;
     $ret['Msg'] = $e->getMessage();
  }
  return $ret;
}

function removeCalendar($id,$rruleType){
  $ret = array();
  $db 	=& JFactory::getDBO();
  try{
        if (substr($rruleType,0,8)=="del_only")
        {
            $sql = "select * from `".DC_MV_CAL."` where id=".$id;

            $db->setQuery( $sql );
            $rows = $db->loadObjectList();
            $exdate = $rows[0]->exdate.substr($rruleType,8);
            
            $sql = "update `".DC_MV_CAL."` set"
              . " `exdate`=" . $db->Quote($exdate) . " "
              . "where `id`=" . $id;
              
            $db->setQuery( $sql );            
            if (!$db->query()){
              $ret['IsSuccess'] = false;
              $ret['Msg'] = $db->stderr();
            }else{
              $ret['IsSuccess'] = true;
              $ret['Msg'] = 'Succefully';
            }
        }  
        else if (substr($rruleType,0,9)=="del_UNTIL")
        {
            $sql = "select * from `".DC_MV_CAL."` where id=".$id;

            $db->setQuery( $sql );
            $rows = $db->loadObjectList();
            $pre_rrule = $rows[0]->rrule;
            //remove until
            $tmp = explode(";UNTIL=",$pre_rrule);
            if (count($tmp)>1)
            {
                $pre_rrule = $tmp[0];
                $tmp2 = explode(";",$tmp[1]); 
                if (count($tmp2)>1)
                    $pre_rrule .= ";".$tmp2[1]; 
            }
            //add
            $pre_rrule .= ";".substr($rruleType,4);
            $sql = "update `".DC_MV_CAL."` set"
              . " `rrule`=" . $db->Quote($pre_rrule) . " "
              . "where `id`=" . $id;
            $db->setQuery( $sql );            
            if (!$db->query()){
              $ret['IsSuccess'] = false;
              $ret['Msg'] = $db->stderr();
            }else{
              $ret['IsSuccess'] = true;
              $ret['Msg'] = 'Succefully';
            }
            
        }
        else  // $rruleType = "del_all" or ""
        {
            $sql = "delete from `".DC_MV_CAL."` where `id`=" . $id;
	        $db->setQuery( $sql );
            if (!$db->query()){
              $ret['IsSuccess'] = false;
              $ret['Msg'] = $db->stderr();
            }else{
              $ret['IsSuccess'] = true;
              $ret['Msg'] = 'Succefully';
            }
        }
	}catch(Exception $e){
     $ret['IsSuccess'] = false;
     $ret['Msg'] = $e->getMessage();
  }
  return $ret;
}










jexit();
?>