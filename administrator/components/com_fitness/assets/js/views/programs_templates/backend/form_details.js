define([
	'jquery',
	'underscore',
	'backbone',
        'app',
        'collections/programs/select_filter',
        'views/programs/select_element',
	'text!templates/programs/backend/form_details.html',
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
            if( 
                app.collections.appointments 
                && app.collections.locations
                && app.collections.session_types
                && app.collections.session_focuses
            ) {
                this.render();
                return;
            } 
      
            app.collections.appointments = new Select_filter_collection();
            app.collections.locations = new Select_filter_collection();
            app.collections.session_types = new Select_filter_collection();
            app.collections.session_focuses = new Select_filter_collection();
                       
            var self = this;
            $.when (
                app.collections.appointments.fetch({
                    data : {table : app.options.db_table_appointments},
                    error: function (collection, response) {
                        alert(response.responseText);
                    }
                }),
                
                app.collections.locations.fetch({
                    data : {table : app.options.db_table_locations, by_business_profile : 1},
                    error: function (collection, response) {
                        alert(response.responseText);
                    }
                }),
                
                app.collections.session_types.fetch({
                    data : {table : app.options.db_table_session_types},
                    error: function (collection, response) {
                        alert(response.responseText);
                    }
                }),
                
                app.collections.session_focuses.fetch({
                    data : {table : app.options.db_table_session_focuses},
                    error: function (collection, response) {
                        alert(response.responseText);
                    }
                })

            ).then (function(response) {
                self.render();
            })
        },

        
        template:_.template(template),
        
        render: function(){
            var data = this.model.toJSON();
            //console.log(data);
            data.$ = $;
            var template = _.template(this.template(data));
            this.$el.html(template);
            
            this.loadAppointment();
            
            var category_id = this.model.get('title');
        
            if(category_id) {
                this.loadSessionType(category_id);
            }
            
            var session_type_id = this.model.get('session_type');
            
            if(session_type_id) {
                this.loadSessionFocus(session_type_id);
            }
            
            this.$el.find("#start_date, #finish_date").datepicker({ dateFormat: "yy-mm-dd"});
            
            this.$el.find("#auto_publish_workout, #auto_publish_event").datepicker({ dateFormat: "yy-mm-dd", minDate: 0});
            
            this.$el.find('#start_time, #finish_time').timepicker({ 'timeFormat': 'H:i', 'step': 15 });
             
            this.loadLocations();
            
            this.setAutoPublishWorkout();
            
            this.setAutoPublishEvent();
            return this;
        },
        
        events : {
            "change #title" : "onChangeAppointment",
            "change #session_type" : "onChangeSessionType",
            "change #start_time" : "onChangeStarttime",
            "click #frontend_published" : "setAutoPublishWorkout",
            "click #published" : "setAutoPublishEvent",
            "change #start_date" : "onChangeStartDate",
        },
        
        loadAppointment : function() {
            var appointments_collection = new Backbone.Collection;
            // filter for "Personal Training", "Semi-Private Training","Resistance Workout", "Cardio Workout"
           
            appointments_collection.add([
                app.collections.appointments.get(1),
                app.collections.appointments.get(2),
                app.collections.appointments.get(3),
                app.collections.appointments.get(4)
            ]);
            
            new Select_element_view({
                model : this.model,
                el : this.$el.find("#appointment_select"),
                collection : appointments_collection,
                first_option_title : '-Select-',
                class_name : '',
                id_name : 'title',
                model_field : 'title'
            }).render();
            
            
        },
        
        onChangeAppointment : function(event) {
            var id = $(event.target).val();
            this.$el.find("#session_focus_select").empty();
            this.loadSessionType(id);
            this.setEndInterval(id);
            app.controller.deleteClients(this.model);
        },
        
        loadSessionType : function(id) {
            var session_type_collection = new Backbone.Collection;
            
            session_type_collection.add(app.collections.session_types.where({category_id : id}));

            new Select_element_view({
                model : this.model,
                el : this.$el.find("#session_type_select"),
                collection : session_type_collection,
                first_option_title : '-Select-',
                class_name : '',
                id_name : 'session_type',
                model_field : 'session_type'
            }).render();
        },
        
        onChangeSessionType : function(event) {
            var id = $(event.target).val();
            this.loadSessionFocus(id);
        },
        
        loadSessionFocus : function(id) {
            var session_focus_collection = new Backbone.Collection;
            
            session_focus_collection.add(app.collections.session_focuses.where({session_type_id : id}));

            new Select_element_view({
                model : this.model,
                el : this.$el.find("#session_focus_select"),
                collection : session_focus_collection,
                first_option_title : '-Select-',
                class_name : '',
                id_name : 'session_focus',
                model_field : 'session_focus'
            }).render();
        },
        
        loadLocations : function() {
            new Select_element_view({
                model : this.model,
                el : this.$el.find("#location_select"),
                collection : app.collections.locations,
                first_option_title : '-Select-',
                class_name : '',
                id_name : 'location',
                model_field : 'location'
            }).render();
        },
        
        setEndInterval : function(id) {
            var endInterval;
            switch(id) {
                case '1' :
                   endInterval = 45;
                   break;
                case '2' :
                   endInterval = 30;
                   break;
                case '3' :
                   endInterval = 45;
                   break;
                default :
                   endInterval = 60; 
            }
            this.set_etparttime(endInterval);
        },

        set_etparttime : function(minutes) {
            var start_time = this.$el.find("#start_time").val();
            if(!start_time) return;
            var start_time = start_time.split(":");
            var date = new Date();
            date.setHours(start_time[0]);
            date.setMinutes(start_time[1]);
            var newdate = this.addMinutes(date, minutes);
            var hours = newdate.getHours();
            var minutes = newdate.getMinutes();
            $("#finish_time").val(this.pad(hours) + ':' + this.pad(minutes));
        },

        addMinutes : function(inDate, inMinutes) {
            var newdate = new Date();
            newdate.setTime(inDate.getTime() + inMinutes * 60000);
            return newdate;
        },

        pad : function (d) {
            return (d < 10) ? '0' + d.toString() : d.toString();
        },
        
        onChangeStarttime : function() {
            var appointment_id = this.$el.find("#title").val();
            this.setEndInterval(appointment_id);
        },
        
        setAutoPublishWorkout : function() {
            var checked = $("#frontend_published").is(":checked");
            var disabled = false;
            if(checked) {
                disabled = true;
                $("#auto_publish_workout").val('');
            }
            $("#auto_publish_workout").attr('disabled', disabled);
        },
        
        setAutoPublishEvent : function() {
            var checked = $("#published").is(":checked");
            var disabled = false;
            if(checked) {
                disabled = true;
                $("#auto_publish_event").val('');
            }
            $("#auto_publish_event").attr('disabled', disabled);
        },
        
        onChangeStartDate : function(event) {
            var value  = $(event.target).val();
            $(this.el).find("#finish_date").val(value);
        }
        


    });
            
    return view;
});