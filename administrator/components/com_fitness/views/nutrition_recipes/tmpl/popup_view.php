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

$nutrition_plan_id = JRequest::getVar('nutrition_plan_id');
$meal_id = JRequest::getVar('meal_id');
$type = JRequest::getVar('type');
$parent_view  = JRequest::getVar('parent_view');

?>
<h1>SELECT A RECIPE</h1>
<form action="<?php echo JRoute::_('index.php?option=com_fitness&view=nutrition_recipes&tmpl=component&layout=popup_view&nutrition_plan_id=' . $nutrition_plan_id . '&meal_id=' . $meal_id . '&type=' . $type . '&parent_view=' . $parent_view); ?>" method="post" name="adminForm" id="adminForm">
	<fieldset id="filter-bar">
		<div class="filter-search fltlft">
			<label class="filter-search-lbl" for="filter_search"><?php echo JText::_('Recipe Name: '); ?></label>
			<input type="text" name="filter_search" id="filter_search" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" title="<?php echo JText::_('Search'); ?>" />
			<button type="submit"><?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?></button>
			<button id="reset_filtered" type="button" ><?php echo JText::_('Reset All'); ?></button>
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

	</fieldset>
    
	<div class="clr"> </div>

	<table class="adminlist">
		<thead>
			<tr>

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
                                     <?php echo JText::_('COM_FITNESS_NUTRITIONDATABASES_CALORIES'); ?>
				</th>
				<th class='left'>
                                    <?php echo JText::_('COM_FITNESS_NUTRITIONDATABASES_ENERGY'); ?>
				</th>
				<th class='left'>
                                    <?php echo JText::_('COM_FITNESS_NUTRITIONDATABASES_PROTEIN'); ?>
				</th>
				<th class='left'>
                                    <?php echo JText::_('Fats (g)'); ?>
				</th>
				<th class='left'>
                                    <?php echo JText::_('Carbs (g)'); ?>
				</th>
				<th class='left'>
                                    <?php echo JText::_('COM_FITNESS_NUTRITIONDATABASES_TOTAL_SUGARS'); ?>
				</th>
				<th class='left'>
                                    <?php echo JText::_('COM_FITNESS_NUTRITIONDATABASES_SODIUM'); ?>
				</th>
                                <th class='left'>
                                    View
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
		

				<td>
        				<?php echo $this->escape($item->recipe_name); ?></a>
	
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
                                        <?php echo $item->carbs; ?>
                                </td>
                                <td>
                                        <?php echo $item->total_sugars; ?>
                                </td>
                                <td>
                                        <?php echo $item->sodium; ?>
                                </td>
                                <td>
                                    <?php
                                        $url = 'index.php?option=com_fitness&tmpl=component&view=nutrition_recipe&layout=popup_view&nutrition_plan_id=' . $nutrition_plan_id . '&meal_id=' . $meal_id . '&type=' . $type . '&parent_view=' . $parent_view . '&id='.(int) $item->id;
                                    ?>
                                    <a href="<?php echo JRoute::_($url); ?>">View</a>
                                </td>

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


    })($js);
    
    
</script>
