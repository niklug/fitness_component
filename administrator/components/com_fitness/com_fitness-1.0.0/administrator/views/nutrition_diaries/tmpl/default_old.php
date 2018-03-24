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



require_once  JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_fitness' . DS .'helpers' . DS . 'fitness.php';

$helper = new FitnessHelper();
?>

<form action="<?php echo JRoute::_('index.php?option=com_fitness&view=nutrition_diaries'); ?>" method="post" name="adminForm" id="adminForm">
	<fieldset id="filter-bar">
		<div class="filter-search fltlft">
			<label class="filter-search-lbl" for="filter_search"><?php echo JText::_('Client Name'); ?></label>
			<input type="text" name="filter_search" id="filter_search" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" title="<?php echo JText::_('Search'); ?>" />
			<button type="submit"><?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?></button>
			<button type="button" id="reset_filtered"><?php echo JText::_('Reset All'); ?></button>
		</div>
		
		<div class='filter-select fltrt'>
			<?php //Filter for the field submit_date
			$selected_from_submit_date = JRequest::getVar('filter_from_submit_date');
			$selected_to_submit_date = JRequest::getVar('filter_to_submit_date');
                        ?>
                        <label class="filter-search-lbl" for="filter_from_active_start"><?php echo JText::_('Submit Date From:'); ?></label>
                        <?php
				echo JHtml::_('calendar', $selected_from_submit_date, 'filter_from_submit_date', 'filter_from_submit_date', '%Y-%m-%d', 'onchange="this.form.submit();"');
                        ?>
                        <label class="filter-search-lbl" for="filter_from_active_start"><?php echo JText::_('Submit Date To:'); ?></label>
                        <?php
				echo JHtml::_('calendar', $selected_to_submit_date, 'filter_to_submit_date', 'filter_to_submit_date', '%Y-%m-%d',  'onchange="this.form.submit();"');
			?>
		</div>
        
		<div class='filter-select fltrt'>
			<?php //Filter for the field entry_date
			$selected_from_entry_date = JRequest::getVar('filter_from_entry_date');
			$selected_to_entry_date = JRequest::getVar('filter_to_entry_date');
                        ?>
                        <label class="filter-search-lbl" for="filter_from_active_start"><?php echo JText::_('Entry Date From:'); ?></label>
                        <?php
				echo JHtml::_('calendar', $selected_from_entry_date, 'filter_from_entry_date', 'filter_from_entry_date', '%Y-%m-%d', 'onchange="this.form.submit();"');
                        ?>
                        <label class="filter-search-lbl" for="filter_from_active_start"><?php echo JText::_('Entry Date To:'); ?></label>
                        <?php          
				echo JHtml::_('calendar', $selected_to_entry_date, 'filter_to_entry_date', 'filter_to_entry_date', '%Y-%m-%d',  'onchange="this.form.submit();"');
			?>
		</div>
            
            
                <div class='filter-select fltrt'>
			<?php
			$selected_from_score = JRequest::getVar('filter_from_score');
			$selected_to_score = JRequest::getVar('filter_to_score');
                        ?>
                        <label class="filter-search-lbl" for="filter_from_score"><?php echo JText::_('Score From (%):'); ?></label>
                        <input maxlength="3" type="text" id="filter_from_score" name="filter_from_score" value="<?php echo $selected_from_score?>" onchange="this.form.submit();"/>

                        <label class="filter-search-lbl" for="filter_from_active_start"><?php echo JText::_('Score To (%):'); ?></label>
                        <input maxlength="3"  type="text" id="filter_to_score" name="filter_to_score" value="<?php echo $selected_to_score?>" onchange="this.form.submit();"/>
		</div>
            
            

	</fieldset>
    	         
    <fieldset style="border:none;">
        
        
        <div class='filter-select fltrt'>
            <select name="filter_published" class="inputbox" onchange="this.form.submit()">
                <option value="">-Published-</option>
                <?php echo JHtml::_('select.options', JHtml::_('jgrid.publishedOptions'), "value", "text", $this->state->get('filter.state'), true); ?>
            </select>
        </div>
        
                
        
        <?php
        
        $status[] = JHTML::_('select.option', FitnessHelper::INPROGRESS_DIARY_STATUS, 'IN PROGRESS' );
        $status[] = JHTML::_('select.option', FitnessHelper::PASS_DIARY_STATUS, 'PASS' );
        $status[] = JHTML::_('select.option', FitnessHelper::FAIL_DIARY_STATUS, 'FAIL' );
        $status[] = JHTML::_('select.option', FitnessHelper::DISTINCTION_DIARY_STATUS, 'DISTINCTION' );
        $status[] = JHTML::_('select.option', FitnessHelper::SUBMITTED_DIARY_STATUS, 'SUBMITTED' );

        ?>

        <div class='filter-select fltrt'>
                <select name="filter_diary_status" class="inputbox" onchange="this.form.submit()">
                        <option value=""><?php echo JText::_('-Status-');?></option>
                        <?php echo JHtml::_('select.options', $status, "value", "text", $this->state->get('filter.diary_status'), true);?>
                </select>
        </div>
        
        
        <?php
        
        $activity_level[] = JHTML::_('select.option', 1, $helper->_activity_level[1] );
        $activity_level[] = JHTML::_('select.option', 2, $helper->_activity_level[2]);
        $activity_level[] = JHTML::_('select.option', 3, $helper->_activity_level[3] );

        ?>

        <div class='filter-select fltrt'>
                <select name="filter_activity_level" class="inputbox" onchange="this.form.submit()">
                        <option value=""><?php echo JText::_('-Activity Level-');?></option>
                        <?php echo JHtml::_('select.options', $activity_level, "value", "text", $this->state->get('filter.activity_level'), true);?>
                </select>
        </div>

        <div class='filter-select fltrt'>
                <select name="filter_nutrition_focus" class="inputbox" onchange="this.form.submit()">
                        <option value=""><?php echo JText::_('-Nutrition Focus-');?></option>
                        <?php echo JHtml::_('select.options', $helper->getNutritionFocuses(), "id", "name", $this->state->get('filter.nutrition_focus'), true);?>
                </select>
        </div>
        
                
        <?php
        $db = JFactory::getDbo();

        $sql = "SELECT DISTINCT d.assessed_by AS value, u.name AS text FROM #__fitness_nutrition_diary  AS d LEFT JOIN #__users AS u ON d.assessed_by=u.id WHERE d.assessed_by !='0'";
        $db->setQuery($sql);
        if(!$db->query()) {
            JError::raiseError($db->getErrorMsg());
        }
        $assessed_by = $db->loadObjectList();

        ?>
        
        <div class='filter-select fltrt'>
                <select name="filter_assessed_by" class="inputbox" onchange="this.form.submit()">
                        <option value=""><?php echo JText::_('-Assessed By-');?></option>
                        <?php echo JHtml::_('select.options', $assessed_by, "value", "text", $this->state->get('filter.assessed_by'), true);?>
                </select>
        </div>

        <div class='filter-select fltrt'>
                <?php echo $helper->generateSelect($helper->getTrainersByUsergroup(), 'filter_primary_trainer', 'primary_trainer', $this->state->get('filter.primary_trainer'), 'Primary Trainer', false, 'inputbox'); ?>
        </div>
        
        <div class='filter-select fltrt'>
            <?php echo $helper->generateSelect($helper->getBusinessProfileList(), 'filter_business_profile_id', 'business_profile_id', $this->state->get('filter.business_profile_id') , 'Business Name', false, "inputbox"); ?>
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
				<?php echo JHtml::_('grid.sort',  'COM_FITNESS_NUTRITION_DIARIES_ENTRY_DATE', 'a.entry_date', $listDirn, $listOrder); ?>
				</th>
				<th class='left'>
				<?php echo JHtml::_('grid.sort',  'COM_FITNESS_NUTRITION_DIARIES_SUBMIT_DATE', 'a.submit_date', $listDirn, $listOrder); ?>
				</th>
				<th class='left'>
				<?php echo JHtml::_('grid.sort',  'COM_FITNESS_NUTRITION_DIARIES_CLIENT_ID', 'a.client_id', $listDirn, $listOrder); ?>
				</th>
				<th class='left'>
				<?php echo JHtml::_('grid.sort',  'COM_FITNESS_NUTRITION_DIARIES_TRAINER_ID', 'a.trainer_id', $listDirn, $listOrder); ?>
				</th>
				<th class='left'>
				<?php echo JHtml::_('grid.sort',  'COM_FITNESS_NUTRITION_DIARIES_ASSESSED_BY', 'a.assessed_by', $listDirn, $listOrder); ?>
				</th>
				<th class='left'>
                                    <?php echo JHtml::_('grid.sort',  'Activity Level', 'a.activity_level', $listDirn, $listOrder); ?>
				</th>
				<th class='left'>
				<?php echo JHtml::_('grid.sort',  'COM_FITNESS_NUTRITION_DIARIES_NUTRITION_FOCUS', 'nf.nutrition_focus_name', $listDirn, $listOrder); ?>
				</th>
				<th class='left'>
				<?php echo JHtml::_('grid.sort',  'COM_FITNESS_NUTRITION_DIARIES_STATUS', 'a.status', $listDirn, $listOrder); ?>
				</th>
				<th class='left'>
				<?php echo JHtml::_('grid.sort',  'COM_FITNESS_NUTRITION_DIARIES_SCORE', 'a.score', $listDirn, $listOrder); ?>
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
						<?php echo JHtml::_('grid.order',  $this->items, 'filesave.png', 'nutrition_diaries.saveorder'); ?>
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
                                    <a href="<?php echo JRoute::_('index.php?option=com_fitness&task=nutrition_diary.edit&id='.(int) $item->id); ?>">
                                    <?php
                                        $date = JFactory::getDate($item->entry_date);
                                        echo  $date->toFormat('%A, %d %b %Y');
                                    ?>
                                    </a>
   
				</td>
				<td>
					<?php 
                                        if($item->submit_date != '0000-00-00 00:00:00') {
                                            $date = JFactory::getDate($item->submit_date);
                                            echo  $date->toFormat('%A, %d %b %Y');
                                        } else {
                                            echo '-';
                                        }
                                        ?>
				</td>
				<td>
                                        <?php echo JFactory::getUser($item->client_id)->name; ?>
				</td>
				<td>
					<?php echo JFactory::getUser($item->trainer_id)->name; ?>
				</td>
				<td>
                                        <?php echo JFactory::getUser($item->assessed_by)->name; ?>
				</td>
				<td>
					<?php
                                            echo $helper->_activity_level[$item->activity_level];
                                        ?>
				</td>
				<td>
					<?php echo $item->nutrition_focus_name; ?>
				</td>
				<td>
                                    <div style="display: inline-block;" id="status_button_place_<?php echo $item->id;?>">
					<?php echo $this->model->status_html($item->id, $item->status); ?>
                                    </div>
                                    
				</td>
				<td>
					<?php
                                        if($item->score) {
                                           echo $item->score . '%'; 
                                        } else {
                                            echo '-';
                                        }
                                         ?>
				</td>


                <?php if (isset($this->items[0]->state)) { ?>
				    <td class="center">
					    <?php echo JHtml::_('jgrid.published', $item->state, $i, 'nutrition_diaries.', $canChange, 'cb'); ?>
				    </td>
                <?php } ?>
                <?php if (isset($this->items[0]->ordering)) { ?>
				    <td class="order">
					    <?php if ($canChange) : ?>
						    <?php if ($saveOrder) :?>
							    <?php if ($listDirn == 'asc') : ?>
								    <span><?php echo $this->pagination->orderUpIcon($i, true, 'nutrition_diaries.orderup', 'JLIB_HTML_MOVE_UP', $ordering); ?></span>
								    <span><?php echo $this->pagination->orderDownIcon($i, $this->pagination->total, true, 'nutrition_diaries.orderdown', 'JLIB_HTML_MOVE_DOWN', $ordering); ?></span>
							    <?php elseif ($listDirn == 'desc') : ?>
								    <span><?php echo $this->pagination->orderUpIcon($i, true, 'nutrition_diaries.orderdown', 'JLIB_HTML_MOVE_UP', $ordering); ?></span>
								    <span><?php echo $this->pagination->orderDownIcon($i, $this->pagination->total, true, 'nutrition_diaries.orderup', 'JLIB_HTML_MOVE_DOWN', $ordering); ?></span>
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


<fieldset>
    <legend>Batch process the selected items</legend>
    <div  id="batch_process_wrapper">
        
    </div>
</fieldset>



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
        
        $("#primary_trainer, #business_profile_id").on('change', function() {
                 var form = $("#adminForm");
                 form.submit();
        })
        
        
        
        
        var status_options = {
            'fitness_administration_url' : '<?php echo JURI::root();?>administrator/index.php?option=com_fitness&tmpl=component&<?php echo JSession::getFormToken(); ?>=1',
            'calendar_frontend_url' : '<?php echo JURI::root()?>index.php?option=com_multicalendar&task=load&calid=0',
            'db_table' : '#__fitness_nutrition_diary',
            'status_button' : 'status_button',
            'status_button_dialog' : 'status_button_dialog',
            'dialog_status_wrapper' : 'dialog_status_wrapper',
            'dialog_status_template' : '#dialog_status_template',
            'status_button_template' : '#status_button_template',
            'status_button_place' : '#status_button_place_',
            'statuses' : {
                '<?php echo FitnessHelper::PASS_DIARY_STATUS ?>' : {'label' : 'PASS', 'class' : 'status_pass', 'email_alias' : 'DiaryPass'},
                '<?php echo FitnessHelper::FAIL_DIARY_STATUS ?>' : {'label' : 'FAIL', 'class' : 'status_fail', 'email_alias' : 'DiaryFail'}, 
                '<?php echo FitnessHelper::DISTINCTION_DIARY_STATUS ?>' : {'label' : 'DISTINCTION', 'class' : 'status_distinction', 'email_alias' : 'DiaryDistinction'}
            },
            'statuses2' : {},
            'close_image' : '<?php echo JUri::root() ?>administrator/components/com_fitness/assets/images/close.png',
            'hide_image_class' : 'hideimage',
            'show_send_email' : true,
            
            setStatuses : function(item_id) {
                return this.statuses;
            },
            'set_updater' : true,
            'view' : 'NutritionDiary',
            'user_id' : '<?php echo JFactory::getUser()->id;?>',
            'set_score' : true,
            
            'batch_anabled' : true,
            'target_element' : '#batch_process_wrapper',
            'title' : 'Choose status to apply to selected nutrition diary entries',
            'email_checkbox_title' : 'Send notification email to all clients',
        }



        var batch_status = $.batch_status(status_options);
        
        batch_status.run();
        
        
        
        // status
        var score_status = $.status(status_options);
        score_status.run();
        
        
        
        
        

    })($js);

</script>