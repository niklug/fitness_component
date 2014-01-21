/*
 * Class Provide functionality for CLIENT & TRAINER(S) and NUTRITION PLAN (PERIODIZATION) 
 * fieldsets of the Nutrition Plan Form
 * 
 */
(function (factory) {
	if (typeof define === 'function' && define.amd) {
		// AMD. Register as an anonymous module.
		define(['jquery'], factory);
	} else {
		// Browser globals
		factory(jQuery);
	}
}(function ($) {
    // Constructor
    function NutritionPlan(options) {
        this.options = options;
        this.options.secondary_trainers_wrapper = $(this.options.secondary_trainers_wrapper);
        this.options.primary_goal_select = $(this.options.primary_goal_select);
        this.options.business_profile_select = $(this.options.business_profile_select);
        this.options.client_select = $(this.options.client_select);
        this.options.trainer_select = $(this.options.trainer_select);
        this.options.primary_goal_start_date = $(this.options.primary_goal_start_date);
        this.options.primary_goal_deadline = $(this.options.primary_goal_deadline);
    }

    NutritionPlan.prototype.setEventListeners = function() {
        var self = this;
        // on business profile select
        this.options.business_profile_select.on('change', function(){
            self.businessProfileChangeEvent($(this));
        });
        
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
        $("#jform_override_dates0").live('click', function(){
            self.forceActiveYes();
        });

        // on Active start 'no' click
        $("#jform_override_dates1").live('click', function(){
            self.forceActiveNo();
        });

        // on No End Date 'yes' click
        $("#jform_no_end_date0").live('click', function(){
            self.forceNoEndDateYes();
        });

        // on No End Date start 'no' click
        $("#jform_no_end_date1").live('click', function(){
            self.forceNoEndDateNo();
        });
        
        // on Minigoal select click
        $("#minigoals_select").live('change', function() {
            var id = $(this).val();
            $("#minigoal_fields").html('');
            $("#jform_mini_goal").val(id);
            self.setMinigoal(id);
        });
        
        var business_profile_id = $("#business_profile_id").val();
        if(business_profile_id) {
            $.fitness_helper.populateTrainersSelectOnBusiness('user_group', business_profile_id, '#jform_trainer_id', this.options.trainer_id);
        }

    }
    
    NutritionPlan.prototype.setMinigoal = function(id) {
        var mini_goal_obj = _.find(this.minigoals, function(obj) { return obj.id == id});
        this.populateMinigoalFields(mini_goal_obj);
    }

    NutritionPlan.prototype.forceActiveYes = function() {
        $( "#jform_active_start, #jform_active_finish" ).datepicker({ dateFormat: "yy-mm-dd"});
        $("#jform_no_end_date-lbl, #jform_no_end_date").show();
        $("#jform_active_finish").attr('readonly', false);

        
        $("#jform_active_start, #jform_active_finish, .override_dates_block").show();
    }

    NutritionPlan.prototype.forceActiveNo = function() {
        $( "#jform_active_start, #jform_active_finish" ).datepicker( "destroy" );
        $("#jform_no_end_date-lbl, #jform_no_end_date").hide();
        $("#jform_active_finish").attr('readonly', true);

        $("#jform_active_start, #jform_active_finish, .override_dates_block").hide();
        
        $("#jform_active_start").val($("#mini_goal_start_date").text());
        $("#jform_active_finish").val($("#mini_goal_deadline").text());
    }


    NutritionPlan.prototype.forceNoEndDateYes = function() {
        $("#jform_active_finish").val(this.options.max_possible_date);
        $("#jform_no_end_date0").val('1');
        $("#jform_no_end_date1").val('0');
    }
    
    NutritionPlan.prototype.forceNoEndDateNo = function() {
        $("#jform_no_end_date0").val('1');
        $("#jform_no_end_date1").val('0');
        
        var active_finish = $("#mini_goal_deadline").text();
        if(this.options.active_finish) {
            active_finish = this.options.active_finish;
        }
        
        $("#jform_active_finish").val(active_finish);
    }


    NutritionPlan.prototype.trainerChangeEvent = function(e) {
        // reset fields
        this.populateSecondaryTrainers({}, this.options.secondary_trainers_wrapper);
        this.populateSelect({}, this.options.client_select);
        this.populateSelect({}, this.options.primary_goal_select);
        this.options.primary_goal_start_date.val('');
        this.options.primary_goal_deadline.val('');
        //
        var self = this;
        this.getTrainerClients(e, function(output) {
            if(output) {
                var selected_option = self.options.client_selected
                self.populateSelect(output, self.options.client_select, selected_option);
            }
        });
        
        this.populateSelect({}, $("#minigoals_select"));
        $("#minigoal_fields").html('');
    }

    NutritionPlan.prototype.clientChangeEvent = function(e) {
        // reset fields
        this.populateSecondaryTrainers({}, this.options.secondary_trainers_wrapper);
        this.populateSelect({}, this.options.primary_goal_select);
        this.options.primary_goal_start_date.val('');
        this.options.primary_goal_deadline.val('');
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
        
        this.populateSelect({}, $("#minigoals_select"));
        $("#minigoal_fields").html('');
    }
    
    NutritionPlan.prototype.businessProfileChangeEvent = function(e) {
        // reset fields
        this.populateSecondaryTrainers({}, this.options.secondary_trainers_wrapper);
        this.populateSelect({}, this.options.client_select);
        this.populateSelect({}, this.options.primary_goal_select);
        this.options.primary_goal_start_date.val('');
        this.options.primary_goal_deadline.val('');
        
        this.populateSelect({}, $("#minigoals_select"));
        $("#minigoal_fields").html('');

    }

    NutritionPlan.prototype.primaryGoalChangeEvent = function(e) {
        this.options.primary_goal_start_date.val('');
        this.options.primary_goal_deadline.val('');
        this.populateSelect({}, $("#minigoals_select"));
        $("#minigoal_fields").html('');
        var self = this;
        this.getGoalData(e, function(output) {
            if(output) {
                self.options.primary_goal_start_date.val(output.start_date);
                self.options.primary_goal_deadline.val(output.deadline);
                self.populateMinigoals(output.minigoals, $("#plan_mini_goals"));
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
               user_id : client_id,
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
        var nutrition_plan_id = this.options.fitness_administration_url;
        $.ajax({
            type : "POST",
            url : url,
            data : {
               view : 'nutrition_plan',
               format : 'text',
               task : 'getGoalData',
               id : id,
               nutrition_plan_id : nutrition_plan_id
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

    NutritionPlan.prototype.dateFieldsLogic = function() {
        // set  Active Start/Finish field inactive/active and No End Date
        
        if(parseInt(this.options.override_dates)) {
            this.forceActiveYes();

        } else {
            this.forceActiveNo();
        }


        if(this.options.active_finish_value == this.options.max_possible_date) {
            this.forceNoEndDateYes();
            $("#jform_no_end_date0").attr('checked', true);

        } else {

            if(parseInt(this.options.override_dates)) {
                this.forceNoEndDateNo();
            }
            $("#jform_no_end_date0").attr('checked', false);

        }
    }

    NutritionPlan.prototype.run = function() {
       this.setEventListeners();

       var self = this;
       // populate clients select onload
       this.getTrainerClients(this.options.trainer_select, function(output) {
            var selected_option = self.options.client_selected
            self.populateSelect(output, self.options.client_select, selected_option);
        });

       // populate secondary trainers  onload
       this.getClientSecondaryTrainers(this.options.client_select, function(output) {
            self.populateSecondaryTrainers(output, self.options.secondary_trainers_wrapper);
        });

       // populate primary goals select onload
       this.getClientPrimaryGoals(this.options.client_select, function(output) {
            var selected_option = self.options.primary_goal_selected;
            self.populateSelect(output, self.options.primary_goal_select, selected_option);
        });

        // papulate training period
        this.getGoalData(this.options.primary_goal_select, function(output) {
            if(output) {
                self.populateMinigoals(output.minigoals, $("#plan_mini_goals"));
                self.options.primary_goal_start_date.val(output.start_date);
                self.options.primary_goal_deadline.val(output.deadline);
                var mini_goal = self.options.mini_goal_selected;
                $("#minigoals_select").val(mini_goal);
                self.setMinigoal(mini_goal);
                self.dateFieldsLogic();
            }
        });

        //set detes fields options depends on saved statuses
        this.dateFieldsLogic();
    }
    
    NutritionPlan.prototype.populateMinigoals = function(minigoals, destination) {
        var html = '';
        this.minigoals = minigoals;
        html += '<table width="100%">';
        html += '<tr>';
        html += '<td style="width:150px;">';
        html += '<label class="hasTip required">Mini Goal <span class="star"> *</span></label>';
        html += '</td>';
        html += '<td>';
        html += '<select style="pointer-events: none; cursor: default;"  id="minigoals_select" class="inputbox required " >';
        html += '<option value="">-Select-</option>';
        $.each(minigoals, function(index,item) {
            html += '<option value="' + item.id + '">' + item.minigoal_name + '</option>';
        });
        html += '</select>';
        html += '</td>';
        html += '</tr>';
       
        html += '</table>';
        
        html += '<div id="minigoal_fields"></div>'
        
        $(destination).html(html);
    }
    
    NutritionPlan.prototype.populateMinigoalFields = function(minigoal) {
        var html = '';
        html += '<table width="100%">';
        html += '<tr>';
        html += '<td style="width:150px;">';
        html += '<label>Training Period</label>';
        html += '</td>';
        html += '<td>';
        html += minigoal.training_period_name;
        html += '</td>';
        html += '</tr>';
        html += '<tr>';
        html += '<td>';
        html += '<label>Start Date / Active From</label>';
        html += '</td>';
        html += '<td id="mini_goal_start_date">';
        html += minigoal.start_date;
        html += '</td>';

        html += '</tr>';
        html += '<tr>';
        html += '<td>';
        html += '<label>Achieve By / Active To</label>';
        html += '</td>';
        html += '<td id="mini_goal_deadline">';
        html += minigoal.deadline;
        html += '</td>';
                
        html += '</tr>';
        
        html += '<tr>';
        html += '<td colspan="2">';
        html += '<label>Do you wish to OVERRIDE the dates above?</label>';
        html += '</td>';

        var override_dates = this.options.override_dates;

        var override_dates_0_checked = '';
        var override_dates_1_checked = ' checked="checked" ';
   
        if(override_dates == '1') {
            override_dates = '1';
            override_dates_0_checked = ' checked="checked"  ';
            override_dates_1_checked = '';
        }

        html += '<td>';
        html += '<fieldset id="jform_override_dates" class="radio">';
        html += '<input id="jform_override_dates0" ' + override_dates_0_checked +  ' class="" type="radio" value="1" name="jform[override_dates]" >';
        html += '<label class="" for="jform_override_dates0" aria-invalid="false">Yes</label>';
        html += '<input id="jform_override_dates1" ' + override_dates_1_checked + ' type="radio"  value="0" name="jform[override_dates]">';
        html += '<label for="jform_override_dates1">No (Cancel & Clear)</label>';
        html += '</fieldset>';
        html += '</td>';
        html += '</tr>';
        
        html += '<tr class="override_dates_block">';
        html += '<td>';
        html += '<label>Start Date / Active From</label>';
        html += '</td>';
        html += '<td>';
        
        var active_start = minigoal.start_date;
        
        if(this.options.active_start) {
            active_start = this.options.active_start;
        }
        
        html += '<input style="display:none" readonly id="jform_active_start" class="required" type="text" value="' + active_start + '" name="jform[active_start]" title="" aria-required="true" required="required">';
        html += '</td>';
        html += '</tr>';
        
        html += '<tr class="override_dates_block">';
        html += '<td>';
        html += '<label>Achieve By / Active To</label>';
        html += '</td>';
        html += '<td>';
        
        var active_finish = minigoal.deadline;
        
        if(this.options.active_finish) {
            active_finish = this.options.active_finish;
        }
        
        
        html += '<input style="display:none" readonly id="jform_active_finish" class="required" type="text" value="' + active_finish + '" name="jform[active_finish]" title="" readonly="readonly" aria-required="true" required="required">';
        html += '</td>';
        html += '<td>';
        html += '<label id="jform_no_end_date-lbl" class="" for="jform_no_end_date" style="display: none;">NO END DATE (plan will not expire)</label>';
        html += '</td>';

        var no_end_date = '0';
        var no_end_date_0_checked = '';
          var no_end_date_1_checked = 'checked';

        if(this.options.active_finish_value == this.options.max_possible_date) {
            no_end_date = '1';
            no_end_date_0_checked = 'checked';
            no_end_date_1_checked = '';
        }
        
        
        html += '<td>';
        html += '<fieldset id="jform_no_end_date" class="radio" style="display: none;">';
        html += '<input id="jform_no_end_date0" type="radio" ' + no_end_date_0_checked + ' value="' + no_end_date + '" name="jform[no_end_date]">';
        html += '<label for="jform_no_end_date0">Yes</label>';
        html += '<input id="jform_no_end_date1" type="radio" ' + no_end_date_1_checked + '  value="' + no_end_date + '" name="jform[no_end_date]">';
        html += '<label for="jform_no_end_date1">No</label>';
        html += '</fieldset>';
        html += '</td>';
        html += '</tr>';
        
        html += '</table>';
        
        $("#minigoal_fields").html(html);
    }
    
    
    // Add the  function to the top level of the jQuery object
    $.nutritionPlan = function(options) {

        var constr = new NutritionPlan(options);

        return constr;
    };

}));