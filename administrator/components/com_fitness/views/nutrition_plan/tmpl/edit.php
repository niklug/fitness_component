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
                <?php echo $this->form->getInput('nutrition_focus'); ?>
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
    var options = {
        // main options
            'fitness_administration_url' : '<?php echo JURI::root();?>administrator/index.php?option=com_fitness&tmpl=component&<?php echo JSession::getFormToken(); ?>=1',
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
            'empty_html_data' : {'calories' : "", 'water' : "", 'protein' : "", 'fats' : "", 'carbs' : ""}
    
    
        };


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



<script type="text/javascript">
    

    /*
    (function($) {
        
        // connect helper class
        var helper_options = {
            'ajax_call_url' : '<?php echo JURI::root();?>administrator/index.php?option=com_fitness&tmpl=component&<?php echo JSession::getFormToken(); ?>=1',
            'base_url' : '<?php echo JURI::root();?>',
        }
        window.fitness_helper = $.fitness_helper(helper_options);
        // END  OPTIONS  


        
        
        
        //BACKBONE MENU LOGIC
        window.app = window.app || {};
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
                "click #diary_guide_link" : "onClickNutrition_guide_old",
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
            
            onClickNutrition_guide_old : function() {
                window.app.controller.navigate("!/diary_guide", true);
            },
            
            onClickNutrition_guide: function() {
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
        
        window.app.protocol_options = {

            'fitness_backend_url' : '<?php echo JURI::root();?>administrator/index.php?option=com_fitness&tmpl=component&<?php echo JSession::getFormToken(); ?>=1',
            
            'nutrition_plan_id' : '<?php echo  $this->item->id ?>',
    
            'user_name' : '<?php echo JFactory::getUser()->name;?>',
                   
            'protocol_comments_db_table' : '#__fitness_nutrition_plan_supplements_comments'
        };
        
        $.NutritionPlanSupplements();
        //
        
        
        // NUTRITION GUIDE
         window.app.example_day_options = {

            'fitness_backend_url' : '<?php echo JURI::root();?>administrator/index.php?option=com_fitness&tmpl=component&<?php echo JSession::getFormToken(); ?>=1',
            
            'nutrition_plan_id' : '<?php echo  $this->item->id ?>',
    
            'user_name' : '<?php echo JFactory::getUser()->name;?>',
                   
            'example_day_meal_comments_db_table' : '#__fitness_nutrition_plan_example_day_meal_comments'
        };
        
        
        
        
        window.app.Nutrition_guide_menu = Backbone.View.extend({
            el : $("#nutrition_guide_wrapper"),

            template : _.template($('#nutrition_guide_menu_template').html()),

            initialize:function () {
                this.render();
            },

            render:function () {
                $(this.el).html(this.template());
                return this;
            },

            events:{
                "click .example_day_link": "onChooseDay"
            },

            onChooseDay:function (event) {
                $(".example_day_link").removeClass("active");
                var day = $(event.target).attr('data-id');
                $(event.target).addClass("active");
                window.app.controller.navigate("!/example_day/" + day, true);
            }

        });
        
        
        window.app.Example_day_view = Backbone.View.extend({
            
            initialize:function () {

            },

            render: function(){
                var template = _.template( $("#nutrition_plan_example_day_template").html());
                this.$el.html(template);
                
                var self = this;
                
		this.mealListItemViews = {};

		this.collection.on("add", function(meal) {
                    window.app.nutrition_plan_example_day_meal_view = new window.app.Nutrition_plan_example_day_meal_view({collection : this,  model : meal}); 
                    self.$el.find("#example_day_meal_list").append( window.app.nutrition_plan_example_day_meal_view.render().el );
                    self.mealListItemViews[ meal.cid ] = window.app.nutrition_plan_example_day_meal_view;
		});
		
		this.collection.on("remove", function(meal, options) {
                    self.mealListItemViews[ meal.cid ].close();
                    delete self.mealListItemViews[ meal.cid ];
		});

                return this;
            },

            events:{
                "click #add_meal": "add_meal"
            },

            add_meal:function () {
                var example_day_id = this.options.example_day_id;
                window.app.controller.navigate("!/example_day/" + example_day_id);
                window.app.controller.navigate("!/add_example_day_meal/" + example_day_id, true);
            }

        });
        
        
        window.app.Nutrition_plan_example_day_meal_view = Backbone.View.extend({
           
            initialize: function(){
                _.bindAll(this, 'onClickSaveMeal', 'onClickDeleteMeal','close', 'render', 'addRecipe');
                this.model.on("destroy", this.close, this);
                
                this.recipes_collection = new window.app.Nutrition_guide_recipes_collection();
                
                this.recipes_collection.bind("add", this.addRecipe, this);
                
                var self = this;
                this.recipes_collection.fetch({
                    data: {
                        meal_id : self.model.get('id')
                    },
                    wait : true,
                    success : function(collection, response) {
                        //console.log(collection);
                    },
                    error: function (collection, response) {
                        alert(response.responseText);
                    }
                });
               
            },
            
            render: function(){
                var template = _.template( $("#nutrition_plan_example_day_item_template").html(), this.model.toJSON());
                this.$el.html(template);
                this.$el.find('.meal_time').timepicker({ 'timeFormat': 'H:i', 'step': 15 });
                
                this.connectComments();
                
                return this;
            },
            
            connectComments : function() {
                var meal_id = this.model.get('id');
                var comment_options = {
                    'item_id' : window.app.example_day_options.nutrition_plan_id,
                    'fitness_administration_url' : window.app.example_day_options.fitness_backend_url,
                    'comment_obj' : {'user_name' : window.app.example_day_options.user_name, 'created' : "", 'comment' : ""},
                    'db_table' : window.app.example_day_options.example_day_meal_comments_db_table,
                    'read_only' : false,
                }
                var comments = $.comments(comment_options, comment_options.item_id, meal_id).run();
                this.$el.find(".comments_wrapper").html(comments);
            },
           
            addRecipe : function(model) {
                this.item_view = new window.app.Nutrition_guide_recipe_view({collection : this.recipes_collection, model : model}); 
                this.$el.find(".meal_recipes").append( this.item_view.render().el );
            },
     
            events: {
                "click .save_example_day_meal" : "onClickSaveMeal",
                "click .delete_example_day_meal" : "onClickDeleteMeal",
                "click .add_meal_recipe" : "onClickAddMealRecipe",
            },

            onClickSaveMeal : function(event) {
                event.preventDefault();
                var data = Backbone.Syphon.serialize(this);
                
                this.model.set(data);

                //validation
                var meal_description_field = this.$el.find('.meal_description');
                meal_description_field.removeClass("red_style_border");
                var meal_time_field = this.$el.find('.meal_time');
                meal_time_field.removeClass("red_style_border");
                if (!this.model.isValid()) {
                    var validate_error = this.model.validationError;
                    
                    if(validate_error == 'description') {
                        meal_description_field.addClass("red_style_border");
                        return false;
                    } else if(validate_error == 'meal_time') {
                        meal_time_field.addClass("red_style_border");
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
             
             onClickDeleteMeal : function(event) {
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
            
            onClickAddMealRecipe : function() {
                window.app.controller.navigate("!/add_meal_recipe/" + this.model.get('id'), true);
            },
             
             close :function() {
                $(this.el).unbind();
		$(this.el).remove();
            },
 
        });
        
        
        window.app.Example_day_add_recipe_view = Backbone.View.extend({
            
            initialize : function() {
                _.bindAll(this, 'render',  'addRecipeItem', 'clearRecipeItems');
                this.collection.bind("reset", this.clearRecipeItems, this);
                this.collection.bind("add", this.addRecipeItem, this);
            },

            render:function () {
                var template = _.template( $("#example_day_add_recipe_template").html());
                this.$el.html(template);
                this.container_el = this.$el.find(".example_day_meal_recipes_list");
                
                this.connectFilter();
                
                this.connectRecipeVariationsFilter();
                
                return this;
            },

            
            addRecipeItem : function(model) {
                var meal_id = this.model.get('id');
                model.set({'meal_id' : meal_id});
                this.item_view = new window.app.Nutrition_guide_add_recipe_item_view({collection : this.collection, model : model}); 
                this.container_el.append( this.item_view.render().el );

                window.app.pagination_app_model.set({'items_total' : model.get('items_total')});
            },
            
            clearRecipeItems : function() {
                this.container_el.empty();
            },

            events:{
                "click .cancel_add_recipe": "onCancelViewRecipe"
            },

            onCancelViewRecipe :function (event) {
                window.app.controller.navigate("!/example_day/" + this.model.get('example_day_id'), true);
            },
            
            connectFilter : function() {
                this.filter_container = this.$el.find("#recipe_database_filter_wrapper");
                
                this.recipe_types_collection = new window.app.Recipe_types_collection();
                
                var self = this;
                
                this.recipe_types_collection.fetch({
                    wait : true,
                    success : function(collection, response) {
                        self.filter_container.html(new window.app.Filter_view({model : response}).render().el);
                    },
                    error: function (collection, response) {
                        alert(response.responseText);
                    }
                })
            },
            
            connectRecipeVariationsFilter : function() {
                this.recipe_variations_filter_container = this.$el.find("#recipe_variations_filter_wrapper");
                
                this.recipe_variations_collection = new window.app.Recipe_variations_collection();
                
                var self = this;
                
                this.recipe_variations_collection.fetch({
                    wait : true,
                    success : function(collection, response) {
                        self.recipe_variations_filter_container.html(new window.app.Recipe_variations_filter_view({collection : collection}).render().el);
                    },
                    error: function (collection, response) {
                        alert(response.responseText);
                    }
                })
            }

        });
        
        
        window.app.Nutrition_guide_add_recipe_item_view = Backbone.View.extend({
            initialize : function() {
                _.bindAll(this, 'render',  'onClickViewRecipe', 'onClickEnterServes');
                this.original_recipe_model = new window.app.Nutrition_guide_add_original_recipe_model();
            },
            render:function () {
                var template = _.template( $("#nutrition_guide_add_recipe_item_template").html(), this.model.toJSON());
                this.$el.html(template);
                return this;
            },
            
            events: {
                "click .view_add_recipe" : "onClickViewRecipe",
                "click .enter_number_serves" : "onClickEnterServes"
            },
            
            onClickViewRecipe : function() {
                var recipe_id = this.model.get('id');
                
                this.recipe_ingredients_collection = new window.app.Nutrition_guide_recipe_ingredients_collection();
                
                this.container_el = this.$el.find(".recipe_details");
                var self = this;
                this.recipe_ingredients_collection.fetch({
                    data: {
                        id : recipe_id
                    },
                    success : function (collection, response) {
                        self.container_el.html( new window.app.Nutrition_guide_add_recipe_details_view({model : response}).render().el );
                        self.$el.find(".view_add_recipe").hide();
                        self.$el.find(".number_serves_wrapper, .recipe_details").show();
                    },
                    error: function (model, response) {
                        alert(response.responseText);
                    }
                });
            },
            
            onClickEnterServes : function() {
                var number_serves = parseInt(this.$el.find(".number_serves").val());
                this.$el.find(".number_serves").removeClass("red_style_border");
                if(!number_serves) {
                    this.$el.find(".number_serves").addClass("red_style_border");
                    return false;
                }
                
                this.original_recipe_model.set(this.model.toJSON());
                
                var original_recipe_id = this.model.get('id');
                
                this.original_recipe_model.set({'number_serves_new' : number_serves, 'original_recipe_id' : original_recipe_id});
                
                this.original_recipe_model.unset('id');
                
                this.$el.find(".number_serves_wrapper, .recipe_details").hide();
                this.$el.find(".view_add_recipe").show();
                
                
                this.original_recipe_model.save(null, {
                    success: function (model, response) {
                        console.log(response);
                    },
                    error: function (model, response) {
                        alert(response.responseText);
                    }
                });
            }
        });
        
        
        window.app.Nutrition_guide_recipe_view = Backbone.View.extend({
            render:function () {
                var template = _.template( $("#nutrition_guide_recipe_template").html(), this.model.toJSON());
                this.$el.html(template);
                return this;
            },
            
            events: {
                "click .save_recipe" : "onClickSaveRecipe",
                "click .delete_recipe" : "onClickDeleteRecipe"
            },
            
            onClickSaveRecipe : function() {
                var recipe_comments = this.$el.find('.recipe_comments').val();
                this.model.set({'description' : recipe_comments});
                 this.model.save(null, {
                    error: function (model, response) {
                        alert(response.responseText);
                    }
                });
            }, 
            
            onClickDeleteRecipe : function() {
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
        });
        
        
        window.app.Filter_view = Backbone.View.extend({

            render : function(){
                var data = {'items' : this.model}
                var template = _.template($("#recipe_database_filter_template").html(),data);
                this.$el.html(template);
                return this;
            },
            
            events: {
                "change #categories_filter" : "onFilterSelect",
            },

            onFilterSelect : function(event){
                var ids = $(event.target).find(':selected').map(function(){ return this.value }).get().join(",");
                //console.log(ids);
                window.app.pagination_app_model.reset();
                window.app.nutrition_guide_add_recipe_collection.reset();
                window.app.get_recipe_params_model.set({'filter_options' : ids});
            }
        });
        
        window.app.Nutrition_guide_add_recipe_details_view = Backbone.View.extend({

            render : function(){
                var template = _.template($("#nutrition_guide_add_recipe_details_template").html(), this.model);
                this.$el.html(template);
                return this;
            },
        });
        
        
        window.app.Recipe_variations_filter_view = Backbone.View.extend({

            render : function(){
                var template = _.template($("#recipe_variations_filter_template").html());
                this.$el.html(template);
                this.populateSelect();
                return this;
            },
            
            populateSelect : function() {
                
                var self = this;
                this.collection.on("add", function(model) {
                    self.$el.find("#recipe_variations_filter").append('<option value="' + model.get('id') + '">' + model.get('name') + '</option>');
		});
                
                _.each(this.collection.models, function (model) { 
                    self.$el.find("#recipe_variations_filter").append('<option value="' + model.get('id') + '">' + model.get('name') + '</option>');
                }, this);
  
            },
            
            events: {
                "change #recipe_variations_filter" : "onFilterSelect",
            },
            
        
            onFilterSelect : function(event){
                var ids = $(event.target).find(':selected').map(function(){ return this.value }).get().join(",");
                window.app.pagination_app_model.reset();
                window.app.nutrition_guide_add_recipe_collection.reset();
                window.app.get_recipe_params_model.set({'recipe_variations_filter_options' : ids});
                //console.log(ids);
            },
            
            close :function() {
                $(this.el).unbind();
		$(this.el).remove();
            }
        });
        
        
        
        //MODELS
        
        window.app.Example_day_meal_model = Backbone.Model.extend({
            urlRoot : window.app.example_day_options.fitness_backend_url + '&format=text&view=nutrition_plan&task=nutrition_plan_example_day_meal&id=',
            
            defaults : {
                id : null,
                description : null,
                nutrition_plan_id : window.app.example_day_options.nutrition_plan_id,
                example_day_id : null,
                meal_time : null,
            },
            
            validate: function(attrs, options) {
                if (!attrs.description) {
                  return 'description';
                }
                if (!attrs.nutrition_plan_id) {
                  return 'Nurtition Plan Id is not valid';
                }
                if (!attrs.example_day_id) {
                  return 'error: No example_day_id';
                }
                var result = false, m;
                var re = /^\s*([01]?\d|2[0-3]):?([0-5]\d)\s*$/;
                if ((m = attrs.meal_time.match(re))) {
                    result = (m[1].length == 2 ? "" : "0") + m[1] + ":" + m[2];
                }
                if (!attrs.meal_time || !result) {
                  return 'meal_time';
                }
            }
        });
        
        
        
        window.app.Get_recipe_params_model = Backbone.Model.extend({
            defaults : {
                sort_by : 'recipe_name',
                order_dirrection : 'ASC',
                page : localStorage.getItem('currentPage') || 1,
                limit : localStorage.getItem('items_number') || 10,
                state : 1,
                filter_options : '',
                recipe_variations_filter_options : '',
                current_page : 'recipe_database'
            }
        });
        
     
        
        window.app.Nutrition_guide_add_recipe_model = Backbone.Model.extend({
            urlRoot : window.app.example_day_options.fitness_backend_url + '&format=text&view=nutrition_plan&task=nutrition_guide_add_recipe_list&id=',
        });
        
        window.app.Nutrition_guide_add_original_recipe_model = Backbone.Model.extend({
            urlRoot : window.app.example_day_options.fitness_backend_url + '&format=text&view=nutrition_plan&task=nutrition_guide_recipes&id=',
        });

        
        
        
        // COLLECTIONS
        window.app.Example_day_meals_collection = Backbone.Collection.extend({
            url : window.app.example_day_options.fitness_backend_url + '&format=text&view=nutrition_plan&task=nutrition_plan_example_day_meal&id=',
            model: window.app.Example_day_meal_model
        });

        
        window.app.Nutrition_guide_add_recipe_collection = Backbone.Collection.extend({
            url : window.app.example_day_options.fitness_backend_url + '&format=text&view=nutrition_plan&task=nutrition_guide_add_recipe_list&id=',
            model: window.app.Nutrition_guide_add_recipe_model
        });
        
        window.app.Recipe_types_collection = Backbone.Collection.extend({
            url : window.app.example_day_options.fitness_backend_url + '&format=text&view=nutrition_plan&task=recipe_types&'
        });
        
        window.app.Nutrition_guide_recipe_ingredients_collection = Backbone.Collection.extend({
            url : window.app.example_day_options.fitness_backend_url + '&format=text&view=nutrition_plan&task=get_recipe&id='
        });
        
        window.app.Nutrition_guide_recipes_collection = Backbone.Collection.extend({
            url : window.app.example_day_options.fitness_backend_url + '&format=text&view=nutrition_plan&task=nutrition_guide_recipes&id='
        });
        
        window.app.Recipe_variations_collection = Backbone.Collection.extend({
            url : window.app.example_day_options.fitness_backend_url + '&format=text&view=recipe_database&task=recipe_variations&'
        });
        
       
        //CONTROLLER
        
        var Controller = Backbone.Router.extend({
            routes: {
                "": "overview", 
                "!/": "overview", 
                "!/overview": "overview", 
                "!/targets": "targets", 
                "!/macronutrients": "macronutrients", 
                "!/diary_guide": "diary_guide", 
                "!/information": "information", 
                "!/archive": "archive", 
                "!/close": "close", 
                
                "!/nutrition_guide": "nutrition_guide", 
                "!/example_day/:id": "example_day", 
                "!/add_example_day_meal/:id": "add_example_day_meal", 
                
                "!/add_meal_recipe/:meal_id": "add_meal_recipe", 

            },
            
            initialize : function() {
                window.app.get_recipe_params_model = new window.app.Get_recipe_params_model();
                window.app.nutrition_guide_add_recipe_collection = new window.app.Nutrition_guide_add_recipe_collection(); 
                
                window.app.get_recipe_params_model.bind("change", this.get_database_recipes, this);
            },
            
            get_database_recipes : function() {
                //console.log(window.app.get_recipe_params_model.toJSON());
                window.app.nutrition_guide_add_recipe_collection.fetch({
                    data : window.app.get_recipe_params_model.toJSON(),
                    error: function (model, response) {
                        alert(response.responseText);
                    }
                });  
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
            
                    
            diary_guide: function () {
                 this.common_actions();
                 $("#diary_guide_wrapper").show();
                 $("#diary_guide_link").addClass("active_link");
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
     
            nutrition_guide : function () {
                 this.common_actions();
                 $("#nutrition_guide_wrapper").show();
                 $("#nutrition_guide_link").addClass("active_link");
                 
                 new window.app.Nutrition_guide_menu();
                 
                 this.example_day(1);
                 $(".example_day_link").first().addClass("active");
            },
            
            example_day : function(example_day_id) {
               
                window.app.example_day_meal_collection = new window.app.Example_day_meals_collection(); 
                 
                window.app.example_day_meal_collection.fetch({data: {
                        nutrition_plan_id : window.app.example_day_options.nutrition_plan_id,
                        example_day_id : example_day_id
                    },
                    error: function (model, response) {
                        alert(response.responseText);
                    }
                });
                
                $('#example_day_wrapper').html(new window.app.Example_day_view({collection : window.app.example_day_meal_collection, 'example_day_id' : example_day_id}).render().el);
            },
            
            add_example_day_meal : function(example_day_id) {
                this.nutrition_plan_example_day_meal_view = new window.app.Nutrition_plan_example_day_meal_view({model : new window.app.Example_day_meal_model({'example_day_id' : example_day_id}), collection : window.app.example_day_meal_collection}); 
                $("#example_day_meal_list").append(this.nutrition_plan_example_day_meal_view.render().el );
            },
            
            add_meal_recipe : function(meal_id) {
                
                window.app.nutrition_guide_add_recipe_collection.reset();
                this.get_database_recipes();
                
                var meal_model = window.app.example_day_meal_collection.get({id : meal_id});
                
                $('#example_day_wrapper').html(new window.app.Example_day_add_recipe_view({collection : window.app.nutrition_guide_add_recipe_collection, model : meal_model}).render().el);

                window.app.pagination_app_model = $.backbone_pagination({});
                
                window.app.pagination_app_model.bind("change:currentPage", this.set_recipes_model, this);
                window.app.pagination_app_model.bind("change:items_number", this.set_recipes_model, this);
            },
            
            set_recipes_model : function() {
                window.app.nutrition_guide_add_recipe_collection.reset();
                window.app.get_recipe_params_model.set({"page" :  window.app.pagination_app_model.get('currentPage') || 1, "limit" : localStorage.getItem('items_number') || 10});
    
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
                                    var force_active = $("#jform_force_active0").is(":checked");
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
    
    */
    
    
    
    
    
    
    
    
    
    
    
    
</script>