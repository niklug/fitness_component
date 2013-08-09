<?php
/**
 * @version     1.0.0
 * @package     com_fitness
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Nikolay Korban <niklug@ukr.net> - http://
 */


// no direct access
defined('_JEXEC') or die;

JHtml::_('behavior.tooltip');
JHTML::_('script','system/multiselect.js',false,true);
// Import CSS
$document = JFactory::getDocument();
$document->addStyleSheet('components/com_fitness/assets/css/fitness.css');

$user	= JFactory::getUser();
$userId	= $user->get('id');
$listOrder	= $this->state->get('list.ordering');
$listDirn	= $this->state->get('list.direction');
$canOrder	= $user->authorise('core.edit.state', 'com_fitness');
$saveOrder	= $listOrder == 'a.ordering';

// GRAPH
require_once JPATH_COMPONENT_ADMINISTRATOR . DS . 'views' . DS. 'goals' . DS . 'tmpl' . DS .  'default_graph.php';

?>

<form action="<?php echo JRoute::_('index.php?option=com_fitness&view=assessments'); ?>" method="post" name="adminForm" id="adminForm">
	<fieldset id="filter-bar">
		<div class="filter-search fltlft">
			<label class="filter-search-lbl" for="filter_search"><?php echo JText::_('Client Name:'); ?></label>
			<input type="text" name="filter_search" id="filter_search" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" title="<?php echo JText::_('Search'); ?>" />
			<button type="submit"><?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?></button>
			<button id="reset_filtered" type="button"><?php echo JText::_('Reset All'); ?></button>
		</div>
		
            <div class='filter-select fltrt'>
                        <?php
                        $selected_from_date = JRequest::getVar('filter_from_date');
			$selected_to_date = JRequest::getVar('filter_to_date');
                        ?>
                        <label class="filter-search-lbl" for="filter_search"><?php echo JText::_('Date from:'); ?></label>
                        <?php
				echo JHtml::_('calendar', $selected_from_date, 'filter_from_date', 'filter_from_deadline', '%Y-%m-%d', 'onchange="this.form.submit();"');
                        ?>
                        <label class="filter-search-lbl" for="filter_search"><?php echo JText::_('Date to:'); ?></label>
                        <?php
				echo JHtml::_('calendar', $selected_to_date, 'filter_to_date', 'filter_to_deadline', '%Y-%m-%d',  'onchange="this.form.submit();"');
			?>
                        
            </div>
        </fieldset>
        <fieldset style="border:none;">
                        
            	<div class='filter-select fltrt'>
			<select name="filter_published" class="inputbox" onchange="this.form.submit()">
				<option value=""><?php echo JText::_('-Published-');?></option>
				<?php echo JHtml::_('select.options', JHtml::_('jgrid.publishedOptions'), "value", "text", $this->state->get('filter.published'), true);?>
			</select>
		</div>
            
                <?php
                $workout_status[] = JHTML::_('select.option', '1', 'Published' );
                $workout_status[] = JHTML::_('select.option', '0', 'Unpublished' );

                ?>
                <div class='filter-select fltrt'>
                        <select name="filter_frontend_published" class="inputbox" onchange="this.form.submit()">
                                <option value=""><?php echo JText::_('-Assessment Published-');?></option>
                                <?php echo JHtml::_('select.options', $workout_status, "value", "text", $this->state->get('filter.frontend_published'), true);?>
                        </select>
                </div>
            
            
                <?php
                $event_status[] = JHTML::_('select.option', '1', 'pending' );
                $event_status[] = JHTML::_('select.option', '2', 'attended' );
                $event_status[] = JHTML::_('select.option', '3', 'cancelled' );
                $event_status[] = JHTML::_('select.option', '4', 'late cancel' );
                $event_status[] = JHTML::_('select.option', '5', 'no show' );
                ?>
                <div class='filter-select fltrt'>
                        <select name="filter_event_status" class="inputbox" onchange="this.form.submit()">
                                <option value=""><?php echo JText::_('-Status-');?></option>
                                <?php echo JHtml::_('select.options', $event_status, "value", "text", $this->state->get('filter.event_status'), true);?>
                        </select>
                </div>

                <?php
                $db = JFactory::getDbo();
                $sql = "SELECT id,  name FROM #__fitness_session_focus WHERE category_id='5' AND state='1' GROUP BY name ";
                $db->setQuery($sql);
                if(!$db->query()) {
                    JError::raiseError($db->getErrorMsg());
                }
                $session_focus= $db->loadObjectList();
                ?>

                <div class='filter-select fltrt'>
                        <select name="filter_session_focus" class="inputbox" onchange="this.form.submit()">
                                <option value=""><?php echo JText::_('-Assessment focus-');?></option>
                                <?php echo JHtml::_('select.options', $session_focus, "name", "name", $this->state->get('filter.session_focus'), true);?>
                        </select>
                </div>

                <?php
                $db = JFactory::getDbo();
                $sql = "SELECT id,  name FROM #__fitness_session_type WHERE category_id='5' AND state='1' GROUP BY name";
                $db->setQuery($sql);
                if(!$db->query()) {
                    JError::raiseError($db->getErrorMsg());
                }
                $session_type= $db->loadObjectList();
                ?>

                <div class='filter-select fltrt'>
                        <select name="filter_session_type" class="inputbox" onchange="this.form.submit()">
                                <option value=""><?php echo JText::_('-Assessment type-');?></option>
                                <?php echo JHtml::_('select.options', $session_type, "name", "name", $this->state->get('filter.session_type'), true);?>
                        </select>
                </div>

                <?php
                // Location filter
                $db = JFactory::getDbo();

                $sql = "SELECT id AS value, name AS text FROM #__fitness_locations WHERE state='1'";
                $db->setQuery($sql);
                if(!$db->query()) {
                    JError::raiseError($db->getErrorMsg());
                }
                $locations = $db->loadObjectList();
                if(isset($locations)) {
                    foreach ($locations as $option) {
                        $locations_name[] = JHTML::_('select.option', trim($option->text), trim($option->text) );
                    }
                }
                ?>

                <div class='filter-select fltrt'>
                        <select name="filter_location" class="inputbox" onchange="this.form.submit()">
                                <option value="0"><?php echo JText::_('-Location-');?></option>
                                <?php echo JHtml::_('select.options', $locations_name, "text", "text", $this->state->get('filter.location'), true);?>
                        </select>
                </div>



                <?php
                // primary trainer filter
                $db = JFactory::getDbo();

                $sql = "SELECT id AS value, username AS text FROM #__users INNER JOIN #__user_usergroup_map ON #__user_usergroup_map.user_id=#__users.id WHERE #__user_usergroup_map.group_id=(SELECT id FROM #__usergroups WHERE title='Trainers')";
                $db->setQuery($sql);
                if(!$db->query()) {
                    JError::raiseError($db->getErrorMsg());
                }
                $primary_trainerlist = $db->loadObjectList();
                if(isset($primary_trainerlist)) {
                    foreach ($primary_trainerlist as $option) {
                        $primary_trainer[] = JHTML::_('select.option', $option->value, $option->text );
                    }
                }

                ?>

                <div class='filter-select fltrt'>
                        <select name="filter_primary_trainer" class="inputbox" onchange="this.form.submit()">
                                <option value=""><?php echo JText::_('-Primary Trainer-');?></option>
                                <?php echo JHtml::_('select.options', $primary_trainer, "value", "text", $this->state->get('filter.primary_trainer'), true);?>
                        </select>
                </div>
            
                <a id="add_appointment" href="javascript:void(0)"></a>


	</fieldset>
	<div class="clr"> </div>

	<table class="adminlist">
		<thead>
			<tr>
				<th width="1%">
					<input type="checkbox" name="checkall-toggle" value="" onclick="checkAll(this)" />
				</th>
                                <th>
                                    Edit/View
                                </th>

				<th class='left'>
				<?php echo JHtml::_('grid.sort',  'COM_FITNESS_PROGRAMS_STARTTIME', 'a.starttime', $listDirn, $listOrder); ?>
				</th>
				<th width="5%" class='left'>
				<?php echo JHtml::_('grid.sort',  'Client' . ' (Sent/Confirmed)', 'a.client_id', $listDirn, $listOrder); ?>
				</th>
				<th class='left'>
				<?php echo JHtml::_('grid.sort',  'COM_FITNESS_PROGRAMS_TRAINER_ID', 'a.trainer_id', $listDirn, $listOrder); ?>
				</th>
				<th class='left'>
				<?php echo JHtml::_('grid.sort',  'COM_FITNESS_PROGRAMS_LOCATION', 'a.location', $listDirn, $listOrder); ?>
				</th>
				<th class='left'>
				<?php echo JHtml::_('grid.sort',  'Assessment Type', 'a.session_type', $listDirn, $listOrder); ?>
				</th>
				<th class='left'>
				<?php echo JHtml::_('grid.sort',  'Assessment Focus', 'a.session_focus', $listDirn, $listOrder); ?>
				</th>
                                <th class='left'>
                                    Height
				</th>
                                <th class='left'>
                                    Weight
				</th>
                                <th class='left'>
                                    Age
				</th>
                                <th class='left'>
                                    Body Fat
				</th>
                                <th class='left'>
                                    Lean Mass
				</th>
				<th class='left'>
				<?php echo JHtml::_('grid.sort',  'COM_FITNESS_PROGRAMS_STATUS', 'a.status', $listDirn, $listOrder); ?>
				</th>
                                <th class="nowrap">
                                    Appointment
                                </th>
                                <th class="nowrap">
                                    Notify
                                </th>
				<th class='left'>
				<?php echo JHtml::_('grid.sort',  'Publish Assessment', 'a.frontend_published', $listDirn, $listOrder); ?>
				</th>
		
                <?php if (isset($this->items[0]->published)) { ?>
				<th>
					<?php echo JHtml::_('grid.sort',  'COM_FITNESS_PROGRAMS_PUBLISHED', 'a.published', $listDirn, $listOrder); ?>
				</th>
                <?php } ?>
                <?php if (isset($this->items[0]->ordering)) { ?>
				<th width="10%">
					<?php echo JHtml::_('grid.sort',  'JGRID_HEADING_ORDERING', 'a.ordering', $listDirn, $listOrder); ?>
					<?php if ($canOrder && $saveOrder) :?>
						<?php echo JHtml::_('grid.order',  $this->items, 'filesave.png', 'assessments.saveorder'); ?>
					<?php endif; ?>
				</th>
                <?php } ?>
                <?php if (isset($this->items[0]->id)) { ?>
                <th width="1%" class="nowrap">
                    <?php echo JHtml::_('grid.sort',  'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?>
                </th>
                <?php } ?>
			</tr>
		</thead>
		<tfoot>
			<?php 
                if(isset($this->items[0])){
                    $colspan = count(get_object_vars($this->items[0]));
                }
                else{
                    $colspan = 10;
                }
            ?>
			<tr>
				<td colspan="<?php echo $colspan ?>">
					<?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
		<tbody>
		<?php foreach ($this->items as $i => $item) :
			$ordering	= ($listOrder == 'a.ordering');
			$canCreate	= $user->authorise('core.create',		'com_fitness');
			$canEdit	= $user->authorise('core.edit',			'com_fitness');
			$canCheckin	= $user->authorise('core.manage',		'com_fitness');
			$canChange	= $user->authorise('core.edit.state',	'com_fitness');
			?>
			<tr class="row<?php echo $i % 2; ?>">
				<td class="center">
					<?php echo JHtml::_('grid.id', $i, $item->id); ?>
				</td>
                                <td>
                                    <a class="edit_event" data-id="<?php echo $item->id?>" href="javascript:void(0)">Edit/View</a>
                                </td>
				<td>
					<?php echo $item->starttime; ?>
				</td>
				<td>
					<?php
                                        echo $this->getGroupClientsData($item->id, $item->client_id);
                                        ?>
				</td>
				<td>
					<?php echo JFactory::getUser($item->trainer_id)->name; ?>
				</td>
				<td>
					<?php echo $this->escape($item->location); ?>
				</td>
				<td>
					<?php echo $item->session_type; ?>
				</td>
				<td>
					<?php echo $item->session_focus; ?>
				</td>
                                <td>
					<?php echo $item->as_height; ?>
				</td>
                                <td>
					<?php echo $item->as_weight; ?>
				</td>
                                <td>
					<?php echo $item->as_age; ?>
				</td>
                                                               
                                <td>
					<?php echo $item->as_body_fat; ?>
				</td>
                                <td>
					<?php echo $item->as_lean_mass; ?>
				</td>
                                
				<td id="status_button_<?php echo $item->id ?>" class="center">
                                    
					<?php echo $this->state_html($item->id, $item->status); ?>
				</td>
                                <td class="center">
                                    <a onclick="sendEmail('<?php echo $item->id ?>', 'Appointment')" class="send_email_button"></a>
                                </td>	
                                <td class="center">
                                   <a onclick="sendEmail('<?php echo $item->id ?>', 'NotifyAssessment')" class="send_email_button"></a>
                                </td>	
				<td>
                                    <?php $frontend_published =  $item->frontend_published; ?>
                                    <a id="frontend_published_<?php echo $item->id; ?>"  style="cursor:pointer;"  class="jgrid" title="Unpublish Item" >
                                        <span onclick="setFrontendPublished('<?php echo $item->id; ?>', '<?php echo $frontend_published; ?>')" 
                                              class="state <?php echo  $frontend_published ? 'publish' : 'unpublish'?>"></span>
                                    </a>
				</td>


                <?php if (isset($this->items[0]->published)) { ?>
				    <td class="center">
					    <?php echo JHtml::_('jgrid.published', $item->published, $i, 'assessments.',true);?>
				    </td>
                <?php } ?>
                <?php if (isset($this->items[0]->ordering)) { ?>
				    <td class="order">
					    <?php if ($canChange) : ?>
						    <?php if ($saveOrder) :?>
							    <?php if ($listDirn == 'asc') : ?>
								    <span><?php echo $this->pagination->orderUpIcon($i, true, 'assessments.orderup', 'JLIB_HTML_MOVE_UP', $ordering); ?></span>
								    <span><?php echo $this->pagination->orderDownIcon($i, $this->pagination->total, true, 'assessments.orderdown', 'JLIB_HTML_MOVE_DOWN', $ordering); ?></span>
							    <?php elseif ($listDirn == 'desc') : ?>
								    <span><?php echo $this->pagination->orderUpIcon($i, true, 'assessments.orderdown', 'JLIB_HTML_MOVE_UP', $ordering); ?></span>
								    <span><?php echo $this->pagination->orderDownIcon($i, $this->pagination->total, true, 'assessments.orderup', 'JLIB_HTML_MOVE_DOWN', $ordering); ?></span>
							    <?php endif; ?>
						    <?php endif; ?>
						    <?php $disabled = $saveOrder ?  '' : 'disabled="disabled"'; ?>
						    <input type="text" name="order[]" size="5" value="<?php echo $item->ordering;?>" <?php echo $disabled ?> class="text-area-order" />
					    <?php else : ?>
						    <?php echo $item->ordering; ?>
					    <?php endif; ?>
				    </td>
                <?php } ?>
                <?php if (isset($this->items[0]->id)) { ?>
				<td class="center">
					<?php echo (int) $item->id; ?>
				</td>
                <?php } ?>
			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>

	<div>
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>

<div class="event_status_wrapper">
  <img class="hideimage " src="<?php echo JUri::base() ?>components/com_fitness/assets/images/close.png" alt="close" title="close"  onclick="hide_status_wrapper()">
      <a data-status="1" class="event_status__button event_status_pending set_status" href="javascript:void(0)">pending</a>
      <a data-status="2" class="event_status__button event_status_attended set_status" href="javascript:void(0)">attended</a>
      <a data-status="3" class="event_status__button event_status_cancelled set_status" href="javascript:void(0)">cancelled</a>
      <a data-status="4" class="event_status__button event_status_latecancel set_status" href="javascript:void(0)">late cancel</a>
      <a data-status="5" class="event_status__button event_status_noshow set_status" href="javascript:void(0)">no show</a>
      <input type="checkbox" class="send_appointment_email" name="send_appointment_email" value="1"> <span style="font-size:12px;">Send email</span>
</div>

<div id="emais_sended"></div>

<div class="ui-dialog ui-widget ui-widget-content ui-corner-all ui-draggable ui-resizable mv_dlg mv_dlg_editevent"
      role="dialog" aria-labelledby="ui-dialog-title-editEvent">
    
    <a class="ui-dialog-titlebar-close ui-corner-all" href="#" role="button">
        <span class="ui-icon ui-icon-closethick"></span>
    </a>

    <div id="editEvent" class="ui-dialog-content ui-widget-content" style="  background-color: #F4F4F4;  overflow-y: auto;width: auto; min-height: 0px; height: 787px;" scrolltop="0" scrollleft="0"></div>
</div>

<script type="text/javascript">
    $(document).ready(function(){

         $(".set_status").bind('click', function(e) {
            var event_status = $(this).data('status');
            eventSetStatus(event_status, event_id);
        });

        $(".edit_event").bind('click', function(e) {
            var event_id = $(this).data('id');
            var url = '<?php echo JURI::root()?>index.php?option=com_multicalendar&month_index=0&task=editevent&delete=1&palette=0&paletteDefault=F00&calid=0&mt=true&css=cupertino&lang=en-GB&id=' + event_id;
            loadAppointmentHtml(event_id, url);
        });

        $(".ui-icon-closethick").bind('click', function(e) {
            closeEditForm();
        });

        $("#add_appointment").bind('click', function(e) {
            var url = '<?php echo JURI::root()?>index.php?option=com_multicalendar&month_index=0&task=editevent&delete=1&palette=0&paletteDefault=F00&calid=0&mt=true&css=cupertino&lang=en-GB';
            loadAppointmentHtml('', url);
        });

        $("#reset_filtered").click(function(){
            var form = $("#adminForm");
            form.find("select").val('');
            form.find("input").val('');
            form.submit();
        });


    });
 
    


    function setFrontendPublished(event_id, status) {
        var url = '<?php echo JUri::base() ?>index.php?option=com_fitness&tmpl=component&<?php echo JSession::getFormToken(); ?>=1'
        $.ajax({
            type : "POST",
            url : url,
            data : {
               view : 'goals',
               format : 'text',
               task : 'setFrontendPublished',
               event_id : event_id,
               status : status
            },
            dataType : 'json',
            success : function(response) {
                if(!response.status.success) {
                    alert(response.status.message);
                    return;
                }
                var event_id = response.data.event_id;
                var status = response.data.status;
                var href = $("#frontend_published_" + event_id);
                if(status == '1') {
                    href.html('<span id="frontend_published_"' + event_id + ' class="state publish" onclick="setFrontendPublished(' + event_id + ', 1)"></span>');
                    
                } else {
                    href.html('<span id="frontend_published_"' + event_id + ' class="state unpublish" onclick="setFrontendPublished(' + event_id + ', 0)"></span>');
                }

            },
            error: function(XMLHttpRequest, textStatus, errorThrown)
            {
                alert("error");
            }
        });
    }
    
    
    
    function openSetBox(id, status) {
         event_id = id;
         $(".event_status_wrapper").show();
         $(".event_status__button").show();
         if(status == 1)  $(".event_status_wrapper .event_status_pending").hide();
         if(status == 2)  $(".event_status_wrapper .event_status_attended").hide();
         if(status == 3)  $(".event_status_wrapper .event_status_cancelled ").hide();
         if(status == 4)  $(".event_status_wrapper .event_status_latecancel").hide();
         if(status == 5)  $(".event_status_wrapper .event_status_noshow").hide();

    }
    
    function hide_status_wrapper() {
        $(".event_status_wrapper").hide();
    }
    
    
    function eventSetStatus(event_status, event_id){
        var url = '<?php echo JURI::root()?>index.php?option=com_multicalendar&task=load&calid=0&method=set_event_status';
        $.ajax({
                type : "POST",
                url : url,
                data : {
                    event_id : event_id,
                    event_status : event_status
                },
                dataType : 'text',
                success : function(event_status) {
                    hide_status_wrapper();
                    $("#status_button_" + event_id).html(event_status_html(event_status, event_id));
                    appointmentEmailLogic(event_id, event_status, 'personal');
                },
                error: function(XMLHttpRequest, textStatus, errorThrown)
                {
                    alert("error");
                }
        });

    }
    
    
        
    function appointmentEmailLogic(event_id, event_status, appointment_client_id){
        var send_appointment_email = $(".send_appointment_email").is(':checked');
        var method;
        switch(event_status) {
            case '1' :
                return;
                break;
            case '2' :
                method = 'AppointmentAttended';
                break;
            case '3' :
               method = 'AppointmentCancelled';
               break;
            case '4' :
               method = 'AppointmentLatecancel';
               break;
            case '5' :
               method = 'AppointmentNoshow';
               break;
            default : 
                return;
                break;
        }
        if(send_appointment_email) {
            sendAppointmentStatusEmail(event_id, method, appointment_client_id);
        }
    }
    
    
    function sendAppointmentStatusEmail(event_id, method, appointment_client_id) {
        var url = '<?php echo JURI::root()?>index.php?option=com_multicalendar&task=load&calid=0&method=send' + method + 'Email';
        $.ajax({
            type : "POST",
            url : url,
            data : {
               event_id : event_id,
               appointment_client_id : appointment_client_id
            },
            dataType : 'json',
            success : function(response) {
                //console.log(response);
                if(response.IsSuccess != true) {
                    alert(response.Msg);
                    return;
                } 
                alert('Email sent');  
            },
            error: function(XMLHttpRequest, textStatus, errorThrown)
            {
                alert("error");
            }
        }); 

    }
    
    function event_status_html(event_status, event_id) {
         if(event_status == 1)  return '<a onclick="openSetBox(' + event_id +  ', ' + event_status + ')"  class="open_status event_status_pending event_status__button" href="javascript:void(0)">pending</a>';
         if(event_status == 2)  return '<a onclick="openSetBox(' + event_id +  ', ' + event_status + ')"  class="open_status event_status_attended event_status__button" href="javascript:void(0)">attended</a>';
         if(event_status == 3)  return '<a onclick="openSetBox(' + event_id +  ', ' + event_status + ')"   class="open_status event_status_cancelled event_status__button" href="javascript:void(0)">cancelled</a>';
         if(event_status == 4)  return '<a onclick="openSetBox(' + event_id +  ', ' + event_status + ')"   class="open_status event_status_latecancel event_status__button" href="javascript:void(0)">late cancel</a>';
         if(event_status == 5)  return '<a onclick="openSetBox(' + event_id +  ', ' + event_status + ')"  class="open_status event_status_noshow event_status__button" href="javascript:void(0)">no show</a>';

    }
    
    
    
    // appointment email
    function sendEmail(event_id, method) {
        var url = '<?php echo JURI::root()?>index.php?option=com_multicalendar&task=load&calid=0&method=send' + method + 'Email';
        $.ajax({
                type : "POST",
                url : url,
                data : {
                    event_id : event_id
                },
                dataType : 'json',
                success : function(response) {
                    if(response.IsSuccess) {
                        var emails = response.Msg.split(',');

                        var message = 'Emails were sent to: ' +  "</br>";
                        $.each(emails, function(index, email) { 
                            message += email +  "</br>";
                        });
                        $("#emais_sended").append(message);

                    } else {
                        alert(response.Msg);
                    }
                },
                error: function(XMLHttpRequest, textStatus, errorThrown)
                {
                    alert("error");
                }
        });
    }
    
 
    function loadAppointmentHtml(event_id, url) {
         $.ajax({
            type : "POST",
            url : url,
            dataType : 'html',
            success : function(content) {
                $(".mv_dlg_editevent").show();
                var height = 820;
                var iframe_start = '<iframe id="dailog_iframe_1305934814858" frameborder="0" style="overflow-y: auto;overflow-x: hidden;border:none;width:598px;height:'+(height-60)+'px" src="'+url+'" border="0" scrolling="auto">';
                var iframe_end = '</iframe>';
                updateAppointmentHtml(iframe_start + iframe_end);
            },
            error: function(XMLHttpRequest, textStatus, errorThrown)
            {
                alert("error");
            }
        });
    }
    
    function updateAppointmentHtml(html) {
        $("#editEvent").html(html);
    }
    
    function closeEditForm() {
        $(".ui-dialog").hide();
    }
    

    
</script>


      
