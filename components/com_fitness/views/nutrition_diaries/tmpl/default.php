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
$user_id = JFactory::getUser()->id;

$helper = new FitnessHelper();

$business_profile_id = $helper->getBusinessProfileId($user_id);

$business_profile_id = $business_profile_id['data'];
?>

<div style="opacity: 1;" class="fitness_wrapper">

    <h2>NUTRITION DIARY</h2>
    
    <div id="progress_graph_container"></div>
    
    <div style="padding: 2px;" id="submenu_container"></div>
    
    <div id="main_container"></div>

</div>



<script type="text/javascript">
       
    var options = {
        'fitness_frontend_url' : '<?php echo JURI::root();?>index.php?option=com_fitness&tmpl=component&<?php echo JSession::getFormToken(); ?>=1',
        'calendar_frontend_url' : '<?php echo JURI::root()?>index.php?option=com_multicalendar&task=load&calid=0',
        'base_url' : '<?php echo JURI::root();?>',
        'base_url_relative': '<?php echo JURI::base(); ?>',
        'ajax_call_url' : '<?php echo JURI::root();?>index.php?option=com_fitness&tmpl=component&<?php echo JSession::getFormToken(); ?>=1',
        'user_name' : '<?php echo JFactory::getUser()->name;?>',
        'user_id' : '<?php echo JFactory::getUser()->id;?>',
        'client_id' : '<?php echo JFactory::getUser()->id;?>',
        'diary_db_table' : '#__fitness_nutrition_diary',
        
        'back_url' : decodeURIComponent('<?php echo JRequest::getVar('back_url') ?>'),
        
        'current_view' : '<?php echo  JFactory::getApplication()->input->get('view'); ?>',
        'business_profile_id' : '<?php echo $business_profile_id; ?>',
        
        'scrollTo' : '<?php echo JRequest::getVar('scrollTo') ?>',
    };
    
    var statuses = {
        'PENDING_GOAL_STATUS' :     {id : '<?php echo FitnessHelper::PENDING_GOAL_STATUS ?>',       name : 'PENDING'},
        'COMPLETE_GOAL_STATUS':     {id : '<?php echo FitnessHelper::COMPLETE_GOAL_STATUS ?>',      name : 'COMPLETE'},
        'INCOMPLETE_GOAL_STATUS' :  {id : '<?php echo FitnessHelper::INCOMPLETE_GOAL_STATUS ?>',    name : 'INCOMPLETE'},
        'EVELUATING_GOAL_STATUS' :  {id : '<?php echo FitnessHelper::EVELUATING_GOAL_STATUS ?>',    name : 'EVALUATING'},
        'INPROGRESS_GOAL_STATUS' :  {id : '<?php echo FitnessHelper::INPROGRESS_GOAL_STATUS ?>',    name : 'IN PROGRESS'},
        'ASSESSING_GOAL_STATUS' :   {id : '<?php echo FitnessHelper::ASSESSING_GOAL_STATUS ?>',     name : 'ASSESSING'},
        'SCHEDULED_GOAL_STATUS' :   {id : '<?php echo FitnessHelper::SCHEDULED_GOAL_STATUS ?>',     name : 'SCHEDULED'}
    };
    
    var primary_goal_status_options = {
        'fitness_administration_url' : '<?php echo JURI::root();?>administrator/index.php?option=com_fitness&tmpl=component&<?php echo JSession::getFormToken(); ?>=1',
        'calendar_frontend_url' : '<?php echo JURI::root()?>administrator/index.php?option=com_multicalendar&task=load&calid=0',
        'db_table' : '#__fitness_goals',
        'status_button' : 'status_button',
        'status_button_dialog' : 'status_button_dialog',
        'dialog_status_wrapper' : 'dialog_status_wrapper',
        'dialog_status_template' : '#dialog_status_template',
        'status_button_template' : '#status_button_template',
        'status_button_place' : '#status_button_place_',

        'statuses' : {
            '<?php echo FitnessHelper::PENDING_GOAL_STATUS ?>'      : {'label' : statuses.PENDING_GOAL_STATUS.name,       'class' : 'goal_status_pending',       'email_alias' : ''},
            '<?php echo FitnessHelper::COMPLETE_GOAL_STATUS ?>'     : {'label' : statuses.COMPLETE_GOAL_STATUS.name,      'class' : 'goal_status_complete',      'email_alias' : 'GoalComplete'},
            '<?php echo FitnessHelper::INCOMPLETE_GOAL_STATUS ?>'   : {'label' : statuses.INCOMPLETE_GOAL_STATUS.name,    'class' : 'goal_status_incomplete',    'email_alias' : 'GoalIncomplete'},
            '<?php echo FitnessHelper::EVELUATING_GOAL_STATUS ?>'   : {'label' : statuses.EVELUATING_GOAL_STATUS.name,    'class' : 'goal_status_evaluating',    'email_alias' : 'GoalEvaluating'},
            '<?php echo FitnessHelper::INPROGRESS_GOAL_STATUS ?>'   : {'label' : statuses.INPROGRESS_GOAL_STATUS.name,    'class' : 'goal_status_inprogress',    'email_alias' : 'GoalInprogress'},
            '<?php echo FitnessHelper::ASSESSING_GOAL_STATUS ?>'    : {'label' : statuses.ASSESSING_GOAL_STATUS.name,     'class' : 'goal_status_assessing',     'email_alias' : 'GoalAssessing'},
            '<?php echo FitnessHelper::SCHEDULED_GOAL_STATUS ?>'    : {'label' : statuses.SCHEDULED_GOAL_STATUS.name,     'class' : 'goal_status_scheduled',     'email_alias' : 'GoalScheduled'},

        },
        'hide_image_class' : 'hideimage',
        'show_send_email' : true,
         setStatuses : function(item_id) {
            return  this.statuses;
        },
        'view' : 'Goal'
    }

    var mini_goal_status_options = {
        'fitness_administration_url' : '<?php echo JURI::root();?>administrator/index.php?option=com_fitness&tmpl=component&<?php echo JSession::getFormToken(); ?>=1',
        'calendar_frontend_url' : '<?php echo JURI::root()?>administrator/index.php?option=com_multicalendar&task=load&calid=0',
        'db_table' : '#__fitness_mini_goals',
        'status_button' : 'status_button_mini',
        'status_button_dialog' : 'status_button_dialog_mini',
        'dialog_status_wrapper' : 'dialog_status_wrapper',
        'dialog_status_template' : '#dialog_status_template',
        'status_button_template' : '#status_button_template',
        'status_button_place' : '#status_button_place_mini_',

        'statuses' : {
            '<?php echo FitnessHelper::PENDING_GOAL_STATUS ?>'      : {'label' : statuses.PENDING_GOAL_STATUS.name,       'class' : 'goal_status_pending',       'email_alias' : ''},
            '<?php echo FitnessHelper::COMPLETE_GOAL_STATUS ?>'     : {'label' : statuses.COMPLETE_GOAL_STATUS.name,      'class' : 'goal_status_complete',      'email_alias' : 'GoalCompleteMini'},
            '<?php echo FitnessHelper::INCOMPLETE_GOAL_STATUS ?>'   : {'label' : statuses.INCOMPLETE_GOAL_STATUS.name,    'class' : 'goal_status_incomplete',    'email_alias' : 'GoalIncompleteMini'},
            '<?php echo FitnessHelper::EVELUATING_GOAL_STATUS ?>'   : {'label' : statuses.EVELUATING_GOAL_STATUS.name,    'class' : 'goal_status_evaluating',    'email_alias' : 'GoalEvaluatingMini'},
            '<?php echo FitnessHelper::INPROGRESS_GOAL_STATUS ?>'   : {'label' : statuses.INPROGRESS_GOAL_STATUS.name,    'class' : 'goal_status_inprogress',    'email_alias' : 'GoalInprogressMini'},
            '<?php echo FitnessHelper::ASSESSING_GOAL_STATUS ?>'    : {'label' : statuses.ASSESSING_GOAL_STATUS.name,     'class' : 'goal_status_assessing',     'email_alias' : 'GoalAssessingMini'},
            '<?php echo FitnessHelper::SCHEDULED_GOAL_STATUS ?>'    : {'label' : statuses.SCHEDULED_GOAL_STATUS.name,     'class' : 'goal_status_scheduled',     'email_alias' : 'GoalScheduledMini'},

        },
        'hide_image_class' : 'hideimage',
        'show_send_email' : true,
         setStatuses : function(item_id) {
            return  this.statuses;
        },
        'view' : 'Goal'
    }
        
    
    options.primary_goal_status_options = primary_goal_status_options;
    options.mini_goal_status_options = mini_goal_status_options;
        
    //requireJS options

    require.config({
        baseUrl: '<?php echo JURI::root();?>administrator/components/com_fitness/assets/js',
    });


    require(['app'], function(app) {
            app.options = options;
    });
</script>

<script src="<?php echo JURI::root();?>administrator/components/com_fitness/assets/js/config.js" type="text/javascript"></script>
<script src="<?php echo JURI::root();?>administrator/components/com_fitness/assets/js/main_diary_frontend.js" type="text/javascript"></script>


