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
?>

<form action="<?php echo JRoute::_('index.php?option=com_fitness&view=nutrition_recipes'); ?>" method="post" name="adminForm" id="adminForm">
	<fieldset id="filter-bar">
                <div class='filter-select fltrt'>
                    <a class="active menu_link" href="index.php?option=com_fitness&view=nutritiondatabases">Nutrition Database</a>
                </div>
            
		<div class="filter-search fltlft">
			<label class="filter-search-lbl" for="filter_search"><?php echo JText::_('JSEARCH_FILTER_LABEL'); ?></label>
			<input type="text" name="filter_search" id="filter_search" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" title="<?php echo JText::_('Search'); ?>" />
			<button type="submit"><?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?></button>
			<button id="reset_filtered" type="button" ><?php echo JText::_('Reset All'); ?></button>
		</div>
		
               
	</fieldset>
    
    	<fieldset id="filter-bar">

		<div class='filter-select fltrt'>
			<select name="filter_published" class="inputbox" onchange="this.form.submit()">
				<option value=""><?php echo JText::_('JOPTION_SELECT_PUBLISHED');?></option>
				<?php echo JHtml::_('select.options', JHtml::_('jgrid.publishedOptions'), "value", "text", $this->state->get('filter.state'), true);?>
			</select>
		</div>
             
                <?php //Filter for the field created_by
                $db = JFactory::getDbo();
                $sql = "SELECT DISTINCT r.created_by AS value, u.name AS text FROM #__fitness_nutrition_recipes AS r 
                    LEFT JOIN #__users AS u ON u.id=r.created_by
                    WHERE r.state='1'";
                $db->setQuery($sql);
                if(!$db->query()) {
                    JError::raiseError($db->getErrorMsg());
                }
                $clients = $db->loadAssocList();
                ?>
                <div class='filter-select fltrt'>
                        <select name="filter_created_by" class="inputbox" onchange="this.form.submit()">
                                <option value=""><?php echo JText::_('-Author Name-');?></option>
                                <?php echo JHtml::_('select.options', $clients, "value", "text", $this->state->get('filter.created_by'), true);?>
                        </select>
                </div>
            
                <?php //Filter for the field created_by
                $db = JFactory::getDbo();
                $sql = "SELECT DISTINCT r.recipe_type AS value, t.name AS text FROM #__fitness_nutrition_recipes AS r 
                    LEFT JOIN #__fitness_recipe_types AS t ON t.id=r.recipe_type
                    WHERE r.state='1'";
                $db->setQuery($sql);
                if(!$db->query()) {
                    JError::raiseError($db->getErrorMsg());
                }
                $recipe_type = $db->loadAssocList();
                ?>
                <div class='filter-select fltrt'>
                        <select name="filter_recipe_type" class="inputbox" onchange="this.form.submit()">
                                <option value=""><?php echo JText::_('-Recipe Type-');?></option>
                                <?php echo JHtml::_('select.options', $recipe_type, "value", "text", $this->state->get('filter.recipe_type'), true);?>
                        </select>
                </div>
		
		<div class='filter-select fltrt'>
			<?php //Filter for the field created
			$selected_from_created = JRequest::getVar('filter_from_created');
			$selected_to_created = JRequest::getVar('filter_to_created');
                        echo '<label class="filter-search-lbl" for="filter_search">'. JText::_('Date From:') . '</label>';
                        echo JHtml::_('calendar', $selected_from_created, 'filter_from_created', 'filter_from_created', '%Y-%m-%d', 'onchange="this.form.submit();"');
                        echo '<label class="filter-search-lbl" for="filter_search">'. JText::_('Date To:') . '</label>';
			echo JHtml::_('calendar', $selected_to_created, 'filter_to_created', 'filter_to_created', '%Y-%m-%d',  'onchange="this.form.submit();"');
			?>
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
				<?php echo JHtml::_('grid.sort',  'COM_FITNESS_NUTRITION_RECIPES_RECIPE_NAME', 'a.recipe_name', $listDirn, $listOrder); ?>
				</th>
				<th class='left'>
				<?php echo JHtml::_('grid.sort',  'COM_FITNESS_NUTRITION_RECIPES_RECIPE_TYPE', 'a.recipe_type', $listDirn, $listOrder); ?>
				</th>
				<th class='left'>
				<?php echo JHtml::_('grid.sort',  'Author Name', 'a.created_by', $listDirn, $listOrder); ?>
				</th>
				<th class='left'>
				<?php echo JHtml::_('grid.sort',  'COM_FITNESS_NUTRITION_RECIPES_CREATED', 'a.created', $listDirn, $listOrder); ?>
				</th>
                                 <th class='left'>
				<?php echo JHtml::_('grid.sort',  'COM_FITNESS_NUTRITIONDATABASES_CALORIES', 'a.calories', $listDirn, $listOrder); ?>
				</th>
				<th class='left'>
				<?php echo JHtml::_('grid.sort',  'COM_FITNESS_NUTRITIONDATABASES_ENERGY', 'a.energy', $listDirn, $listOrder); ?>
				</th>
				<th class='left'>
				<?php echo JHtml::_('grid.sort',  'COM_FITNESS_NUTRITIONDATABASES_PROTEIN', 'a.protein', $listDirn, $listOrder); ?>
				</th>
				<th class='left'>
				<?php echo JHtml::_('grid.sort',  'COM_FITNESS_NUTRITIONDATABASES_FATS', 'a.fats', $listDirn, $listOrder); ?>
				</th>
                                <th class='left'>
				<?php echo JHtml::_('grid.sort',  'COM_FITNESS_NUTRITIONDATABASES_SATURATED_FAT', 'a.saturated_fat', $listDirn, $listOrder); ?>
				</th>
				<th class='left'>
				<?php echo JHtml::_('grid.sort',  'COM_FITNESS_NUTRITIONDATABASES_CARBS', 'a.carbs', $listDirn, $listOrder); ?>
				</th>

				<th class='left'>
				<?php echo JHtml::_('grid.sort',  'COM_FITNESS_NUTRITIONDATABASES_TOTAL_SUGARS', 'a.total_sugars', $listDirn, $listOrder); ?>
				</th>
				<th class='left'>
				<?php echo JHtml::_('grid.sort',  'COM_FITNESS_NUTRITIONDATABASES_SODIUM', 'a.sodium', $listDirn, $listOrder); ?>
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
						<?php echo JHtml::_('grid.order',  $this->items, 'filesave.png', 'nutrition_recipes.saveorder'); ?>
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
				<td colspan="16">
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
				<?php if (isset($item->checked_out) && $item->checked_out) : ?>
					<?php echo JHtml::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time, 'nutrition_recipes.', $canCheckin); ?>
				<?php endif; ?>
				<?php if ($canEdit) : ?>
					<a href="<?php echo JRoute::_('index.php?option=com_fitness&task=nutrition_recipe.edit&id='.(int) $item->id); ?>">
					<?php echo $this->escape($item->recipe_name); ?></a>
				<?php else : ?>
					<?php echo $this->escape($item->recipe_name); ?>
				<?php endif; ?>
				</td>
				<td>
					<?php echo $this->getRecipeTypeByName($item->recipe_type); ?>
				</td>
				<td>
					<?php echo $item->created_by; ?>
				</td>
				<td>
					<?php echo $item->created; ?>
				</td>
                                <td>
                                        <?php echo $item->calories; ?>
                                </td>
                                <td>
                                        <?php echo $item->energy; ?>
                                </td>
                                <td>
                                        <?php echo $item->protein; ?>
                                </td>
                                <td>
                                        <?php echo $item->fats; ?>
                                </td>
                                <td>
                                        <?php echo $item->saturated_fat; ?>
                                </td>
                                <td>
                                        <?php echo $item->carbs; ?>
                                </td>
                                <td>
                                        <?php echo $item->total_sugars; ?>
                                </td>
                                <td>
                                        <?php echo $item->sodium; ?>
                                </td>
                                <td>
                                      <a onclick="sendEmail('<?php echo $item->id ?>', 'Recipe')" class="send_email_button"></a>  
                                </td>

                <?php if (isset($this->items[0]->state)) { ?>
				    <td class="center">
					    <?php echo JHtml::_('jgrid.published', $item->state, $i, 'nutrition_recipes.', $canChange, 'cb'); ?>
				    </td>
                <?php } ?>
                <?php if (isset($this->items[0]->ordering)) { ?>
				    <td class="order">
					    <?php if ($canChange) : ?>
						    <?php if ($saveOrder) :?>
							    <?php if ($listDirn == 'asc') : ?>
								    <span><?php echo $this->pagination->orderUpIcon($i, true, 'nutrition_recipes.orderup', 'JLIB_HTML_MOVE_UP', $ordering); ?></span>
								    <span><?php echo $this->pagination->orderDownIcon($i, $this->pagination->total, true, 'nutrition_recipes.orderdown', 'JLIB_HTML_MOVE_DOWN', $ordering); ?></span>
							    <?php elseif ($listDirn == 'desc') : ?>
								    <span><?php echo $this->pagination->orderUpIcon($i, true, 'nutrition_recipes.orderdown', 'JLIB_HTML_MOVE_UP', $ordering); ?></span>
								    <span><?php echo $this->pagination->orderDownIcon($i, $this->pagination->total, true, 'nutrition_recipes.orderup', 'JLIB_HTML_MOVE_DOWN', $ordering); ?></span>
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

    $(document).ready(function(){
        $("#reset_filtered").click(function(){
            var form = $("#adminForm");
            form.find("select").val('');
            form.find("input").val('');
            form.submit();
        });

    });
  
    
    function sendEmail(recipe_id, method) {
        var url = '<?php echo JURI::root()?>index.php?option=com_multicalendar&task=load&calid=0&method=send' + method + 'Email';
        $.ajax({
                type : "POST",
                url : url,
                data : {
                    recipe_id : recipe_id
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