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

function getUserGroup($user_id) {
    if(!$user_id) {
        $user_id = &JFactory::getUser()->id;
    }
    $db = JFactory::getDBO();
    $query = "SELECT title FROM #__usergroups WHERE id IN 
        (SELECT group_id FROM #__user_usergroup_map WHERE user_id='$user_id')";
    $db->setQuery($query);
    if(!$db->query()) {
        JError::raiseError($db->getErrorMsg());
    }
    return $db->loadResult();
}

$db = JFactory::getDbo();
$sql = "SELECT DISTINCT user_id FROM #__fitness_clients WHERE state='1'";
if(getUserGroup() != 'Super Users') {
    $user_id = &JFactory::getUser()->id;
    $sql .= " AND (primary_trainer='$user_id' OR other_trainers LIKE '%$user_id%')";
}
$db->setQuery($sql);
$clients = $db->loadObjectList();

function getTrainingPeriods() {
    // Training Period List
    $db = JFactory::getDbo();
    $sql = "SELECT * FROM #__fitness_training_period WHERE state='1'";
    $db->setQuery($sql);
    $training_periods = $db->loadObjectList();

    foreach ($training_periods as $item) {
        $color = '<div style="float:left;margin-right:5px;width:15px; height:15px;background-color:' . $item->color . '" ></div>';
        $name = '<div> ' . $item->name . '</div>';
        $html .= $color . $name .  "<br/>";
    }
    return $html;
}


?>

<div id="content">
    <table>
        <tr>
            <td>
                <div id="choices" style=" width:135px;"></div>
            </td>
            <td>
                <div class="graph-container" style="width:900px;">

                    <div id="placeholder" class="graph-placeholder"></div>

                </div>
            </td>
            <td>
                <fieldset style="width:140px; margin-left: 150px; ">
                    <legend>Training Period Keys</legend>
                    <?php echo getTrainingPeriods();?>
                </fieldset>
            </td>
        </tr>
        <tr>
            <td></td>
            <td>
                Zoom Scale to: <button id="whole">Whole period</button>
                <button id="by_year">Current Year</button>
                <button id="by_month">Current Month</button>
                <button id="by_week">Current Week</button>
                <button id="by_day">Current Day</button>
            </td>
            <td>
                Select Client to display on Graph: &nbsp;
                <select style="float:right;"  id="graph_client" name="client_id" class="inputbox">
                        <option value=""><?php echo JText::_('-Select-');?></option>
                        <?php 
                            foreach ($clients as $client) {
                                echo '<option value="' . $client->user_id . '">' . JFactory::getUser($client->user_id)->name. '</option>';
                            }
                        ?>
                </select>
            </td>
        </tr>
    </table>
    



</div>
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
                $sql = "SELECT id, name FROM #__fitness_goal_categories`";
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
                $sql = "SELECT id, name FROM #__fitness_training_period";
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
    function getScript(url,success) {
        var script = document.createElement('script');
        script.src = url;
        var head = document.getElementsByTagName('head')[0],
        done = false;
        // Attach handlers for all browsers
        script.onload = script.onreadystatechange = function() {
            if (!done && (!this.readyState
                || this.readyState == 'loaded'
                || this.readyState == 'complete')) {
                done = true;
                success();
                script.onload = script.onreadystatechange = null;
                head.removeChild(script);
            }
        };
        head.appendChild(script);
    }
 
    $(document).ready(function(){

        $("#reset_filtered").click(function(){
            var form = $("#adminForm");
            form.find("select").val('');
            form.find("input").val('');
            form.submit();
        });
        
        $("#graph_client").change(function(){
            var client_id =  $(this).find(':selected').val();
            if(!client_id) return;
            $("#choices").html('');
            $.ajax({
                    type : "POST",
                    url : '<?php echo JUri::base() ?>index.php?option=com_fitness&tmpl=component&<?php echo JSession::getFormToken(); ?>=1',
                    data : {
                        view : 'goals',
                        format : 'text',
                        task : 'getGraphData',
                        client_id : client_id
                      },
                    dataType : 'json',
                    success : function(response) {
                        if(response.status.success != true) {
                            alert(response.status.message);
                            return;
                        }
                        //console.log(response.data.mini_goals);
                        var data = {};
                        console.log(response.data);
                        // primary goals
                        var primary_goals_data = setPrimaryGoalsGraphData(response.data.primary_goals);
                        $.extend(true,data, primary_goals_data);
                        
                        // mini goals
                        var mini_goals_data = setMiniGoalsGraphData(response.data.mini_goals);
                        $.extend(true,data, mini_goals_data);
                        
                        // Personal training
                        var personal_training_data = setAppointmentGraphData('personal_training', response.data.personal_training, 3);
                        $.extend(true,data, personal_training_data);
                        //console.log(personal_training_data);
                        
                        // Semi-Private Training
                        var semi_private_data = setAppointmentGraphData('semi_private', response.data.semi_private, 4);
                        $.extend(true,data, semi_private_data);
                        
                        // Resistance Workout
                        var resistance_workout_data = setAppointmentGraphData('resistance_workout', response.data.resistance_workout, 5);
                        $.extend(true,data, resistance_workout_data);
                        
                        // Cardio Workout
                        var cardio_workout_data = setAppointmentGraphData('cardio_workout', response.data.cardio_workout, 6);
                        $.extend(true,data, cardio_workout_data);
                        
                        // Assessment
                        var assessment_data = setAppointmentGraphData('assessment', response.data.assessment, 7);
                        $.extend(true,data, assessment_data);                       
                        
                        //console.log(personal_training_data);
                        drawGraph(data);

                    },
                    error: function(XMLHttpRequest, textStatus, errorThrown)
                    {
                        alert("error");
                    }
                });
 
        });

    });
    
    
    function setPrimaryGoalsGraphData(primary_goals) {
        var data = {};
        data.primary_goals = x_axisDateArray(primary_goals, 2, 'deadline');
        data.client_primary = graphItemDataArray(primary_goals, 'client_name');
        data.goal_primary = graphItemDataArray(primary_goals, 'primary_goal_name');
        data.start_primary = graphItemDataArray(primary_goals, 'start_date');
        data.finish_primary = graphItemDataArray(primary_goals, 'deadline');
        data.status_primary = graphItemDataArray(primary_goals, 'completed');
        data.training_period_colors = graphItemDataArray(primary_goals, 'training_period_color');
        return data;
    }
    
    function setMiniGoalsGraphData(mini_goals) {
        var data = {};
        data.mini_goals = x_axisDateArray(mini_goals, 1, 'deadline');
        data.client_mini = graphItemDataArray(mini_goals, 'client_name');
        data.goal_mini = graphItemDataArray(mini_goals, 'mini_goal_name');
        data.start_mini = graphItemDataArray(mini_goals, 'start_date');
        data.finish_mini = graphItemDataArray(mini_goals, 'deadline');
        data.status_mini = graphItemDataArray(mini_goals, 'completed');
        return data;
    }
    
    
    function setAppointmentGraphData(type, appointment, y_axis) {
        var data = {};
        
        data[type + '_xaxis'] = x_axisDateArray(appointment, y_axis, 'starttime');
        data[type + '_session_type'] = graphItemDataArray(appointment, 'session_type');
        data[type + '_session_focus'] = graphItemDataArray(appointment, 'session_focus');
        data[type + '_date'] = graphItemDataArray(appointment, 'starttime');
        data[type + '_trainer'] = graphItemDataArray(appointment, 'trainer_name');
        data[type + '_location'] = graphItemDataArray(appointment, 'location');
        data[type + '_appointment_color'] = graphItemDataArray(appointment, 'color');
        
        //console.log(data);
        return data;      
    }
    
    
    function graphItemDataArray(data, type) {
        var items = []; 
        for(var i = 0; i < data.length; i++) {
            items[i] = data[i][type];
        }
        return items;
    }
 
 
    /** 
    * 

     * @param {type} data
     * @returns {Array}     */
    function x_axisDateArray(data, y_value, field) {
        var x_axis_array = []; 
        
        for(var i = 0; i < data.length; i++) {
            //console.log(data[i][field]);
            var unix_time = new Date(Date.parse(data[i][field])).getTime();
            
            //console.log(unix_time);
            
            //var date = new Date(unix_time);
            
            //console.log(date);
            x_axis_array[i] = [unix_time, y_value];
        }
        return x_axis_array;
    }

    
    /**
    * draw Flot Graph on select client

     * @param {type} data
     * @returns {undefined}     */
    function drawGraph(client_data) {
        
         //TIME SETTINGS
        var current_time = new Date().getTime();
        var start_year = new Date(new Date().getFullYear(), 0, 1).getTime();
        var end_year = new Date(new Date().getFullYear(), 12, 0).getTime();

        var date = new Date();
        var firstDay = new Date(date.getFullYear(), date.getMonth(), 1).getTime() - 60*59*24 * 1000;
        var lastDay = new Date(date.getFullYear(), date.getMonth() + 1, 0).getTime() + 60*59*24 * 1000;

      
        //var start_week = 1375056000000;
        //var end_week = 1375574400000;
        
        var start_week = startAndEndOfWeek(new Date())[0];
        var end_week = startAndEndOfWeek(new Date())[1];

        var start_day = (new Date(date.getFullYear(), date.getMonth(),date.getDate())).getTime();
        var end_day = (new Date(date.getFullYear(), date.getMonth(), date.getDate())).getTime() + 60*60*24 * 1000;
        //alert(date.getDate());
       
        // END TIME SETTINGS

        // DATA
        // Primary Goals
        //var d1 = [[1377993600 * 1000, 2]];
        var d1 = client_data.primary_goals;

        var training_period_colors = client_data.training_period_colors;
        
        
        // Training periods 
        var markings = []; 
        for(var i = 0; i < d1.length - 1; i++) {
            markings[i] =  { xaxis: { from: d1[i][0], to: d1[i + 1][0] }, yaxis: { from: 0.25, to: 0.75 }, color: training_period_colors[i+1]};
        }
        // first Primary Goal marking
        
        var first_primary_goal_start_date = new Date(client_data.start_primary[0]).getTime();
        if(first_primary_goal_start_date) {
            markings[markings.length] =  { xaxis: { from: first_primary_goal_start_date, to: d1[0][0] }, yaxis: { from: 0.25, to: 0.75 }, color: training_period_colors[0]};
        }
        //console.log(markings);
        //
        // Mini Goals
        //var d2 = [[1320376000 * 1000, 1], [1330376000 * 1000, 1], [1340376000 * 1000, 1], [1350998400 * 1000, 1], [1374710400 * 1000, 1]];
        var d2 = client_data.mini_goals;
       
        var d3 = client_data.personal_training_xaxis;
        
        var d4 = client_data.semi_private_xaxis; 
        
        var d5 = client_data.resistance_workout_xaxis;
        
        var d6 = client_data.cardio_workout_xaxis;
        
        var d7 = client_data.assessment_xaxis;
        
        // Current Time
        var d8 = [[current_time, 8]];
        
        var data = [
            {label: "Primary Goal", data: d1},
            {label: "Mini Goal", data: d2},
            {label: "Personal Training", data: d3},
            {label: "Semi-Private Training", data: d4},
            {label: "Resistance Workout", data: d5},
            {label: "Cardio Workout", data: d6},
            {label: "Assessment", data: d7},
            {label: "Current Time", data: d8}
        ];
        // END DATA
        
        // START OPTIONS
        // base common options
        var options = {
            xaxis: {mode: "time", timezone: "browser"},
            yaxis: {show: false},
            series: {
                lines: {show: false },
                points: {show: true, radius: 5, symbol: "circle", fill: true, fillColor: "#FFFFFF" },
                bars: {show: true, lineWidth: 3},
            },
            grid: {
                        hoverable: true,
                        clickable: true,
                        backgroundColor: {
                             colors: ["#FFFFFF", "#F0F0F0"]
                        },
                        markings: markings
            },
            legend: {show: true, margin: [-170, 0]},

            colors: [
                "#A3270F",// Primary Goal
                "#287725", // Mimi Goal
                client_data.personal_training_appointment_color[0] ||"#00BF32",
                client_data.semi_private_appointment_color[0] || "#007F01",
                client_data.resistance_workout_appointment_color[0] || "#0070FF",
                client_data.cardio_workout_appointment_color[0] || "#E94E1B",
                client_data.assessment_appointment_color[0] || "#E6007E",
                "#FFB01F"// Current Time
            ]


        };

        // month options
        var options_year = { xaxis: {tickSize: [1, "month"], min: start_year, max: end_year}};
        Object.deepExtend(options_year, options);
        // month options
        var options_month = { xaxis: {tickSize: [1, "day"], min:  firstDay, max: lastDay, timeformat: "%d"}};
        Object.deepExtend(options_month, options);
        // week options
        var options_week= { xaxis: {tickSize: [1, "day"], min:  start_week, max: end_week, timeformat: "%a"}};
        Object.deepExtend(options_week, options);      
        // day options
        var options_day = { xaxis: {minTickSize: [1, "hour"],min: start_day, max: end_day, twelveHourClock: true}};
        Object.deepExtend(options_day, options);

        var current_options = {
            get : function() {return this.options;},
            set : function(options) {this.options = options}
        };
        current_options = options_year;
        // END OPTIONS

        // START RUN BY PERIOD
        // whole 
        $("#whole").click(function() {
            current_options = options;
            plotAccordingToChoices(data, current_options);
        });

         // by year
        $("#by_year").click(function() {
            current_options = options_year;
            plotAccordingToChoices(data, current_options);
        });


       // by month
        $("#by_month").click(function() {
            current_options = options_month;
            plotAccordingToChoices(data, current_options);
        });
        
        // by week
        $("#by_week").click(function() {
            current_options = options_week;
            plotAccordingToChoices(data, current_options);
        });
        
        // by day
        $("#by_day").click(function() {
            current_options = options_day
            plotAccordingToChoices(data, current_options);
        });
        
         // TOOGLE
        // insert checkboxes 
        $.each(data, function(key, val) {
            $("#choices").append("<br/><input type='checkbox' name='" + key +
                    "' checked='checked' id='id" + key + "'></input>" +
                    "<label for='id" + key + "'>"
                    + val.label + "</label>");
        });
        $("#choices").find("input").click(function() {
            plotAccordingToChoices(data, current_options);
        });
        plotAccordingToChoices(data, current_options);
        //END TOOGLE
        //
        // END START RUN BY PERIOD

        $("<div id='tooltip'></div>").css({
                position: "absolute",
                display: "none",
                border: "2px solid #cccccc",
                "border-radius": "10px",
                padding: "5px",
                "background-color": "#fee",
                opacity: 0.9
        }).appendTo("body");

        $("#placeholder").bind("plothover", function (event, pos, item) {
            if (item) {
                var data_type = item.datapoint[1];
                var html = "<p style=\"text-align:center;\"><b>" +  item.series.label + "</b></p>";

                switch(data_type) {
                    case 1 : // Mini Goals
                        html +=  "Client: " +  client_data.client_mini[item.dataIndex] + "</br>";
                        html +=  "Goal: " +  client_data.goal_mini[item.dataIndex] + "</br>";
                        html +=  "Start: " +  client_data.start_mini[item.dataIndex] + "</br>";
                        html +=  "Finish: " +  client_data.finish_mini[item.dataIndex] + "</br>";
                        html +=  "Status: " +  getStatusById(client_data.status_mini[item.dataIndex]) + "</br>"; 
                        $("#tooltip").css("background-color", "#287725");
                        break;
                    case 2 : // Primary Goals
                        html +=  "Client: " +  client_data.client_primary[item.dataIndex] + "</br>";
                        html +=  "Goal: " +  client_data.goal_primary[item.dataIndex] + "</br>";
                        html +=  "Start: " +  client_data.start_primary[item.dataIndex] + "</br>";
                        html +=  "Finish: " +  client_data.finish_primary[item.dataIndex] + "</br>";
                        html +=  "Status: " +  getStatusById(client_data.status_primary[item.dataIndex]) + "</br>"; 
                        $("#tooltip").css("background-color", "#A3270F");
                        break;
                    case 3 : // Personal Training
                        html =  setAppointmentsTooltip(html, client_data, item, 'personal_training');
                        break;
                    case 4 : // Semi-Private Training
                        html =  setAppointmentsTooltip(html, client_data, item, 'semi_private');
                        break;
                    case 5 : // Resistance Workout
                        html =  setAppointmentsTooltip(html, client_data, item, 'resistance_workout');
                        break;
                    case 6 : //  Cardio Workout
                        html =  setAppointmentsTooltip(html, client_data, item, 'cardio_workout');
                          break;
                    case 7 : // Assessment
                        html =  setAppointmentsTooltip(html, client_data, item, 'assessment');
                        break;
                    case 8 : // Current Time
                        html =  "Current Time" ;
                        $("#tooltip").css("background-color", "#FFB01F");
                        break;
                    default :
                        break;
                }

                $("#tooltip").html(html)
                    .css({top: item.pageY+5, left: item.pageX+5})
                    .fadeIn(200);
            } else {
                    $("#tooltip").hide();
            }
            
        });
    }
    
    function plotAccordingToChoices(data, options) {
        var data_temp = [];
        $("#choices").find("input:checked").each(function () {
                var key = $(this).attr("name");
                if (key && data[key]) {
                        data_temp.push(data[key]);
                        
                } else {
                    data_temp.push(null);
                }
        });
console.log(data_temp);
        if (data_temp.length > 0) {
                $.plot("#placeholder", data_temp, options);
        }
    }
    

    
    function setAppointmentsTooltip(html, client_data, item, type) {
   
       $("#tooltip").css("background-color", client_data[type + '_appointment_color'][0]);
    
       html +=  "Session Type: " +  client_data[type + '_session_type'][item.dataIndex] + "</br>";
       html +=  "Session Focus: " +  client_data[type + '_session_focus'][item.dataIndex] + "</br>";
       html +=  "Date: " +  client_data[type + '_date'][item.dataIndex] + "</br></br>";
       html +=  "Trainer: " +  client_data[type + '_trainer'][item.dataIndex] + "</br>";
       html +=  "Location: " +  client_data[type + '_location'][item.dataIndex] + "</br>"; 

       return html;
    }
    
    function getStatusById(id) {
    var status_name;
        switch(id) {
            case '1' : 
               status_name = 'Pending';
               break;
            case '2' :
               status_name = 'Complete';
               break;
            case '3' :
               status_name = 'Incomplete';
            default :

               break;
        }
        return status_name;
    }
    
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
    
    
                
    // HELP LIBRARY
    // provide inheritance
    function inherit(p) {
        if (p == null) throw TypeError();
        if(Object.create) {
            return Object.create(p);
        }
        var t = typeof p;
        if(t !== "object" && t !== "function") throw TypeError();
        function f() {};
        f.prototype = p;
        return new f();
    }

    // provide deep inheritance
    Object.deepExtend = function(destination, source) {
      for (var property in source) {
        if (source[property] && source[property].constructor &&
         source[property].constructor === Object) {
          destination[property] = destination[property] || {};
          arguments.callee(destination[property], source[property]);
        } else {
          destination[property] = source[property];
        }
      }
      return destination;
    };


    function startAndEndOfWeek(date) {

      // If no date object supplied, use current date
      // Copy date so don't modify supplied date
      var now = date? new Date(date) : new Date();

      // set time to some convenient value
      now.setHours(0,0,0,0);

      // Get the previous Monday
      var monday = new Date(now);
      monday.setDate(monday.getDate() - monday.getDay() + 1);

      // Get next Sunday
      var sunday = new Date(now);
      sunday.setDate(sunday.getDate() - sunday.getDay() + 7);

      // Return array of date objects
      return [monday, sunday];
    }
    // END HELP LIBRARY

    
    
</script>
