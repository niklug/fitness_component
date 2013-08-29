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
?>
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
        js = jQuery.noConflict();
        js(document).ready(function(){
            

            Joomla.submitbutton = function(task)
            {
                if (task == 'nutrition_diary.cancel') {
                    Joomla.submitform(task, document.getElementById('nutrition_diary-form'));
                }
                else{
                    
                    if (task != 'nutrition_diary.cancel' && document.formvalidator.isValid(document.id('nutrition_diary-form'))) {
                        
                        Joomla.submitform(task, document.getElementById('nutrition_diary-form'));
                    }
                    else {
                        alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED')); ?>');
                    }
                }
            }
        });
    });
</script>

<form action="<?php echo JRoute::_('index.php?option=com_fitness&layout=edit&id=' . (int) $this->item->id); ?>" method="post" enctype="multipart/form-data" name="adminForm" id="nutrition_diary-form" class="form-validate">
    <div class="width-60 fltlft">
        <fieldset class="adminform">
            <legend><?php echo JText::_('COM_FITNESS_LEGEND_NUTRITION_DIARY'); ?></legend>
            <ul class="adminformlist">

                				<li><?php echo $this->form->getLabel('id'); ?>
				<?php echo $this->form->getInput('id'); ?></li>
				<li><?php echo $this->form->getLabel('entry_date'); ?>
				<?php echo $this->form->getInput('entry_date'); ?></li>
				<li><?php echo $this->form->getLabel('submit_date'); ?>
				<?php echo $this->form->getInput('submit_date'); ?></li>
				<li><?php echo $this->form->getLabel('client_id'); ?>
				<?php echo $this->form->getInput('client_id'); ?></li>
				<li><?php echo $this->form->getLabel('trainer_id'); ?>
				<?php echo $this->form->getInput('trainer_id'); ?></li>
				<li><?php echo $this->form->getLabel('assessed_by'); ?>
				<?php echo $this->form->getInput('assessed_by'); ?></li>
				<li><?php echo $this->form->getLabel('goal_category_id'); ?>
				<?php echo $this->form->getInput('goal_category_id'); ?></li>
				<li><?php echo $this->form->getLabel('training_period_id'); ?>
				<?php echo $this->form->getInput('training_period_id'); ?></li>
				<li><?php echo $this->form->getLabel('nutrition_focus'); ?>
				<?php echo $this->form->getInput('nutrition_focus'); ?></li>
				<li><?php echo $this->form->getLabel('status'); ?>
				<?php echo $this->form->getInput('status'); ?></li>
				<li><?php echo $this->form->getLabel('score'); ?>
				<?php echo $this->form->getInput('score'); ?></li>
				<li><?php echo $this->form->getLabel('trainer_comments'); ?>
				<?php echo $this->form->getInput('trainer_comments'); ?></li>
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