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

//Load admin language file
$lang = JFactory::getLanguage();
$lang->load('com_fitness', JPATH_ADMINISTRATOR);
$canEdit = JFactory::getUser()->authorise('core.edit', 'com_fitness');
if (!$canEdit && JFactory::getUser()->authorise('core.edit.own', 'com_fitness')) {
	$canEdit = JFactory::getUser()->id == $this->item->created_by;
}
?>
<?php if ($this->item) : ?>

    <div class="item_fields">

        <ul class="fields_list">

            			<li><?php echo JText::_('COM_FITNESS_FORM_LBL_NUTRITION_DIARY_ID'); ?>:
			<?php echo $this->item->id; ?></li>
			<li><?php echo JText::_('COM_FITNESS_FORM_LBL_NUTRITION_DIARY_ENTRY_DATE'); ?>:
			<?php echo $this->item->entry_date; ?></li>
			<li><?php echo JText::_('COM_FITNESS_FORM_LBL_NUTRITION_DIARY_SUBMIT_DATE'); ?>:
			<?php echo $this->item->submit_date; ?></li>
			<li><?php echo JText::_('COM_FITNESS_FORM_LBL_NUTRITION_DIARY_CLIENT_ID'); ?>:
			<?php echo $this->item->client_id; ?></li>
			<li><?php echo JText::_('COM_FITNESS_FORM_LBL_NUTRITION_DIARY_TRAINER_ID'); ?>:
			<?php echo $this->item->trainer_id; ?></li>
			<li><?php echo JText::_('COM_FITNESS_FORM_LBL_NUTRITION_DIARY_ASSESSED_BY'); ?>:
			<?php echo $this->item->assessed_by; ?></li>
			<li><?php echo JText::_('COM_FITNESS_FORM_LBL_NUTRITION_DIARY_PRIMARY_GOAL'); ?>:
			<?php echo $this->item->primary_goal; ?></li>
			<li><?php echo JText::_('COM_FITNESS_FORM_LBL_NUTRITION_DIARY_NUTRITION_FOCUS'); ?>:
			<?php echo $this->item->nutrition_focus; ?></li>
			<li><?php echo JText::_('COM_FITNESS_FORM_LBL_NUTRITION_DIARY_STATUS'); ?>:
			<?php echo $this->item->status; ?></li>
			<li><?php echo JText::_('COM_FITNESS_FORM_LBL_NUTRITION_DIARY_SCORE'); ?>:
			<?php echo $this->item->score; ?></li>
			<li><?php echo JText::_('COM_FITNESS_FORM_LBL_NUTRITION_DIARY_TRAINER_COMMENTS'); ?>:
			<?php echo $this->item->trainer_comments; ?></li>
			<li><?php echo JText::_('COM_FITNESS_FORM_LBL_NUTRITION_DIARY_STATE'); ?>:
			<?php echo $this->item->state; ?></li>


        </ul>

    </div>
    <?php if($canEdit): ?>
		<a href="<?php echo JRoute::_('index.php?option=com_fitness&task=nutrition_diary.edit&id='.$this->item->id); ?>"><?php echo JText::_("COM_FITNESS_EDIT_ITEM"); ?></a>
	<?php endif; ?>
								<?php if(JFactory::getUser()->authorise('core.delete','com_fitness')):
								?>
									<a href="javascript:document.getElementById('form-nutrition_diary-delete-<?php echo $this->item->id ?>').submit()"><?php echo JText::_("COM_FITNESS_DELETE_ITEM"); ?></a>
									<form id="form-nutrition_diary-delete-<?php echo $this->item->id; ?>" style="display:inline" action="<?php echo JRoute::_('index.php?option=com_fitness&task=nutrition_diary.remove'); ?>" method="post" class="form-validate" enctype="multipart/form-data">
										<input type="hidden" name="jform[id]" value="<?php echo $this->item->id; ?>" />
										<input type="hidden" name="jform[entry_date]" value="<?php echo $this->item->entry_date; ?>" />
										<input type="hidden" name="jform[submit_date]" value="<?php echo $this->item->submit_date; ?>" />
										<input type="hidden" name="jform[client_id]" value="<?php echo $this->item->client_id; ?>" />
										<input type="hidden" name="jform[trainer_id]" value="<?php echo $this->item->trainer_id; ?>" />
										<input type="hidden" name="jform[assessed_by]" value="<?php echo $this->item->assessed_by; ?>" />
										<input type="hidden" name="jform[primary_goal]" value="<?php echo $this->item->primary_goal; ?>" />
										
										<input type="hidden" name="jform[nutrition_focus]" value="<?php echo $this->item->nutrition_focus; ?>" />
										<input type="hidden" name="jform[status]" value="<?php echo $this->item->status; ?>" />
										<input type="hidden" name="jform[score]" value="<?php echo $this->item->score; ?>" />
										<input type="hidden" name="jform[trainer_comments]" value="<?php echo $this->item->trainer_comments; ?>" />
										<input type="hidden" name="jform[state]" value="<?php echo $this->item->state; ?>" />
										<input type="hidden" name="option" value="com_fitness" />
										<input type="hidden" name="task" value="nutrition_diary.remove" />
										<?php echo JHtml::_('form.token'); ?>
									</form>
								<?php
								endif;
							?>
<?php
else:
    echo JText::_('COM_FITNESS_ITEM_NOT_LOADED');
endif;
?>
