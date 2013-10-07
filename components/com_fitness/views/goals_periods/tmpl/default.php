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
<div style="opacity: 1;" class="fitness_wrapper">
    <h2>GOALS & TRAINING PERIODS</h2>
    <div class="fitness_block_wrapper">
        <div  style="width:250px; float: left;">
            <h3>MY TRAINING PLAN</h3>
        </div>
        <div  style="width:500px; float:right; text-align: right;margin-top: 4px;margin-right: 20px;">
            <a id="whole" href="javascript:void(0)">[All Goals]</a>
            <a  id="by_year_previous" href="javascript:void(0)">[Previous Year]</a>
            <a  id="by_year" href="javascript:void(0)">[Current Year]</a>
            <a  id="by_year_next" href="javascript:void(0)">[Next Year]</a>
            <a  id="by_month" href="javascript:void(0)">[Current Month]</a>
        </div>
        <div class="clr"></div>
        <hr class="orange_line">
        <div class="internal_wrapper">
            <table>
                <tr>
                    <td>
                        <div class="graph-container" style="width:780px;">
                            <div id="placeholder" class="graph-placeholder"></div>
                        </div>
                    </td>
                    <td>
                        <fieldset style="width:140px !important;">
                            <legend class="grey_title">Training Period Keys</legend>
                            <?php echo $this->model->getTrainingPeriods();?>
                        </fieldset>
                    </td>
                </tr>
            </table>
        </div>

    </div>
</div>

<div id="goal_container" class="fitness_wrapper">

</div>



<script type="text/javascript">
    (function($) {
        var options = {
            'fitness_frontend_url' : '<?php echo JURI::root();?>index.php?option=com_fitness&tmpl=component&<?php echo JSession::getFormToken(); ?>=1',
            'calendar_frontend_url' : '<?php echo JURI::root()?>index.php?option=com_multicalendar&task=load&calid=0',
            'pending_review_text' : 'Pending Review',
            'user_name' : '<?php echo JFactory::getUser()->name;?>',
            'user_id' : '<?php echo JFactory::getUser()->id;?>',
            'goals_db_table' : '#__fitness_goals',
            'minigoals_db_table' : '#__fitness_mini_goals',
            'goals_comments_db_table' : '#__fitness_goal_comments',
            'minigoals_comments_db_table' : '#__fitness_mini_goal_comments',
        };

        $.goals_frontend(options);

    })($js);
</script>



