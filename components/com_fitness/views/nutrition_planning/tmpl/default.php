<?php
$user = &JFactory::getUser();

$trainer_id =  $this->active_plan_data->trainer_id;

$nutrition_plan_id = $this->active_plan_data->id;

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
    
    <div id="nutrition_guide_wrapper" class="block ">

        <div id="nutrition_guide_header" ></div>

        <div id="nutrition_guide_container" class=""></div>
        
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
    var options = {
            'fitness_frontend_url' : '<?php echo JURI::root();?>index.php?option=com_fitness&tmpl=component&<?php echo JSession::getFormToken(); ?>=1',
            'calendar_frontend_url' : '<?php echo JURI::root()?>index.php?option=com_multicalendar&task=load&calid=0',
            'base_url' : '<?php echo JURI::root();?>',
            'ajax_call_url' : '<?php echo JURI::root();?>index.php?option=com_fitness&tmpl=component&<?php echo JSession::getFormToken(); ?>=1',
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
            
            'client_id' : '<?php echo JFactory::getUser()->id;?>',
            
            'user_name' : '<?php echo JFactory::getUser()->name;?>',
            
            'item_id' : '<?php echo  $nutrition_plan_id?>'
        };
        
        
        //requireJS options

        require.config({
            baseUrl: '<?php echo JURI::root();?>administrator/components/com_fitness/assets/js',
        });


        require(['app'], function(app) {
                app.options = options;
        });
        
        

</script>

<script src="<?php echo JURI::root();?>administrator/components/com_fitness/assets/js/main.js" type="text/javascript"></script>

<script type="text/javascript">
    /*
    (function($) {
        window.app = {};
        Backbone.emulateHTTP = true ;
        Backbone.emulateJSON = true;

        
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

        //
        
        
        window.app.example_day_options = {

            'fitness_frontend_url' : '<?php echo JURI::root();?>index.php?option=com_fitness&tmpl=component&<?php echo JSession::getFormToken(); ?>=1',
            
            'base_url' : '<?php echo JURI::root();?>',
            
            'nutrition_plan_id' : '<?php echo  $nutrition_plan_id ?>',
    
            'user_name' : '<?php echo JFactory::getUser()->name;?>',
                   
            'example_day_meal_comments_db_table' : '#__fitness_nutrition_plan_example_day_meal_comments'
        };
        
        $.NutritionGuide();
   
        // END NUTRITION GUIDE options

        
        //window.app.nutrition_plan_menu_view = new window.app.Nutrition_plan_menu_view();            
        
        //CONTROLLER
        
        var Controller = Backbone.Router.extend({

        
            routes: {
                "": "nutrition_guide", 
                "!/nutrition_guide": "nutrition_guide", 
                "!/example_day/:id": "example_day", 
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
    
*/
        
</script>



