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

    <h2>RECIPE DATABASE</h2>
    
    <div id="recipe_mainmenu"></div>
    
    <div id="recipe_submenu"></div>
    
    <div id="recipe_main_container"></div>
    
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
        'user_name' : '<?php echo JFactory::getUser()->name;?>',
        'user_id' : '<?php echo JFactory::getUser()->id;?>',
        'client_id' : '<?php echo JFactory::getUser()->id;?>',
        'item_id' : '<?php echo  $this->item->id ?>',
        
 
        'recipes_db_table' : '#__fitness_nutrition_recipes',
        'ingredients_db_table' : '#__fitness_nutrition_database',
        'recipe_types_db_table' : '#__fitness_recipe_types',
        'recipe_comments_db_table' : '#__fitness_nutrition_recipes_comments',
        'recipes_favourites_db_table' : '#__fitness_nutrition_recipes_favourites',
        'default_image' : '<?php echo JURI::root();?>administrator/components/com_fitness/assets/images/no_image.png',
        'default_video_image' : '<?php echo JURI::root();?>administrator/components/com_fitness/assets/images/no_video_image.png',
        'no_video_image_big' : '<?php echo JURI::root();?>administrator/components/com_fitness/assets/images/no_video_big.png',
        'upload_folder' : '<?php echo JPATH_ROOT . DS . 'images' . DS . 'Recipe_Images' . DS  ?>',
        'video_upload_folder' : '<?php echo JPATH_ROOT . DS . 'images' . DS . 'Recipe_Videos' . DS  ?>',
        'img_path' : 'images/Recipe_Images',
        'video_path' : 'images/Recipe_Videos',
        'add_diary_options' : add_diary_options,
        
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
        'PENDING_RECIPE_STATUS' :     {id : '<?php echo FitnessHelper::PENDING_RECIPE_STATUS ?>',       name : 'PENDING'},
        'APPROVED_RECIPE_STATUS':     {id : '<?php echo FitnessHelper::APPROVED_RECIPE_STATUS ?>',      name : 'APPROVED'},
        'NOTAPPROVED_RECIPE_STATUS' : {id : '<?php echo FitnessHelper::NOTAPPROVED_RECIPE_STATUS ?>',   name : 'NOTAPPROVED'},
        'ASSESSING_RECIPE_STATUS' : {id : '<?php echo FitnessHelper::ASSESSING_RECIPE_STATUS ?>',   name : 'ASSESSING'},
    };
    
     var status_options = {
        'fitness_administration_url' : '<?php echo JURI::root();?>administrator/index.php?option=com_fitness&tmpl=component&<?php echo JSession::getFormToken(); ?>=1',
        'calendar_frontend_url' : '<?php echo JURI::root()?>index.php?option=com_multicalendar&task=load&calid=0',
        'db_table' : '#__fitness_nutrition_recipes',
        'status_button' : 'status_button',
        'status_button_dialog' : 'status_button_dialog',
        'dialog_status_wrapper' : 'dialog_status_wrapper',
        'dialog_status_template' : '#dialog_status_template',
        'status_button_template' : '#status_button_template',
        'status_button_place' : '#status_button_place_',

        'statuses' : {
            '<?php echo FitnessHelper::PENDING_RECIPE_STATUS ?>' : {'label' :       statuses.PENDING_RECIPE_STATUS.name,        'class' : 'recipe_status_pending',      'email_alias' : ''},
            '<?php echo FitnessHelper::APPROVED_RECIPE_STATUS ?>' : {'label' :      statuses.APPROVED_RECIPE_STATUS.name,       'class' : 'recipe_status_approved',     'email_alias' : ''}, 
            '<?php echo FitnessHelper::NOTAPPROVED_RECIPE_STATUS ?>' : {'label' :   statuses.NOTAPPROVED_RECIPE_STATUS.name,    'class' : 'recipe_status_notapproved',  'email_alias' : ''},
            '<?php echo FitnessHelper::ASSESSING_RECIPE_STATUS ?>' : {'label' :   statuses.ASSESSING_RECIPE_STATUS.name,    'class' : 'goal_status_assessing',  'email_alias' : ''}
        },

        'statuses2' : {},
        'close_image' : '<?php echo JUri::root() ?>administrator/components/com_fitness/assets/images/close.png',
        'hide_image_class' : 'hideimage',
        'show_send_email' : true,
        setStatuses : function(item_id) {
            return this.statuses;
        },
        'set_updater' : true,
        'user_id' : '<?php echo JFactory::getUser()->id;?>',
        'view' : 'NutritionRecipe'
    }
    
    options.statuses = statuses;
    options.status_options = status_options;

        
    //requireJS options

    require.config({
        baseUrl: '<?php echo JURI::root();?>administrator/components/com_fitness/assets/js',
    });


    require(['app'], function(app) {
            app.options = options;
    });
</script>

<script src="<?php echo JURI::root();?>administrator/components/com_fitness/assets/js/config.js" type="text/javascript"></script>
<script src="<?php echo JURI::root();?>administrator/components/com_fitness/assets/js/main_recipe_database_frontend.js" type="text/javascript"></script>




