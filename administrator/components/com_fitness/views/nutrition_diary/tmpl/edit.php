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

$nutrition_plan = $helper->getNutritionPlan($this->item->nutrition_plan_id);

$primary_goal = $helper->getGoalData($nutrition_plan->primary_goal_id, 1);

$mini_goal = $helper->getGoalData($nutrition_plan->mini_goal, 2);

?>

<form action="<?php echo JRoute::_('index.php?option=com_fitness&layout=edit&id=' . (int) $this->item->id); ?>" method="post" enctype="multipart/form-data" name="adminForm" id="nutrition_diary-form" class="form-validate">
    <div class="width-100 fltlft">
        <table width="100%">
            <tr>
                <td width="50%">
                    <fieldset style="min-height: 130px;" class="adminform">
                        <legend>CLIENT & TRAINERS </legend>
                        <table>
                            <tr>
                                <td>
                                    <?php echo $this->form->getLabel('client_id'); ?>
                                </td>
                                <td>
                                    <?php echo JFactory::getUser($this->item->client_id)->name; ?>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <?php echo $this->form->getLabel('trainer_id'); ?>
                                </td>
                                <td>
                                    <?php echo JFactory::getUser($this->item->trainer_id)->name; ?>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    Secondary Trainers
                                </td>
                                <td>
                                    <span class="grey_title">
                                        <?php
                                        $secondary_trainers = $this->model->get_client_trainers($this->item->client_id);
                                        
                                        foreach ($secondary_trainers as $trainer) {
                                            echo $trainer . "<br/>";
                                        };
                                        ?>
                                    </span>
                                </td>
                            </tr>
                        </table>
                    </fieldset>
                </td>
                <td>
                    <fieldset class="adminform">
                        <legend>NUTRITION DIARY ENTRY DETAILS</legend>
                        <table width="100%">
                            <tr>
                                <td width="100">
                                    <?php echo $this->form->getLabel('entry_date'); ?> 
                                </td>
                                <td>
                                    <?php echo $this->item->entry_date; ?>
                                </td>
                                
                            </tr>
                            <tr>
                                <td>
                                    <?php echo $this->form->getLabel('created'); ?> 
                                </td>
                                <td>
                                    <?php echo $this->item->created; ?>
                                </td>
                                
                            </tr>
                            <tr>
                                <td>
                                    <?php echo $this->form->getLabel('submit_date'); ?> 
                                </td>
                                <td>
                                    <?php 
                                    if($this->item->submit_date != '0000-00-00 00:00:00') {
                                        echo $this->item->submit_date;
                                    } else {
                                        echo 'Not Submitted';
                                    }
                                    ?>
                                </td>
                 
                            </tr>
                            <tr>
                                <td>
                                    <?php echo $this->form->getLabel('assessed_by'); ?>
                                </td>
                                <td>
                                    <?php echo JFactory::getUser($this->item->assessed_by)->name; ?>
                                </td>
                            </tr>
                        </table>
                    </fieldset>
                </td>
            </tr>
            
            <tr>
                <td colspan="2">
                    <fieldset class="adminform">
                        <legend>CURRENT GOALS & TRAINING FOCUS</legend>
                        <table width="100%">
                            <tr>
                                <td width="50%">
                                    <table width="100%">
                                        <tr>
                                            <td width="100">
                                                Primary Goal
                                            </td>
                                            <td>
                                                <span class="grey_title">
                                                    <?php echo $primary_goal->category_name; ?>
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                Start Date
                                            </td>
                                            <td>
                                                <span class="grey_title">
                                                    <?php echo $primary_goal->start_date ?>
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                Achieve By 
                                            </td>
                                            <td>
                                                <span class="grey_title">
                                                    <?php echo $primary_goal->deadline ?>
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                Status 
                                            </td>
                                            <td>
                                                <span class="grey_title">
                                                    <?php  echo $this->backend_goals_model->status_html($primary_goal->id, $primary_goal->status, 'status_button_passive')   ?>
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                Goal Details 
                                            </td>
                                            <td>
                                                <span class="grey_title">
                                                    <?php echo $primary_goal->details ?>
                                                </span>
                                            </td>
                                        </tr>
                                    </table>
                                    
                                </td>
                                <td>
                                    <table width="100%">
                                        <tr>
                                            <td width="100">
                                                Mini  Goal
                                            </td>
                                            <td>
                                                <span class="grey_title">
                                                    <?php echo $mini_goal->category_name; ?>
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td width="100">
                                                Training Period 
                                            </td>
                                            <td>
                                                <span class="grey_title">
                                                    <?php echo $mini_goal->training_period_name; ?>
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                Start Date
                                            </td>
                                            <td>
                                                <span class="grey_title">
                                                    <?php echo $mini_goal->start_date ?>
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                Achieve By 
                                            </td>
                                            <td>
                                                <span class="grey_title">
                                                    <?php echo $mini_goal->deadline ?>
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                Status 
                                            </td>
                                            <td>
                                                <span class="grey_title">
                                                    <?php  echo $this->backend_goals_model->status_html($mini_goal->id, $mini_goal->status, 'status_button_passive')   ?>
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                Goal Details 
                                            </td>
                                            <td>
                                                <span class="grey_title">
                                                    <?php echo $mini_goal->details ?>
                                                </span>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </fieldset>
                </td>
            </tr>
            
            <tr>
                <td colspan="2">
                    <fieldset   class="adminform">
                        <legend>NUTRITION FOCUS</legend>
                            <table width="100%">
                                <tr>
                                    <td width="100">
                                        Nutrition Focus
                                    </td>
                                    <td>
                                        <span class="grey_title">
                                            <?php echo $this->frontend_form_model->getNutritionFocusName($this->item->nutrition_focus); ?>
                                        </span>               
                                    </td>
                                </tr>
                                <tr>
                                    <td width="100">
                                        Nutrition Details
                                    </td>
                                    <td>
                                        <span class="grey_title">
                                            <?php echo $nutrition_plan->trainer_comments;?>
                                        </span>               
                                    </td>
                                </tr>
                            </table>
                        
                        </fieldset>
                </td>
            </tr>
            
            <tr>
                <td colspan="2">
                    <fieldset  id="daily_micronutrient" class="adminform">
                        <legend>MACRONUTRIENT & CALORIE TARGETS</legend>
                        
                        </fieldset>
                </td>
            </tr>
            
            
            <tr>
                <td colspan="2">
                    <fieldset id="diary_guide"  class="adminform">
                        
                        <legend>NUTRITION DIARY ENTRY</legend>
                        <div style="display:none">
                            <?php echo $this->form->getLabel('activity_level'); ?>
                            <?php echo $this->form->getInput('activity_level'); ?>
                        </div>
                        <div class="clr"></div>
                        <div id="meals_wrapper"></div>
                        <div class="clr"></div>
            
                        <br/>
                        <hr>
                        <br/>
                        <div  style="float:left; width:500px;">
                            <table width="100%">
                                <tr>
                                    <td  id="pie_td" class="center">
                                        <div class="pie-container">
                                            <h3>MACRONUTRIENT SCORES</h3>
                                            <div id="placeholder_targets" class="placeholder_pie"></div>
                                        </div>
                                    </td>
                                    <td>
                                        <h3 style="text-align: center;">FINAL SCORE</h3>
                                        <div id="final_score"></div>
                                         <br/>
                                        <div id="status_button_place_<?php echo $this->item->id;?>">
                                            
                                                <?php echo $this->backend_list_model->status_html($this->item->id, $this->item->status) ?>
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div style="float:right">
                            <?php  include   JPATH_COMPONENT_ADMINISTRATOR . DS . 'views' . DS . 'nutrition_diary' . DS . 'tmpl'. DS . 'plan_summary_view.php'; ?>
                        </div>
                        <div class="clr"></div>
                        <div class="clr"></div>
                        
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
    
    <input type="hidden" name="jform[assessed_by]" value="<?php echo JFactory::getUser()->id; ?>" />
    <input type="hidden" id="score_input" name="jform[score]" value="" />
    <input type="hidden" name="task" value="" />
    <?php echo JHtml::_('form.token'); ?>
    <div class="clr"></div>

</form>

<div id="emais_sended"></div>


<script type="text/javascript">
    
    (function($) {
        // targets
        var macronutrient_targets_options = {
            'main_wrapper' : $("#daily_micronutrient"),
            'fitness_administration_url' : '<?php echo JURI::base();?>index.php?option=com_fitness&tmpl=component&<?php echo JSession::getFormToken(); ?>=1',
            'protein_grams_coefficient' : 4,
            'fats_grams_coefficient' : 9,
            'carbs_grams_coefficient' : 4,
            'nutrition_plan_id' : '<?php echo $this->item->nutrition_plan_id;?>',
            'empty_html_data' : {'calories' : "", 'water' : "", 'protein' : "", 'fats' : "", 'carbs' : ""},
            'readonly' : true
        }
        
        var activity_level = '<?php echo $this->item->activity_level; ?>';
        
        
        if(activity_level == '1') {
            var macronutrient_targets_heavy = $.macronutrientTargets(macronutrient_targets_options, 'heavy', 'HEAVY TRAINING DAY');
            macronutrient_targets_heavy.run();
        }
        
        if(activity_level == '2') {
            var macronutrient_targets_light = $.macronutrientTargets(macronutrient_targets_options, 'light', 'LIGHT TRAINING DAY');
            macronutrient_targets_light.run();
        }
        
        if(activity_level == '3') {
            var macronutrient_targets_rest = $.macronutrientTargets(macronutrient_targets_options, 'rest', 'RECOVERY / REST DAY');
            macronutrient_targets_rest.run();
        }
        //
        
        // meals
        var item_description_options = {
            'nutrition_plan_id' : '<?php echo $this->item->id;?>',
            'fitness_administration_url' : '<?php echo JURI::root();?>administrator/index.php?option=com_fitness&tmpl=component&<?php echo JSession::getFormToken(); ?>=1',
            'main_wrapper' : $("#diary_guide"),
            'ingredient_obj' : {id : "", meal_name : "", quantity : "", measurement : "", protein : "", fats : "", carbs : "", calories : "", energy : "", saturated_fat : "", total_sugars : "", sodium : ""},
            'db_table' : '#__fitness_nutrition_diary_ingredients',
            'parent_view' : 'nutrition_plan_backend',
            'read_only' : true

        }

        var nutrition_meal_options = {
            'main_wrapper' : $("#meals_wrapper"),
            'nutrition_plan_id' : '<?php echo $this->item->id;?>',
            'fitness_administration_url' : '<?php echo JURI::root();?>administrator/index.php?option=com_fitness&tmpl=component&<?php echo JSession::getFormToken(); ?>=1',
            'add_meal_button' : $("#add_plan_meal"),
            'activity_level' : "input[name='jform[activity_level]']",
            'meal_obj' : {id : "", 'nutrition_plan_id' : "", 'meal_time' : "", 'water' : "", 'previous_water' : ""},
            'db_table' : '#__fitness_nutrition_diary_meals',
            'read_only' : true,
            'import_date' : true,
            'import_date_source' : '#jform_entry_date'
        }


        var nutrition_comment_options = {
            'item_id' : '<?php echo $this->item->id;?>',
            'fitness_administration_url' : '<?php echo JURI::root();?>administrator/index.php?option=com_fitness&tmpl=component&<?php echo JSession::getFormToken(); ?>=1',
            'comment_obj' : {'user_name' : '<?php echo JFactory::getUser()->name;?>', 'created' : "", 'comment' : ""},
            'db_table' : '#__fitness_nutrition_diary_meal_comments',
            'read_only' : false
        }
        
        var nutrition_bottom_comment_options = {
            'item_id' : '<?php echo $this->item->id;?>',
            'fitness_administration_url' : '<?php echo JURI::root();?>administrator/index.php?option=com_fitness&tmpl=component&<?php echo JSession::getFormToken(); ?>=1',
            'comment_obj' : {'user_name' : '<?php echo JFactory::getUser()->name;?>', 'created' : "", 'comment' : ""},
            'db_table' : '#__fitness_nutrition_diary_comments',
            'read_only' : false
        }
        
        var calculate_summary_options = {
            'activity_level' : "input[name='jform[activity_level]']",
            'chart_container' : $("#placeholder_targets"),
            'draw_chart' : true
        }
        
        var status_options = {
            'fitness_administration_url' : '<?php echo JURI::root();?>administrator/index.php?option=com_fitness&tmpl=component&<?php echo JSession::getFormToken(); ?>=1',
            'calendar_frontend_url' : '<?php echo JURI::root()?>index.php?option=com_multicalendar&task=load&calid=0',
            'db_table' : '#__fitness_nutrition_diary',
            'status_button' : 'status_button',
            'status_button_dialog' : 'status_button_dialog',
            'dialog_status_wrapper' : 'dialog_status_wrapper',
            'dialog_status_template' : '#dialog_status_template',
            'status_button_template' : '#status_button_template',
            'status_button_place' : '#status_button_place_',
            'statuses' : {
                '2' : {'label' : 'PASS', 'class' : 'status_pass', 'email_alias' : 'DiaryPass'},
                '3' : {'label' : 'FAIL', 'class' : 'status_fail', 'email_alias' : 'DiaryFail'}, 
                '4' : {'label' : 'DISTINCTION', 'class' : 'status_distinction', 'email_alias' : ''}
            },
            'statuses2' : {},
            'close_image' : '<?php echo JUri::root() ?>administrator/components/com_fitness/assets/images/close.png',
            'hide_image_class' : 'hideimage',
            'show_send_email' : true,
             setStatuses : function(item_id) {
                return this.statuses;
            },
            'view' : 'NutritionDiary'
        }
        
        // meal blocks object
        var nutrition_meal = $.nutritionMeal(nutrition_meal_options, item_description_options, nutrition_comment_options);
        
        var calculateSummary =  $.calculateSummary(calculate_summary_options);
        
         //bottom comments
        var plan_comments = $.comments(nutrition_bottom_comment_options, nutrition_comment_options.item_id, 0);
        
        
        nutrition_meal.run();
        calculateSummary.run();
        
        var plan_comments_html = plan_comments.run();
        $("#plan_comments_wrapper").html(plan_comments_html);
        
        // status
        var score_status = $.status(status_options);
        score_status.run();
        
        
        Joomla.submitbutton = function(task)
        {
            if (task == 'nutrition_diary.cancel') {
                Joomla.submitform(task, document.getElementById('nutrition_diary-form'));
            }
            else{

                if (task != 'nutrition_diary.cancel' && document.formvalidator.isValid(document.id('nutrition_diary-form'))) {

                    Joomla.submitform(task, document.getElementById('nutrition_diary-form'));
                }
                else {
                    alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED')); ?>');
                }
            }
        }
        
    })($js);

    
</script>

