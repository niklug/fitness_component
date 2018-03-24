<?php
$user = &JFactory::getUser();

$trainer_id =  $this->active_plan_data->trainer_id;

$nutrition_plan_id = $this->active_plan_data->id;

$user_id = JFactory::getUser()->id;

$helper = new FitnessHelper();

$business_profile_id = $helper->getBusinessProfileId($user_id);

$business_profile_id = $business_profile_id['data'];

require_once  JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_fitness' . DS .'helpers' . DS . 'fitness.php';

?>

<div style="opacity: 1;" class="fitness_wrapper">
    <h2>NUTRITION PLAN</h2>
    <div id="header_wrapper" ></div>
    <div class="clr"></div>
    <div id="nutrition_guide_header"></div>
    <div class="clr"></div>
    <div id="main_container"></div>
    <div class="clr"></div>
    <br/>
    <div id="graph_container"></div>
 
</div>


<script type="text/javascript">
    
    var add_diary_options = {
        'nutrition_plan_id' : '<?php echo JRequest::getVar('nutrition_plan_id'); ?>',
        'diary_id' : '<?php echo JRequest::getVar('diary_id'); ?>',
        'meal_entry_id' : '<?php echo JRequest::getVar('meal_entry_id'); ?>',
        'meal_id' : '<?php echo JRequest::getVar('meal_id'); ?>',
        'type' : '<?php echo JRequest::getVar('type'); ?>',
        'parent_view' : '<?php echo JRequest::getVar('parent_view');?>',
        'back_url' : '<?php echo JRequest::getVar('back_url');?>'
    };
    
    var options = {
            'fitness_frontend_url' : '<?php echo JURI::root();?>index.php?option=com_fitness&tmpl=component&<?php echo JSession::getFormToken(); ?>=1',
            'calendar_frontend_url' : '<?php echo JURI::root()?>index.php?option=com_multicalendar&task=load&calid=0',
            'base_url' : '<?php echo JURI::root();?>',
            'ajax_call_url' : '<?php echo JURI::root();?>index.php?option=com_fitness&tmpl=component&<?php echo JSession::getFormToken(); ?>=1',
            'relative_url' : '<?php echo JURI::base();?>',
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
            
            'item_id' : '<?php echo  $nutrition_plan_id?>',
            
            'is_client' : '<?php echo FitnessFactory::is_client($user_id); ?>',
            'is_superuser' : '<?php echo FitnessFactory::is_superuser($user_id); ?>',
            'is_trainer' : '<?php echo FitnessFactory::is_trainer($user_id); ?>',
            'is_simple_trainer' : '<?php echo FitnessFactory::is_simple_trainer($user_id); ?>',
            'is_trainer_administrator' : '<?php echo FitnessFactory::is_trainer_administrator($user_id); ?>',
            'is_backend' : '<?php echo JFactory::getApplication()->isAdmin(); ?>',
            'business_profile_id' : '<?php echo $business_profile_id; ?>',
        };

        
        //
        var menu_status_options = {
            'statuses' : {
                '1' : {'label' : 'PENDING', 'class' : 'menu_plan_status_pending', 'email_alias' : ''}, 
                '2' : {'label' : 'APPROVED', 'class' : 'recipe_status_approved', 'email_alias' : 'menu_plan_approved'},
                '3' : {'label' : 'NOT APPROVED', 'class' : 'recipe_status_notapproved', 'email_alias' : 'menu_plan_notapproved'},
                '4' : {'label' : 'IN PROGRESS', 'class' : 'status_inprogress', 'email_alias' : 'menu_plan_inprogress'},
                '5' : {'label' : 'SUBMITTED', 'class' : 'status_submitted', 'email_alias' : ''}, 
                '6' : {'label' : 'RESUBMIT', 'class' : 'status_fail', 'email_alias' : 'menu_plan_resubmit'}
            },
            statuses2 : {},
              'show_send_email' : true,
            setStatuses : function() {
                return this.statuses;
            },
            view : 'MenuPlan',
        }
        
        options.menu_status_options = menu_status_options;
        options.add_diary_options = add_diary_options;
        
        
        
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




