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
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.keepalive');
// Import CSS
$document = JFactory::getDocument();
$document->addStyleSheet('components/com_fitness/assets/css/fitness.css');

$session = &JFactory::getSession();

$primary_goal_id = $session->get('primary_goal_id');

?>
<style type="text/css">
#jform_details-lbl, #jform_comments-lbl {
    float: none;
}

</style>
<script type="text/javascript">

    (function($) {


        Joomla.submitbutton = function(task)
        {
            if (task == 'minigoal.cancel') {
                Joomla.submitform(task, document.getElementById('minigoal-form'));
            }
            else{

                if (task != 'minigoal.cancel' && document.formvalidator.isValid(document.id('minigoal-form'))) {

                    Joomla.submitform(task, document.getElementById('minigoal-form'));
                }
                else {
                    alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED')); ?>');
                }
            }
        }
     })($js);

    
</script>

</script>

<form action="<?php echo JRoute::_('index.php?option=com_fitness&layout=edit&id=' . (int) $this->item->id); ?>" method="post" enctype="multipart/form-data" name="adminForm" id="minigoal-form" class="form-validate">
    <div class="width-60 fltlft">
        <fieldset class="adminform">
            <legend><?php echo JText::_('COM_FITNESS_LEGEND_MINIGOAL'); ?></legend>
            <ul class="adminformlist">
                <input id="jform_primary_goal_id" class="inputbox" type="hidden" value="<?php echo $primary_goal_id?>" name="jform[primary_goal_id]">

				<li><?php echo $this->form->getLabel('mini_goal_category_id'); ?>
				<?php echo $this->form->getInput('mini_goal_category_id'); ?></li>
                                <li><?php echo $this->form->getLabel('training_period_id'); ?>
				<?php echo $this->form->getInput('training_period_id'); ?></li> 
                                <li><?php echo $this->form->getLabel('start_date'); ?>
				<?php echo $this->form->getInput('start_date'); ?></li>
				<li><?php echo $this->form->getLabel('deadline'); ?>
				<?php echo $this->form->getInput('deadline'); ?></li>
                                <li><?php echo $this->form->getLabel('status'); ?>
				<?php echo $this->form->getInput('status'); ?></li>
				<li><?php echo $this->form->getLabel('details'); ?>
				<?php echo $this->form->getInput('details'); ?></li>
				<li><?php echo $this->form->getLabel('comments'); ?>
				<?php echo $this->form->getInput('comments'); ?></li>
				<li><?php echo $this->form->getLabel('state'); ?>
				<?php echo $this->form->getInput('state'); ?></li>


            </ul>
        </fieldset>
    </div>

    

    <input type="hidden" name="task" value="" />
    <?php echo JHtml::_('form.token'); ?>
    <div class="clr"></div>

    <style type="text/css">
        /* Temporary fix for drifting editor fields */
        .adminformlist li {
            clear: both;
        }
    </style>
</form>