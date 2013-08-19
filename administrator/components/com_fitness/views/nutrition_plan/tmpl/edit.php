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
            <tr>
                <td colspan="2">
                    <fieldset id="daily_micronutrient"  class="adminform">
                        <legend>DAILY MACRONUTRIENT & CALORIE TARGETS</legend>
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
    var nutrition_plan_options = {
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
    
    var macronutrient_targets_options = {
        'main_wrapper' : $("#daily_micronutrient"),
    }
    
    // cteate main object
    var nutrition_plan = new NutritionPlan(nutrition_plan_options);
    
        // cteate main object
    var macronutrient_targets_heavy = new MacronutrientTargets(macronutrient_targets_options, 'heavy', 'HEAVY TRAINING DAY');
    
    // attach listeners on document ready
    $(document).ready(function(){
        nutrition_plan.run();
        macronutrient_targets_heavy.run();
    });
    
    // Constructor
    function MacronutrientTargets(options, type, title) {
        this.options = options;
        this.type = type;
        this.title = title;
    }
    
    // Controller
    MacronutrientTargets.prototype.run = function() {
        var html = this.generateHtml();
        this.options.main_wrapper.append(html);
        this.setEventListeners();
    }
    
    MacronutrientTargets.prototype.setEventListeners = function() {

        this.options._calories = $("#" + this.type + "_calories");
        this.options._water = $("#" + this.type + "_water");
        this.options._protein = $("#" + this.type + "_protein");
        this.options._protein_grams = $("#" + this.type + "_prorein_grams");
        this.options._protein_cals = $("#" + this.type + "_protein_cals");
        this.options._fats = $("#" + this.type + "_fats");
        this.options._fats_grams = $("#" + this.type + "_fats_grams");
        this.options._fats_cals = $("#" + this.type + "_fats_cals");
        this.options._carbs = $("#" + this.type + "_carbs");
        this.options._carbs_grams = $("#" + this.type + "_carbs_grams");
        this.options._carbs_cals = $("#" + this.type + "_carbs_cals");
        
        var self = this;
        
        // calculate grams and cals values
        $(this.options._protein).on('focusout', function(){
            self.calculateTargetGrams($(this), self.options._protein_grams);
            self.calculateTargetCals($(this), self.options._protein_cals);
            self.validateSum100();
            self.calculate_totals();
        });
        
        $(this.options._fats).on('focusout', function(){
            self.calculateTargetGrams($(this), self.options._fats_grams);
            self.calculateTargetCals($(this), self.options._fats_cals);
            self.validateSum100();
            self.calculate_totals();
        });
        
        $(this.options._carbs).on('focusout', function(){
            self.calculateTargetGrams($(this), self.options._carbs_grams);
            self.calculateTargetCals($(this), self.options._carbs_cals);
            self.validateSum100();
            self.calculate_totals();
        });

        
    }
    
    MacronutrientTargets.prototype.validateSum100 = function() {
        var protein = this.options._protein.val();
        var fats = this.options._fats.val();
        var carbohydrate  = this.options._carbs.val();
        var sum = parseFloat(protein) + parseFloat(fats) + parseFloat(carbohydrate);
        if(sum != 100) {
            $("#" + this.type +  "sum_100_error").html('Protein, Fats & Carbs MUST equal (=) 100%')
            return false;
        } else {
            $("#" + this.type +  "sum_100_error").html('');
            return true;
        } 
    }
    
    MacronutrientTargets.prototype.calculateTargetGrams = function(o, destination) {
        var calories = this.options._calories.val();
        if(!calories) return;
        var target_percent = parseFloat(o.val()) * 0.01;
        var grams_value = parseFloat(calories) * (target_percent/4);
        destination.val(this.round_2_sign(grams_value));
    }
      
      
    MacronutrientTargets.prototype.calculateTargetCals = function(o, destination) {
        var calories = this.options._calories.val();
        if(!calories) return;
        var target_percent = parseFloat(o.val()) * 0.01;
        var cals_value = parseFloat(calories) * target_percent;
        destination.val(this.round_2_sign(cals_value));
    }
    
    
    MacronutrientTargets.prototype.calculate_totals = function() {
        this.set_item_total(this.get_item_total(this.type + '_percent_value'), this.type + '_total');
        this.set_item_total(this.get_item_total(this.type + '_grams_value'), this.type + '_total_grams');
        this.set_item_total(this.get_item_total(this.type + '_cals_value'), this.type + '_total_cals');
    }
    
    MacronutrientTargets.prototype.get_item_total = function(element) {
       var item_array = $("." +element);
       var sum = 0;
       item_array.each(function(){
           var value = parseFloat($(this).val());
           if(value > 0) {
              sum += parseFloat(value); 
           }
           
       });
       return this.round_2_sign(sum);
    }
    
    
    MacronutrientTargets.prototype.set_item_total = function(value, element) {
        $("#" + element).val(value);
    }
    
    
    MacronutrientTargets.prototype.generateHtml = function() {
        var html = '<fieldset id="' + this.type + 'fieldset"  class="adminform">';
        html += '<legend>' + this.title + '</legend>';
        html += '<table class="nutrition_targets_table" width="100%">';
   
        html += '<tbody>';
        html += '<tr>';
        html += '<td>';
        html += 'Calorie Target';
        html += '</td>';
        
        html += '<td>';
        html += '<input type="text" value="" id="' + this.type + '_calories" class="required  validate-numeric" />';
        html += '</td>'
        
        html += '<td>';
        html += 'Calories';
        html += '</td>';
        
        html += '<td>';
        html += 'Water Target';
        html += '</td>';
        
        html += '<td>';
        html += '<input type="text" value="" id="' + this.type + '_water" class="required  validate-numeric" />';
        html += '</td>';
        
        html += '<td>';
        html += 'millilitres';
        html += '</td>';
        
        html += '<td colspan="3">';
        html += '</td>';
        html += '</tr>';
        
        
        html += '<tr>';
        html += '<td>';
        html += 'Macronutrients Targets';
        html += '</td>';
        
        html += '<td>';
        html += '<input type="text" value="" id="' + this.type + '_protein" class="required  validate-numeric ' + this.type + '_percent_value" />';
        html += '</td>'
        
        html += '<td>';
        html += '(%) Protein';
        html += '</td>';
        
        html += '<td>';
        html += 'Macronutrients Targets';
        html += '</td>';
        
        html += '<td>';
        html += '<input type="text" value="" id="' + this.type + '_prorein_grams" readonly class="' + this.type + '_grams_value" />';
        html += '</td>';
        
        html += '<td>';
        html += '(grams) Protein';
        html += '</td>';
        
        html += '<td>';
        html += 'Macronutrients Targets';
        html += '</td>';
        html += '<td>';
        html += '<input type="text" value="" id="' + this.type + '_protein_cals" readonly class="' + this.type + '_cals_value" />';
        html += '</td>'
        
        html += '<td>';
        html += '(cals) Protein';
        html += '</td>'
        html += '</tr>';
        
        
        html += '<tr>';
        html += '<td>';
        html += '</td>';
        
        html += '<td>';
        html += '<input type="text" value="" id="' + this.type + '_fats" class="required  validate-numeric ' + this.type + '_percent_value" />';
        html += '</td>'
        
        html += '<td>';
        html += '(%) Fats';
        html += '</td>';
        
        html += '<td>';
        html += '</td>';
        
        html += '<td>';
        html += '<input type="text" value="" id="' + this.type + '_fats_grams" readonly class="' + this.type + '_grams_value" />';
        html += '</td>';
        
        html += '<td>';
        html += '(grams) Fats';
        html += '</td>';
        
        html += '<td>';
        html += '</td>';
        html += '<td>';
        html += '<input type="text" value="" id="' + this.type + '_fats_cals"readonly class="' + this.type + '_cals_value" />';
        html += '</td>'
        
        html += '<td>';
        html += '(cals) Fats';
        html += '</td>'
        html += '</tr>';
        
        
        html += '<tr>';
        html += '<td>';
        html += '</td>';
        
        html += '<td>';
        html += '<input type="text" value="" id="' + this.type + '_carbs" class="required  validate-numeric ' + this.type + '_percent_value" />';
        html += '</td>'
        
        html += '<td>';
        html += '(%) Carbohydrates';
        html += '</td>';
        
        html += '<td>';
        html += '</td>';
        
        html += '<td>';
        html += '<input type="text" value="" id="' + this.type + '_carbs_grams" readonly class="' + this.type + '_grams_value" />';
        html += '</td>';
        
        html += '<td>';
        html += '(grams) Carbohydrates';
        html += '</td>';
        
        html += '<td>';
        html += '</td>';
        html += '<td>';
        html += '<input type="text" value="" id="' + this.type + '_carbs_cals" readonly class="' + this.type + '_cals_value" />';
        html += '</td>'
        
        html += '<td>';
        html += '(cals) Carbohydrates';
        html += '</td>'
        html += '</tr>';
        html += '</tbody>';
        
        
        html += '<tfoot>';
        html += '<tr>';
        html += '<td>';
        html += '</td>';
        
        html += '<td>';
        html += '<input type="text" value="" id="' + this.type + '_total" readonly />';
        html += '</td>'
        
        html += '<td>';
        html += '(%) TOTAL';
        html += '</td>';
        
        html += '<td>';
        html += '</td>';
        
        html += '<td>';
        html += '<input type="text" value="" id="' + this.type + '_total_grams" readonly />';
        html += '</td>';
        
        html += '<td>';
        html += '(grams) TOTAL';
        html += '</td>';
        
        html += '<td>';
        html += '</td>';
        html += '<td>';
        html += '<input type="text" value="" id="' + this.type + '_total_cals" readonly />';
        html += '</td>'
        
        html += '<td>';
        html += '(cals) TOTAL';
        html += '</td>'
        html += '</tr>';      
        html += '</tfoot>';
        
        html += '</table>';
        html += '<div style="color:red;" id="' + this.type + 'sum_100_error"></div>';
        html += '</fieldset>'; 
        return html;
    }
    
    
    MacronutrientTargets.prototype.round_2_sign = function(value) {
        return Math.round(value * 100)/100;
    }

    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
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

    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
</script>