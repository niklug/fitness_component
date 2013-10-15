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
                $userGroup = $helper->getUserGroup($this->item->user_id); 
                
                if(!$userGroup['success']) {
                    JError::raiseError($userGroup['message']);
                }
                
                $id = $this->item->id;
                ?>

                <li>
                    <label id="jform_user_id-lbl" class="" for="jform_user_id">User Group</label>
                   <?php if(!$id) { ?>
                    
                    <?php echo $helper->generateSelect($helper->getGroupList(), 'user_group', 'user_group', $this->item->user_group, '', true, "required"); ?>
                    
                    <?php } else {
                        echo $userGroup['data'];
                    }
                    ?>
                </li>
                <?php
                    $id = $this->item->id;
                ?>
                <li>
                    <label id="jform_user_id-lbl" class="" for="jform_user_id">Username</label>
                    <?php if(!$id) { ?>
                        <select id="jform_user_id" class="inputbox" name="jform[user_id]"></select>
                    <?php } else {
                        echo JFactory::getUser($this->item->user_id)->username;
                    }
                    ?>
                </li>


                <li><?php echo $this->form->getLabel('primary_trainer'); ?>
                <?php echo $helper->generateSelect($helper->getTrainersByUsergroup(), 'jform[primary_trainer]', 'jform_primary_trainer', $this->item->primary_trainer); ?>
                </li>
                <li><?php echo $this->form->getLabel('other_trainers'); ?>
                    <?php echo $helper->getOtherTrainersSelect($this->item->id, '#__fitness_clients'); ?>
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
            //////////////////////////////////////////
            $('#user_group').change(function(){
               var user_group = $(this).find(':selected').val();
               getUsersByGroup(user_group);
            });
            
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


            function getUsersByGroup(user_group) {
                var url = '<?php echo JUri::base() ?>index.php?option=com_fitness&tmpl=component&<?php echo JSession::getFormToken(); ?>=1'
                $.ajax({
                    type : "POST",
                    url : url,
                    data : {
                       view : 'goals',
                       format : 'text',
                       task : 'getUsersByGroup',
                       user_group : user_group
                    },
                    dataType : 'json',
                    success : function(response) {
                        if(!response.status.success) {
                            alert(response.status.message);
                            $("#jform_user_id").html('');
                            return;
                        }
                        var client_id = '<?php echo $this->item->user_id; ?>';

                        var html = '<option  value="">-Select-</option>';
                        $.each(response.data, function(index, value) {
                             if(index) {
                                var selected = '';
                                if(client_id == index) {
                                    selected = 'selected';
                                }
                                html += '<option ' + selected + ' value="' + index + '">' +  value + '</option>';
                            }
                        });
                        $("#jform_user_id").html(html);

                    },
                    error: function(XMLHttpRequest, textStatus, errorThrown)
                    {
                        alert("error");
                    }
                });
            }

            var user_group = $("#user_group").find(':selected').val();
            if(user_group) {
                getUsersByGroup(user_group);
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



