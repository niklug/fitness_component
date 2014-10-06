define([
	'jquery',
	'underscore',
	'backbone',
        'app',
        'collections/programs/select_filter',
        'models/goals/primary_goal',
        'models/goals/mini_goal',
        'models/notifications/notification',
        'views/programs/select_element',
	'text!templates/goals/backend/form_mini.html'

], function (
        $,
        _,
        Backbone,
        app,
        Select_filter_collection,
        Primary_goal_model,
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
            "click #finalise_mini_goal" : "onClickFinaliseMiniGoal",
            "click .schedule_mini_goal" : "onClickSchedule",
        },
        
        onRender : function() {
            var self = this;
            $(this.el).show('0', function() {
                app.controller.connectStatus(self.model, self.$el, 'mini');
                app.controller.connectComments(self.model, self.$el, 'mini');
                self.getPrimaryGoal();
                self.loadMiniGoals();
                self.loadTrainingPeriods();
            });
        },
        
        loadMiniGoals : function() {
            if( 
                app.collections.mini_goals_categories
            ) {
                this.populateGoalsSelect();
                return;
            } 
            app.collections.mini_goals_categories = new Select_filter_collection();
            var self = this;
            app.collections.mini_goals_categories.fetch({
                data : {table : '#__fitness_mini_goal_categories', by_business_profile : true},
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
                el : $(this.el).find("#mini_goal_wrapper"),
                collection : app.collections.mini_goals_categories,
                first_option_title : '-Select-',
                class_name : 'filter_select',
                id_name : 'mini_goal',
                model_field : 'mini_goal_category_id'
            }).render();
        },
        
        loadTrainingPeriods : function() {
            if( 
                app.collections.training_periods
            ) {
                this.populateTPSelect();
                return;
            } 
            app.collections.training_periods = new Select_filter_collection();
            var self = this;
            app.collections.training_periods.fetch({
                data : {table : '#__fitness_training_period', business_profile_id : app.options.business_profile_id},
                success : function (collection, response) {
                    self.populateTPSelect();
                },
                error : function (collection, response) {
                    alert(response.responseText);
                }
            });
        },
        
        populateTPSelect : function() {
            new Select_element_view({
                model : this.model,
                el : $(this.el).find("#training_period_wrapper"),
                collection : app.collections.training_periods,
                first_option_title : '-Select-',
                class_name : 'filter_select',
                id_name : 'training_period',
                model_field : 'training_period_id'
            }).render();
        },
        
        getPrimaryGoal : function() {
            var primary_goal_id = this.options.primary_goal_id;
            
            var primary_goal_model  = app.collections.primary_goals.get(primary_goal_id);
            
            if(primary_goal_model) {
                this.loadCalendar(primary_goal_model);
                return;
            }
            
            if(!primary_goal_model) {
                primary_goal_model = new Primary_goal_model({id : primary_goal_id});
                var self = this;
                primary_goal_model.fetch({
                    wait : true,
                    success: function (model, response) {
                        self.loadCalendar(model);
                    },
                    error: function (collection, response) {
                        alert(response.responseText);
                    } 
                })
            }

        },
        
        loadCalendar : function(model) {
            var start_date  = model.get('start_date');
            var deadline = model.get('deadline');
            var min_date = new Date(Date.parse(start_date));
            var max_date = new Date(Date.parse(deadline));
            $(this.el).find("#start_date, #deadline").datepicker({ dateFormat: "yy-mm-dd", minDate: min_date, maxDate: max_date });
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
                    start_date : start_date, 
                    deadline : deadline, 
                    details : details_field.val(),
                    primary_goal_id : this.options.primary_goal_id,
                    user_id : app.options.client_id
            });
            
            var self = this;
            if(this.model.isNew()) {
                this.model.set({
                    created_by : app.options.user_id                    
                });
            }
            
                        
            var overlap_start_date = this.onCheckOverlapDate('start_date');
            var overlap_deadline = this.onCheckOverlapDate('deadline');
     
            if(start_date && overlap_start_date.status) {
                start_date_field.addClass("red_style_border");
                message = 'The date overlaps with Mini Goal beginning ' + overlap_start_date.model.get('start_date') + ' and ending ' + overlap_start_date.model.get('deadline');
                error_start_date_field.html(message);
                return false;
            }
            
            if(deadline && overlap_deadline.status) {
                deadline_field.addClass("red_style_border");
                message = 'The date overlaps with Mini Goal beginning ' + overlap_start_date.model.get('start_date') + ' and ending ' + overlap_start_date.model.get('deadline');
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
            
            var mini_goal_category_id = this.model.get('mini_goal_category_id');
            var mini_goal_category_model = app.collections.mini_goals_categories.get(mini_goal_category_id)
            var tp_id = this.model.get('training_period_id');
            var tp_model = app.collections.training_periods.get(tp_id)

            this.model.set({mini_goal_name : mini_goal_category_model.get('name'), training_period_name : tp_model.get('name')});
            
            var status = parseInt($(".status_button_mini").attr('data-status_id'));
            
            if(status) {
                 this.model.set({status : status});
            }


            if (this.model.isNew()) {
                this.collection.create(this.model, {
                    wait: true,
                    success: function (model, response) {
                        self.connectNotification(model);
                        self.addPlan(model);
                        if(self.save_method == 'save_close') {
                            app.controller.navigate("!/list_view", true);
                        } else if(self.save_method == 'save') {
                            app.controller.navigate("!/mini_form/" + model.get('id'), true);
                        }
                    },
                    error: function (model, response) {
                        alert(response.responseText);
                    }
                })
            } else {
                this.model.save(null, {
                    success: function (model, response) {
                        self.addPlan(model);
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
        
        onClickFinaliseMiniGoal : function() {
            var self = this;
            this.model.save({status : app.options.statuses.EVELUATING_GOAL_STATUS.id}, {
                success: function (model, response) {
                    app.collections.mini_goals.add(model);
                    app.controller.navigate("!/list_view", true)
                },
                error: function (model, response) {
                    alert(response.responseText);
                }
            });
        },
        
        onClickSchedule : function() {
            app.controller.navigate("!/schedule/" + this.model.get('primary_goal_id') + '/' + this.model.get('id') , true);
        },
        
        addPlan : function(model) {
            
            var data = model.toJSON();
            //console.log(data);
            var url = app.options.ajax_call_url;
            var view = 'goals';
            var task = 'addPlan';
            var table = '';

            var self = this;
            $.AjaxCall(data, url, view, task, table, function(output) {
                console.log(output);
            });
        },
        
        connectNotification : function(model) {
            var options = {
                template_id : 2,
                date : model.get('start_date'),
                user_id : model.get('user_id'),
                created : model.get('created'),
                url_id_1 : model.get('id'),
                url_id_2 : model.get('primary_goal_id')
            };
      
            var model = new Notification_model(options);
        },

    });
            
    return view;
});