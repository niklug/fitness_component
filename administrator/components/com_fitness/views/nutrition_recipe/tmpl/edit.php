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
// Import CSS
$document = JFactory::getDocument();
$document->addStyleSheet('components/com_fitness/assets/css/fitness.css');
?>
<script type="text/javascript">
    function getScript(url,success) {
        var script = document.createElement('script');
        script.src = url;
        var head = document.getElementsByTagName('head')[0],
        done = false;
        // Attach handlers for all browsers
        script.onload = script.onreadystatechange = function() {
            if (!done && (!this.readyState
                || this.readyState == 'loaded'
                || this.readyState == 'complete')) {
                done = true;
                success();
                script.onload = script.onreadystatechange = null;
                head.removeChild(script);
            }
        };
        head.appendChild(script);
    }
    getScript('//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js',function() {
        js = jQuery.noConflict();
        js(document).ready(function(){
            

            Joomla.submitbutton = function(task)
            {
                if (task == 'nutrition_recipe.cancel') {
                    Joomla.submitform(task, document.getElementById('nutrition_recipe-form'));
                }
                else{
                    
                    if (task != 'nutrition_recipe.cancel' && document.formvalidator.isValid(document.id('nutrition_recipe-form'))) {
                        
                        Joomla.submitform(task, document.getElementById('nutrition_recipe-form'));
                    }
                    else {
                        alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED')); ?>');
                    }
                }
            }
        });
    });
</script>

<form action="<?php echo JRoute::_('index.php?option=com_fitness&layout=edit&id=' . (int) $this->item->id); ?>" method="post" enctype="multipart/form-data" name="adminForm" id="nutrition_recipe-form" class="form-validate">
    <div class="width-100 fltlft">
        <fieldset class="adminform">
            <legend><?php echo JText::_('COM_FITNESS_LEGEND_NUTRITION_RECIPE'); ?></legend>
            <table>
                <thead>
                    <tr>
                        <td>
                            <?php echo $this->form->getLabel('recipe_name'); ?>
                        </td>
                        <td>
                            <?php echo $this->form->getLabel('recipe_type'); ?>
                        </td>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <?php echo $this->form->getInput('recipe_name'); ?>
                        </td>
                        <td>
                            <?php echo $this->form->getInput('recipe_type'); ?> 
                        </td>
                    </tr>
                </tbody>
            </table>
            
            <table width="100%">
                <thead>
                    <tr>
                        <th>MEAL ITEM DESCRIPTION</th>
                        <th>QUANTITY</th>
                        <th>PRO (g)</th>
                        <th>FAT (g)</th>
                        <th>CARB (g)</th>
                        <th>CALS</th>
                        <th>ENRG (kJ)</th>
                        <th>FAT, SAT (g)</th>
                        <th>SUG (g)</th>
                        <th>SOD (mg)</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <input size="60" type="text"  name="meal_description[]" value="">
                        </td>
                        <td>
                            <input size="5" type="text"  name="meal_quantity[]" value="">
                        </td>
                        <td>
                            <input size="5" type="text" name="meal_pro[]" value="">
                        </td>
                        <td>
                            <input size="5" type="text" name="meal_fat[]" value="">
                        </td>
                        <td>
                            <input size="5" type="text" name="meal_carb[]" value="">
                        </td>
                        <td>
                            <input size="5" type="text" name="meal_cals[]" value="">
                        </td>
                        <td>
                            <input size="5" type="text" name="meal_enrg[]" value="">
                        </td>
                        <td>
                            <input size="5" type="text" name="meal_fat_sat[]" value="">
                        </td>
                        <td>
                            <input size="5" type="text" name="meal_sug[]" value="">
                        </td>
                        <td>
                            <input size="5" type="text" name="meal_sod[]" value="">
                        </td>
                        <td>
                            <a href="javascript:void(0)" class="delete_cros" title="delete"></a>
                        </td>
                    </tr>
                    
                </tbody>
            </table>
            
            

            <br/>
            <?php echo $this->form->getLabel('state'); ?>
            <?php echo $this->form->getInput('state'); ?>
        </fieldset>
    </div>

    
    <div style="display:none;">
    <?php echo $this->form->getLabel('created_by'); ?>
    <?php echo $this->form->getInput('created_by'); ?>
    <?php echo $this->form->getLabel('created'); ?>
    <?php echo $this->form->getInput('created'); ?>
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