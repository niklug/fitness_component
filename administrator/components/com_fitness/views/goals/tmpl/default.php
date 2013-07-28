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
?>
<div id="content">

    <div class="graph-container">
        <div id="placeholder" class="graph-placeholder"></div>
    </div>

    <p>Zoom to: <button id="whole">Whole period</button>
        <button id="by_year">Current year</button>
        <button id="by_month">Current month</button>
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
				<th class='left'>
				<?php echo JHtml::_('grid.sort',  'COM_FITNESS_GOALS_GOALS_CREATED', 'a.created', $listDirn, $listOrder); ?>
				</th>
				<th class='left'>
				<?php echo JHtml::_('grid.sort',  'COM_FITNESS_GOALS_GOALS_MODIFIED', 'a.modified', $listDirn, $listOrder); ?>
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
				<td id="goal_status_button_<?php echo $item->id ?>" class="center">
					<?php echo $this->goal_state_html($item->id, $item->completed, $item->user_id); ?>
				</td>
                                <td class="center">
                                    <a onclick="sendEmail('<?php echo $item->id ?>', 'NotifyGoal')" class="send_email_button"></a>
                                </td>
				<td>
					<?php echo $item->created; ?>
				</td>
				<td>
					<?php echo $item->modified; ?>
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

<div class="goal_status_wrapper">
    <img class="hideimage " src="<?php echo JUri::base() ?>components/com_fitness/assets/images/close.png" alt="close" title="close" onclick="hide_goal_status_wrapper()">
    <span>Set Goal <b id="goal_number"></b> as:</span>
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
        
        
        
         //TIME SETTINGS
        var current_time = new Date().getTime();
        var start_year = new Date(new Date().getFullYear(), 0, 1).getTime();
        var end_year = new Date(new Date().getFullYear(), 12, 0).getTime();

        var date = new Date();
        var firstDay = new Date(date.getFullYear(), date.getMonth(), 1).getTime();
        var lastDay = new Date(date.getFullYear(), date.getMonth() + 1, 0).getTime();
        // END TIME SETTINGS

        // DATA
        // Primary Goals
        var d1 = [[1325376000 * 1000, 2], [1335376000 * 1000, 2], [1345376000 * 1000, 2], [1356998400 * 1000, 2], [1386998400 * 1000, 2]];

        var training_period_colors = ["#E4C7EA", "#FFC7E1", "#CCDDFF", "#FFFDAB", "#E5E2A8", "#CDFCF7", "#DCF2C4", "#F4ABDC"];

        var training_period_keys = [5, 2, 3, 4, 5, 6, 7];
        // Training periods 
        var markings = []; 
        for(var i = 0; i < d1.length - 1; i++) {
            markings[i] =  { xaxis: { from: d1[i][0], to: d1[i + 1][0] }, yaxis: { from: 0.5, to: 0.75 }, color: training_period_colors[training_period_keys[i]]};
        }
        //
        // Mini Goals
        var d2 = [[1320376000 * 1000, 1], [1330376000 * 1000, 1], [1340376000 * 1000, 1], [1350998400 * 1000, 1], [1374710400 * 1000, 1]];
        // Current Time
        var d3 = [[current_time, 3]];

        var data = [
            {label: "Primary Goal", data: d1},
            {label: "Mini Goal", data: d2},
            {data: d3}
        ];


        var client_primary = ['Nick', 'Roy', 'John', 'Dave', 'Bill'];
        var goal_primary  = ['Increase Muscle Mass', 'Increase Strength', 'Increase Strength and Fitness', 'Increase Fitness for Distance', 'Increase Mind'];
        var start_primary  = ['2013-02-03', '2013-02-14', '2013-08-03', '2013-02-22', '2013-02-05'];
        var finish_primary  = ['2013-03-03', '2013-03-14', '2013-03-03', '2013-03-23', '2013-02-18'];
        var status_primary  = ['Pending', 'Complete', 'Incomplete', 'Pending', 'Complete'];



        var client_mini = ['Nick_minu', 'Roy_mini', 'John_mini', 'Dave_mini', 'Bill_mini'];
        var goal_mini  = ['Increase Muscle Mass', 'Increase Strength', 'Increase Strength and Fitness', 'Increase Fitness for Distance', 'Increase Mind'];
        var start_mini  = ['2013-02-03', '2013-02-14', '2013-08-03', '2013-02-22', '2013-02-05'];
        var finish_mini  = ['2013-03-03', '2013-03-14', '2013-03-03', '2013-03-23', '2013-02-18'];
        var status_mini  = ['Pending', 'Complete', 'Incomplete', 'Pending', 'Complete'];
        // END DATA

        // START OPTIONS
        // base common options
        var options = {
            xaxis: {mode: "time", timezone: "browser"},
            yaxis: {show: false},
            series: {
                lines: {show: false },
                points: {show: true, radius: 7, symbol: "circle", fill: true, fillColor: "#FFFFFF" },
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

            colors: ["#A3270F", "#287725", "#FFB01F"]


        };

        // month options
        var options_year = { xaxis: {tickSize: [1, "month"], min: start_year, max: end_year}};
        Object.deepExtend(options_year, options);
        // week options
        var options_month = { xaxis: {tickSize: [1, "day"], min:  firstDay, max: lastDay, timeformat: "%d"}};
        Object.deepExtend(options_month, options);


        // END OPTIONS

        // START RUN BY PERIOD
        // default
        $.plot("#placeholder", data, options);

        // whole 
        $("#whole").click(function() {
            $.plot("#placeholder", data, options);
        });

         // by year
        $("#by_year").click(function() {
            $.plot("#placeholder", data, options_year);
        });


       // by month
        $("#by_month").click(function() {
            $.plot("#placeholder", data, options_month);
        });
        // END START RUN BY PERIOD

        $("<div id='tooltip'></div>").css({
                position: "absolute",
                display: "none",
                border: "2px solid #A3270F",
                "border-radius": "10px",
                padding: "5px",
                "background-color": "#fee",
                opacity: 0.9
        }).appendTo("body");

        $("#placeholder").bind("plothover", function (event, pos, item) {
            if (item) {
                var data_type = item.datapoint[1];
                var html = "<p style=\"text-align:center;\"><b>" +  item.series.label + "</b></p>";

                if(data_type == 1) {
                    html +=  "Client: " +  client_mini[item.dataIndex] + "</br>";
                    html +=  "Goal: " +  goal_mini[item.dataIndex] + "</br>";
                    html +=  "Start: " +  start_mini[item.dataIndex] + "</br>";
                    html +=  "Finish: " +  finish_mini[item.dataIndex] + "</br>";
                    html +=  "Status: " +  status_mini[item.dataIndex] + "</br>";
                }
                if(data_type == 2){
                    html +=  "Client: " +  client_primary[item.dataIndex] + "</br>";
                    html +=  "Goal: " +  goal_primary[item.dataIndex] + "</br>";
                    html +=  "Start: " +  start_primary[item.dataIndex] + "</br>";
                    html +=  "Finish: " +  finish_primary[item.dataIndex] + "</br>";
                    html +=  "Status: " +  status_primary[item.dataIndex] + "</br>";
                }
                if(data_type == 3) html = "Current date";
                //console.log(item);

                $("#tooltip").html(html)
                    .css({top: item.pageY+5, left: item.pageX+5})
                    .fadeIn(200);
            } else {
                    $("#tooltip").hide();
            }

        });

    });
 
    
    
    
    /**
     * 
     * @param {type} goal_id
     * @param {type} goal_status
     * @param {type} user_id
     * @returns {undefined}
     */
    function openSetGoalBox(goal_id, goal_status, user_id) {
         goal_user_id = user_id;
         $(".goal_status_wrapper").show();
         $("#goal_number").html(goal_id);
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
        var goal_id = $("#goal_number").text();
        var user_id = goal_user_id;
                    
        $.ajax({
                    type : "POST",
                    url : '<?php echo JUri::base() ?>index.php?option=com_fitness&tmpl=component&<?php echo JSession::getFormToken(); ?>=1',
                    data : {
                        view : 'goals',
                        format : 'text',
                        task : 'setGoalStatus',
                        goal_id : goal_id,
                        goal_status_id : goal_status_id,
                        user_id : user_id
                    },
                    dataType : 'text',
                    success : function(respond_goal_status_id) {
                        if(respond_goal_status_id == goal_status_id) {
                            hide_goal_status_wrapper();
                            $("#goal_status_button_" + goal_id).html( goal_status_html(goal_id, goal_status_id, user_id) );
                            var send_goal_email = $("#send_goal_email").is(':checked');
                            var method;
                            switch(respond_goal_status_id) {
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
                                sendEmail(goal_id, method);
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
    function goal_status_html(goal_id, goal_status, user_id) {
        if(goal_status == 1) return '<a data-status="'  + goal_status + '"  class="goal_status_pending goal_status__button" href="javascript:void(0)" onclick="openSetGoalBox(' + goal_id + ', ' + goal_status + ', ' + user_id + ')">pending</a>';
        if(goal_status == 2) return '<a data-status="'  + goal_status + '"  class="goal_status_complete goal_status__button" href="javascript:void(0)" onclick="openSetGoalBox(' + goal_id + ', ' + goal_status + ', ' + user_id + ')">complete</a>';
        if(goal_status == 3) return '<a data-status="'  + goal_status + '"  class="goal_status_incomplete goal_status__button" href="javascript:void(0)" onclick="openSetGoalBox(' + goal_id + ', ' + goal_status + ', ' + user_id + ')">incomplete</a>';
    }
    
    
    /**
     * 
     * @param {type} goal_id
     * @param {type} goal_status_id
     * @param {type} user_id
     * @returns {undefined}
     */
    function sendEmail(goal_id, method) {
        //var goal_status_id = $("#goal_status_button_" + goal_id + " a").attr('data-status');
        var url = '<?php echo JURI::root()?>index.php?option=com_multicalendar&task=load&calid=0&method=send' + method + 'Email';
        $.ajax({
                type : "POST",
                url : url,
                data : {
                    goal_id : goal_id
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
    // END HELP LIBRARY

    
    
</script>
