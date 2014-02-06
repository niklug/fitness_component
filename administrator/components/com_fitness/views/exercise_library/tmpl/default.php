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
<div id="main_menu" style="float: right;"></div>
<div class="width-100 fltlft">
    
    <fieldset class="adminform">
        <legend>Exercise Details</legend>
        <div id="exercise_details_wrapper">
            <table width="100%">
                <tr>
                    <td width="100">
                        <label>Exercise Name</label>
                    </td>
                    <td id="exercise_name_wrapper" >
                        <input id="exercise_name" size="50" type="text" title="" value="" name="exercise_name">
                    </td>
                </tr>
            </table>
            
            <div id="select_filter_wrapper"></div>

        </div>
    </fieldset>
</div>






<script type="text/javascript">
       
    var options = {
        'fitness_frontend_url' : '<?php echo JURI::root();?>index.php?option=com_fitness&tmpl=component&<?php echo JSession::getFormToken(); ?>=1',
        'calendar_frontend_url' : '<?php echo JURI::root()?>index.php?option=com_multicalendar&task=load&calid=0',
        'base_url' : '<?php echo JURI::root();?>',
        'ajax_call_url' : '<?php echo JURI::root();?>administrator/index.php?option=com_fitness&tmpl=component&<?php echo JSession::getFormToken(); ?>=1',
        'user_name' : '<?php echo JFactory::getUser()->name;?>',
        'user_id' : '<?php echo JFactory::getUser()->id;?>',
        'client_id' : '<?php echo JFactory::getUser()->id;?>',
        'db_table_exercise_type' : '#__fitness_settings_exercise_type',
        'db_table_body_part' : '#__fitness_settings_body_part',
        'db_table_difficulty' : '#__fitness_settings_difficulty',
        'db_table_equipment' : '#__fitness_settings_equipment',
        'db_table_force_type' : '#__fitness_settings_force_type',
        'db_table_mechanics_type' : '#__fitness_settings_mechanics_type',
        'db_table_target_muscles' : '#__fitness_settings_target_muscles'
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
<script src="<?php echo JURI::root();?>administrator/components/com_fitness/assets/js/main_exercise_library.js" type="text/javascript"></script>
