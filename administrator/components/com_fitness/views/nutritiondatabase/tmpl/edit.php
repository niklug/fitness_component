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
                <li><?php echo $this->form->getLabel('description'); ?>
                <?php echo $this->form->getInput('description'); ?></li>
                <br/><br/><br/>
                <p><b>NOTE: Enter ALL nutrient values based on 100g Edible Portion (EP)</b></p>


                <li><?php echo $this->form->getLabel('measurement_unit'); ?>
                <?php echo $this->form->getInput('measurement_unit'); ?></li>

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
                                


            </ul>
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
        </fieldset>
    </div>

    

    <input type="hidden" name="task" value="" />
    <?php echo JHtml::_('form.token'); ?>
    <div class="clr"></div>

</form>

<script type="text/javascript">

    (function($) {

        // input focus out events
        $("#jform_calories").on('focusout', function() {
            calculate_energy();
        });
        
        $("#jform_energy").on('focusout', function() {
            calculate_calories();
        });
        
        $("#jform_saturated_fat").on('focusout', function() {
            validate_saturated_fat();
        });
        
        $("#jform_fats").on('focusout', function() {
            validate_saturated_fat();
            validate_sum_100();
        });
        
       
        $("#jform_protein").on('focusout', function() {
            validate_sum_100();
        });
        
        $("#jform_carbs").on('focusout', function() {
            validate_sum_100();
            validate_sugars();
        });
        
        $("#jform_total_sugars").on('focusout', function() {
            validate_sugars();
        });
        
        
        $("#jform_measurement_unit").on('change', function() {
            var measurement_unit = $(this).find(':selected').val();
            set_measurement_unit(measurement_unit);
        });   
        
        $("#jform_specific_gravity").on('focusout', function() {
            var specific_gravity = parse_comma_number($(this).val());
            specific_gravity_set_grams(specific_gravity);
        });
        
        check_specific_gravity();
        
        
        ///////////////// 
        
        $("#enter_energy").on('focusout', function() {
            set_converted_value($(this).val(), 'jform_energy');
        });
        
        $("#enter_protein").on('focusout', function() {
            set_converted_value($(this).val(), 'jform_protein');
            validate_sum_100();
        });
                
        $("#enter_fats").on('focusout', function() {
            set_converted_value($(this).val(), 'jform_fats');
            validate_saturated_fat();
            validate_sum_100();
        });
        
                        
        $("#enter_saturated_fat").on('focusout', function() {
            set_converted_value($(this).val(), 'jform_saturated_fat');
            validate_saturated_fat();
        });
        
        $("#enter_carbs").on('focusout', function() {
            set_converted_value($(this).val(), 'jform_carbs');
            validate_sum_100();
            validate_sugars();
        });

        $("#enter_total_sugars").on('focusout', function() {
            set_converted_value($(this).val(), 'jform_total_sugars');
            validate_sugars();
        });
        
        
        $("#enter_sodium").on('focusout', function() {
            set_converted_value($(this).val(), 'jform_sodium');
        });
        
        $("#enter_energy").on('focusout', function() {
            calculate_calories();
        });
        
        $("#jform_specific_gravity").on('focusout', function() {
            on_specific_gravity_change();
        });        
        
    
        function validate_form() {
            var saturated_fat_error = validate_saturated_fat();
            var sum_100_error = validate_sum_100();
            var sugars_error = validate_sugars();
            if(saturated_fat_error && sum_100_error && sugars_error) {
                return true;
            }
            return false;
        }

        function validate_saturated_fat() {
            var saturated_fat = parse_comma_number($("#jform_saturated_fat").val());
            var total_fat = parse_comma_number($("#jform_fats").val());

            if(parseFloat(saturated_fat) > parseFloat(total_fat)) {
                $("#saturated_error").html('Saturated fat value must be less than or equal to the total fat value.')
                return false;
            } else {
                $("#saturated_error").html('');
                return true;
            } 
        }


        function validate_sum_100() {
            var protein = parse_comma_number($("#jform_protein").val());
            var total_fat = parse_comma_number($("#jform_fats").val());
            var carbohydrate  = parse_comma_number($("#jform_carbs").val());
            var sum = parseFloat(protein) + parseFloat(total_fat) + parseFloat(carbohydrate);
            if(sum > 100) {
                $("#sum_100_error").html('Sum of proximates cannot exceed 100g.')
                return false;
            } else {
                $("#sum_100_error").html('');
                return true;
            } 
        }

        function validate_sugars() {
            var sugars = parse_comma_number($("#jform_total_sugars").val());
            var carbs = parse_comma_number($("#jform_carbs").val());
            if(parseFloat(sugars) > parseFloat(carbs)) {
                $("#sugars_error").html('Sugar value must be less than or equal to the carbohydrate value.')
                return false;
            } else {
                $("#sugars_error").html('');
                return true;
            }  
        }

        function check_specific_gravity() {
            var specific_gravity = parse_comma_number('<?php echo $this->item->specific_gravity; ?>');
            var measurement_unit =  $("#jform_measurement_unit").val();
            hide_left_column();
            $("#right_title").html("<b>Enter Nutrition Info</b><br/>(as on product label: “average per 100g”) ");
            if(specific_gravity || (measurement_unit == '2')) {
                $("#jform_measurement_unit").val('2');
                $("#jform_specific_gravity").val(specific_gravity);
                $("#measurement_unit_wrapper").show();
                set_measurement_unit('2');
                specific_gravity_set_grams(specific_gravity);
                show_left_column();
            }
        }

        function specific_gravity_set_grams(specific_gravity) {
            if(specific_gravity == 0) return;
            $("#specific_gravity_grams").val(round_2_sign(parseFloat(100/specific_gravity)));
        }

        function set_measurement_unit(measurement_unit) {
            if(measurement_unit == '2') {
                $("#measurement_unit_wrapper").show();
                show_left_column();
                $("#right_title").html("<b>Values as 100g Edible Portion (EP)</b><br/>(stored in nutrition database) ");
            } else {
                $("#jform_specific_gravity").val('');
                $("#specific_gravity_grams").val('');
                $("#measurement_unit_wrapper").hide();
                hide_left_column();
                $("#right_title").html("<b>Enter Nutrition Info</b><br/>(as on product label: “average per 100g”) ");
            }
        }

        function parse_comma_number(str) {
            return str.replace(',' ,'.');
        }

        function calculate_energy() {
            var calories = parse_comma_number($("#jform_calories").val());
            var energy = calories * 4.184;
            energy = round_2_sign(energy);
            $("#jform_energy").val(energy);
        }

        function calculate_calories(type) {
            var energy = parse_comma_number($("#jform_energy").val());;
            var calories = energy / 4.184;
            calories = round_2_sign(calories);
            $("#jform_calories").val(calories);
        }

        function round_2_sign(value) {
            return Math.round(value * 100)/100;
        }
        //////////////////////////////

        function set_converted_value(value, field_id) {
            var specific_gravity_grams = $("#specific_gravity_grams").val();
            if(!specific_gravity_grams) {
                alert('Set up Specific Gravity field first!');
                return;
            }
            var convertedValue = (value / 100) * specific_gravity_grams;
            convertedValue = round_2_sign(convertedValue);
            $("#" + field_id).val(convertedValue);
        }

        function hide_left_column() {
            $(".millilitres_column").hide();
        }

        function show_left_column() {
            $(".millilitres_column").show();
        }

        function on_specific_gravity_change() {
            set_converted_value($("#enter_energy").val(), 'jform_energy');

            set_converted_value($("#enter_protein").val(), 'jform_protein');

            set_converted_value($("#enter_fats").val(), 'jform_fats');

            set_converted_value($("#enter_saturated_fat").val(), 'jform_saturated_fat');

            set_converted_value($("#enter_carbs").val(), 'jform_carbs');

            set_converted_value($("#enter_total_sugars").val(), 'jform_total_sugars');

            set_converted_value($("#enter_sodium").val(), 'jform_sodium');

            calculate_calories();
        }
        
        Joomla.submitbutton = function(task)
        {

            if (task == 'nutritiondatabase.cancel') {
                Joomla.submitform(task, document.getElementById('nutritiondatabase-form'));
            }
            else{

                if (task != 'nutritiondatabase.cancel' && document.formvalidator.isValid(document.id('nutritiondatabase-form'))) {
                    if(validate_form() != true) return;
                    Joomla.submitform(task, document.getElementById('nutritiondatabase-form'));
                }
                else {
                    alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED')); ?>');
                }
            }
        }
    
    })($js);
</script>