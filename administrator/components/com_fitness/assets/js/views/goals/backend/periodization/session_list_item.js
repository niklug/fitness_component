define([
	'jquery',
	'underscore',
	'backbone',
        'app',
        'collections/programs/select_filter',
        'views/programs/select_element',
	'text!templates/goals/backend/periodization/session_list_item.html',
        'jquery.timepicker'
], function (
        $,
        _,
        Backbone,
        app,
        Select_filter_collection,
        Select_element_view,
        template
    ) {

    var view = Backbone.View.extend({
        
        initialize : function() {
            this.editable = this.options.editable || false;
        },
        
        tagName : "tr",
        
        template : _.template(template),
        
        render : function(){
            this.model.set({editable : this.editable});
            this.element_disabled = '';
            if(this.editable == false) {
                this.element_disabled = 'disabled';
            }
            
            var data = {item : this.model.toJSON()};
            data.app = app;
            data.$ = $;
            var template = _.template(this.template(data));
            this.$el.html(template);
            
            this.onRender();
            
            return this;
        },
        
        events: {
            "click .save_session" : "onClickSave",
            "click .edit_session" : "onClickEdit",
            "click .delete_session" : "onClickDelete",
            "change .appointment_type_id" : "onChangeAppointment",
            "change .session_type" : "onChangeSessionType",
            "click .schedule_session" : "onClickSchedule",
        },
        
        onRender : function() {
            
            var self = this;
            $(this.el).show('0', function() {
                $(self.el).find(".start_date").datepicker({ dateFormat: "yy-mm-dd"});
                $(self.el).find('.start_time').timepicker({ 'timeFormat': 'H:i', 'step': 15 });
                
                if(self.editable) {
                    self.loadAppointment();
                    self.loadSessionType(self.model.get('appointment_type_id'));
                    self.loadSessionFocus(self.model.get('session_type'));
                    self.loadLocations();
                    self.loadProgramTemplates();
                }
            });
        },
        
        loadAppointment : function() {
            if(app.collections.appointments) {
                this.populateAppointment();
                return;
            } 
            app.collections.appointments = new Select_filter_collection();
            var self = this;
            app.collections.appointments.fetch({
                data : {table : app.options.db_table_appointments},
                success : function (collection, response) {
                    self.populateAppointment();
                },
                error : function (collection, response) {
                    alert(response.responseText);
                }
            });
        },
        
        populateAppointment : function() {
            var appointments_collection = new Backbone.Collection;
            appointments_collection.add([
                app.collections.appointments.get(1),
                app.collections.appointments.get(2),
                app.collections.appointments.get(3),
                app.collections.appointments.get(4),
            ]);
            new Select_element_view({
                model : this.model,
                el : $(this.el).find(".appointment_select"),
                collection : appointments_collection,
                first_option_title : '-Select-',
                class_name : ' appointment_type_id ',
                id_name : '',
                model_field : 'appointment_type_id',
                element_disabled :  this.element_disabled 
            }).render();
        },
        
        loadSessionType : function(id) {
            if(app.collections.session_types) {
                this.populateSessionType(id);
                return;
            } 
            app.collections.session_types = new Select_filter_collection();
            var self = this;
            app.collections.session_types.fetch({
                data : {table : app.options.db_table_session_types},
                success : function (collection, response) {
                    self.populateSessionType(id);
                },
                error : function (collection, response) {
                    alert(response.responseText);
                }
            });
        },
        
        populateSessionType : function(id) {
            var session_type_collection = new Backbone.Collection;
            session_type_collection.add(app.collections.session_types.where({category_id : id}));
            new Select_element_view({
                model : this.model,
                el : $(this.el).find(".session_type_select"),
                collection : session_type_collection,
                first_option_title : '-Select-',
                class_name : 'session_type',
                id_name : '',
                model_field : 'session_type',
                element_disabled :  this.element_disabled 
            }).render();
        },
        
        loadSessionFocus : function(id) {
            if(app.collections.session_focuses) {
                this.populateSessionFocus(id);
                return;
            } 
            app.collections.session_focuses = new Select_filter_collection();
            var self = this;
            app.collections.session_focuses.fetch({
                data : {table : app.options.db_table_session_focuses},
                success : function (collection, response) {
                    self.populateSessionFocus(id);
                },
                error : function (collection, response) {
                    alert(response.responseText);
                }
            });
        },
        
        populateSessionFocus : function(id) {
            var session_focus_collection = new Backbone.Collection;
            session_focus_collection.add(app.collections.session_focuses.where({session_type_id : id}));
            new Select_element_view({
                model : this.model,
                el : $(this.el).find(".session_focus_select"),
                collection : session_focus_collection,
                first_option_title : '-Select-',
                class_name : 'session_focus',
                id_name : '',
                model_field : 'session_focus',
                element_disabled :  this.element_disabled 
            }).render();
        },
        
        loadLocations : function() {
            if(app.collections.locations) {
                this.populateLocations();
                return;
            } 
            app.collections.locations = new Select_filter_collection();
            var self = this;
            app.collections.locations.fetch({
                data : {table : app.options.db_table_locations, by_business_profile : 1},
                success : function (collection, response) {
                    self.populateLocations();
                },
                error : function (collection, response) {
                    alert(response.responseText);
                }
            });
        },
        
        populateLocations : function() {
            new Select_element_view({
                model : this.model,
                el : $(this.el).find(".location_select"),
                collection : app.collections.locations,
                first_option_title : '-Select-',
                class_name : 'location',
                id_name : '',
                model_field : 'location',
                element_disabled :  this.element_disabled 
            }).render();
        },
        
        loadProgramTemplates : function() {
            if(app.collections.program_templates) {
                this.populateProgramTemplates();
                return;
            } 
            app.collections.program_templates = new Select_filter_collection();
            var self = this;
            app.collections.program_templates.fetch({
                data : {table : app.options.db_table_program_templates, by_business_profile : 1, sort_by : 'created', order : 'DESC'},
                success : function (collection, response) {
                    self.populateProgramTemplates();
                },
                error : function (collection, response) {
                    alert(response.responseText);
                }
            });
        },
        
        populateProgramTemplates : function() {
            new Select_element_view({
                model : this.model,
                el : $(this.el).find(".pr_temp_select"),
                collection : app.collections.program_templates,
                first_option_title : '-None-',
                class_name : 'pr_temp',
                id_name : '',
                model_field : 'pr_temp_id',
                element_disabled :  this.element_disabled 
            }).render();
        },
        
        onChangeAppointment : function(event) {
            var id = $(event.target).val();
            $(this.el).find(".session_focus").html(''); 
            this.loadSessionType(id);
        },
        
        onChangeSessionType : function(event) {
            var id = $(event.target).val();
            this.loadSessionFocus(id);
        },
        
        onClickDelete : function(event) {
            var self = this;
            this.model.destroy({
                success: function (model) {
                    self.close();
                },
                error: function (model, response) {
                    alert(response.responseText);
                }
            });
        },
        
        onClickEdit : function() {
            this.editable = true;
            this.render();
        },
        
        onClickSave : function() {
            this.editable = false;
            
            var data = {};
            
            var appointment_field = $(this.el).find('.appointment_type_id');
            
            var session_type_field = $(this.el).find('.session_type');
            
            var session_focus_field = $(this.el).find('.session_focus');
            
            var location_field = $(this.el).find('.location');
            
            var pr_temp_field = $(this.el).find('.pr_temp');
            
            var start_date_field = $(this.el).find('.start_date');
            
            var start_time_field = $(this.el).find('.start_time');
            
            var appointment_type_id = appointment_field.val()
                    
            data.appointment_type_id = appointment_type_id;
            
            data.session_type = session_type_field.val();
            
            data.session_focus = session_focus_field.val();
            
            data.location = location_field.val();
            
            data.pr_temp_id = pr_temp_field.val();
            
            var start_time =  start_time_field.val();
            
            data.starttime  = start_date_field.val() + ' ' + start_time;
            
            var end_time = $.fitness_helper.setAppointmentEndtime(appointment_type_id, start_time) || '';
            
            data.endtime = start_date_field.val() + ' ' + end_time;
            
            this.model.set(data);
            
            appointment_field.removeClass("red_style_border");
            session_type_field.removeClass("red_style_border");
            session_focus_field.removeClass("red_style_border");
            location_field.removeClass("red_style_border");
            start_date_field.removeClass("red_style_border");
            start_time_field.removeClass("red_style_border");
            
            //validation  
            if(!start_date_field.val()) {
                start_date_field.addClass("red_style_border");
                return false;
            }
            
            
            if(!start_time_field.val()) {
                start_time_field.addClass("red_style_border");
                return false;
            }
            
            
            if (!this.model.isValid()) {
                var validate_error = this.model.validationError;
                console.log(validate_error);
                if(validate_error == 'appointment_type_id') {
                    appointment_field.addClass("red_style_border");
                    return false;
                } else if(validate_error == 'session_type') {
                    session_type_field.addClass("red_style_border");
                    return false;
                } else if(validate_error == 'session_focus') {
                    session_focus_field.addClass("red_style_border");
                    return false;
                }  else if(validate_error == 'location') {
                    location_field.addClass("red_style_border");
                    return false;
                } else if(validate_error == 'starttime') {
                    start_date_field.addClass("red_style_border");
                    start_time_field.addClass("red_style_border");
                    return false;
                } else {
                    alert(this.model.validationError);
                    return false;
                }
            }
            //end validation
            
            this.model.set({
                appointment_name : appointment_field.find("option:selected").text(),
                session_type_name : session_type_field.find("option:selected").text(),
                session_focus_name : session_focus_field.find("option:selected").text(),
                location_name : location_field.find("option:selected").text(),
                pr_temp_name : pr_temp_field.find("option:selected").text()
            });

            console.log(this.model.toJSON());
            
            var self = this;
            
            if (this.model.isNew()) {
                this.collection.create(this.model, {
                    wait: true,
                    success: function (model, response) {
                        self.render();
                    },
                    error: function (model, response) {
                        alert(response.responseText);
                    }
                })
            } else {
                this.model.save(null, {
                    success: function (model, response) {
                        self.render();
                    },
                    error: function (model, response) {
                        alert(response.responseText);
                    }
                });
            }
        },
        
        close : function() {
            $(this.el).unbind();
            $(this.el).remove();
        },
        
        onClickSchedule : function() {
            this.model.set({client_id : app.options.client_id, owner : app.options.user_id});
            var data = this.model.toJSON()
            var url = app.options.ajax_call_url;
            var view = 'goals';
            var task = 'scheduleSession';
            var table = '';
            console.log(data);
            $.AjaxCall(data, url, view, task, table, function(output){
                console.log(output);
            });
        }
        
    });
            
    return view;
});