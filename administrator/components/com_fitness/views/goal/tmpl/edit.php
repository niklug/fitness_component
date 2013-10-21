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

require_once  JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_fitness' . DS .'helpers' . DS . 'fitness.php';

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
<form action="<?php echo JRoute::_('index.php?option=com_fitness&layout=edit&id=' . (int) $this->item->id); ?>" method="post" enctype="multipart/form-data" name="adminForm" id="goal-form" class="form-validate">
    <div class="width-60 fltlft">
        <fieldset class="adminform">
            <legend><?php echo JText::_('COM_FITNESS_GOALS_LEGEND_GOAL'); ?></legend>
            <ul class="adminformlist">
                                
                		<input type="hidden" name="jform[id]" value="<?php echo $this->item->id; ?>" />
                                <li><?php echo $this->form->getLabel('business_profile_id'); 
                                $business_profile_id = $helper->getBubinessIdByClientId($this->item->user_id);
                                
                                echo $helper->generateSelect($helper->getBusinessProfileList(), 'jform[business_profile_id]', 'business_profile_id', $business_profile_id , '', true, "required required"); ?>
                                </li>

                                <li>
                                   <?php echo $this->form->getLabel('user_id'); ?>
                                    <select id="jform_user_id" class="inputbox required" name="jform[user_id]" required="required"></select>
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
        
        // connect helper class
        var helper_options = {
            'ajax_call_url' : '<?php echo JURI::root();?>administrator/index.php?option=com_fitness&tmpl=component&<?php echo JSession::getFormToken(); ?>=1',
        }
        var fitness_helper = $.fitness_helper(helper_options);
        
        $("#business_profile_id").on('change', function() {
            var business_profile_id = $(this).val();
            // populate clients select
            fitness_helper.populateClientsSelectOnBusiness('getClientsByBusiness', 'goals', business_profile_id, '#jform_user_id', '<?php echo $this->item->user_id; ?>');
        });
        
        var business_profile_id = $("#business_profile_id").val();
        if(business_profile_id) {
            fitness_helper.populateClientsSelectOnBusiness('getClientsByBusiness', 'goals', business_profile_id, '#jform_user_id', '<?php echo $this->item->user_id; ?>');
        }
        
        
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