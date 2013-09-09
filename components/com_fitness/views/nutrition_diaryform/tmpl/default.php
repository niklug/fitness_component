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

JHtml::_('behavior.keepalive');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');

//Load admin language file
$lang = JFactory::getLanguage();
$lang->load('com_fitness', JPATH_ADMINISTRATOR);

$user = &JFactory::getUser();

$nutrition_plan_id = $this->nutrition_plan_id;

$trainer_id = $item->trainer_id ? $item->trainer_id : $this->active_plan_data->trainer_id;

$goal_category_id = $item->goal_category_id ? $item->goal_category_id : $this->active_plan_data->primary_goal_id;

$training_period_id = $item->training_period_id ? $item->training_period_id : $this->active_plan_data->training_period_id;

$nutrition_focus = $item->nutrition_focus ? $item->nutrition_focus : $this->active_plan_data->nutrition_focus;

$submitted = false;
if ($this->item->submit_date && ($this->item->submit_date != '0000-00-00 00:00:00')) {
    $submitted = true;
}


$heavy_target = $this->model->getNutritionTarget($nutrition_plan_id, 'heavy');

$light_target = $this->model->getNutritionTarget($nutrition_plan_id, 'light');

$rest_target = $this->model->getNutritionTarget($nutrition_plan_id, 'rest');

?>
<div class="fitness_wrapper">
    <form id="form-nutrition_diary" action="<?php echo JRoute::_('index.php?option=com_fitness&task=nutrition_diary.save'); ?>" method="post" class="form-validate" enctype="multipart/form-data">
        <h2>NUTRITION DIARY</h2>
        <table width="100%">
            <tr style="display:none;">
                <td colspan="2">
                    <fieldset id="daily_micronutrient"></fieldset>
                </td>
            </tr>
            <tr>
                <td width="40%">
                    <div class="fitness_block_wrapper" style="min-height:200px;">
                        <h3>CLIENT & TRAINERS</h3>
                        <hr class="orange_line">
                        <div class="internal_wrapper">
                            <table width="100%">
                                <tr>
                                    <td>
                                        Client Name
                                    </td>
                                    <td>
                                        <span class="grey_title">
                                            <?php echo $user->name; ?>
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        Primary Trainer
                                    </td>
                                    <td>
                                        <span class="grey_title">
                                            <?php echo JFactory::getUser($trainer_id)->name ?>
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        Secondary Trainers
                                    </td>
                                    <td>
                                        <span class="grey_title">
                                            <?php
                                            foreach ($this->secondary_trainers as $trainer) {
                                                echo $trainer . "<br/>";
                                            };
                                            ?>
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </td>
                <td>
                    <div class="fitness_block_wrapper" style="min-height:200px;">
                        <h3>ENTRY DETAILS</h3>
                        <hr class="orange_line">
                        <div class="internal_wrapper">
                            <table width="100%">
                                <tr>
                                    <td>
                                        Date of Entry
                                    </td>
                                    <td>
                                        <span class="grey_title">
                                            <?php
                                            if ($submitted) {
                                                $jdate = new JDate($this->item->entry_date);
                                                echo $jdate->toFormat('%A %d %B %Y');
                                            } else {
                                                echo $this->form->getInput('entry_date'); 
                                            }
                                            
                                            ?>
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        Date Created
                                    </td>
                                    <td>
                                        <span class="grey_title">
                                            <?php echo $this->form->getInput('created'); ?>
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        Date Submitted
                                    </td>
                                    <td>
                                        <span class="grey_title">
                                            <?php
                                            if ($submitted) {
                                                $jdate = new JDate($this->item->submit_date);
                                                echo $jdate->format(JText::_('DATE_FORMAT_LC2'));
                                            }
                                            ?>
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        Assessed By
                                    </td>
                                    <td>
                                        <span class="grey_title">
                                            <?php
                                            if ($this->item->assessed_by) {
                                                echo JFactory::getUser($this->item->assessed_by)->name;
                                            }
                                            ?>
                                        </span>
                                    </td>
                                </tr>

                            </table>
                        </div>
                    </div>

                </td>
            </tr>

            <tr>
                <td colspan="2">
                    <div class="fitness_block_wrapper" style="min-height:150px;">
                        <h3>PRIMARY GOAL</h3>
                        <hr class="orange_line">
                        <div class="internal_wrapper">
                            <table width="100%">
                                <tr>
                                    <td>
                                        <table width="50%">
                                            <tr>
                                                <td>
                                                    Primary Goal
                                                </td>
                                                <td>
                                                    <span class="grey_title">
                                                        <?php echo $this->model->getGoalName($goal_category_id); ?>
                                                    </span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    Training Period
                                                </td>
                                                <td>
                                                    <span class="grey_title">
                                                        <?php echo $this->model->getTrainingPeriodName($training_period_id); ?>
                                                    </span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    Nutrition Focus
                                                </td>
                                                <td>
                                                    <span class="grey_title">
                                                        <?php echo $this->model->getNutritionFocusName($nutrition_focus); ?>
                                                    </span>
                                                </td>
                                            </tr>
                                        </table> 
                                    </td>
                                    <td>
                                        <table width="50%">
                                            <tr>
                                                <td>
                                                    <?php
                                                    if($this->item->trainer_comments) {
                                                        echo 'Trainer Comments';
                                                    }
                                                    ?>
                                                </td>
                                                <td>
                                                    <?php echo $this->item->trainer_comments;?>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>


                        </div>
                    </div>
                </td>

            </tr>
            <tr>
                <td colspan="2">
                    <div class="fitness_block_wrapper" style="height: 300px;">
                        <h3>MACRONUTRIENT TARGETS</h3>
                        <hr class="orange_line">
                        <div class="internal_wrapper">
                            <table width="100%">
                                <tr>
                                    <td>
                                    <?php
                                    //if($this->item->id) {
                                    ?>
                                    <?php echo $this->form->getLabel('activity_level'); ?>
                                    <?php echo $this->form->getInput('activity_level'); ?>
                                    <?php
                                    //}
                                    ?>
                                    </td>
                                    <td id="pie_td" style="visibility: hidden;" class="center">
                                        <div class="pie-container">
                                            <h5>MACRONUTRIENT RATIOS</h5>
                                            <div id="placeholder_targets" class="placeholder_pie"></div>
                                        </div>
                                    </td>
                                    <td id="calories_td"  style="visibility: hidden;" class="center">
                                        <table width="100%">
                                            <tr>
                                                <td>
                                                   <h5>TARGET CALORIE INTAKE</h5> 
                                                </td>
                                            </tr>
                                            <tr>
                                                <td id="calories_value" style="font-size: 48px; color:#00983A; font-weight: bold;">
                                                    
                                                </td>
                                            </tr>
                                            <tr>
                                                <td  style="font-size: 22px; color:#00983A;font-weight: bold; height: 40px;">
                                                    calories
                                                </td>
                                            </tr>
                                            
                                            <tr>
                                                <td>
                                                    <h5>TARGET WATER INTAKE</h5>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td id="water_value" style="font-size:48px; color:#3F9EEB; font-weight: bold;">
                                                    
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="font-size: 22px; color:#3F9EEB; font-weight: bold; height: 40px;">
                                                    millilitres
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table> 
                        </div>
                    </div>
                </td>
            </tr>
             <tr>
                <td colspan="2">
                    <div class="fitness_block_wrapper" style="min-height: 300px;">
                        <h3>MEALS, SNACKS & SUPPLEMENTS</h3>
                        <hr class="orange_line">
                        <div class="internal_wrapper">
                            <div class="clr"></div>
                            <?php
                            if($this->item->id) {
                            ?>
                                <div id="meals_wrapper"></div>
                                <div class="clr"></div>
                                <hr>
                                <?php
                                if (!$submitted) {
                                ?>
                                <input style="display:none;" type="button" id="add_plan_meal" value="NEW MEAL">
                                <?php
                                } 
                            } else {
                                echo 'Save entry to proceed add Meals ';
                            }
                            ?>
                            <div class="clr"></div>
                        </div>
                    </div>
                </td>
            </tr>
            <?php
            if($this->item->id) {
                if(!in_array($this->item->status, array('2', '3', '4'))) {
                    $score_hidden = 'visibility: hidden;';
                }
            ?>
            <tr>
                <td colspan="2">
                    <div class="fitness_block_wrapper" style="min-height: 320px;">
                        <div class="internal_wrapper">
                            <div  style="float:left; width:300px;<?php echo  $score_hidden;?>">
                                <div class="score_pie_container pie-container">
                                    <h5 style="text-align: center;">MACRONUTRIENT SCORES</h5>
                                    <div id="placeholder_scope" class="placeholder_pie"></div>
                                </div>
                                <table id="total_score" width="100%">
                                    <tr>
                                        <td>
                                            <h5 style="text-align: center;">FINAL SCORE</h5>
                                        </td>
                                        <td>
                                           <div id="final_score" ></div>
                                        </td>
                                        <td>
                                            <div id="status_button_place" >
                                                <?php echo $this->frontend_list_model->status_html($this->item->status) ?>
                                            </div>
                                        </td>
                                            
                                            
                                    </tr>
                                </table>
                            </div>
                            <div style="float:right; font-size: 10px;">
                                <table >
                                    <thead>
                                        <tr>
                                            <th></th>
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
                                    <tbody id="">
                                        <tr>
                                            <td width="150">
                                                <b>DAILY TOTALS (grams)</b>
                                            </td>
                                            <td>
                                                <input class="number_input" readonly size="5" type="text"  id="daily_protein_grams" name="daily_protein_grams" value="">
                                            </td>
                                            <td>
                                                <input class="number_input" readonly size="5" type="text"  id="daily_fats_grams" name="daily_fats_grams" value="">
                                            </td>
                                            <td>
                                                <input class="number_input" readonly size="5" type="text"  id="daily_carbs_grams" name="daily_carbs_grams" value="">
                                            </td>
                                            <td></td>
                                            <td></td>
                                            <td>
                                                <input class="number_input" readonly size="5" type="text"  id="daily_saturated_fat_grams" name="daily_saturated_fat_grams" value="">
                                            </td>
                                            <td>
                                                <input class="number_input" readonly size="5" type="text"  id="daily_total_sugars_grams" name="daily_total_sugars_grams" value="">
                                            </td>
                                            <td>
                                                <input class="number_input" readonly size="5" type="text"  id="daily_sodium_grams"  name="daily_sodium_grams" value="">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <b>DAILY TOTALS (%)</b>
                                            </td>
                                            <td>
                                                <input class="number_input" readonly size="5" type="text"  id="daily_protein_percents" name="daily_protein_percents" value="">
                                            </td>
                                            <td>
                                                <input class="number_input" readonly size="5" type="text"  id="daily_fats_percents" name="daily_fats_percents" value="">
                                            </td>
                                            <td>
                                                <input class="number_input" readonly size="5" type="text"  id="daily_carbs_percents" name="daily_carbs_percents" value="">
                                            </td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td><b>DAILY TOTALS</b></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td>
                                                <input class="number_input" readonly size="5" type="text"  id="daily_calories" name="daily_calories" value="">
                                            </td>
                                            <td>
                                                <input class="number_input" readonly size="5" type="text"  id="daily_energy" name="daily_energy" value="">
                                            </td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td><b>DAILY TOTAL WATER</b></td>
                                            <td><input class="number_input" readonly size="5" type="text"  id="daily_total_water" name="daily_total_water" value=""></td>
                                            <td>MILLILITRES</td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                        </tr>
                                    </tbody>
                                </table>

                                <br/><br/>

                                 <table>
                                    <thead>
                                        <tr>
                                            <th></th>
                                            <th>PRO (g)</th>
                                            <th>FAT (g)</th>
                                            <th>CARB (g)</th>
                                            <th>CALS</th>
                                            <th>ENRG (kJ)</th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody id="">
                                        <tr>
                                            <td width="150">
                                                <b>VARIANCE (grams)</b>
                                            </td>
                                            <td>
                                                <input class="number_input" readonly size="5" type="text"  id="variance_protein_grams" name="variance_protein_grams" value="">
                                            </td>
                                            <td>
                                                <input class="number_input" readonly size="5" type="text"  id="variance_fats_grams" name="variance_fats_grams" value="">
                                            </td>
                                            <td>
                                                <input class="number_input" readonly size="5" type="text"  id="variance_carbs_grams" name="variance_carbs_grams" value="">
                                            </td>
                                            <td></td>
                                            <td></td>
                                             <td></td>
                                            <td></td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <b>VARIANCE (%)</b>
                                            </td>
                                            <td>
                                                <input class="number_input" readonly size="5" type="text"  id="variance_protein_percents" name="variance_protein_percents" value="">
                                            </td>
                                            <td>
                                                <input class="number_input" readonly size="5" type="text"  id="variance_fats_percents" name="variance_fats_percents" value="">
                                            </td>
                                            <td>
                                                <input class="number_input" readonly size="5" type="text"  id="variance_carbs_percents" name="variance_carbs_percents" value="">
                                            </td>
                                            <td>
                                                <input class="number_input" readonly size="5" type="text"  id="variance_calories_percents" name="variance_calories_percents" value="">
                                            </td>
                                            <td>
                                                <input class="number_input" readonly size="5" type="text"  id="variance_energy_percents" name="variance_energy_percents" value="">
                                            </td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td><b>VARIANCE</b></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td>
                                                <input class="number_input" readonly size="5" type="text"  id="variance_calories" name="variance_calories" value="">
                                            </td>
                                            <td>
                                                <input class="number_input" readonly size="5" type="text"  id="variance_energy" name="variance_energy" value="">
                                            </td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td><b>VARIANCE (WATER)</b></td>
                                            <td><input class="number_input" readonly size="5" type="text"  id="variance_daily_total_water" name="variance_daily_total_water" value=""></td>
                                            <td>MILLILITRES</td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </td>
            </tr>
            <?php
            if(in_array($this->item->status, array('2', '3', '4'))) {
            ?>
                <tr>
                    <td colspan="2">
                            <div class="clr"></div>
                            <div id="plan_comments_wrapper"></div>
                            <div class="clr"></div>
                            <input id="add_comment_0" class="" type="button" value="Add Comment" >
                            <div class="clr"></div>
                    </td>
                </tr>
                <?php
                }
                ?>
            <?php
            }
            ?>
        </table>



        <input type="hidden" name="jform[id]" value="<?php echo $this->item->id; ?>" />
        <input type="hidden" name="jform[nutrition_plan_id]" value="<?php echo $nutrition_plan_id; ?>" />
        <input type="hidden" name="jform[client_id]" value="<?php echo $user->id; ?>" />
        <input type="hidden" name="jform[trainer_id]" value="<?php echo $trainer_id ?>" />
        <input type="hidden" name="jform[goal_category_id]" value="<?php echo $goal_category_id; ?>" />
        <input type="hidden" name="jform[training_period_id]" value="<?php echo $training_period_id; ?>" />
        <input type="hidden" name="jform[nutrition_focus]" value="<?php echo $nutrition_focus; ?>" />



        <input type="hidden" name="jform[state]" value="1" />


            <div>
                <?php
                    if (!$submitted) {
                ?>
                    <?php
                        if ($this->item->id) {
                    ?>
                    <input type="submit" id="submit_button" class="validate" name="submit" value="Submit" />
                    <?php
                        }
                    ?>

                    <input type="submit" class="validate" name="save" value="Save" />

                    <input type="submit" class="validate" name="save_close" value="Save&Close" />

                    <?php
                        if (!$submitted && $this->item->id) {
                    ?>
                            <input type="submit" class="validate" name="delete" value="Delete" />
                    <?php
                        }
                    ?>
                <?php
                    }
                ?>               
                <a href="<?php echo JRoute::_('index.php?option=com_fitness&task=nutrition_diary.cancel'); ?>" title="Close Entry without saving">Close</a>

                <input type="hidden" name="option" value="com_fitness" />
                <input type="hidden" name="task" value="nutrition_diaryform.save" />
                <?php echo JHtml::_('form.token'); ?>
            </div>
    </form>
    
    <?php

    //var_dump($this->active_plan_data->id);

    ?>

</div>

<script type="text/javascript">
    
    (function($) {
        
        var heavy_target = <?php echo $heavy_target;?>;
        var light_target = <?php echo $light_target;?>;
        var rest_target = <?php echo $rest_target;?>;
        
        var activity_level_element =  "input[name='jform[activity_level]']";
        
        var activity_level = $(activity_level_element +":checked").val();
        
        if (activity_level) {
           setTargetData(activity_level);
        }
        
        $(activity_level_element).on('click', function() {
            var activity_level = $(this).val();
            setTargetData(activity_level);
        })

        
        
        function setTargetData(activity_level) {
            var activity_data;
            if(activity_level == '1') activity_data = heavy_target;
            if(activity_level == '2') activity_data = light_target;
            if(activity_level == '3') activity_data = rest_target;


            var calories = activity_data.calories;
            var water = activity_data.water;
            
            $("#calories_value").html(calories);
            $("#water_value").html(water);
            
            $("#pie_td, #calories_td").css('visibility', 'visible');
            
            
            //console.log(activity_data);
            var data = [
                {label: "Protein:", data: [[1, activity_data.protein]]},
                {label: "Carbs:", data: [[1, activity_data.carbs]]},
                {label: "Fat:", data: [[1, activity_data.fats]]}
            ];

            var container = $("#placeholder_targets");

            var targets_pie = $.drawPie(data, container, {'no_percent_label' : false});

            targets_pie.draw(); 
        }
        
        
        /* MEALS BLOCK */
        var item_description_options = {
            'nutrition_plan_id' : '<?php echo $this->item->id;?>',
            'fitness_administration_url' : '<?php echo JURI::root();?>index.php?option=com_fitness&tmpl=component&<?php echo JSession::getFormToken(); ?>=1',
            'main_wrapper' : $("#diary_guide"),
            'ingredient_obj' : {id : "", meal_name : "", quantity : "", measurement : "", protein : "", fats : "", carbs : "", calories : "", energy : "", saturated_fat : "", total_sugars : "", sodium : ""},
            'db_table' : '#__fitness_nutrition_diary_ingredients',
            'parent_view' : 'nutrition_diary_frontend',
            'read_only' : '<?php echo $submitted ?>'

        }

        var nutrition_meal_options = {
            'main_wrapper' : $("#meals_wrapper"),
            'nutrition_plan_id' : '<?php echo $this->item->id;?>',
            'fitness_administration_url' : '<?php echo JURI::root();?>index.php?option=com_fitness&tmpl=component&<?php echo JSession::getFormToken(); ?>=1',
            'add_meal_button' : $("#add_plan_meal"),
            'activity_level' : "input[name='jform[activity_level]']",
            'meal_obj' : {id : "", 'nutrition_plan_id' : "", 'meal_time' : "", 'water' : "", 'previous_water' : ""},
            'db_table' : '#__fitness_nutrition_diary_meals',
            'read_only' : '<?php echo $submitted ?>'
        }


        var nutrition_comment_options = {
            'nutrition_plan_id' : '<?php echo $this->item->id;?>',
            'fitness_administration_url' : '<?php echo JURI::root();?>index.php?option=com_fitness&tmpl=component&<?php echo JSession::getFormToken(); ?>=1',
            'comment_obj' : {'user_name' : '<?php echo JFactory::getUser()->name;?>', 'created' : "", 'comment' : ""},
            'db_table' : '#__fitness_nutrition_diary_meal_comments'
        }
        
        
        var calculate_summary_options = {
            'activity_level' : "input[name='jform[activity_level]']",
            'chart_container' : $("#placeholder_scope"),
            'draw_chart' : true
        }
        
        var macronutrient_targets_options = {
            'main_wrapper' : $("#daily_micronutrient"),
            'fitness_administration_url' : '<?php echo JURI::root();?>index.php?option=com_fitness&tmpl=component&<?php echo JSession::getFormToken(); ?>=1',
            'protein_grams_coefficient' : 4,
            'fats_grams_coefficient' : 9,
            'carbs_grams_coefficient' : 4,
            'nutrition_plan_id' : '<?php echo $nutrition_plan_id;?>',
            'empty_html_data' : {'calories' : "", 'water' : "", 'protein' : "", 'fats' : "", 'carbs' : ""}
        }
        
        var nutrition_bottom_comment_options = {
            'nutrition_plan_id' : '<?php echo $this->item->id;?>',
            'fitness_administration_url' : '<?php echo JURI::root();?>index.php?option=com_fitness&tmpl=component&<?php echo JSession::getFormToken(); ?>=1',
            'comment_obj' : {'user_name' : '<?php echo JFactory::getUser()->name;?>', 'created' : "", 'comment' : ""},
            'db_table' : '#__fitness_nutrition_diary_comments'
        }
        
        var nutrition_meal = $.nutritionMeal(nutrition_meal_options, item_description_options, nutrition_comment_options);
        var calculateSummary =  $.calculateSummary(calculate_summary_options);
        
            // append targets fieldsets
        var macronutrient_targets_heavy = $.macronutrientTargets(macronutrient_targets_options, 'heavy', 'HEAVY TRAINING DAY');

        var macronutrient_targets_light = $.macronutrientTargets(macronutrient_targets_options, 'light', 'LIGHT TRAINING DAY');

        var macronutrient_targets_rest = $.macronutrientTargets(macronutrient_targets_options, 'rest', 'RECOVERY / REST DAY');
        //bottom comments
        var plan_comments = $.nutritionComment(nutrition_bottom_comment_options, nutrition_comment_options.nutrition_plan_id, 0);
        
        nutrition_meal.run();
        calculateSummary.run();
        
        macronutrient_targets_heavy.run();
        macronutrient_targets_light.run();
        macronutrient_targets_rest.run();
        
        var plan_comments_html = plan_comments.run();
        $("#plan_comments_wrapper").html(plan_comments_html);
        /* END MEALS BLOCK */
        
        // on save
    
        $('#form-nutrition_diary').submit(function(event) {
            var activity_level_element =  "input[name='jform[activity_level]']";
            var activity_level = $(activity_level_element +":checked").val();
            if(! activity_level) {
                alert('Choose your Activity Level first!');
                return false;
            }
        });
     
       
        
    })($js);

    
</script>