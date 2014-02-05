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

<div class="width-50 fltlft">
    <fieldset class="adminform">
        <legend>Exercise Details</legend>
        <div id="exercise_details_wrapper">
            <table width="100%">
                <tr>
                    <td width="150">
                        <label>Exercise Name</label>
                    </td>
                    <td id="exercise_name_wrapper" colspan="2">
                        <input id="exercise_name" size="50" type="text" title="" value="" name="exercise_name">
                    </td>
                </tr>
                
                <tr>
                    <td width="25%">
                        <label>Exercise Type</label>
                    </td>
                    <td width="25%" id="exercise_type_filter_wrapper">
                        
                    </td>
                    <td width="25%">
                        <label>Force Type</label>
                    </td>
                    <td width="25%" id="force_type_filter_wrapper">
                        
                    </td>
                </tr>
                
                <tr>
                    <td>
                        <label>Difficulty</label>
                    </td>
                    <td id="difficulty_filter_wrapper">
                        
                    </td>
                    <td>
                        <label>Mechanics Type</label>
                    </td>
                    <td id="mechanics_type_filter_wrapper">
                        
                    </td>
                </tr>
            </table>
            <br/>
            <table width="100%">
                <tr>
                    <td width="33%">
                        <label>Body Part(s)</label>
                    </td>
                    <td width="33%">
                        <label>Target Muscle(s)</label>
                    </td>
                    <td width="33%">
                        <label>Equipment Type</label>
                    </td>
                </tr>
                <tr>
                    <td id="body_part_filter_wrapper">
                        
                    </td>
                    <td id="target_mucle_filter_wrapper">
                        
                    </td>
                    <td id="equipment_filter_wrapper">
                        
                    </td>
                </tr>
            </table>
        </div>
    </fieldset>
</div>






<script type="text/javascript">
       
    var options = {
        'fitness_frontend_url' : '<?php echo JURI::root();?>index.php?option=com_fitness&tmpl=component&<?php echo JSession::getFormToken(); ?>=1',
        'calendar_frontend_url' : '<?php echo JURI::root()?>index.php?option=com_multicalendar&task=load&calid=0',
        'base_url' : '<?php echo JURI::root();?>',
        'ajax_call_url' : '<?php echo JURI::root();?>index.php?option=com_fitness&tmpl=component&<?php echo JSession::getFormToken(); ?>=1',
        'user_name' : '<?php echo JFactory::getUser()->name;?>',
        'user_id' : '<?php echo JFactory::getUser()->id;?>',
        'client_id' : '<?php echo JFactory::getUser()->id;?>'
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
