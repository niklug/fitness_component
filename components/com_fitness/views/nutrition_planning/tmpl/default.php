<?php
$user = &JFactory::getUser();

$trainer_id =  $this->active_plan_data->trainer_id;

$nutrition_plan_id = $this->active_plan_data->id;

$heavy_target = $this->nutrition_diaryform_model->getNutritionTarget($nutrition_plan_id, 'heavy');

$light_target = $this->nutrition_diaryform_model->getNutritionTarget($nutrition_plan_id, 'light');

$rest_target = $this->nutrition_diaryform_model->getNutritionTarget($nutrition_plan_id, 'rest');


?>

<div style="opacity: 1;" class="fitness_wrapper">
    <h2>NUTRITION PLAN</h2>
    
    <div id="plan_menu"></div>
    
    <br/>
    
    <!-- OVERVIEW -->
    <div id="overview_wrapper" class="block">
        <div id="nutrition_focus_wrapper"></div>
        <div class="fitness_block_wrapper" style="min-height:150px;margin: 2px;">
            <div  style="width:400px; float: left;">
                <h3>MY GOALS & TRAINING PERIODS</h3>
            </div>
            <div  style="width:500px; float:right; text-align: right;margin-top: 4px;margin-right: 20px;">
                <a id="whole" href="javascript:void(0)">[All Goals]</a>
                <a  id="by_year_previous" href="javascript:void(0)">[Previous Year]</a>
                <a  id="by_year" href="javascript:void(0)">[Current Year]</a>
                <a  id="by_year_next" href="javascript:void(0)">[Next Year]</a>
                <a  id="by_month" href="javascript:void(0)">[Current Month]</a>
            </div>
            <div class="clr"></div>
            <hr class="orange_line">
            <div class="internal_wrapper">
                <table>
                    <tr>
                        <td>
                            <div class="graph-container" style="width:780px;">
                                <div id="placeholder" class="graph-placeholder"></div>
                            </div>
                        </td>
                        <td>
                            <fieldset style="width:140px !important;">
                                <legend class="grey_title">Training Period Keys</legend>
                                <?php echo $this->goals_periods_model->getTrainingPeriods();?>
                            </fieldset>
                        </td>
                    </tr>
                </table>
            </div>

        </div>

    
     </div>
    
    <!-- TARGETS -->
    <div id="targets_wrapper" class="block">
        <div id="targets_container" class="fitness_block_wrapper" style="min-height: 300px;">
        </div>
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
        <div id="macronutrients_container"></div>

        <div class="clr"></div>
        <br/>
        <div id="macronutrients_comments_wrapper" style="width:100%"></div>
        <div class="clr"></div>
        <br/>
        <input id="add_comment_1" class="" type="button" value="Add Comment" >
        <div class="clr"></div>
        
    </div>
    
    <!-- SUPPLEMENTS -->
    <div id="supplements_wrapper" class="block">

    </div>

    <!-- NUTRITION GUIDE -->
    
    <div id="nutrition_guide_wrapper" class="block">

        <div id="nutrition_guide_header" ></div>

        <div id="nutrition_guide_container" class="fitness_block_wrapper"></div>
        
    </div>
    
    <!-- INFORMATION -->
    <div id="information_wrapper" class="block">
        
    </div>
    
    <!-- ARCHIVE -->
    <div id="archive_wrapper" class="block">

    </div>
    
    
    <div id="close_wrapper" class="block">
        
    </div>
 
</div>


<script type="text/javascript">
    
    (function($) {
        
        window.app = {};
        Backbone.emulateHTTP = true ;
        Backbone.emulateJSON = true;
        
        var options = {
            'fitness_frontend_url' : '<?php echo JURI::root();?>index.php?option=com_fitness&tmpl=component&<?php echo JSession::getFormToken(); ?>=1',
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
            
            'client_id' : '<?php echo JFactory::getUser()->id;?>',
            
            'user_name' : '<?php echo JFactory::getUser()->name;?>',
            
            'item_id' : '<?php echo  $nutrition_plan_id?>'
        };
        
        //SUPPLEMENTS options
        window.app.protocol_options = {

            'fitness_frontend_url' : '<?php echo JURI::root();?>index.php?option=com_fitness&tmpl=component&<?php echo JSession::getFormToken(); ?>=1',
            
            'nutrition_plan_id' : '<?php echo  $nutrition_plan_id ?>',
    
            'user_name' : '<?php echo JFactory::getUser()->name;?>',
                   
            'protocol_comments_db_table' : '#__fitness_nutrition_plan_supplements_comments'
        };
        $.NutritionPlanSupplements();
        
        // END SUPPLEMENTS options
        
        // NUTRITION GUIDE options
        // connect helper class
        var helper_options = {
            'ajax_call_url' : '<?php echo JURI::root();?>index.php?option=com_fitness&tmpl=component&<?php echo JSession::getFormToken(); ?>=1',
            'base_url' : '<?php echo JURI::root();?>',
            'user_name' : '<?php echo JFactory::getUser()->name;?>',
            'client_id' : '<?php echo JFactory::getUser()->id;?>',
        }
        window.fitness_helper = $.fitness_helper(helper_options);
        
        window.app.example_day_options = {

            'fitness_frontend_url' : '<?php echo JURI::root();?>index.php?option=com_fitness&tmpl=component&<?php echo JSession::getFormToken(); ?>=1',
            
            'base_url' : '<?php echo JURI::root();?>',
            
            'nutrition_plan_id' : '<?php echo  $nutrition_plan_id ?>',
    
            'user_name' : '<?php echo JFactory::getUser()->name;?>',
                   
            'example_day_meal_comments_db_table' : '#__fitness_nutrition_plan_example_day_meal_comments'
        };
        
        $.NutritionGuide();
   
        // END NUTRITION GUIDE options


        
        //MODELS
        window.app.Nutrition_plan_overview_model = Backbone.Model.extend({
            urlRoot : options.fitness_frontend_url + '&format=text&view=goals_periods&task=nutrition_plan&',
            defaults : {
                id : options.item_id,
                client_id : options.client_id
            },
            
        });
        
        window.app.Nutrition_plan_target_model = Backbone.Model.extend({
            defaults : {
                id : options.item_id,
            },
            
        });
        
        // COLLECTIONS
        window.app.Nutrition_plans_collection = Backbone.Collection.extend({
            url : options.fitness_frontend_url + '&format=text&view=goals_periods&task=nutrition_plan&',
            model: window.app.Nutrition_plan_overview_model 
        });
        
        window.app.Nutrition_plan_target_collection = Backbone.Collection.extend({
            url : options.fitness_frontend_url + '&format=text&view=goals_periods&task=nutrition_targets&',
            model: window.app.Nutrition_plan_target_model 
        });
        ////
        window.app.Nutrition_focus_view = Backbone.View.extend({
            render: function(){
                var template = _.template( $("#nutrition_plan_template").html(), this.model.toJSON());
                this.$el.html(template);
                return this;
            },
            
        });
        
        window.app.Archive_list_view = Backbone.View.extend({

            render: function(){
                var template = _.template( $("#nutrition_plan_history_template").html(), {'items' : this.collection.toJSON()});
                this.$el.html(template);
                return this;
            },
            
            events: {
                "click .preview" : "viewPlan",
            },
            
            viewPlan : function(event) {
                var id = $(event.target).attr('data-id');
                window.app.nutrition_plan_overview_model.set({id : id});
                $("#close_tab").show();
                window.app.controller.navigate("!/overview", true);
            },
        });
        

        
        window.app.Target_block_view = Backbone.View.extend({
            initialize: function(){
                _.bindAll(this, 'setTargetData', 'render');
                this.model.on("destroy", this.close, this);
            },
            
            render: function(){
                var template = _.template( $("#target_block_template").html(), this.model.toJSON());
                this.$el.html(template);
   
                setTimeout(this.setTargetData,100);
                  
                return this;
            },
            
            setTargetData : function() {
                var model = this.model;
                var activity_level;
                var type = model.get('type');
                
                var tite_container = this.$el.find(".title");
                
                if(type == 'heavy') {
                    activity_level = '1';
                    tite_container.text('Heavy Training Day');
                    tite_container.css('color', '#AD0C0C');
                }
                if(type == 'light') {
                    activity_level = '2';
                    tite_container.text('Light Training Day');
                    tite_container.css('color', '#0D7F22');
                }
                if(type == 'rest') {
                    activity_level = '3';
                    tite_container.text('Recovery / Rest Day');
                    tite_container.css('color', '#223FAA');
                }

                var data = [
                    {label: "Protein:", data: [[1, model.get('protein')]]},
                    {label: "Carbs:", data: [[1, model.get('carbs')]]},
                    {label: "Fat:", data: [[1, model.get('fats')]]}
                ];

                var container = this.$el.find(".placeholder_pie");
      
                var targets_pie = $.drawPie(data, container, {'no_percent_label' : false});

                targets_pie.draw(); 
            },
            
            
        });
        
        window.app.Macronutrients_view = Backbone.View.extend({

            render: function(){
                var template = _.template( $("#macronutrients_template").html(), this.model.toJSON());
                this.$el.html(template);
                return this;
            },
            
            events: {
                "click #pdf_button_macros" : "onClickPdf",
                "click #email_button_macros" : "onClickEmail",
            },
            
            onClickPdf : function(event) {
                var id = $(event.target).attr('data-id');
                var htmlPage = window.fitness_helper.base_url + 'index.php?option=com_multicalendar&view=pdf&tpml=component&layout=email_pdf_nutrition_plan_macros&id=' + id + '&client_id=' + options.client_id;
                window.fitness_helper.printPage(htmlPage);
            },
            
            onClickEmail : function(event) {
                var data = {};
                data.url = options.fitness_frontend_url;
                data.view = '';
                data.task = 'ajax_email';
                data.table = '';

                data.id = $(event.target).attr('data-id');
                data.view = 'NutritionPlan';
                data.method = 'email_pdf_nutrition_plan_macros';
                window.fitness_helper.sendEmail(data);
            },
        });
        
        window.app.Nutrition_plan_information_view = Backbone.View.extend({

            render: function(){
                var template = _.template( $("#nutrition_plan_information_template").html(), this.model.toJSON());
                this.$el.html(template);
                return this;
            },

        });
        
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
        
        //CONTROLLER
        
        var Controller = Backbone.Router.extend({
        
            initialize: function(){
                window.app.nutrition_plan_overview_model = new window.app.Nutrition_plan_overview_model({'id' : options.item_id});
                
                window.app.nutrition_plans_collection = new window.app.Nutrition_plans_collection();
                
                window.app.nutrition_plan_target_collection = new window.app.Nutrition_plan_target_collection({'id' : options.item_id});
            },
        
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
                "!/example_day/:id": "example_day", 
            },

            overview: function () {
                 this.no_active_plan_action();
                 this.common_actions();
                 $("#overview_wrapper").show();
                 $("#overview_link").addClass("active_link");
                 // connect Graph from Goals frontend logic
                 $.goals_frontend(options);
                 var id = window.app.nutrition_plan_overview_model.get('id');
                 window.app.nutrition_plan_overview_model.fetch({
                    data: {id : id},
                    wait : true,
                    success : function(model, response) {
                        window.app.nutrition_focus_view = new window.app.Nutrition_focus_view({model : model});
                        
                        $("#nutrition_focus_wrapper").html(window.app.nutrition_focus_view.render().el);
                    },
                    error: function (collection, response) {
                        alert(response.responseText);
                    }
                 });
                 
            },

            targets: function () {
                 this.no_active_plan_action();
                 this.common_actions();
                 $("#targets_wrapper").show();
                 $("#targets_link").addClass("active_link");
                 var id = window.app.nutrition_plan_overview_model.get('id');
                 window.app.nutrition_plan_target_collection.fetch({
                    data: {id : id, client_id : options.client_id},
                    wait : true,
                    success : function(collection, response) {
                        $("#targets_container").empty();
                        _.each(collection.models, function(model) {
                            window.app.target_block_view = new window.app.Target_block_view({model : model});
                            $("#targets_container").append(window.app.target_block_view.render().el);
                        });
                    },
                    error: function (collection, response) {
                        alert(response.responseText);
                    }
                 });
                 // connect comments
                 var comment_options = {
                    'item_id' :  id,
                    'fitness_administration_url' : window.fitness_helper.base_url,
                    'comment_obj' : {'user_name' : window.fitness_helper.user_name, 'created' : "", 'comment' : ""},
                    'db_table' :  '#__fitness_nutrition_plan_targets_comments',
                    'read_only' : true,
                    'anable_comment_email' : false
                }
                var comments = $.comments(comment_options, comment_options.item_id, 0);

                var comments_html = comments.run();
                $("#targets_comments_wrapper").html(comments_html);
            },
            
            macronutrients: function () {
                 this.no_active_plan_action();
                 this.common_actions();
                 $("#macronutrients_wrapper").show();
                 $("#macronutrients_link").addClass("active_link");
               
                 var id = window.app.nutrition_plan_overview_model.get('id');
                 window.app.nutrition_plan_overview_model.fetch({
                    data: {id : id},
                    wait : true,
                    success : function(model, response) {
                        window.app.macronutrients_view = new window.app.Macronutrients_view({model : model});
                        
                        $("#macronutrients_container").html(window.app.macronutrients_view.render().el);
                    },
                    error: function (collection, response) {
                        alert(response.responseText);
                    }
                 });
                 // connect comments
                 var comment_options = {
                    'item_id' :  id,
                    'fitness_administration_url' : window.fitness_helper.base_url,
                    'comment_obj' : {'user_name' : window.fitness_helper.user_name, 'created' : "", 'comment' : ""},
                    'db_table' : '#__fitness_nutrition_plan_macronutrients_comments',
                    'read_only' : true,
                    'anable_comment_email' : false
                }
                var comments = $.comments(comment_options, comment_options.item_id, 1);

                var comments_html = comments.run();
                $("#macronutrients_comments_wrapper").html(comments_html);
            },
            
            supplements: function () {
                 this.no_active_plan_action();
                 this.common_actions();
                 $("#supplements_wrapper").show();
                 $("#supplements_link").addClass("active_link");

                 window.app.protocols = new window.app.Protocols_collection(); 
                 var id = window.app.nutrition_plan_overview_model.get('id');
                 window.app.protocols.fetch({
                    data: {nutrition_plan_id : id},
                    error: function (collection, response) {
                        alert(response.responseText);
                    }
                 });
                 
                 window.app.nutrition_plan_protocols_view = new window.app.Nutrition_plan_protocols_view({model : window.app.nutrition_plan_overview_model, collection : window.app.protocols}); 
                 $("#supplements_wrapper").html(window.app.nutrition_plan_protocols_view.render().el);
            },
                    
            nutrition_guide: function () {
                 this.no_active_plan_action();
                 this.common_actions();
                 $("#nutrition_guide_wrapper").show();
                 $("#nutrition_guide_link").addClass("active_link");
                 
                 window.app.email_pdf_header_template =  new window.app.Email_pdf_header_template({model : window.app.nutrition_plan_overview_model});
                 
                 $("#nutrition_guide_header").html(window.app.email_pdf_header_template.render().el);
                 
                 window.app.nutrition_guide_menu =  new window.app.Nutrition_guide_menu();
                 
                 $("#nutrition_guide_container").html(window.app.nutrition_guide_menu.render().el);
                 
                 this.example_day(1);
                 $(".example_day_link").first().addClass("active");
            },
            
            example_day : function(example_day_id) {
               
                window.app.example_day_meal_collection = new window.app.Example_day_meals_collection(); 
                var id = window.app.nutrition_plan_overview_model.get('id');
                window.app.example_day_meal_collection.fetch({data: {
                        nutrition_plan_id : id,
                        example_day_id : example_day_id
                    },
                    error: function (collection, response) {
                        alert(response.responseText);
                    }
                });
                
                $('#example_day_wrapper').html(new window.app.Example_day_view({collection : window.app.example_day_meal_collection, 'example_day_id' : example_day_id}).render().el);
            },
                    
            information: function () {
                 this.no_active_plan_action();
                 this.common_actions();
                 $("#information_wrapper").show();
                 $("#information_link").addClass("active_link");
                 var id = window.app.nutrition_plan_overview_model.get('id');
                 window.app.nutrition_plan_overview_model.fetch({
                    data: {id : id},
                    wait : true,
                    success : function(model, response) {
                        window.app.nutrition_plan_information_view = new window.app.Nutrition_plan_information_view({model : model});
                        
                        $("#information_wrapper").html(window.app.nutrition_plan_information_view.render().el);
                    },
                    error: function (collection, response) {
                        alert(response.responseText);
                    }
                 });
            },
                    
            archive: function () {
                 this.common_actions();
                 $("#archive_wrapper").show();
                 $("#archive_focus_link").addClass("active_link");

                 window.app.nutrition_plans_collection.fetch({
                    data: {id : options.item_id, client_id : options.client_id},
                    wait : true,
                    success : function(collection, response) {
                        window.app.archive_list_view = new window.app.Archive_list_view({collection : collection});
                        
                        $("#archive_wrapper").html(window.app.archive_list_view.render().el);
                        
                    },
                    error: function (collection, response) {
                        alert(response.responseText);
                    }
                 });
            },
                    
            close: function() {
                 this.no_active_plan_action();
                 $("#close_tab").hide();
                 window.app.nutrition_plan_overview_model.set({id : options.item_id});
                 this.overview();
            },
            
            common_actions : function() {
                $(".block").hide();
                $(".plan_menu_link").removeClass("active_link")
            },
            
            no_active_plan_action : function() {
                if(!options.item_id) {
                    alert('Please contact your trainer immediately regarding your current Nutrition Plan!');
                    return false;
                }
            }

        });

        window.app.controller = new Controller(); 

        Backbone.history.start();  
       

    })($js);
    

        
</script>



