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
        <table width="100%">
            <tr>
                <td width="40%">
                    <div class="fitness_block_wrapper" style="min-height:224px;">
                        <h3>MY TRAINERS</h3>
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
                    <div class="fitness_block_wrapper" style="min-height:224px;">
                        <h3>NUTRITION PLAN & TRAINING PERIOD</h3>
                        <hr class="orange_line">
                        <div class="internal_wrapper">
                            <table width="100%">
                                <tr>
                                    <td>
                                        Primary Goal 
                                    </td>
                                    <td>
                                        <span class="grey_title">
                                            <?php
                                            echo $this->active_plan_data->primary_goal_name;
                                            ?>
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        Start Date
                                    </td>
                                    <td>
                                        <span class="grey_title">
                                            <?php
                                                $jdate = new JDate($this->active_plan_data->primary_goal_start_date);
                                                echo $jdate->toFormat('%A %d %B %Y');
                                            ?>
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        Achieve By 
                                    </td>
                                    <td>
                                        <span class="grey_title">
                                            <?php
                                                $jdate = new JDate($this->active_plan_data->primary_goal_deadline);
                                                echo $jdate->toFormat('%A %d %B %Y');
                                            ?>
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2">
                                        <hr>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        Mini Goal 
                                    </td>
                                    <td>
                                        <span class="grey_title">
                                            <?php
                                            echo $this->active_plan_data->mini_goal_name;
                                            ?>
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        Training Period 
                                    </td>
                                    <td>
                                        <span class="grey_title">
                                            <?php
                                            echo $this->active_plan_data->training_period_name;
                                            ?>
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        Start Date
                                    </td>
                                    <td>
                                        <span class="grey_title">
                                            <?php
                                                $jdate = new JDate($this->active_plan_data->active_start);
                                                echo $jdate->toFormat('%A %d %B %Y');
                                            ?>
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        Achieve By 
                                    </td>
                                    <td>
                                        <span class="grey_title">
                                            <?php
                                                $jdate = new JDate($this->active_plan_data->active_finish);
                                                echo $jdate->toFormat('%A %d %B %Y');
                                            ?>
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </td>
            </tr>
            
        </table>
   
    
        <br/>

        <div class="fitness_block_wrapper" style="min-height:150px;margin: 2px;">
            <h3>MY NUTRITION FOCUS</h3>
            <hr class="orange_line">
            <div class="internal_wrapper">
                <table width="100%">
                    <tr>
                        <td width="20%">
                            Nutrition Focus
                        </td>
                        <td>
                            <span class="grey_title">
                                 <?php echo $this->active_plan_data->nutrition_focus_name; ?>
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td></td>
                        <td>
                            <?php echo $this->active_plan_data->trainer_comments ?>
                        </td>
                    </tr>
                </table> 


            </div>
        </div>
        
        <br/>

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
        <div class="fitness_block_wrapper" style="min-height: 300px;">
            <h3>DAILY MACRONUTRIENT & CALORIE TARGETS</h3>
            <hr class="orange_line">
            <div class="internal_wrapper" style="height:240px;">
                <table width="100%">
                    <tr>
                        <td  class="center" width="300px">
                            <h6>ACTIVITY LEVEL</h6>
                            
                            <span style="line-height: 69px;font-size:28px; color:#AD0C0C;font-weight:bold;">
                                Heavy Training Day
                            </span>
                        </td>
                        <td  class="center">
                            <div class="pie-container">
                                <h6 style="margin-top:10px;margin-bottom: 5px">MACRONUTRIENT RATIOS</h6>
                                <div id="placeholder_targets_1" class="placeholder_pie"></div>
                            </div>
                        </td>
                        <td class="center">
                            <h6 style="margin-top:50px;">TARGET CALORIE INTAKE</h6> 
                            <div id="calories_value_1" style="line-height: 80px;font-size: 48px; color:#00983A; font-weight: bold;"></div>
                            <div  style="font-size: 22px; color:#00983A;font-weight: bold; height: 40px;"> calories</div>
                        </td>
                        <td class="center">
                            <h6 style="margin-top:50px;">TARGET WATER INTAKE</h6>
                            <div id="water_value_1" style="line-height: 80px;font-size:48px; color:#3F9EEB; font-weight: bold;"></div>
                            <div style="font-size: 22px; color:#3F9EEB; font-weight: bold; height: 40px;">millilitres</div>
                            
                        </td>
                    </tr>
                </table> 
            </div>
            <br/>
            <hr>
            <br/>
            <div class="internal_wrapper" style="height:225px;">
                <table width="100%">
                    <tr>
                        <td  class="center" width="300px">
                            <h6>ACTIVITY LEVEL</h6>
                            
                            <span style="line-height: 69px;font-size:28px; color:#0D7F22;font-weight:bold;">
                                Light Training Day
                            </span>
                        </td>
                        <td  class="center">
                            <div class="pie-container">
                                <h6 style="padding-bottom:5px;margin-top: -6px;" >MACRONUTRIENT RATIOS</h6>
                                <div id="placeholder_targets_2" class="placeholder_pie"></div>
                            </div>
                        </td>
                        <td class="center">
                            <h6 style="margin-top:50px;">TARGET CALORIE INTAKE</h6> 
                            <div id="calories_value_2" style="line-height: 80px;font-size: 48px; color:#00983A; font-weight: bold;"></div>
                            <div  style="font-size: 22px; color:#00983A;font-weight: bold; height: 40px;"> calories</div>
                        </td>
                        <td class="center">
                            <h6 style="margin-top:50px;">TARGET WATER INTAKE</h6>
                            <div id="water_value_2" style="line-height: 80px;font-size:48px; color:#3F9EEB; font-weight: bold;"></div>
                            <div style="font-size: 22px; color:#3F9EEB; font-weight: bold; height: 40px;">millilitres</div>
                            
                        </td>
                    </tr>
                </table> 
            </div>
            
            <br/>
            <hr>
            <br/>
            <div class="internal_wrapper" style="height:245px;">
                <table width="100%">
                    <tr>
                        <td  class="center" width="300px">
                            <h6>ACTIVITY LEVEL</h6>
                            
                            <span style="line-height: 69px;font-size:28px; color:#223FAA; font-weight:bold;">
                                Recovery / Rest Day
                            </span>
                        </td>
                        <td  class="center">
                            <div class="pie-container">
                                <h6 style="padding-bottom:5px;margin-top: -6px;">MACRONUTRIENT RATIOS</h6>
                                <div id="placeholder_targets_3" class="placeholder_pie"></div>
                            </div>
                        </td>
                        <td class="center">
                            <h6 style="margin-top:50px;">TARGET CALORIE INTAKE</h6> 
                            <div id="calories_value_3" style="line-height: 80px;font-size: 48px; color:#00983A; font-weight: bold;"></div>
                            <div  style="font-size: 22px; color:#00983A;font-weight: bold; height: 40px;"> calories</div>
                        </td>
                        <td class="center">
                            <h6 style="margin-top:50px;">TARGET WATER INTAKE</h6>
                            <div id="water_value_3" style="line-height: 80px;font-size:48px; color:#3F9EEB; font-weight: bold;"></div>
                            <div style="font-size: 22px; color:#3F9EEB; font-weight: bold; height: 40px;">millilitres</div>
                            
                        </td>
                    </tr>
                </table> 
            </div>
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
        <table width="100%">
            <tr>
                <td  class="left">
                    <h3 style="color:#FFFFFF !important;">ALLOWED MACRONUTRIENTS</h3>
                </td>
                <td>
                    <a data-id="<?php echo $nutrition_plan_id; ?>" style="float:right;"  class="pdf_button" href="javascript:void(0)" title="Save in PDF"></a>
                    <a data-id="<?php echo $nutrition_plan_id; ?>"  style="float:right;" class="email_button" href="javascript:void(0)" title="Send by Email"></a>
                </td>
            </tr>
        </table>

        <div class="fitness_block_wrapper" style="min-height: 100px;">
            <h3>ALLOWED PROTEINS</h3>
            <hr class="orange_line">
            <div class="internal_wrapper">
                <?php
                echo $this->active_plan_data->allowed_proteins;
                ?>
            </div>
        </div>
        
        <br/>
        
        <div class="fitness_block_wrapper" style="min-height: 100px;">
            <h3>ALLOWED FATS</h3>
            <hr class="orange_line">
            <div class="internal_wrapper">
                <?php
                echo $this->active_plan_data->allowed_fats;
                ?>
            </div>
        </div>
        
        <br/>
        
        <div class="fitness_block_wrapper" style="min-height: 100px;">
            <h3>ALLOWED CARBOHYDRATES</h3>
            <hr class="orange_line">
            <div class="internal_wrapper">
                <?php
                    echo $this->active_plan_data->allowed_carbs;
                ?>
            </div>
        </div>
        
        <br/>
        
        <div class="fitness_block_wrapper" style="min-height: 100px;">
            <h3>ALLOWED LIQUIDS</h3>
            <hr class="orange_line">
            <div class="internal_wrapper">
                <?php
                    echo $this->active_plan_data->allowed_liquids;
                ?>
            </div>
        </div>
        
        <br/>
        
        <div class="fitness_block_wrapper" style="min-height: 100px;">
            <h3>OTHER RECOMMENDATIONS / INSTRUCTIONS</h3>
            <hr class="orange_line">
            <div class="internal_wrapper">
                <?php
                    echo $this->active_plan_data->other_recommendations;
                ?>
            </div>
        </div>

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
        <div>
            <h3 style="color:#FFFFFF !important;">SUPPLEMENTS & SUPPLEMENT PROTOCOLS</h3>
        </div>
        <div id="protocols_wrapper"></div>
    </div>

    <!-- NUTRITION GUIDE -->
    
    <div id="nutrition_guide_wrapper" class="block">
        <div>
            <h3 style="color:#FFFFFF !important;">DAILY MENU & NUTRITION GUIDE</h3>
        </div>
        <div id="nutrition_guide_container" class="fitness_block_wrapper"></div>
        
    </div>
    
    <!-- INFORMATION -->
    <div id="information_wrapper" class="block">
        <div>
            <h3 style="color:#FFFFFF !important;">NUTRITION INFORMATION</h3>
        </div>
        <div class="fitness_block_wrapper" style="min-height: 100px;">
            <div class="internal_wrapper">
                <?php
                    echo $this->active_plan_data->information;
                ?>
            </div>
        </div>
    </div>
    
    <!-- ARCHIVE -->
    <div id="archive_wrapper" class="block">
        <div class="fitness_block_wrapper" style="min-height: 100px;">
            <h3>NUTRITION PLAN HISTORY</h3>
            <hr class="orange_line">
            <div class="internal_wrapper">
                <table width="100%">
                    <thead>
                        <th>
                            START DATE  
                        </th>
                        <th>
                            END DATE 
                        </th>
                        <th>
                            MINI GOAL 
                        </th>
                        <th>
                            NUTRITION FOCUS 
                        </th>
                        <th>
                            CALS
                        </th>
                        <th>
                           PRO %
                        </th>
                        <th>
                            FAT %
                        </th>
                        <th>
                            CARBS %
                        </th>
                        <th>
                            TRAINER
                        </th>
                        <th>
                            VIEW
                        </th>
                    </thead>
                    <tbody>
                        <?php
                           $plans = $this->goals_periods_model->getUserPans($user->id, $nutrition_plan_id);
                           foreach ($plans as $item) {
                                echo "<tr>";
                                echo "<td>" . $item->active_start . "</td>";
                                echo "<td>" . $item->active_finish . "</td>";
                                echo "<td>" . $item->mini_goal_name . "</td>";
                                echo "<td>" . $item->nutrition_focus_name . "</td>";
                                echo "<td>" . $item->calories . "</td>";
                                echo "<td>" . $item->protein . "</td>";
                                echo "<td>" . $item->fats . "</td>";
                                echo "<td>" . $item->carbs . "</td>";
                                echo "<td>" . JFactory::getUser($item->trainer_id)->name . "</td>";
                                echo '<td><a href="javascript:void(0)"><span data-id="' . $item->id . '" class="preview"></span></a></td>';
                                echo '</tr>';
                           }
                        ?>
                    </tbody>
                </table> 
            </div>
        </div>
        
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
            
            'user_name' : '<?php echo JFactory::getUser()->name;?>',
            
            'item_id' : '<?php echo  $nutrition_plan_id?>'
        };
        
        
        
        //TARGETS
        var heavy_target = <?php echo $heavy_target;?>;
        var light_target = <?php echo $light_target;?>;
        var rest_target = <?php echo $rest_target;?>;

        
        function setTargetData(activity_level) {
            var activity_data;
            if(activity_level == '1') activity_data = heavy_target;
            if(activity_level == '2') activity_data = light_target;
            if(activity_level == '3') activity_data = rest_target;


            var calories = activity_data.calories;
            var water = activity_data.water;
            
            $("#calories_value_" + activity_level).html(calories);
            $("#water_value_" + activity_level).html(water);
              
            //console.log(activity_data);
            var data = [
                {label: "Protein:", data: [[1, activity_data.protein]]},
                {label: "Carbs:", data: [[1, activity_data.carbs]]},
                {label: "Fat:", data: [[1, activity_data.fats]]}
            ];

            var container = $("#placeholder_targets_" + activity_level);

            var targets_pie = $.drawPie(data, container, {'no_percent_label' : false});

            targets_pie.draw(); 
        }
        
        
        
        
       
        window.app.Nutrition_plan_model = Backbone.Model.extend({
            defaults: {

            },

            initialize: function(){
                
            },

            ajaxCall : function(data, url, view, task, table, handleData) {
                return $.AjaxCall(data, url, view, task, table, handleData);
            },
                    
            populatePlan : function(id) {
                var data = {};
                var url = this.get('fitness_frontend_url');
                var view = 'goals_periods';
                var task = 'populatePlan';
                var table = '';
                data.id = id;
                var self = this;
                this.ajaxCall(data, url, view, task, table, function(output) {
                    //console.log(output);
                    self.set("plan_data", output);
                });
            },
            
            connect_targets_comments : function() {
                var comment_options = {
                    'item_id' :  this.get('item_id'),
                    'fitness_administration_url' : this.get('fitness_frontend_url'),
                    'comment_obj' : {'user_name' : this.get('user_name'), 'created' : "", 'comment' : ""},
                    'db_table' : this.get('nutrition_plan_targets_comments_db_table'),
                    'read_only' : true,
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
                    'read_only' : true,
                    'anable_comment_email' : false
                }
                var comments = $.comments(comment_options, comment_options.item_id, 1);

                var comments_html = comments.run();
                $("#macronutrients_comments_wrapper").html(comments_html);
            }
            
        });
        
        
        ////
        window.app.Nutrition_focus_view = Backbone.View.extend({
            initialize: function(){
                this.render();
            },
            render: function(){
                var nutrition_plan_id = this.options.nutrition_plan_id;
                this.model.populatePlan(nutrition_plan_id);
                this.listenToOnce(this.model, "change:plan_data", this.onPopulatePlan);
            },
                    
            events: {
                
            },
            loadTemplate : function(variables, target) {
                var template = _.template( $("#" +target).html(), variables );
                this.$el.html(template);
            },
            onPopulatePlan : function() {
                if (this.model.has("plan_data")){
                    var model = this.model;
                    var variables = {
                        'model' : model,
                    }
                    this.loadTemplate(variables, this.options.template);
                };  
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

        
        $(".preview").on('click', function() {
            var id = $(this).attr('data-id');
            $("#close_tab").show();
            controller.navigate("close", true);
            $(".block").hide();
            $("#close_wrapper").show();
            $(".plan_menu_link").removeClass("active_link");
            $("#close_link").addClass("active_link");
            
            new window.app.Nutrition_focus_view({ el: $("#close_wrapper"), model : window.app.nutrition_plan_model, 'nutrition_plan_id' : id, 'template' : 'nutrition_plan_template'});
        });
        
        
        //INIT
        window.app.nutrition_plan_model = new window.app.Nutrition_plan_model(options);
        
        
        //SUPPLEMENTS
        window.app.protocol_options = {

            'fitness_frontend_url' : '<?php echo JURI::root();?>index.php?option=com_fitness&tmpl=component&<?php echo JSession::getFormToken(); ?>=1',
            
            'nutrition_plan_id' : '<?php echo  $nutrition_plan_id ?>',
    
            'user_name' : '<?php echo JFactory::getUser()->name;?>',
                   
            'protocol_comments_db_table' : '#__fitness_nutrition_plan_supplements_comments'
        };
        
        //VIEWS
        window.app.Nutrition_plan_protocols_view = Backbone.View.extend({
            
            initialize: function(){
                
                var self = this;
                
		this.protocolListItemViews = {};

		this.collection.on("add", function(protocol) {
                    var comment_options = {
                        'item_id' : window.app.protocol_options.nutrition_plan_id,
                        'fitness_administration_url' : window.app.protocol_options.fitness_frontend_url,
                        'comment_obj' : {'user_name' : window.app.protocol_options.user_name, 'created' : "", 'comment' : ""},
                        'db_table' : window.app.protocol_options.protocol_comments_db_table,
                        'read_only' : true,
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
            
   
            events: {
                
            },

        });
        
        
        window.app.Nutrition_plan_protocol_view = Backbone.View.extend({
           
            initialize: function(){
                _.bindAll(this,'close', 'render');
                this.model.on("destroy", this.close, this);
            },
            
            render: function(){
                var template = _.template( $("#nutrition_plan_frontend_protocol_item_template").html(), this.model.toJSON());
                this.$el.html(template);
                
                this.connectComments(this.$el);

                this.supplements_list_el = this.$el.find(".supplements_list");
                
                this.supplement_collection = new  window.app.Supplements_collection();
  
                this.protocol_id = this.model.get('id');
                
                if(this.protocol_id ){ 
  
                    this.supplement_collection.fetch({data: {nutrition_plan_id : window.app.protocol_options.nutrition_plan_id, protocol_id : this.protocol_id}});
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

            },

            close :function() {
                $(this.el).unbind();
		$(this.el).remove();
            },

        });
        
        window.app.Nutrition_plan_supplement_view = Backbone.View.extend({
           
            initialize: function(){
     
            },
            
            render: function(){
                var template = _.template( $("#nutrition_plan_frontend_supplement_template").html(), this.model.toJSON());
                this.$el.html(template);
                return this;
            },
                    
            events: {
                "click .view_product": "onClickViewProduct"
            },

            onClickViewProduct : function(event) {
                var url = $(event.target).attr('data-url');
                window.open(url);
            },
            
            close :function() {
                $(this.el).unbind();
                $(this.el).remove();
            },
            
        });
        
         //MODELS
        
        window.app.Protocol_model = Backbone.Model.extend({
            urlRoot : window.app.protocol_options.fitness_frontend_url + '&format=text&view=nutrition_plan&task=nutrition_plan_protocol&',
            
            defaults : {
                id : null,
                nutrition_plan_id : window.app.protocol_options.nutrition_plan_id,
                name : null,
            },
            
            validate: function(attrs, options) {
                if (!attrs.name) {
                  return 'Protocol Name is empty';
                }
            }
        });
        
        window.app.Supplement_model = Backbone.Model.extend({
            urlRoot : window.app.protocol_options.fitness_frontend_url + '&format=text&view=nutrition_plan&task=nutrition_plan_supplement&',
            
            defaults : {
                id : null,
                nutrition_plan_id : window.app.protocol_options.nutrition_plan_id,
                protocol_id : null,
                name : null,
                description : null,
                comments : null,
                url : null,
            },

        });
        
        // COLLECTIONS
        window.app.Protocols_collection = Backbone.Collection.extend({
            url : window.app.protocol_options.fitness_frontend_url + '&format=text&view=nutrition_plan&task=nutrition_plan_protocol&',
            model: window.app.Protocol_model
        });

        
        
        window.app.Supplements_collection = Backbone.Collection.extend({
            url : window.app.protocol_options.fitness_frontend_url + '&format=text&view=nutrition_plan&task=nutrition_plan_supplement&',
            model: window.app.Supplement_model
        });
        // END SUPPLEMENTS
        
        
        // NUTRITION GUIDE
        // connect helper class
        var helper_options = {
            'ajax_call_url' : '<?php echo JURI::root();?>index.php?option=com_fitness&tmpl=component&<?php echo JSession::getFormToken(); ?>=1',
            'base_url' : '<?php echo JURI::root();?>',
        }
        window.fitness_helper = $.fitness_helper(helper_options);
        
        window.app.example_day_options = {

            'fitness_frontend_url' : '<?php echo JURI::root();?>index.php?option=com_fitness&tmpl=component&<?php echo JSession::getFormToken(); ?>=1',
            
            'nutrition_plan_id' : '<?php echo  $nutrition_plan_id ?>',
    
            'user_name' : '<?php echo JFactory::getUser()->name;?>',
                   
            'example_day_meal_comments_db_table' : '#__fitness_nutrition_plan_example_day_meal_comments'
        };
        
        
        //EMAIL_PDF
        $(".pdf_button").on('click', function() {
            var htmlPage = '<?php echo JURI::base() ?>index.php?option=com_multicalendar&view=pdf&tpml=component&layout=email_pdf_nutrition_plan_macros&id=<?php echo  $nutrition_plan_id?>&client_id=<?php echo JFactory::getUser()->id;?>';
            window.fitness_helper.printPage(htmlPage);
        });
        
        $(".email_button").on('click', function() {
            var data = {};
            data.url = '<?php echo JURI::base();?>index.php?option=com_fitness&tmpl=component&<?php echo JSession::getFormToken(); ?>=1';
            data.view = '';
            data.task = 'ajax_email';
            data.table = '';

            data.id = <?php echo  $nutrition_plan_id?>;
            data.view = 'NutritionPlan';
            data.method = 'email_pdf_nutrition_plan_macros';
            window.fitness_helper.sendEmail(data);
        });
        //
        
        
        
        window.app.Nutrition_guide_menu = Backbone.View.extend({
            el : $("#nutrition_guide_container"),

            template : _.template($('#nutrition_guide_menu_frontend_template').html()),

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
            render: function(){
                var template = _.template( $("#nutrition_plan_example_day_frontend_template").html());
                this.$el.html(template);
                
                var self = this;
                
		this.mealListItemViews = {};

		this.collection.on("add", function(meal) {
                    window.app.nutrition_plan_example_day_meal_view = new window.app.Nutrition_plan_example_day_meal_view({collection : this,  model : meal}); 
                    self.$el.find("#example_day_meal_list").append( window.app.nutrition_plan_example_day_meal_view.render().el );
		});
               
                return this;
            },
        });
        
        window.app.Nutrition_plan_example_day_meal_view = Backbone.View.extend({
           
            initialize: function(){
                _.bindAll(this,'close', 'render');
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
                var template = _.template( $("#nutrition_plan_example_day_item_frontend_template").html(), this.model.toJSON());
                this.$el.html(template);
               
                this.connectComments();
                
                return this;
            },
            
            addRecipe : function(model) {
                this.item_view = new window.app.Nutrition_guide_recipe_view({collection : this.recipes_collection, model : model}); 
                this.$el.find(".meal_recipes").append( this.item_view.render().el );
            },
            
            connectComments : function() {
                var meal_id = this.model.get('id');
                var comment_options = {
                    'item_id' : window.app.example_day_options.nutrition_plan_id,
                    'fitness_administration_url' : window.app.example_day_options.fitness_backend_url,
                    'comment_obj' : {'user_name' : window.app.example_day_options.user_name, 'created' : "", 'comment' : ""},
                    'db_table' : window.app.example_day_options.example_day_meal_comments_db_table,
                    'read_only' : true,
                }
                var comments = $.comments(comment_options, comment_options.item_id, meal_id).run();
                this.$el.find(".comments_wrapper").html(comments);
            },
           
            close :function() {
                $(this.el).unbind();
		$(this.el).remove();
            },
 
        });
        
        
        window.app.Nutrition_guide_recipe_view = Backbone.View.extend({
            render:function () {
                var template = _.template( $("#nutrition_guide_recipe_frontend_template").html(), this.model.toJSON());
                this.$el.html(template);
                return this;
            },
            
            events: {
                "click .view_recipe" : "onClickViewRecipe",
            },
            
            onClickViewRecipe : function(event) {
                var url = helper_options.base_url + 'index.php?option=com_fitness&view=recipe_database&Itemid=1002#!/nutrition_recipe/' + this.model.get('original_recipe_id');
                window.open(url);
            }, 
            
        });
        
        
        //MODELS
        window.app.Example_day_meal_model = Backbone.Model.extend({
            urlRoot : window.app.example_day_options.fitness_frontend_url + '&format=text&view=nutrition_plan&task=nutrition_plan_exercie_day_meal&',
            
            defaults : {
                id : null,
                description : null,
                nutrition_plan_id : window.app.example_day_options.nutrition_plan_id,
                example_day_id : null,
                meal_time : null,
            },
        });
        
        
         // COLLECTIONS
        window.app.Example_day_meals_collection = Backbone.Collection.extend({
            url : window.app.example_day_options.fitness_frontend_url + '&format=text&view=nutrition_plan&task=nutrition_plan_exercie_day_meal&',
            model: window.app.Example_day_meal_model
        });
        
        window.app.Nutrition_guide_recipes_collection = Backbone.Collection.extend({
            url : window.app.example_day_options.fitness_frontend_url + '&format=text&view=nutrition_plan&task=nutrition_guide_recipes&'
        });
        
        
        // END NUTRITION GUIDE
        
        
        
        
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
                "!/example_day/:id": "example_day", 
            },

            overview: function () {
                 this.common_actions();
                 $("#overview_wrapper").show();
                 $("#overview_link").addClass("active_link");
                 // connect Graph from Goals frontend logic
                 $.goals_frontend(options);
                 
            },

            targets: function () {
                 this.common_actions();
                 $("#targets_wrapper").show();
                 $("#targets_link").addClass("active_link");
                  
                 //draw targets
                 setTargetData(1);
                 setTargetData(2);
                 setTargetData(3);
                 
                 window.app.nutrition_plan_model.connect_targets_comments();
                 
            },

            macronutrients: function () {
                 this.common_actions();
                 $("#macronutrients_wrapper").show();
                 $("#macronutrients_link").addClass("active_link");
                 window.app.nutrition_plan_model.connect_macronutrients_comments();
            },
            
            supplements: function () {
                 this.common_actions();
                 $("#supplements_wrapper").show();
                 $("#supplements_link").addClass("active_link");
                 
                 $("#protocols_wrapper").empty();
                 
                 window.app.protocols = new window.app.Protocols_collection(); 
                 
                 window.app.protocols.fetch({data: {nutrition_plan_id : window.app.protocol_options.nutrition_plan_id}});
                 
                 window.app.nutrition_plan_protocols_view = new window.app.Nutrition_plan_protocols_view({el : $("#protocols_wrapper"), collection : window.app.protocols}); 

            },
                    
            nutrition_guide: function () {
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
                    success : function (collection, response) {
                        //console.log(response);
                    },
                    error: function (collection, response) {
                        alert(response.responseText);
                    }
                });
                
                $('#example_day_wrapper').html(new window.app.Example_day_view({collection : window.app.example_day_meal_collection, 'example_day_id' : example_day_id}).render().el);
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
            }
                    
            
        });

        window.app.controller = new Controller(); 

        Backbone.history.start();  
       

    })($js);
    

        
</script>



