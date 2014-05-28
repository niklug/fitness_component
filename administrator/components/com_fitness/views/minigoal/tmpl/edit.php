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

$primary_goal = $this->backend_list_model->getGoal($primary_goal_id);



require_once JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_fitness' . DS . 'helpers' . DS . 'fitness.php';

$helper = new FitnessHelper();


?>
<style type="text/css">
#jform_details-lbl, #jform_comments-lbl {
    float: none;
}
.adminformlist li {
    clear: both;
}

</style>


<form action="<?php echo JRoute::_('index.php?option=com_fitness&layout=edit&id=' . (int) $this->item->id); ?>" method="post" enctype="multipart/form-data" name="adminForm" id="minigoal-form" class="form-validate">
    <div class="width-60 fltlft">
        <fieldset class="adminform">
            <legend><?php echo JText::_('COM_FITNESS_LEGEND_MINIGOAL'); ?></legend>
            <ul class="adminformlist">
                <input id="jform_primary_goal_id" class="inputbox" type="hidden" value="<?php echo $primary_goal_id?>" name="jform[primary_goal_id]">
                        
                        <li>
                            <?php echo $this->form->getLabel('mini_goal_category_id'); ?>

                            <?php
                            echo $helper->generateSelect($helper->getMiniGoalCategory(), 'jform[mini_goal_category_id]', 'jform_mini_goal_category_id', $this->item->mini_goal_category_id, '', true, "required");
                            ?>
                        </li>
                        <li>
                            <?php echo $this->form->getLabel('training_period_id'); ?>

                            <?php
                            echo $helper->generateSelect($helper->getTrainingPeriod(), 'jform[training_period_id]', 'jform_training_period_id', $this->item->training_period_id, '', true, "required");
                            ?>
                        </li>
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
            <br/>
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

    

    <input type="hidden" name="jform_id" value="<?php echo $this->item->id;?>" />
    <input type="hidden"  id="jform_user_id"  name="jform[user_id]" value="<?php echo $primary_goal->user_id;?>" />
    <input type="hidden" name="task" value="" />

    <?php echo JHtml::_('form.token'); ?>
    <div class="clr"></div>
</form>
<div id="emais_sended"></div>

<script type="text/javascript">
    
    (function($) {
        // set datapicker
        var min_date = new Date(Date.parse('<?php echo $primary_goal->start_date; ?>'));
        var max_date = new Date(Date.parse('<?php echo $primary_goal->deadline; ?>'));
        $( "#jform_start_date, #jform_deadline" ).datepicker({ dateFormat: "yy-mm-dd" , minDate: min_date, maxDate: max_date });
        
        var comment_options = {
            'item_id' : '<?php echo $this->item->id;?>',
            'fitness_administration_url' : '<?php echo JURI::root();?>administrator/index.php?option=com_fitness&tmpl=component&<?php echo JSession::getFormToken(); ?>=1',
            'comment_obj' : {'user_name' : '<?php echo JFactory::getUser()->name;?>', 'created' : "", 'comment' : ""},
            'db_table' : '#__fitness_mini_goal_comments',
            'read_only' : false,
            'anable_comment_email' : true,
            'comment_method' : 'GoalComment'
        }
        
        // comments
        var comments = $.comments(comment_options, comment_options.item_id, 0);
          
        var comments_html = comments.run();
        $("#comments_wrapper").html(comments_html);
        





        Joomla.submitbutton = function(task)
        {
            if (task == 'minigoal.cancel') {
                Joomla.submitform(task, document.getElementById('minigoal-form'));
            }
            else{

                if (task != 'minigoal.cancel' && document.formvalidator.isValid(document.id('minigoal-form'))) {
                    // check Goals overlaping 
                    var data = {};
                    var url = '<?php echo JURI::root();?>administrator/index.php?option=com_fitness&tmpl=component&<?php echo JSession::getFormToken(); ?>=1';
                    var view = 'goals';
                    var ajax_task = 'checkOverlapDate';
                    var table = '#__fitness_mini_goals';
                    
                    data.item_id = '<?php echo $this->item->id; ?>';
                    data.where_column = 'primary_goal_id';
                    data.where_value = '<?php echo $primary_goal_id; ?>';
                    data.start_date = $("#jform_start_date").val();
                    
                    data.end_date = $("#jform_deadline").val();
                    data.start_date_column = 'start_date';
                    data.end_date_column = 'deadline';
                    //console.log(data);
                    $.AjaxCall(data, url, view, ajax_task, table, function(output){
                        if(output) {
                            //console.log(output);
                            alert('Goal Date is Overlaping!');
                            return false;
                        }
        
                        Joomla.submitform(task, document.getElementById('minigoal-form'));
                    });
                    
                }
                else {
                    alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED')); ?>');
                }
            }
        }
    
    })($js);
    
    
    
</script>