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
<style type="text/css">
#jform_description-lbl{
    float: none;
}

</style>
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
                if (task == 'nutritiondatabase.cancel') {
                    Joomla.submitform(task, document.getElementById('nutritiondatabase-form'));
                }
                else{
                    
                    if (task != 'nutritiondatabase.cancel' && document.formvalidator.isValid(document.id('nutritiondatabase-form'))) {
                        
                        Joomla.submitform(task, document.getElementById('nutritiondatabase-form'));
                    }
                    else {
                        alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED')); ?>');
                    }
                }
            }
        });
    });
</script>

<form action="<?php echo JRoute::_('index.php?option=com_fitness&layout=edit&id=' . (int) $this->item->id); ?>" method="post" enctype="multipart/form-data" name="adminForm" id="nutritiondatabase-form" class="form-validate">
    <div class="width-60 fltlft">
        <fieldset class="adminform">
            <legend><?php echo JText::_('COM_FITNESS_LEGEND_NUTRITIONDATABASE'); ?></legend>
            <ul class="adminformlist">

				<li><?php echo $this->form->getLabel('ingredient_name'); ?>
				<?php echo $this->form->getInput('ingredient_name'); ?></li>
                                <li><?php echo $this->form->getLabel('description'); ?>
				<?php echo $this->form->getInput('description'); ?></li>
				<li><?php echo $this->form->getLabel('calories'); ?>
				<?php echo $this->form->getInput('calories'); ?></li>
				<li><?php echo $this->form->getLabel('energy'); ?>
				<?php echo $this->form->getInput('energy'); ?></li>
				<li><?php echo $this->form->getLabel('protein'); ?>
				<?php echo $this->form->getInput('protein'); ?></li>
				<li><?php echo $this->form->getLabel('fats'); ?>
				<?php echo $this->form->getInput('fats'); ?></li>
				<li><?php echo $this->form->getLabel('carbs'); ?>
				<?php echo $this->form->getInput('carbs'); ?></li>
				<li><?php echo $this->form->getLabel('saturated_fat'); ?>
				<?php echo $this->form->getInput('saturated_fat'); ?></li>
				<li><?php echo $this->form->getLabel('total_sugars'); ?>
				<?php echo $this->form->getInput('total_sugars'); ?></li>
				<li><?php echo $this->form->getLabel('sodium'); ?>
				<?php echo $this->form->getInput('sodium'); ?></li>
                                <li><?php echo $this->form->getLabel('specific_gravity'); ?>
				<?php echo $this->form->getInput('specific_gravity'); ?></li>
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