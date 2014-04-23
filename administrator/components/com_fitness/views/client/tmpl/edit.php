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


<form action="<?php echo JRoute::_('index.php?option=com_fitness&layout=edit&id=' . (int) $this->item->id); ?>" method="post" enctype="multipart/form-data" name="adminForm" id="client-form" class="form-validate">
    <div class="width-60 fltlft">
        <fieldset class="adminform">
            <legend><?php echo JText::_('COM_FITNESS_LEGEND_CLIENT'); ?></legend>
            <ul class="adminformlist">
                <?php
                $business_profile = $helper->JErrorFromAjaxDecorator($helper->getBusinessProfile($this->item->business_profile_id));
                
                $id = $this->item->id;
                ?>

                <li>
                    <label id="" class="" for="jform_business_profile_id">Business Name</label>
                   <?php if(!$id) { ?>
                    <?php echo $helper->generateSelect($helper->getBusinessProfileList(), 'jform[business_profile_id]', 'business_profile_id', $this->item->business_profile_id , '', true, "required"); ?>
                    
                    <?php } else {
                        echo $business_profile->name;
                    }
                    ?>
                </li>
                <?php
                    $id = $this->item->id;
                ?>
                <li>
                    <label id="jform_user_id-lbl" class="" for="jform_user_id">Username</label>
                    <?php if(!$id) { ?>
                        <select id="jform_user_id" class="inputbox required" name="jform[user_id]"><option value="">-Select-</option></select>
                    <?php } else {
                        echo JFactory::getUser($this->item->user_id)->username;
                    }
                    ?>
                </li>


                <li><?php echo $this->form->getLabel('primary_trainer'); ?>
                <?php
                      $business_profile_id = $this->item->business_profile_id;
                      
                      $business_profile = $helper->JErrorFromAjaxDecorator($helper->getBusinessProfile($business_profile_id));
                      $group_id = $business_profile->group_id;
                      $primary_trainers = array();
                      if($group_id) {
                          $primary_trainers = $helper->getTrainersByUsergroup($group_id);
                      } 
                      echo $helper->generateSelect($primary_trainers, 'jform[primary_trainer]', 'jform_primary_trainer', $this->item->primary_trainer, '', true, 'required'); ?>
                </li>



                <li><?php echo $this->form->getLabel('other_trainers'); ?>
                    <?php
                    $item_id = $this->item->id;
                    if($item_id) {
                        echo $helper->getOtherTrainersSelect($this->item->id, '#__fitness_clients', $group_id);
                    } else {
                        echo $helper->generateMultipleSelect(array(), 'jform[other_trainers]', 'jform_other_trainers', '', '', false, 'inputbox');
                    }
                    ?>
                </li>

            </ul>
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
        
        var business_profile_id = '<?php echo $this->item->business_profile_id; ?>';
        
        if(business_profile_id) {
            setFields(business_profile_id);
        }
        
                   
        $("#business_profile_id").on('change', function() {

            $("#jform_primary_trainer, #jform_other_trainers").html('<option  value="">-Select-</option>');
            
            // primary trainers
            var business_profile_id = $(this).val();
                
            setFields(business_profile_id);
        });
        
        function setFields(business_profile_id) {
            fitness_helper.populateTrainersSelectOnBusiness('user_group', business_profile_id, '#jform_primary_trainer', '<?php echo $this->item->primary_trainer; ?>');
            
            // populate other trainers select
            fitness_helper.on('change:trainers', function(model, items) {
                fitness_helper.excludeSelectOption('#jform_primary_trainer', '#jform_other_trainers');
            });
            
            
            // populate clients select
            fitness_helper.populateClientsSelectOnBusiness('getUsersByBusiness', 'goals', business_profile_id, '#jform_user_id', '<?php echo $this->item->user_id; ?>');
            
            // exclude options logic
            
        }

        


        Joomla.submitbutton = function(task)
        {
            if (task == 'client.cancel') {
                Joomla.submitform(task, document.getElementById('client-form'));
            }
            else{

                if (task != 'client.cancel' && document.formvalidator.isValid(document.id('client-form'))) {

                    Joomla.submitform(task, document.getElementById('client-form'));
                }
                else {
                    alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED')); ?>');
                }
            }
        }
        
    })($js);


</script>



