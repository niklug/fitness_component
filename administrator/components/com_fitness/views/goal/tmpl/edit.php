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
.adminformlist li {
    clear: both;
}

</style>
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
            <?php if($this->item->id) { ?>
                <br/>
                <div class="clr"></div>
                <hr>
                <div id="comments_wrapper"></div>
                <div class="clr"></div>
                <input id="add_comment_0" class="" type="button" value="Add Comment" >
                <div class="clr"></div>
            <?php } ?>
        </fieldset>
    </div>

    

    <input type="hidden" name="task" value="" />
    <?php echo JHtml::_('form.token'); ?>
    <div class="clr"></div>

</form>



<script type="text/javascript">
    
    (function($) {
        

        
        var comment_options = {
            'item_id' : '<?php echo $this->item->id;?>',
            'fitness_administration_url' : '<?php echo JURI::root();?>administrator/index.php?option=com_fitness&tmpl=component&<?php echo JSession::getFormToken(); ?>=1',
            'comment_obj' : {'user_name' : '<?php echo JFactory::getUser()->name;?>', 'created' : "", 'comment' : ""},
            'db_table' : '#__fitness_goal_comments',
            'read_only' : false,
            'anable_comment_email' : true
        }
        
        // comments
        var comments = $.comments(comment_options, comment_options.item_id, 0);
          
        var comments_html = comments.run();
        $("#comments_wrapper").html(comments_html);
        

        Joomla.submitbutton = function(task)
        {
            if (task == 'goal.cancel') {
                Joomla.submitform(task, document.getElementById('goal-form'));
            }
            else{

                if (task != 'goal.cancel' && document.formvalidator.isValid(document.id('goal-form'))) {
                    
                    // check Goals overlaping 
                    var data = {};
                    var url = '<?php echo JURI::root();?>administrator/index.php?option=com_fitness&tmpl=component&<?php echo JSession::getFormToken(); ?>=1';
                    var view = 'goals';
                    var ajax_task = 'checkOverlapDate';
                    var table = '#__fitness_goals';
                    
                    data.item_id = '<?php echo $this->item->id; ?>';
                    data.where_column = 'user_id';
                    data.where_value = $("#jform_user_id").val();
                    data.start_date = $("#jform_start_date").val();
                    
                    data.end_date = $("#jform_deadline").val();
                    data.start_date_column = 'start_date';
                    data.end_date_column = 'deadline';
                    //console.log(data);
                    $.AjaxCall(data, url, view, ajax_task, table, function(output){
                        if(output) {
                             alert('Goal Date is Overlaping!');
                            return false;
                        }
        
                        Joomla.submitform(task, document.getElementById('goal-form'));
                    });
                    
                }
                else {
                    alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED')); ?>');
                }
            }
        }
    
    })($js);
    
    
    
</script>