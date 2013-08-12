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
            </table>
            <br/><br/>
            <input class="open_popup" type="button" id="add_item" value="Add New Item">
            

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

<div id="select_meal_form" style="display:none;">
    <a class="ui-dialog-titlebar-close ui-corner-all" role="button" href="#">
        <span class="close_popup ui-icon ui-icon-closethick"></span>
    </a>
    <br/>
    Find ingredient
    <br/>
    <input size="40" type="text"  id="meal_search_input" value="">
    <span id="results_count"></span>
    <br/>
    
    <select size="31" id="ingredients_results">
        
    </select>
    <div id="quantity_wrapper">Quantity <input size="2" type="text" id="quantity" name="quantity" value="100"></div>
    <input class="close_popup" type="button" id="meal_search_cancel" value="Cancel">
    <div id="ingred_select_error_message"></div>
    <input type="button" id="meal_search_next" value="Next">
</div>

<script type="text/javascript">
    
    // SET VARIABLES AND CONSTANTS
    var _meal_form = $("#select_meal_form");
    var _close_popup = $(".close_popup");
    var _open_popup = $(".open_popup");
    var _meal_search_input = $("#meal_search_input");
    var _ingredients_results = $("#ingredients_results");
    var _next_button = $("#meal_search_next");
    var select_error_message = $("#ingred_select_error_message");
  
    var typingTimer;
    var doneTypingInterval = 1000;
 
 
    // ATTACH EVENTS
    $(document).ready(function(){
    
        _open_popup.on('click', function() {open_popup(_meal_form)});
        
        _close_popup.on('click', function() {close_popup(_meal_form)});
        
        _meal_search_input.on('keyup', function() {
            clearTimeout(typingTimer);
            if (_meal_search_input.val()) {
                typingTimer = setTimeout(
                        function() {getSearchIngredients(_meal_search_input.val(), _ingredients_results)},
                        doneTypingInterval
                );
            }
        });
        
        _next_button.on('click', function() {
            var ingredient_id = _ingredients_results.find(":selected").val();
            var quantity = $("#quantity").val();
            onClickNext(ingredient_id, select_error_message, parseFloat(quantity));
        });
        
    });
    

    // FUNCTIONS 
    function open_popup(element) {
        element.fadeIn();
    }
    
    function close_popup(element) {
        element.fadeOut();
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
    
    
    function onClickNext(ingredient_id, select_error_message, quantity) {
        select_error_message.html('');
        if(!ingredient_id) {
            select_error_message.html('Select ingredient to proceed.');
            return;
        }
        
        if(!quantity || quantity < 1) {
            select_error_message.html('Set up quantity!');
            return;           
        }
        getIngredientData(ingredient_id, quantity);
    }
    
    function getIngredientData(id, quantity) {
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
                var  calculatedIngredient = calculatedIngredientItems(response.ingredient, quantity);
                var tr_html = createIngredientTR(calculatedIngredient);
                
                $("#meals_content").append(tr_html);
                
                close_popup($("#select_meal_form"));
                //console.log(tr_html);
            },
            error: function(XMLHttpRequest, textStatus, errorThrown)
            {
                alert("error");
            }
        }); 
    }
    
    function createIngredientTR(calculatedIngredient) {

        var html = '';
        html += '<tr>'
        
        html += '<td>';
        html += calculatedIngredient.meal_description;
        html += '</td>';
        
        html += '<td>';
        html += calculatedIngredient.quantity;
        html += '</td>';
        
        html += '<td>';
        html += calculatedIngredient.protein;
        html += '</td>';
        
        html += '<td>';
        html += calculatedIngredient.fats;
        html += '</td>';

        html += '<td>';
        html += calculatedIngredient.carbs;
        html += '</td>';

        html += '<td>';
        html += calculatedIngredient.calories;
        html += '</td>';
        
        html += '<td>';
        html += calculatedIngredient.energy;
        html += '</td>';

        html += '<td>';
        html += calculatedIngredient.saturated_fat;
        html += '</td>';

        html += '<td>';
        html += calculatedIngredient.total_sugars;
        html += '</td>';

        html += '<td>';
        html += calculatedIngredient.sodium;
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
        calculated_ingredient.meal_description = ingredient.ingredient_name;
        
        calculated_ingredient.quantity = quantity;
        
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

</script>

