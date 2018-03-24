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
    /* Temporary fix for drifting editor fields */
    .adminformlist li {
        clear: both;
    }

    #jform_instructions-lbl{
        float: none;
    }

    .error_message{
        color:#C00;
        padding-top: 7px;
}

</style>

<div class="width-100 fltlft">
    <fieldset class="adminform">
        <legend>RECIPE DETAILS</legend>
        <table>
            <tr>
                <td>
                    Recipe Name: 
                </td>
                <td>
                    <?php echo $this->item->recipe_name; ?>
                </td>
            </tr>
            <tr>
                <td>
                    <?php echo $this->form->getLabel('recipe_type'); ?>

                </td>
                <td>
                    <?php echo $this->getRecipeTypeByName($this->item->recipe_type); ?> 
                </td>
            </tr>
            <tr>
                <td>
                    Author: 

                </td>
                <td>
                    <?php echo JFactory::getUser($this->item->created_by)->name; ?> 
                </td>
            </tr>
        </table>
        <table width="100%">
            <thead>
                <tr>
                    <th width="450">MEAL ITEM DESCRIPTION</th>
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
                <tr id="">
                    <td>NUMBER OF SERVES:  <?php echo $this->item->number_serves ?></td>
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
        <div class="clr"></div>
        <br/>
        <div id="comments_wrapper"></div>
        <div class="clr"></div>
        <hr>
        <br/>
        <b>PORTION SIZE / ADD TO DIARY ENTRY</b>
        <br/>
        <table>
            <tr>
                <td>
                    How many/serves do you wish to add as your meal?
                </td>
                <td>
                    <input class="required"  size="5" type="text"  id="number_serves" value="">
                </td>
                <td>
                    serves
                </td>
                <td>
                    <div id="error_wrapper" style="display:none;color:red;">Wrong value!</div>
                </td>
            </tr>
        </table>
        <hr>
        <input  type="button" id="add_to_diary" value="Add To Diary">
        <input  type="button" id="cancel_diary" value="Cancel">
    </fieldset>
</div>

<?php
$nutrition_plan_id = JRequest::getVar('nutrition_plan_id');
$meal_id = JRequest::getVar('meal_id');
$type = JRequest::getVar('type');
$parent_view = JRequest::getVar('parent_view');

if($parent_view == 'nutrition_diary_frontend') $table = '#__fitness_nutrition_diary_ingredients';
if($parent_view == 'nutrition_plan_backend') $table = '#__fitness_nutrition_plan_ingredients';

?>

<script type="text/javascript">
    (function($) {
        // SET VARIABLES AND CONSTANTS
        var _recipe_id = '<?php echo (int) $this->item->id;?>';
        var _meal_form = $("#select_meal_form");
        var _add_meal = $(".add_meal");
        var _meals_content = $("#meals_content");
        var _meal_name_input = $(".meal_name_input");
        var _meal_quantity_input = $(".meal_quantity_input");
        var _selected_option = $("#ingredients_results option");
        var _delete_meal = $(".delete_meal");
        var _add_comment = $("#add_comment");
        var _comments_wrapper = $("#comments_wrapper");
        var _save_comment = $(".save_comment");
        var _delete_comment = $(".delete_comment");
        var _user_name = '<?php echo JFactory::getUser()->name;?>'

        var ingredient_obj = {id : "", meal_name : "", quantity : "", measurement : "", protein : "", fats : "", carbs : "", calories : "", energy : "", saturated_fat : "", total_sugars : "", sodium : ""};
        var _comment_obj = {'user_name' : _user_name, 'created' : "", 'comment' : ""};

        var _results_template = '<div id="select_meal_form"><span id="results_count"></span><select size="25" id="ingredients_results"></select></div>';

        var _nutrition_plan_id = '<?php echo $nutrition_plan_id;?>';
        var _meal_id = '<?php echo $meal_id;?>';
        var _type = '<?php echo $type;?>';
        var _number_serves_recipe = '<?php echo $this->item->number_serves ?>';
        var _import_obj = {
                    'nutrition_plan_id' : _nutrition_plan_id,
                    'meal_id' : _meal_id,
                    'type' : _type,
                    'recipe_id' : _recipe_id, 
                    'number_serves_recipe' : _number_serves_recipe,
                    'db_table' : '<?php echo $table;?>'
        };

        // ATTACH EVENTS

        populateTable(_recipe_id, _meals_content);

        // comments
        //populateComments(_recipe_id, _comments_wrapper);

        $("#cancel_diary").on('click', function() {
            window.location.href = '<?php echo JUri::base() ?>index.php?option=com_fitness&view=nutrition_recipes&tmpl=component&layout=popup_view';
        });


        $("#add_to_diary").on('click', function() {
            $("#error_wrapper").hide();
            var number_serves = $("#number_serves").val();
            if(!validateInteger(number_serves) ||  parseInt(number_serves) <= 0) {
                $("#error_wrapper").show();
                return;
            }
            _import_obj.number_serves = number_serves;
            $("#add_to_diary").attr('disabled', 'disabled');
            importRecipe(_import_obj, function(output) {
                if(output) {
                    //window.parent.populateMealsLogic();
                    window.parent.location.reload();
                    //window.parent.closeRecipePopup();
                }
            });
        })



        // FUNCTIONS 

        function importRecipe(o, handleData) {
            var table = o.db_table;
            var data_encoded = JSON.stringify(o);
            $.ajax({
                type : "POST",
                url : '<?php echo JUri::base() ?>index.php?option=com_fitness&tmpl=component&<?php echo JSession::getFormToken(); ?>=1',
                data : {
                    view : 'nutrition_plan',
                    format : 'text',
                    task : 'importRecipe',
                    data_encoded : data_encoded,
                    table : table
                },
                dataType : 'json',
                success : function(response) {
                    if(!response.status.success) {
                        alert(response.status.message);
                        return;
                    }
                    handleData(response.status.success);
                  },
                error: function(XMLHttpRequest, textStatus, errorThrown)
                {
                    alert("error importRecipe");
                }
            }); 

        }
        function validateInteger(value) {
            return (value.test(/^\d+$/));
        }


        function createIngredientTR(calculatedIngredient) {

            var html = '';
            html += '<tr data-ingredient_id="' + calculatedIngredient.ingredient_id + '"  data-id="' + calculatedIngredient.id + '">'

            html += '<td>';
            html += '<input readonly size="60" type="text"  class="meal_name_input" value="' + calculatedIngredient.meal_name + '">';
            html += '</td>';

            html += '<td>';
            html += '<input readonly size="5"  type="text"  class="meal_quantity_input" value="' + calculatedIngredient.quantity + '">';
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
            calculated_ingredient.ingredient_id = ingredient.id;

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

        function populateTable(recipe_id, meals_content) {
            if(!recipe_id) return;
            $.ajax({
                type : "POST",
                url : '<?php echo JUri::base() ?>index.php?option=com_fitness&tmpl=component&<?php echo JSession::getFormToken(); ?>=1',
                data : {
                    view : 'nutrition_recipe',
                    format : 'text',
                    task : 'populateTable',
                    recipe_id : recipe_id
                  },
                dataType : 'json',
                success : function(response) {
                    if(!response.status.success) {
                        alert(response.status.Msg);
                        return;
                    }
                    var recipe_meals = response.recipe_meals;
                    if(!recipe_meals) return;

                    var html = '';
                    recipe_meals.each(function(meal){
                        html += createIngredientTR(meal);
                    });
                    meals_content.html(html);
                    calculate_totals();
                    //console.log(html);
                    },
                error: function(XMLHttpRequest, textStatus, errorThrown)
                {
                    alert("error");
                }
            }); 
        }

        function pad(d) {
            return (d < 10) ? '0' + d.toString() : d.toString();
        }

        function createCommentTemplate(comment_obj) {
            var d1 = new Date();
            if(comment_obj.created) {
                d1 = new Date(Date.parse(comment_obj.created));
            }

            var current_time = getCurrentDate(d1);
            var comment_template = '<div data-id="' + comment_obj.id + '" class="comment_wrapper">';
            comment_template += '<table width="100%">';
            comment_template += '<tr>';
            comment_template += '<td><b>Comment by: </b><span class="comment_by">' + comment_obj.user_name +  '</span></td>';
            comment_template += '<td><b>Date: </b> <span class="comment_date">' + current_time.date +  '</span></td>';
            comment_template += '<td><b>Time: </b> <span class="comment_time">' + current_time.time_short +  '</span></td>';
            comment_template += '<td></td>'
            comment_template += '<td align="center"></td>';
            comment_template += '</tr>';
            comment_template += '<tr>';
            comment_template += '<td colspan="5"><textarea readonly class="comment_textarea" cols="100" rows="3">' + comment_obj.comment +  '</textarea></td>';
            comment_template += '</tr>';
            comment_template += '</table>';
            comment_template += '</div>';
            return comment_template;
        }

        function getCurrentDate(d1) {
            var date = d1.getFullYear() + "-" + (pad(d1.getMonth()+1)) + "-" + pad(d1.getDate()); 
            var time = pad(d1.getHours()) + ":" + pad(d1.getMinutes()) + ":" + pad(d1.getSeconds());
            var time_short = pad(d1.getHours()) + ":" + pad(d1.getMinutes());
            return {'date' : date, 'time' : time, 'time_short' : time_short};
        }

        function populateComments(recipe_id, comments_wrapper) {
            if(!recipe_id) return;
            $.ajax({
                type : "POST",
                url : '<?php echo JUri::base() ?>index.php?option=com_fitness&tmpl=component&<?php echo JSession::getFormToken(); ?>=1',
                data : {
                    view : 'nutrition_recipe',
                    format : 'text',
                    task : 'populateComments',
                    recipe_id : recipe_id
                  },
                dataType : 'json',
                success : function(response) {
                    if(!response.status.success) {
                        alert(response.status.Msg);
                        return;
                    }
                    var comments = response.comments;
                    if(!comments) return;

                    var html = '';
                    comments.each(function(comment_obj){
                    //console.log(comment_obj);            
                        html += createCommentTemplate(comment_obj);
                    });
                    comments_wrapper.html(html);
                    //console.log(html);
                    },
                error: function(XMLHttpRequest, textStatus, errorThrown)
                {
                    alert("error");
                }
            }); 
        }
        
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
   })($js);
</script>

