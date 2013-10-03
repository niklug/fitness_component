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
?>

<form action="<?php echo JRoute::_('index.php?option=com_fitness&layout=edit&id=' . (int) $this->item->id); ?>" method="post" enctype="multipart/form-data" name="adminForm" id="user_group-form" class="form-validate">
    <div class="width-60 fltlft">
        <fieldset class="adminform">
            <legend><?php echo JText::_('COM_FITNESS_LEGEND_USER_GROUP'); ?></legend>
            <ul class="adminformlist">
                                <?php
                                $db = JFactory::getDbo();
                                $sql = 'SELECT id AS value, title AS text'. ' FROM #__usergroups' . ' ORDER BY id';
                                $db->setQuery($sql);
                                $grouplist = $db->loadObjectList();
                                ?>

                                <li>
                                    <label id="jform_user_id-lbl" class="" for="jform_gid">User Group</label>
                                    <select  id="gid"  name="jform[gid]" aria-required="true" required="required" class="required" >
                                        <option value=""><?php echo JText::_('-Select-'); ?></option>
                                        <?php 
                                        foreach ($grouplist as $option) {
                                            if($this->item->gid == $option->value){ 
                                                $selected = 'selected';
                                            } else {
                                                $selected = '';
                                            }
                                            echo '<option ' . $selected . ' value="' . $option->value . '">' . $option->text . ' </option>';
                                        }
                                        ?>
                                    </select>
                                </li>
                
				<li><?php echo $this->form->getLabel('primary_trainer'); ?>
				<?php echo $this->form->getInput('primary_trainer'); ?></li>
                                
                                
				
                                <li><?php echo $this->form->getLabel('other_trainers'); ?>
                                <?php echo $this->client_model->getInput($this->item->id, '#__fitness_user_groups'); ?></li>
                                
                                
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
        
        $("#jform_primary_trainer").on('change', function() {
            var value = $(this).val();
            hideSelectOption(value, '#other_trainers');
        });

        var value = $("#jform_primary_trainer").val();
        hideSelectOption(value, '#other_trainers');

        function hideSelectOption(value, element) {
            $(element + " option").show();
            $(element + " option[value=" + value + "]").hide();
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

