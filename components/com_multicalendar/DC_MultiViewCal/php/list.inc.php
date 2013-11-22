<?php
require_once  JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_fitness' . DS .'helpers' . DS . 'fitness.php';

/** get locations from fitness component
 * npkorban
 * 
 * @return type
 */
function getLocations() { 
    $db	= & JFactory::getDBO();
    $query = "SELECT name FROM #__fitness_locations";
    $db->setQuery($query);
    if(!$db->query()) {
        JError::raiseError($db->getErrorMsg());
    }
    $locations = $db->loadResultArray();
    return $locations;
}

/** get Appointment (category) from fitness component
 * npkorban
 * 
 * @return type
 */
function getAppointments() { 
    $db	= & JFactory::getDBO();
    $query = "SELECT name, color, id FROM #__fitness_categories";
    $db->setQuery($query);
    $appointment[0] = $db->loadResultArray(0);
    $appointment[1] = $db->loadResultArray(1);
    $appointment[2] = $db->loadResultArray(2);
    return $appointment;
}

function getEmailPdfData($event_id) {
   $db	= & JFactory::getDBO();
   $query = "SELECT * FROM #__dc_mv_events WHERE id='$event_id'";
   $db->setQuery($query);
    if(!$db->query()) {
        JError::raiseError($db->getErrorMsg());
    }
   $result = $db->loadObject();
   return $result;
}



/**
 * npkorban
 * @param type $event_id
 * @param type $event_status
 * @return string
 */  
function event_state_html($event_status) {
    $html = '';
    switch ($event_status) {
        case 1:
            $html .= '<a data-status="1" class="open_status event_status_pending event_status__button" href="javascript:void(0)">pending</a>';
            break;
        case 2:
            $html .= '<a data-status="2" class="open_status event_status_attended event_status__button" href="javascript:void(0)">attended</a>';
            break;
        case 3:
            $html .= '<a data-status="3" class="open_status event_status_cancelled event_status__button" href="javascript:void(0)">cancelled</a>';
            break;
        case 4:
            $html .= '<a data-status="4" class="open_status event_status_latecancel event_status__button" href="javascript:void(0)">late cancel</a>';
            break;
        case 5:
            $html .= '<a data-status="5" class="open_status event_status_noshow event_status__button" href="javascript:void(0)">no show</a>';
            break;
        default:
            $html .= '<a data-status="1" class="open_status event_status_pending event_status__button" href="javascript:void(0)">pending</a>';
            break;
            break;
    }

    return $html;
}

function getExercises($event_id) {
    $db	= & JFactory::getDBO();
    $query = "SELECT * FROM #__fitness_events_exercises WHERE event_id='$event_id'ORDER BY `#__fitness_events_exercises`.`order` ASC ";
    $db->setQuery($query);
    if(!$db->query()) {
        JError::raiseError($db->getErrorMsg());
    }
    $result = $db->loadObjectList();
    return $result;
}

//$dc_subjects = array("title 1","title 2","title 3","title 4");
//$dc_locations = array("location 1","location 2","location 3","location 4");
//$dc_subjects = getAppointments();
$dc_locations = getLocations();

$appointments = getAppointments();
$dc_subjects = $appointments[0];


define("JC_JQUERY_MV",true);
global $JC_JQUERY_SPECIAL ;
$JC_JQUERY_SPECIAL = array();
//$JC_JQUERY_SPECIAL = array("http://code.jquery.com/jquery-1.9.1.js","http://code.jquery.com/ui/1.10.2/jquery-ui.js");
define("JC_NO_OVERLAPPING_TIME",false);
define("JC_NO_OVERLAPPING_SUBJECT",false);
define("JC_NO_OVERLAPPING_LOCATION",false);



function isValidOwner($g1)
{
    $g2 = explode("=",$g1);
    if ($g2[1]!="" && $g2[1]!="0")
    {
        $g = explode(",",$g2[1]);
        if (in_array("owner",$g))
            return true;
    }
    return false;
}
function isValid($g1,$u1,$groups,$userid)
{
    $g2 = explode("=",$g1);
    if ($g2[1]!="" && $g2[1]!="0")
    {
        $g = explode(",",$g2[1]);
        for ($i=0;$i<count($g);$i++)
        {
            if (in_array($g[$i],$groups))
                return true;
        }
    }

    $u2 = explode("=",$u1);
    if ($u2[1]!="" && $u2[1]!="0")
    {
        $u = explode(",",$u2[1]);
        if (in_array($userid,$u))
            return true;
    }
    return false;
}

global $arrayJS_list;
$arrayJS_list = 'dc_subjects = ';
if (isset($dc_subjects) && is_array($dc_subjects))
{
    $arrayJS_list .= ' new Array (';
    for ($i=0;$i<count($dc_subjects);$i++)
    {
        if ($i!=0)
            $arrayJS_list .= ', ';
        $arrayJS_list .= '"'.$dc_subjects[$i].'"';
    }
    $arrayJS_list .= ');';
}
else
    $arrayJS_list .= '"";';

$arrayJS_list .= 'dc_locations = ';
if (isset($dc_locations) && is_array($dc_locations))
{
    $arrayJS_list .= ' new Array (';
    for ($i=0;$i<count($dc_locations);$i++)
    {
        if ($i!=0)
            $arrayJS_list .= ', ';
        $arrayJS_list .= '"'.$dc_locations[$i].'"';
    }
    $arrayJS_list .= ');';
}
else
    $arrayJS_list .= '"";';

global $zscripts;$zscripts ='';
function print_html($container)
{
    global $zscripts;
    return '<div style="z-index:1000;">
             <div id="'.$container.'"  class="multicalendar"></div>                                                                                                                                                                                                                                                                                                                                                               '.$zscripts.'
             <div style="clear:both"></div>
           </div>
           ';
}
function print_scripts($id,$container,$language,$style,$views,$buttons,$edition,$sample,$otherparamsvalue,$palette,$viewdefault,$numberOfMonths,$start_weekday,$notPlugin,$matches)
{
    global $JC_JQUERY_SPECIAL ;
    $mainframe = JFactory::getApplication();
    $msg = "";
    if ($id==-1)
    {
        $user =& JFactory::getUser();
        $db		=& JFactory::getDBO();
        $db->setQuery( 'SELECT * FROM #__dc_mv_calendars where owner='.$user->id );
        $rows1 = $db->loadObjectList();
        if (count($rows1)>0)
            $id = $rows1[0]->id;
        else
        {
            $id = "none";
            $msg = "No calendar found for this user";
        }
    }
    if (!is_numeric($id) && $msg=="")
        $msg = "No calendar found";
    
    if ($msg=="")
    {
        $document =& JFactory::getDocument();
        if (JC_JQUERY_MV)
        {
            $document->addScript("components/com_multicalendar/DC_MultiViewCal/js/jquery-1.7.2.min.js");
            $document->addScript("components/com_multicalendar/DC_MultiViewCal/js/jquery-ui-1.8.20.custom.min.js");
        }    
        else
        {
            for ($i=0;$i<count($JC_JQUERY_SPECIAL);$i++)
                $document->addScript($JC_JQUERY_SPECIAL[$i]);
        }
        $document->addScript("components/com_multicalendar/DC_MultiViewCal/src/Plugins/underscore.js");
        $document->addScript("components/com_multicalendar/DC_MultiViewCal/src/Plugins/rrule.js");
        $document->addScript("components/com_multicalendar/DC_MultiViewCal/src/Plugins/Common.js");
        if (file_exists("components/com_multicalendar/DC_MultiViewCal/language/multiview_lang_".$language.".js"))
            $document->addScript("components/com_multicalendar/DC_MultiViewCal/language/multiview_lang_".$language.".js");
        else
            $document->addScript("components/com_multicalendar/DC_MultiViewCal/language/multiview_lang_en-GB.js");
        $document->addScript("components/com_multicalendar/DC_MultiViewCal/src/Plugins/jquery.calendar.js");
        $document->addScript("components/com_multicalendar/DC_MultiViewCal/src/Plugins/jquery.alert.js");
        $document->addScript("components/com_multicalendar/DC_MultiViewCal/src/Plugins/multiview.js");
        
        if (file_exists("components/com_multicalendar/DC_MultiViewCal/css/".$style."/calendar.css"))
            $document->addStyleSheet("components/com_multicalendar/DC_MultiViewCal/css/".$style."/calendar.css");
        else
            $document->addStyleSheet("components/com_multicalendar/DC_MultiViewCal/css/cupertino/calendar.css");
        $document->addStyleSheet("components/com_multicalendar/DC_MultiViewCal/css/main.css"); 
        if (!$notPlugin)
        {
            if (!function_exists('getvalues')) {
                function getvalues($param,$ar)
                {
                    if (!is_array($param)) $param = array();
                    for ($i=0;$i<count($ar);$i++)
                    {
            
                        if ((count($param)<=$i) || ($param[$i]!=$ar[$i])) array_splice ($param, $i, 0, "false"); else $param[$i] = "true";
            
            
                    }
                    return $param;
                }
            }
            $view = getvalues($views,array("viewDay","viewWeek","viewMonth","viewNMonth"));
            $buttons = getvalues($buttons,array("btoday","bnavigation","brefresh"));
            $edition = (($edition=="1")?"true":"false");
            $nmonths = $sample;
            if ($nmonths[0]!='1') array_splice ($nmonths, 0, 0, "false"); else $nmonths[0] = "true";
            if ($nmonths[1]!='mouseover') $nmonths[1]=1; else $nmonths[1] = 0;
            if ($nmonths[2]!='1') array_splice ($nmonths, 2, 0, "false"); else $nmonths[2] = "true";
            if ($nmonths[1]==1) $nmonths[2]="false";
            if ($nmonths[4]!='new_window') $nmonths[4]=1; else $nmonths[4] = 0;
        }    
        else
        {
            $view = array();
            $view[] = (((int)$matches[2][0]==1)?"true":"false");
            $view[] = (((int)$matches[2][1]==1)?"true":"false");
            $view[] = (((int)$matches[2][2]==1)?"true":"false");
            $view[] = (((int)$matches[2][3]==1)?"true":"false");
            $buttons = array();
            $buttons[] = (((int)$matches[7][0]==1)?"true":"false");
            $buttons[] = (((int)$matches[7][1]==1)?"true":"false");
            $buttons[] = (((int)$matches[7][2]==1)?"true":"false");
            $edition = (($edition=="1")?"true":"false");
            $nmonths = array();
            $nmonths[] = (((int)$matches[9]==1)?"true":"false");
            $nmonths[] = (((string)$matches[10]!="mouseover")?"1":"0");
            $nmonths[] = (((int)$matches[11]==1)?"true":"false");
            $nmonths[] = $matches[13];
            $nmonths[] = (((string)$matches[12]!="new_window")?"1":"0");
            
        }
        $otherparams = trim($otherparamsvalue);
        $otherparams = str_replace("\n","",$otherparams);
        $otherparams = str_replace("\r","",$otherparams);
        $newp = "";
        if ($otherparams!="")
        {
            $p = explode(",", $otherparams);
            for($i=0;$i<count($p);$i++)
                if (trim($p[$i])!="")
                    $newp .= ", " .$p[$i];
    
        }
        $user =& JFactory::getUser();
        $db		=& JFactory::getDBO();
        $db->setQuery( 'SELECT * FROM #__dc_mv_calendars where id='.$id );
        $rows = $db->loadObjectList();
        if (count($rows)>0)
        {
            $p = explode(";",$rows[0]->permissions);
            if (isValid($p[0],$p[1],$user->getAuthorisedGroups(),$user->id)) $newp .= ", userAdd:true"; else $newp .= ", userAdd:false";
            if (isValid($p[2],$p[3],$user->getAuthorisedGroups(),$user->id)) $newp .= ", userEdit:true"; else $newp .= ", userEdit:false";
            if (isValid($p[4],$p[5],$user->getAuthorisedGroups(),$user->id)) $newp .= ", userDel:true"; else $newp .= ", userDel:false";
            if (isValidOwner($p[2])) $newp .= ", userEditOwner:true"; else $newp .= ", userEditOwner:false";
            if (isValidOwner($p[4])) $newp .= ", userDelOwner:true"; else $newp .= ", userDelOwner:false";
            $newp .= ", userOwner:".$user->id."";
        }
        $db->setQuery( "select palettes from #__dc_mv_configuration where id=1" );
        $palettes = $db->loadObjectList();
        $palettes = unserialize($palettes[0]->palettes);
        if (count($palettes) > $palette)
        {
            $newp .= ", palette:".$palette."";
            $newp .= ", paletteDefault:\"".$palettes[$palette]["default"]."\"";
        }
        global $arrayJS_list;
        $document->addScriptDeclaration($arrayJS_list);
        $document->addScriptDeclaration("initMultiViewCal(\"".$container."\",".$id.",{viewDay:".$view[0].",viewWeek:".$view[1].",viewMonth:".$view[2].",viewNMonth:".$view[3].",viewdefault:\"".$viewdefault."\",numberOfMonths:".$numberOfMonths.",showtooltip:".$nmonths[0].",tooltipon:".$nmonths[1].",shownavigate:".$nmonths[2].",url:\"".$nmonths[3]."\",target:".$nmonths[4].",start_weekday:".$start_weekday.",language:\"".$language."\",cssStyle:\"".$style."\",edition:".$edition.",btoday:".$buttons[0].",bnavigation:".$buttons[1].",brefresh:".$buttons[2].",bnew:".$edition.",path:\"".JURI::root()."/\"".$newp."});");
    }
    return $msg;
}
?>