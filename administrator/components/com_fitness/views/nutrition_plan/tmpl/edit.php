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

<form action="<?php echo JRoute::_('index.php?option=com_fitness&layout=edit&id=' . (int) $this->item->id); ?>" method="post" enctype="multipart/form-data" name="adminForm" id="nutrition_plan-form" class="form-validate">
    <div class="width-100 fltlft">
        <fieldset  class="adminform">
        <legend id="plan_menu"></legend>
        
        <!-- OVERVIEW -->
        <div id="overview_wrapper" class="block" style="display:none;">
            <table width="100%" style="height: 450px;">
                <tr>
                    <td width="30%" style="vertical-align:top;height: 100%;">
                        <fieldset style=" height: 100%; padding-bottom: 0;margin-bottom: 0;" class="adminform">
                            <legend>CLIENT & TRAINER(S)</legend>
                            <ul>
                                <li><?php echo $this->form->getLabel('business_profile_id'); 
                                    $business_profile_id = $helper->getBubinessIdByClientId($this->item->client_id);

                                    echo $helper->generateSelect($helper->getBusinessProfileList(), 'jform[business_profile_id]', 'business_profile_id', $business_profile_id , '', true, "required", true); ?>
                                </li>

                                <li><?php echo $this->form->getLabel('trainer_id'); ?>
                                    <?php echo $helper->generateSelect(array(), 'jform[trainer_id]', 'jform_trainer_id', $this->item->trainer_id, '' ,true, 'required', true); ?>
                                </li>
                                <li><?php echo $this->form->getLabel('client_id'); ?>
                                    <select style="pointer-events: none; cursor: default;"  id="jform_client_id" class="inputbox required" name="jform[client_id]">
                                        <?php
                                        if($this->item->client_id) {
                                            echo '<option value="' . $this->item->client_id . '">'. JFactory::getUser($this->item->client_id)->name .'</option>';
                                        } else {
                                            echo '<option value="">' . JText::_('-Select-')  . '</option>';
                                        }
                                        ?>
                                    </select>
                                </li>
                                <li>
                                    <label id="jform_secondary_trainers-lbl"  for="jform_secondary_trainers" >Secondary Trainers</label>
                                    <div class="clr"></div>
                                    <div style="margin-left:138px;" id="secondary_trainers"></div>
                                </li>
                            </ul>
                        </fieldset>
                    </td>
                    <td style="vertical-align:top;height: 100%;" >
                        <fieldset style=" height: 100%;padding-bottom: 0;margin-bottom: 0;"   class="adminform">
                            <legend>CLIENT GOALS, TRAINING & NUTRITION PERIODS</legend>
                            <table>
                                <tr>
                                    <td>
                                        <?php echo $this->form->getLabel('primary_goal'); ?>
                                        <select style="pointer-events: none; cursor: default;"  id="jform_primary_goal" class="inputbox required" name="jform[primary_goal]">
                                            <?php
                                            if($this->item->primary_goal) {
                                                echo '<option value="' . $this->item->primary_goal. '">'. $this->getPrimaryGoalName($this->item->primary_goal) .'</option>';
                                            } else {
                                                echo '<option value="">' . JText::_('-Select-')  . '</option>';
                                            }
                                            ?>
                                        </select>
                                    </td>
                                    <td>
                                        <?php echo $this->form->getLabel('state'); ?>
                                        <?php echo $this->form->getInput('state'); ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <label for="primary_goal_start_date">
                                        Start Date
                                        </label>
                                        <input id="primary_goal_start_date" ctype="text" value="" name="primary_goal_start_date" readonly="readonly" >
                                    </td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>
                                        <label for="primary_goal_deadline">
                                        Achieve By
                                        </label>
                                        <input id="primary_goal_deadline" value="" name="primary_goal_deadline" readonly="readonly" >
                                    </td>
                                     <td></td>
                                </tr>
                            </table>
                            
                            <hr>
                            
                            <table>
                                <tr>
                                    <td id="plan_mini_goals" colspan="4">

                                    </td>
                                </tr>
                            </table>
                            
                            <hr>
                            
                            <table>
                                <tr>
                                    <td>
                                        <label>Nutrition Plan Status </label>
                                    </td>
                                    <td>
                                        <?php
                                            $active_plan_id = $this->backend_list_model->getUserActivePlanId($this->item->client_id);
                                            echo $this->showActiveStatus($this->item->id, $active_plan_id);
                                        ?>
                                    </td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>
                                        <?php echo $this->form->getLabel('force_active'); ?>
                                     </td>
                                    <td>
                                        <?php echo $this->form->getInput('force_active'); ?>
                                    </td>
                                    <td>
                                        <table>
                                            <tbody>
                                                <tr>
                                                    <td>
                                                        <div class="notice_image"></div>
                                                    </td>
                                                    <td style="font-size:10px;"> If this Nutrition Plan is ‘forced active’ ... <br>
                                                        - nutrition plan will only stay forced active until the expiry date set above! <br>
                                                        - after expiry, the most recently created (and current) nutrition plan will then become ‘active’
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="3">
                                        Warning - There may be several Nutrition Plans with overlapping dates! <br>
                                        Forcing this plan ‘active’ will set this Nutrition Plan as the client’s ‘current plan’. <br>
                                        All Nutrition Diary entries, calculations and scoring will be performed using the values in this Nutrition Plan!
                                    </td>
                                </tr>
                            </table>
                        </fieldset>
                        <input  id="jform_mini_goal" class="required" type="hidden"  value="<?php echo $this->item->mini_goal ?>" name="jform[mini_goal]"  required="required">
                    </td>
                </tr>
            </table>
            <div class="clr"></div>

            <fieldset style="margin: 30px 12px 12px;;"  class="adminform">
                <legend>NUTRITION FOCUS</legend>
                <?php echo $this->form->getLabel('nutrition_focus'); ?>
                
                <?php
                echo $helper->generateSelect($helper->getNutritionFocuses(), 'jform[nutrition_focus]', 'jform_nutrition_focus', $this->item->nutrition_focus, '', true, "required");
                ?>
                <div class="clr"></div>
                <?php echo $this->form->getLabel('trainer_comments'); ?>
                <?php echo $this->form->getInput('trainer_comments'); ?>
            </fieldset>
        </div>
        
        <!-- TARGETS -->
        <div id="targets_wrapper" class="block" style="display:none;">
            <fieldset id="daily_micronutrient"  class="adminform">
                <?php
                if(!$this->item->id) {
                    echo 'Save form to proceed add Targets';
                }
                ?>
                <legend>DAILY MACRONUTRIENT & CALORIE TARGETS</legend>
            </fieldset>
            <div class="clr"></div>
            <br/>
            <div id="targets_comments_wrapper" style="width:100%"></div>
            <div class="clr"></div>
            <br/>
            <input id="add_comment_0" class="" type="button" value="Add Comment" >
            <div class="clr"></div>
        </div>
        
        <!-- MACRONUTRIENTS -->
        <div id="macronutrients_wrapper" class="block" style="display:none;">
            <fieldset  class="adminform">
                <legend>ALLOWED FOODS SHOPPING LIST</legend>
                <div class="clr"></div>
                <?php echo $this->form->getLabel('allowed_proteins'); ?>
                <?php echo $this->form->getInput('allowed_proteins'); ?>
                <div class="clr"></div>
                <br/>
                <?php echo $this->form->getLabel('allowed_fats'); ?>
                <?php echo $this->form->getInput('allowed_fats'); ?>
                <div class="clr"></div>
                <br/>
                <?php echo $this->form->getLabel('allowed_carbs'); ?>
                <?php echo $this->form->getInput('allowed_carbs'); ?>
                <div class="clr"></div>
                <br/>
                <?php echo $this->form->getLabel('allowed_liquids'); ?>
                <?php echo $this->form->getInput('allowed_liquids'); ?>
                <div class="clr"></div>
                <br/>
                <?php echo $this->form->getLabel('other_recommendations'); ?>
                <?php echo $this->form->getInput('other_recommendations'); ?>
                <div class="clr"></div>
                <br/>
            </fieldset>
            <div class="clr"></div>
            <br/>
            <div id="macronutrients_comments_wrapper" stle="width:100%"></div>
            <div class="clr"></div>
            <br/>
            <input id="add_comment_1" class="" type="button" value="Add Comment" >
            <div class="clr"></div>
        </div>
        
        <!-- SUPPLEMENTS -->
        <div id="supplements_wrapper" class="block" style="display:none;">
            <fieldset  class="adminform">
                <legend>SUPPLEMENTS & SUPPLEMENT PROTOCOLS</legend>
                <div id="protocols_wrapper">

                </div>
            </fieldset>
        </div>
        
        <!-- DIARY GUIDE -->
        <div id="diary_guide_wrapper" class="block" style="display:none;">
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
                <br/>
                <?php
                if($this->item->id) {
                ?>
                <div style="float:right">
                    <?php  include   JPATH_COMPONENT_ADMINISTRATOR . DS . 'views' . DS . 'nutrition_diary' . DS . 'tmpl'. DS . 'plan_summary_view.php'; ?>
                </div>
                <?php
                }
                ?>
                <div class="clr"></div>
                <div class="clr"></div>

                <hr>
                <div id="plan_comments_wrapper"></div>
                <div class="clr"></div>
                <input id="add_comment_0" class="" type="button" value="Add Comment" >
                <div class="clr"></div>
            </fieldset>
        </div>
        
        <!-- NUTRITION GUIDE-->
        <div id="nutrition_guide_wrapper" class="block" style="display:none;">
            <div id="nutrition_guide_header"></div>
            <div id="nutrition_guide_container"></div>
        </div>
        
        <!-- INFORMATION -->
        <div id="information_wrapper" class="block">
            <fieldset  class="adminform">
                <legend>INFORMATION</legend>
                <?php echo $this->form->getLabel('information'); ?>
                <?php echo $this->form->getInput('information'); ?>
            </fieldset>
        </div>

        </fieldset>
    </div>
        
    <div style="display:none;">
    <?php echo $this->form->getLabel('created'); ?>
    <?php echo $this->form->getInput('created'); ?>
    </div>
    <input type="hidden" name="task" value="" />
    <?php echo JHtml::_('form.token'); ?>
    <div class="clr"></div>
    

</form>

<?php

$user_id = JFactory::getUser()->id;

?>


<script type="text/javascript">
    var options = {
        // main options
            'fitness_administration_url' : '<?php echo JURI::root();?>administrator/index.php?option=com_fitness&tmpl=component&<?php echo JSession::getFormToken(); ?>=1',
            'fitness_frontend_url' : '<?php echo JURI::root();?>index.php?option=com_fitness&tmpl=component&<?php echo JSession::getFormToken(); ?>=1',
            'calendar_frontend_url' : '<?php echo JURI::root()?>index.php?option=com_multicalendar&task=load&calid=0',
            'base_url' : '<?php echo JURI::root();?>',
            'ajax_call_url' : '<?php echo JURI::root();?>administrator/index.php?option=com_fitness&tmpl=component&<?php echo JSession::getFormToken(); ?>=1',
            'pending_review_text' : 'Pending Review',
            'user_name' : '<?php echo JFactory::getUser()->name;?>',
            'user_id' : '<?php echo JFactory::getUser()->id;?>',
            'goals_db_table' : '#__fitness_goals',
            'minigoals_db_table' : '#__fitness_mini_goals',
            'goals_comments_db_table' : '#__fitness_goal_comments',
            'minigoals_comments_db_table' : '#__fitness_mini_goal_comments',
            'nutrition_plan_targets_comments_db_table' : '#__fitness_nutrition_plan_targets_comments',
            'nutrition_plan_macronutrients_comments_db_table' : '#__fitness_nutrition_plan_macronutrients_comments',
            'protocol_comments_db_table' : '#__fitness_nutrition_plan_supplements_comments',
            'example_day_meal_comments_db_table' : '#__fitness_nutrition_plan_example_day_meal_comments',
            
            'client_id' : '<?php echo JFactory::getUser()->id;?>',
            'user_id' : '<?php echo JFactory::getUser()->id;?>',
            'trainer_id' : '<?php echo $this->item->trainer_id;?>',
            
            'user_name' : '<?php echo JFactory::getUser()->name;?>',
            
            'item_id' : '<?php echo $this->item->id;?>',
            
            
            //nutrition plan class options
            'nutrition_plan_id' : '<?php echo $this->item->id;?>',
            'business_profile_select' : "#business_profile_id",
            'trainer_select' : "#jform_trainer_id",
            'client_select' : "#jform_client_id",
            'secondary_trainers_wrapper' : "#secondary_trainers",
            'primary_goal_select' : "#jform_primary_goal",
            'client_selected' : '<?php echo $this->item->client_id;?>',
            'primary_goal_selected' : '<?php echo $this->item->primary_goal;?>',
            'mini_goal_selected' : '<?php echo $this->item->mini_goal;?>',
            'active_finish_value' : '<?php echo $this->item->active_finish;?>',
            'max_possible_date' : '9999-12-31',
            'primary_goal_start_date' : "#primary_goal_start_date",
            'primary_goal_deadline' : "#primary_goal_deadline",
            'override_dates' : '<?php echo $this->item->override_dates;?>',
            'active_start' : '<?php echo $this->item->active_start;?>',
            'active_finish' : '<?php echo $this->item->active_finish;?>',
            
            //targets options
            'targets_main_wrapper' : "#daily_micronutrient",
            'protein_grams_coefficient' : 4,
            'fats_grams_coefficient' : 9,
            'carbs_grams_coefficient' : 4,
            'empty_html_data' : {'calories' : "", 'water' : "", 'protein' : "", 'fats' : "", 'carbs' : ""},
            
            'is_backend' : true,
            'is_trainer' : '<?php echo FitnessFactory::is_trainer($user_id); ?>',
            'is_client' : '<?php echo FitnessFactory::is_client($user_id); ?>',
    
        };
        
        //status class
        var status_options = {
            'fitness_administration_url' : '<?php echo JURI::root();?>administrator/index.php?option=com_fitness&tmpl=component&<?php echo JSession::getFormToken(); ?>=1',
            'calendar_frontend_url' : '<?php echo JURI::root()?>index.php?option=com_multicalendar&task=load&calid=0',
            'db_table' : '#__fitness_nutrition_plan_menus',
            'status_button' : 'status_button',
            'status_button_dialog' : 'status_button_dialog',
            'dialog_status_wrapper' : 'dialog_status_wrapper',
            'dialog_status_template' : '#dialog_status_template',
            'status_button_template' : '#status_button_template',
            'status_button_place' : '#status_button_place_',
            'statuses' : {
                '1' : {'label' : 'PENDING', 'class' : 'menu_plan_status_pending', 'email_alias' : ''}, 
                '2' : {'label' : 'APPROVED', 'class' : 'status_approved', 'email_alias' : 'menu_plan_approved'},
                '3' : {'label' : 'NOT APPROVED', 'class' : 'status_notapproved', 'email_alias' : 'menu_plan_notapproved'},
                '4' : {'label' : 'IN PROGRESS', 'class' : 'status_inprogress', 'email_alias' : 'menu_plan_inprogress'},
                '5' : {'label' : 'SUBMITTED', 'class' : 'status_submitted', 'email_alias' : ''}, 
                '6' : {'label' : 'RESUBMIT', 'class' : 'status_resubmit', 'email_alias' : 'menu_plan_resubmit'}
            },
            'statuses2' : {},
            'close_image' : '<?php echo JUri::root() ?>administrator/components/com_fitness/assets/images/close.png',
            'hide_image_class' : 'hideimage',
            'show_send_email' : true,
            setStatuses : function(item_id) {
                return this.statuses;
            },
            'view' : 'MenuPlan',
            'set_updater' : true,
            'user_id' : options.user_id 
        }
        
        options.status_options = status_options;

        //requireJS options

        require.config({
            baseUrl: '<?php echo JURI::root();?>administrator/components/com_fitness/assets/js',
        });


        require(['app'], function(app) {
                app.options = options;
        });
        
        

</script>
<script src="<?php echo JURI::root();?>administrator/components/com_fitness/assets/js/config.js" type="text/javascript"></script>
<script src="<?php echo JURI::root();?>administrator/components/com_fitness/assets/js/main_nutrition_plan.js" type="text/javascript"></script>
