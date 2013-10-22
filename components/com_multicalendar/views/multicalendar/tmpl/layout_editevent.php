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

$hoursStart = (is_numeric($_GET["hoursStart"]))?$_GET["hoursStart"]:0;
$hoursEnd = (is_numeric($_GET["hoursEnd"]))?$_GET["hoursEnd"]:23;
$db 	=& JFactory::getDBO();
$db->setQuery( "select palettes from #__dc_mv_configuration where id=1" );
$palettes = $db->loadObjectList();
$palettes = unserialize($palettes[0]->palettes);
if (count($palettes) > $_GET["palette"])
    $palette = $palettes[$_GET["palette"]];
else
    $palette = $palettes[0];

//exit;

function getCalendarByRange($id){
  try{
    $db 	=& JFactory::getDBO();
    //$sql = "select * from `".DC_MV_CAL."` where `".DC_MV_CAL_ID."` = " . $id;

    $sql = "SELECT * FROM #__dc_mv_events LEFT JOIN #__fitness_assessments ON #__dc_mv_events.id = #__fitness_assessments.event_id WHERE #__dc_mv_events.id='$id'";

    $db->setQuery( $sql );

    $rows = $db->loadObjectList();
	}catch(Exception $e){
  }

  return $rows[0];
}
function fomartTimeAMPM($h,$m) {
    if (JRequest::getVar("mt")!="false")
        $tmp = (($h < 10)  ? "0" : "") . $h . ":" . (($m < 10)?"0":"") . $m  ;
    else
    {
            $tmp = (($h%12) < 10) && $h!=12 ? "0" . ($h%12)  : ($h==12?"12":($h%12))  ;
            $tmp .= ":" . (($m < 10)?"0":"") . $m . (($h>=12)?"pm":"am");
    }
    return $tmp ;
}
if(JRequest::getVar("id")!=""){
  $event = getCalendarByRange(JRequest::getVar("id"));
}

$path = JURI::root(true)."/components/com_multicalendar/DC_MultiViewCal/";
$datafeed = JURI::root()."index.php?option=com_multicalendar&task=load";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" >
  <head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <title>Calendar Details</title>
 
<?php
if (file_exists("./components/com_multicalendar/DC_MultiViewCal/css/".$_GET["css"]."/calendar.css"))
{
?>
    <link type="text/css" href="<?php echo $path; ?>css/<?php echo $_GET["css"]?>/calendar.css" rel="stylesheet" />
<?php } else { ?>
    <link type="text/css" href="<?php echo $path; ?>css/cupertino/calendar.css" rel="stylesheet" />
<?php } ?>
		<script type="text/javascript" src="<?php echo $path; ?>js/jquery-1.7.2.min.js"></script>
		<script type="text/javascript" src="<?php echo $path; ?>js/jquery-ui-1.8.20.custom.min.js"></script>
                <script type="text/javascript" src="<?php echo $path; ?>js/jquery.tablednd.js"></script>
		<script src="<?php echo $path; ?>src/Plugins/Common.js" type="text/javascript"></script>
                
                <script type="text/javascript" src="<?php echo JUri::root() . 'administrator/components' . DS . 'com_fitness' . DS .'assets'. DS .'js'. DS . 'underscore-min.js'?>"></script>
                <script type="text/javascript" src="<?php echo JUri::root() . 'administrator/components' . DS . 'com_fitness' . DS .'assets'. DS .'js'. DS . 'backbone-min.js'?>"></script>
                <script type="text/javascript" src="<?php echo JUri::root() . 'administrator/components' . DS . 'com_fitness' . DS .'assets'. DS .'js'. DS . 'ajax_call_function.js'?>"></script>
                <script type="text/javascript" src="<?php echo JUri::root() . 'administrator/components' . DS . 'com_fitness' . DS .'assets'. DS .'js'. DS . 'fitness_helper.js'?>"></script>



        <script src="<?php echo $path; ?>src/Plugins/jquery.form.js" type="text/javascript"></script>
<?php
$mainframe = JFactory::getApplication();
if (file_exists("./components/com_multicalendar/DC_MultiViewCal/language/multiview_lang_".$mainframe->getCfg('language').".js"))
{
?>
		<script src="<?php echo $path; ?>language/multiview_lang_<?php echo $mainframe->getCfg('language')?>.js" type="text/javascript"></script>
<?php } else { ?>
		<script src="<?php echo $path; ?>language/multiview_lang_en-GB.js" type="text/javascript"></script>
<?php } ?>
		<script src="<?php echo $path; ?>src/Plugins/jquery.calendar.js" type="text/javascript"></script>
		<script src="<?php echo $path; ?>src/Plugins/jquery.validate.js" type="text/javascript"></script>
		<script src="<?php echo $path; ?>src/Plugins/jquery.colorselect.js" type="text/javascript"></script>
		<script src="<?php echo $path; ?>src/Plugins/jquery.dropdown.js" type="text/javascript"></script>

    <link href="<?php echo $path; ?>css/main.css" rel="stylesheet" />
    <link href="<?php echo $path; ?>css/dropdown.css" rel="stylesheet" />
    <link href="<?php echo $path; ?>css/colorselect.css" rel="stylesheet" />

    <link rel="stylesheet" type="text/css" href="<?php echo $path; ?>src/Plugins/jquery.cleditor.css" />
    <script type="text/javascript" src="<?php echo $path; ?>src/Plugins/jquery.cleditor.js"></script>
    <script type="text/javascript" src="<?php echo $path; ?>src/Plugins/repeat.js"></script>
    

    <script type="text/javascript">
        var __WDAY = new Array(i18n.dcmvcal.dateformat.sun, i18n.dcmvcal.dateformat.mon, i18n.dcmvcal.dateformat.tue, i18n.dcmvcal.dateformat.wed, i18n.dcmvcal.dateformat.thu, i18n.dcmvcal.dateformat.fri, i18n.dcmvcal.dateformat.sat);
        var __WDAY2 = new Array(i18n.dcmvcal.dateformat.sun2, i18n.dcmvcal.dateformat.mon2, i18n.dcmvcal.dateformat.tue2, i18n.dcmvcal.dateformat.wed2, i18n.dcmvcal.dateformat.thu2, i18n.dcmvcal.dateformat.fri2, i18n.dcmvcal.dateformat.sat2);
        var __MonthName = new Array(i18n.dcmvcal.dateformat.jan, i18n.dcmvcal.dateformat.feb, i18n.dcmvcal.dateformat.mar, i18n.dcmvcal.dateformat.apr, i18n.dcmvcal.dateformat.may, i18n.dcmvcal.dateformat.jun, i18n.dcmvcal.dateformat.jul, i18n.dcmvcal.dateformat.aug, i18n.dcmvcal.dateformat.sep, i18n.dcmvcal.dateformat.oct, i18n.dcmvcal.dateformat.nov, i18n.dcmvcal.dateformat.dec);
        var __MonthNameLarge = new Array(i18n.dcmvcal.dateformat.l_jan, i18n.dcmvcal.dateformat.l_feb, i18n.dcmvcal.dateformat.l_mar, i18n.dcmvcal.dateformat.l_apr, i18n.dcmvcal.dateformat.l_may, i18n.dcmvcal.dateformat.l_jun, i18n.dcmvcal.dateformat.l_jul, i18n.dcmvcal.dateformat.l_aug, i18n.dcmvcal.dateformat.l_sep, i18n.dcmvcal.dateformat.l_oct, i18n.dcmvcal.dateformat.l_nov, i18n.dcmvcal.dateformat.l_dec);
        var __MilitaryTime = <?php echo  (JRequest::getVar("mt")!="false")?"true":"false";?>

        if (!DateAdd || typeof (DateDiff) != "function") {
            var DateAdd = function(interval, number, idate) {
                number = parseInt(number);
                var date;
                if (typeof (idate) == "string") {
                    date = idate.split(/\D/);
                    eval("var date = new Date(" + date.join(",") + ")");
                }
                if (typeof (idate) == "object") {
                    date = new Date(idate.toString());
                }
                switch (interval) {
                    case "y": date.setFullYear(date.getFullYear() + number); break;
                    case "m": date.setMonth(date.getMonth() + number); break;
                    case "d": date.setDate(date.getDate() + number); break;
                    case "w": date.setDate(date.getDate() + 7 * number); break;
                    case "h": date.setHours(date.getHours() + number); break;
                    case "n": date.setMinutes(date.getMinutes() + number); break;
                    case "s": date.setSeconds(date.getSeconds() + number); break;
                    case "l": date.setMilliseconds(date.getMilliseconds() + number); break;
                }
                return date;
            }
        }
        function formatDateFromTo(value,y1_index,m1_index,d1_index,separator1,y2_index,m2_index,d2_index,separator2)
        {
            var arrs = value.split(separator1);
            var year = arrs[y1_index];
            var month = arrs[m1_index];
            var day = arrs[d1_index];

            var newArray = new Array();
            newArray[y2_index] = year;
            newArray[m2_index] = month;
            newArray[d2_index] = day;
            value = newArray.join(separator2);
            return value;
        }
        function getHM(date)
        {
             var hour =date.getHours();
             var minute= date.getMinutes();
             var ret= (hour>9?hour:"0"+hour)+":"+(minute>9?minute:"0"+minute) ;
             return ret;
        }
        $(document).ready(function() {
            
            
        
            //debugger;
            $("#Description").cleditor({width:450, height:150, useCSS:true})[0].focus();
            $("#trainer_comments").cleditor({width:560, height:150, useCSS:true})[0];
            var DATA_FEED_URL = "<?php echo $datafeed?>&calid=<?php echo $_GET["calid"]?>";
            var arrT = [];
            var tt = "{0}:{1}";
            for (var i = <?php echo $hoursStart?>; i <= <?php echo $hoursEnd?>; i++) {
                //arrT.push({ text: StrFormat(tt, [i >= 10 ? i : "0" + i, "00"]) }, { text: StrFormat(tt, [i >= 10 ? i : "0" + i, "30"]) });
                arrT.push({ text: fomartTimeAMPM(i,0,__MilitaryTime) }, {  text: fomartTimeAMPM(i,15,__MilitaryTime) },{  text: fomartTimeAMPM(i,30,__MilitaryTime) },{  text: fomartTimeAMPM(i,45,__MilitaryTime) });
            }

            $("#timezone").val(new Date().getTimezoneOffset()/60 * -1);
            $("#stparttime").dropdown({
                dropheight: 200,
                dropwidth:56,
                selectedchange: function() { },
                items: arrT
            });
            $("#etparttime").dropdown({
                dropheight: 200,
                dropwidth:56,
                selectedchange: function() { },
                items: arrT
            });
            $("#repeatcheckbox").click(function(e) {
                if (!this.checked)
                {
                    $("#rrule").val("");
                    $("#repeatspan").html("");
                } 
                else
                {
                    $("#rrule").val($("#format").val());
                    $("#repeatspan").html($("#summary").html());
                    openRepeatWin();
                }   
            });
            $("#repeatanchor").click(function(e) {
                openRepeatWin();
                
            });
            
            var check = $("#IsAllDayEvent").click(function(e) {
                if (this.checked) {
                    $("#stparttime").val(fomartTimeAMPM(0,0,__MilitaryTime)).hide();
                    $("#etparttime").val(fomartTimeAMPM(0,0,__MilitaryTime)).hide();
                }
                else {
                    var d = new Date();
                    var p = 60 - d.getMinutes();
                    if (p > 30) p = p - 30;
                    d = DateAdd("n", p, d);
                    $("#stparttime").val(fomartTimeAMPM(d.getHours(),d.getMinutes(),__MilitaryTime)).show();
                    d = DateAdd("h", 1, d);
                    $("#etparttime").val(fomartTimeAMPM(d.getHours(),d.getMinutes(),__MilitaryTime)).show();
                }
            });
            if (check[0].checked) {
                $("#stparttime").val(fomartTimeAMPM(0,0,__MilitaryTime)).hide();
                $("#etparttime").val(fomartTimeAMPM(0,0,__MilitaryTime)).hide();
            }
            $("#repeat1").html(i18n.dcmvcal.repeat);
            $("#repeatanchor").html(i18n.dcmvcal.edit);
            $( "#s_subject" ).html(i18n.dcmvcal.subject);
            $( "#s_time" ).html(i18n.dcmvcal.time);
            $( "#s_to" ).html(i18n.dcmvcal.to);
            $( "#s_all_day_event" ).html(i18n.dcmvcal.all_day_event);
            $( "#s_location" ).html(i18n.dcmvcal.location);
            $( "#s_remark" ).html(i18n.dcmvcal.remark);
            $("#savebtn,#saveclosebtn,#closebtn,#deletebtn" ).button();
            $( "#savebtn" ).button( "option", "label", i18n.dcmvcal.i_save );
            $( "#closebtn" ).button( "option", "label", i18n.dcmvcal.i_close );
            $( "#deletebtn" ).button( "option", "label", i18n.dcmvcal.i_delete );
            $("#savebtn").click(function() { 
                close_status = false;
                $("#fmEdit").submit();
            });
            
            $("#saveclosebtn").click(function() { 
                close_status = true;
                $("#fmEdit").submit();
            });
            
            
            $("#closebtn").click(function() { closeEdit(); });
            deleteEvent = function(){
                var param = [{ "name": "calendarId", value: <?php echo isset($event)?$event->id:0; ?>},{ "name": "rruleType", value:$( "#rruleType" ).val() }];
                    $.ajaxSetup({
                       jsonp: null,
                       jsonpCallback: null
                    }); 
                    $.post(DATA_FEED_URL + "&method=remove",
                        param,
                        function(data){
                              if (data.success) {
                                    closeEdit();
                                }
                                else
                                    alert(i18n.dcmvcal.error_occurs+ ".\r\n" + ((data.Msg=='OVERLAPPING')?i18n.dcmvcal.error_overlapping:data.Msg));
                        }
                    ,"json");
            } 
            $("#deletebtn").click(function() {
<?php if (isset($event) && ($event->rrule!="")) { ?>
                $("#repeatdelete").dialog({width:500,modal: true,resizable: false}).parent().addClass("mv_dlg").addClass("mv_dlg_editevent").addClass("infocontainer") ;    
<?php } else { ?>                
                 if (confirm(i18n.dcmvcal.are_you_sure_delete)) {
                    deleteEvent();
                } 
<?php } ?>                
            }); 
 
           //$("#stpartdate,#etpartdate").datepicker({ picker: "<button class='calpick'></button>",});
              var arrs = new Array
              arrs[i18n.dcmvcal.dateformat.year_index] = "yy";
              arrs[i18n.dcmvcal.dateformat.month_index] = "mm";
              arrs[i18n.dcmvcal.dateformat.day_index] = "dd";
              var dateFormat = arrs.join(i18n.dcmvcal.dateformat.separator);
              var dates = $( "#stpartdate, #etpartdate" ).datepicker({numberOfMonths: 1,
              dateFormat: dateFormat,
              monthNamesShort:__MonthName,
              monthNames:__MonthNameLarge,
              dayNamesShort:__WDAY,
              dayNamesMin:__WDAY2,
              firstDay: <?php echo (isset($_GET["weekstartday"]))?$_GET["weekstartday"]:1;?>,
			  changeMonth: true,
			  showOn: "button",
			  	 		//buttonImage: "<?php echo $path; ?>css/images/cal.gif",
			  onSelect: function( selectedDate ) {
			  	 var option = this.id == "stpartdate" ? "minDate" : "maxDate",
			  	 	instance = $( this ).data( "datepicker" ),
			  	 	date = $.datepicker.parseDate(
			  	 		instance.settings.dateFormat ||
			  	 		$.datepicker._defaults.dateFormat,
			  	 		selectedDate, instance.settings );
			  	 dates.not( this ).datepicker( "option", option, date );
			  } 
		      }); 
            var cv =$("#colorvalue").val() ;
            if(cv=="") 
            { 
                cv="-1"; 
            } 
            $("#calendarcolor").colorselect({ title: i18n.dcmvcal.color, index: cv, hiddenid: "colorvalue",colors:<?php echo json_encode($palette);?>,paletteDefault:"<?php echo $_GET["paletteDefault"];?>" });
            //to define parameters of ajaxform
             
            var options = {
                beforeSubmit: function() {
                    return true;
                }, 
                jsonp: null,
                jsonpCallback: null,
 
                dataType: "json",
                success: function(data) {
                    //console.log(data.Data);
                    if (data.success) {
                        var event_id = data.Data;
                        var current_url = window.location.href.replace('&id=0').replace('#') + '&id=' + event_id +'#';
                        //console.log(current_url);
                        $.ajax({
                            type : "POST",
                            url : current_url,
                            dataType : 'html',
                            success : function(content) {
                                var height = 820;
                                var iframe_start = '<iframe id="dailog_iframe_1305934814858" frameborder="0" style="overflow-y: auto;overflow-x: hidden;border:none;width:598px;height:'+(height-60)+'px" src="'+current_url+'" border="0" scrolling="auto">';
                                var iframe_end = '</iframe>';
                                
                                if(close_status) {
                                    closeEdit();
                                }
                                
                                if(window.parent.$jc === undefined) {
                                    window.parent.updateAppointmentHtml(iframe_start + iframe_end);
                                } else {
                                    window.parent.$jc('#editEvent').html(iframe_start +  iframe_end);
                                }
                            },
                            error: function(XMLHttpRequest, textStatus, errorThrown)
                            {
                                alert("error");
                            }
                        });
              
                    } 
                    else 
                        alert(i18n.dcmvcal.error_occurs+ ".\r\n" + ((data.Msg=='OVERLAPPING')?i18n.dcmvcal.error_overlapping:data.Msg));
                } 
            }; 
            $("#r_save_one","#r_save_following","#r_save_all","#r_save_cancel","#r_delete_one","#r_delete_following","#r_delete_all","#r_delete_cancel" ).button();
            $("#r_save_one").click(function() {          
                $("#rruleType").val("only");
                $("#repeatsave").dialog('close');
                $("#fmEdit").ajaxSubmit(options);
            }); 
            $("#r_save_following").click(function() {
                value = $("#stpartdatelast").val();
                var arrs = value.split("/");
                var endDate = new Date(arrs[2], arrs[0]-1, arrs[1]);
                var endDate = DateAdd("d", -1, endDate);                
                $("#rruleType").val("UNTIL="+timeToUntilString(endDate));
                $("#repeatsave").dialog('close');
                $("#fmEdit").ajaxSubmit(options);
            }); 
            $("#r_save_all").click(function() {
                $("#rruleType").val("all");
                $("#repeatsave").dialog('close');
                $("#fmEdit").ajaxSubmit(options);
            }); 
            $("#r_save_cancel").click(function() {
                $("#repeatsave").dialog('close');
            }); 
            $("#r_delete_one").click(function() {
                var arrs = $("#stpartdate").val().split(i18n.dcmvcal.dateformat.separator);
                var year = arrs[i18n.dcmvcal.dateformat.year_index];
                var month = arrs[i18n.dcmvcal.dateformat.month_index];
                var day = arrs[i18n.dcmvcal.dateformat.day_index];
                $("#stpartdatelast").val([month,day,year].join("/"));
                 
                $("#rruleType").val("del_only,"+$("#stpartdatelast").val());
                $("#repeatdelete").dialog('close');
                deleteEvent();
            }); 
            $("#r_delete_following").click(function() {
                 
                var arrs = $("#stpartdate").val().split(i18n.dcmvcal.dateformat.separator);
                var year = arrs[i18n.dcmvcal.dateformat.year_index];
                var month = arrs[i18n.dcmvcal.dateformat.month_index];
                var day = arrs[i18n.dcmvcal.dateformat.day_index];
                $("#stpartdatelast").val([month,day,year].join("/"));
                 
                value = $("#stpartdatelast").val();
                var arrs = value.split("/");
                var endDate = new Date(arrs[2], arrs[0]-1, arrs[1]);
                var endDate = DateAdd("d", -1, endDate); 
                $("#rruleType").val("del_UNTIL="+timeToUntilString(endDate));
                $("#repeatdelete").dialog('close');                
                deleteEvent();
            }); 
            $("#r_delete_all").click(function() {
                $("#rruleType").val("del_all");
                $("#repeatdelete").dialog('close');
                deleteEvent();
            }); 
            $("#r_delete_cancel").click(function() {
                $("#repeatdelete").dialog('close');
            }); 
            $.validator.addMethod("date", function(value, element) {
                var arrs = value.split(i18n.dcmvcal.dateformat.separator);
                var year = arrs[i18n.dcmvcal.dateformat.year_index];
                var month = arrs[i18n.dcmvcal.dateformat.month_index];
                var day = arrs[i18n.dcmvcal.dateformat.day_index];
                var standvalue = [year,month,day].join("-");
 
                var r = this.optional(element) || /^(?:(?:1[6-9]|[2-9]\d)?\d{2}[\/\-\.](?:0?[1,3-9]|1[0-2])[\/\-\.](?:29|30))(?: (?:0?\d|1\d|2[0-3])\:(?:0?\d|[1-5]\d)\:(?:0?\d|[1-5]\d)(?: \d{1,3})?)?$|^(?:(?:1[6-9]|[2-9]\d)?\d{2}[\/\-\.](?:0?[1,3,5,7,8]|1[02])[\/\-\.]31)(?: (?:0?\d|1\d|2[0-3])\:(?:0?\d|[1-5]\d)\:(?:0?\d|[1-5]\d)(?: \d{1,3})?)?$|^(?:(?:1[6-9]|[2-9]\d)?(?:0[48]|[2468][048]|[13579][26])[\/\-\.]0?2[\/\-\.]29)(?: (?:0?\d|1\d|2[0-3])\:(?:0?\d|[1-5]\d)\:(?:0?\d|[1-5]\d)(?: \d{1,3})?)?$|^(?:(?:16|[2468][048]|[3579][26])00[\/\-\.]0?2[\/\-\.]29)(?: (?:0?\d|1\d|2[0-3])\:(?:0?\d|[1-5]\d)\:(?:0?\d|[1-5]\d)(?: \d{1,3})?)?$|^(?:(?:1[6-9]|[2-9]\d)?\d{2}[\/\-\.](?:0?[1-9]|1[0-2])[\/\-\.](?:0?[1-9]|1\d|2[0-8]))(?: (?:0?\d|1\d|2[0-3])\:(?:0?\d|[1-5]\d)\:(?:0?\d|[1-5]\d)(?:\d{1,3})?)?$/.test(standvalue);
                if (r) 
                { 
                    $("#"+element.id+"last").val([month,day,year].join("/"));
                } 
                return r;
            }, i18n.dcmvcal.invalid_date_format);
            $.validator.addMethod("time", function(value, element) {
                if (__MilitaryTime)
                    var r =  this.optional(element) || /^([0-1]?[0-9]|2[0-3]):([0-5][0-9])$/.test(value);
                else 
                    var r =  this.optional(element) || /^(0[0-9]|1[0-2]):([0-5][0-9](am|pm))$/.test(value);
                if (r) 
                { 
                    if (__MilitaryTime)
                        $("#"+element.id+"last").val($("#"+element.id).val());
                    else 
                    { 
                         
                        var v = $("#"+element.id).val();
                        if (v.indexOf("am")!=-1)
                            v = v.replace("am","");
                        else
                        {
                            v = v.replace("pm","");
                            var d = v.split(":");
                            v = ((parseInt(d[0]*1)==12)?12:(parseInt(d[0]*1)+12))+":"+d[1];
                        }    
                        $("#"+element.id+"last").val(v);
                    }    
                } 
                return r;    
            }, i18n.dcmvcal.invalid_time_format);
            $.validator.addMethod("safe", function(value, element) {
                return this.optional(element) || /^[^$\<\>]+$/.test(value);
            }, i18n.dcmvcal._simbol_not_allowed);
            $("#fmEdit").validate({
                submitHandler: function(form) {
                // 
                <?php if (isset($event) && ($event->rrule!="")) { ?>
                $("#repeatsave").dialog({width:500,modal: true,resizable: false}).parent().addClass("mv_dlg").addClass("mv_dlg_editevent").addClass("infocontainer") ;    
                <?php } else { ?> 
                                $("#fmEdit").ajaxSubmit(options);
                <?php } ?> 
                 
                }, 
                errorElement: "div",
                errorClass: "cusErrorPanel",
                errorPlacement: function(error, element) {
                    showerror(error, element);
                }  
            });  
            function showerror(error, target) {
                var pos = target.position();
                var height = target.height();
                var newpos = { left: pos.left, top: pos.top + height + 2 }
                var form = $("#fmEdit");
                error.appendTo(form).css(newpos);
            }  
            
            
            function closeEdit() {
                if(window.parent.$jc === undefined) {
                    window.parent.closeEditForm();
                    window.parent.location.reload();
                } else {
                    window.parent.$jc('#editEvent').dialog('close');
                }

            }
       
           
        });  

    </script>  
       
    <!-- Top form, calendar, appointment status -->
    <?php
    require_once( JPATH_BASE.'/components/com_multicalendar/DC_MultiViewCal/php/top_form.inc.php' );
    ?>
    <!-- Main fields -->
    <?php
    require_once( JPATH_BASE.'/components/com_multicalendar/DC_MultiViewCal/php/main_fields.inc.php' );
    ?>
    <!-- Add clients, Semi-Private form -->
    <?php
    require_once( JPATH_BASE.'/components/com_multicalendar/DC_MultiViewCal/php/clients.inc.php' );
    ?>
    <!-- Assessment form -->
    <?php
    require_once( JPATH_BASE.'/components/com_multicalendar/DC_MultiViewCal/php/assessment.inc.php' );
    ?>
    <!-- Details, Email, Pdf -->
    <?php
    require_once( JPATH_BASE.'/components/com_multicalendar/DC_MultiViewCal/php/details.inc.php' );
    ?>
    <!-- Exicise table --> 
    <?php
    if (isset($event->status)) {
        require_once( JPATH_BASE.'/components/com_multicalendar/DC_MultiViewCal/php/exercises.inc.php' );
    }
    ?>
    <!-- Trainer Feedback / Comments -->
    <?php
    require_once( JPATH_BASE.'/components/com_multicalendar/DC_MultiViewCal/php/comments.inc.php' );
    ?>
    <!-- Bottom form --> 
    <?php
    require_once( JPATH_BASE.'/components/com_multicalendar/DC_MultiViewCal/php/bottom_form.inc.php' );
    ?>
         
  
<?php  
jexit();  
?>    
    
   