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
echo $this->loadTemplate('graph');?>
<!-- END GRAPH -->

<form action="<?php echo JRoute::_('index.php?option=com_fitness&view=goals'); ?>" method="post" name="adminForm" id="adminForm">
	<fieldset id="filter-bar">
		<div class="filter-search fltlft">
			<label class="filter-search-lbl" for="filter_search"><?php echo JText::_('Client Name: '); ?></label>
			<input type="text" name="filter_search" id="filter_search" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" title="<?php echo JText::_('Search'); ?>" />
			<button type="submit"><?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?></button>
			<button type="button" id="reset_filtered"><?php echo JText::_('Reset All'); ?></button>
		</div>
		
        
		<div class='filter-select fltrt'>
                    
      			<?php //Filter for the field start date
			$selected_from_start_date = JRequest::getVar('filter_from_start_date');
			$selected_to_start_date = JRequest::getVar('filter_to_start_date');
                        ?>
                        <label class="filter-search-lbl" for="filter_search"><?php echo JText::_('Start from:'); ?></label>
                        <?php
				echo JHtml::_('calendar', $selected_from_start_date, 'filter_from_start_date', 'filter_from_start_date', '%Y-%m-%d', 'onchange="this.form.submit();"');
                        ?>
                        <label class="filter-search-lbl" for="filter_search"><?php echo JText::_('Start to:'); ?></label>
                        <?php
				echo JHtml::_('calendar', $selected_to_start_date, 'filter_to_start_date', 'filter_to_start_date', '%Y-%m-%d',  'onchange="this.form.submit();"');
			?>              

                    
                    
			<?php //Filter for the field deadline
			$selected_from_deadline = JRequest::getVar('filter_from_deadline');
			$selected_to_deadline = JRequest::getVar('filter_to_deadline');
                        ?>
                        <label class="filter-search-lbl" for="filter_search"><?php echo JText::_('Deadline from:'); ?></label>
                        <?php
				echo JHtml::_('calendar', $selected_from_deadline, 'filter_from_deadline', 'filter_from_deadline', '%Y-%m-%d', 'onchange="this.form.submit();"');
                        ?>
                        <label class="filter-search-lbl" for="filter_search"><?php echo JText::_('Deadline to:'); ?></label>
                        <?php
				echo JHtml::_('calendar', $selected_to_deadline, 'filter_to_deadline', 'filter_to_deadline', '%Y-%m-%d',  'onchange="this.form.submit();"');
			?>


                        <?php //Filter for the created
                        $filter_created = JRequest::getVar('filter_created');
                              ?>
                        <label class="filter-search-lbl" for="filter_created"><?php echo JText::_('Created: '); ?></label>
                        <?php
                                echo JHtml::_('calendar', $filter_created, 'filter_created', 'filter_created', '%Y-%m-%d', 'onchange="this.form.submit();"');
                        ?>

                        <?php //Filter for the created
                        $filter_modified = JRequest::getVar('filter_modified');
                              ?>
                        <label class="filter-search-lbl" for="filter_modified"><?php echo JText::_('Modified: '); ?></label>
                        <?php
                                echo JHtml::_('calendar', $filter_modified, 'filter_modified', 'filter_modified', '%Y-%m-%d', 'onchange="this.form.submit();"');
                        ?>
            
		</div>
            </fieldset>
            <fieldset style="border:none;">

		<div class='filter-select fltrt'>
			<select name="filter_published" class="inputbox" onchange="this.form.submit()">
				<option value=""><?php echo JText::_('JOPTION_SELECT_PUBLISHED');?></option>
				<?php echo JHtml::_('select.options', JHtml::_('jgrid.publishedOptions'), "value", "text", $this->state->get('filter.state'), true);?>
			</select>
		</div>

                
                <?php
                $db = JFactory::getDbo();
                $sql = "SELECT id, name FROM #__fitness_goal_categories WHERE state='1'";
                $db->setQuery($sql);
                if(!$db->query()) {
                    JError::raiseError($db->getErrorMsg());
                }
                $goal_category= $db->loadObjectList();
                ?>

                <div class='filter-select fltrt'>
			<select name="filter_goal_category" class="inputbox" onchange="this.form.submit()">
				<option value=""><?php echo JText::_('-Primary Goal-');?></option>
				<?php echo JHtml::_('select.options', $goal_category, "id", "name", $this->state->get('filter.goal_category'), true);?>
			</select>
		</div>
            
                <?php
                $db = JFactory::getDbo();
                $sql = "SELECT id, name FROM #__fitness_training_period WHERE state='1'";
                $db->setQuery($sql);
                if(!$db->query()) {
                    JError::raiseError($db->getErrorMsg());
                }
                $training_period= $db->loadObjectList();
                ?>

                <div class='filter-select fltrt'>
			<select name="filter_training_period" class="inputbox" onchange="this.form.submit()">
				<option value=""><?php echo JText::_('-Training Period-');?></option>
				<?php echo JHtml::_('select.options', $training_period, "id", "name", $this->state->get('filter.training_period'), true);?>
			</select>
		</div>
            
            
            
                <?php
                $db = JFactory::getDbo();
                $sql = 'SELECT id AS value, title AS text'. ' FROM #__usergroups' . ' ORDER BY id';
                $db->setQuery($sql);
                if(!$db->query()) {
                    JError::raiseError($db->getErrorMsg());
                }
                $grouplist = $db->loadObjectList();
                foreach ($grouplist as $option) {
                    $group[] = JHTML::_('select.option', $option->value, $option->text );
                }
 
                ?>

                <div class='filter-select fltrt'>
			<select name="filter_group" class="inputbox" onchange="this.form.submit()">
				<option value=""><?php echo JText::_('-User Group-');?></option>
				<?php echo JHtml::_('select.options', $group, "value", "text", $this->state->get('filter.group'), true);?>
			</select>
		</div>
            
            
            
                <?php
                $goal_status[] = JHTML::_('select.option', '1', 'Incomplete' );
                $goal_status[] = JHTML::_('select.option', '2', 'Pending' );
                $goal_status[] = JHTML::_('select.option', '3', 'Complete' );
 
                ?>

                <div class='filter-select fltrt'>
			<select name="filter_goal_status" class="inputbox" onchange="this.form.submit()">
				<option value=""><?php echo JText::_('-Primary Goal Status-');?></option>
				<?php echo JHtml::_('select.options', $goal_status, "value", "text", $this->state->get('filter.goal_status'), true);?>
			</select>
		</div>


	</fieldset>
	<div class="clr"> </div>

	<table class="adminlist">
		<thead>
			<tr>
				<th width="1%">
					<input type="checkbox" name="checkall-toggle" value="" onclick="checkAll(this)" />
				</th>

				<th class='left'>
				<?php echo JHtml::_('grid.sort',  'COM_FITNESS_GOALS_GOALS_USER_ID', 'u.name', $listDirn, $listOrder); ?>
				</th>
                                <th class='left'>
				<?php echo JHtml::_('grid.sort',  'Training Period', 'gf.name', $listDirn, $listOrder); ?>
				</th>
				<th class='left'>
				<?php echo JHtml::_('grid.sort',  'Primary Goal', 'gc.name', $listDirn, $listOrder); ?>
				</th>
                                <th class='left'>
				<?php echo JHtml::_('grid.sort',  'User Group', 'a.user_group', $listDirn, $listOrder); ?>
				</th>
				<th class='left'>
				<?php echo JHtml::_('grid.sort',  'Start Date', 'a.startdate', $listDirn, $listOrder); ?>
				</th>			
				<th class='left'>
				<?php echo JHtml::_('grid.sort',  'Accomplish By', 'a.deadline', $listDirn, $listOrder); ?>
				</th>
				<th class='left'>
				<?php echo JHtml::_('grid.sort',  'Primary Goal Status', 'a.completed', $listDirn, $listOrder); ?>
				</th>
                                <th width="1%" class="nowrap">
                                        Notify
                                </th>
                                <th  class="nowrap">
                                        Mini Goals
                                </th>
                                <th class="nowrap">
                                        Mini Goals Deadline
                                </th>
                                <th  class="nowrap">
                                        Mini Goals Status
                                </th>
                                <th  class="nowrap">
                                        Add/Edit Mini Goals
                                </th>

                <?php if (isset($this->items[0]->state)) { ?>
				<th width="5%">
					<?php echo JHtml::_('grid.sort',  'JPUBLISHED', 'a.state', $listDirn, $listOrder); ?>
				</th>

                <?php } ?>
                <?php if (isset($this->items[0]->ordering)) { ?>
				<th width="10%">
					<?php echo JHtml::_('grid.sort',  'JGRID_HEADING_ORDERING', 'a.ordering', $listDirn, $listOrder); ?>
					<?php if ($canOrder && $saveOrder) :?>
						<?php echo JHtml::_('grid.order',  $this->items, 'filesave.png', 'goals.saveorder'); ?>
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
					<?php
                                        $user = JFactory::getUser($item->user_id);
                                        ?>
					<a href="<?php echo JRoute::_('index.php?option=com_fitness&task=goal.edit&id='.(int) $item->id); ?>">
					<?php echo $this->escape($user->name); ?></a>
                          
				</td>
                                <td>
                                        <?php echo $item->training_period; ?>
				</td>
				<td>
                                        <?php echo $item->goal_category_name; ?>
				</td>

                                <td>
					<?php echo $item->usergroup; ?>
				</td>
                                <td>
					<?php echo $item->start_date; ?>
				</td>
				<td>
					<?php echo $item->deadline; ?>
				</td>
				<td  class="center">
					<?php echo $this->goal_state_html($item->id, $item->completed, '1'); // 1 -> Primary Goal ?>
				</td>
                                <td class="center">
                                    <a onclick="sendEmail('<?php echo $item->id ?>', 'NotifyGoal', 1)" class="send_email_button"></a>
                                </td>
				<td>
					<?php echo $this->getMiniGoalsList($item->id, 'minigoals'); ?>
				</td>
				<td>
					<?php echo $this->getMiniGoalsList($item->id, 'deadline'); ?>
				</td>
				<td>
                                    <?php echo $this->getMinigoalsStatusHtml($item->id); ?>
					
				</td>
                                <td>
                                    <a href="<?php echo JRoute::_('index.php?option=com_fitness&view=minigoals&id='.(int) $item->id); ?>">New/Edit</a>
				</td>

                <?php if (isset($this->items[0]->state)) { ?>
				    <td class="center">
					    <?php echo JHtml::_('jgrid.published', $item->state, $i, 'goals.', $canChange, 'cb'); ?>
				    </td>
                <?php } ?>
                <?php if (isset($this->items[0]->ordering)) { ?>
				    <td class="order">
					    <?php if ($canChange) : ?>
						    <?php if ($saveOrder) :?>
							    <?php if ($listDirn == 'asc') : ?>
								    <span><?php echo $this->pagination->orderUpIcon($i, true, 'goals.orderup', 'JLIB_HTML_MOVE_UP', $ordering); ?></span>
								    <span><?php echo $this->pagination->orderDownIcon($i, $this->pagination->total, true, 'goals.orderdown', 'JLIB_HTML_MOVE_DOWN', $ordering); ?></span>
							    <?php elseif ($listDirn == 'desc') : ?>
								    <span><?php echo $this->pagination->orderUpIcon($i, true, 'goals.orderdown', 'JLIB_HTML_MOVE_UP', $ordering); ?></span>
								    <span><?php echo $this->pagination->orderDownIcon($i, $this->pagination->total, true, 'goals.orderup', 'JLIB_HTML_MOVE_DOWN', $ordering); ?></span>
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

<div data-goalid="" data-goaltype="" class="goal_status_wrapper">
    <img class="hideimage " src="<?php echo JUri::base() ?>components/com_fitness/assets/images/close.png" alt="close" title="close" onclick="hide_goal_status_wrapper()">
    <a onclick="goalSetStatus('1')" class="goal_status_pending goal_status__button" href="javascript:void(0)">pending</a>
    <a onclick="goalSetStatus('2')" class="goal_status_complete goal_status__button" href="javascript:void(0)">complete</a>
    <a onclick="goalSetStatus('3')" class="goal_status_incomplete goal_status__button" href="javascript:void(0)">incomplete</a>
    <input type="checkbox" id="send_goal_email" name="send_goal_email" value=""> Send email
</div>
<div id="emais_sended"></div>



<script type="text/javascript">

    $(document).ready(function(){

        $("#reset_filtered").click(function(){
            var form = $("#adminForm");
            form.find("select").val('');
            form.find("input").val('');
            form.submit();
        });

    });
    
    
    /**
     * 
     * @param {type} goal_id
     * @param {type} goal_status
     * @param {type} user_id
     * @returns {undefined}
     */
    function openSetGoalBox(goal_id, goal_status, goal_type) {
         $(".goal_status_wrapper").show();
         $(".goal_status_wrapper").attr('data-goalid', goal_id);
         $(".goal_status_wrapper").attr('data-goaltype', goal_type);
         $(".goal_status__button").show();
         if(goal_status == 1)  $(".goal_status_wrapper .goal_status_pending").hide();
         if(goal_status == 2)  $(".goal_status_wrapper .goal_status_complete").hide();
         if(goal_status == 3)  $(".goal_status_wrapper .goal_status_incomplete").hide();
    }
    
    /**
     * 
     * @returns {undefined}
     */
    function hide_goal_status_wrapper() {
        $(".goal_status_wrapper").hide();
    }
    
    /**
     * 
     * @param {type} goal_status_id
     * @returns {undefined}
     * 
     */
    function goalSetStatus(goal_status_id) {
        var goal_id = $(".goal_status_wrapper").attr('data-goalid');
        var goal_type = $(".goal_status_wrapper").attr('data-goaltype');// 1-> Primary Goal; 2 -> Mini Goal

        $.ajax({
                    type : "POST",
                    url : '<?php echo JUri::base() ?>index.php?option=com_fitness&tmpl=component&<?php echo JSession::getFormToken(); ?>=1',
                    data : {
                        view : 'goals',
                        format : 'text',
                        task : 'setGoalStatus',
                        goal_id : goal_id,
                        goal_status_id : goal_status_id,
                        goal_type : goal_type
                      },
                    dataType : 'json',
                    success : function(response) {
                        if(response.IsSuccess != true) {
                            alert(response.Msg);
                            return;
                        }
                        
                        var goal_status_id = response.Msg;
                        if(goal_status_id == goal_status_id) {
                            hide_goal_status_wrapper();
                            $("#goal_status_button_" + goal_id + "_" + goal_type).html( goal_status_html(goal_id, goal_status_id, goal_type) );
                            var send_goal_email = $("#send_goal_email").is(':checked');
                            var method;
                            switch(goal_status_id) {
                                case '1' :
                                    return;
                                    break;
                                case '2' :
                                    method = 'GoalComplete';
                                    break;
                                case '3' :
                                   method = 'GoalIncomplete';
                                   break;
                                default : 
                                    return;
                                    break;
                            }
                            if(send_goal_email) {
                                sendEmail(goal_id, method, goal_type);
                            }
                        } else {
                            alert('error');
                        }
                    },
                    error: function(XMLHttpRequest, textStatus, errorThrown)
                    {
                        alert("error");
                    }
                });
 
    }
    
    /**
    * 

     * @param {type} goal_id
     * @param {type} goal_status
     * @param {type} user_id
     * @returns {String}     */
    function goal_status_html(goal_id, goal_status, goal_type) {
        if(goal_status == 1) return '<a data-status="'  + goal_status + '"  class="goal_status_pending goal_status__button" href="javascript:void(0)" onclick="openSetGoalBox(' + goal_id + ', ' + goal_status + ', ' + goal_type + ')">pending</a>';
        if(goal_status == 2) return '<a data-status="'  + goal_status + '"  class="goal_status_complete goal_status__button" href="javascript:void(0)" onclick="openSetGoalBox(' + goal_id + ', ' + goal_status + ', ' + goal_type + ')">complete</a>';
        if(goal_status == 3) return '<a data-status="'  + goal_status + '"  class="goal_status_incomplete goal_status__button" href="javascript:void(0)" onclick="openSetGoalBox(' + goal_id + ', ' + goal_status + ', ' + goal_type + ')">incomplete</a>';
    }
    
    
    /**
     * 
     * @param {type} goal_id
     * @param {type} goal_status_id
     * @param {type} user_id
     * @returns {undefined}
     */
    function sendEmail(goal_id, method, goal_type) {
        var url = '<?php echo JURI::root()?>index.php?option=com_multicalendar&task=load&calid=0&method=send' + method + 'Email';
        $.ajax({
                type : "POST",
                url : url,
                data : {
                    goal_id : goal_id,
                    goal_type : goal_type
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

    
    
</script>
