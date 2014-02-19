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

$user	= JFactory::getUser();
$userId	= $user->get('id');
$listOrder	= $this->state->get('list.ordering');
$listDirn	= $this->state->get('list.direction');
$canOrder	= $user->authorise('core.edit.state', 'com_fitness');
$saveOrder	= $listOrder == 'a.ordering';

// GRAPH
require_once JPATH_COMPONENT_ADMINISTRATOR . DS . 'views' . DS. 'goals' . DS . 'tmpl' . DS .  'default_graph.php';

require_once  JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_fitness' . DS .'helpers' . DS . 'fitness.php';

$helper = new FitnessHelper();

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

                <div class='filter-select fltrt'>
                    <?php echo $helper->generateSelect($helper->getTrainersByUsergroup(), 'filter_primary_trainer', 'primary_trainer', $this->state->get('filter.primary_trainer'), 'Primary Trainer', false, 'inputbox'); ?>
                </div>
            
                <div class='filter-select fltrt'>
                    <?php echo $helper->generateSelect($helper->getBusinessProfileList(), 'filter_business_profile_id', 'business_profile_id', $this->state->get('filter.business_profile_id') , 'Business Name', false, "inputbox"); ?>
		</div>
            
                <a id="add_appointment" title="Add New Item" href="javascript:void(0)"></a>


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
                    $colspan = 17;
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
                                
				<td id="status_button_place_<?php echo $item->id;?>">
                                        <?php echo $this->model->status_html($item->id, $item->status, 'status_button') ?>
                                </td>
                                <td class="center">
                                    <a data-id="<?php echo $item->id ?>"  class="send_email_button appointment_email" ></a>
                                </td>	
                                <td class="center">
                                   <a data-id="<?php echo $item->id ?>"  class="send_email_button notify_email"></a>
                                </td>	
				<td>
                                    <?php $frontend_published =  $item->frontend_published; ?>
                                    <a id="frontend_published_<?php echo $item->id; ?>"  style="cursor:pointer;"  class="jgrid" title="Unpublish Item" >
                                        <span data-id="<?php echo $item->id; ?>"  data-status="<?php echo $frontend_published; ?>"  
                                              class="frontend_published state <?php echo  $frontend_published ? 'publish' : 'unpublish'?>"></span>
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

<div id="emais_sended"></div>
<div class="event_status_wrapper"> </div>

<div class="ui-dialog ui-widget ui-widget-content ui-corner-all ui-draggable ui-resizable mv_dlg mv_dlg_editevent"
      role="dialog" aria-labelledby="ui-dialog-title-editEvent">
    
    <a class="ui-dialog-titlebar-close ui-corner-all" href="#" role="button">
        <span class="ui-icon ui-icon-closethick"></span>
    </a>

    <div id="editEvent" class="ui-dialog-content ui-widget-content" style="  background-color: #F4F4F4;  overflow-y: auto;width: auto; min-height: 0px; height: 787px;" scrolltop="0" scrollleft="0"></div>
</div>
<script type="text/javascript">
    (function($) {
        
        var user_id = '<?php echo JFactory::getUser()->id ?>';
        
        $("#business_profile_id").on('change', function() {
             var form = $("#adminForm");
             form.submit();
        })

        $(".edit_event").live('click', function(e) {
            var event_id = $(this).data('id');
            var url = '<?php echo JURI::root()?>index.php?option=com_multicalendar&month_index=0&task=editevent&delete=1&palette=0&paletteDefault=F00&calid=0&mt=true&css=cupertino&lang=en-GB&id=' + event_id + '&cid=' + user_id;
            loadAppointmentHtml(event_id, url);
        });

        $(".ui-icon-closethick").live('click', function(e) {
            closeEditForm();
        });

        $("#add_appointment").live('click', function(e) {
            var url = '<?php echo JURI::root()?>index.php?option=com_multicalendar&month_index=0&task=editevent&delete=1&palette=0&paletteDefault=F00&calid=0&mt=true&css=cupertino&lang=en-GB' + '&cid=' + user_id;
            loadAppointmentHtml('', url);
        });

        $("#reset_filtered").click(function(){
            var limit = $("#limit").val();
            var form = $("#adminForm");
            form.find("select").val('');
            form.find("input").val('');
            $("#limit").val(limit);
            form.submit();
        });

        $(".event_status_wrapper .hideimage").live('click', function(e) {
            hide_status_wrapper();
        });

        $(".frontend_published").live('click', function() {
            var event_id = $(this).attr('data-id');
            var status = $(this).attr('data-status');
            setFrontendPublished(event_id, status);
        });
        
        $("#primary_trainer").on('change', function() {
                 var form = $("#adminForm");
                 form.submit();
        })


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
                        href.html('<span data-id="' + event_id + '" data-status="1" id="frontend_published_"' + event_id + ' class="frontend_published state publish" ></span>');

                    } else {
                        href.html('<span data-id="' + event_id + '" data-status="0" id="frontend_published_"' + event_id + ' class="frontend_published state unpublish"></span>');
                    }

                },
                error: function(XMLHttpRequest, textStatus, errorThrown)
                {
                    alert("error setFrontendPublished");
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
        
        
        // status
        var status_options = {
            'fitness_administration_url' : '<?php echo JURI::root();?>administrator/index.php?option=com_fitness&tmpl=component&<?php echo JSession::getFormToken(); ?>=1',
            'calendar_frontend_url' : '<?php echo JURI::root()?>index.php?option=com_multicalendar&task=load&calid=0',
            'db_table' : '#__dc_mv_events',
            'status_button' : 'status_button',
            'status_button_dialog' : 'status_button_dialog',
            'dialog_status_wrapper' : 'dialog_status_wrapper',
            'dialog_status_template' : '#dialog_status_template',
            'status_button_template' : '#status_button_template',
            'status_button_place' : '#status_button_place_',
            'statuses' : {
                '1' : {'label' : 'PENDING', 'class' : 'event_status_pending', 'email_alias' : ''},
                '2' : {'label' : 'ATTENDED', 'class' : 'event_status_attended', 'email_alias' : 'AppointmentAttended'}, 
                '3' : {'label' : 'CANCELLED', 'class' : 'event_status_cancelled', 'email_alias' : 'AppointmentCancelled'},
                '4' : {'label' : 'LATE CANCEL', 'class' : 'event_status_latecancel', 'email_alias' : 'AppointmentLatecancel'},
                '5' : {'label' : 'NO SHOW', 'class' : 'event_status_noshow', 'email_alias' : 'AppointmentNoshow'}, 
            },
            'statuses2' : {
                '1' : {'label' : 'PENDING', 'class' : 'event_status_pending', 'email_alias' : ''},
                '3' : {'label' : 'CANCELLED', 'class' : 'event_status_cancelled', 'email_alias' : 'AppointmentCancelled'},
                '6' : {'label' : 'COMPLETE', 'class' : 'event_status_complete', 'email_alias' : ''}
            },
            'close_image' : '<?php echo JUri::root() ?>administrator/components/com_fitness/assets/images/close.png',
            'hide_image_class' : 'hideimage',
            'show_send_email' : true,
             setStatuses : function(item_id) {
                var appointment_title = $("#appointment_title_" + item_id).attr('data-appointment');
                if(appointment_title == 'Personal Training') return  this.statuses;
                return  this.statuses2;
            },
            'view' : 'Assessment'
        }
        
        var status = $.status(status_options);
        status.run();
        
        $(".appointment_email").on('click', function() {
            var item_id = $(this).attr('data-id');
            
            status.sendEmail(item_id, 'Appointment');
        });
        
        $(".notify_email").on('click', function() {
            var item_id = $(this).attr('data-id');
            status.sendEmail(item_id, 'Notify')
        });
        
        // Add the  functions to the top level of the jQuery object
        $.closeEditForm = function() {

            var constr = closeEditForm();

            return constr;
        };
        
        $.updateAppointmentHtml = function(html) {

            var constr = updateAppointmentHtml(html);

            return constr;
        };
        
        
    })($js);
    
    function closeEditForm() {
        $js.closeEditForm();
    }
    
    function updateAppointmentHtml(html) {
        $js.updateAppointmentHtml(html);
    }

    
</script>


     

      
