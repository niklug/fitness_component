define([
	'jquery',
	'underscore',
	'backbone',
        'app',
        'collections/programs/select_filter',
        'models/goals/primary_goal',
        'models/notifications/notification',
        'views/programs/select_element',
	'text!templates/goals/backend/form_primary.html'

], function (
        $,
        _,
        Backbone,
        app,
        Select_filter_collection,
        Model,
        Notification_model,
        Select_element_view,
        template
    ) {

    var view = Backbone.View.extend({
        
        initialize : function() {
        },

        
        template:_.template(template),
        
        render: function(){
            var data = {item : this.model.toJSON()};
            //console.log(data);
            data.app = app;
            data.$ = $;
            var template = _.template(this.template(data));
            this.$el.html(template);
            
            this.onRender();
            
            return this;
        },
        
        events : {
            "click #save" : "onClickSave",
            "click #save_close" : "onClickSaveClose",
            "click #cancel" : "onClickCancel",
            "click #finalise_primary_goal" : "onClickFinalisePrimaryGoal"
        },
        
        onRender : function() {
            var self = this;
            $(this.el).show('0', function() {
                app.controller.connectStatus(self.model, self.$el);
                app.controller.connectComments(self.model, $(self.el), 'primary');
                self.loadCalendar();
                self.loadPrimaryGoals();
            });
        },
        
        loadCalendar : function() {
            $(this.el).find("#start_date, #deadline").datepicker({ dateFormat: "yy-mm-dd"});
        },
        
        loadPrimaryGoals : function() {
            if( 
                app.collections.primary_goals_categories
            ) {
                this.populateGoalsSelect();
                return;
            } 
            app.collections.primary_goals_categories = new Select_filter_collection();
            var self = this;
            app.collections.primary_goals_categories.fetch({
                data : {table : '#__fitness_goal_categories', by_business_profile : true},
                success : function (collection, response) {
                    self.populateGoalsSelect();
                },
                error : function (collection, response) {
                    alert(response.responseText);
                }
            });
        },
        
        populateGoalsSelect : function() {
            new Select_element_view({
                model : this.model,
                el : $(this.el).find("#primary_goal_wrapper"),
                collection : app.collections.primary_goals_categories,
                first_option_title : '-Select-',
                class_name : 'filter_select',
                id_name : 'primary_goal',
                model_field : 'goal_category_id'
            }).render();
        },
       
        onClickSave : function() {
            this.saveItem();
        },

        onClickSaveClose : function() {
            this.save_method = 'save_close';
            this.saveItem();
        },


        onClickCancel : function() {
            app.controller.navigate("!/list_view", true);
        },
        
        
        saveItem : function() {
            //validation
            var start_date_field = $(this.el).find('#start_date');
            var deadline_field = $(this.el).find('#deadline');
            var details_field = $(this.el).find('#details');
            var error_start_date_field = $(this.el).find('#error_start_date');
            var error_deadline_field = $(this.el).find('#error_deadline');
            
            start_date_field.removeClass("red_style_border");
            deadline_field.removeClass("red_style_border");
            error_start_date_field.html('');
            error_deadline_field.html('');
            
            var start_date = start_date_field.val();
            var deadline= deadline_field.val();
            
            var message = '';
            
            this.model.set({
                    start_date : start_date_field.val(), 
                    deadline : deadline_field.val(), 
                    details : details_field.val(),
                    user_id : app.options.client_id
            });
            
            if(this.model.isNew()) {
                this.model.set({
                    created_by : app.options.user_id,
                    created : moment(new Date()).format("YYYY-MM-DD HH:mm:ss") 
                });
            }
            
            var overlap_start_date = this.onCheckOverlapDate('start_date');
            var overlap_deadline = this.onCheckOverlapDate('deadline');
     
            if(start_date && overlap_start_date.status) {
                start_date_field.addClass("red_style_border");
                message = 'The date overlaps with Primary Goal beginning ' + overlap_start_date.model.get('start_date') + ' and ending ' + overlap_start_date.model.get('deadline');
                error_start_date_field.html(message);
                return false;
            }
            
            if(deadline && overlap_deadline.status) {
                deadline_field.addClass("red_style_border");
                message = 'The date overlaps with Primary Goal beginning ' + overlap_start_date.model.get('start_date') + ' and ending ' + overlap_start_date.model.get('deadline');
                error_deadline_field.html(message);
                return false;
            }
            
            if(start_date && deadline && (start_date >= deadline)) {
                start_date_field.addClass("red_style_border");
                deadline_field.addClass("red_style_border");
                message = '"Start Date" should be less than "Achieve By"! ';
                error_start_date_field.html(message);
                return false;
            }

            if (!this.model.isValid()) {
                var validate_error = this.model.validationError;

                if(validate_error == 'start_date') {
                    start_date_field.addClass("red_style_border");
                    return false;
                } else if(validate_error == 'deadline') {
                    deadline_field.addClass("red_style_border");
                    return false;
                } else {
                    alert(this.model.validationError);
                    return false;
                }

            }
            
            var self = this;
            
            var status = parseInt($(".status_button").attr('data-status_id'));
            
            if(status) {
                 this.model.set({status : status});
            }


            if (this.model.isNew()) {
                this.collection.create(this.model, {
                    wait: true,
                    success: function (model, response) {
                        self.connectNotification(model);
                        if(self.save_method == 'save_close') {
                            app.controller.navigate("!/list_view", true);
                        } else if(self.save_method == 'save') {
                            app.controller.navigate("!/primary_form/" + model.get('id'), true);
                        }
                    },
                    error: function (model, response) {
                        alert(response.responseText);
                    }
                })
            } else {
                this.model.save(null, {
                    success: function (model, response) {
                        if(self.save_method == 'save_close') {
                            app.controller.navigate("!/list_view", true);
                        }
                    },
                    error: function (model, response) {
                        alert(response.responseText);
                    }
                });
            }
        },
        
        onCheckOverlapDate : function(type) {
            var result = {},
                current = this.model.get(type),
                id = this.model.get('id'),
                i;
        
            for(i = 0; i < this.collection.models.length; i++) {
                var model = this.collection.models[i];
                result.model = model;
                if(id != model.get('id')) {
                    var start_date = model.get('start_date');
                    var deadline = model.get('deadline');

                    if(current >= start_date && current <= deadline) {
                        result.status = true;
                        return result;
                    }
                }
            }

            result.status = false;
            return result;
        },
        
        onClickFinalisePrimaryGoal : function() {
            var self = this;
            this.model.save({status : app.options.statuses.EVELUATING_GOAL_STATUS.id}, {
                success: function (model, response) {
                    app.collections.primary_goals.add(model);
                    //app.controller.sendGoalEmail(model.get('id'), 'GoalEvaluating');
                    app.controller.navigate("!/list_view", true)
                },
                error: function (model, response) {
                    alert(response.responseText);
                }
            });
        },
        
        connectNotification : function(model) {
            var options = {
                template_id : 1,
                date : model.get('start_date'),
                user_id : model.get('user_id'),
                created : model.get('created'),
                url_id_1 : model.get('id'),
                
            };
      
            var model = new Notification_model(options);
        },
 
    });
            
    return view;
});