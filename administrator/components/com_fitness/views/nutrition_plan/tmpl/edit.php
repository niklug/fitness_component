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
    /* Temporary fix for drifting editor fields */
    .adminformlist li {
        clear: both;
    }
</style>

<form action="<?php echo JRoute::_('index.php?option=com_fitness&layout=edit&id=' . (int) $this->item->id); ?>" method="post" enctype="multipart/form-data" name="adminForm" id="nutrition_plan-form" class="form-validate">
    <div class="width-100 fltlft">
        <table width="100%">
            <tr>
                <td width="30%">
                    <fieldset style="height:410px;" class="adminform">
                        <legend>CLIENT & TRAINER(S)</legend>
                        <?php
                        $db = JFactory::getDbo();

                        $sql = "SELECT id AS value, username AS text FROM #__users INNER JOIN #__user_usergroup_map ON #__user_usergroup_map.user_id=#__users.id WHERE #__user_usergroup_map.group_id=(SELECT id FROM #__usergroups WHERE title='Trainers')";
                        $db->setQuery($sql);
                        if(!$db->query()) {
                            JError::raiseError($db->getErrorMsg());
                        }
                        $primary_trainerlist = $db->loadObjectList();
                        ?>
                        <ul>
                            <li><?php echo $this->form->getLabel('trainer_id'); ?>
                                <div class='filter-select fltrt'>
                                    <select id="jform_trainer_id" class="inputbox" name="jform[trainer_id]">
                                        <option value=""><?php echo JText::_('-Select-');?></option>
                                        <?php echo JHtml::_('select.options', $primary_trainerlist, "value", "text", $this->item->trainer_id, true);?>
                                    </select>
                                </div>
                            </li>
                            <li><?php echo $this->form->getLabel('client_id'); ?>
                                <div class='filter-select fltrt'>
                                    <select id="jform_client_id" class="inputbox" name="jform[client_id]">
                                        <?php
                                        if($this->item->client_id) {
                                            echo '<option value="' . $this->item->client_id . '">'. JFactory::getUser($this->item->client_id)->name .'</option>';
                                        } else {
                                            echo '<option value="">' . JText::_('-Select-')  . '</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                            </li>
                            <li>
                                <label id="jform_trainers_id-lbl" class="" for="jform_trainers_id">Secondary Trainers</label>
                                <div class='filter-select fltrt'>
                                    <div id="secondary_trainers"></div>
                                </div>
                            </li>
                        </ul>
                    </fieldset>
                </td>
                <td width="70%">
                    <fieldset  class="adminform">
                        <legend>NUTRITION PLAN (PERIODIZATION)</legend>
                        <table>
                            <tr>
                                <td>
                                    <?php echo $this->form->getLabel('primary_goal'); ?>
                                    <select id="jform_primary_goal" class="inputbox" name="jform[primary_goal]">
                                        <?php
                                        if($this->item->primary_goal) {
                                            echo '<option value="' . $this->item->primary_goal. '">'. $this->getPrimaryGoalName($this->item->primary_goal) .'</option>';
                                        } else {
                                            echo '<option value="">' . JText::_('-Select-')  . '</option>';
                                        }
                                        ?>
                                    </select>
                                </td>
                                <td></td>
                                <td></td>
                                <td>
                                    <?php echo $this->form->getLabel('state'); ?>
                                    <?php echo $this->form->getInput('state'); ?>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="3">
                                    <?php echo $this->form->getLabel('training_period'); ?>
                                    <?php echo $this->form->getInput('training_period'); ?>
                                </td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>
                                    <?php echo $this->form->getLabel('active_start'); ?>
                                    <?php echo $this->form->getInput('active_start'); ?>
                                </td>
                                <td>
                                    <?php echo $this->form->getLabel('force_active'); ?>
                                </td>
                                <td>
                                    <?php echo $this->form->getInput('force_active'); ?>
                                </td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>
                                    <?php echo $this->form->getLabel('active_finish'); ?>
                                    <?php echo $this->form->getInput('active_finish'); ?>
                                </td>
                                <td>
                                    <?php echo $this->form->getLabel('no_end_date'); ?>
                                </td>
                                <td>
                                    <?php echo $this->form->getInput('no_end_date'); ?>
                                </td>
                                <td></td>
                            </tr>
                            <tr>
                                <td colspan="3">
                                    <?php echo $this->form->getLabel('nutrition_focus'); ?>
                                    <?php echo $this->form->getInput('nutrition_focus'); ?>
                                </td>
                                <td></td>
                            </tr>
                            
                            <td colspan="4">
                                    <?php echo $this->form->getLabel('trainer_comments'); ?>
                                    <?php echo $this->form->getInput('trainer_comments'); ?>
                                </td>
                            </tr>
                        </table>
                    </fieldset>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <fieldset id="daily_micronutrient"  class="adminform">
                        <?php
                        if(!$this->item->id) {
                            echo 'Save form to proceed add Targets';
                        }
                        ?>
                        <legend>DAILY MACRONUTRIENT & CALORIE TARGETS</legend>
                    </fieldset>
                </td>
            </tr>
            
            <tr>
                <td colspan="2">
                    <fieldset id="diary_guide"  class="adminform">
                        <?php
                        if(!$this->item->id) {
                            echo 'Save form to proceed add Meals';
                        }
                        ?>
                        <legend>NUTRITION DIARY GUIDE</legend>
                    </fieldset>
                </td>
            </tr>
        </table>
    </div>
        
    <div style="display:none;">
    <?php echo $this->form->getLabel('created'); ?>
    <?php echo $this->form->getInput('created'); ?>
    </div>
    <input type="hidden" name="task" value="" />
    <?php echo JHtml::_('form.token'); ?>
    <div class="clr"></div>
</form>


<script type="text/javascript">
    
    // set options
    var nutrition_plan_options = {
        'trainer_select' : $("#jform_trainer_id"),
        'client_select' : $("#jform_client_id"),
        'secondary_trainers_wrapper' : $("#secondary_trainers"),
        'primary_goal_select' : $("#jform_primary_goal"),
        'training_period_select' : $("#jform_training_period"),
        'calendar_frontend_url' : '<?php echo JURI::root();?>index.php?option=com_multicalendar&task=load&calid=0',
        'fitness_administration_url' : '<?php echo JURI::root();?>administrator/index.php?option=com_fitness&tmpl=component&<?php echo JSession::getFormToken(); ?>=1',
        'client_selected' : '<?php echo $this->item->client_id;?>',
        'primary_goal_selected' : '<?php echo $this->item->primary_goal;?>',
        'active_start_field' : $("#jform_active_start"),
        'active_finish_field' : $("#jform_active_finish"),
        'active_start_img' : $("#jform_active_start_img"),
        'active_finish_img' : $("#jform_active_finish_img"),
        'force_active_yes' : $("#jform_force_active0"),
        'force_active_no' : $("#jform_force_active1"),
        'force_active_value' : '<?php echo $this->item->force_active;?>',
        'active_finish_value' : '<?php echo $this->item->active_finish;?>',
        'no_end_date_label': $("#jform_no_end_date-lbl"),
        'no_end_fieldset' : $("#jform_no_end_date"),
        'no_end_date_yes' : $("#jform_no_end_date0"),
        'no_end_date_no' : $("#jform_no_end_date1"),
        'max_possible_date' : '9999-12-31'
    }
    
    var macronutrient_targets_options = {
        'main_wrapper' : $("#daily_micronutrient"),
        'fitness_administration_url' : '<?php echo JURI::root();?>administrator/index.php?option=com_fitness&tmpl=component&<?php echo JSession::getFormToken(); ?>=1',
        'protein_grams_coefficient' : 4,
        'fats_grams_coefficient' : 9,
        'carbs_grams_coefficient' : 4,
        'nutrition_plan_id' : '<?php echo $this->item->id;?>',
        'empty_html_data' : {'calories' : "", 'water' : "", 'protein' : "", 'fats' : "", 'carbs' : ""}
    }
    
    var item_description_options = {
        'nutrition_plan_id' : '<?php echo $this->item->id;?>',
        'fitness_administration_url' : '<?php echo JURI::root();?>administrator/index.php?option=com_fitness&tmpl=component&<?php echo JSession::getFormToken(); ?>=1',
        'main_wrapper' : $("#diary_guide"),
        'ingredient_obj' : {id : "", meal_name : "", quantity : "", measurement : "", protein : "", fats : "", carbs : "", calories : "", energy : "", saturated_fat : "", total_sugars : "", sodium : ""},
        
    }
    
    // cteate main object
    var nutrition_plan = new NutritionPlan(nutrition_plan_options);
    
    // append targets fieldsets
    var macronutrient_targets_heavy = new MacronutrientTargets(macronutrient_targets_options, 'heavy', 'HEAVY TRAINING DAY');
    
    var macronutrient_targets_light = new MacronutrientTargets(macronutrient_targets_options, 'light', 'LIGHT TRAINING DAY');
    
    var macronutrient_targets_rest = new MacronutrientTargets(macronutrient_targets_options, 'rest', 'RECOVERY / REST DAY');
    
    // item descriptions
    var item_description_meal = new ItemDescription(item_description_options, 'meal', 'MEAL ITEM DESCRIPTION', 1);
    var item_description_supplement = new ItemDescription(item_description_options, 'supplement', 'SUPPLEMENT ITEM DESCRIPTION', 2);
    var item_description_drinks = new ItemDescription(item_description_options, 'drinks', 'DRINKS & LIQUIDS ITEM DESCRIPTION', 3);
    
    //
    //
    // attach listeners on document ready
    $(document).ready(function(){
        nutrition_plan.run();

        macronutrient_targets_heavy.run();
        macronutrient_targets_light.run();
        macronutrient_targets_rest.run();
        
        item_description_meal.run()
        item_description_supplement.run();
        item_description_drinks.run();
    });
    
    
    
    function ItemDescription(options, type, title, meal_id) {
        this.options = options;
        this._type = type;
        this._title = title;
        this._meal_id = meal_id;
        
        this._description_id = '_' + this._meal_id;
        
        this._doneTypingInterval = 1000;
    }
    
    ItemDescription.prototype.run = function() {
        var item_description_html = this.generateHtml();
        this.options.main_wrapper.append(item_description_html);
        this.setEventListeners();
    }
    
    ItemDescription.prototype.setEventListeners = function() {
        var self = this;
        $("#add_item"+ self._description_id).live('click', function() {
            var tr_html = self.createIngredientTR(self.options.ingredient_obj);
            $("#meals_content" + self._description_id).append(tr_html);
            $("#meals_content" + self._description_id).find("tr:last td:first input").focus();

        });

        $(".meal_name_input").live('input', function() {
            self.populateSearchResults($(this));
        });
        
        $(".ingredients_results option").live('click', function() {
            var closest_TR = $(this).closest("tr");
            self.setupTrDataId($(this));
            self.setIngredientData($(this));
            self.close_popup($("#select_meal_form" + self._description_id));
            var selected_ingredient_name = $(this).text();
            closest_TR.find(".meal_name_input").val(selected_ingredient_name);
            closest_TR.find(".meal_quantity_input").focus();
        });
        
        $("#meal_quantity_input"+ this._description_id).live('focusout', function(e){
            self.onQuantityInput($(this));
        });
        
        $(".delete_meal").live('click', function() {
            var closest_TR = $(this).closest("tr");
            var id = closest_TR.attr('data-id');
            self.deleteIngredient(id, function(id) {
                closest_TR.remove();;
            })
        });
        
        this.populateItemDescription(function(output) {
            if(!output) return;
            var html = '';
            output.each(function(ingredient){
                html += self.createIngredientTR(ingredient);
            });
            $("#meals_content" + self._description_id).html(html);
        });
    }
    
    
    ItemDescription.prototype.generateHtml = function() {
        var html = '<table width="100%">';
        html += '<thead>';
        html += '<tr>';
        html += '<th width="450">' + this._title + '</th>';
        html += '<th>QUANTITY</th>';
        html += '<th>PRO (g)</th>';
        html += '<th>FAT (g)</th>';
        html += '<th>CARB (g)</th>';
        html += '<th>CALS</th>';
        html += '<th>ENRG (kJ)</th>';
        html += '<th>FAT, SAT (g)</th>';
        html += '<th>SUG (g)</th>';
        html += '<th>SOD (mg)</th>';
        html += '<th></th>';
        html += '</tr>';
        html += '</thead>';
        html += '<tbody id="meals_content' + this._description_id + '">';
        html += '</tbody>';
        html += '<tfoot>';
        html += '<tr id="totals_row' + this._description_id + '">';
        html += '<td><input class="add_meal" type="button" id="add_item' + this._description_id + '" value="Add New Item"></td>';
        html += '</tr>';
        html += '</tfoot>';
        html += '</table>';
        
        return html;
    }
    
    ItemDescription.prototype.createIngredientTR = function(calculatedIngredient) {

        var html = '<tr data-ingredient_id="' + calculatedIngredient.ingredient_id + '"  data-id="' + calculatedIngredient.id + '">'
        
        html += '<td>';
        html += '<input  size="60" type="text"  class="meal_name_input" value="' + calculatedIngredient.meal_name + '">';
        html += '</td>';
        
        html += '<td>';
        html += '<input size="5" type="text"  class="meal_quantity_input" id="meal_quantity_input' + this._description_id + '" value="' + calculatedIngredient.quantity + '">';
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
    
    ItemDescription.prototype.searchResultsTemplate = function(calculatedIngredient) {
        var html = '<div class="select_meal_form" id="select_meal_form' + this._description_id + '">';
        html += '<span id="results_count' + this._description_id + '"></span>';
        html += '<select size="25" class="ingredients_results" id="ingredients_results' + this._description_id + '"></select>';
        html += '</div>';
        return html;
    }
    
    ItemDescription.prototype.getSearchIngredients = function(search_text, handleData) {
        var url = this.options.fitness_administration_url;
        $.ajax({
            type : "POST",
            url : url,
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
                handleData(response);
            },
            error: function(XMLHttpRequest, textStatus, errorThrown)
            {
                alert("error");
            }
        });
    }
    
    ItemDescription.prototype.populateSearchResults = function(o) {
        var typingTimer;
        var search_text = o.val();
        if($('#select_meal_form' + this._description_id).length == 0) {
            o.parent().append(this.searchResultsTemplate());
        }
        clearTimeout(typingTimer);
        var self = this;
        if (search_text) {
            typingTimer = setTimeout(
                function() {
                    self.getSearchIngredients(
                        search_text,
                        function(output) {
                            //console.log(output);
                            $("#results_count"+ self._description_id).html('Search returned ' + output.count + ' ingredients.');
                            $("#ingredients_results" + self._description_id).html(output.html);
                            $("#ingredients_results" + self._description_id).find(":odd").css("background-color", "#F0F0EE")
                        })
                    },
                self.options.doneTypingInterval
            );
        }
    }
    
    ItemDescription.prototype.getIngredientData = function(id, handleData) {
        var url = this.options.fitness_administration_url;
        $.ajax({
            type : "POST",
            url : url,
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
                handleData(response.ingredient);
            },
            error: function(XMLHttpRequest, textStatus, errorThrown)
            {
                alert("error");
            }
        }); 
    }
    
    ItemDescription.prototype.setIngredientData = function(o) {
        var ingredient_id = o.val();
        var selected_ingredient_name = o.text();
        var closest_TR = o.closest("tr");
        var self = this;
        this.getIngredientData(
            ingredient_id, 
            function(ingredient) {
                if(!ingredient) return;
                var measurement = self.getMeasurement(ingredient.specific_gravity);
                closest_TR.find(".grams_mil").html(measurement);
                //console.log(ingredient);
            }
        );
    }
    
    ItemDescription.prototype.getMeasurement = function(specific_gravity) {
        if(parseFloat(specific_gravity) > 0) {
            return 'millilitres';
        } 
        return 'grams';
    }
    
    
    ItemDescription.prototype.close_popup = function(element) {
        element.remove();
    }
    
    ItemDescription.prototype.setupTrDataId = function(o) {
        o.closest("tr").attr('data-ingredient_id', o.val());
    }
    
    ItemDescription.prototype.onQuantityInput = function(o) {
        var quantity = o.val();
        var closest_TR = o.closest("tr");
        var self = this;
        var ingredient_id = closest_TR.attr('data-ingredient_id');
        this.getIngredientData(
            ingredient_id, 
            function(ingredient) {
                if(!ingredient) return;
                if(quantity) {
                    var calculatedIngredient = self.calculatedIngredientItems(ingredient, quantity);
                    
                    calculatedIngredient.nutrition_plan_id = self.options.nutrition_plan_id;
                    calculatedIngredient.meal_id = self._meal_id;
                    var id = closest_TR.attr('data-id');
                    calculatedIngredient.id = id; 
                    calculatedIngredient.type = self._type;
                    self.saveIngredient(calculatedIngredient, function(inserted_id) {
                         if(inserted_id) {
                            calculatedIngredient.id = inserted_id;
                            var TR_html = self.createIngredientTR(calculatedIngredient);
                            closest_TR.replaceWith(TR_html);
                         }
                    });
                }
                
            }
        );
    }
    
    
    ItemDescription.prototype.calculatedIngredientItems = function(ingredient, quantity) {
        var calculated_ingredient = {};
        var specific_gravity = ingredient.specific_gravity;
        //quantity = 100;
        //specific_gravity = 1.03;
        //ingredient.protein = 3.2;
        calculated_ingredient.ingredient_id = ingredient.id;
        
        calculated_ingredient.meal_name = ingredient.ingredient_name;
        
        calculated_ingredient.quantity = quantity;
        
        calculated_ingredient.measurement = this.getMeasurement(ingredient.specific_gravity);
        
        calculated_ingredient.protein = this.calculateDependsOnGravity(ingredient.protein, quantity, specific_gravity);
        
        calculated_ingredient.fats = this.calculateDependsOnGravity(ingredient.fats, quantity, specific_gravity);
        
        calculated_ingredient.carbs = this.calculateDependsOnGravity(ingredient.carbs, quantity, specific_gravity);
        
        calculated_ingredient.calories = this.calculateDependsOnGravity(ingredient.calories, quantity, specific_gravity);
        
        calculated_ingredient.energy = this.calculateDependsOnGravity(ingredient.energy, quantity, specific_gravity);
        
        calculated_ingredient.saturated_fat = this.calculateDependsOnGravity(ingredient.saturated_fat, quantity, specific_gravity);
        
        calculated_ingredient.total_sugars = this.calculateDependsOnGravity(ingredient.total_sugars, quantity, specific_gravity);
        
        calculated_ingredient.sodium = this.calculateDependsOnGravity(ingredient.sodium, quantity, specific_gravity);
        
        //console.log(ingredient.specific_gravity);
        //console.log(ingredient);
        //console.log(calculated_ingredient);
                
        return calculated_ingredient;
    }
   

    ItemDescription.prototype.calculateDependsOnGravity =  function(value, quantity, specific_gravity) {
        var calculated_value;
        if(parseFloat(specific_gravity) > 0) {
            calculated_value = this.millilitresFormula(value, quantity, specific_gravity);
        } else {
            calculated_value = this.gramsFormula(value, quantity);
        }
        return calculated_value;
    }
    
    ItemDescription.prototype.gramsFormula = function(value, quantity) {
        return this.round_2_sign (value / 100 * quantity );
    }
    
    ItemDescription.prototype.millilitresFormula = function(value, quantity, specific_gravity) {
        return this.round_2_sign (value / 100 * quantity * specific_gravity );
    }
    
    ItemDescription.prototype.round_2_sign = function(value) {
        return Math.round(value * 100)/100;
    }
    
    
    ItemDescription.prototype.saveIngredient = function(calculatedIngredient, handleData) {
        var ingredient_encoded = JSON.stringify(calculatedIngredient);
        var url = this.options.fitness_administration_url;
        $.ajax({
            type : "POST",
            url : url,
            data : {
                view : 'nutrition_plan',
                format : 'text',
                task : 'saveIngredient',
                ingredient_encoded : ingredient_encoded
            },
            dataType : 'json',
            success : function(response) {
                if(!response.status.IsSuccess) {
                    alert(response.status.Msg);
                    return;
                }
                handleData(response.inserted_id);
              },
            error: function(XMLHttpRequest, textStatus, errorThrown)
            {
                alert("error saveIngredient");
            }
        }); 
     }
    
    
     ItemDescription.prototype.deleteIngredient = function(id, handleData) {
        var url = this.options.fitness_administration_url;
        $.ajax({
            type : "POST",
            url : url,
            data : {
                view : 'nutrition_plan',
                format : 'text',
                task : 'deleteIngredient',
                id : id
              },
            dataType : 'json',
            success : function(response) {
                if(!response.status.IsSuccess) {
                    alert(response.status.Msg);
                    return;
                }
                handleData(response.id);
                },
            error: function(XMLHttpRequest, textStatus, errorThrown)
            {
                alert("error deleteIngredient");
            }
        }); 
    }
    
    
    ItemDescription.prototype.populateItemDescription =  function(handleData) {
        var url = this.options.fitness_administration_url;
        var nutrition_plan_id = this.options.nutrition_plan_id;
        var meal_id = this._meal_id;
        var type = this._type;
        if(!nutrition_plan_id) return;
        $.ajax({
            type : "POST",
            url : url,
            data : {
                view : 'nutrition_plan',
                format : 'text',
                task : 'populateItemDescription',
                nutrition_plan_id : nutrition_plan_id,
                meal_id : meal_id,
                type : type
              },
            dataType : 'json',
            success : function(response) {
                if(!response.status.IsSuccess) {
                    alert(response.status.Msg);
                    return;
                }
                handleData(response.data);
                },
            error: function(XMLHttpRequest, textStatus, errorThrown)
            {
                alert("error");
            }
        }); 
    }
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    Joomla.submitbutton = function(task)  {
        if (task == 'nutrition_plan.cancel') {
            Joomla.submitform(task, document.getElementById('nutrition_plan-form'));
        }
        else{

            if (task != 'nutrition_plan.cancel' && document.formvalidator.isValid(document.id('nutrition_plan-form'))) {
                
                if(macronutrient_targets_options.nutrition_plan_id) {
                    // Targets
                    var heavy_validation = macronutrient_targets_heavy.validateSum100();
                    if(heavy_validation == false) {
                        alert('<?php echo $this->escape('Protein, Fats and Carbs MUST equal (=) 100%'); ?>');
                        return;
                    }

                    var light_validation = macronutrient_targets_light.validateSum100();
                    if(light_validation == false) {
                        alert('<?php echo $this->escape('Protein, Fats and Carbs MUST equal (=) 100%'); ?>');
                        return;
                    }

                    var rest_validation = macronutrient_targets_rest.validateSum100();
                    if(rest_validation == false) {
                        alert('<?php echo $this->escape('Protein, Fats and Carbs MUST equal (=) 100%'); ?>');
                        return;
                    }
                }

                //save targets data
                if(macronutrient_targets_options.nutrition_plan_id) {     
                    macronutrient_targets_heavy.saveTargetsData(function(output) {
                        macronutrient_targets_light.saveTargetsData(function(output) {
                            macronutrient_targets_rest.saveTargetsData(function(output) {
                                //reset force active fields in database by ajax
                                var force_active = nutrition_plan.options.force_active_yes.is(":checked");
                                if(force_active) {
                                    nutrition_plan.resetAllForceActive(function() {
                                        Joomla.submitform(task, document.getElementById('nutrition_plan-form'));
                                    });
                                } else {
                                    Joomla.submitform(task, document.getElementById('nutrition_plan-form'));
                                }
                            });
                        });

                      });
                } else {
                    //reset force active fields in database by ajax
                    var force_active = nutrition_plan.options.force_active_yes.is(":checked");
                    if(force_active) {
                        nutrition_plan.resetAllForceActive(function() {
                            Joomla.submitform(task, document.getElementById('nutrition_plan-form'));
                        });
                    } else {
                        Joomla.submitform(task, document.getElementById('nutrition_plan-form'));
                    }
                }
            }
            else {
                alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED')); ?>');
            }
        }
    }

    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
</script>