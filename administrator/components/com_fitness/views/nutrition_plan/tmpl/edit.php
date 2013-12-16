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
    
    #jform_allowed_proteins-lbl, #jform_allowed_fats-lbl, #jform_allowed_carbs-lbl, #jform_allowed_liquids-lbl, #jform_other_recommendations-lbl, #jform_trainer_comments-lbl, #jform_information-lbl{
        float: none;
    }
</style>

<form action="<?php echo JRoute::_('index.php?option=com_fitness&layout=edit&id=' . (int) $this->item->id); ?>" method="post" enctype="multipart/form-data" name="adminForm" id="nutrition_plan-form" class="form-validate">
    <div class="width-100 fltlft">
        <fieldset  class="adminform">
        <legend id="plan_menu"></legend>
        
        <!-- OVERVIEW -->
        <div id="overview_wrapper" class="block">
            <table width="100%" style="height: 450px;">
                <tr>
                    <td width="30%" style="vertical-align:top;height: 100%;">
                        <fieldset style=" height: 100%; padding-bottom: 0;margin-bottom: 0;" class="adminform">
                            <legend>CLIENT & TRAINER(S)</legend>
                            <ul>
                                <li><?php echo $this->form->getLabel('business_profile_id'); 
                                    $business_profile_id = $helper->getBubinessIdByClientId($this->item->client_id);

                                    echo $helper->generateSelect($helper->getBusinessProfileList(), 'jform[business_profile_id]', 'business_profile_id', $business_profile_id , '', true, "required required"); ?>
                                </li>

                                <li><?php echo $this->form->getLabel('trainer_id'); ?>
                                    <?php echo $helper->generateSelect(array(), 'jform[trainer_id]', 'jform_trainer_id', $this->item->trainer_id, '' ,true, 'required'); ?>
                                </li>
                                <li><?php echo $this->form->getLabel('client_id'); ?>
                                    <select id="jform_client_id" class="inputbox required" name="jform[client_id]">
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
                                        <select id="jform_primary_goal" class="inputbox required" name="jform[primary_goal]">
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
                                            echo $this->showActiveStatus($item->id, $active_plan_id);
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
                <?php echo $this->form->getInput('nutrition_focus'); ?>
                <div class="clr"></div>
                <?php echo $this->form->getLabel('trainer_comments'); ?>
                <?php echo $this->form->getInput('trainer_comments'); ?>
            </fieldset>
        </div>
        
        <!-- TARGETS -->
        <div id="targets_wrapper" class="block">
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
        <div id="macronutrients_wrapper" class="block">
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
        <div id="supplements_wrapper" class="block">
            <fieldset  class="adminform">
                <legend>SUPPLEMENTS & SUPPLEMENT PROTOCOLS</legend>
                <div id="protocols_wrapper">

                </div>
            </fieldset>
        </div>
        
        <!-- NUTRITION GUIDE -->
        <div id="nutrition_guide_wrapper" class="block">
            <fieldset id="nutrition_guide"  class="adminform">
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



<script type="text/javascript">
    

    
    (function($) {
        
        // connect helper class
        var helper_options = {
            'ajax_call_url' : '<?php echo JURI::root();?>administrator/index.php?option=com_fitness&tmpl=component&<?php echo JSession::getFormToken(); ?>=1',
        }
        var fitness_helper = $.fitness_helper(helper_options);
        
        $("#business_profile_id").on('change', function() {
            var business_profile_id = $(this).val();
            fitness_helper.populateTrainersSelectOnBusiness('user_group', business_profile_id, '#jform_trainer_id', '<?php echo $this->item->trainer_id; ?>');

        });
        
        var business_profile_id = $("#business_profile_id").val();
        if(business_profile_id) {
            fitness_helper.populateTrainersSelectOnBusiness('user_group', business_profile_id, '#jform_trainer_id', '<?php echo $this->item->trainer_id; ?>');
        }


        /*  OPTIONS  */
        var nutrition_plan_options = {
            'business_profile_select' : $("#business_profile_id"),
            'trainer_select' : $("#jform_trainer_id"),
            'client_select' : $("#jform_client_id"),
            'secondary_trainers_wrapper' : $("#secondary_trainers"),
            'primary_goal_select' : $("#jform_primary_goal"),
            'calendar_frontend_url' : '<?php echo JURI::root();?>index.php?option=com_multicalendar&task=load&calid=0',
            'fitness_administration_url' : '<?php echo JURI::root();?>administrator/index.php?option=com_fitness&tmpl=component&<?php echo JSession::getFormToken(); ?>=1',
            'client_selected' : '<?php echo $this->item->client_id;?>',
            'primary_goal_selected' : '<?php echo $this->item->primary_goal;?>',
            'mini_goal_selected' : '<?php echo $this->item->mini_goal;?>',
            'active_finish_value' : '<?php echo $this->item->active_finish;?>',
            'max_possible_date' : '9999-12-31',
            'primary_goal_start_date' : $("#primary_goal_start_date"),
            'primary_goal_deadline' : $("#primary_goal_deadline"),
            'override_dates' : '<?php echo $this->item->override_dates;?>',
            'active_start' : '<?php echo $this->item->active_start;?>',
            'active_finish' : '<?php echo $this->item->active_finish;?>',
           
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
            'logged_in_admin' : <?php echo JFactory::getApplication()->isAdmin();?>,
            'fitness_frontend_url' : '<?php echo JURI::root();?>index.php?option=com_fitness&tmpl=component&<?php echo JSession::getFormToken(); ?>=1',
            'fitness_administration_url' : '<?php echo JURI::root();?>administrator/index.php?option=com_fitness&tmpl=component&<?php echo JSession::getFormToken(); ?>=1',
            'main_wrapper' : $("#nutrition_guide"),
            'ingredient_obj' : {id : "", meal_name : "", quantity : "", measurement : "", protein : "", fats : "", carbs : "", calories : "", energy : "", saturated_fat : "", total_sugars : "", sodium : ""},
            'db_table' : '#__fitness_nutrition_plan_ingredients',
            'parent_view' : 'nutrition_plan_backend',
            'read_only' : false
        }

        var nutrition_meal_options = {
            'main_wrapper' : $("#meals_wrapper"),
            'nutrition_plan_id' : '<?php echo $this->item->id;?>',
            'fitness_administration_url' : '<?php echo JURI::root();?>administrator/index.php?option=com_fitness&tmpl=component&<?php echo JSession::getFormToken(); ?>=1',
            'add_meal_button' : $("#add_plan_meal"),
            'activity_level' : "input[name='jform[activity_level]']",
            'meal_obj' : {id : "", 'nutrition_plan_id' : "", 'meal_time' : "", 'water' : "", 'previous_water' : ""},
            'db_table' : '#__fitness_nutrition_plan_meals',
            'read_only' : false,
            'import_date' : false,
            'import_date_source' : ''
        }


        var nutrition_comment_options = {
            'item_id' : '<?php echo $this->item->id;?>',
            'fitness_administration_url' : '<?php echo JURI::root();?>administrator/index.php?option=com_fitness&tmpl=component&<?php echo JSession::getFormToken(); ?>=1',
            'comment_obj' : {'user_name' : '<?php echo JFactory::getUser()->name;?>', 'created' : "", 'comment' : ""},
            'db_table' : '#__fitness_nutrition_plan_meal_comments',
            'read_only' : false
        }

        var nutrition_bottom_comment_options = {
            'item_id' : '<?php echo $this->item->id;?>',
            'fitness_administration_url' : '<?php echo JURI::root();?>administrator/index.php?option=com_fitness&tmpl=component&<?php echo JSession::getFormToken(); ?>=1',
            'comment_obj' : {'user_name' : '<?php echo JFactory::getUser()->name;?>', 'created' : "", 'comment' : ""},
            'db_table' : '#__fitness_nutrition_plan_comments',
            'read_only' : false
        }



        var calculate_summary_options = {
            'activity_level' : "input[name='jform[activity_level]']",
            'draw_chart' : false

        }
        /* END  OPTIONS  */
    
    
        // cteate main object
        var nutrition_plan = $.nutritionPlan(nutrition_plan_options);

        // append targets fieldsets
        var macronutrient_targets_heavy = $.macronutrientTargets(macronutrient_targets_options, 'heavy', 'HEAVY TRAINING DAY');

        var macronutrient_targets_light = $.macronutrientTargets(macronutrient_targets_options, 'light', 'LIGHT TRAINING DAY');

        var macronutrient_targets_rest = $.macronutrientTargets(macronutrient_targets_options, 'rest', 'RECOVERY / REST DAY');


        // meal blocks object
        var nutrition_meal = $.nutritionMeal(nutrition_meal_options, item_description_options, nutrition_comment_options);


        //bottom comments
        var plan_comments = $.comments(nutrition_bottom_comment_options, nutrition_comment_options.item_id, 0);

        var calculateSummary =  $.calculateSummary(calculate_summary_options);


        nutrition_plan.run();

        macronutrient_targets_heavy.run();
        macronutrient_targets_light.run();
        macronutrient_targets_rest.run();

        nutrition_meal.run();

        var plan_comments_html = plan_comments.run();
        $("#plan_comments_wrapper").html(plan_comments_html);

        calculateSummary.run();
        
        
        //BACKBONE MENU LOGIC
        window.app = {};
        Backbone.emulateHTTP = true ;
        Backbone.emulateJSON = true;

        var backbone_menu_options = {

            'fitness_backend_url' : '<?php echo JURI::root();?>administrator/index.php?option=com_fitness&tmpl=component&<?php echo JSession::getFormToken(); ?>=1',
            'calendar_frontend_url' : '<?php echo JURI::root()?>index.php?option=com_multicalendar&task=load&calid=0',
            'pending_review_text' : 'Pending Review',
            'user_name' : '<?php echo JFactory::getUser()->name;?>',
            'user_id' : '<?php echo JFactory::getUser()->id;?>',
            'goals_db_table' : '#__fitness_goals',
            'minigoals_db_table' : '#__fitness_mini_goals',
            'goals_comments_db_table' : '#__fitness_goal_comments',
            'minigoals_comments_db_table' : '#__fitness_mini_goal_comments',
            'nutrition_plan_targets_comments_db_table' : '#__fitness_nutrition_plan_targets_comments',
            'nutrition_plan_macronutrients_comments_db_table' : '#__fitness_nutrition_plan_macronutrients_comments',
            
            'user_name' : '<?php echo JFactory::getUser()->name;?>',
            
            'item_id' : '<?php echo  $this->item->id ?>'
        };
        
        //MODELS
        window.app.Nutrition_plan_model = Backbone.Model.extend({
            defaults: {

            },

            initialize: function(){
                
            },

            ajaxCall : function(data, url, view, task, table, handleData) {
                return $.AjaxCall(data, url, view, task, table, handleData);
            },
                    

            
            connect_targets_comments : function() {
                var comment_options = {
                    'item_id' :  this.get('item_id'),
                    'fitness_administration_url' : this.get('fitness_frontend_url'),
                    'comment_obj' : {'user_name' : this.get('user_name'), 'created' : "", 'comment' : ""},
                    'db_table' : this.get('nutrition_plan_targets_comments_db_table'),
                    'read_only' : false,
                    'anable_comment_email' : false
                }
                var comments = $.comments(comment_options, comment_options.item_id, 0);

                var comments_html = comments.run();
                $("#targets_comments_wrapper").html(comments_html);
            },
            
            connect_macronutrients_comments : function() {
                var comment_options = {
                    'item_id' :  this.get('item_id'),
                    'fitness_administration_url' : this.get('fitness_frontend_url'),
                    'comment_obj' : {'user_name' : this.get('user_name'), 'created' : "", 'comment' : ""},
                    'db_table' : this.get('nutrition_plan_macronutrients_comments_db_table'),
                    'read_only' : false,
                    'anable_comment_email' : false
                }
                var comments = $.comments(comment_options, comment_options.item_id, 1);

                var comments_html = comments.run();
                $("#macronutrients_comments_wrapper").html(comments_html);
            }
            
        });
        
        
        //VIEWS
        
        window.app.Nutrition_plan_menu_view = Backbone.View.extend({
            el: $("#plan_menu"), 
            
            initialize: function(){
                this.render();
            },
            
            render: function(){
                this.loadTemplate();
            },
                    
            events: {
                "click #overview_link" : "onClickOverview",
                "click #targets_link" : "onClickTargets",
                "click #macronutrients_link" : "onClickMacronutrients",
                "click #supplements_link" : "onClickSupplements",
                "click #nutrition_guide_link" : "onClickNutrition_guide",
                "click #information_link" : "onClickInformation",
                "click #archive_focus_link" : "onClickArchive_focus",
                "click #close_tab" : "onClickClose",
            },
            
            loadTemplate : function(variables, target) {
                var template = _.template( $("#nutrition_plan_menu_template").html(), variables );
                this.$el.html(template);
                $("#archive_focus_link").parent().hide();
            },
            
            onClickOverview : function() {
                window.app.controller.navigate("!/overview", true);
            },
            
            onClickTargets : function() {
                window.app.controller.navigate("!/targets", true);
            },
            
            onClickMacronutrients : function() {
                window.app.controller.navigate("!/macronutrients", true);
            },
            
            onClickSupplements : function() {
                window.app.controller.navigate("!/supplements", true);
            },
            
            onClickNutrition_guide : function() {
                window.app.controller.navigate("!/nutrition_guide", true);
            },
            
            onClickInformation : function() {
                window.app.controller.navigate("!/information", true);
            },
            
            onClickArchive_focus : function() {
                window.app.controller.navigate("!/archive", true);
            },
            
            onClickClose : function() {
                window.app.controller.navigate("!/close", true);
            }

        });
        
        
        window.app.nutrition_plan_menu_view = new window.app.Nutrition_plan_menu_view();
        
        
        //INIT
        window.app.nutrition_plan_model = new window.app.Nutrition_plan_model(backbone_menu_options);
        
        
       
        //BACKBONE PROTOCOLS
        
        window.protocol_options = {

            'fitness_backend_url' : '<?php echo JURI::root();?>administrator/index.php?option=com_fitness&tmpl=component&<?php echo JSession::getFormToken(); ?>=1',
            
            'nutrition_plan_id' : '<?php echo  $this->item->id ?>',
    
            'user_name' : '<?php echo JFactory::getUser()->name;?>',
                   
            'protocol_comments_db_table' : '#__fitness_nutrition_plan_supplements_comments'
        };
        
        
        
        window.app.Protocols_wrapper = Backbone.View.extend({

            template : _.template($('#nutrition_plan_protocol_template').html()),

            initialize:function () {
                this.render();
            },

            render:function (eventName) {
                $(this.el).html(this.template());
                return this;
            },

            events:{
                "click #add_protocol": "onAddProtocol"
            },

            onAddProtocol:function () {
                window.app.controller.navigate("!/supplements");
                window.app.controller.navigate("!/add_supplement_protocol", true);
            }

        });
        
        
        
        window.app.Nutrition_plan_protocols_view = Backbone.View.extend({
            
            initialize: function(){
                
                var self = this;
                
		this.protocolListItemViews = {};

		this.collection.on("add", function(protocol) {
                    var comment_options = {
                        'item_id' : window.protocol_options.nutrition_plan_id,
                        'fitness_administration_url' : window.protocol_options.fitness_backend_url,
                        'comment_obj' : {'user_name' : window.protocol_options.user_name, 'created' : "", 'comment' : ""},
                        'db_table' : window.protocol_options.protocol_comments_db_table,
                        'read_only' : false,
                    }
                    var comments = $.comments(comment_options, comment_options.item_id, protocol.id);

                    window.app.nutrition_plan_protocol_view = new window.app.Nutrition_plan_protocol_view({collection : this,  model : protocol, 'comments' : comments}); 
                    $(self.el).append( window.app.nutrition_plan_protocol_view.render().el );
                    self.protocolListItemViews[ protocol.cid ] = window.app.nutrition_plan_protocol_view;
		});
		
		this.collection.on("remove", function(protocol, options) {
                    self.protocolListItemViews[ protocol.cid ].close();
                    delete self.protocolListItemViews[ protocol.cid ];
		});
            },
            
            render : function () {
                
                _.each(this.collection.models, function (protocol) { 
                    $(this.el).append(new window.app.Nutrition_plan_protocol_view({model : protocol }).render().el); 
                    
                }, this);

                return this;
            },
                    
            events: {
                "click #add_protocol" : "onClickAddProtocol",
            },

        });
        
        
        window.app.Nutrition_plan_protocol_view = Backbone.View.extend({
           
            initialize: function(){
                _.bindAll(this, 'onClickSaveProtocol','close', 'render');
                this.model.on("destroy", this.close, this);
            },
            
            render: function(){
                var template = _.template( $("#nutrition_plan_protocol_item_template").html(), this.model.toJSON());
                this.$el.html(template);
                
                this.connectComments(this.$el);

                this.supplements_list_el = this.$el.find(".supplements_list");
                
                this.supplement_collection = new  window.app.Supplements_collection();
  
                this.protocol_id = this.model.get('id');
                
                if(this.protocol_id ){ 
  
                    this.supplement_collection.fetch({data: {nutrition_plan_id : window.protocol_options.nutrition_plan_id, protocol_id : this.protocol_id}});
                }
                
                
                var self = this;
                
		this.supplementListItemViews = {};

		this.supplement_collection.on("add", function(supplement) {
                    window.app.nutrition_plan_supplement_view = new window.app.Nutrition_plan_supplement_view({collection : self.supplement_collection, model : supplement}); 
                    self.supplements_list_el.append( window.app.nutrition_plan_supplement_view.render().el );
                    self.supplementListItemViews[ supplement.cid ] = window.app.nutrition_plan_supplement_view;
		});
		
		this.supplement_collection.on("remove", function(supplement, options) {
                    self.supplementListItemViews[ supplement.cid ].close();
                    delete self.supplementListItemViews[ supplement.cid ];
		});
                
                return this;
            },
            
            connectComments : function() {
                if(typeof this.options.comments !== 'undefined') {
                    var comments = this.options.comments.run();
                    this.$el.find(".comments_wrapper").html(comments);
                }
            },
                    
            events: {
                "click .save_protocol" : "onClickSaveProtocol",
                "click .delete_protocol" : "onClickDeleteProtocol",
                "click .add_supplement" : "onClickAddSupplement",
            },

            
            onClickSaveProtocol : function(event) {
                var protocol_name_field = this.$el.find('.protocol_name');
                var protocol_name = protocol_name_field.attr('value');
                
                protocol_name_field.removeClass("red_style_border");
               
                this.model.set({ name : protocol_name});
                
                if (!this.model.isValid()) {
                    protocol_name_field.addClass("red_style_border");
                    //console.log(this.model.validationError);
                    return false;
                }
                
                var self = this;
                if (this.model.isNew()) {
                    this.collection.create(this.model, {
                        wait: true,
                        success: function (model, response) {
                            self.close();
                            //console.log(model.toJSON());
                        },
                        error: function (model, response) {
                            alert(response.responseText);
                        }
                    })
                } else {
                    this.model.save(null, {
                        success: function (model, response) {
                            //console.log(self.collection);
                        },
                        error: function (model, response) {
                            alert(response.responseText);
                        }
                    });
                }
             },
             
             onClickDeleteProtocol : function(event) {
                var self = this;
                this.model.destroy({
                    success: function (model) {
                        self.close();
                    },
                    error: function (model, response) {
                        alert(response.responseText);
                    }
                });
             },
             
             close :function() {
                $(this.el).unbind();
		$(this.el).remove();
            },
             

            onClickAddSupplement : function(event) {
                var nutrition_plan_supplement_view = new window.app.Nutrition_plan_supplement_view({collection : this.supplement_collection, model : new window.app.Supplement_model({protocol_id : this.model.get('id')}) }); 
                this.supplements_list_el.append(nutrition_plan_supplement_view.render().el );
            }
        });
        
        
        
        
        
        window.app.Nutrition_plan_supplement_view = Backbone.View.extend({
           
            initialize: function(){
                _.bindAll(this, 'onClickSaveSupplement', 'onInputSupplementName');
            },
            
            render: function(){
                var template = _.template( $("#nutrition_plan_supplement_template").html(), this.model.toJSON());
                this.$el.html(template);
                return this;
            },
                    
            events: {
                "submit form" : "onClickSaveSupplement",
                "click .delete_supplement" : "onClickDeleteSupplement",
                "input .supplement_name" : "onInputSupplementName"
            },

            onClickSaveSupplement : function(event) {
                event.preventDefault();
                var data = Backbone.Syphon.serialize(this);
                
                this.model.set(data);

                //validation
                var supplement_name_field = this.$el.find('.supplement_name');
                supplement_name_field.removeClass("red_style_border");
                var supplement_url_field = this.$el.find('.supplement_url');
                supplement_url_field.removeClass("red_style_border");
                if (!this.model.isValid()) {
                    var validate_error = this.model.validationError;
                    
                    if(validate_error == 'name') {
                        supplement_name_field.addClass("red_style_border");
                        return false;
                    } else if(validate_error == 'url') {
                        supplement_url_field.addClass("red_style_border");
                        return false;
                    } else {
                        alert(this.model.validationError);
                        return false;
                    }
                    
                }
                
                var self = this;
                if (this.model.isNew()) {
                    this.collection.create(this.model, {
                        wait: true,
                        success: function (model, response) {
                            self.close();
                            //console.log(self.collection);
                        },
                        error: function (model, response) {
                            alert(response.responseText);
                        }
                    })
                } else {
                    this.model.save(null, {
                        success: function (model, response) {
                            //console.log(self.collection);
                        },
                        error: function (model, response) {
                            alert(response.responseText);
                        }
                    });
                }

            },

            onClickDeleteSupplement : function(event) {
                var self = this;
                this.model.destroy({
                    success: function (model) {
                        self.close();
                    },
                    error: function (model, response) {
                        alert(response.responseText);
                    }
                });
            },
            
            onInputSupplementName : function(event) {
                var o = $(event.target);
                var typingTimer;
                var search_text = o.val();
                
                var id = this.model.get('id');
                
                $(".select_meal_form").parent().remove();
                this.ingredients_search_results_view = new window.app.Ingredients_search_results_view({model : this.model, 'parent_view_el' : this.$el});
                this.ingredients_search_results_view.render().el;
                
                o.parent().append(this.ingredients_search_results_view.render().el);

                
                clearTimeout(typingTimer);
                var self = this;
                if (search_text) {
                    typingTimer = setTimeout(
                        function() {
                            self.getSearchIngredients(
                                search_text,
                                function(output) {
                                    self.ingredients_search_results_view.$el.find(".results_count").html('Search returned ' + output.count + ' results.');
                                    self.ingredients_search_results_view.$el.find(".supplement_name_results").html(output.html);
                                    self.ingredients_search_results_view.$el.find(".supplement_name_results").find(":odd").css("background-color", "#F0F0EE")
                                })
                            },
                        self.options.doneTypingInterval
                    );
                }
      
            },
            
            getSearchIngredients : function(search_text, handleData) {
                var url = window.protocol_options.fitness_backend_url;
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
                        if(!response.status.success) {
                            alert(response.status.message);
                            return;
                        }
                        handleData(response);
                    },
                    error: function(XMLHttpRequest, textStatus, errorThrown)
                    {
                        alert("error getSearchIngredients");
                    }
                });
            },
            
            close :function() {
                $(this.el).unbind();
                $(this.el).remove();
            },
            
        });
        
        
        window.app.Ingredients_search_results_view = Backbone.View.extend({

            initialize : function () {
                _.bindAll(this, 'onClickOption', 'render');
            },

            render : function (eventName) {
                var template = _.template( $("#ingredients_seaarch_results_template").html(), this.model.toJSON());
                this.$el.html(template);
              
                return this;
            },

            events:{
                "click .supplement_name_results option": "onClickOption"
            },

            onClickOption : function (event) {
                var parent_view_el = this.options.parent_view_el;
  
                var ingredient_id = $(event.target).val();
                
                var self = this;
                this.getIngredientData(
                    ingredient_id, 
                    function(ingredient) {
                        if(!ingredient) return;
                        parent_view_el.find(".supplement_name").val(ingredient.ingredient_name);
                        parent_view_el.find(".supplement_description").val(ingredient.description);
                        self.close();
                    }
                );
            },
            
            getIngredientData : function(id, handleData) {
                var url = window.protocol_options.fitness_backend_url;
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
                        if(!response.status.success) {
                            alert(response.status.message);
                            return;
                        }
                        handleData(response.ingredient);
                    },
                    error: function(XMLHttpRequest, textStatus, errorThrown)
                    {
                        alert("error getIngredientData");
                    }
                }); 
            },
            
            close : function() {
                $(this.el).unbind();
                $(this.el).remove();
            },

        });
        
        //MODELS
        
        window.app.Protocol_model = Backbone.Model.extend({
            urlRoot : window.protocol_options.fitness_backend_url + '&format=text&view=nutrition_plan&task=nutrition_plan_protocol&',
            
            defaults : {
                id : null,
                nutrition_plan_id : window.protocol_options.nutrition_plan_id,
                name : null,
            },
            
            validate: function(attrs, options) {
                if (!attrs.name) {
                  return 'Protocol Name is empty';
                }
            }
        });
        
        window.app.Supplement_model = Backbone.Model.extend({
            urlRoot : window.protocol_options.fitness_backend_url + '&format=text&view=nutrition_plan&task=nutrition_plan_supplement&',
            
            defaults : {
                id : null,
                nutrition_plan_id : window.protocol_options.nutrition_plan_id,
                protocol_id : null,
                name : null,
                description : null,
                comments : null,
                url : null,
            },

            validate: function(attrs, options) {
                if (!attrs.nutrition_plan_id) {
                  return 'Nurtition Plan Id is not valid';
                }
                if (!attrs.protocol_id) {
                  return 'Protocol Id is not valid';
                }
                if (!attrs.name) {
                  return 'name';
                }

                var regexp = /(ftp|http|https):\/\/(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?/;
                if (attrs.url && !regexp.test(attrs.url) ) {
                  return 'url';
                }
            }
        });
        
        
        // COLLECTIONS
        window.app.Protocols_collection = Backbone.Collection.extend({
            url : window.protocol_options.fitness_backend_url + '&format=text&view=nutrition_plan&task=nutrition_plan_protocol&',
            model: window.app.Protocol_model
        });

        
        
        window.app.Supplements_collection = Backbone.Collection.extend({
            url : window.protocol_options.fitness_backend_url + '&format=text&view=nutrition_plan&task=nutrition_plan_supplement&',
            model: window.app.Supplement_model
        });

        
        //CONTROLLER
        
        var Controller = Backbone.Router.extend({
            routes: {
                "": "overview", 
                "!/": "overview", 
                "!/overview": "overview", 
                "!/targets": "targets", 
                "!/macronutrients": "macronutrients", 
                "!/supplements": "supplements", 
                "!/nutrition_guide": "nutrition_guide", 
                "!/information": "information", 
                "!/archive": "archive", 
                "!/close": "close", 
                "!/add_supplement_protocol": "add_supplement_protocol",
            },
            
            initialize:function () {
                $('#protocols_wrapper').html(new window.app.Protocols_wrapper().render().el);
            },

            overview: function () {
                 this.common_actions();
                 $("#overview_wrapper").show();
                 $("#overview_link").addClass("active_link");
                
            },

            targets: function () {
                 this.common_actions();
                 $("#targets_wrapper").show();
                 $("#targets_link").addClass("active_link");
                 
                 window.app.nutrition_plan_model.connect_targets_comments();
                 
            },

            macronutrients: function () {
                 this.common_actions();
                 $("#macronutrients_wrapper").show();
                 $("#macronutrients_link").addClass("active_link");
                 window.app.nutrition_plan_model.connect_macronutrients_comments();
            },
            
            supplements: function () {
                $("#protocol_list").empty();
                 this.common_actions();
                 $("#supplements_wrapper").show();
                 $("#supplements_link").addClass("active_link");
                 
                 window.app.protocols = new window.app.Protocols_collection(); 
                 
                 window.app.protocols.fetch({data: {nutrition_plan_id : window.protocol_options.nutrition_plan_id}});
                 
                 window.app.nutrition_plan_protocols_view = new window.app.Nutrition_plan_protocols_view({el : $("#protocol_list"), collection : window.app.protocols}); 
                
                 //window.app.nutrition_plan_protocols_view.render().el;

            },
                    
            nutrition_guide: function () {
                 this.common_actions();
                 $("#nutrition_guide_wrapper").show();
                 $("#nutrition_guide_link").addClass("active_link");
            },
                    
            information: function () {
                 this.common_actions();
                 $("#information_wrapper").show();
                 $("#information_link").addClass("active_link");
            },
                    
            archive: function () {
                 this.common_actions();
                 $("#archive_wrapper").show();
                 $("#archive_focus_link").addClass("active_link");
            },
                    
            close: function() {
                 $("#close_tab").hide();
                 this.archive();
            },
            
            common_actions : function() {
                $(".block, #close_tab").hide();
                $(".plan_menu_link").removeClass("active_link")
            },
            
            add_supplement_protocol : function() {
                this.nutrition_plan_protocol_view = new window.app.Nutrition_plan_protocol_view({model : new window.app.Protocol_model(), collection : window.app.protocols}); 
                $("#protocol_list").append(this.nutrition_plan_protocol_view.render().el );
            }
                    
            
        });

        window.app.controller = new Controller(); 

        Backbone.history.start();  
        
        
        
        
        
        
        
        
        
        
        //
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        




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
                                    var force_active = $("#jform_override_dates0").is(":checked");
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
                        var force_active = $("#jform_override_dates0").is(":checked");
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

    
    })($js);
    
    
    
    
    
    
    
    
    
    
    
    
    
    
</script>