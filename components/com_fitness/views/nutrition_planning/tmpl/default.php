<?php
$user = &JFactory::getUser();

$trainer_id =  $this->active_plan_data->trainer_id;

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
                    <div class="fitness_block_wrapper" style="min-height:200px;">
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
                                 <?php echo $this->nutrition_diaryform_model->getNutritionFocusName($this->active_plan_data->nutrition_focus); ?>
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
    
    
    <div id="daily_targets_wrapper" class="block">DAILY TARGETS</div>
    
    <div id="shopping_list_wrapper" class="block">SHOPPING LISTS</div>
    
    <div id="diary_guide_wrapper" class="block">DIARY GUIDE</div>
    
    <div id="information_wrapper" class="block">INFORMATION</div>
    
    <div id="archive_wrapper" class="block">ARCHIVE</div>
 
</div>




<script type="text/javascript">
    
    (function($) {
        
        
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
            },

            nutrition_focus: function () {
                 $(".block").hide();
                 $("#nutrition_focus_wrapper").show();
                 $(".plan_menu_link").removeClass("active_link");
                 $("#nutrition_focus_link").addClass("active_link");
            },

            daily_targets: function () {
                 $(".block").hide();
                 $("#daily_targets_wrapper").show();
                 $(".plan_menu_link").removeClass("active_link");
                 $("#daily_targets_link").addClass("active_link");
                 
            },

            shopping_list: function () {
                 $(".block").hide();
                 $("#shopping_list_wrapper").show();
                 $(".plan_menu_link").removeClass("active_link");
                 $("#shopping_list_link").addClass("active_link");
            },
                    
            diary_guide: function () {
                 $(".block").hide();
                 $("#diary_guide_wrapper").show();
                 $(".plan_menu_link").removeClass("active_link");
                 $("#diary_guide_link").addClass("active_link");
            },
                    
            information: function () {
                 $(".block").hide();
                 $("#information_wrapper").show();
                 $(".plan_menu_link").removeClass("active_link");
                 $("#information_link").addClass("active_link");
            },
                    
            archive: function () {
                 $(".block").hide();
                 $("#archive_wrapper").show();
                 $(".plan_menu_link").removeClass("active_link");
                 $("#archive_focus_link").addClass("active_link");
            }
                    
            
        });

        var controller = new Controller(); 

        Backbone.history.start();  
        

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


        // connect Graph from Goals frontend logic
        $.goals_frontend(options);

    })($js);
    

        
</script>



