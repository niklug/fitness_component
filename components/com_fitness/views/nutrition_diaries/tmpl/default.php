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
<script type="text/javascript">
    function deleteItem(item_id) {
        if (confirm("<?php echo JText::_('COM_FITNESS_DELETE_MESSAGE'); ?>")) {
            document.getElementById('form-nutrition_diary-delete-' + item_id).submit();
        }
    }
</script>
<div class="fitness_wrapper">
    <h2>NUTRITION DIARY</h2>
    <div class="fitness_content_wrapper">
        <div style="float:right;">
            <?php
            $state[] = JHTML::_('select.option', '1', 'Published' );
            $state[] = JHTML::_('select.option', '0', 'Trashed' );
            ?>

            <form action="<?php echo JRoute::_('index.php?option=com_fitness'); ?>" method="post" enctype="multipart/form-data">
                    <div class='filter-select fltrt'>
                            <select name="filter_published" class="inputbox" onchange="this.form.submit()">
                                    <?php echo JHtml::_('select.options', $state, "value", "text", $this->state->get('filter.state'), true);?>
                            </select>
                    </div>
            </form>
        </div>

        <div style="float:right;">
            <?php if (JFactory::getUser()->authorise('core.create', 'com_fitness')): ?>
            <a title="Add New Entry" href="<?php echo JRoute::_('index.php?option=com_fitness&task=nutrition_diary.edit&id=0'); ?>">
                <span  class="add_item"></span>
            </a>
            <?php endif; ?>
        </div>
        <div class="clr"> </div>
        <br/>
        <hr class="orange_line">
        <br/>

        <div class="items">
            <table width="100%">
                <thead>
                    <tr>
                        <th>ENTRY DATE</th>
                        <th>STATUS</th>
                        <th>SCORE</th>
                        <th>ASSESSED BY</th>
                        <th>SUBMITTED</th>
                        <th>TIME</th>
                        <th>VIEW</th>
                        <th>COMMENTS</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($this->items as $item) : ?>
                    <tr>
                        <td>
                            <?php
                            $date = JFactory::getDate($item->entry_date);
                            echo  $date->toFormat('%a %d %b %y');
                            ?>
                        </td>
                        <td>
                            <?php echo $this->model->status_html($item->status); ?>
                        </td>
                        <td>
                            <?php echo $item->score ? $item->score . '%' : '-' ?>
                        </td>
                        <td>
                            <?php
                            if($item->assessed_by) {
                                echo JFactory::getUser($item->assessed_by)->name;
                            } else {
                                echo ' - ';
                            }
                            ?>
                        </td>
                        <td>
                            <?php 
                            $date = JFactory::getDate($item->submit_date);
                            $submit_date = $date->toFormat('%a %d %b %y');
                    
                            if($item->submit_date != '0000-00-00 00:00:00') {
                                echo $submit_date;
                            } else {
                                echo ' - ';
                            }
                            ?>
                        </td>
                        <td>
                            <?php 
                            $submit_time =  $date->format('H:i'); 
                            if($item->submit_date != '0000-00-00 00:00:00') {
                                echo $submit_time;
                            } else {
                                echo ' - ';
                            }
                            ?>
                        </td>
                        <td>
                            <?php if ($item->status != '5') { ?>
                            <a href="<?php echo JRoute::_('index.php?option=com_fitness&task=nutrition_diary.edit&id=' . (int)$item->id); ?>"><span class="preview"></span></a>
                            <?php } ?>
                        </td>
                        <td>
                            <?php echo $item->trainer_comments ? $item->trainer_comments : '-' ?>
                        </td>
                        <td>
                            <a class="jgrid" title="Trash" href="javascript:document.getElementById('form-nutrition_diary-state-<?php echo $item->id; ?>').submit()">
                                <span class="state 
                                      <?php if ($item->state == 1): echo "publish";
                                        else: echo "unpublish"; endif;
                                      ?>
                                    ">
                                </span>
                            </a>
                       </td>
                    </tr>

                    <tr>
                        <td>
                            <form id="form-nutrition_diary-state-<?php echo $item->id ?>" style="display:inline" action="<?php echo JRoute::_('index.php?option=com_fitness&task=nutrition_diary.save'); ?>" method="post" class="form-validate" enctype="multipart/form-data">
                                    <input type="hidden" name="jform[id]" value="<?php echo $item->id; ?>" />
                                    <input type="hidden" name="jform[entry_date]" value="<?php echo $item->entry_date; ?>" />
                                    <input type="hidden" name="jform[submit_date]" value="<?php echo $item->submit_date; ?>" />
                                    <input type="hidden" name="jform[client_id]" value="<?php echo $item->client_id; ?>" />
                                    <input type="hidden" name="jform[trainer_id]" value="<?php echo $item->trainer_id; ?>" />
                                    <input type="hidden" name="jform[assessed_by]" value="<?php echo $item->assessed_by; ?>" />
                                    <input type="hidden" name="jform[primary_goal]" value="<?php echo $item->primary_goal; ?>" />
                                    <input type="hidden" name="jform[training_period]" value="<?php echo $item->training_period; ?>" />
                                    <input type="hidden" name="jform[nutrition_focus]" value="<?php echo $item->nutrition_focus; ?>" />
                                    <input type="hidden" name="jform[status]" value="<?php echo $item->status; ?>" />
                                    <input type="hidden" name="jform[score]" value="<?php echo $item->score; ?>" />
                                    <input type="hidden" name="jform[trainer_comments]" value="<?php echo $item->trainer_comments; ?>" />
                                    <input type="hidden" name="jform[state]" value="<?php echo (int) !((int) $item->state); ?>" />
                                    <input type="hidden" name="option" value="com_fitness" />
                                    <input type="hidden" name="task" value="nutrition_diary.save" />
                                <?php echo JHtml::_('form.token'); ?>
                                </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>

            </table>
            <div class="pagination">
                <p class="counter">
                <?php echo $this->pagination->getPagesCounter(); ?>
                </p>
                <?php echo $this->pagination->getPagesLinks(); ?>
            </div>
        </div>
    </div>
</div>

