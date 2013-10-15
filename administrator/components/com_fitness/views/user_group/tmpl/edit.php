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


require_once  JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_fitness' . DS .'helpers' . DS . 'fitness.php';

$helper = new FitnessHelper();
?>



<form action="<?php echo JRoute::_('index.php?option=com_fitness&layout=edit&id=' . (int) $this->item->id); ?>" method="post" enctype="multipart/form-data" name="adminForm" id="user_group-form" class="form-validate">
    <div class="width-60 fltlft">
        <fieldset class="adminform">
            <legend><?php echo JText::_('COM_FITNESS_LEGEND_USER_GROUP'); ?></legend>
            <ul class="adminformlist">
                <li>
                    <label id="" class="" for="jform_group_id">Business Name</label>
                    <?php echo $helper->generateSelect($helper->getBusinessProfileList(), 'jform[business_profile_id]', 'business_profile_id', $this->item->business_profile_id , '', true, "required"); ?>
                </li>

                <li>
                    <label id="jform_user_id-lbl" class="" for="jform_group_id">User Group</label>
                    
                    <?php echo $helper->generateSelect($helper->getGroupList(), 'jform[group_id]', 'group_id', $this->item->group_id, '', true, "required"); ?>
                </li>

                <li><?php echo $this->form->getLabel('primary_trainer'); ?>
                <?php echo $helper->generateSelect($helper->getTrainersByUsergroup(), 'jform[primary_trainer]', 'jform_primary_trainer', $this->item->primary_trainer, ''); ?>
                </li>



                <li><?php echo $this->form->getLabel('other_trainers'); ?>
                <?php echo $helper->getOtherTrainersSelect($this->item->id, '#__fitness_user_groups'); ?>
                </li>


                <input type="hidden" name="jform[state]" value="<?php echo $this->item->state; ?>" />


            </ul>
        </fieldset>
    </div>

    

    <input type="hidden" name="task" value="" />
    <?php echo JHtml::_('form.token'); ?>
    <div class="clr"></div>

</form>


<script type="text/javascript">
    (function($) {
        
        var all_options = $('#other_trainers').html();
        
        $("#jform_primary_trainer").on('change', function() {
            var value = $(this).val();
            hideSelectOption(value, '#other_trainers', all_options);
        });

        var value = $("#jform_primary_trainer").val();
        hideSelectOption(value, '#other_trainers', all_options);
        
        

        function hideSelectOption(value, element, all_options) {
            
            $(element).html(all_options);
        
            $(element + " option[value=" + value + "]").remove();
        }

         Joomla.submitbutton = function(task)
            {
            if (task == 'user_group.cancel') {
                Joomla.submitform(task, document.getElementById('user_group-form'));
            }
            else{

                if (task != 'user_group.cancel' && document.formvalidator.isValid(document.id('user_group-form'))) {

                    Joomla.submitform(task, document.getElementById('user_group-form'));
                }
                else {
                    alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED')); ?>');
                }
            }
        }
    })($js);
</script>

