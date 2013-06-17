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

<form action="<?php echo JRoute::_('index.php?option=com_fitness&view=clients'); ?>" method="post" name="adminForm" id="adminForm">
	<fieldset id="filter-bar">
		<div class="filter-search fltlft">
			<label class="filter-search-lbl" for="filter_search"><?php echo JText::_('JSEARCH_FILTER_LABEL'); ?></label>
			<input type="text" name="filter_search" id="filter_search" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" title="<?php echo JText::_('Search'); ?>" />
			<button type="submit"><?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?></button>
			<button type="button" onclick="document.id('filter_search').value='';this.form.submit();"><?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?></button>
		</div>
            
                <div class='filter-select fltrt'>
			<select name="filter_published" class="inputbox" onchange="this.form.submit()">
				<option value=""><?php echo JText::_('JOPTION_SELECT_PUBLISHED');?></option>
				<?php echo JHtml::_('select.options', JHtml::_('jgrid.publishedOptions'), "value", "text", $this->state->get('filter.state'), true);?>
			</select>
		</div>
            
                <?php
                $db = JFactory::getDbo();
                
                $sql = "SELECT id AS value, username AS text FROM #__users INNER JOIN #__user_usergroup_map ON #__user_usergroup_map.user_id=#__users.id WHERE #__user_usergroup_map.group_id=(SELECT id FROM #__usergroups WHERE title='Trainers')";
                $db->setQuery($sql);
                $primary_trainerlist = $db->loadObjectList();
                if(isset($primary_trainerlist)) {
                    foreach ($primary_trainerlist as $option) {
                        $primary_trainer[] = JHTML::_('select.option', $option->value, $option->text );
                    }
                }
                
                ?>

                <div class='filter-select fltrt'>
			<select name="filter_primary_trainer" class="inputbox" onchange="this.form.submit()">
				<option value=""><?php echo JText::_('-Primary Trainer-');?></option>
				<?php echo JHtml::_('select.options', $primary_trainer, "value", "text", $this->state->get('filter.primary_trainer'), true);?>
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

	</fieldset>
	<div class="clr"> </div>

	<table class="adminlist">
		<thead>
			<tr>
				<th width="1%">
					<input type="checkbox" name="checkall-toggle" value="" onclick="checkAll(this)" />
				</th>
                                
                                <th class='left'>
				<?php echo JHtml::_('grid.sort',  'Name', 'u.name', $listDirn, $listOrder); ?>
				</th>
                                
                                <th class='left'>
				<?php echo JHtml::_('grid.sort',  'Username', 'u.username', $listDirn, $listOrder); ?>
				</th>
                                
                                <th class='left'>
				<?php echo JHtml::_('grid.sort',  'User Group', 'a.user_group', $listDirn, $listOrder); ?>
				</th>

				<th class='left'>
				<?php echo JHtml::_('grid.sort',  'Email Adress', 'u.email', $listDirn, $listOrder); ?>
				</th>
				<th class='left'>
				<?php echo JHtml::_('grid.sort',  'COM_FITNESS_CLIENTS_PRIMARY_TRAINER', 'a.primary_trainer', $listDirn, $listOrder); ?>
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
						<?php echo JHtml::_('grid.order',  $this->items, 'filesave.png', 'clients.saveorder'); ?>
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
                        //$canChange = 1;
			?>
			<tr class="row<?php echo $i % 2; ?>">
				<td class="center">
					<?php echo JHtml::_('grid.id', $i, $item->id); ?>
				</td>
                                <td>
					<?php echo $item->name; ?>
				</td>
                                <td>
					<?php echo $item->username;?>
				</td>
                                <td>
					<?php echo $item->usergroup; ?>
				</td>
				<td>
					<?php echo $item->email; ?>
				</td>
				<td>
					<?php 
                                        $user = JFactory::getUser($item->primary_trainer);
                                        
                                        echo $user->username; 
                                        ?>
				</td>
	

                <?php if (isset($this->items[0]->state)) { ?>
				    <td class="center">
					    <?php echo JHtml::_('jgrid.published', $item->state, $i, 'clients.', $canChange, 'cb'); ?>
				    </td>
                <?php } ?>
                <?php if (isset($this->items[0]->ordering)) { ?>
				    <td class="order">
					    <?php if ($canChange) : ?>
						    <?php if ($saveOrder) :?>
							    <?php if ($listDirn == 'asc') : ?>
								    <span><?php echo $this->pagination->orderUpIcon($i, true, 'clients.orderup', 'JLIB_HTML_MOVE_UP', $ordering); ?></span>
								    <span><?php echo $this->pagination->orderDownIcon($i, $this->pagination->total, true, 'clients.orderdown', 'JLIB_HTML_MOVE_DOWN', $ordering); ?></span>
							    <?php elseif ($listDirn == 'desc') : ?>
								    <span><?php echo $this->pagination->orderUpIcon($i, true, 'clients.orderdown', 'JLIB_HTML_MOVE_UP', $ordering); ?></span>
								    <span><?php echo $this->pagination->orderDownIcon($i, $this->pagination->total, true, 'clients.orderup', 'JLIB_HTML_MOVE_DOWN', $ordering); ?></span>
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