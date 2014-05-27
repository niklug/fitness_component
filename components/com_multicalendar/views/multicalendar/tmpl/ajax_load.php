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
 * */
defined('_JEXEC') or die('Restricted access');
require_once( JPATH_COMPONENT . '/DC_MultiViewCal/php/functions.php' );
require_once( JPATH_BASE . '/components/com_multicalendar/DC_MultiViewCal/php/list.inc.php' );

require_once JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_fitness' . DS . 'helpers' . DS . 'fitness.php';
require_once JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_fitness' . DS . 'helpers' . DS . 'email.php';

$db = & JFactory::getDBO();
header('Content-type:text/javascript;charset=UTF-8');
$method = JRequest::getVar('method');
$calid = JRequest::getVar('calid');

switch ($method) {
    case "add":
        $ret = addCalendar($calid, JRequest::getVar("CalendarStartTime"), JRequest::getVar("CalendarEndTime"), JRequest::getVar("CalendarTitle"), JRequest::getVar("IsAllDayEvent"), JRequest::getVar("Location")
        );
        break;
    case "list":
        //$ret = listCalendar(JRequest::getVar("showdate"), JRequest::getVar("viewtype"));

        $d1 = js2PhpTime(JRequest::getVar("startdate"));
        $d2 = js2PhpTime(JRequest::getVar("enddate"));
        $client_id = JRequest::getVar("client_id");
        $trainer_id = JRequest::getVar("trainer_id");
        $client_id = JRequest::getVar("client_id");
        $location = JRequest::getVar("location");
        $appointment = JRequest::getVar("appointment");
        $session_type = JRequest::getVar("session_type");
        $session_focus = JRequest::getVar("session_focus");

        $business_profile_id = JRequest::getVar("filter_business_profile_id");

        $d1 = mktime(0, 0, 0, date("m", $d1), date("d", $d1), date("Y", $d1));
        $d2 = mktime(0, 0, 0, date("m", $d2), date("d", $d2), date("Y", $d2)) + 24 * 60 * 60 - 1;
        $ret = listCalendarByRange($calid, ($d1), ($d2), $trainer_id, $client_id, $location, $appointment, $session_type, $session_focus, $business_profile_id);

        break;
    case "update":
        $ret = updateCalendar(JRequest::getVar("calendarId"), JRequest::getVar("CalendarStartTime"), JRequest::getVar("CalendarEndTime"));
        break;
    case "remove":
        $ret = removeCalendar(JRequest::getVar("calendarId"), JRequest::getVar("rruleType"));
        break;
    case "get_session_type":
        $ret = get_session_type();
        break;
    case "get_session_focus":
        $ret = get_session_focus();
        break;
    case "get_trainers":
        $ret = get_trainers(JRequest::getVar("user_id"));
        break;
    case "get_clients":
        $ret = get_clients();
        break;
    case "set_event_status":
        $ret = set_event_status();
        break;
    case "add_exercise":
        $ret = add_exercise();
        break;
    case "delete_exercise":
        $ret = delete_exercise();
        break;
    case "set_event_exircise_order":
        $ret = set_event_exircise_order();
    //

    case "update_exercise_field":
        $ret = update_exercise_field();
        break;
    case "get_semi_clients":
        $ret = get_semi_clients();
        break;
    case "delete_event_clients":
        $ret = delete_event_clients();
        break;
    case "add_update_group_client":
        $ret = add_update_group_client();
        break;
    case "delete_group_client":
        $ret = delete_group_client();
        break;
    case "set_group_client_status":
        $ret = set_group_client_status();
        break;
    case "generateFormHtml":
        $ret = generateFormHtml();
        break;
    case "saveDragedData":
        $ret = saveDragedData();
        break;
    case "sendRemindersManually":
        $ret = sendRemindersManually();
        break;
    case "deleteEvent":
        $ret = deleteEvent();
        break;

    case "adddetails":

        $st = JRequest::getVar("stpartdatelast") . " " . JRequest::getVar("stparttimelast");
        $et = JRequest::getVar("etpartdatelast") . " " . JRequest::getVar("etparttimelast");
        if (JRequest::getVar("id") != "") {

            $ret = updateDetailedCalendar(
                    JRequest::getVar("id"), $st, $et, JRequest::getVar("Subject"), (JRequest::getVar("IsAllDayEvent") == 1) ? 1 : 0, JRequest::getVar('Description', '',
                    'POST', 'STRING', JREQUEST_ALLOWHTML), JRequest::getVar('comments', '', 'POST', 'STRING', JREQUEST_ALLOWHTML), 
                    JRequest::getVar('session_type', '', 'POST', 'STRING', JREQUEST_ALLOWHTML), JRequest::getVar('session_focus', '', 'POST', 'STRING', JREQUEST_ALLOWHTML), 
                    JRequest::getVar("trainer_id"), JRequest::getVar("Location"),  JRequest::getVar("frontend_published"),
                    JRequest::getVar("published"),  JRequest::getVar("auto_publish_workout"),  JRequest::getVar("auto_publish_event"), JRequest::getVar("rrule"), JRequest::getVar("rruleType"), JRequest::getVar("timezone"), JRequest::getVar("business_profile_id")
            );
        } else {

            $ret = addDetailedCalendar(
                    $calid, $st, $et, JRequest::getVar("Subject"), (JRequest::getVar("IsAllDayEvent") == 1) ? 1 : 0,
                    JRequest::getVar('Description', '', 'POST', 'STRING', JREQUEST_ALLOWHTML),
                    JRequest::getVar('session_type', '', 'POST', 'STRING', JREQUEST_ALLOWHTML),
                    JRequest::getVar('session_focus', '', 'POST', 'STRING', JREQUEST_ALLOWHTML),
                    JRequest::getVar("trainer_id"), JRequest::getVar("Location"), 
                    JRequest::getVar("rrule"), 0, JRequest::getVar("timezone"),
                    JRequest::getVar("business_profile_id")
            );
        }
        break;
}
echo json_encode($ret);

function checkIfOverlappingThisEvent($id, $st, $et) {
    return true; // changed
    $db = & JFactory::getDBO();
    $sql = "select * from `" . DC_MV_CAL . "` where id=" . $id;

    $db->setQuery($sql);
    $rows = $db->loadObjectList();
    if (count($rows) > 0)
        return checkIfOverlapping($rows[0]->calid, $st, $et, $rows[0]->title, $rows[0]->location, $id);
    else
        return true;
}

function checkIfOverlapping($calid, $st, $et, $sub, $loc, $id) {
    return true;//changed
    $db = & JFactory::getDBO();
    $sd = date("Y-m-d H:i:s", js2PhpTime($st));
    $ed = date("Y-m-d H:i:s", js2PhpTime($et));
    $condition = "";
    if (JC_NO_OVERLAPPING_TIME)
        $condition .= " and ( (`" . DC_MV_CAL_FROM . "` > '"
                . ($sd) . "' and `" . DC_MV_CAL_FROM . "` < '" . ($ed) . "') or (`" . DC_MV_CAL_TO . "` > '"
                . ($sd) . "' and `" . DC_MV_CAL_TO . "` < '" . ($ed) . "') or (`" . DC_MV_CAL_FROM . "` <= '"
                . ($sd) . "' and `" . DC_MV_CAL_TO . "` >= '" . ($ed) . "') )   ";
    if (JC_NO_OVERLAPPING_SUBJECT)
        $condition .= " and ( `" . DC_MV_CAL_TITLE . "` = '" . $sub . "' )   ";
    if (JC_NO_OVERLAPPING_LOCATION)
        $condition .= " and ( `" . DC_MV_CAL_LOCATION . "` = '" . $loc . "' )   ";
    if ($condition == "")
        $condition = " and 1=0";
    $sql = "select * from `" . DC_MV_CAL . "` where " . DC_MV_CAL_IDCAL . "=" . $calid . $condition;

    $db->setQuery($sql);

    $rows = $db->loadObjectList();
    if (count($rows) == 0 || (count($rows) == 1 && $rows[0]->id == $id))
        return true;
    else
        return false;
}

function getMessageOverlapping() {
    $ret = array();
    $ret['success'] = false;
    $ret['message'] = "OVERLAPPING";
    return $ret;
}

function addCalendar(
$calid, $st, $et, $sub, $ade, $Location
) {
    $ret = array();
    $db = & JFactory::getDBO();
    $user = & JFactory::getUser();
    try {
        if (checkIfOverlapping($calid, $st, $et, $sub, $loc, 0)) {
            $sql = "insert into `" . DC_MV_CAL . "` (
        `" . DC_MV_CAL_IDCAL . "`,
        `" . DC_MV_CAL_TITLE . "`,
        `" . DC_MV_CAL_FROM . "`,
        `" . DC_MV_CAL_TO . "`,
        `" . DC_MV_CAL_ISALLDAY . "`,

        `" . DC_MV_CAL_LOCATION . "`,
         
        `owner`,
        `published`
        ) values (
        
      " . $calid . ","
                    . $db->Quote($sub) . ", '"
                    . php2MySqlTime(js2PhpTime($st)) . "', '"
                    . php2MySqlTime(js2PhpTime($et)) . "', "
                    . $db->Quote($ade) . ", "
                    . $db->Quote($loc) . ", "
                    . $user->id
                    . ",1)";


            $db->setQuery($sql);
            if (!$db->query()) {
                $ret['success'] = false;
                $ret['message'] = $db->stderr();
            } else {
                $ret['success'] = true;
                $ret['message'] = 'add success';
                $ret['Data'] = $db->insertid();
            }
        } else
            $ret = getMessageOverlapping();
    } catch (Exception $e) {
        $ret['success'] = false;
        $ret['message'] = $e->getMessage();
    }

    return $ret;
}

function addDetailedCalendar(
    $calid, $st, $et, $sub, $ade, $dscr, $session_type, $session_focus,
        $trainer_id, $loc, $rrule, $uid, $tz, $business_profile_id
    ) {

    $ret = array();

    $db = & JFactory::getDBO();
    $user = & JFactory::getUser(JRequest::getVar('cid'));
    $frontend_published = JRequest::getVar('frontend_published', '1');
    $comments = JRequest::getVar('comments');
    
    try {
            $sql = "insert into `" . DC_MV_CAL . "` (
        `" . DC_MV_CAL_IDCAL . "`,
        `" . DC_MV_CAL_TITLE . "`,
        `" . DC_MV_CAL_FROM . "`, 
        `" . DC_MV_CAL_TO . "`, 
        `" . DC_MV_CAL_ISALLDAY . "`,
        `" . DC_MV_CAL_DESCRIPTION . "`,
        `comments`,
        `session_type`,
        `session_focus`,
        `trainer_id`,
        `" . DC_MV_CAL_LOCATION . "`, 
        `rrule`,`uid`,`owner`, `published`,`frontend_published`,
        `business_profile_id`) values (
        
       " . $calid . ","
        . $db->Quote($sub) . ", '"
        . php2MySqlTime(js2PhpTime($st)) . "', '"
        . php2MySqlTime(js2PhpTime($et)) . "', "
        . $db->Quote($ade) . ", "
        . $db->Quote($dscr) . ", "
        . $db->Quote($comments) . ", "
        . $db->Quote($session_type) . ", "
        . $db->Quote($session_focus) . ", "
        . $db->Quote($trainer_id) . ", "
        . $db->Quote($loc) . ", "
        . $db->Quote($rrule) . ", " . $db->Quote($uid) . ", " 
        . $user->id 
        . ",1,"
        . $frontend_published . ", "
        . $db->Quote($business_profile_id) ." )";

        $db->setQuery($sql);
        if (!$db->query()) {
            $ret['success'] = false;
            $ret['message'] = $db->stderr();
        } else {
            $ret['success'] = true;
            $ret['message'] = 'add success';
            $id = $db->insertid();
            $ret['Data'] = $id;
        }
        
        $client_added = addAppointmentClient($id);
        if(!$client_added['success']) {
            $ret['success'] = false;
            $ret['message'] = $client_added['message'];
        }


    } catch (Exception $e) {
        $ret['success'] = false;
        $ret['message'] = $e->getMessage();
    }

    return $ret;
}

function addAppointmentClient($event_id) {
    $ret['success'] = true;
    $client_id = JRequest::getVar("client_id");
    
    try {
        if($client_id) {
            $db = JFactory::getDbo();
            $table = '#__fitness_appointment_clients';
            $data = new stdClass();
            $data->event_id = $event_id;
            $data->client_id = $client_id;
            
            $query = "SELECT id FROM $table WHERE event_id='$event_id' AND client_id='$client_id'";
            
            $db->setQuery($query);
            if (!$db->query()) {
                $ret['success'] = false;
                $ret['message'] = $db->stderr();
                return $ret;
            }
            
            $event_client_id = $db->loadResult();
            
            if($event_client_id) {
                $data->id = $event_client_id;
                $insert = $db->updateObject($table, $data, 'id');
            } else {
                $insert = $db->insertObject($table, $data, 'id');
            }
            if(!$insert) {
                $ret['success'] = false;
                $ret['message'] = $db->stderr();
                return $ret;
            }
        }

    } catch (Exception $e) {
        $ret['success'] = false;
        $ret['message'] = $e->getMessage();
    }
    return $ret;
}

function listCalendarByRange($calid, $sd, $ed, $trainer_id, $client_id, $location, $appointment, $session_type, $session_focus, $business_profile_id) {
    $ret = array();
    $ret['events'] = array();
    $ret["issort"] = true;
    $ret["start"] = php2JsTime($sd);
    $ret["end"] = php2JsTime($ed);
    $ret['error'] = null;
    $db = & JFactory::getDBO();

    //logged user
    $cid = JRequest::getVar('cid');
    $user = &JFactory::getUser($cid);
    $user_id = $user->id ;
    
    $helper = new FitnessHelper();

    try {
        $is_trainer_administrator = FitnessHelper::is_trainer_administrator($user_id);
    } catch (Exception $e) {
        $ret['error'] = '"' . $e->getMessage() . '"' . ' - File: ' . $e->getFile() . ' Line: ' . $e->getLine();
        return $ret;
    }

   
    try {
        $is_simple_trainer = FitnessHelper::is_simple_trainer($user_id);
    } catch (Exception $e) {
        $ret['error'] = '"' . $e->getMessage() . '"' . ' - File: ' . $e->getFile() . ' Line: ' . $e->getLine();
        return $ret;
    }
    
    try {
        $is_client = FitnessHelper::is_client($user_id);
    } catch (Exception $e) {
        $ret['error'] = '"' . $e->getMessage() . '"' . ' - File: ' . $e->getFile() . ' Line: ' . $e->getLine();
        return $ret;
    }
    
    try {
        $business_profile = $helper->getBusinessProfileId($user_id);
        
        $business_profile_id = $business_profile['data'];
    } catch (Exception $e) {
        $ret['error'] = '"' . $e->getMessage() . '"' . ' - File: ' . $e->getFile() . ' Line: ' . $e->getLine();
        return $ret;
    }


    try {
        $sql = "select a.*, ";
        
        $sql .= " t.name AS appointment_name,";
        
        $sql .= " l.name AS location_name,";
        
        $sql .= " st.name AS session_type_name,";
        
        $sql .= " sf.name AS session_focus_name,";
        
        $sql .= " t.color AS color";
        
        $sql .= " FROM `" . DC_MV_CAL . "` AS a ";

        $sql .= " LEFT JOIN #__fitness_categories AS t ON t.id = a.title ";
        
        $sql .= " LEFT JOIN #__fitness_locations AS l ON l.id = a.location ";
        
        $sql .= " LEFT JOIN #__fitness_session_type AS st ON st.id = a.session_type ";
        
        $sql .= " LEFT JOIN #__fitness_session_focus AS sf ON sf.id = a.session_focus ";
        

        //$sql .= " where a." . DC_MV_CAL_IDCAL . "=" . $calid;
        $sql .= " where 1";

        if ($is_trainer_administrator) {
            $sql .= " AND  a.business_profile_id ='$business_profile_id' ";
        }
        
        // trainer can see appointment for client created by another trainer if it is his client too
        if ($is_simple_trainer) {
            $sql .= " AND  a.business_profile_id ='$business_profile_id' ";
            $sql .= " AND ((a.trainer_id is NULL OR a.trainer_id = '') OR a.trainer_id='$user_id' OR a.id IN (SELECT  DISTINCT event_id FROM #__fitness_appointment_clients WHERE client_id IN (SELECT user_id FROM #__fitness_clients WHERE primary_trainer='$user_id' OR FIND_IN_SET('$user_id', other_trainers)))) ";
        }
        
        if($is_client) {
            $sql .= " AND  a.business_profile_id ='$business_profile_id' ";
            $sql .= " AND ((a.trainer_id is NULL OR a.trainer_id = '') OR a.id IN (SELECT  DISTINCT event_id FROM #__fitness_appointment_clients WHERE client_id='$user_id')) ";
        }

        $client_ids = implode($client_id, ',');
        

        if ($client_id[0]) {
            $sql .= " and (a.id IN (SELECT  DISTINCT event_id FROM #__fitness_appointment_clients WHERE client_id IN ($client_ids))) ";
        }

        $trainer_ids = implode($trainer_id, ',');

        if ($trainer_id[0]) {
            $sql .= " and trainer_id IN ($trainer_ids) ";
        }

        //logged user
        $cid = JRequest::getVar('cid');

        $user = &JFactory::getUser($cid);

        $locations = "'" . implode("','", $location) . "'";
        
        if ($location[0]) {
            $sql .= " and a.location IN ($locations) ";
        }


        $appointments = "'" . implode("','", $appointment) . "'";
        if ($appointment[0]) {
            $sql .= " and a.title IN ($appointments) ";
        }


        $session_types = "'" . implode("','", $session_type) . "'";
        if ($session_type[0]) {
            $sql .= " and a.session_type IN ($session_types) ";
        }


        $session_focuses = "'" . implode("','", $session_focus) . "'";
        if ($session_focus[0]) {
            $sql .= " and a.session_focus IN ($session_focuses) ";
        }
        //$sql .= " AND published='1'";

        $sql .= " and ( (a." . DC_MV_CAL_FROM . " between '"
                . php2MySqlTime($sd) . "' and '" . php2MySqlTime($ed) . "') or (a." . DC_MV_CAL_TO . " between '"
                . php2MySqlTime($sd) . "' and '" . php2MySqlTime($ed) . "') or (a." . DC_MV_CAL_FROM . " <= '"
                . php2MySqlTime($sd) . "' and a." . DC_MV_CAL_TO . " >= '" . php2MySqlTime($ed) . "') or a.rrule<>'') order by a.uid desc,  a." . DC_MV_CAL_FROM . "  ";


        $db->setQuery($sql);
        if (!$db->query()) {
            $ret['success'] = false;
            $ret['error'] = $db->stderr();
            return $ret;
        }
        $rows = $db->loadObjectList();


        $str = "";



        for ($i = 0; $i < count($rows); $i++) {
            $clients = array();
            $clients_names = array();
            $row = $rows[$i];
            if (strlen($row->exdate) > 0)
                $row->rrule .= ";exdate=" . $row->exdate;

            $id = $row->id;
            $query = "SELECT  client_id FROM #__fitness_appointment_clients WHERE event_id='$id'";
            $db->setQuery($query);
            if (!$db->query()) {
                $ret['success'] = false;
                $ret['message'] = $db->stderr();
            }
            
            $clients = $db->loadResultArray(0);
   
            $clients = array_unique($clients);
            foreach ($clients as $client) {
                $clients_names[] = JFactory::getUser($client)->name;
            }
            $clients_names = array_filter($clients_names);
            
      
            $readonly = $helper->eventCalendarFrontendReadonly($row->title, $user_id);

            $ev = array(
                $row->id,
                $row->appointment_name,
                php2JsTime(mySql2PhpTime($row->starttime)),
                php2JsTime(mySql2PhpTime($row->endtime)),
                $row->isalldayevent,
                0, //more than one day event
                //$row->InstanceType,
                ((is_numeric($row->uid) && $row->uid > 0) ? $row->uid : $row->rrule), //Recurring event rule,
                $row->color,
                1, //editable
                $row->location_name,
                '', //$attends
                $row->description,
                $row->owner,
                $row->published,
                JFactory::getUser($row->trainer_id)->name,
                $clients_names,
                $row->session_type_name,
                $row->session_focus_name,
                $readonly
            );
            $ret['events'][] = $ev;
        }
    } catch (Exception $e) {
        $ret['error'] = $e->getMessage();
    }
    return $ret;
}

function listCalendar($day, $type) {
    $phpTime = js2PhpTime($day);
    //echo $phpTime . "+" . $type;
    switch ($type) {
        case "month":
            $st = mktime(0, 0, 0, date("m", $phpTime), 1, date("Y", $phpTime));
            $et = mktime(0, 0, -1, date("m", $phpTime) + 1, 1, date("Y", $phpTime));
            break;
        case "week":
            //suppose first day of a week is monday
            $monday = date("d", $phpTime) - date('N', $phpTime) + 1;
            //echo date('N', $phpTime);
            $st = mktime(0, 0, 0, date("m", $phpTime), $monday, date("Y", $phpTime));
            $et = mktime(0, 0, -1, date("m", $phpTime), $monday + 7, date("Y", $phpTime));
            break;
        case "day":
            $st = mktime(0, 0, 0, date("m", $phpTime), date("d", $phpTime), date("Y", $phpTime));
            $et = mktime(0, 0, -1, date("m", $phpTime), date("d", $phpTime) + 1, date("Y", $phpTime));
            break;
    }
    //echo $st . "--" . $et;
    return listCalendarByRange($st, $et, '', '', '', '', '', '', '', '');
}

function updateCalendar($id, $st, $et) {
    $ret = array();
    $db = & JFactory::getDBO();
    try {
        $sql = "update `" . DC_MV_CAL . "` set"
                . " `" . DC_MV_CAL_FROM . "`='" . php2MySqlTime(js2PhpTime($st)) . "', "
                . " `" . DC_MV_CAL_TO . "`='" . php2MySqlTime(js2PhpTime($et)) . "' "
                . "where `id`=" . $id;
        $db->setQuery($sql);
        if (!$db->query()) {
            $ret['success'] = false;
            $ret['message'] = $db->stderr();
        } else {
            $ret['success'] = true;
            $ret['message'] = 'Succefully';
        }

    } catch (Exception $e) {
        $ret['success'] = false;
        $ret['message'] = $e->getMessage();
    }

    return $ret;
}


function updateDetailedCalendar(
$id, $st, $et, $sub, $ade, $dscr, $comments, $session_type, $session_focus, $trainer_id, $loc, $frontend_published, 
        $published, $auto_publish_workout, $auto_publish_event, $rrule, $rruleType, $tz, $business_profile_id
) {

    $ret = array();
    $db = & JFactory::getDBO();

    try {
       if ($rruleType == "only") {
            return addDetailedCalendar(
                    JRequest::getVar('calid'), $st, $et, $sub, $ade, $dscr, $session_type, $session_focus, $trainer_id, $loc, "", $id, $tz
            );
        } else if ($rruleType == "all") {
            $sql = "update `" . DC_MV_CAL . "` set"
                    . " `" . DC_MV_CAL_FROM . "`='" . php2MySqlTime(js2PhpTime($st)) . "', "
                    . " `" . DC_MV_CAL_TO . "`='" . php2MySqlTime(js2PhpTime($et)) . "', "
                    . " `" . DC_MV_CAL_TITLE . "`=" . $db->Quote($sub) . ", "
                    . " `" . DC_MV_CAL_ISALLDAY . "`=" . $db->Quote($ade) . ", "
                    . " `" . DC_MV_CAL_DESCRIPTION . "`=" . $db->Quote($dscr) . ", "
                    . " `comments`=" . $db->Quote($comments) . ", "
                    . " `session_type`=" . $db->Quote($session_type) . ", "
                    . " `session_focus`=" . $db->Quote($session_focus) . ", "
                    . " `trainer_id`=" . $db->Quote($trainer_id) . ", "
                    . " `" . DC_MV_CAL_LOCATION . "`=" . $db->Quote($loc) . ", "
                    . " `frontend_published`=" . $db->Quote($frontend_published) . ", "
                    . " `published`=" . $db->Quote($published) . ", "
                    . " `auto_publish_workout`=" . $db->Quote($auto_publish_workout) . ", "
                    . " `auto_publish_event`=" . $db->Quote($auto_publish_event) . ", "
                    . " `rrule`=" . $db->Quote($rrule) . ", "
                    . " `business_profile_id`=" . $db->Quote($business_profile_id) . " "
                    . "where `id`=" . $id;
            $db->setQuery($sql);
            if (!$db->query()) {
                $ret['success'] = false;
                $ret['message'] = $db->stderr();
            } else {
                $ret['success'] = true;
                $ret['message'] = 'Succefully';
                $ret['Data'] = $id;
                
                $client_added = addAppointmentClient($id);
                if(!$client_added['success']) {
                    $ret['success'] = false;
                    $ret['message'] = $client_added['message'];
                }
            }
        } else if (substr($rruleType, 0, 5) == "UNTIL") {
            $sql = "select * from `" . DC_MV_CAL . "` where id=" . $id;

            $db->setQuery($sql);
            $rows = $db->loadObjectList();
            $pre_rrule = $rows[0]->rrule;
            //remove until
            $tmp = explode(";UNTIL=", $pre_rrule);
            if (count($tmp) > 1) {
                $pre_rrule = $tmp[0];
                $tmp2 = explode(";", $tmp[1]);
                if (count($tmp2) > 1)
                    $pre_rrule .= ";" . $tmp2[1];
            }
            //add
            $pre_rrule .= ";" . $rruleType;
            $sql = "update `" . DC_MV_CAL . "` set"
                    . " `rrule`=" . $db->Quote($pre_rrule) . " "
                    . "where `id`=" . $id;
            $db->setQuery($sql);
            $db->query();
            return addDetailedCalendar(
                    JRequest::getVar('calid'), $st, $et, $sub, $ade, $dscr, $session_type, $session_focus, $trainer_id, $loc, "", $id, $tz, $business_profile_id
            );
        }
        else {
            $sql = "update `" . DC_MV_CAL . "` set"
                    . " `" . DC_MV_CAL_FROM . "`='" . php2MySqlTime(js2PhpTime($st)) . "', "
                    . " `" . DC_MV_CAL_TO . "`='" . php2MySqlTime(js2PhpTime($et)) . "', "
                    . " `" . DC_MV_CAL_TITLE . "`=" . $db->Quote($sub) . ", "
                    . " `" . DC_MV_CAL_ISALLDAY . "`=" . $db->Quote($ade) . ", "
                    . " `" . DC_MV_CAL_DESCRIPTION . "`=" . $db->Quote($dscr) . ", "
                    . " `comments`=" . $db->Quote($comments) . ", "
                    . " `session_type`=" . $db->Quote($session_type) . ", "
                    . " `session_focus`=" . $db->Quote($session_focus) . ", "
                    . " `trainer_id`=" . $db->Quote($trainer_id) . ", "
                    . " `" . DC_MV_CAL_LOCATION . "`=" . $db->Quote($loc) . ", "
                    . " `frontend_published`=" . $db->Quote($frontend_published) . ", "
                    . " `published`=" . $db->Quote($published) . ", "
                    . " `auto_publish_workout`=" . $db->Quote($auto_publish_workout) . ", "
                    . " `auto_publish_event`=" . $db->Quote($auto_publish_event) . ", "
                    . " `rrule`=" . $db->Quote($rrule) . ", "
                    . " `business_profile_id`=" . $db->Quote($business_profile_id) . " "
                    . "where `id`=" . $id;
            $db->setQuery($sql);
            if (!$db->query()) {
                $ret['success'] = false;
                $ret['message'] = $db->stderr();
            } else {
                $ret['success'] = true;
                $ret['message'] = 'Succefully';
                $ret['Data'] = $id;
                
                $client_added = addAppointmentClient($id);
                if(!$client_added['success']) {
                    $ret['success'] = false;
                    $ret['message'] = $client_added['message'];
                }
            }
        }
    } catch (Exception $e) {
        $ret['success'] = false;
        $ret['message'] = $e->getMessage();
    }

    return $ret;
}

function removeCalendar($id, $rruleType) {
    $ret = array();
    $db = & JFactory::getDBO();
    try {
        if (substr($rruleType, 0, 8) == "del_only") {
            $sql = "select * from `" . DC_MV_CAL . "` where id=" . $id;

            $db->setQuery($sql);
            $rows = $db->loadObjectList();
            $exdate = $rows[0]->exdate . substr($rruleType, 8);

            $sql = "update `" . DC_MV_CAL . "` set"
                    . " `exdate`=" . $db->Quote($exdate) . " "
                    . "where `id`=" . $id;

            $db->setQuery($sql);
            if (!$db->query()) {
                $ret['success'] = false;
                $ret['message'] = $db->stderr();
            } else {
                $ret['success'] = true;
                $ret['message'] = 'Succefully';
            }
        } else if (substr($rruleType, 0, 9) == "del_UNTIL") {
            $sql = "select * from `" . DC_MV_CAL . "` where id=" . $id;

            $db->setQuery($sql);
            $rows = $db->loadObjectList();
            $pre_rrule = $rows[0]->rrule;
            //remove until
            $tmp = explode(";UNTIL=", $pre_rrule);
            if (count($tmp) > 1) {
                $pre_rrule = $tmp[0];
                $tmp2 = explode(";", $tmp[1]);
                if (count($tmp2) > 1)
                    $pre_rrule .= ";" . $tmp2[1];
            }
            //add
            $pre_rrule .= ";" . substr($rruleType, 4);
            $sql = "update `" . DC_MV_CAL . "` set"
                    . " `rrule`=" . $db->Quote($pre_rrule) . " "
                    . "where `id`=" . $id;
            $db->setQuery($sql);
            if (!$db->query()) {
                $ret['success'] = false;
                $ret['message'] = $db->stderr();
            } else {
                $ret['success'] = true;
                $ret['message'] = 'Succefully';
            }
        } else {  // $rruleType = "del_all" or ""
            $sql = "delete from `" . DC_MV_CAL . "` where `id`=" . $id;
            $db->setQuery($sql);
            if (!$db->query()) {
                $ret['success'] = false;
                $ret['message'] = $db->stderr();
            } else {
                $ret['success'] = true;
                $ret['message'] = 'Succefully';
            }
        }
    } catch (Exception $e) {
        $ret['success'] = false;
        $ret['message'] = $e->getMessage();
    }
    return $ret;
}

/** get appointment type by category
 * npkorban
 * @param type $catid
 */
function get_session_type() {
    $catid = JRequest::getVar("catid");
    $db = & JFactory::getDBO();
    $query = "SELECT id, name FROM #__fitness_session_type WHERE category_id='$catid' AND state='1'";
    $query .= " ORDER BY name";
    $db->setQuery($query);
    $id = $db->loadResultArray(0);
    $name = $db->loadResultArray(1);
    $result = array_combine($name, $id);
    return $result;
}

/** get appointment type by category
 * npkorban
 * @param type $catid
 */
function get_session_focus() {
    $catid = JRequest::getVar("catid");
    $session_type = JRequest::getVar("session_type");
    $db = & JFactory::getDBO();
    $query = "SELECT id, name FROM #__fitness_session_focus WHERE category_id='$catid' AND session_type_id='$session_type' AND state='1'";
    $query .= " ORDER BY name";
    $db->setQuery($query);
    $id = $db->loadResultArray(0);
    $name = $db->loadResultArray(1);
    $result = array_combine($name, $id);
    return $result;
}

/** for populate select with trainers
 * 
 * @return string
 */
function get_trainers($user_id) {
    $status['success'] = 1;

    if (!$user_id) {
        return array('status' => $status);
    }

    $secondary_only = JRequest::getVar("secondary_only");
    
    $all_trainers = JRequest::getVar("all_trainers");

    $db = & JFactory::getDBO();

    if ((FitnessHelper::is_trainer_administrator($user_id) || FitnessHelper::is_superuser($user_id)) || $secondary_only || $all_trainers) {
        $query = "SELECT primary_trainer, other_trainers FROM #__fitness_clients WHERE user_id='$user_id' AND state='1'";
        $db->setQuery($query);
        if (!$db->query()) {
            $status['success'] = 0;
            $status['message'] = $db->stderr();
            return array('status' => $status);
        }
        $primary_trainer = $db->loadResultArray(0);
        $other_trainers = $db->loadResultArray(1);
        $other_trainers = explode(',', $other_trainers[0]);
        $all_trainers_id = array_unique(array_merge($primary_trainer, $other_trainers));
    }

    if (FitnessHelper::is_simple_trainer($user_id)) {
        $all_trainers_id = array($user_id);
    }

    if ($secondary_only) {
        $all_trainers_id = $other_trainers;
    }

    if (!$all_trainers_id) {
        $status['success'] = 0;
        $status['message'] = 'No trainers assigned to this client.';
        return array('status' => $status);
    }

    foreach ($all_trainers_id as $user_id) {
        $user = &JFactory::getUser($user_id);
        $all_trainers_name[] = $user->name;
    }

    $result = array('status' => $status, 'data' => array_combine($all_trainers_id, $all_trainers_name));
    return $result;
}

/** for populate select with clients
 * 
 * @return string
 */
function get_clients() {
    $trainer_id = JRequest::getVar("trainer_id");
    $db = & JFactory::getDBO();
    $query = "SELECT c.user_id "
            . " FROM #__fitness_clients AS c"
            . " LEFT JOIN #__users AS u ON c.user_id=u.id"
            . " WHERE c.primary_trainer='$trainer_id' "
            . " OR FIND_IN_SET('$trainer_id' , c.other_trainers)"
            . " AND c.state='1'"
            . " ORDER BY u.name ASC";
    $db->setQuery($query);
    $status['success'] = 1;
    if (!$db->query()) {
        $status['success'] = 0;
        $status['message'] = $db->stderr();
        return array('status' => $status);
    }

    $clients = $db->loadResultArray(0);

    if (!$clients) {
        $status['success'] = 0;
        $status['message'] = 'No clients assigned to this trainer.';
        return array('status' => $status);
    }


    foreach ($clients as $user_id) {
        $user = &JFactory::getUser($user_id);
        $clients_name[] = $user->name;
    }

    $result = array('status' => $status, 'data' => array_combine($clients_name, $clients));
    return $result;
}

/**
 * set event status, on  click status button
 */
function set_event_status() {
    $event_id = JRequest::getVar("event_id");
    $event_status = JRequest::getVar("event_status");
    $db = & JFactory::getDBO();
    $query = "UPDATE #__dc_mv_events SET status='$event_status' WHERE id='$event_id'";
    $db->setQuery($query);
    if (!$db->query()) {
        echo $db->stderr();
    } else {
        echo $event_status;
    }
    die();
}

/**
 * add event exercise
 * @return type
 */
function add_exercise() {
    $post = JRequest::get('post');
    $db = & JFactory::getDBO();
    $no_fields = array('method', 'layout', 'view', 'option');
    $obj = new stdClass();
    foreach ($post as $key => $value) {
        if (!in_array($key, $no_fields)) {
            $obj->$key = $value;
        }
    }

    $post['success'] = 1;
    $insert = $db->insertObject('#__fitness_events_exercises', $obj, 'id');
    if (!$insert) {
        $post['success'] = 0;
        $post['message'] = $db->stderr();
    }

    $post['id'] = $db->insertid();
    return $post;
}

/**
 * delete event exercise
 */
function delete_exercise() {
    $exercise_id = JRequest::getVar('exercise_id');
    $db = & JFactory::getDBO();
    $query = "DELETE FROM #__fitness_events_exercises WHERE id='$exercise_id'";
    $db->setQuery($query);
    $post['exercise_id'] = $exercise_id;
    $post['success'] = 1;
    if (!$db->query()) {
        $post['success'] = 0;
        $post['message'] = $db->stderr();
    }
    return $post;
}

/**
 * change event exercises order on drag and drop
 */
function set_event_exircise_order() {
    $status['success'] = 1;
    $row_id = JRequest::getVar('row_id');
    $order = JRequest::getVar('order');
    $db = & JFactory::getDBO();
    $query = "UPDATE `#__fitness_events_exercises` SET `order` = '$order' WHERE `id` ='$row_id'";
    $db->setQuery($query);
    if (!$db->query()) {
        $status['success'] = 0;
        $status['message'] = $db->stderr();
    }
    return $status;
}

/**
 * 
 * @return type
 */
function update_exercise_field() {
    $status['success'] = 1;
    $exercise_id = &JRequest::getVar('exercise_id');
    $exercise_column = &JRequest::getVar('exercise_column');
    $new_value = &JRequest::getVar('new_value');

    switch ($exercise_column) {
        case 1:
            $column = 'title';
            break;
        case 2:
            $column = 'speed';
            break;
        case 3:
            $column = 'weight';
            break;
        case 4:
            $column = 'reps';
            break;
        case 5:
            $column = 'time';
            break;
        case 6:
            $column = 'sets';
            break;
        case 7:
            $column = 'rest';
            break;

        default:
            return;
            break;
    }
    $db = & JFactory::getDBO();
    $query = "UPDATE `#__fitness_events_exercises` SET `$column` = '$new_value' WHERE `id` ='$exercise_id'";
    $db->setQuery($query);
    if (!$db->query()) {
        $status['success'] = 0;
        $status['message'] = $db->stderr();
    }

    return $status;
}

function get_semi_clients() {
    $status['success'] = 1;
    $event_id = JRequest::getVar("event_id");
    $db = & JFactory::getDBO();
    $query = "SELECT id, client_id, status FROM #__fitness_appointment_clients WHERE event_id='$event_id'";
    $db->setQuery($query);
    if (!$db->query()) {
        $status['success'] = 0;
        $status['message'] = $db->stderr();
    }
    $ids = $db->loadResultArray(0);
    $clients = $db->loadResultArray(1);
    $status = $db->loadResultArray(2);

    foreach ($clients as $user_id) {
        $user = &JFactory::getUser($user_id);
        $clients_name[] = $user->name;
    }

    $result = array('ids' => $ids, 'clients' => $clients, 'clients_name' => $clients_name, 'status' => $status);
    return $result;
}

function delete_event_clients() {
    $status['success'] = 1;
    $event_id = JRequest::getVar("event_id");
    $db = & JFactory::getDBO();
    $query = "DELETE FROM #__fitness_appointment_clients WHERE event_id='$event_id'";
    $db->setQuery($query);
    if (!$db->query()) {
        $status['success'] = 0;
        $status['message'] = $db->stderr();
    }
    $result = array('status' => $status);
    return $result;
}

/**
 * 
 * @return type
 */
function add_update_group_client() {
    $status['success'] = 1;
    $event_id = JRequest::getVar("event_id");
    $client_id = JRequest::getVar("client_id");
    $id = JRequest::getVar("id");

    $db = & JFactory::getDBO();
    $query = "SELECT client_id FROM #__fitness_appointment_clients WHERE event_id='$event_id' AND client_id='$client_id'";
    $db->setQuery($query);
    if (!$db->query()) {
        $status['success'] = 0;
        $status['message'] = $db->stderr();
    }
    $client = $db->loadResult();

    if ($client == $client_id) {
        $user = &JFactory::getUser($client_id);
        $status['success'] = 0;
        $status['message'] = $user->username . ' already added for this appointment';
        return $status;
    }

    if ($id) {
        $query = "UPDATE `#__fitness_appointment_clients` SET `client_id` = '$client_id' WHERE `id` ='$id'";
        $db->setQuery($query);
        $status['success'] = 1;
        if (!$db->query()) {
            $status['success'] = 0;
            $status['message'] = $db->stderr();
        }
    } else {

        $query = "INSERT  INTO `#__fitness_appointment_clients` (`client_id`,`event_id`,`status`) VALUES ('$client_id', '$event_id', '1')";
        $db->setQuery($query);
        $status['success'] = 1;
        if (!$db->query()) {
            $status['success'] = 0;
            $status['message'] = $db->stderr();
        }
        $status['id'] = $db->insertid();
    }
    return $status;
}

function delete_group_client() {
    $id = JRequest::getVar("id");
    $db = & JFactory::getDBO();
    $query = "DELETE FROM #__fitness_appointment_clients WHERE id='$id'";
    $db->setQuery($query);
    $status['id'] = $id;
    if (!$db->query()) {
        $status['success'] = 0;
        $status['message'] = $db->stderr();
    }
    $status['success'] = 1;
    return $status;
}

/**
 * set event status, on  click status button
 */
function set_group_client_status() {
    $id = JRequest::getVar("id");
    $client_status = JRequest::getVar("client_status");
    $db = & JFactory::getDBO();
    $query = "UPDATE #__fitness_appointment_clients SET status='$client_status'  WHERE id='$id'";
    $db->setQuery($query);
    if (!$db->query()) {
        $status['success'] = 0;
        $status['message'] = $db->stderr();
    }
    $status['ids'] = $id;
    $status['success'] = 1;
    return $status;
}


function getCategoryNameColorById($id) {
    $result['success'] = true;
    $db = & JFactory::getDBO();
    $query = "SELECT name, color FROM #__fitness_categories WHERE id='$id' AND state='1'";
    $db->setQuery($query);
    if (!$db->query()) {
        $result['success'] = false;
        $result['message'] = $db->stderr();
    }
    $result['name'] = $db->loadResultArray(0);
    $result['color'] = $db->loadResultArray(1);


    return $result;
}

function saveDragedData() {
    $ret['success'] = true;
    $post = JRequest::get('post');

    $starttime = $post['starttime'];
    $field = $post['field'];
    $value = $post['value'];

    $event_id = $post['event_id'];
    
    $db = & JFactory::getDBO();
    $query = "SELECT id, title,  starttime FROM #__dc_mv_events WHERE id='$event_id'";
    $db->setQuery($query);
    if (!$db->query()) {
        $ret['success'] = false;
        $ret['message'] = $db->stderr();
    }
    $id = $db->loadResultArray(0);
    $id = $id[0];
    $event_name = $db->loadResultArray(1);
    $event_name = $event_name[0];

    if ($id) {
        if ($field == 'client_id') {
            $client_id = $value;
            $insertGroupClient = insertGroupClient($event_id, $client_id);
            if(!$insertGroupClient['success']) {
                $ret['success'] = false;
                $ret['message'] = $insertGroupClient['message'];
            }
            return $ret;
        }
        
        $query = "UPDATE #__dc_mv_events SET $field='$value' WHERE id='$id'";
        
        $db->setQuery($query);
        if (!$db->query()) {
            $ret['success'] = false;
            $ret['message'] = $db->stderr();
            return $ret;
        }

    } else {
        if ($field == 'title') {
            
            $helper = new FitnessHelper();

            $cid = JRequest::getVar( 'cid' );
            
            $user = &JFactory::getUser($cid);

            $user_id = $user->id;
            
            $is_client = (bool) FitnessFactory::is_client($user_id);
  
            if($is_client) {
                $primary_trainer = $helper->getPrimaryTrainer($user_id);
                $post['trainer_id'] = $primary_trainer->id;
                JRequest::setVar('client_id', $user_id);
                $post['frontend_published'] = '1';
            }
            $post['title'] = $value;
            $post['description'] = '';
            $post['comments'] = '';

            $insert = insertEvent($post);
            
            if(!$insert['success']) {
                $ret['success'] = false;
                $ret['message'] = $insert['message'];
                return $ret;
            }
        } else {
            $ret['success'] = false;
            $ret['message'] = 'Place appointment first';
        }
    }

    return $ret;
}

function insertGroupClient($event_id, $client_id) {
    $ret['success'] = true;
    $db = & JFactory::getDBO();
    $query = "SELECT client_id FROM #__fitness_appointment_clients WHERE event_id='$event_id' AND client_id='$client_id'";
    $db->setQuery($query);
    if (!$db->query()) {
        $ret['success'] = false;
        $ret['message'] = $db->stderr();
        return $ret;
    }
    $client = $db->loadResult();

    if ($client == $client_id) {
        $user = &JFactory::getUser($client_id);
        $ret['success'] = false;
        $ret['message'] = $user->username . ' already added for this appointment';
        return $ret;
    }
    $query = "INSERT  INTO #__fitness_appointment_clients (event_id, client_id)
        VALUES ('$event_id', '$client_id')";

    $db->setQuery($query);
    if (!$db->query()) {
        $ret['success'] = false;
        $ret['message'] = $db->stderr();
    }

    return $ret;
}

function insertEvent($post) {
    $ret['success'] = true;
    $db = & JFactory::getDBO();
    $obj = new stdClass();
    $obj->starttime = $post['starttime'];
    $obj->endtime = $post['endtime'];
    $obj->trainer_id = $post['trainer_id'];
    $obj->location = $post['location'];
    $obj->description = $post['description'];
    $obj->comments = $post['comments'];
    $obj->title = $post['title'];
    $obj->calid = JRequest::getVar('calid');
    $obj->published = 1;
    $obj->frontend_published = $post['frontend_published'];
    $obj->owner = JRequest::getVar('cid');
    $obj->business_profile_id = $post['business_profile_id'];

    $insert = $db->insertObject('#__dc_mv_events', $obj, 'id');
    
    if(!$insert) {
        $ret['success'] = false;
        $ret['message'] = $db->stderr();
    }
    
    $id = $db->insertid();
    
    $client_added = addAppointmentClient($id);
    if(!$client_added['success']) {
        $ret['success'] = false;
        $ret['message'] = $client_added['message'];
        return $ret;
    }
    
    $ret['data'] = $id;
            
    return $ret;
}

function sendRemindersManually() {
    $db = & JFactory::getDBO();
    $appointments = JRequest::getVar('appointments');
    $appointments = "'" . implode("','", $appointments) . "'";
    $reminder_from = JRequest::getVar('reminder_from');
    $reminder_from_formated = $reminder_from . ' 00:00';
    $reminder_to = JRequest::getVar('reminder_to');
    $reminder_to_formated = $reminder_to . ' 23:59';

    $query = "SELECT id FROM #__dc_mv_events WHERE title IN ($appointments) ";
    if ($reminder_from AND $reminder_to) {
        $query .= " AND starttime BETWEEN" . $db->quote($reminder_from_formated) . "AND" . $db->quote($reminder_to_formated);
    }
    $query .= "
        AND title  IN (
        '1', 
        '2', 
        '5',
        '6',
        '7') 
    ";
     // IN 'Personal Training', 'Semi-Private Training', 'Assessment', 'Consultation', 'Special Event'
    $db->setQuery($query);
    if (!$db->query()) {
        $ret['success'] = false;
        $ret['message'] = $db->stderr();
        return $ret;
    }
    $event_ids = $db->loadResultArray(0);
    
    $obj = new AppointmentEmail();
    $ret['success'] = true;
    
    foreach ($event_ids as $event_id) {
        try {

            $data_obj = new stdClass();
            $data_obj->id = $event_id;
            $data_obj->method = 'Appointment';
            $data_obj->user_id = JRequest::getVar('cid');

            $emails  .= ' ' .$obj->processing($data_obj);
        } catch (Exception $exc) {
            $ret['success'] = 0;
            $ret['message'] = $exc->getMessage();
        }
    }
    
    $ret['message'] = $emails;

    return $ret;
}

function deleteEvent() {
    $ret['success'] = true;

    $event_id = JRequest::getVar('event_id');
    
    $cid = JRequest::getVar('cid');
    $user = &JFactory::getUser($cid);
    $user_id = $user->id;
    
    $db = & JFactory::getDBO();
    
    $helper = new FitnessHelper();
    
    $is_client = (bool) FitnessHelper::is_client($user_id);

    if($is_client) {
        $query = "SELECT * FROM #__dc_mv_events WHERE id='$event_id'";
        $db->setQuery($query);
        if (!$db->query()) {
            $ret['success'] = false;
            $ret['message'] = $db->stderr();
            return $ret;
        }

        $event = $db->loadObject();
        
        $appointment_id = $event->title;
        
        $created_by = $event->owner;

        $readonly = $helper->eventCalendarFrontendReadonly($appointment_id, $user_id);
        
        //Resistance Workout,  Cardio Workout, Available, Unavailable
        if(!$helper->eCalendarFrontendAllowDel($appointment_id, $created_by, $user_id)) {
            return $ret;
        }

        if($readonly) {
            return $ret;
        }
    }
    
    
    $query = "DELETE FROM #__dc_mv_events WHERE id='$event_id'";
    $db->setQuery($query);
    if (!$db->query()) {
        $ret['success'] = false;
        $ret['message'] = $db->stderr();
    }
    return $ret;
}




jexit();
?>