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
<script type="text/javascript">
    
    $(document).ready(function(){


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
</script>
<style type="text/css">
    /* Temporary fix for drifting editor fields */
    .adminformlist li {
        clear: both;
    }
</style>
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
                <tbody id="meals_content">
                </tbody>
                <tfoot>
                    <tr id="totals_row">
                        <td></td>
                        <td><b>TOTALS</b></td>
                        <td><input readonly size="5" type="text"  id="meal_protein_input_total" value=""></td>
                        <td><input readonly size="5" type="text"  id="meal_fats_input_total" value=""></td>
                        <td><input readonly size="5" type="text"  id="meal_carbs_input_total" value=""></td>
                        <td><input readonly size="5" type="text"  id="meal_calories_input_total" value=""></td>
                        <td><input readonly size="5" type="text"  id="meal_energy_input_total" value=""></td>
                        <td><input readonly size="5" type="text"  id="meal_saturated_fat_input_total" value=""></td>
                        <td><input readonly size="5" type="text"  id="meal_total_sugars_input_total" value=""></td>
                        <td><input readonly size="5" type="text"  id="meal_sodium_input_total" value=""></td>
                    </tr>
                </tfoot>
            </table>
            <br/><br/>
            <input class="add_meal" type="button" id="add_item" value="Add New Item">
            

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

</form>

<script type="text/javascript">
    
    // SET VARIABLES AND CONSTANTS
    var _meal_form = $("#select_meal_form");
    var _add_meal = $(".add_meal");
    var _meals_content = $("#meals_content");
    var _meal_name_input = $(".meal_name_input");
    var _meal_quantity_input = $(".meal_quantity_input");
    var _selected_option = $("#ingredients_results option");
    
    var ingredient_obj = {id : "", meal_name : "", quantity : "", measurement : "", protein : "", fats : "", carbs : "", calories : "", energy : "", saturated_fat : "", total_sugars : "", sodium : ""};
    
    var _results_template = '<div id="select_meal_form"><span id="results_count"></span><select size="25" id="ingredients_results"></select></div>';
  
    var typingTimer;
    var doneTypingInterval = 1000;
 
 
    // ATTACH EVENTS
    $(document).ready(function(){
    
        _meal_name_input.live('input', function() {
            var search_text = $(this).val();
            if($('#select_meal_form').length == 0) {
                $(this).parent().append(_results_template);
            }
 
            clearTimeout(typingTimer);
            if (search_text) {
                typingTimer = setTimeout(
                    function() {getSearchIngredients(search_text, $("#ingredients_results"))},
                    doneTypingInterval
                );
            }
        });
        
        _selected_option.live('click', function() {
            var ingredient_id = $(this).val();
            var selected_ingredient_name = $(this).text();
            var closest_TR = $(this).closest("tr");
            
            setupTrDataId($(this));
            
            getIngredientData(ingredient_id, closest_TR, '');
            close_popup($("#select_meal_form"));
            closest_TR.find("input").val('');
            closest_TR.find(".meal_name_input").val(selected_ingredient_name);
            closest_TR.find(".meal_quantity_input").focus();

        });
        
        _add_meal.on('click', function() {
            var tr_html = createIngredientTR(ingredient_obj);
            _meals_content.append(tr_html);
            _meals_content.find("tr:last td:first input").focus();
        });
        
        _meal_quantity_input.live('keypress', function(e){
            if (e.keyCode == 13) {
                var quantity = $(this).val();
                var closest_TR = $(this).closest("tr");
                var ingredient_id = closest_TR.attr('data-ingredient_id');
                getIngredientData(ingredient_id, closest_TR, quantity);
            }
        })

    });
    

    // FUNCTIONS 
    
    function close_popup(element) {
        element.remove();
    }
    
    function getSearchIngredients(search_text, destination) {
        $.ajax({
            type : "POST",
            url : '<?php echo JUri::base() ?>index.php?option=com_fitness&tmpl=component&<?php echo JSession::getFormToken(); ?>=1',
            data : {
                view : 'nutrition_recipe',
                format : 'text',
                task : 'getSearchIngredients',
                search_text : search_text
              },
            dataType : 'json',
            success : function(response) {
                if(!response.status.IsSuccess) {
                    alert(response.status.Msg);
                    return;
                }
                
                $("#results_count").html('Search returned ' + response.count + ' ingredients.'); 
                
                destination.html(response.html);
                destination.find(":odd").css("background-color", "#F0F0EE")
            },
            error: function(XMLHttpRequest, textStatus, errorThrown)
            {
                alert("error");
            }
        });
    }

    
    function getIngredientData(id, closest_TR, quantity) {
       $.ajax({
            type : "POST",
            url : '<?php echo JUri::base() ?>index.php?option=com_fitness&tmpl=component&<?php echo JSession::getFormToken(); ?>=1',
            data : {
                view : 'nutrition_recipe',
                format : 'text',
                task : 'getIngredientData',
                id : id
              },
            dataType : 'json',
            success : function(response) {
                if(!response.status.IsSuccess) {
                    alert(response.status.Msg);
                    return;
                }
                if(!response.ingredient) return;
                var measurement = getMeasurement(response.ingredient.specific_gravity);
                closest_TR.find(".grams_mil").html(measurement);
                if(quantity) {
                    var  calculatedIngredient = calculatedIngredientItems(response.ingredient, quantity);
                    var TR_html = createIngredientTR(calculatedIngredient);
                    closest_TR.replaceWith(TR_html);
                    calculate_totals();
                }
              },
            error: function(XMLHttpRequest, textStatus, errorThrown)
            {
                alert("error");
            }
        }); 
    }
    
    function createIngredientTR(calculatedIngredient) {

        var html = '';
        html += '<tr data-ingredient_id="' + calculatedIngredient.id + '">'
        
        html += '<td>';
        html += '<input  size="60" type="text"  class="meal_name_input" value="' + calculatedIngredient.meal_name + '">';
        html += '</td>';
        
        html += '<td>';
        html += '<input size="5" type="text"  class="meal_quantity_input" value="' + calculatedIngredient.quantity + '">';
        html += '<span class="grams_mil">' + calculatedIngredient.measurement + '</span>';
        html += '</td>';
        
        html += '<td>';
        html += '<input readonly size="5" type="text"  class="meal_protein_input" value="' + calculatedIngredient.protein + '">';
        html += '</td>';
        
        html += '<td>';
        html += '<input readonly size="5" type="text"  class="meal_fats_input" value="' + calculatedIngredient.fats + '">';
        html += '</td>';

        html += '<td>';
        html += '<input readonly size="5" type="text"  class="meal_carbs_input" value="' + calculatedIngredient.carbs + '">';
        html += '</td>';

        html += '<td>';
        html += '<input readonly size="5" type="text"  class="meal_calories_input" value="' + calculatedIngredient.calories + '">';
        html += '</td>';
        
        html += '<td>';
        html += '<input readonly size="5" type="text"  class="meal_energy_input" value="' + calculatedIngredient.energy + '">';
        html += '</td>';

        html += '<td>';
        html += '<input readonly size="5" type="text"  class="meal_saturated_fat_input" value="' + calculatedIngredient.saturated_fat + '">';
        html += '</td>';

        html += '<td>';
        html += '<input readonly size="5" type="text"  class="meal_total_sugars_input" value="' + calculatedIngredient.total_sugars + '">';
        html += '</td>';

        html += '<td>';
        html += '<input readonly size="5" type="text"  class="meal_sodium_input" value="' + calculatedIngredient.sodium + '">';
        html += '</td>';
        
        html += '<td>';
        html += '<a href="javascript:void(0)" class="delete_cros" title="delete"></a>';
        html += '</td>';

        html += '</tr>';
        
        return html;
    }
    
    
    function round_2_sign(value) {
        return Math.round(value * 100)/100;
    }
    
    function calculatedIngredientItems(ingredient, quantity) {
        var calculated_ingredient = {};
        var specific_gravity = ingredient.specific_gravity;
        
        //quantity = 100;
        //specific_gravity = 1.03;
        //ingredient.protein = 3.2;
        calculated_ingredient.id = ingredient.id;
        
        calculated_ingredient.meal_name = ingredient.ingredient_name;
        
        calculated_ingredient.quantity = quantity;
        
        calculated_ingredient.measurement = getMeasurement(ingredient.specific_gravity);
        
        calculated_ingredient.protein = calculateDependsOnGravity(ingredient.protein, quantity, specific_gravity);
        
        calculated_ingredient.fats = calculateDependsOnGravity(ingredient.fats, quantity, specific_gravity);
        
        calculated_ingredient.carbs = calculateDependsOnGravity(ingredient.carbs, quantity, specific_gravity);
        
        calculated_ingredient.calories = calculateDependsOnGravity(ingredient.calories, quantity, specific_gravity);
        
        calculated_ingredient.energy = calculateDependsOnGravity(ingredient.energy, quantity, specific_gravity);
        
        calculated_ingredient.saturated_fat = calculateDependsOnGravity(ingredient.saturated_fat, quantity, specific_gravity);
        
        calculated_ingredient.total_sugars = calculateDependsOnGravity(ingredient.total_sugars, quantity, specific_gravity);
        
        calculated_ingredient.sodium = calculateDependsOnGravity(ingredient.sodium, quantity, specific_gravity);
        
        //console.log(ingredient.specific_gravity);
        //console.log(ingredient);
        //console.log(calculated_ingredient);
                
        return calculated_ingredient;
    }
    
    function calculateDependsOnGravity(value, quantity, specific_gravity) {
        var calculated_value;
        if(parseFloat(specific_gravity) > 0) {
            calculated_value = millilitresFormula(value, quantity, specific_gravity);
        } else {
            calculated_value = gramsFormula(value, quantity);
        }
        return calculated_value;
    }
    
    function gramsFormula(value, quantity) {
        return round_2_sign (value / 100 * quantity );
    }
    
    function millilitresFormula(value, quantity, specific_gravity) {
        return round_2_sign (value / 100 * quantity * specific_gravity );
    }
    
    function setupTrDataId(current_obj) {
        current_obj.closest("tr").attr('data-ingredient_id', current_obj.val());
    }
    
    function getMeasurement(specific_gravity) {
        if(parseFloat(specific_gravity) > 0) {
            return 'millilitres';
        } 
        return 'grams';
    }

    function calculate_totals() {
       set_item_total(get_item_total('meal_protein_input'), 'meal_protein_input_total');
       
       set_item_total(get_item_total('meal_fats_input'), 'meal_fats_input_total');
       
       set_item_total(get_item_total('meal_carbs_input'), 'meal_carbs_input_total');
       
       set_item_total(get_item_total('meal_calories_input'), 'meal_calories_input_total');
       
       set_item_total(get_item_total('meal_energy_input'), 'meal_energy_input_total');
       
       set_item_total(get_item_total('meal_saturated_fat_input'), 'meal_saturated_fat_input_total');
       
       set_item_total(get_item_total('meal_total_sugars_input'), 'meal_total_sugars_input_total');
       
       set_item_total(get_item_total('meal_sodium_input'), 'meal_sodium_input_total');
    }
    
    function get_item_total(element) {
       var item_array = $("." +element);
       var sum = 0;
       item_array.each(function(){
           var value = parseFloat($(this).val());
           if(value > 0) {
              sum += parseFloat(value); 
           }
           
       });
       return round_2_sign(sum);
    }
    
    function set_item_total(value, element) {
        $("#" + element).val(value);
    }
    
    
</script>

