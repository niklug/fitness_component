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

<form action="<?php echo JRoute::_('index.php?option=com_fitness&layout=edit&id=' . (int) $this->item->id); ?>" method="post" enctype="multipart/form-data" name="adminForm" id="category-form" class="form-validate">
    <div class="width-60 fltlft">
        <fieldset class="adminform">
            <legend><?php echo JText::_('COM_FITNESS_LEGEND_CATEGORY'); ?></legend>
            <ul class="adminformlist">
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


        $("#calendarcolor").colorselect({ title: i18n.dcmvcal.color, index: cv, hiddenid: "colorvalue",colors:<?php echo json_encode($palette);?>,paletteDefault:"<?php echo $_GET["paletteDefault"];?>" });

        Joomla.submitbutton = function(task)
        {
            if (task == 'category.cancel') {
                Joomla.submitform(task, document.getElementById('category-form'));
            }
            else{

                if (task != 'category.cancel' && document.formvalidator.isValid(document.id('category-form'))) {

                    Joomla.submitform(task, document.getElementById('category-form'));
                }
                else {
                    alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED')); ?>');
                }
            }
        }

    })($js);
    

        
</script>