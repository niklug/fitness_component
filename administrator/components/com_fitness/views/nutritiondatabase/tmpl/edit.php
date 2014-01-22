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
<style type="text/css">
#jform_description-lbl{
    float: none;
}

.adminformlist li {
    clear: both;
}

.error_message{
    color:#C00;
    padding-top: 7px;
}

</style>


<form action="<?php echo JRoute::_('index.php?option=com_fitness&layout=edit&id=' . (int) $this->item->id); ?>" method="post" enctype="multipart/form-data" name="adminForm" id="nutritiondatabase-form" class="form-validate">
    <div class="width-60 fltlft">
        <fieldset class="adminform">
            <legend><?php echo JText::_('COM_FITNESS_LEGEND_NUTRITIONDATABASE'); ?></legend>
            <ul class="adminformlist">

                <li><?php echo $this->form->getLabel('ingredient_name'); ?>
                <?php echo $this->form->getInput('ingredient_name'); ?></li>
                <li><?php echo $this->form->getLabel('category'); ?>
                <?php echo $this->form->getInput('category'); ?></li>
                <li><?php echo $this->form->getLabel('description'); ?>
                <?php echo $this->form->getInput('description'); ?></li>
                <br/><br/><br/>
                <p><b>NOTE: Enter ALL nutrient values based on 100g Edible Portion (EP)</b></p>


                <li><?php echo $this->form->getLabel('measurement_unit'); ?>
                <?php echo $this->form->getInput('measurement_unit'); ?></li>
                    <div class="main_fields_wrapper" style="display: none;">
                        <li id="measurement_unit_wrapper" style="display:none;">
                            <p style="font-style:italic;">
                                You are entering a liquid ingredient. See this <a target="_blank" href="<?php echo  JUri::base() . 'components' . DS . 'com_fitness' . DS .'assets'. DS . 'includes' . DS . 'Specific gravity appendix.pdf' ?>">LIST</a> for a suitable specific gravity
                                <br/>for your product. Is this the correct ‘specific gravity’ for this ingredient?
                            </p>
                            <table>
                                <tr>
                                    <td>
                                        <label id="jform_specific_gravity-lbl" class="" for="jform_specific_gravity">Specific Gravity</label> 
                                    </td>
                                    <td>
                                        <input id="jform_specific_gravity" class="inputbox validate-numeric" type="text" size="10" value="" name="jform[specific_gravity]">
                                    </td>
                                    <td>
                                        SG = ml for 100 grams 
                                    </td>
                                    <td>
                                        <input readonly id="specific_gravity_grams" class="inputbox" type="text" size="10" value="" name="specific_gravity_grams"><span style="position: absolute;padding-top:5px;">ml</span>
                                    </td>
                                </tr>
                            </table>
                            <p>
                                To correctly enter the nutritional values of this liquid ingredient into the
                                database, you must enter the product nutritional values (from the nutrition panel
                                on the product) taken from the "average quantity per 100ml" serving size column...
                            </p>
                        </li>
                        <div class="clr"></div>
                    </div>
                </ul>
                <div class="main_fields_wrapper" style="display: none;"> 
                    <div class="clr"></div>
                    <table id="ingradient_fields" width="100%">
                        <tr>
                            <td class="millilitres_column" width="50%">
                                <b>Enter Nutrition Info</b><br/>
                                (as on product label: “average per 100ml”)
                            </td>
                            <td id="right_title"  width="50%">
                                <b>Values as 100g Edible Portion (EP)</b><br/>
                                (stored in nutrition database)                       
                            </td>
                        </tr>

                        <tr>
                            <td class="millilitres_column">

                            </td>
                            <td>
                                <?php echo $this->form->getLabel('calories'); ?>
                                <?php echo $this->form->getInput('calories'); ?>
                            </td>
                        </tr>

                        <tr>
                            <td class="millilitres_column">
                                <?php echo $this->form->getLabel('energy'); ?>
                                <input id="enter_energy" class="inputbox validate-numeric" type="text" size="10" value="" name="enter_energy">
                            </td>
                            <td>
                                <?php echo $this->form->getLabel('energy'); ?>
                                <?php echo $this->form->getInput('energy'); ?>
                            </td>
                        </tr>

                        <tr>
                            <td class="millilitres_column">
                                <?php echo $this->form->getLabel('protein'); ?>
                                <input id="enter_protein" class="inputbox validate-numeric" type="text" size="10" value="" name="enter_protein">
                            </td>
                            <td>
                                <?php echo $this->form->getLabel('protein'); ?>
                                <?php echo $this->form->getInput('protein'); ?>
                            </td>
                        </tr>

                        <tr>
                            <td class="millilitres_column">
                                <?php echo $this->form->getLabel('fats'); ?>
                                <input id="enter_fats" class="inputbox validate-numeric" type="text" size="10" value="" name="enter_fats">
                            </td>
                            <td>
                                <?php echo $this->form->getLabel('fats'); ?>
                                <?php echo $this->form->getInput('fats'); ?>
                            </td>
                        </tr>

                        <tr>
                            <td class="millilitres_column">
                                <?php echo $this->form->getLabel('saturated_fat'); ?>
                                <input id="enter_saturated_fat" class="inputbox validate-numeric" type="text" size="10" value="" name="enter_saturated_fat">
                                <div class="clr"></div>
                            </td>
                            <td>
                                <?php echo $this->form->getLabel('saturated_fat'); ?>
                                <?php echo $this->form->getInput('saturated_fat'); ?>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                               <div id="saturated_error" class="error_message"></div>
                            </td>
                        </tr>

                        <tr>
                            <td class="millilitres_column">
                                <?php echo $this->form->getLabel('carbs'); ?>
                                <input id="enter_carbs" class="inputbox validate-numeric" type="text" size="10" value="" name="enter_carbs">
                            </td>
                            <td>
                                <?php echo $this->form->getLabel('carbs'); ?>
                                <?php echo $this->form->getInput('carbs'); ?>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                               <div id="sum_100_error" class="error_message"></div>
                            </td>
                        </tr>

                        <tr>
                            <td class="millilitres_column">
                                <?php echo $this->form->getLabel('total_sugars'); ?>
                                <input id="enter_total_sugars" class="inputbox validate-numeric" type="text" size="10" value="" name="enter_total_sugars">
                            </td>
                            <td>
                                <?php echo $this->form->getLabel('total_sugars'); ?>
                                <?php echo $this->form->getInput('total_sugars'); ?>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                               <div id="sugars_error" class="error_message"></div> 
                            </td>
                        </tr>

                        <tr>
                            <td class="millilitres_column">
                                <?php echo $this->form->getLabel('sodium'); ?>
                                <input id="enter_sodium" class="inputbox validate-numeric" type="text" size="10" value="" name="enter_sodium">
                            </td>
                            <td>
                                <?php echo $this->form->getLabel('sodium'); ?>
                                <?php echo $this->form->getInput('sodium'); ?>
                            </td>
                        </tr>
                    </table>
                    <br/>
                    <?php echo $this->form->getLabel('state'); ?>
                    <?php echo $this->form->getInput('state'); ?>
                </div>
        </fieldset>
    </div>

    

    <input type="hidden" name="task" value="" />
    <?php echo JHtml::_('form.token'); ?>
    <div class="clr"></div>

</form>

<script type="text/javascript">

    (function($) {
        
        var options = {
            'specific_gravity' : '<?php echo $this->item->specific_gravity; ?>'
        }
        
        var recipe_database = $.recipe_database(options);
        recipe_database.run();
        
        
        
        
        Joomla.submitbutton = function(task)
        {

            if (task == 'nutritiondatabase.cancel') {
                Joomla.submitform(task, document.getElementById('nutritiondatabase-form'));
            }
            else{

                if (task != 'nutritiondatabase.cancel' && document.formvalidator.isValid(document.id('nutritiondatabase-form'))) {
                    if(recipe_database.validate_form() != true) return;
                    Joomla.submitform(task, document.getElementById('nutritiondatabase-form'));
                }
                else {
                    alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED')); ?>');
                }
            }
        }
    
    })($js);
</script>