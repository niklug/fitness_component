<?php
/**
 * @version     1.0.0
 * @package     com_fitness_goals
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
#jform_details-lbl, #jform_comments-lbl {
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
                if (task == 'goal.cancel') {
                    Joomla.submitform(task, document.getElementById('goal-form'));
                }
                else{
                    
                    if (task != 'goal.cancel' && document.formvalidator.isValid(document.id('goal-form'))) {
                        
                        Joomla.submitform(task, document.getElementById('goal-form'));
                    }
                    else {
                        alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED')); ?>');
                    }
                }
            }
        });
    });
</script>

<form action="<?php echo JRoute::_('index.php?option=com_fitness&layout=edit&id=' . (int) $this->item->id); ?>" method="post" enctype="multipart/form-data" name="adminForm" id="goal-form" class="form-validate">
    <div class="width-60 fltlft">
        <fieldset class="adminform">
            <legend><?php echo JText::_('COM_FITNESS_GOALS_LEGEND_GOAL'); ?></legend>
            <ul class="adminformlist">

                		<input type="hidden" name="jform[id]" value="<?php echo $this->item->id; ?>" />
                                <?php
                                $user = &JFactory::getUser();
                                $userGroup = $this->getUserGroup($user->id); 
                                $db = JFactory::getDbo();

                                $sql = "SELECT #__users.id AS value, #__users.username as text FROM #__users INNER JOIN #__fitness_clients ON #__fitness_clients.user_id=#__users.id WHERE #__fitness_clients.state='1'";
                    
                                if ($userGroup != 'Super Users') {
                                    $sql .= "AND  #__fitness_clients.primary_trainer='$user->id'";
                                }
                                
                                $db->setQuery($sql);
                                $clients = $db->loadObjectList();
                                ?>

                                <li>
                                    <label id="jform_user_id-lbl" class=" required" for="jform_user_id">
                                        Client
                                        <span class="star"> *</span>
                                    </label>
                                    <select id="jform_user_id" class="inputbox required" name="jform[user_id]" aria-required="true" required="required">
                                        <option value=""><?php echo JText::_('-Select-'); ?></option>
                                        <?php 
                                        foreach ($clients as $option) {
                                            if($this->item->user_id == $option->value){ 
                                                $selected = 'selected';
                                            } else {
                                                $selected = '';
                                            }
                                            echo '<option ' . $selected . ' value="' . $option->value . '">' . $option->text . ' </option>';
                                        }
                                        ?>
                                    </select>
                                </li>
                                                             
				<li><?php echo $this->form->getLabel('goal_category_id'); ?>
				<?php echo $this->form->getInput('goal_category_id'); ?></li>
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
	
				<input type="hidden" name="jform[state]" value="<?php echo $this->item->state; ?>" />
                                
                                 <div style="display:none;">
                                <?php echo $this->form->getLabel('created'); ?>
                                <?php echo $this->form->getInput('created'); ?>
                                     
                                </div>
                                <?php
                                $config = JFactory::getConfig();
                                $date = new DateTime();
                                $date->setTimezone(new DateTimeZone($config->getValue('config.offset')));
                                $time_created = $date->format('Y-m-d H:i:s');
                              
                                ?>
				<input type="hidden" name="jform[modified]" value="<?php echo $time_created; ?>" />


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





