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
        'base_url_relative': '<?php echo JURI::base(); ?>',
        'ajax_call_url': '<?php echo JURI::root(); ?>administrator/index.php?option=com_fitness&tmpl=component&<?php echo JSession::getFormToken(); ?>=1',
        'user_name': '<?php echo JFactory::getUser()->name; ?>',
        'user_id': '<?php echo JFactory::getUser()->id; ?>',
        'client_id': '<?php echo JFactory::getUser()->id; ?>',
        'db_table_appointments': '#__fitness_categories',
        'db_table_locations': '#__fitness_locations',
        'db_table_session_types': '#__fitness_session_type',
        'db_table_session_focuses': '#__fitness_session_focus',
        'db_table_photos' : '#__fitness_assessments_photos',
        
        'default_image' : '<?php echo JURI::root();?>administrator/components/com_fitness/assets/images/no_image.png',
        'default_video_image' : '<?php echo JURI::root();?>administrator/components/com_fitness/assets/images/no_video_image.png',
        'upload_folder' : '<?php echo JPATH_ROOT . DS . 'images' . DS . 'Assessments_Images' . DS  ?>',
        'img_path' : 'images/Assessments_Images',


        'default_video_image' : '<?php echo JURI::root();?>administrator/components/com_fitness/assets/images/no_video_image.png',
        'no_video_image_big' : '<?php echo JURI::root();?>administrator/components/com_fitness/assets/images/no_video_big.png',
        'video_upload_folder' : '<?php echo JPATH_ROOT . DS . 'images' . DS . 'Assessments_Videos' . DS  ?>',
        'video_path' : 'images/Assessments_Videos',
        
        'current_view' : '<?php echo  JFactory::getApplication()->input->get('view'); ?>',
        'is_superuser' : '<?php echo FitnessFactory::is_superuser($user_id); ?>',
        'is_trainer' : '<?php echo FitnessFactory::is_trainer($user_id); ?>',
        'is_client' : '<?php echo FitnessFactory::is_client($user_id); ?>',
        'is_simple_trainer' : '<?php echo FitnessFactory::is_simple_trainer($user_id); ?>',
        'is_trainer_administrator' : '<?php echo FitnessFactory::is_trainer_administrator($user_id); ?>',
        'is_backend' : '<?php echo JFactory::getApplication()->isAdmin(); ?>',
        'business_profile_id' : '<?php echo $business_profile_id; ?>',
        
        'pr_temp_id' : '<?php echo JRequest::getVar('pr_temp_id') ?>',
        'back_url' : decodeURIComponent('<?php echo JRequest::getVar('back_url') ?>'),
    };
    

    
       //event client status options
        var status_options = {
        'fitness_administration_url' : '<?php echo JURI::root();?>administrator/index.php?option=com_fitness&tmpl=component&<?php echo JSession::getFormToken(); ?>=1',
        'calendar_frontend_url' : '<?php echo JURI::root()?>administrator/index.php?option=com_multicalendar&task=load&calid=0',
        'db_table' : '#__fitness_appointment_clients',
        'status_button' : 'status_button',
        'status_button_dialog' : 'status_button_dialog',
        'dialog_status_wrapper' : 'dialog_status_wrapper',
        'dialog_status_template' : '#dialog_status_template',
        'status_button_template' : '#status_button_template',
        'status_button_place' : '#status_button_place_',
        'statuses' : {
            '1' : {'label' : 'PENDING',         'class' : 'event_status_pending',       'email_alias' : ''},
            '2': {'label' : 'LATE CANCEL',     'class' : 'status_fail',                'email_alias' : 'AppointmentLatecancel'},
            '3' : {'label' : 'DISTINCTION',     'class' : 'status_distinction',         'email_alias' : 'AssessmentDistinction'},
            '4' : {'label' : 'EXCELLENT',       'class' : 'status_pass',                'email_alias' : 'AssessmentExcellent'},
            '5' : {'label' : 'PASS',            'class' : 'status_pass',                'email_alias' : 'AssessmentPass'},
            '6' : {'label' : 'IMPROVEMENT',     'class' : 'status_inprogress',          'email_alias' : 'AssessmentImprove'},
            '7' : {'label' : 'FAIL',            'class' : 'status_fail',                'email_alias' : 'AssessmentFail'},
            '8' : {'label' : 'ATTENDED',        'class' : 'status_pass',                'email_alias' : 'AppointmentAttended'},
            '9':  {'label' : 'CANCELLED',       'class' : 'event_status_cancelled',     'email_alias' : 'AppointmentCancelled'},
            '10' : {'label' : 'ASSESSING',       'class' : 'event_status_assessing',     'email_alias' : 'AssessmentAssessing'},
            '11': {'label' : 'NO SHOW',         'class' : 'status_fail',                'email_alias' : 'AppointmentNoshow'}, 
            '12': {'label' : 'CONFIRMED',       'class' : 'event_status_scheduled',     'email_alias' : 'AppointmentConfirmed'}, 
        },

 
        'hide_image_class' : 'hideimage',
        'show_send_email' : true,
         setStatuses : function(item_id) {
            return  this.statuses;
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
<script src="<?php echo JURI::root(); ?>administrator/components/com_fitness/assets/js/main_assessments.js" type="text/javascript"></script>
