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

JHtml::_('behavior.keepalive');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');

//Load admin language file
$lang = JFactory::getLanguage();
$lang->load('com_fitness', JPATH_ADMINISTRATOR);

$user = &JFactory::getUser();

$trainer_id = $item->trainer_id ? $item->trainer_id : $this->active_plan_data->trainer_id;

$goal_category_id = $item->goal_category_id ? $item->goal_category_id : $this->active_plan_data->primary_goal_id;

$training_period_id = $item->training_period_id ? $item->training_period_id : $this->active_plan_data->training_period_id;

$nutrition_focus = $item->nutrition_focus ? $item->nutrition_focus : $this->active_plan_data->nutrition_focus;

$submitted = false;
if ($this->item->submit_date && ($this->item->submit_date != '0000-00-00 00:00:00')) {
    $submitted = true;
}
?>
<script type="text/javascript">
    $(document).ready(function() {
        $('#form-nutrition_diary').submit(function(event) {

        });
    });
</script>
<div class="fitness_wrapper">
    <form id="form-nutrition_diary" action="<?php echo JRoute::_('index.php?option=com_fitness&task=nutrition_diary.save'); ?>" method="post" class="form-validate" enctype="multipart/form-data">
        <h2>NUTRITION DIARY</h2>
        <table width="100%">
            <tr>
                <td width="40%">
                    <div class="fitness_block_wrapper" style="min-height:200px;">
                        <h3>CLIENT & TRAINERS</h3>
                        <hr class="orange_line">
                        <div class="internal_wrapper">
                            <table width="100%">
                                <tr>
                                    <td>
                                        Client Name
                                    </td>
                                    <td>
                                        <span class="grey_title">
                                            <?php echo $user->name; ?>
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        Primary Trainer
                                    </td>
                                    <td>
                                        <span class="grey_title">
                                            <?php echo JFactory::getUser($trainer_id)->name ?>
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        Secondary Trainers
                                    </td>
                                    <td>
                                        <span class="grey_title">
                                            <?php
                                            foreach ($this->secondary_trainers as $trainer) {
                                                echo $trainer . "<br/>";
                                            };
                                            ?>
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </td>
                <td>
                    <div class="fitness_block_wrapper" style="min-height:200px;">
                        <h3>ENTRY DETAILS</h3>
                        <hr class="orange_line">
                        <div class="internal_wrapper">
                            <table width="100%">
                                <tr>
                                    <td>
                                        Date of Entry
                                    </td>
                                    <td>
                                        <span class="grey_title">
                                            <?php
                                            if ($submitted) {
                                                $jdate = new JDate($this->item->entry_date);
                                                echo $jdate->toFormat('%A %d %B %Y');
                                            } else {
                                                echo $this->form->getInput('entry_date'); 
                                            }
                                            
                                            ?>
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        Date Created
                                    </td>
                                    <td>
                                        <span class="grey_title">
                                            <?php echo $this->form->getInput('created'); ?>
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        Date Submitted
                                    </td>
                                    <td>
                                        <span class="grey_title">
                                            <?php
                                            if ($submitted) {
                                                $jdate = new JDate($this->item->submit_date);
                                                echo $jdate->format(JText::_('DATE_FORMAT_LC2'));
                                            }
                                            ?>
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        Assessed By
                                    </td>
                                    <td>
                                        <span class="grey_title">
                                            <?php
                                            if ($this->item->assessed_by) {
                                                echo JFactory::getUser($this->item->assessed_by)->name;
                                            }
                                            ?>
                                        </span>
                                    </td>
                                </tr>

                            </table>
                        </div>
                    </div>

                </td>
            </tr>

            <tr>
                <td colspan="2">
                    <div class="fitness_block_wrapper" style="min-height:150px;">
                        <h3>PRIMARY GOAL</h3>
                        <hr class="orange_line">
                        <div class="internal_wrapper">
                            <table width="100%">
                                <tr>
                                    <td>
                                        <table width="50%">
                                            <tr>
                                                <td>
                                                    Primary Goal
                                                </td>
                                                <td>
                                                    <span class="grey_title">
                                                        <?php echo $this->model->getGoalName($goal_category_id); ?>
                                                    </span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    Training Period
                                                </td>
                                                <td>
                                                    <span class="grey_title">
                                                        <?php echo $this->model->getTrainingPeriodName($training_period_id); ?>
                                                    </span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    Nutrition Focus
                                                </td>
                                                <td>
                                                    <span class="grey_title">
                                                        <?php echo $this->model->getNutritionFocusName($nutrition_focus); ?>
                                                    </span>
                                                </td>
                                            </tr>
                                        </table> 
                                    </td>
                                    <td>
                                        <table width="50%">
                                            <tr>
                                                <td>
                                                    <?php
                                                    if($this->item->trainer_comments) {
                                                        echo 'Trainer Comments';
                                                    }
                                                    ?>
                                                </td>
                                                <td>
                                                    <?php echo $this->item->trainer_comments;?>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>


                        </div>
                    </div>
                </td>

            </tr>
        </table>



        <input type="hidden" name="jform[id]" value="<?php echo $this->item->id; ?>" />
        <input type="hidden" name="jform[client_id]" value="<?php echo $user->id; ?>" />
        <input type="hidden" name="jform[trainer_id]" value="<?php echo $trainer_id ?>" />
        <input type="hidden" name="jform[goal_category_id]" value="<?php echo $goal_category_id; ?>" />
        <input type="hidden" name="jform[training_period_id]" value="<?php echo $training_period_id; ?>" />
        <input type="hidden" name="jform[nutrition_focus]" value="<?php echo $nutrition_focus; ?>" />



        <input type="hidden" name="jform[state]" value="1" />


            <div>
                <?php
                    if (!$submitted) {
                ?>
                    <?php
                        if ($this->item->id) {
                    ?>
                    <input type="submit" class="validate" name="submit" value="Submit" />
                    <?php
                        }
                    ?>

                    <input type="submit" class="validate" name="save" value="Save" />

                    <input type="submit" class="validate" name="save_close" value="Save&Close" />

                    <?php
                        if (!$submitted && $this->item->id) {
                    ?>
                            <input type="submit" class="validate" name="delete" value="Delete" />
                    <?php
                        }
                    ?>
                <?php
                    }
                ?>               
                <a href="<?php echo JRoute::_('index.php?option=com_fitness&task=nutrition_diary.cancel'); ?>" title="Close Entry without saving">Close</a>

                <input type="hidden" name="option" value="com_fitness" />
                <input type="hidden" name="task" value="nutrition_diaryform.save" />
                <?php echo JHtml::_('form.token'); ?>
            </div>
    </form>
    
    <?php

    //var_dump($this->item->id);

    ?>

</div>


