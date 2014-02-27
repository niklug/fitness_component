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

<form action="<?php echo JRoute::_('index.php?option=com_fitness&layout=edit&id=' . (int) $this->item->id); ?>" method="post" enctype="multipart/form-data" name="adminForm" id="nutrition_focus-form" class="form-validate">
    <div class="width-60 fltlft">
        <fieldset class="adminform">
            <legend><?php echo JText::_('COM_FITNESS_LEGEND_NUTRITION_FOCUS'); ?></legend>
            <ul class="adminformlist">
                <li>
                    <?php
                    echo $this->form->getLabel('business_profile_id');
       
                    echo $helper->generateSelect($helper->getBusinessProfileList(), 'jform[business_profile_id]', 'business_profile_id', $this->item->business_profile_id, '', true, "required");
                    ?>
                </li>
                <li><?php echo $this->form->getLabel('name'); ?>
                <?php echo $this->form->getInput('name'); ?></li>
                <li><?php echo $this->form->getLabel('color'); ?>
                <?php echo $this->form->getInput('color'); ?></li>
                <li><?php echo $this->form->getLabel('state'); ?>
                <?php echo $this->form->getInput('state'); ?></li>
            </ul>
        </fieldset>
    </div>

    

    <input type="hidden" name="task" value="" />
    <?php echo JHtml::_('form.token'); ?>
    <div class="clr"></div>

    <style type="text/css">
        /* Temporary fix for drifting editor fields */
        .adminformlist li {
            clear: both;
        }
    </style>
</form>

<script type="text/javascript">
    
    (function($) {


         Joomla.submitbutton = function(task)
            {
                if (task == 'nutrition_focus.cancel') {
                    Joomla.submitform(task, document.getElementById('nutrition_focus-form'));
                }
                else{
                    
                    if (task != 'nutrition_focus.cancel' && document.formvalidator.isValid(document.id('nutrition_focus-form'))) {
                        
                        Joomla.submitform(task, document.getElementById('nutrition_focus-form'));
                    }
                    else {
                        alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED')); ?>');
                    }
                }
            }

    })($js);
    

        
</script>