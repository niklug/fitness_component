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
                            <?php echo $this->form->getInput('name'); ?></li>
                        <li>
                            <?php
                            $db = JFactory::getDbo();
                            $sql = 'SELECT id AS value, title AS text' . ' FROM #__usergroups' . ' ORDER BY id';
                            $db->setQuery($sql);
                            $grouplist = $db->loadObjectList();
                            ?>

                        <li>
                            <?php echo $this->form->getLabel('group_id'); ?>
                            <select  id="group_id"  name="jform[group_id]" aria-required="true" required="required" class="required" >
                                <option value=""><?php echo JText::_('-Select-'); ?></option>
                                <?php
                                foreach ($grouplist as $option) {
                                    if ($this->item->group_id == $option->value) {
                                        $selected = 'selected';
                                    } else {
                                        $selected = '';
                                    }
                                    echo '<option ' . $selected . ' value="' . $option->value . '">' . $option->text . ' </option>';
                                }
                                ?>
                            </select>
                        </li>
                        <li><?php echo $this->form->getLabel('primary_administrator'); ?>
                            <?php echo $this->form->getInput('primary_administrator'); ?></li>
                        <li><?php echo $this->form->getLabel('secondary_administrator'); ?>
                            <?php echo $this->form->getInput('secondary_administrator'); ?></li>
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
            })
            
        })



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