<?php
$user = &JFactory::getUser();

$trainer_id =  $this->active_plan_data->trainer_id;

$nutrition_plan_id = $this->active_plan_data->id;

$heavy_target = $this->nutrition_diaryform_model->getNutritionTarget($nutrition_plan_id, 'heavy');

$light_target = $this->nutrition_diaryform_model->getNutritionTarget($nutrition_plan_id, 'light');

$rest_target = $this->nutrition_diaryform_model->getNutritionTarget($nutrition_plan_id, 'rest');

?>
<style>
    
    #plan_menu {
        width: 100%;
        margin: 2px 2px 2px;
    }
    
    #plan_menu ul {
        width: 100%;
        display: inline-block;
        margin: 0;
        padding: 0;
    }
    
    #plan_menu ul li {
        border: 1px solid #CCCCCC;
        float: left;
        line-height: 1.7em;
        list-style: none outside none;
        padding: 0 10px;
        position: relative;
        text-align: center;
        width: auto;
    }
    
    #plan_menu ul li a {
        text-decoration: none !important;
        color: #BC4A26;
        font-size: 16px;
    }
    
    #plan_menu ul li a:hover, #plan_menu ul li a:active, .active_link {
        color:#fff !important;
        border-bottom: 2px solid #BC4A26;
        font-weight:bold;
    }
    
</style>


<div style="opacity: 1;" class="fitness_wrapper">
    <h2>NUTRITION PLAN</h2>
    
    <div id="plan_menu">
        <ul>
            <li><a id="nutrition_focus_link" class="plan_menu_link"  href="#!/nutrition_focus">NUTRITION FOCUS</a></li>
            <li><a id="daily_targets_link" class="plan_menu_link"  href="#!/daily_targets">DAILY TARGETS</a></li>
            <li><a id="shopping_list_link" class="plan_menu_link"  href="#!/shopping_list">SHOPPING LISTS</a></li>
            <li><a id="diary_guide_link" class="plan_menu_link"  href="#!/diary_guide">DIARY GUIDE</a></li>
            <li><a id="information_link" class="plan_menu_link"  href="#!/information">INFORMATION</a></li>
            <li><a id="archive_focus_link" class="plan_menu_link"  href="#!/archive">ARCHIVE</a></li>
            <li style="display:none;" id="close_tab" ><a id="close_link" class="plan_menu_link"  href="#!/close">CLOSE</a></li>
        </ul>
    </div>
    
    <br/>
    
    <div id="nutrition_focus_wrapper" class="block">
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
    
    
    <div id="daily_targets_wrapper" class="block">
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
    </div>
    
    
    
    <div id="shopping_list_wrapper" class="block">
        <div>
            <h3 style="color:#FFFFFF !important;">ALLOWED MACRONUTRIENTS / SHOPPING LIST</h3>
        </div>
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
        
        <br/>
        
        <div>
            <h3 style="color:#FFFFFF !important;">SUPPLEMENTS / SHOPPING LIST</h3>
        </div>
        <div class="fitness_block_wrapper" style="min-height: 100px;">
            <div class="internal_wrapper">
                <table width="100%">
                    <thead>
                        <th>
                            Supplement Name 
                        </th>
                        <th>
                            Recommended Usage 
                        </th>
                        <th>
                            Trainer Comments 
                        </th>
                        <th>
                            Shop/Link
                        </th>
                    </thead>
                    <tbody>
                        <?php
                            $shopping_list = $this->goals_periods_model->getPlanShoppingList($nutrition_plan_id);
                            
                            foreach ($shopping_list as $item) {
                                echo "<tr> <td>" . $item->name . "</td>";
                                echo "<td>" . $item->usage . "</td>";
                                echo "<td>" . $item->comments . "</td>";
                                echo '<td width="80"><a style="font-size:12px;" target="_blank" href="' . $item->url . '">[view product]</a></td></tr>';
                            }
                        ?>
                    </tbody>
                </table> 
            </div>
        </div>
        
    </div>

    
    <div id="diary_guide_wrapper" class="block">
        DIARY GUIDE
    </div>
    
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
        
        
        var Controller = Backbone.Router.extend({
            routes: {
                "": "nutrition_focus", 
                "!/": "nutrition_focus", 
                "!/nutrition_focus": "nutrition_focus", 
                "!/daily_targets": "daily_targets", 
                "!/shopping_list": "shopping_list", 
                "!/diary_guide": "diary_guide", 
                "!/information": "information", 
                "!/archive": "archive", 
                "!/close": "close", 
            },

            nutrition_focus: function () {
                 $(".block").hide();
                 $("#nutrition_focus_wrapper").show();
                 $(".plan_menu_link").removeClass("active_link");
                 $("#nutrition_focus_link").addClass("active_link");
                 $("#close_tab").hide();
                 // connect Graph from Goals frontend logic
                 $.goals_frontend(options);
            },

            daily_targets: function () {
                 $(".block").hide();
                 $("#daily_targets_wrapper").show();
                 $(".plan_menu_link").removeClass("active_link");
                 $("#daily_targets_link").addClass("active_link");
                 $("#close_tab").hide();
                 
                 //draw targets
                 setTargetData(1);
                 setTargetData(2);
                 setTargetData(3);
                 
            },

            shopping_list: function () {
                 $(".block").hide();
                 $("#shopping_list_wrapper").show();
                 $(".plan_menu_link").removeClass("active_link");
                 $("#shopping_list_link").addClass("active_link");
                 $("#close_tab").hide();
            },
                    
            diary_guide: function () {
                 $(".block").hide();
                 $("#diary_guide_wrapper").show();
                 $(".plan_menu_link").removeClass("active_link");
                 $("#diary_guide_link").addClass("active_link");
                 $("#close_tab").hide();
            },
                    
            information: function () {
                 $(".block").hide();
                 $("#information_wrapper").show();
                 $(".plan_menu_link").removeClass("active_link");
                 $("#information_link").addClass("active_link");
                 $("#close_tab").hide();
            },
                    
            archive: function () {
                 $(".block").hide();
                 $("#archive_wrapper").show();
                 $(".plan_menu_link").removeClass("active_link");
                 $("#archive_focus_link").addClass("active_link");
                 $("#close_tab").hide();
            },
                    
            close: function() {
                 
                 //alert('closing');
                 $("#close_tab").hide();
                 this.archive();
            }
                    
            
        });

        var controller = new Controller(); 
        

        Backbone.history.start();  
        
       
        Nutrition_plan_model = Backbone.Model.extend({
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
            }
            
        });
        
        
        ////
        Nutrition_focus_view = Backbone.View.extend({
            initialize: function(){
                this.model = new Nutrition_plan_model(options);
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

        
        $(".preview").on('click', function() {
            var id = $(this).attr('data-id');
            $("#close_tab").show();
            controller.navigate("close", true);
            $(".block").hide();
            $("#close_wrapper").show();
            $(".plan_menu_link").removeClass("active_link");
            $("#close_link").addClass("active_link");
            
            new Nutrition_focus_view({ el: $("#close_wrapper"), 'nutrition_plan_id' : id, 'template' : 'nutrition_plan_template'});
        });
       

    })($js);
    

        
</script>



