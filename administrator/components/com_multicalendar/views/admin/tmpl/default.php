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
require_once( JPATH_COMPONENT_SITE.'/DC_MultiViewCal/php/list.inc.php' );

require_once  JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_fitness' . DS .'helpers' . DS . 'fitness.php';

$helper = new FitnessHelper();

$group_id = $helper->getTrainersGroupId();

$business_profile = $helper->JErrorFromAjaxDecorator($helper->getBusinessByTrainerGroup($group_id));

$business_profile_id = $business_profile->id;



$document = &JFactory::getDocument();
$document -> addscript( JUri::root() . 'administrator/components' . DS . 'com_fitness' . DS .'assets'. DS .'js'. DS . 'jquery.js');
$document -> addscript( JUri::root() . 'administrator/components' . DS . 'com_fitness' . DS .'assets'. DS .'js'. DS . 'jquerynoconflict.js');
$document -> addscript( JUri::root() . 'administrator/components' . DS . 'com_fitness' . DS .'assets'. DS .'js'. DS . 'underscore-min.js');
include_once JPATH_COMPONENT_ADMINISTRATOR . DS .'assets'. DS .'js'. DS . 'underscore_templates.html';
$document -> addscript( JUri::root() . 'administrator/components' . DS . 'com_fitness' . DS .'assets'. DS .'js'. DS . 'backbone-min.js');
$document -> addscript( JUri::root() . 'administrator/components' . DS . 'com_fitness' . DS .'assets'. DS .'js'. DS . 'ajax_call_function.js');
$document -> addscript( JUri::root() . 'administrator/components' . DS . 'com_fitness' . DS .'assets'. DS .'js'. DS . 'fitness_helper.js');


global $arrayJS_list;
global $JC_JQUERY_SPECIAL ;
$mainframe  =& JFactory::getApplication();
?>
<?php JHTML::_('behavior.tooltip'); ?>
<?php
    $cid = JRequest::getVar( 'cid', array(0), '', 'array' );
    $id = $cid[0];
    $db =& JFactory::getDBO();
    $db->setQuery( "select palettes,administration from #__dc_mv_configuration where id=1" );
    $rows = $db->loadObjectList();
    $palettes = unserialize($rows[0]->palettes);
    $admin = unserialize($rows[0]->administration);
    $newp = "";
    if (count($palettes) > $admin["paletteColor"])
    {
        $newp .= ", palette:".$admin["paletteColor"]."";   
        $newp .= ", paletteDefault:\"".$palettes[$admin["paletteColor"]]["default"]."\"";   
    }
    
    $query = 'select * from #__dc_mv_calendars'
        . ' WHERE id = '. $id  ;
    
    $db->setQuery( $query );    
    $rows = $db->loadObjectList();
    if (count($rows)>0){
	    JToolBarHelper::title(   $rows[0]->title . JText::_('COMMULTICALENDAR_TITLE_DASH_ADMIN'), "multicalendar-management" );
    }
	JToolBarHelper::cancel( 'cancel', JText::_('Close') );
    
	JToolBarHelper::help( 'screen.multicalendar.admin', true );
	
	$language = $mainframe->getCfg('language');
?>
<script language="javascript" type="text/javascript">
	function submitbutton(pressbutton) {
		if (pressbutton == 'cancel') {
			submitform( pressbutton );
			return false;
		}
		if (pressbutton=='addMulti') {
			YAHOO.DC.MultiCalendar.showAddEvent('<?php echo $id?>','cal<?php echo $id?>Admin',-1);
		}
		if (pressbutton=='listMulti') {
			YAHOO.DC.MultiCalendar.showEventlist('<?php echo $id?>','cal<?php echo $id?>Admin',1);
		}
		return false;
	}
	<?php echo $arrayJS_list;?>
	
</script>
<?php if (JC_JQUERY_MV) {?>
<script language='JavaScript' type='text/javascript' src='../components/com_multicalendar/DC_MultiViewCal/js/jquery-1.7.2.min.js'></script>
<script language='JavaScript' type='text/javascript' src='../components/com_multicalendar/DC_MultiViewCal/js/jquery-ui-1.8.20.custom.min.js'></script>
<?php 
}
else
    for ($i=0;$i<count($JC_JQUERY_SPECIAL);$i++)
      echo "<script language='JavaScript' type='text/javascript' src='".$JC_JQUERY_SPECIAL[$i]."'></script>";
?>
<script language='JavaScript' type='text/javascript' src='../components/com_multicalendar/DC_MultiViewCal/src/Plugins/underscore.js'></script>
<script language='JavaScript' type='text/javascript' src='../components/com_multicalendar/DC_MultiViewCal/src/Plugins/rrule.js'></script>
<script language='JavaScript' type='text/javascript' src='../components/com_multicalendar/DC_MultiViewCal/src/Plugins/Common.js'></script>
<?php if (file_exists("../components/com_multicalendar/DC_MultiViewCal/language/multiview_lang_".$mainframe->getCfg('language').".js")){?>
    <script language='JavaScript' type='text/javascript' src='../components/com_multicalendar/DC_MultiViewCal/language/multiview_lang_<?php echo $mainframe->getCfg('language');?>.js'></script>
<?php }else{ ?>
    <script language='JavaScript' type='text/javascript' src='../components/com_multicalendar/DC_MultiViewCal/language/multiview_lang_en-GB.js'></script>
<?php }?>
<script language='JavaScript' type='text/javascript' src='../components/com_multicalendar/DC_MultiViewCal/src/Plugins/jquery.calendar.js'></script>
<script language='JavaScript' type='text/javascript' src='../components/com_multicalendar/DC_MultiViewCal/src/Plugins/jquery.alert.js'></script>
<script language='JavaScript' type='text/javascript' src='../components/com_multicalendar/DC_MultiViewCal/src/Plugins/multiview.js'></script>

<?php
if (file_exists("../components/com_multicalendar/DC_MultiViewCal/css/".$admin["cssStyle"]."/calendar.css")){
?>
<link rel="stylesheet" href="../components/com_multicalendar/DC_MultiViewCal/css/<?php echo $admin["cssStyle"]?>/calendar.css" type="text/css" />
<?php }else{ ?>
<link rel="stylesheet" href="../components/com_multicalendar/DC_MultiViewCal/css/cupertino/calendar.css" type="text/css" />
<?php }?>
<link rel="stylesheet" href="../components/com_multicalendar/DC_MultiViewCal/css/main.css" type="text/css" />




<?php



?>
<div id="calendar_filters"  style="clear: both;height: 80px; width: 100%;">
    
        
    <form id="calendar_filter_form">
    <div class='filter-select fltrt'>
        <?php echo $helper->generateSelect($helper->getBusinessProfileList(), 'filter_business_profile_id', 'business_profile_id','' , 'Business Name', false, "inputbox"); ?>
    </div>

    <div   style="float:left;" >
        <select multiple size="6" id="filter_client" name="client_id[]" class="inputbox">
                <option value=""><?php echo JText::_('-Select Clients-');?></option>
        </select>
    </div>


    <div  style="float:left;margin-left: 10px;">
        <select multiple size="6" id="filter_trainer" name="trainer_id[]" class="inputbox" >
          <option value=""><?php echo JText::_('-Select Trainers-');?></option>
        </select>
    </div>
    <?php
 
    
    $db = JFactory::getDbo();
    $sql = "SELECT name FROM #__fitness_locations WHERE state='1'";
    $db->setQuery($sql);
    if(!$db->query()) {
        JError::raiseError($db->getErrorMsg());
    }
    $locations = $db->loadObjectList();

    ?>

    <div  style="float:left;margin-left: 10px;">
        <select multiple size="6" id="filter_location" name="location[]" class="inputbox" >
                <option value=""><?php echo JText::_('-Select Locations-');?></option>
                <?php 
                    foreach ($locations as $location) {
                        echo '<option value="' . $location->name . '">' . $location->name . '</option>';
                    }
                ?>
        </select>
    </div>
        
                
    <?php
    $db = JFactory::getDbo();
    $sql = "SELECT id, name, color FROM #__fitness_categories WHERE state='1'";
    $db->setQuery($sql);
    if(!$db->query()) {
        JError::raiseError($db->getErrorMsg());
    }
    $appointments = $db->loadObjectList();

    ?>

    <div  style="float:left;margin-left: 10px;">
        <select multiple size="6" id="filter_appointment" name="appointment[]" class="inputbox" >
                <option value=""><?php echo JText::_('-Select Appointments-');?></option>
                <?php 
                    foreach ($appointments as $appointment) {
                        echo '<option value="' . $appointment->name . '">' . $appointment->name . '</option>';
                    }
                ?>
        </select>
    </div>
        
    <?php
    $db = JFactory::getDbo();
    $sql = "SELECT DISTINCT name FROM #__fitness_session_type WHERE state='1'";
    $db->setQuery($sql);
    if(!$db->query()) {
        JError::raiseError($db->getErrorMsg());
    }
    $session_types = $db->loadObjectList();

    ?>

    <div style="float:left;margin-left: 10px;">
        <select multiple size="6" id="filter_session_type" name="session_type[]" class="inputbox" >
                <option value=""><?php echo JText::_('-Select Session Types-');?></option>
                <?php 
                    foreach ($session_types as $session_type) {
                        echo '<option value="' . $session_type->name . '">' . $session_type->name . '</option>';
                    }
                ?>
        </select>
    </div>
        
    <?php
    $db = JFactory::getDbo();
    $sql = "SELECT DISTINCT name FROM #__fitness_session_focus WHERE state='1'";
    $db->setQuery($sql);
    if(!$db->query()) {
        JError::raiseError($db->getErrorMsg());
    }
    $session_focuses = $db->loadObjectList();

    ?>

    <div style="float:left;margin-left: 10px;">
        <select multiple size="6" id="filter_session_focus" name="session_focus[]" class="inputbox" >
                <option value=""><?php echo JText::_('-Select Session Focuses-');?></option>
                <?php 
                    foreach ($session_focuses as $session_focus) {
                        echo '<option value="' . $session_focus->name . '">' . $session_focus->name . '</option>';
                    }
                ?>
        </select>
    </div>

    <input style="margin-left: 20px;" type="button" value="Go" name="find_filtered" id="find_filtered"/>
    <input style="margin-left: 20px;" type="button" value="Reset" name="freset_filtered" id="reset_filtered"/>
    </form>
    <div style="float: left;margin-left: 21px;margin-top: 20px;font-size: 12px;">
        <input type="checkbox" id="remember_drag" name="remember_drag" value="1" /> 
        Remember Drag options
    </div>
    <div style="float: left;margin-left: 21px;margin-top: 20px;font-size: 12px;">
        <input type="checkbox" id="delete_event" name="delete_event" value="1" /> 
        Enable events delete on click
    </div>
</div>
<table border="0">
    <tbody>
        <tr>
            <td>
                <div id="cal<?php echo $id?>" style="width:918px;" class="multicalendar"></div>
            </td>
            <td  style="vertical-align:top;">
                <table border="0">
                    <tbody>
                        <tr>
                            <td>
                                
                                <div class="drag_area">
                                    <h4 >1. Add Appointment to calendar</h4>
                                    
                                    <ul>
                                    <?php 
                                        foreach ($appointments as $appointment) {
                                            echo '<li data-name="title" data-value="' . $appointment->id . '" class="drag_data" title="' . $appointment->name . '" 
                                                  style="background-color:' .  $appointment->color . '">' . $appointment->name . '</li>';
                                        }
                                    
                                    ?>
                                    </ul>

                                </div>
                            </td>
                            
                            <td>
                                
                                <div class="drag_area">
                                    <h4 >2. Add Client to Appointment</h4>
                                    <ul id="clients_ul">
                                    
                                    </ul>

                                </div>
                            </td>
                            <td>
                                  <div class="drag_area">
                                    <h4 >3. Add Trainer to Appointment</h4>
                                    <ul id="trainers_ul">
                                    </ul>

                                </div>

                            </td>
                        </tr>
                        <tr>
                             <td>
                                
                                <div class="drag_area">
                                    <h4 >4. Add Location to Appointment</h4>
                                    <ul>
                                    <?php 
                                        foreach ($locations as $location) {
                                            echo '<li data-name="location" data-value="' . trim($location->name) . '" class="drag_data" title="' . $location->name   . '" ">' 
                                                 . $location->name . '</li>';
                                        }
                                    
                                    ?>
                                    </ul>

                                </div>
                            </td>
                            <td >
                                
                                <div style="margin-left:5px;width: 200px;" class="drag_area send_reminder_emails">
                                    <form id="send_reminder_form">
                                        <h4 >5. Send Email Confirmations</h4>
                                        <div style="margin-bottom:5px;">Select Appointment Type(s)</div>
                                        <div  style="height: 243px;">
                                            <select style="font-size: 14px; font-weight: bold;  width: 200px;" multiple size="9"  name="appointments[]" class="inputbox" >
                                                <?php
                                                foreach ($appointments as $appointment) {
                                                    echo '<option value="' . $appointment->name . '">' . $appointment->name . '</option>';
                                                }
                                                ?>
                                            </select>
                                            <table>
                                                <tr>
                                                    <td>
                                                        Select Date Range
                                                        </br>
                                                        <?php 
                                                        echo "From: " . JHTML::calendar('','reminder_from','reminder_from','%Y-%m-%d', array('readonly'=>'true'));
                                                        echo "<br/>";
                                                        echo "To: " . JHTML::calendar('','reminder_to','reminder_to','%Y-%m-%d', array('readonly'=>'true'));
                                                        ?>                                                       
                                                    </td>
                                                    <td>
                                                       <input id="send_emails_button" type="button" name="send_emails_button" value="Send" > 
                                                    </td>
                                                </tr>
                                            </table>
                                            <div id="emais_sended"></div>
                                        </div>
                                    </form>
                                </div>
                            </td>
                       
                        </tr>
                        
   

                    </tbody>
                </table>

            </td>
        </tr>
    </tbody>
</table>


<script type="text/javascript">
var pathCalendarRootPic = "<?php echo JURI::root();?>";
initMultiViewCal("cal<?php echo $id?>",<?php echo $id?>,
{viewDay:<?php echo (in_array("viewDay",$admin["views"]))?"true":"false"?>,
viewWeek:<?php echo (in_array("viewWeek",$admin["views"]))?"true":"false"?>,
viewMonth:<?php echo (in_array("viewMonth",$admin["views"]))?"true":"false"?>,
viewNMonth:<?php echo (in_array("viewNMonth",$admin["views"]))?"true":"false"?>,
viewdefault:"<?php echo $admin["viewdefault"]?>",
numberOfMonths:<?php echo $admin["numberOfMonths"]?>,
showtooltip:<?php echo ($admin["sample0"]=="1")?"true":"false"?>,
tooltipon:<?php echo ($admin["sample1"]!="mouseover")?"1":"0"?>,
shownavigate:<?php echo ($admin["sample2"]=="1")?"true":"false"?>,
url:"<?php echo $admin["sample3"]?>",
target:<?php echo ($admin["sample4"]!="new_window")?"1":"0"?>,
start_weekday:<?php echo $admin["start_weekday"]?>,
language:"<?php echo $mainframe->getCfg('language');?>",
cssStyle:"<?php echo $admin["cssStyle"]?>",
edition:true,
btoday:<?php echo ($admin["btoday"]=="1")?"true":"false"?>,
bnavigation:<?php echo ($admin["bnavigation"]=="1")?"true":"false"?>,
brefresh:<?php echo ($admin["brefresh"]=="1")?"true":"false"?>,
bnew:true,
path:"<?php echo  JURI::root() . 'index.php?cid=' . JFactory::getUser()->id . '&'?>",
userAdd:true,
            userEdit:true,
            userDel:true,
            userEditOwner:true,
            userDelOwner:true,
            userOwner:-1 <?php echo $newp;?>});



//npkorban
    (function($) {
        
        // connect helper class
        var helper_options = {
            'ajax_call_url' : '<?php echo JURI::root();?>administrator/index.php?option=com_fitness&tmpl=component&<?php echo JSession::getFormToken(); ?>=1',
        }
        var fitness_helper = $.fitness_helper(helper_options);
        
                   
        $("#business_profile_id").on('change', function() {
            
            var business_profile_id = $(this).val();
            businessLogic(business_profile_id);
            
        });
        
        function businessLogic(business_profile_id) {
            if(!business_profile_id) {
                populateDragUL({}, $("#clients_ul"), 'client_id');
                populateDragUL({}, $("#trainers_ul"), 'trainer_id');
                fitness_helper.populateSelect({}, '#filter_client', '');
                fitness_helper.populateSelect({}, '#filter_trainer', '');
                return;
            }
            // populate clients select
            fitness_helper.populateClientsSelectOnBusiness('getClientsByBusiness', 'goals', business_profile_id, '#filter_client', '');
            
            fitness_helper.on('change:clients', function(model, items) {
                populateDragUL(items, $("#clients_ul"), 'client_id');
            });
            
            
            // populate trainers select
            fitness_helper.populateTrainersSelectOnBusiness('user_group', business_profile_id, '#filter_trainer', '');
            
            fitness_helper.on('change:trainers', function(model, items) {
                populateDragUL(items, $("#trainers_ul"), 'trainer_id');
            });
        }
        
        
        function populateDragUL(data, target, data_name) {
            var html = '';
            $.each(data, function(index, value) {
                if(index) {
                    html += '<li data-name="'  +data_name + '" data-value="' + index + '" class="drag_data" title="' + value + '" >' + value + '</li>'
                }
            });
            $(target).html(html);
            return html;
        }
        
        var business_profile_id = '<?php echo $business_profile_id;?>';
        
        if(business_profile_id) {
            $("#business_profile_id").val(business_profile_id);
            businessLogic(business_profile_id);
        }
            
    })($js);

</script>

<form action="index.php?option=com_multicalendar" method="post" name="adminForm" id="adminForm">
    <input type="hidden" name="task" value="" />
	<input type="hidden" name="option" value="com_multicalendar" />
	<input type="hidden" name="id" value="<?php echo $this->calendar->id; ?>" />
	<input type="hidden" name="cid[]" value="<?php echo $this->calendar->id; ?>" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>
