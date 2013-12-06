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
                    <label id="" class="" for="jform_business_profile_id">Business Name</label>
                    <?php echo $helper->generateSelect($helper->getBusinessProfileList(), 'jform[business_profile_id]', 'business_profile_id', $this->item->business_profile_id , '', true, "required"); ?>
                </li>

                <li>
                    <label id="jform_user_id-lbl" class="" for="jform_group_id">User Group</label>
                    
                    <?php
                    echo $helper->generateSelect($helper->getGroupList(), 'jform[group_id]', 'group_id', $this->item->group_id, '', true, "required");
                    ?>
                </li>

                <li><?php echo $this->form->getLabel('primary_trainer'); ?>
                <?php
                      $business_profile_id = $this->item->business_profile_id;
                      
                      $business_profile = $helper->getBusinessProfile($business_profile_id);
                      if(!$business_profile['success']) {
                          JError::raiseError($business_profile['message']);
                      }
                      $business_profile = $business_profile['data'];
                      $group_id = $business_profile->group_id;
                      $primary_trainers = array();
                      if($group_id) {
                          $primary_trainers = $helper->getTrainersByUsergroup($group_id);
                      } 
                      echo $helper->generateSelect($primary_trainers, 'jform[primary_trainer]', 'jform_primary_trainer', $this->item->primary_trainer, ''); ?>
                </li>



                <li><?php echo $this->form->getLabel('other_trainers'); ?>
                    <?php
                    $item_id = $this->item->id;
                    if($item_id) {
                        echo $helper->getOtherTrainersSelect($this->item->id, '#__fitness_user_groups', $group_id);
                    } else {
                        echo $helper->generateSelect(array(), 'jform[other_trainers]', 'jform_other_trainers', $this->item->other_trainers, ''); 
                    }
                    ?>
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
        
        $("#business_profile_id").on('change', function() {
            all_options = '';
            $("#jform_primary_trainer, #jform_other_trainers").html('<option  value="">-Select-</option>');
            
            var business_profile_id = $(this).val();
            var data = {};
            var url = '<?php echo JURI::root();?>administrator/index.php?option=com_fitness&tmpl=component&<?php echo JSession::getFormToken(); ?>=1';
            var view = 'user_group';
            var task = 'onBusinessNameChange';
            var table = '#__fitness_business_profiles';
            data.business_profile_id = business_profile_id;
            
            $.AjaxCall(data, url, view, task, table, function(output) {
                
                var primary_trainer = '<?php echo $this->item->primary_trainer; ?>';
                var html = '<option  value="">-Select-</option>';
                $.each(output, function(index, value) {
                     if(index) {
                        var selected = '';
                        if(primary_trainer == index) {
                            selected = 'selected';
                        }
                        html += '<option ' + selected + ' value="' + index + '">' +  value + '</option>';
                    }
                });
                $("#jform_primary_trainer").html(html);

                var html2 ='<select size="10" id="jform_other_trainers" class="inputbox" multiple="multiple" name="jform[other_trainers][]">';
                html2 += '<option  value="">none</option>';
                $.each(output, function(index, value) {
                     if(index) {
                        html2 += '<option  value="' + index + '">' +  value + '</option>';
                    }
                });
                
                html2 += '</select>';
                
                $("#jform_other_trainers").replaceWith(html2);
                
            });
            
        });
        
        
        
        
        
        
        all_options = $('#jform_other_trainers').html();
        $("#jform_primary_trainer").live('change', function() {
            if(!all_options) {
                all_options = $('#jform_other_trainers').html();
            }
            var value = $(this).val();
            hideSelectOption(value, '#jform_other_trainers', all_options);
        });

        var value = $("#jform_primary_trainer").val();
        hideSelectOption(value, '#jform_other_trainers', all_options);
        
        
        
        $("#group_id").on('change', function() {
            var group_id = $(this).val();
            
            var data = {};
            var url = '<?php echo JURI::root();?>administrator/index.php?option=com_fitness&tmpl=component&<?php echo JSession::getFormToken(); ?>=1';
            var view = 'business_profile';
            var task = 'checkUniqueGroup';
            var table = '#__fitness_user_groups';
            data.value = group_id;
            data.column = 'group_id';
            
            $.AjaxCall(data, url, view, task, table, function(output) {
                var group_exists = output;
                if(group_exists) {
                    alert('User Group already in use!');
                    $("#group_id").val('');
                }
            });
            
        });
        
        

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

