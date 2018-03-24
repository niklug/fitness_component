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
$document = &JFactory::getDocument();
$document -> addscript( JUri::root() . 'administrator/components/com_fitness/assets/js/lib/require.js');

require_once  JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_fitness' . DS .'helpers' . DS . 'fitness.php';

$user_id = JFactory::getUser()->id;

$helper = new FitnessHelper();

$business_profile_id = $helper->getBusinessProfileId($user_id);

$business_profile_id = $business_profile_id['data'];

?>
<div style="opacity: 1;" class="fitness_wrapper">

    <h2>GOALS & TRAINING PERIODS</h2>
    
    <div class="clr"></div>
    
    <div id="graph_container" >  </div>
    
    <div class="clr"></div>
    
    <div id="list_type_wrapper"></div>
    
    <div class="clr"></div>
    
    <div  id="submenu_container"></div>
    
    <div id="main_container"></div>

</div>


<script type="text/javascript">

    var options = {
        'fitness_frontend_url' : '<?php echo JURI::root();?>index.php?option=com_fitness&tmpl=component&<?php echo JSession::getFormToken(); ?>=1',
        'calendar_frontend_url' : '<?php echo JURI::root()?>index.php?option=com_multicalendar&task=load&calid=0',
        'ajax_call_url': '<?php echo JURI::root(); ?>index.php?option=com_fitness&tmpl=component&<?php echo JSession::getFormToken(); ?>=1',
        'pending_review_text' : 'Pending Review',
        'user_name' : '<?php echo JFactory::getUser()->name;?>',
        'user_id' : '<?php echo JFactory::getUser()->id;?>',
        'client_id' : '<?php echo JFactory::getUser()->id;?>',
        'goals_db_table' : '#__fitness_goals',
        'minigoals_db_table' : '#__fitness_mini_goals',
        'goals_comments_db_table' : '#__fitness_goal_comments',
        'minigoals_comments_db_table' : '#__fitness_mini_goal_comments',
        
        'is_client' : '<?php echo FitnessFactory::is_client($user_id); ?>',
        'is_superuser' : '<?php echo FitnessFactory::is_superuser($user_id); ?>',
        'is_trainer' : '<?php echo FitnessFactory::is_trainer($user_id); ?>',
        'is_simple_trainer' : '<?php echo FitnessFactory::is_simple_trainer($user_id); ?>',
        'is_trainer_administrator' : '<?php echo FitnessFactory::is_trainer_administrator($user_id); ?>',
        'is_backend' : '<?php echo JFactory::getApplication()->isAdmin(); ?>',
        'business_profile_id' : '<?php echo $business_profile_id; ?>',
    };
    

    
        //status options
        
        var statuses = {
            'PENDING_GOAL_STATUS' :     {id : '<?php echo FitnessHelper::PENDING_GOAL_STATUS ?>',       name : 'PENDING'},
            'COMPLETE_GOAL_STATUS':     {id : '<?php echo FitnessHelper::COMPLETE_GOAL_STATUS ?>',      name : 'COMPLETE'},
            'INCOMPLETE_GOAL_STATUS' :  {id : '<?php echo FitnessHelper::INCOMPLETE_GOAL_STATUS ?>',    name : 'INCOMPLETE'},
            'EVELUATING_GOAL_STATUS' :  {id : '<?php echo FitnessHelper::EVELUATING_GOAL_STATUS ?>',    name : 'EVALUATING'},
            'INPROGRESS_GOAL_STATUS' :  {id : '<?php echo FitnessHelper::INPROGRESS_GOAL_STATUS ?>',    name : 'IN PROGRESS'},
            'ASSESSING_GOAL_STATUS' :   {id : '<?php echo FitnessHelper::ASSESSING_GOAL_STATUS ?>',     name : 'ASSESSING'},
            'SCHEDULED_GOAL_STATUS' :   {id : '<?php echo FitnessHelper::SCHEDULED_GOAL_STATUS ?>',     name : 'SCHEDULED'}
        };
        
        
        
        
        var status_options = {
        'fitness_administration_url' : '<?php echo JURI::root();?>administrator/index.php?option=com_fitness&tmpl=component&<?php echo JSession::getFormToken(); ?>=1',
        'calendar_frontend_url' : '<?php echo JURI::root()?>administrator/index.php?option=com_multicalendar&task=load&calid=0',
        'db_table' : '#__fitness_mini_goals',
        'status_button' : 'status_button',
        'status_button_dialog' : 'status_button_dialog',
        'dialog_status_wrapper' : 'dialog_status_wrapper',
        'dialog_status_template' : '#dialog_status_template',
        'status_button_template' : '#status_button_template',
        'status_button_place' : '#status_button_place_',
                
        'statuses' : {
            '<?php echo FitnessHelper::PENDING_GOAL_STATUS ?>'      : {'label' : statuses.PENDING_GOAL_STATUS.name,       'class' : 'goal_status_pending',       'email_alias' : ''},
            '<?php echo FitnessHelper::COMPLETE_GOAL_STATUS ?>'     : {'label' : statuses.COMPLETE_GOAL_STATUS.name,      'class' : 'goal_status_complete',      'email_alias' : ''},
            '<?php echo FitnessHelper::INCOMPLETE_GOAL_STATUS ?>'   : {'label' : statuses.INCOMPLETE_GOAL_STATUS.name,    'class' : 'goal_status_incomplete',    'email_alias' : ''},
            '<?php echo FitnessHelper::EVELUATING_GOAL_STATUS ?>'   : {'label' : statuses.EVELUATING_GOAL_STATUS.name,    'class' : 'goal_status_evaluating',    'email_alias' : ''},
            '<?php echo FitnessHelper::INPROGRESS_GOAL_STATUS ?>'   : {'label' : statuses.INPROGRESS_GOAL_STATUS.name,    'class' : 'goal_status_inprogress',    'email_alias' : ''},
            '<?php echo FitnessHelper::ASSESSING_GOAL_STATUS ?>'    : {'label' : statuses.ASSESSING_GOAL_STATUS.name,     'class' : 'goal_status_assessing',     'email_alias' : ''},
            '<?php echo FitnessHelper::SCHEDULED_GOAL_STATUS ?>'    : {'label' : statuses.SCHEDULED_GOAL_STATUS.name,     'class' : 'goal_status_scheduled',     'email_alias' : ''},

        },

 
        'hide_image_class' : 'hideimage',
        'show_send_email' : true,
         setStatuses : function(item_id) {
            return  this.statuses;
        },
        'view' : 'Programs'
    }
    options.status_options = status_options;
    
    options.statuses = statuses;

    //console.log(options);
    //requireJS options

    require.config({
        baseUrl: '<?php echo JURI::root(); ?>administrator/components/com_fitness/assets/js',
    });


    require(['app'], function(app) {
        app.options = options;
    });
</script>

<script src="<?php echo JURI::root(); ?>administrator/components/com_fitness/assets/js/config.js" type="text/javascript"></script>
<script src="<?php echo JURI::root(); ?>administrator/components/com_fitness/assets/js/main_goals_frontend.js" type="text/javascript"></script>



