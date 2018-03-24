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
<form action="<?php echo JRoute::_('index.php?option=com_fitness&layout=edit&id=' . (int) $this->item->id); ?>" method="post" enctype="multipart/form-data" name="adminForm" id="nutrition_recipe-form" class="form-validate">
    <div class="width-100 fltlft">
        <fieldset class="adminform">
            <legend><?php echo JText::_('COM_FITNESS_LEGEND_NUTRITION_RECIPE'); ?></legend>
            <table>
                    <tr>
                        <td width="150">
                            <?php echo $this->form->getLabel('recipe_name'); ?>
                        </td>
                        <td width="150">
                            <?php echo $this->form->getInput('recipe_name'); ?>
                        </td>
  
                    </tr>
                    <tr>
                        <td style="text-align: left;">
                            <div id="image_upload_content"></div>
                        </td>
                        <td style="text-align: left;">
                            <div id="video_upload_content"></div>
                        </td>
                    </tr>
                    
                    
                    
                    <tr>
                        <td>
                            <?php echo $this->form->getLabel('recipe_type'); ?>
                            <?php
                            $recipe_types = $helper->getRecipeTypes();
                            if(!$recipe_types['success']) {
                                JError::raiseError($recipe_types['message']);
                            }
                            $recipe_types = $recipe_types['data'];
                                           
                            echo $helper->generateMultipleSelect($recipe_types, 'jform[recipe_type]', 'jform_recipe_type', $this->item->recipe_type, '', true, 'inputbox');
                            
                            ?> 
                        </td>
                        <td>
                            <?php echo $this->form->getLabel('recipe_variation'); ?>
                            <?php
                            $recipe_variations = $helper->getRecipeVariations();
                            echo $helper->generateMultipleSelect($recipe_variations, 'jform[recipe_variation]', 'jform_recipe_variation', $this->item->recipe_variation, '', true, 'inputbox');
                            
                            ?> 
                     
                        </td>
                    </tr>
                </tbody>
            </table>
            <hr>
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
                    <tr id="totals_row">
                        <td><input class="add_meal" type="button" id="add_item" value="Add New Item"></td>
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
            <hr>

            <br/>
            <?php echo $this->form->getLabel('number_serves'); ?>
            <?php echo $this->form->getInput('number_serves'); ?>
            <br/>
            <?php echo $this->form->getLabel('instructions'); ?>
            <?php echo $this->form->getInput('instructions'); ?>
            <div class="clr"></div>
            <hr>
            <div id="comments_wrapper"> </div>
            <div class="clr"></div>
            <input id="add_comment_0" class="" type="button" value="Add Comment" >
            <div class="clr"></div>
            <hr>
            <?php echo $this->form->getLabel('status'); ?>
            <?php echo $this->form->getInput('status'); ?>
            <div class="clr"></div>
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
    <?php echo $this->form->getInput('video'); ?>
    <?php echo $this->form->getInput('image'); ?>
    <?php echo JHtml::_('form.token'); ?>
    <div class="clr"></div>

</form>

<div id="emais_sended"></div>


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




        var typingTimer;
        var doneTypingInterval = 1000;


        // ATTACH EVENTS
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

            getIngredientData(_recipe_id, ingredient_id, closest_TR, '');
            close_popup($("#select_meal_form"));
            closest_TR.find("input").val('');
            closest_TR.find(".meal_name_input").val(selected_ingredient_name);
            closest_TR.find(".meal_quantity_input").focus();

        });

        _add_meal.on('click', function() {
            if(_recipe_id == 0) {
                alert("Please save this Recipe before proceeding to add items/ingredients");
                return;
            }
            var tr_html = createIngredientTR(ingredient_obj);
            _meals_content.append(tr_html);
            _meals_content.find("tr:last td:first input").focus();
        });

        _meal_quantity_input.live('focusout', function(e){
            var quantity = $(this).val();
            var closest_TR = $(this).closest("tr");
            var ingredient_id = closest_TR.attr('data-ingredient_id');
            getIngredientData(_recipe_id, ingredient_id, closest_TR, quantity);

        });

        _delete_meal.live('click', function(){
            var closest_TR = $(this).closest("tr");
            var meal_id = closest_TR.attr('data-id');
            deleteMeal(meal_id, closest_TR);
        });
        
        

            populateTable(_recipe_id, _meals_content);
  


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
                    if(!response.status.success) {
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


        function getIngredientData(recipe_id, id, closest_TR, quantity) {
            var recipe_id = recipe_id;
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
                    if(!response.status.success) {
                        alert(response.status.Msg);
                        return;
                    }
                    if(!response.ingredient) return;
                    var measurement = getMeasurement(response.ingredient.specific_gravity);
                    closest_TR.find(".grams_mil").html(measurement);
                    if(quantity) {
                        var  calculatedIngredient = calculatedIngredientItems(response.ingredient, quantity);

                        calculatedIngredient.recipe_id = recipe_id;

                        var id = closest_TR.attr('data-id');

                        calculatedIngredient.id = id;  

                        saveMeal(calculatedIngredient, function(output){
                            var inserted_id = output;
                            if(inserted_id) {
                                calculatedIngredient.id = inserted_id;
                                var TR_html = createIngredientTR(calculatedIngredient);
                                closest_TR.replaceWith(TR_html);
                                calculate_totals();
                             }
                        });

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
            html += '<tr data-ingredient_id="' + calculatedIngredient.ingredient_id + '"  data-id="' + calculatedIngredient.id + '">'

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
            html += '<a href="javascript:void(0)" class="delete_meal" title="delete"></a>';
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

        function saveMeal(calculatedIngredient, handleData) {
            var ingredient_encoded = JSON.stringify(calculatedIngredient);
            //console.log(calculatedIngredient);
            $.ajax({
                type : "POST",
                url : '<?php echo JUri::base() ?>index.php?option=com_fitness&tmpl=component&<?php echo JSession::getFormToken(); ?>=1',
                data : {
                    view : 'nutrition_recipe',
                    format : 'text',
                    task : 'saveMeal',
                    ingredient_encoded : ingredient_encoded
                  },
                dataType : 'json',
                success : function(response) {
                    if(!response.status.success) {
                        alert(response.status.Msg);
                        return;
                    }
                    handleData(response.inserted_id);
                  },
                error: function(XMLHttpRequest, textStatus, errorThrown)
                {
                    alert("error");
                }
            }); 
         }

        function deleteMeal(id, closest_TR) {
            $.ajax({
                type : "POST",
                url : '<?php echo JUri::base() ?>index.php?option=com_fitness&tmpl=component&<?php echo JSession::getFormToken(); ?>=1',
                data : {
                    view : 'nutrition_recipe',
                    format : 'text',
                    task : 'deleteMeal',
                    id : id
                  },
                dataType : 'json',
                success : function(response) {
                    if(!response.status.success) {
                        alert(response.status.Msg);
                        return;
                    }
                    closest_TR.remove();
                    calculate_totals();
                    },
                error: function(XMLHttpRequest, textStatus, errorThrown)
                {
                    alert("error");
                }
            }); 
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

        // comments
        var comment_options = {
            'item_id' : '<?php echo $this->item->id;?>',
            'fitness_administration_url' : '<?php echo JURI::root();?>administrator/index.php?option=com_fitness&tmpl=component&<?php echo JSession::getFormToken(); ?>=1',
            'comment_obj' : {'user_name' : '<?php echo JFactory::getUser()->name;?>', 'created' : "", 'comment' : ""},
            'db_table' : '#__fitness_nutrition_recipes_comments',
            'read_only' : false,
            'anable_comment_email' : true,
            'comment_method' : 'RecipeComment'
        }
        
        if(comment_options.item_id) {
        
            var comments = $.comments(comment_options, comment_options.item_id, 0);
            var comments_html = comments.run();
            $("#comments_wrapper").html(comments_html);
        }
        
        //IMAGE
        
        var imagepath = '<?php echo $this->item->image;?>';
        var filename = '';
        if(typeof imagepath !== 'undefined') {
            var fileNameIndex = imagepath.lastIndexOf("/") + 1;
            filename = imagepath.substr(fileNameIndex);
        }



        var image_upload_options = {
            'url' : '<?php echo JURI::root();?>index.php?option=com_fitness&view=recipe_database&task=uploadImage&format=text',
            'base_url' : '<?php echo JURI::root();?>',
            'picture' : filename,
            'default_image' : '<?php echo JURI::root();?>administrator/components/com_fitness/assets/images/no_image.png',
            'upload_folder' : '<?php echo JPATH_ROOT . DS . 'images' . DS . 'Recipe_Images' . DS  ?>',
            'preview_height' : '180px',
            'preview_width' : '200px',
            'el' : $('#image_upload_content'),
            'img_path' : 'images/Recipe_Images',
            'image_name' : '<?php echo $this->item->id;?>'

        };

        var image_upload = $.backbone_image_upload(image_upload_options); 
     
        
        
        
        
        //VIDEO
        
        var videopath = '<?php echo $this->item->video;?>';
        var filename = '';
        if(typeof videopath !== 'undefined') {
            var fileNameIndex = videopath.lastIndexOf("/") + 1;
            filename = videopath.substr(fileNameIndex);
        }
                
                
                
        var video_upload_options = {
            'url' : '<?php echo JURI::root();?>index.php?option=com_fitness&view=recipe_database&task=uploadVideo&format=text',
            'base_url' : '<?php echo JURI::root();?>',
            'video' : filename,
            'default_video_image' : '<?php echo JURI::root();?>administrator/components/com_fitness/assets/images/no_video_image.png',
            'no_video_image_big' : '<?php echo JURI::root();?>administrator/components/com_fitness/assets/images/no_video_big.png',
            'upload_folder' : '<?php echo JPATH_ROOT . DS . 'images' . DS . 'Recipe_Videos' . DS  ?>',
            'video_path' : 'images/Recipe_Videos',
            'preview_height' : '180px',
            'preview_width' : '250px',
            'el' : $('#video_upload_content'),
            'video_name' : '<?php echo $this->item->id;?>',
            

        };

        var video_upload = $.backbone_video_upload(video_upload_options); 
        
        
        
        
        Joomla.submitbutton = function(task)
        {
            if (task == 'nutrition_recipe.cancel') {
                Joomla.submitform(task, document.getElementById('nutrition_recipe-form'));
            }
            else{

                if (task != 'nutrition_recipe.cancel' && document.formvalidator.isValid(document.id('nutrition_recipe-form'))) {

                    var video_path = $("#video_container").attr('data-videopath');
                    
                    $("#jform_video_input").val(video_path);
                    
                    var image_path = $(".preview_image").attr('data-imagepath');
                    
                    $("#jform_image_input").val(image_path);

                    Joomla.submitform(task, document.getElementById('nutrition_recipe-form'));
                }
                else {
                    alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED')); ?>');
                }
            }
        }
        
     })($js);
</script>

