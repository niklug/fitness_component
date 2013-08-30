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
?>
<script type="text/javascript">
    $(document).ready(function() {
        $('#form-nutrition_diary').submit(function(event) {

        });
    });
</script>
<div class="fitness_block_wrapper">
    <h2>NUTRITION DIARY</h2>
    <table>
        <tr>
            <td>

            </td>
        </tr>
    </table>

    <div class="fitness_content_wrapper">
        <div class="nutrition_diary-edit front-end-edit">

            <form id="form-nutrition_diary" action="<?php echo JRoute::_('index.php?option=com_fitness&task=nutrition_diary.save'); ?>" method="post" class="form-validate" enctype="multipart/form-data">
                <ul>
                    <li><?php echo $this->form->getLabel('id'); ?>
                        <?php echo $this->form->getInput('id'); ?></li>
                    <li><?php echo $this->form->getLabel('entry_date'); ?>
                        <?php echo $this->form->getInput('entry_date'); ?></li>
                    <li><?php echo $this->form->getLabel('submit_date'); ?>
                        <?php echo $this->form->getInput('submit_date'); ?></li>
                    <li><?php echo $this->form->getLabel('client_id'); ?>
                        <?php echo $this->form->getInput('client_id'); ?></li>
                    <li><?php echo $this->form->getLabel('trainer_id'); ?>
                        <?php echo $this->form->getInput('trainer_id'); ?></li>
                    <li><?php echo $this->form->getLabel('assessed_by'); ?>
                        <?php echo $this->form->getInput('assessed_by'); ?></li>
                    <li><?php echo $this->form->getLabel('primary_goal'); ?>
                        <?php echo $this->form->getInput('primary_goal'); ?></li>
                    <li><?php echo $this->form->getLabel('training_period'); ?>
                        <?php echo $this->form->getInput('training_period'); ?></li>
                    <li><?php echo $this->form->getLabel('nutrition_focus'); ?>
                        <?php echo $this->form->getInput('nutrition_focus'); ?></li>
                    <li><?php echo $this->form->getLabel('status'); ?>
                        <?php echo $this->form->getInput('status'); ?></li>
                    <li><?php echo $this->form->getLabel('score'); ?>
                        <?php echo $this->form->getInput('score'); ?></li>

                    <?php $canState = false; ?>
                    <?php $canState = $canState = JFactory::getUser()->authorise('core.edit.state', 'com_fitness'); ?>				<?php if (!$canState): ?>
                        <li><?php echo $this->form->getLabel('state'); ?>
                            <?php
                            $state_string = 'Unpublish';
                            $state_value = 0;
                            if ($this->item->state == 1):
                                $state_string = 'Publish';
                                $state_value = 1;
                            endif;
                            echo $state_string;
                            ?></li>
                        <input type="hidden" name="jform[state]" value="<?php echo $state_value; ?>" />				<?php else: ?>					<li><?php echo $this->form->getLabel('state'); ?>
                            <?php echo $this->form->getInput('state'); ?></li>
                    <?php endif; ?>
                </ul>

                <div>
                    <button type="submit" class="validate"><span><?php echo JText::_('JSUBMIT'); ?></span></button>
                            <?php echo JText::_('or'); ?>
                    <a href="<?php echo JRoute::_('index.php?option=com_fitness&task=nutrition_diary.cancel'); ?>" title="<?php echo JText::_('JCANCEL'); ?>"><?php echo JText::_('JCANCEL'); ?></a>

                    <input type="hidden" name="option" value="com_fitness" />
                    <input type="hidden" name="task" value="nutrition_diaryform.save" />
                    <?php echo JHtml::_('form.token'); ?>
                </div>
            </form>
        </div>

    </div>
</div>
