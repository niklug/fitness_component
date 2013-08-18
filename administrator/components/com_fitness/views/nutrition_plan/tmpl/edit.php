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

<form action="<?php echo JRoute::_('index.php?option=com_fitness&layout=edit&id=' . (int) $this->item->id); ?>" method="post" enctype="multipart/form-data" name="adminForm" id="nutrition_plan-form" class="form-validate">
    <div class="width-100 fltlft">
        <table width="100%">
            <tr>
                <td width="30%">
                    <fieldset style="height:410px;" class="adminform">
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
                        <ul>
                            <li><?php echo $this->form->getLabel('trainer_id'); ?>
                                <div class='filter-select fltrt'>
                                    <select id="jform_trainer_id" class="inputbox" name="jform[trainer_id]">
                                        <option value=""><?php echo JText::_('-Select-');?></option>
                                        <?php echo JHtml::_('select.options', $primary_trainerlist, "value", "text", $this->item->trainer_id, true);?>
                                    </select>
                                </div>
                            </li>
                            <li><?php echo $this->form->getLabel('client_id'); ?>
                                <div class='filter-select fltrt'>
                                    <select id="jform_client_id" class="inputbox" name="jform[client_id]">
                                        <?php
                                        if($this->item->client_id) {
                                            echo '<option value="' . $this->item->client_id . '">'. JFactory::getUser($this->item->client_id)->name .'</option>';
                                        } else {
                                            echo '<option value="">' . JText::_('-Select-')  . '</option>';
                                        }
                                        ?>
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
                        <table>
                            <tr>
                                <td>
                                    <?php echo $this->form->getLabel('primary_goal'); ?>
                                    <select id="jform_primary_goal" class="inputbox" name="jform[primary_goal]">
                                        <?php
                                        if($this->item->primary_goal) {
                                            echo '<option value="' . $this->item->primary_goal. '">'. $this->getPrimaryGoalName($this->item->primary_goal) .'</option>';
                                        } else {
                                            echo '<option value="">' . JText::_('-Select-')  . '</option>';
                                        }
                                        ?>
                                    </select>
                                </td>
                                <td></td>
                                <td></td>
                                <td>
                                    <?php echo $this->form->getLabel('state'); ?>
                                    <?php echo $this->form->getInput('state'); ?>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="3">
                                    <?php echo $this->form->getLabel('training_period'); ?>
                                    <?php echo $this->form->getInput('training_period'); ?>
                                </td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>
                                    <?php echo $this->form->getLabel('active_start'); ?>
                                    <?php echo $this->form->getInput('active_start'); ?>
                                </td>
                                <td>
                                    <?php echo $this->form->getLabel('force_active'); ?>
                                </td>
                                <td>
                                    <?php echo $this->form->getInput('force_active'); ?>
                                </td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>
                                    <?php echo $this->form->getLabel('active_finish'); ?>
                                    <?php echo $this->form->getInput('active_finish'); ?>
                                </td>
                                <td>
                                    <?php echo $this->form->getLabel('no_end_date'); ?>
                                </td>
                                <td>
                                    <?php echo $this->form->getInput('no_end_date'); ?>
                                </td>
                                <td></td>
                            </tr>
                            <tr>
                                <td colspan="3">
                                    <?php echo $this->form->getLabel('nutrition_focus'); ?>
                                    <?php echo $this->form->getInput('nutrition_focus'); ?>
                                </td>
                                <td></td>
                            </tr>
                            
                            <td colspan="4">
                                    <?php echo $this->form->getLabel('trainer_comments'); ?>
                                    <?php echo $this->form->getInput('trainer_comments'); ?>
                                </td>
                            </tr>
                        </table>
                    </fieldset>
                </td>
            </tr>
            
        </table>
    </div>
        
    <div style="display:none;">
    <?php echo $this->form->getLabel('created'); ?>
    <?php echo $this->form->getInput('created'); ?>
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
        'primary_goal_select' : $("#jform_primary_goal"),
        'training_period_select' : $("#jform_training_period"),
        'calendar_frontend_url' : '<?php echo JURI::root();?>index.php?option=com_multicalendar&task=load&calid=0',
        'fitness_administration_url' : '<?php echo JURI::root();?>administrator/index.php?option=com_fitness&tmpl=component&<?php echo JSession::getFormToken(); ?>=1',
        'client_selected' : '<?php echo $this->item->client_id;?>',
        'primary_goal_selected' : '<?php echo $this->item->primary_goal;?>',
        'active_start_field' : $("#jform_active_start"),
        'active_finish_field' : $("#jform_active_finish"),
        'active_start_img' : $("#jform_active_start_img"),
        'active_finish_img' : $("#jform_active_finish_img"),
        'force_active_yes' : $("#jform_force_active0"),
        'force_active_no' : $("#jform_force_active1"),
        'force_active_value' : '<?php echo $this->item->force_active;?>',
        'active_finish_value' : '<?php echo $this->item->active_finish;?>',
        'no_end_date_label': $("#jform_no_end_date-lbl"),
        'no_end_fieldset' : $("#jform_no_end_date"),
        'no_end_date_yes' : $("#jform_no_end_date0"),
        'no_end_date_no' : $("#jform_no_end_date1"),
        'max_possible_date' : '9999-12-31'
    }
    // cteate main object
    var nutrition_plan = new NutritionPlan(options);
    
    // attach listeners on document ready
    $(document).ready(function(){
       nutrition_plan.setEventListeners();
       
       // populate clients select onload
       nutrition_plan.getTrainerClients(options.trainer_select, function(output) {
            var selected_option = nutrition_plan.options.client_selected
            nutrition_plan.populateSelect(output, nutrition_plan.options.client_select, selected_option);
        });
       
       // populate secondary trainers  onload
       nutrition_plan.getClientSecondaryTrainers(options.client_select, function(output) {
            nutrition_plan.populateSecondaryTrainers(output, nutrition_plan.options.secondary_trainers_wrapper);
        });
       
       // populate primary goals select onload
       nutrition_plan.getClientPrimaryGoals(options.client_select, function(output) {
            var selected_option = nutrition_plan.options.primary_goal_selected;
            nutrition_plan.populateSelect(output, nutrition_plan.options.primary_goal_select, selected_option);
        });
        
        // set  Active Start/Finish field inactive/active and No End Date
        
        if(parseInt(nutrition_plan.options.force_active_value)) {
            nutrition_plan.forceActiveYes();
        } else {
            nutrition_plan.forceActiveNo();
        }

        
        if(nutrition_plan.options.active_finish_value == nutrition_plan.options.max_possible_date) {
            nutrition_plan.forceNoEndDateYes();
            nutrition_plan.options.no_end_date_yes.attr('checked', true);

        } else {
            nutrition_plan.forceNoEndDateNo();
            nutrition_plan.options.no_end_date_yes.attr('checked', false);
        }

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
        
        // on primary goal select
        this.options.primary_goal_select.on('change', function(){
            self.primaryGoalChangeEvent($(this));
        });
        
        // on Active start 'yes' click
        this.options.force_active_yes.on('click', function(){
            self.forceActiveYes();
        });
        
        // on Active start 'no' click
        this.options.force_active_no.on('click', function(){
            self.forceActiveNo();
        });
        
        // on No End Date 'yes' click
        this.options.no_end_date_yes.on('click', function(){
            self.forceNoEndDateYes();
        });
        
        // on No End Date start 'no' click
        this.options.no_end_date_no.on('click', function(){
            self.forceNoEndDateNo();
        });
        
    }
    
    NutritionPlan.prototype.forceActiveYes = function() {
        this.options.active_start_img.css('display', 'block');
        this.options.active_start_field.attr('readonly', false);
        this.options.active_finish_img.css('display', 'block');
        this.options.active_finish_field.attr('readonly', false);
        this.options.no_end_date_label.show();
        this.options.no_end_fieldset.show();
    }
    
    NutritionPlan.prototype.forceActiveNo = function() {
        this.options.active_start_img.css('display', 'none');
        this.options.active_start_field.attr('readonly', true);
        this.options.active_finish_img.css('display', 'none');
        this.options.active_finish_field.attr('readonly', true);
        this.options.no_end_date_label.hide();
        this.options.no_end_fieldset.hide(); 
        //this.options.no_end_date_active_input.val('0');
    }
    
    NutritionPlan.prototype.forceNoEndDateNo = function() {
        this.options.active_finish_img.css('display', 'block');
        this.options.active_finish_field.attr('readonly', false);
    }
    
    NutritionPlan.prototype.forceNoEndDateYes = function() {
        this.options.active_finish_img.css('display', 'none');
        this.options.active_finish_field.attr('readonly', true); 
        this.options.active_finish_field.val(this.options.max_possible_date); 
        //this.options.active_finish_field.css('display', 'none'); 
    }
    
   
    NutritionPlan.prototype.trainerChangeEvent = function(e) {
        // reset fields
        this.populateSecondaryTrainers({}, this.options.secondary_trainers_wrapper);
        this.populateSelect({}, this.options.client_select);
        this.populateSelect({}, this.options.primary_goal_select);
        this.options.active_start_field.val('');
        this.options.active_finish_field.val('');
        //
        var self = this;
        this.getTrainerClients(e, function(output) {
            if(output) {
                var selected_option = self.options.client_selected
                self.populateSelect(output, self.options.client_select, selected_option);
            }
        });
    }
    
    NutritionPlan.prototype.clientChangeEvent = function(e) {
        // reset fields
        this.populateSecondaryTrainers({}, this.options.secondary_trainers_wrapper);
        this.populateSelect({}, this.options.primary_goal_select);
        this.options.active_start_field.val('');
        this.options.active_finish_field.val('');
        //
        var self = this;
        this.getClientSecondaryTrainers(e, function(output) {
            self.populateSecondaryTrainers(output, self.options.secondary_trainers_wrapper);
        });
        this.getClientPrimaryGoals(e, function(output) {
            //console.log(output);
            if(output) {
                var selected_option = self.options.primary_goal_selected;
                self.populateSelect(output, self.options.primary_goal_select, selected_option);
            }
        });
    }
    
    NutritionPlan.prototype.primaryGoalChangeEvent = function(e) {
        this.options.training_period_select.val('');
        this.options.active_start_field.val('');
        this.options.active_finish_field.val('');
        var self = this;
        this.getGoalData(e, function(output) {
            if(output) {
                self.options.training_period_select.val(output.training_period_name);
                self.options.active_start_field.val(output.start_date);
                self.options.active_finish_field.val(output.deadline);
            }
        });
    }
    
    NutritionPlan.prototype.getTrainerClients = function(e, handleData) {
        var trainer_id = e.find(":selected").val();
        if(!trainer_id) return;
        var url = this.options.calendar_frontend_url;
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
                    alert(response.status.message);
                    return;
                }
                handleData(response.data);
            },
            error: function(XMLHttpRequest, textStatus, errorThrown)
            {
                alert("error");
            }
        });
      
    }
    
    NutritionPlan.prototype.getClientSecondaryTrainers = function(e, handleData) {
        var client_id = e.find(":selected").val();
        if(!client_id) return;
        var url = this.options.calendar_frontend_url;
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
                handleData(response.data);
            },
            error: function(XMLHttpRequest, textStatus, errorThrown)
            {
                alert("error");
            }
        });
    }
    
    NutritionPlan.prototype.getClientPrimaryGoals = function(e, handleData) {
        var client_id = e.find(":selected").val();
        if(!client_id) return;
        var url = this.options.fitness_administration_url;
        var self = this;
        $.ajax({
            type : "POST",
            url : url,
            data : {
               view : 'nutrition_plan',
               format : 'text',
               task : 'getClientPrimaryGoals',
               client_id : client_id
            },
            dataType : 'json',
            success : function(response) {
                if(!response.status.success) {
                    alert(response.status.message);
                    return;
                }
                //console.log(response.data);
                handleData(response.data);
            },
            error: function(XMLHttpRequest, textStatus, errorThrown)
            {
                alert("error getClientPrimaryGoals");
            }
        });
    }
    
    NutritionPlan.prototype.getGoalData = function(e, handleData) {
        var id = e.find(":selected").val();
        if(!id) return;
        var url = this.options.fitness_administration_url;
        $.ajax({
            type : "POST",
            url : url,
            data : {
               view : 'nutrition_plan',
               format : 'text',
               task : 'getGoalData',
               id : id
            },
            dataType : 'json',
            success : function(response) {
                if(!response.status.success) {
                    alert(response.status.message);
                    return;
                }
                handleData(response.data);
            },
            error: function(XMLHttpRequest, textStatus, errorThrown)
            {
                alert("error getGoalData");
            }
        });
    }
    
    NutritionPlan.prototype.populateSelect = function(data, destination, selected_option) {
        var selected;
        var html = '<option  value="">-Select-</option>';
        $.each(data, function(index, value) {
             if(index) {
                 if(index == selected_option) {
                     selected = 'selected';
                 } else {
                     selected = '';
                 }
                html += '<option ' + selected + ' value="' + index + '">' +  value + '</option>';
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

    Joomla.submitbutton = function(task)  {
        if (task == 'nutrition_plan.cancel') {
            Joomla.submitform(task, document.getElementById('nutrition_plan-form'));
        }
        else{

            if (task != 'nutrition_plan.cancel' && document.formvalidator.isValid(document.id('nutrition_plan-form'))) {
                var force_active = nutrition_plan.options.force_active_yes.is(":checked");
                if(force_active) {
                    nutrition_plan.resetAllForceActive(function() {
                        Joomla.submitform(task, document.getElementById('nutrition_plan-form'));
                    });
                } else {
                    Joomla.submitform(task, document.getElementById('nutrition_plan-form'));
                }
             
            }
            else {
                alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED')); ?>');
            }
        }
    }
    
    NutritionPlan.prototype.resetAllForceActive = function(handleData) {
        var url = this.options.fitness_administration_url;
        $.ajax({
            type : "POST",
            url : url,
            data : {
               view : 'nutrition_plan',
               format : 'text',
               task : 'resetAllForceActive'
            },
            dataType : 'json',
            success : function(response) {
                if(!response.status.success) {
                    alert(response.status.message);
                    return;
                }
                handleData();
            },
            error: function(XMLHttpRequest, textStatus, errorThrown)
            {
                alert("error resetAllForceActive");
            }
        }); 
    }

    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
</script>