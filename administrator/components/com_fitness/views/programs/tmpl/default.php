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
?>
<div id="header_wrapper"></div>
<div class="clr"></div>
<div id="main_container"></div>

<?php

$user_id = JFactory::getUser()->id;

$helper = new FitnessHelper();

$business_profile_id = $helper->getBusinessProfileId($user_id);

$business_profile_id = $business_profile_id['data'];

?>




<script type="text/javascript">

    var options = {
        'fitness_frontend_url': '<?php echo JURI::root(); ?>index.php?option=com_fitness&tmpl=component&<?php echo JSession::getFormToken(); ?>=1',
        'calendar_frontend_url': '<?php echo JURI::root() ?>index.php?option=com_multicalendar&task=load&calid=0',
        'base_url': '<?php echo JURI::root(); ?>',
        'ajax_call_url': '<?php echo JURI::root(); ?>administrator/index.php?option=com_fitness&tmpl=component&<?php echo JSession::getFormToken(); ?>=1',
        'user_name': '<?php echo JFactory::getUser()->name; ?>',
        'user_id': '<?php echo JFactory::getUser()->id; ?>',
        'client_id': '<?php echo JFactory::getUser()->id; ?>',
        'db_table_exercise_type': '#__fitness_settings_exercise_type',
        'db_table_body_part': '#__fitness_settings_body_part',
        'db_table_difficulty': '#__fitness_settings_difficulty',
        'db_table_equipment': '#__fitness_settings_equipment',
        'db_table_force_type': '#__fitness_settings_force_type',
        'db_table_mechanics_type': '#__fitness_settings_mechanics_type',
        'db_table_target_muscles': '#__fitness_settings_target_muscles',
        

        'default_video_image' : '<?php echo JURI::root();?>administrator/components/com_fitness/assets/images/no_video_image.png',
        'no_video_image_big' : '<?php echo JURI::root();?>administrator/components/com_fitness/assets/images/no_video_big.png',
        'video_upload_folder' : '<?php echo JPATH_ROOT . DS . 'images' . DS . 'Exercise_Library_Videos' . DS  ?>',
        'video_path' : 'images/Exercise_Library_Videos',
        
        'is_superuser' : '<?php echo FitnessFactory::is_superuser($user_id); ?>',
        'is_trainer' : '<?php echo FitnessFactory::is_trainer($user_id); ?>',
        'business_profile_id' : '<?php echo $business_profile_id; ?>'
    };
    
    //status class
    var status_options = {
            'fitness_administration_url' : '<?php echo JURI::root();?>administrator/index.php?option=com_fitness&tmpl=component&<?php echo JSession::getFormToken(); ?>=1',
            'calendar_frontend_url' : '<?php echo JURI::root()?>index.php?option=com_multicalendar&task=load&calid=0',
            'db_table' : '#__dc_mv_events',
            'status_button' : 'status_button',
            'status_button_dialog' : 'status_button_dialog',
            'dialog_status_wrapper' : 'dialog_status_wrapper',
            'dialog_status_template' : '#dialog_status_template',
            'status_button_template' : '#status_button_template',
            'status_button_place' : '#status_button_place_',
            'statuses' : {
                '1' : {'label' : 'PENDING', 'class' : 'event_status_pending', 'email_alias' : ''},
                '2' : {'label' : 'ATTENDED', 'class' : 'event_status_attended', 'email_alias' : 'AppointmentAttended'}, 
                '3' : {'label' : 'CANCELLED', 'class' : 'event_status_cancelled', 'email_alias' : 'AppointmentCancelled'},
                '4' : {'label' : 'LATE CANCEL', 'class' : 'event_status_latecancel', 'email_alias' : 'AppointmentLatecancel'},
                '5' : {'label' : 'NO SHOW', 'class' : 'event_status_noshow', 'email_alias' : 'AppointmentNoshow'}, 
            },
            'statuses2' : {
                '1' : {'label' : 'PENDING', 'class' : 'event_status_pending', 'email_alias' : ''},
                '3' : {'label' : 'CANCELLED', 'class' : 'event_status_cancelled', 'email_alias' : 'AppointmentCancelled'},
                '6' : {'label' : 'COMPLETE', 'class' : 'event_status_complete', 'email_alias' : ''}
            },
            'close_image' : '<?php echo JUri::root() ?>administrator/components/com_fitness/assets/images/close.png',
            'hide_image_class' : 'hideimage',
            'show_send_email' : true,
             setStatuses : function(item_id) {
                var appointment_title = $("#appointment_title_" + item_id).attr('data-appointment');
                if(appointment_title == 'Personal Training') return  this.statuses;
                return  this.statuses2;
            },
            'view' : 'Programs'
        }

    options.status_options = status_options;


    //requireJS options

    require.config({
        baseUrl: '<?php echo JURI::root(); ?>administrator/components/com_fitness/assets/js',
    });


    require(['app'], function(app) {
        app.options = options;
    });
</script>

<script src="<?php echo JURI::root(); ?>administrator/components/com_fitness/assets/js/config.js" type="text/javascript"></script>
<script src="<?php echo JURI::root(); ?>administrator/components/com_fitness/assets/js/main_programs.js" type="text/javascript"></script>
