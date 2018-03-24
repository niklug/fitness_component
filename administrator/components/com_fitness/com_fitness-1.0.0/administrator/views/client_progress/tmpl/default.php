<?php
defined('_JEXEC') or die;


require_once  JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_fitness' . DS .'helpers' . DS . 'fitness.php';

$user_id = JFactory::getUser()->id;

$helper = new FitnessHelper();

$business_profile_id = $helper->getBusinessProfileId($user_id);

$business_profile_id = $business_profile_id['data'];
?>
<div style="width:100%;">
    <div id="header_wrapper" class="fitness_wrapper" style="background:none;"></div>

    <div class="clr"></div>
    <br/>
    
    <div id="sub_search_wrapper" class="fitness_wrapper" style="background:none;"></div>

    <div class="clr"></div>
    <br/>
    
    <div id="progress_graph_container" class="fitness_wrapper" style="background:none;"></div>

    <div class="clr"></div>

    <div id="main_container" class="fitness_wrapper" style="background:none;"></div>

</div>




<script type="text/javascript">

    var options = {
        'fitness_frontend_url' : '<?php echo JURI::root();?>index.php?option=com_fitness&tmpl=component&<?php echo JSession::getFormToken(); ?>=1',
        'calendar_frontend_url' : '<?php echo JURI::root()?>index.php?option=com_multicalendar&task=load&calid=0',
        'ajax_call_url': '<?php echo JURI::root(); ?>administrator/index.php?option=com_fitness&tmpl=component&<?php echo JSession::getFormToken(); ?>=1',
        'base_url' : '<?php echo JURI::root(); ?>',
        'user_name' : '<?php echo JFactory::getUser()->name;?>',
        'user_id' : '<?php echo JFactory::getUser()->id;?>',
        'goals_db_table' : '#__fitness_goals',
        'minigoals_db_table' : '#__fitness_mini_goals',
        'goals_comments_db_table' : '#__fitness_goal_comments',
        'minigoals_comments_db_table' : '#__fitness_mini_goal_comments',
        'db_table_appointments': '#__fitness_categories',
        'db_table_locations': '#__fitness_locations',
        'db_table_session_types': '#__fitness_session_type',
        'db_table_session_focuses': '#__fitness_session_focus',
        'db_table_photos' : '#__fitness_assessments_photos',
        
        'is_superuser' : '<?php echo FitnessFactory::is_superuser($user_id); ?>',
        'is_trainer' : '<?php echo FitnessFactory::is_trainer($user_id); ?>',
        'is_simple_trainer' : '<?php echo FitnessFactory::is_simple_trainer($user_id); ?>',
        'is_trainer_administrator' : '<?php echo FitnessFactory::is_trainer_administrator($user_id); ?>',
        'is_backend' : '<?php echo JFactory::getApplication()->isAdmin(); ?>',
        'business_profile_id' : '<?php echo $business_profile_id; ?>',
        'assessment_priorities' : '<?php echo json_encode(FitnessHelper::assessmentPriorities()) ?>',
        
    };

    require.config({
        baseUrl: '<?php echo JURI::root(); ?>administrator/components/com_fitness/assets/js',
    });


    require(['app'], function(app) {
        app.options = options;
    });
</script>

<script src="<?php echo JURI::root(); ?>administrator/components/com_fitness/assets/js/config.js" type="text/javascript"></script>
<script src="<?php echo JURI::root(); ?>administrator/components/com_fitness/assets/js/main_client_progress_backend.js" type="text/javascript"></script>



