/*
 * Class Provide functionality for CLIENT & TRAINER(S) and NUTRITION PLAN (PERIODIZATION) 
 * fieldsets of the Nutrition Plan Form
 * 
 */
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
    if(parseInt(this.options.force_active_value)) {
        this.forceActiveYes();

    } else {
        this.forceActiveNo();
    }


    if(this.options.active_finish_value == this.options.max_possible_date) {
        this.forceNoEndDateYes();
        this.options.no_end_date_yes.attr('checked', true);

    } else {

        if(parseInt(this.options.force_active_value)) {
            this.forceNoEndDateNo();
        }
        this.options.no_end_date_yes.attr('checked', false);

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
            self.options.training_period_select.val(output.training_period_name);
        }
    });

    //set detes fields options depends on saved statuses
    this.dateFieldsLogic();
}