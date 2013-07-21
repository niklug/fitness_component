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
                if (task == 'program.cancel') {
                    Joomla.submitform(task, document.getElementById('program-form'));
                }
                else{
                    
                    if (task != 'program.cancel' && document.formvalidator.isValid(document.id('program-form'))) {
                        
                        Joomla.submitform(task, document.getElementById('program-form'));
                    }
                    else {
                        alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED')); ?>');
                    }
                }
            }
        });
    });
</script>

<form action="<?php echo JRoute::_('index.php?option=com_fitness&layout=edit&id=' . (int) $this->item->id); ?>" method="post" enctype="multipart/form-data" name="adminForm" id="program-form" class="form-validate">
    <div class="width-60 fltlft">
        <fieldset class="adminform">
            <legend><?php echo JText::_('COM_FITNESS_LEGEND_PROGRAM'); ?></legend>
            <ul class="adminformlist">

                				<li><?php echo $this->form->getLabel('id'); ?>
				<?php echo $this->form->getInput('id'); ?></li>
				<li><?php echo $this->form->getLabel('starttime'); ?>
				<?php echo $this->form->getInput('starttime'); ?></li>
				<li><?php echo $this->form->getLabel('client_id'); ?>
				<?php echo $this->form->getInput('client_id'); ?></li>
				<li><?php echo $this->form->getLabel('trainer_id'); ?>
				<?php echo $this->form->getInput('trainer_id'); ?></li>
				<li><?php echo $this->form->getLabel('location'); ?>
				<?php echo $this->form->getInput('location'); ?></li>
				<li><?php echo $this->form->getLabel('title'); ?>
				<?php echo $this->form->getInput('title'); ?></li>
				<li><?php echo $this->form->getLabel('session_type'); ?>
				<?php echo $this->form->getInput('session_type'); ?></li>
				<li><?php echo $this->form->getLabel('session_focus'); ?>
				<?php echo $this->form->getInput('session_focus'); ?></li>
				<li><?php echo $this->form->getLabel('status'); ?>
				<?php echo $this->form->getInput('status'); ?></li>
				<li><?php echo $this->form->getLabel('frontend_published'); ?>
				<?php echo $this->form->getInput('frontend_published'); ?></li>
				<li><?php echo $this->form->getLabel('published'); ?>
				<?php echo $this->form->getInput('published'); ?></li>
				<input type="hidden" name="jform[calid]" value="<?php echo $this->item->calid; ?>" />
				<input type="hidden" name="jform[endtime]" value="<?php echo $this->item->endtime; ?>" />
				<input type="hidden" name="jform[description]" value="<?php echo $this->item->description; ?>" />
				<input type="hidden" name="jform[isalldayevent]" value="<?php echo $this->item->isalldayevent; ?>" />
				<input type="hidden" name="jform[color]" value="<?php echo $this->item->color; ?>" />
				<input type="hidden" name="jform[owner]" value="<?php echo $this->item->owner; ?>" />
				<input type="hidden" name="jform[rrule]" value="<?php echo $this->item->rrule; ?>" />
				<input type="hidden" name="jform[uid]" value="<?php echo $this->item->uid; ?>" />
				<input type="hidden" name="jform[exdate]" value="<?php echo $this->item->exdate; ?>" />


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