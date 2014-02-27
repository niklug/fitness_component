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

$primary_goal_id = JRequest::getVar('id');
$session = &JFactory::getSession();
if($primary_goal_id) {
    $session->set('primary_goal_id', $primary_goal_id);
}

require_once  JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_fitness' . DS .'helpers' . DS . 'fitness.php';

$helper = new FitnessHelper();


?>

<form action="<?php echo JRoute::_('index.php?option=com_fitness&view=minigoals'); ?>" method="post" name="adminForm" id="adminForm">
	<fieldset id="filter-bar">
		<div class="filter-search fltlft">
			<label class="filter-search-lbl" for="filter_search"><?php echo JText::_('JSEARCH_FILTER_LABEL'); ?></label>
			<input type="text" name="filter_search" id="filter_search" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" title="<?php echo JText::_('Search'); ?>" />
			<button type="submit"><?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?></button>
			<button type="button" id="reset_filtered"><?php echo JText::_('Reset All'); ?></button>
		</div>
            
            
		
        
		<div class='filter-select fltrt'>
			<select name="filter_published" class="inputbox" onchange="this.form.submit()">
				<option value=""><?php echo JText::_('JOPTION_SELECT_PUBLISHED');?></option>
				<?php echo JHtml::_('select.options', JHtml::_('jgrid.publishedOptions'), "value", "text", $this->state->get('filter.state'), true);?>
			</select>
		</div>

                <div class='filter-select fltrt'>
			<select name="filter_training_period" class="inputbox" onchange="this.form.submit()">
				<option value=""><?php echo JText::_('-Training Period-');?></option>
				<?php echo JHtml::_('select.options', $helper->getTrainingPeriod(), "id", "name", $this->state->get('filter.training_period'), true);?>
			</select>
		</div>
            
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


	</fieldset>
	<div class="clr"> </div>

	<table class="adminlist">
		<thead>
			<tr>
				<th width="1%">
					<input type="checkbox" name="checkall-toggle" value="" onclick="checkAll(this)" />
				</th>
				<th class='left'>
				<?php echo JHtml::_('grid.sort',  'COM_FITNESS_MINIGOALS_MINI_GOAL_CATEGORY_ID', 'a.mini_goal_category_id', $listDirn, $listOrder); ?>
				</th>
				<th class='left'>
				<?php echo JHtml::_('grid.sort',  'COM_FITNESS_MINIGOALS_PRIMARY_GOAL_ID', 'a.primary_goal_id', $listDirn, $listOrder); ?>
				</th>
                                <th class='left'>
				<?php echo JHtml::_('grid.sort',  'Training Period', 'gf.name', $listDirn, $listOrder); ?>
				</th>
                                <th class='left'>
				<?php echo JHtml::_('grid.sort',  'Start Date', 'a.startdate', $listDirn, $listOrder); ?>
				</th>	
				<th class='left'>
				<?php echo JHtml::_('grid.sort',  'COM_FITNESS_MINIGOALS_DEADLINE', 'a.deadline', $listDirn, $listOrder); ?>
				</th>
                                <th  class="left">
                                        Status
                                </th>
                                <th  class="left">
                                        Edit/View
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
						<?php echo JHtml::_('grid.order',  $this->items, 'filesave.png', 'minigoals.saveorder'); ?>
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
                                    <a href="<?php echo JRoute::_('index.php?option=com_fitness&task=minigoal.edit&id='.(int) $item->id); ?>">
					<?php echo $this->escape($this->getMiniGoalName($item->mini_goal_category_id)); ?></a>
					
				</td>
                                
				<td>
					<?php echo $this->getPrimaryGoalName($item->primary_goal_id); ?>
				</td>
                                
                                <td>
                                        <?php echo $item->training_period; ?>
				</td>
                                <td>
					<?php echo $item->start_date; ?>
				</td>
				<td>
					<?php echo $item->deadline; ?>
				</td>
                                <td  id="status_button_place_<?php echo $item->id;?>">
                                        <?php echo $this->goals_model->status_html($item->id, $item->status, 'status_button') ?>
                                </td>
                                <td>
                                    <a href="<?php echo JRoute::_('index.php?option=com_fitness&task=minigoal.edit&id='.(int) $item->id); ?>">Edit/View </a>
				</td>


                <?php if (isset($this->items[0]->state)) { ?>
				    <td class="center">
					    <?php echo JHtml::_('jgrid.published', $item->state, $i, 'minigoals.', $canChange, 'cb'); ?>
				    </td>
                <?php } ?>
                <?php if (isset($this->items[0]->ordering)) { ?>
				    <td class="order">
					    <?php if ($canChange) : ?>
						    <?php if ($saveOrder) :?>
							    <?php if ($listDirn == 'asc') : ?>
								    <span><?php echo $this->pagination->orderUpIcon($i, true, 'minigoals.orderup', 'JLIB_HTML_MOVE_UP', $ordering); ?></span>
								    <span><?php echo $this->pagination->orderDownIcon($i, $this->pagination->total, true, 'minigoals.orderdown', 'JLIB_HTML_MOVE_DOWN', $ordering); ?></span>
							    <?php elseif ($listDirn == 'desc') : ?>
								    <span><?php echo $this->pagination->orderUpIcon($i, true, 'minigoals.orderdown', 'JLIB_HTML_MOVE_UP', $ordering); ?></span>
								    <span><?php echo $this->pagination->orderDownIcon($i, $this->pagination->total, true, 'minigoals.orderup', 'JLIB_HTML_MOVE_DOWN', $ordering); ?></span>
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


<script type="text/javascript">

    (function($) {
        $("#reset_filtered").click(function(){
            var limit = $("#limit").val();
            var form = $("#adminForm");
            form.find("select").val('');
            form.find("input").val('');
            $("#limit").val(limit);
            form.submit();
        });
        
        
        
        var mini_goal_status_options = {
            'fitness_administration_url' : '<?php echo JURI::root();?>administrator/index.php?option=com_fitness&tmpl=component&<?php echo JSession::getFormToken(); ?>=1',
            'calendar_frontend_url' : '<?php echo JURI::root()?>index.php?option=com_multicalendar&task=load&calid=0',
            'db_table' : '#__fitness_mini_goals',
            'status_button' : 'status_button',
            'status_button_dialog' : 'status_button_dialog',
            'dialog_status_wrapper' : 'dialog_status_wrapper',
            'dialog_status_template' : '#dialog_status_template',
            'status_button_template' : '#status_button_template',
            'status_button_place' : '#status_button_place_',
            'statuses' : {
                '4' : {'label' : 'EVALUATING', 'class' : 'goal_status_evaluating', 'email_alias' : 'GoalEvaluatingMini'}, 
                '5' : {'label' : 'IN PROGRESS', 'class' : 'goal_status_inprogress', 'email_alias' : 'GoalInprogressMini'},
                '6' : {'label' : 'ASSESSING', 'class' : 'goal_status_assessing', 'email_alias' : 'GoalAssessingMini'},
                '1' : {'label' : 'PENDING', 'class' : 'goal_status_pending', 'email_alias' : 'GoalPengingMini'},
                '2' : {'label' : 'COMPLETE', 'class' : 'goal_status_complete', 'email_alias' : 'GoalCompleteMini'}, 
                '3' : {'label' : 'INCOMPLETE', 'class' : 'goal_status_incomplete', 'email_alias' : 'GoalIncompleteMini'}
      
            },
            'statuses2' : {},
            'close_image' : '<?php echo JUri::root() ?>administrator/components/com_fitness/assets/images/close.png',
            'hide_image_class' : 'hideimage',
            'show_send_email' : true,
            setStatuses : function(item_id) {
                return this.statuses;
            }
        }
        
        
        
        var goal_status_mini = $.status(mini_goal_status_options);
        goal_status_mini.run();

    })($js);

</script>