define([
	'jquery',
	'underscore',
	'backbone',
        'app',
	'text!templates/diary/frontend/menus/submenu_form.html'
], function ( $, _, Backbone, app, template ) {

    var view = Backbone.View.extend({
        
        initialize : function() {
            this.active_plan_data = app.models.active_plan_data.toJSON();
        },
        
        template:_.template(template),
        
        render: function(){
            var template = _.template(this.template());
            this.$el.html(template);
            
            this.setCalendar();
            
            return this;
        },

        events: {
            "click #next" : "onClickNext",
            "click #cancel" : "onClickCancel",
        },
        
        setCalendar : function() {
            var self = this;
            $(this.el).find("#entry_date").datepicker({ dateFormat: "yy-mm-dd",  minDate : -5, beforeShowDay: self.disableDays});
        },
        
        disableDays : function(date) {
            var disabledDays = app.models.diary_days.toJSON();
            var calendar_date = moment(date).format("YYYY-MM-DD");
            var result =  [true];
            if(_.contains(disabledDays, calendar_date)) {
                result =  [false];
            }
            return result
        },

        onClickNext : function() {
            var entry_date_field = $(this.el).find('#entry_date');
            
            var self  = this;
            var data = {};
            data.entry_date = entry_date_field.val();
            data.nutrition_plan_id = this.active_plan_data.id;
            data.client_id = this.active_plan_data.client_id;
            data.trainer_id = this.active_plan_data.trainer_id;
            data.goal_category_id = this.active_plan_data.mini_goal;
            data.nutrition_focus = this.active_plan_data.nutrition_focus;
            data.created = moment(new Date()).format("YYYY-MM-DD HH:mm:ss"); 
            data.created_by = app.options.user_id;
            data.state = '1';
            data.status =  app.options.statuses.INPROGRESS_DIARY_STATUS.id;
            
            data.target_protein = this.active_plan_data.target_protein;
            data.target_water = this.active_plan_data.target_water;
            data.target_fats = this.active_plan_data.target_fats;
            data.target_carbs = this.active_plan_data.target_carbs;
            data.target_calories = this.active_plan_data.target_calories;
            data.target_protein_percent = this.active_plan_data.target_protein_percent;
            data.target_fats_percent = this.active_plan_data.target_fats_percent;
            data.target_carbs_percent = this.active_plan_data.target_carbs_percent;
            
            this.model.set(data);
            
            //console.log(this.model.toJSON());

            entry_date_field.removeClass("red_style_border");


            if (!this.model.isValid()) {
                var validate_error = this.model.validationError;

                if(validate_error == 'entry_date') {
                    entry_date_field.addClass("red_style_border");
                    return false;
                } else {
                    alert(this.model.validationError);
                    return false;
                }
            }

            this.model.save(null, {
                success: function (model, response) {
                    app.controller.navigate("!/item_view/" + model.get('id'), true);
                },
                error: function (model, response) {
                    alert(response.responseText);
                }
            });

        },

        onClickCancel : function() {
            app.controller.navigate("!/list_view", true);
        },
    });
            
    return view;
});