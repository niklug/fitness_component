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
                    <fieldset  class="adminform">
                        <?php
                        if(!$this->item->id) {
                            echo 'Save form to proceed add Shopping Items';
                        }
                        ?>
                        <legend>SUPPLEMENT SHOPPING LIST</legend>
                        <div class="clr"></div>
                        <div id="shopping_list_wrapper"></div>
                        <div class="clr"></div>
                        <input type="button" id="add_shopping_item" value="ADD NEW ITEM">
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
                        <?php
                        if($this->item->id) {
                        ?>
                        <?php echo $this->form->getLabel('activity_level'); ?>
                        <?php echo $this->form->getInput('activity_level'); ?>
                        <?php
                        }
                        ?>
                        <div class="clr"></div>
                        <div id="meals_wrapper"></div>
                        <div class="clr"></div>
                        <hr>
                        <input style="display:none;" type="button" id="add_plan_meal" value="NEW MEAL">
                        
                        <div class="clr"></div>
                        <br/>
                        <hr>
                        
                        <div id="plan_comments_wrapper"></div>
                        <div class="clr"></div>
                        <input id="add_comment_0" class="" type="button" value="Add Comment" >
                        <div class="clr"></div>
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
    
    var nutrition_meal_options = {
        'main_wrapper' : $("#meals_wrapper"),
        'nutrition_plan_id' : '<?php echo $this->item->id;?>',
        'fitness_administration_url' : '<?php echo JURI::root();?>administrator/index.php?option=com_fitness&tmpl=component&<?php echo JSession::getFormToken(); ?>=1',
        'add_meal_button' : $("#add_plan_meal"),
        'activity_level' : "input[name='jform[activity_level]']",
        'meal_obj' : {id : "", 'nutrition_plan_id' : "", 'meal_time' : "", 'water' : "", 'previous_water' : ""}
    }
    
    
    var nutrition_comment_options = {
        'nutrition_plan_id' : '<?php echo $this->item->id;?>',
        'fitness_administration_url' : '<?php echo JURI::root();?>administrator/index.php?option=com_fitness&tmpl=component&<?php echo JSession::getFormToken(); ?>=1',
        'comment_obj' : {'user_name' : '<?php echo JFactory::getUser()->name;?>', 'created' : "", 'comment' : ""},
        'db_table' : '#__fitness_nutrition_plan_meal_comments'
    }
    
    var nutrition_bottom_comment_options = {
        'nutrition_plan_id' : '<?php echo $this->item->id;?>',
        'fitness_administration_url' : '<?php echo JURI::root();?>administrator/index.php?option=com_fitness&tmpl=component&<?php echo JSession::getFormToken(); ?>=1',
        'comment_obj' : {'user_name' : '<?php echo JFactory::getUser()->name;?>', 'created' : "", 'comment' : ""},
        'db_table' : '#__fitness_nutrition_plan_comments'
    }
    
    
        
    var shopping_list_options = {
        'nutrition_plan_id' : '<?php echo $this->item->id;?>',
        'fitness_administration_url' : '<?php echo JURI::root();?>administrator/index.php?option=com_fitness&tmpl=component&<?php echo JSession::getFormToken(); ?>=1',
        'item_obj' : {'name' : "", 'usage' : "", 'comments' : "", 'url' : ""}
    }
    
    // cteate main object
    var nutrition_plan = new NutritionPlan(nutrition_plan_options);
    
    // append targets fieldsets
    var macronutrient_targets_heavy = new MacronutrientTargets(macronutrient_targets_options, 'heavy', 'HEAVY TRAINING DAY');
    
    var macronutrient_targets_light = new MacronutrientTargets(macronutrient_targets_options, 'light', 'LIGHT TRAINING DAY');
    
    var macronutrient_targets_rest = new MacronutrientTargets(macronutrient_targets_options, 'rest', 'RECOVERY / REST DAY');
    

    // meal blocks object
    var nutrition_meal = new NutritionMeal(nutrition_meal_options);
    
    // shopping list
    var shopping_list = new ShoppingList(shopping_list_options);
    
    //bottom comments
    var plan_comments = new NutritionComment(nutrition_bottom_comment_options, nutrition_comment_options.nutrition_plan_id, 0);
    
    // attach listeners on document ready
    $(document).ready(function(){
        nutrition_plan.run();

        macronutrient_targets_heavy.run();
        macronutrient_targets_light.run();
        macronutrient_targets_rest.run();

        nutrition_meal.run();
        
        shopping_list.run();
        
        var plan_comments_html = plan_comments.run();
        $("#plan_comments_wrapper").html(plan_comments_html);
        
    });
    
    
    
    function ShoppingList(options) {
        this.options = options;
    }
    
    
    ShoppingList.prototype.run = function() {
        var item_html = this.generateHtml();
        $("#shopping_list_wrapper").append(item_html);
        
        this.populate();
        
        this.setEventListeners();
    }
    
    ShoppingList.prototype.populate = function() {
        var self = this;
        this.getShoppingItemData(function(output) {
            if(!output) return;
            var html = '';
            output.each(function(item){
                html += self.generateItemTR(item);
            });
            $("#shopping_list_content").html(html);
        });
    }


    ShoppingList.prototype.setEventListeners = function() {
        var self = this;
        
        $("#add_shopping_item").on('click', function() {
            var item_html = self.generateItemTR(self.options.item_obj);
            $("#shopping_list_content").append(item_html);
        });
        
        $(".save_shopping_item").live('click', function() {
            var closest_tr = $(this).closest("tr");
            var data = {};
            
            data.id = closest_tr.attr('data-id');
            data.nutrition_plan_id = self.options.nutrition_plan_id;
            data.name = closest_tr.find(".shopping_name").val();
            data.usage = closest_tr.find(".shopping_usage").val();
            data.comments = closest_tr.find(".shopping_comments").val();
            data.url = closest_tr.find(".shopping_url").val();
            
            self.saveShoppingItem(data, function(output) {
                var html = self.generateItemTR(output);
                closest_tr.replaceWith(html);
            });
        });
        
        
        $(".delete_shopping_item").live('click', function() {
            var closest_tr = $(this).closest("tr");
            var id = closest_tr.attr('data-id');
            self.deleteShoppingItem(id, function(id) {
            closest_tr.remove();;
            });
        });
    }
    
    ShoppingList.prototype.generateHtml = function() {
        var html = '';
        html += '<table width="100%">';
        html += '<thead>';
        html += '<tr>';
        html += '<th>';
        html += 'PRODUCT NAME';
        html += '</th>';
        html += '<th>';
        html += 'RECOMMENDED USAGE';
        html += '</th>';
        html += '<th>';
        html += 'TRAINER COMMENTS';
        html += '</th>';
        html += '<th>';
        html += 'SHOP URL';
        html += '</th>';
        html += '</tr>';
        html += '</thead>';
        
        html += '<tbody id="shopping_list_content">';
       
        html += '<tbody>';
        html += '</table>';
        
        return html;
    }
    
    ShoppingList.prototype.generateItemTR = function(o) {
        var html = '';
        html += '<tr data-id="' + o.id + '" >';
        html += '<td>';
        html += '<input  size="60" type="text"  class=" shopping_name " value="' + o.name + '"> ';
        html += '</td>';
        
        html += '<td>';
        html += '<input  size="50" type="text"  class=" shopping_usage" value="' + o.usage + '"> ';
        html += '</td>';
        
        html += '<td>';
        html += '<input  size="50" type="text"  class=" shopping_comments" value="' + o.comments + '"> ';
        html += '</td>';
        
        html += '<td>';
        html += '<input  size="30" type="text"  class=" shopping_url" value="' + o.url + '"> ';
        html += '</td>';
        
        html += '<td>';
        html += '<input title="Save/Update Shopping Item" class="save_shopping_item " type="button"  value="Save">';
        html += '</td>';
        
        html += '<td>';
        html += '<a href="javascript:void(0)" class="delete_shopping_item" title="Delete Shopping Item"></a>';
        html += '</td>';
        html += '</tr>';
        
        return html;
    }
    
    
    ShoppingList.prototype.saveShoppingItem = function(o, handleData) {
        if(o.id === 'undefined')  o.id = "";
        var data_encoded = JSON.stringify(o);

        var url = this.options.fitness_administration_url;
        $.ajax({
            type : "POST",
            url : url,
            data : {
                view : 'nutrition_plan',
                format : 'text',
                task : 'saveShoppingItem',
                data_encoded : data_encoded
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
                alert("error saveShoppingItem");
            }
        }); 
    }
    
     ShoppingList.prototype.deleteShoppingItem = function(id, handleData) {
        var url = this.options.fitness_administration_url;
        $.ajax({
            type : "POST",
            url : url,
            data : {
                view : 'nutrition_plan',
                format : 'text',
                task : 'deleteShoppingItem',
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
                alert("error deleteShoppingItem");
            }
        }); 
    }



    ShoppingList.prototype.getShoppingItemData=  function(handleData) {
        var url = this.options.fitness_administration_url;
        var nutrition_plan_id = this.options.nutrition_plan_id;
        if(!nutrition_plan_id) return;
        $.ajax({
            type : "POST",
            url : url,
            data : {
                view : 'nutrition_plan',
                format : 'text',
                task : 'getShoppingItemData',
                nutrition_plan_id : nutrition_plan_id,
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
                alert("error getShoppingItemData");
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