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

<form action="<?php echo JRoute::_('index.php?option=com_fitness&view=goals'); ?>" method="post" name="adminForm" id="adminForm">
	<fieldset id="filter-bar">
		<div class="filter-search fltlft">
			<label class="filter-search-lbl" for="filter_search"><?php echo JText::_('JSEARCH_FILTER_LABEL'); ?></label>
			<input type="text" name="filter_search" id="filter_search" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" title="<?php echo JText::_('Search'); ?>" />
			<button type="submit"><?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?></button>
			<button type="button" onclick="document.id('filter_search').value='';this.form.submit();"><?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?></button>
		</div>
		
        
		<div class='filter-select fltrt'>
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
                $goal_category= $db->loadObjectList();
                ?>

                <div class='filter-select fltrt'>
			<select name="filter_goal_category" class="inputbox" onchange="this.form.submit()">
				<option value=""><?php echo JText::_('-Goal Type-');?></option>
				<?php echo JHtml::_('select.options', $goal_category, "id", "name", $this->state->get('filter.goal_category'), true);?>
			</select>
		</div>
            
                <?php
                $db = JFactory::getDbo();
                $sql = "SELECT id, name FROM #__fitness_goal_focus``";
                $db->setQuery($sql);
                $goal_focus= $db->loadObjectList();
                ?>

                <div class='filter-select fltrt'>
			<select name="filter_goal_focus" class="inputbox" onchange="this.form.submit()">
				<option value=""><?php echo JText::_('-Goal Focus-');?></option>
				<?php echo JHtml::_('select.options', $goal_focus, "id", "name", $this->state->get('filter.goal_focus'), true);?>
			</select>
		</div>
            
            
            
                <?php
                $db = JFactory::getDbo();
                $sql = 'SELECT id AS value, title AS text'. ' FROM #__usergroups' . ' ORDER BY id';
                $db->setQuery($sql);
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
				<option value=""><?php echo JText::_('-Goal status-');?></option>
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
				<?php echo JHtml::_('grid.sort',  'Goal Type', 'gc.name', $listDirn, $listOrder); ?>
				</th>
                                <th class='left'>
				<?php echo JHtml::_('grid.sort',  'Goal Focus', 'gf.name', $listDirn, $listOrder); ?>
				</th>
                                <th class='left'>
				<?php echo JHtml::_('grid.sort',  'User Group', 'a.user_group', $listDirn, $listOrder); ?>
				</th>
			
				<th class='left'>
				<?php echo JHtml::_('grid.sort',  'COM_FITNESS_GOALS_GOALS_DEADLINE', 'a.deadline', $listDirn, $listOrder); ?>
				</th>
				<th class='left'>
				<?php echo JHtml::_('grid.sort',  'COM_FITNESS_GOALS_GOALS_status', 'a.status', $listDirn, $listOrder); ?>
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
                <th width="1%" class="nowrap">
                    <?php echo 'Send email'; ?>
                </th>
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
                                        <?php echo $item->goal_category_name; ?>
				</td>
                                <td>
                                        <?php echo $item->goal_focus_name; ?>
				</td>
                                <td>
					<?php echo $item->usergroup; ?>
				</td>
				<td>
					<?php echo $item->deadline; ?>
				</td>
				<td id="goal_status_button_<?php echo $item->id ?>" class="center">
					<?php echo $this->goal_state_html($item->id, $item->status, $item->user_id); ?>
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
                                <td class="center">
                                    <?php
                                    echo '<a onclick="sendGoalEmail(' . $item->id . ', ' . $item->status . ', ' . $item->user_id . ')" class="send_email_button" href="javascript:void(0)"></a>';
                                    ?>
					
                                
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
    <a onclick="goalSetStatus('1')" class="goal_status_incomplete goal_status__button" href="javascript:void(0)">incomplete</a>
    <a onclick="goalSetStatus('2')" class="goal_status_pending goal_status__button" href="javascript:void(0)">pending</a>
    <a onclick="goalSetStatus('3')" class="goal_status_complete goal_status__button" href="javascript:void(0)">complete</a>
    <input type="checkbox" id="send_goal_email" name="send_goal_email" value=""> Send email
</div>

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
    getScript('//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js',function() {
        jQuery = jQuery.noConflict();
        jQuery(document).ready(function(){
      
 
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
         jQuery(".goal_status_wrapper").show();
         jQuery("#goal_number").html(goal_id);
         jQuery(".goal_status__button").show();
         if(goal_status == 1)  jQuery(".goal_status_wrapper .goal_status_incomplete").hide();
         if(goal_status == 2)  jQuery(".goal_status_wrapper .goal_status_pending").hide();
         if(goal_status == 3)  jQuery(".goal_status_wrapper .goal_status_complete").hide();
    }
    
    /**
     * 
     * @returns {undefined}
     */
    function hide_goal_status_wrapper() {
        jQuery(".goal_status_wrapper").hide();
    }
    
    /**
     * 
     * @param {type} goal_status_id
     * @returns {undefined}
     * 
     */
    function goalSetStatus(goal_status_id) {
        var goal_id = jQuery("#goal_number").text();
        var user_id = goal_user_id;
                    
        jQuery.ajax({
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
                            jQuery("#goal_status_button_" + goal_id).html( goal_status_html(goal_id, goal_status_id, user_id) );
                            var send_goal_email = jQuery("#send_goal_email").is(':checked');
                            
                            if(send_goal_email) {
                                
                                sendGoalEmail(goal_id, goal_status_id, user_id);
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
        if(goal_status == 1) return '<a class="goal_status_incomplete goal_status__button" href="javascript:void(0)" onclick="openSetGoalBox(' + goal_id + ', ' + goal_status + ', ' + user_id + ')">incomplete</a>';
        
        if(goal_status == 2) return '<a class="goal_status_pending goal_status__button" href="javascript:void(0)" onclick="openSetGoalBox(' + goal_id + ', ' + goal_status + ', ' + user_id + ')">pending</a>';
        
        if(goal_status == 3) return '<a class="goal_status_complete goal_status__button" href="javascript:void(0)" onclick="openSetGoalBox(' + goal_id + ', ' + goal_status + ', ' + user_id + ')">complete</a>';
    }
    
    
    /**
     * 
     * @param {type} goal_id
     * @param {type} goal_status_id
     * @param {type} user_id
     * @returns {undefined}
     */
    function sendGoalEmail(goal_id, goal_status_id, user_id) {
        jQuery.ajax({
                    type : "POST",
                    url : '<?php echo JUri::base() ?>index.php?option=com_fitness&tmpl=component&<?php echo JSession::getFormToken(); ?>=1',
                    data : {
                        view : 'goals',
                        format : 'text',
                        task : 'sendGoalEmail',
                        goal_id : goal_id,
                        goal_status_id : goal_status_id,
                        user_id : user_id
                    },
                    dataType : 'text',
                    success : function(email_send_status) {
                        if(email_send_status == '1') {
                            alert('Email sent');
                        } else {
                            alert(email_send_status);
                        }
                    },
                    error: function(XMLHttpRequest, textStatus, errorThrown)
                    {
                        alert("error");
                    }
                });
    }
    
</script>
