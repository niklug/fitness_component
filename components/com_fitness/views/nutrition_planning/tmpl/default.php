<?php
$user = &JFactory::getUser();

$trainer_id =  $this->active_plan_data->trainer_id;

$nutrition_plan_id = $this->active_plan_data->id;

require_once  JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_fitness' . DS .'helpers' . DS . 'fitness.php';

?>

<div style="opacity: 1;" class="fitness_wrapper">
    <h2>NUTRITION PLAN</h2>
    
    <div id="plan_menu"></div>
    
    <br/>
    
    <!-- OVERVIEW -->
    <div id="overview_wrapper" class="block" style="display:none;">
        <div id="nutrition_focus_wrapper"></div>
        <br/>
        <div id="graph_container"></div>
    </div>
    
    <!-- TARGETS -->
    <div id="targets_wrapper" class="block" style="display:none;">
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
    <div id="macronutrients_wrapper" class="block" style="display:none;">
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
    <div id="supplements_wrapper" class="block" style="display:none;">

    </div>

    <!-- NUTRITION GUIDE -->
    
    <div id="nutrition_guide_wrapper" class="block " style="display:none;">

        <div id="nutrition_guide_header" ></div>

        <div id="nutrition_guide_container" class=""></div>
        
    </div>
    
    <!-- INFORMATION -->
    <div id="information_wrapper" class="block" style="display:none;">
        
    </div>
    
    <!-- ARCHIVE -->
    <div id="archive_wrapper" class="block" style="display:none;">

    </div>
    
    
    <div id="close_wrapper" class="block" style="display:none;">
        
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
            'example_day_meal_comments_db_table' : '#__fitness_nutrition_plan_example_day_meal_comments',
            
            'client_id' : '<?php echo JFactory::getUser()->id;?>',
            
            'user_name' : '<?php echo JFactory::getUser()->name;?>',
            
            'item_id' : '<?php echo  $nutrition_plan_id?>',
            'is_trainer' : '<?php echo FitnessFactory::is_trainer($user_id); ?>',
            'is_client' : '<?php echo FitnessFactory::is_client($user_id); ?>',
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
<script src="<?php echo JURI::root();?>administrator/components/com_fitness/assets/js/main_nutrition_plan_frontend.js" type="text/javascript"></script>




