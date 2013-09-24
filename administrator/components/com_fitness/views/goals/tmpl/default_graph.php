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

function getUserGroup($user_id) {
    if(!$user_id) {
        $user_id = &JFactory::getUser()->id;
    }
    $db = JFactory::getDBO();
    $query = "SELECT title FROM #__usergroups WHERE id IN 
        (SELECT group_id FROM #__user_usergroup_map WHERE user_id='$user_id')";
    $db->setQuery($query);
    if(!$db->query()) {
        JError::raiseError($db->getErrorMsg());
    }
    return $db->loadResult();
}

$db = JFactory::getDbo();
$sql = "SELECT DISTINCT user_id FROM #__fitness_clients WHERE state='1'";
if(getUserGroup() != 'Super Users') {
    $user_id = &JFactory::getUser()->id;
    $sql .= " AND (primary_trainer='$user_id' OR other_trainers LIKE '%$user_id%')";
}
$db->setQuery($sql);
$clients = $db->loadObjectList();

function getTrainingPeriods() {
    // Training Period List
    $db = JFactory::getDbo();
    $sql = "SELECT * FROM #__fitness_training_period WHERE state='1'";
    $db->setQuery($sql);
    $training_periods = $db->loadObjectList();

    foreach ($training_periods as $item) {
        $color = '<div style="float:left;margin-right:5px;width:15px; height:15px;background-color:' . $item->color . '" ></div>';
        $name = '<div> ' . $item->name . '</div>';
        $html .= $color . $name .  "<br/>";
    }
    return $html;
}

?>

<div id="content">
    <table>
        <tr>
            <td>
                <div id="choices" style=" width:135px;"></div>
            </td>
            <td>
                <div class="graph-container" style="width:900px;">

                    <div id="placeholder" class="graph-placeholder"></div>

                </div>
            </td>
            <td>
                <fieldset style="width:140px; margin-left: 150px; ">
                    <legend>Training Period Keys</legend>
                    <?php echo getTrainingPeriods();?>
                </fieldset>
            </td>
        </tr>
        <tr>
            <td></td>
            <td>
                <button id="all_goals">All Goals</button>
                <button id="current_primary_goal">Current Primary Goal</button>
                <button id="by_year_previous">Previous Year</button>
                <button id="by_year">Current Year</button>
                <button id="by_year_next">Next Year</button>
                <button id="by_month">Current Month</button>
                <button id="by_week">Current Week</button>
                <button id="by_day">Current Day</button>
            </td>
            <td>
                Select Client to display on Graph: &nbsp;
                <select style="float:right;"  id="graph_client" name="client_id" class="inputbox">
                        <option value=""><?php echo JText::_('-Select-');?></option>
                        <?php 
                            foreach ($clients as $client) {
                                echo '<option value="' . $client->user_id . '">' . JFactory::getUser($client->user_id)->name. '</option>';
                            }
                        ?>
                </select>
            </td>
        </tr>
    </table>
</div>


<script type="text/javascript">
    (function($) {
        // localStorage functions
        function checkLocalStorage() {
            if(typeof(Storage)==="undefined") {
               return false;
            }
            return true;
        }

        function setLocalStorageItem(name, value) {
            if(!checkLocalStorage) return;
            localStorage.setItem(name, value);
        }

        function getLocalStorageItem(name) {
            if(!checkLocalStorage) {
                return false;
            }
            var store_value =  localStorage.getItem(name);
            if(!store_value) return false;
            return store_value;
        }
        //
        
        // on reload
        var client_id = getLocalStorageItem('client_id');
        
        if(client_id) {
              runGraph({'list_type' : ''}, client_id);
            $("#graph_client").val(client_id);
        }
        
        $("#graph_client").change(function(){
            var client_id =  $(this).find(':selected').val();
            setLocalStorageItem('client_id', client_id);
            runGraph({'list_type' : ''}, client_id);
            
        });
        
        // by current primary goal
        $("#current_primary_goal").click(function() {
            $(this).addClass('choosen_link');
            $("#whole, #all_goals, #by_year_previous, #by_year, #by_year_next, #by_month, #by_week, #by_day").removeClass('choosen_link');
            setLocalStorageItem('graph_period', 'options_year');
            runGraph({'list_type' : 'current_primary_goal'}, client_id);
        });
        
        // by all goals
        $("#all_goals").click(function() {
            $(this).addClass('choosen_link');
            $("#whole, #current_primary_goal, #by_year_previous, #by_year, #by_year_next, #by_month, #by_week, #by_day").removeClass('choosen_link');
            setLocalStorageItem('graph_period', 'options');
            runGraph({'list_type' : ''}, client_id);
        });
        
        function runGraph(data, client_id) {
            if(!client_id) return;
           
            $("#choices").html('');
            var url = '<?php echo JUri::base() ?>index.php?option=com_fitness&tmpl=component&<?php echo JSession::getFormToken(); ?>=1';
            
            $.getGraphData(data, client_id, url);
        }

    })($js);
</script>
