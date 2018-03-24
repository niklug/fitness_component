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

<form action="<?php echo JRoute::_('index.php?option=com_fitness&view=nutrition_plans'); ?>" method="post" name="adminForm" id="adminForm">
	<fieldset id="filter-bar">
		<div class="filter-search fltlft">
			<label class="filter-search-lbl" for="filter_search"><?php echo JText::_('Client Name: '); ?></label>
			<input type="text" name="filter_search" id="filter_search" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" title="<?php echo JText::_('Search'); ?>" />
			<button type="submit"><?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?></button>
			<button type="button" id="reset_filtered"><?php echo JText::_('Reset All'); ?></button>
		</div>
            
            
		<div class='filter-select fltrt'>
			<?php //Filter for the field active_finish
			$selected_from_active_finish = JRequest::getVar('filter_from_active_finish');
			$selected_to_active_finish = JRequest::getVar('filter_to_active_finish');
                        ?>
                        <label class="filter-search-lbl" for="filter_from_active_finish"><?php echo JText::_('Active Finish from:'); ?></label>
                        <?php
				echo JHtml::_('calendar', $selected_from_active_finish, 'filter_from_active_finish', 'filter_from_active_finish', '%Y-%m-%d', 'onchange="this.form.submit();"');
                        ?>
                        <label class="filter-search-lbl" for="filter_to_active_finish"><?php echo JText::_('Active Finish To:'); ?></label>
                        <?php
				echo JHtml::_('calendar', $selected_to_active_finish, 'filter_to_active_finish', 'filter_to_active_finish', '%Y-%m-%d',  'onchange="this.form.submit();"');
			?>
		</div>		
        
		<div class='filter-select fltrt'>
			<?php //Filter for the field active_start
			$selected_from_active_start = JRequest::getVar('filter_from_active_start');
			$selected_to_active_start = JRequest::getVar('filter_to_active_start');
                        ?>
                        <label class="filter-search-lbl" for="filter_from_active_start"><?php echo JText::_('Active Start from:'); ?></label>
                        <?php
				echo JHtml::_('calendar', $selected_from_active_start, 'filter_from_active_start', 'filter_from_active_start', '%Y-%m-%d', 'onchange="this.form.submit();"');
                        ?>
                        <label class="filter-search-lbl" for="filter_to_active_start"><?php echo JText::_('Active Start To:'); ?></label>
                        <?php
				echo JHtml::_('calendar', $selected_to_active_start, 'filter_to_active_start', 'filter_to_active_start', '%Y-%m-%d',  'onchange="this.form.submit();"');
			?>
		</div>
            

	</fieldset>
            
        <fieldset style="border:none;">
            <div class='filter-select fltrt'>
                <select name="filter_published" class="inputbox" onchange="this.form.submit()">
                    <option value=""><?php echo JText::_('JOPTION_SELECT_PUBLISHED'); ?></option>
                    <?php echo JHtml::_('select.options', JHtml::_('jgrid.publishedOptions'), "value", "text", $this->state->get('filter.state'), true); ?>
                </select>
            </div>


            <div class='filter-select fltrt'>
                    <select name="filter_nutrition_focus" class="inputbox" onchange="this.form.submit()">
                            <option value=""><?php echo JText::_('-Nutrition Focus-');?></option>
                            <?php echo JHtml::_('select.options', $helper->getNutritionFocuses(), "id", "name", $this->state->get('filter.nutrition_focus'), true);?>
                    </select>
            </div>
                            
            <div class='filter-select fltrt'>
                    <select name="filter_mini_goal" class="inputbox" onchange="this.form.submit()">
                            <option value=""><?php echo JText::_('-Mini Goal-');?></option>
                            <?php echo JHtml::_('select.options', $helper->getMiniGoalCategory(), "id", "name", $this->state->get('filter.mini_goal'), true);?>
                    </select>
            </div>
            <div class='filter-select fltrt'>
                    <select name="filter_primary_goal" class="inputbox" onchange="this.form.submit()">
                            <option value=""><?php echo JText::_('-Primary Goal-');?></option>
                            <?php echo JHtml::_('select.options', $helper->getPrimaryGoalCategory(), "id", "name", $this->state->get('filter.primary_goal'), true);?>
                    </select>
            </div>
            
            
            
            
            <?php
            $force_active[] = JHTML::_('select.option', '1', 'Force Active' );
            $force_active[] = JHTML::_('select.option', '2', 'Force Inactive' );
            ?>
            <div class='filter-select fltrt'>
                    <select name="filter_force_active" class="inputbox" onchange="this.form.submit()">
                            <option value=""><?php echo JText::_('-Force Active-');?></option>
                            <?php echo JHtml::_('select.options', $force_active, "value", "text", $this->state->get('filter.force_active'), true);?>
                    </select>
            </div>
            
            <?php
            $active[] = JHTML::_('select.option', '1', 'Active' );
            $active[] = JHTML::_('select.option', '2', 'Inactive' );
            ?>
            <div class='filter-select fltrt'>
                    <select name="filter_active" class="inputbox" onchange="this.form.submit()">
                            <option value=""><?php echo JText::_('-Active-');?></option>
                            <?php echo JHtml::_('select.options', $active, "value", "text", $this->state->get('filter.active'), true);?>
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
				<?php echo JHtml::_('grid.sort',  'COM_FITNESS_NUTRITION_PLANS_CLIENT_ID', 'u.name', $listDirn, $listOrder); ?>
				</th>
				<th class='left'>
				<?php echo JHtml::_('grid.sort',  'COM_FITNESS_NUTRITION_PLANS_TRAINER_ID', 'u.name', $listDirn, $listOrder); ?>
				</th>
				<th class='left'>
				<?php echo JHtml::_('grid.sort',  'COM_FITNESS_NUTRITION_PLANS_active_start', 'u.name', $listDirn, $listOrder); ?>
				</th>
				<th class='left'>
				<?php echo JHtml::_('grid.sort',  'COM_FITNESS_NUTRITION_PLANS_active_finish', 'a.active_finish', $listDirn, $listOrder); ?>
				</th>
				<th class='left'>
				<?php echo JHtml::_('grid.sort',  'COM_FITNESS_NUTRITION_PLANS_ACTIVE', 'a.active', $listDirn, $listOrder); ?>
				</th>
				<th class='left'>
				<?php echo JHtml::_('grid.sort',  'COM_FITNESS_NUTRITION_PLANS_FORCE_ACTIVE', 'a.force_active', $listDirn, $listOrder); ?>
				</th>
				<th class='left'>
				<?php echo JHtml::_('grid.sort',  'COM_FITNESS_NUTRITION_PLANS_PRIMARY_GOAL', 'primary_goal_name', $listDirn, $listOrder); ?>
				</th>
                                
                                <th class='left'>
				<?php echo JHtml::_('grid.sort',  'Mini Goal', 'mini_goal_name', $listDirn, $listOrder); ?>
				</th>
                                
                                
				<th class='left'>
				<?php echo JHtml::_('grid.sort',  'COM_FITNESS_NUTRITION_PLANS_NUTRITION_FOCUS', 'a.nutrition_focus_name', $listDirn, $listOrder); ?>
				</th>
                                
                               
                                <th class='left'>
				<?php echo JHtml::_('grid.sort',  'COM_FITNESS_NUTRITION_RECIPES_CREATED', 'a.created', $listDirn, $listOrder); ?>
				</th>
                                <th style="width:1%;" class='left'>
                                    Notify
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
						<?php echo JHtml::_('grid.order',  $this->items, 'filesave.png', 'nutrition_plans.saveorder'); ?>
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
                    $colspan = 15;
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
                                    <a href="<?php echo JRoute::_('index.php?option=com_fitness&task=nutrition_plan.edit&id='.(int) $item->id); ?>">
					<?php echo $this->escape(JFactory::getUser($item->client_id)->name); ?></a>
						</td>
				<td>
					<?php echo JFactory::getUser($item->trainer_id)->name; ?>
				</td>
				<td>
					<?php echo $item->active_start; ?>
				</td>
				<td>
					<?php 
                                        $active_finish =  $item->active_finish;
                                        if($active_finish == '9999-12-31') $active_finish = 'Infinitely';
                                        echo $active_finish;
                                        ?>
				</td>
				<td>
					<?php
                                        
                                        $active_plan_id = $this->model->getUserActivePlanId($item->client_id);
                                        echo $this->showActiveStatus($item->id, $active_plan_id); ?>
				</td>
				<td>
					<?php echo $this->showStatus($item->force_active); ?>
				</td>
				<td>
					<?php echo $item->primary_goal_name; ?>
				</td>
                                <td>
					<?php echo $item->mini_goal_name; ?>
				</td>
				<td>
					<?php echo $item->nutrition_focus_name; ?>
				</td>
                                    
                                <td>
					<?php echo $item->created; ?>
				</td>
                                
                                <td>
                                      <a data-id='<?php echo $item->id ?>' onclick="javascript:void(0)" class="send_email_button"></a>  
                                </td>


                <?php if (isset($this->items[0]->state)) { ?>
				    <td class="center">
					    <?php echo JHtml::_('jgrid.published', $item->state, $i, 'nutrition_plans.', $canChange, 'cb'); ?>
				    </td>
                <?php } ?>
                <?php if (isset($this->items[0]->ordering)) { ?>
				    <td class="order">
					    <?php if ($canChange) : ?>
						    <?php if ($saveOrder) :?>
							    <?php if ($listDirn == 'asc') : ?>
								    <span><?php echo $this->pagination->orderUpIcon($i, true, 'nutrition_plans.orderup', 'JLIB_HTML_MOVE_UP', $ordering); ?></span>
								    <span><?php echo $this->pagination->orderDownIcon($i, $this->pagination->total, true, 'nutrition_plans.orderdown', 'JLIB_HTML_MOVE_DOWN', $ordering); ?></span>
							    <?php elseif ($listDirn == 'desc') : ?>
								    <span><?php echo $this->pagination->orderUpIcon($i, true, 'nutrition_plans.orderdown', 'JLIB_HTML_MOVE_UP', $ordering); ?></span>
								    <span><?php echo $this->pagination->orderDownIcon($i, $this->pagination->total, true, 'nutrition_plans.orderup', 'JLIB_HTML_MOVE_DOWN', $ordering); ?></span>
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
        
        $("#business_profile_id").on('change', function() {
             var form = $("#adminForm");
             form.submit();
             
        })
        
        $(".send_email_button").on('click', function() {
             var id = $(this).attr('data-id');
             sendEmail(id, 'Notify') 
        })
        
        function sendEmail(id, method) {
            var data = {};
            var url = '<?php echo JURI::root();?>administrator/index.php?option=com_fitness&tmpl=component&<?php echo JSession::getFormToken(); ?>=1';
            var view = '';
            var task = 'ajax_email';
            var table = '';

            data.id = id;
            data.view = 'NutritionPlan';
            data.method = method;


            $.AjaxCall(data, url, view, task, table, function(output){
                console.log(output);
                var emails = output.split(',');
                var message = 'Emails were sent to: ' +  "</br>";
                $.each(emails, function(index, email) { 
                    message += email +  "</br>";
                });
                $("#emais_sended").append(message);
            });
        }



        $("#primary_trainer").on('change', function() {
                 var form = $("#adminForm");
                 form.submit();
        })
        
    })($js);
</script>