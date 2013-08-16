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

JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.keepalive');
// Import CSS
$document = JFactory::getDocument();
$document->addStyleSheet('components/com_fitness/assets/css/fitness.css');
?>
<style type="text/css">
    /* Temporary fix for drifting editor fields */
    .adminformlist li {
        clear: both;
    }
</style>
<script type="text/javascript">
    $(document).ready(function(){
        Joomla.submitbutton = function(task)
        {
            if (task == 'nutrition_plan.cancel') {
                Joomla.submitform(task, document.getElementById('nutrition_plan-form'));
            }
            else{

                if (task != 'nutrition_plan.cancel' && document.formvalidator.isValid(document.id('nutrition_plan-form'))) {

                    Joomla.submitform(task, document.getElementById('nutrition_plan-form'));
                }
                else {
                    alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED')); ?>');
                }
            }
        }
    });
</script>

<form action="<?php echo JRoute::_('index.php?option=com_fitness&layout=edit&id=' . (int) $this->item->id); ?>" method="post" enctype="multipart/form-data" name="adminForm" id="nutrition_plan-form" class="form-validate">
    <div class="width-100 fltlft">
        <table width="100%">
            <tr>
                <td width="30%">
                    <fieldset class="adminform">
                        <legend>CLIENT & TRAINER(S)</legend>
                            <?php
                            $db = JFactory::getDbo();

                            $sql = "SELECT id AS value, username AS text FROM #__users INNER JOIN #__user_usergroup_map ON #__user_usergroup_map.user_id=#__users.id WHERE #__user_usergroup_map.group_id=(SELECT id FROM #__usergroups WHERE title='Trainers')";
                            $db->setQuery($sql);
                            if(!$db->query()) {
                                JError::raiseError($db->getErrorMsg());
                            }
                            $primary_trainerlist = $db->loadObjectList();
                            ?>

                            <li><?php echo $this->form->getLabel('trainer_id'); ?>
                                <div class='filter-select fltrt'>
                                    <select id="jform_trainer_id" class="inputbox" name="jform[trainer_id]">
                                        <option value=""><?php echo JText::_('-Select-');?></option>
                                        <?php echo JHtml::_('select.options', $primary_trainerlist, "value", "text", $this->state->get('filter.primary_trainer'), true);?>
                                    </select>
                                </div>
                            </li>
                            <li><?php echo $this->form->getLabel('client_id'); ?>
                                <div class='filter-select fltrt'>
                                    <select id="jform_client_id" class="inputbox" name="jform[client_id]">
                                        <option value=""><?php echo JText::_('-Select-');?></option>
                                    </select>
                                </div>
                            </li>
                            <li>
                                <label id="jform_trainers_id-lbl" class="" for="jform_trainers_id">Secondary Trainers</label>
                                <div class='filter-select fltrt'>
                                    <div id="secondary_trainers"></div>
                                </div>
                            </li>
                        </ul>
                    </fieldset>
                </td>
                <td width="70%">
                    <fieldset  class="adminform">
                        <legend>NUTRITION PLAN (PERIODIZATION)</legend>
                        
                    </fieldset>
                </td>
            </tr>
            
        </table>
        
        <fieldset class="adminform">
            <legend><?php echo JText::_('COM_FITNESS_LEGEND_NUTRITION_PLAN'); ?></legend>
            <ul class="adminformlist">
                <li><?php echo $this->form->getLabel('active_start'); ?>
                <?php echo $this->form->getInput('active_start'); ?></li>
                <li><?php echo $this->form->getLabel('active_finish'); ?>
                <?php echo $this->form->getInput('active_finish'); ?></li>
                <li><?php echo $this->form->getLabel('active'); ?>
                <?php echo $this->form->getInput('active'); ?></li>
                <li><?php echo $this->form->getLabel('force_active'); ?>
                <?php echo $this->form->getInput('force_active'); ?></li>
                <li><?php echo $this->form->getLabel('primary_goal'); ?>
                <?php echo $this->form->getInput('primary_goal'); ?></li>
                <li><?php echo $this->form->getLabel('nutrition_focus'); ?>
                <?php echo $this->form->getInput('nutrition_focus'); ?></li>
                <li><?php echo $this->form->getLabel('state'); ?>
                <?php echo $this->form->getInput('state'); ?></li>


            </ul>
        </fieldset>
    </div>
    <input type="hidden" name="task" value="" />
    <?php echo JHtml::_('form.token'); ?>
    <div class="clr"></div>
</form>

<script type="text/javascript">
    
    // set options
    var options = {
        'trainer_select' : $("#jform_trainer_id"),
        'client_select' : $("#jform_client_id"),
        'secondary_trainers_wrapper' : $("#secondary_trainers"),
        'calendar_frontend_url' : '<?php echo JURI::root();?>index.php?option=com_multicalendar&task=load&calid=0'
    }
    // cteate main object
    var nutrition_plan = new NutritionPlan(options);
    
    // attach listeners on document ready
    $(document).ready(function(){
       nutrition_plan.setEventListeners();
    });
    
    // Constructor
    function NutritionPlan(options) {
        this.options = options;
    }
    
    NutritionPlan.prototype.setEventListeners = function() {
        var self = this;
        // on trainer select
        this.options.trainer_select.on('change', function(){
            self.trainerChangeEvent($(this));
        });
        
        // on client select
        this.options.client_select.on('change', function(){
            self.clientChangeEvent($(this));
        });
    }
    
    NutritionPlan.prototype.trainerChangeEvent = function(e) {
        this.getTrainerClients(e);
        this.populateSecondaryTrainers({}, this.options.secondary_trainers_wrapper);
    }
    
    NutritionPlan.prototype.clientChangeEvent = function(e) {
        this.getClientSecondaryTrainers(e);
    }
    
    NutritionPlan.prototype.getTrainerClients = function(e) {
        var trainer_id = e.find(":selected").val();
        if(!trainer_id) return;
        var url = this.options.calendar_frontend_url;
        var self = this;
        $.ajax({
            type : "POST",
            url : url,
            data : {
               method : 'get_clients',
               trainer_id : trainer_id
            },
            dataType : 'json',
            success : function(response) {
                
                if(!response.status.success) {
                    self.populateSelect({}, self.options.client_select);
                    alert(response.status.message);
                    return;
                }
                self.populateSelect(response.data, self.options.client_select);
            },
            error: function(XMLHttpRequest, textStatus, errorThrown)
            {
                alert("error");
            }
        });
      
    }
    
    NutritionPlan.prototype.getClientSecondaryTrainers = function(e) {
        var client_id = e.find(":selected").val();
        if(!client_id) return;
        var url = this.options.calendar_frontend_url;
        var self = this;
        $.ajax({
            type : "POST",
            url : url,
            data : {
               method : 'get_trainers',
               client_id : client_id,
               secondary_only : true
            },
            dataType : 'json',
            success : function(response) {
                if(!response.status.success) {
                    alert(response.status.message);
                    return;
                }
                self.populateSecondaryTrainers(response.data, self.options.secondary_trainers_wrapper);
            },
            error: function(XMLHttpRequest, textStatus, errorThrown)
            {
                alert("error");
            }
        });
      
    }
    
    NutritionPlan.prototype.populateSelect = function(data, destination) {
        var html = '<option  value="">-Select-</option>';
        $.each(data, function(index, value) {
             if(index) {
                html += '<option  value="' + index + '">' +  value + '</option>';
            }
        });
        destination.html(html);
    };
    
    
    NutritionPlan.prototype.populateSecondaryTrainers = function(data, destination) {
        var html = '<ul>';
        $.each(data, function(index, value) {
             if(index) {
                html += '<li>' + value + '</li>';
            }
        });
        html += '</ul>';
        destination.html(html);
    };
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
</script>