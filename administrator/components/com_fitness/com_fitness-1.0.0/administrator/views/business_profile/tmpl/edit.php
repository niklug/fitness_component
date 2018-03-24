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
<style type="text/css">
    #jform_terms_conditions-lbl {
        float: none;
    }
    .adminformlist li {
        clear: both;
    }

</style>
<form action="<?php echo JRoute::_('index.php?option=com_fitness&layout=edit&id=' . (int) $this->item->id); ?>" method="post" enctype="multipart/form-data" name="adminForm" id="business_profile-form" class="form-validate">
    <table width="100%">
        <tr>
            <td width="50%">
                <div class="width-100 fltlft">
                <fieldset class="adminform">
                    <legend><?php echo JText::_('COM_FITNESS_LEGEND_BUSINESS_PROFILE'); ?></legend>
                    <ul class="adminformlist">
                        <li><?php echo $this->form->getLabel('name'); ?>
                            <?php echo $this->form->getInput('name'); ?>
                        </li>
 
                        <li>
                            <?php echo $this->form->getLabel('group_id'); ?>
                            <?php echo $helper->generateSelect($helper->getGroupList(), 'jform[group_id]', 'group_id', $this->item->group_id, '', "required", true); ?>
                        </li>
                        <li><?php echo $this->form->getLabel('primary_administrator'); ?>
                            <select id="jform_primary_administrator" class="inputbox" name="jform[primary_administrator]"></select>
                        </li>
                        <li><?php echo $this->form->getLabel('secondary_administrator'); ?>
                            <select id="jform_secondary_administrator" class="inputbox" name="jform[secondary_administrator]"></select></li>
                        <li><?php echo $this->form->getLabel('terms_conditions'); ?>
                            <?php echo $this->form->getInput('terms_conditions'); ?></li>
                    </ul>
                </fieldset>
                </div>
            </td>
            <td>
                <div class="width-100 fltlft">
        <fieldset class="adminform"  style="min-height: 434px;">
            <legend>Email Template</legend>
            <ul class="adminformlist">
                <li><?php echo $this->form->getLabel('header_image'); ?>
                    <?php echo $this->form->getInput('header_image'); ?>
                </li>
                <li>
                    (header must not exceed maximum width 570 pixels and maximum height 150 pixels)
                </li>
                <li><?php echo $this->form->getLabel('facebook_url'); ?>
                    <?php echo $this->form->getInput('facebook_url'); ?></li>
                <li><?php echo $this->form->getLabel('twitter_url'); ?>
                    <?php echo $this->form->getInput('twitter_url'); ?></li>
                <li><?php echo $this->form->getLabel('youtube_url'); ?>
                    <?php echo $this->form->getInput('youtube_url'); ?></li>
                <li><?php echo $this->form->getLabel('instagram_url'); ?>
                    <?php echo $this->form->getInput('instagram_url'); ?></li>
                <li><?php echo $this->form->getLabel('google_plus_url'); ?>
                    <?php echo $this->form->getInput('google_plus_url'); ?></li>
                <li><?php echo $this->form->getLabel('linkedin_url'); ?>
                    <?php echo $this->form->getInput('linkedin_url'); ?></li>
                <li><?php echo $this->form->getLabel('website_url'); ?>
                    <?php echo $this->form->getInput('website_url'); ?></li>
                <li><?php echo $this->form->getLabel('email'); ?>
                    <?php echo $this->form->getInput('email'); ?></li>
                <li><?php echo $this->form->getLabel('contact_number'); ?>
                    <?php echo $this->form->getInput('contact_number'); ?></li>
                <input type="hidden" name="jform[state]" value="<?php echo $this->item->state; ?>" />


            </ul>
        </fieldset>
                </div>
        </td>
        </tr>
    </table>



    <input type="hidden" name="task" value="" />
    <?php echo JHtml::_('form.token'); ?>
    <div class="clr"></div>
</form>



<script type="text/javascript">

    (function($) {

        all_secondary_administrator_options = '';
        
        
        $("#jform_primary_administrator").live('change', function() {
            if(!all_secondary_administrator_options) {
                all_secondary_administrator_options = $('#jform_secondary_administrator').html();
            }
            var value = $(this).val();
            hideSelectOption(value, '#jform_secondary_administrator', all_secondary_administrator_options);

        });


        
        function hideSelectOption(value, element, all_options) {

            $(element).html(all_options);

            $(element + " option[value=" + value + "]").remove();
        }
        
        
        
        $("#group_id").on('change', function() {
            var group_id = $(this).val();
            
            var data = {};
            var url = '<?php echo JURI::root();?>administrator/index.php?option=com_fitness&tmpl=component&<?php echo JSession::getFormToken(); ?>=1';
            var view = 'business_profile';
            var task = 'checkUniqueGroup';
            var table = '#__fitness_business_profiles';
            data.value = group_id;
            data.column = 'group_id';
            
            $.AjaxCall(data, url, view, task, table, function(output) {
                var group_exists = output;
                
                if(group_exists) {
                    alert('Business Profile already created for this User Group!');
                    $("#group_id").val('');
                }
            });
            
            getUsersByGroup(group_id);
            
        });
        
        
        
        function getUsersByGroup(group_id) {
            var url = '<?php echo JUri::base() ?>index.php?option=com_fitness&tmpl=component&<?php echo JSession::getFormToken(); ?>=1'
            $.ajax({
                type : "POST",
                url : url,
                data : {
                   view : 'goals',
                   format : 'text',
                   task : 'getUsersByGroup',
                   user_group : group_id
                },
                dataType : 'json',
                success : function(response) {
                    if(!response.status.success) {
                        alert(response.status.message);
                        $("#jform_primary_administrator, #jform_secondary_administrator").html('');
                        return;
                    }
                    var primary_administrator = '<?php echo $this->item->primary_administrator; ?>';

                    var html = '<option  value="">-Select-</option>';
                    $.each(response.data, function(index, value) {
                         if(index) {
                            var selected = '';
                            if(primary_administrator == index) {
                                selected = 'selected';
                            }
                            html += '<option ' + selected + ' value="' + index + '">' +  value + '</option>';
                        }
                    });
                    $("#jform_primary_administrator").html(html);
                    
                    
                    
                    
                    var secondary_administrator = '<?php echo $this->item->secondary_administrator; ?>';

                    var html = '<option  value="">-Select-</option>';
                    $.each(response.data, function(index, value) {
                         if(index) {
                            var selected = '';
                            if(secondary_administrator == index) {
                                selected = 'selected';
                            }
                            html += '<option ' + selected + ' value="' + index + '">' +  value + '</option>';
                        }
                    });
                    $("#jform_secondary_administrator").html(html);

                },
                error: function(XMLHttpRequest, textStatus, errorThrown)
                {
                    alert("error");
                }
            });
        }
        
        var group_id = $("#group_id").find(':selected').val();
        if(group_id) {
            getUsersByGroup(group_id);
        }



        Joomla.submitbutton = function(task)
        {
            if (task == 'business_profile.cancel') {
                Joomla.submitform(task, document.getElementById('business_profile-form'));
            }
            else {

                if (task != 'business_profile.cancel' && document.formvalidator.isValid(document.id('business_profile-form'))) {

                    Joomla.submitform(task, document.getElementById('business_profile-form'));
                }
                else {
                    alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED')); ?>');
                }
            }
        }



    })($js);



</script>