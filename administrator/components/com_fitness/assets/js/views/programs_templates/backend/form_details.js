define([
	'jquery',
	'underscore',
	'backbone',
        'app',
        'collections/programs/select_filter',
        'views/programs/select_element',
	'text!templates/programs_templates/backend/form_details.html',
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
                && app.collections.session_types
                && app.collections.session_focuses
            ) {
                this.render();
                return;
            } 
      
            app.collections.appointments = new Select_filter_collection();
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
            
            var category_id = this.model.get('appointment_id');
        
            if(category_id) {
                this.loadSessionType(category_id);
            }
            
            var session_type_id = this.model.get('session_type');
            
            if(session_type_id) {
                this.loadSessionFocus(session_type_id);
            }
            

            if(!this.model.get('view_allowed')) {
                $(this.el).find("#workout_name").attr('disabled', 'disabled');
            }
   
            return this;
        },
        
        events : {
            "change #appointment_id" : "onChangeAppointment",
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
                app.collections.appointments.get(4),
                app.collections.appointments.get(5)
            ]);
            
       
            var element_disabled = 'disabled';
            if(this.model.get('view_allowed')) {
                element_disabled = '';
            }
            
            new Select_element_view({
                model : this.model,
                el : this.$el.find("#appointment_select"),
                collection : appointments_collection,
                first_option_title : '-Select-',
                class_name : '',
                id_name : 'appointment_id',
                model_field : 'appointment_id',
                element_disabled : element_disabled
            }).render();
            
            
        },
        
        onChangeAppointment : function(event) {
            var id = $(event.target).val();
            this.$el.find("#session_focus_select").empty();
            this.loadSessionType(id);
            app.controller.deleteClients(this.model);
        },
        
        loadSessionType : function(id) {
            var session_type_collection = new Backbone.Collection;
            
            session_type_collection.add(app.collections.session_types.where({category_id : id}));
            
            var element_disabled = 'disabled';
            if(this.model.get('view_allowed')) {
                element_disabled = '';
            }

            new Select_element_view({
                model : this.model,
                el : this.$el.find("#session_type_select"),
                collection : session_type_collection,
                first_option_title : '-Select-',
                class_name : '',
                id_name : 'session_type',
                model_field : 'session_type',
                element_disabled : element_disabled
            }).render();
        },
        
        onChangeSessionType : function(event) {
            var id = $(event.target).val();
            this.loadSessionFocus(id);
        },
        
        loadSessionFocus : function(id) {
            var session_focus_collection = new Backbone.Collection;
            
            session_focus_collection.add(app.collections.session_focuses.where({session_type_id : id}));
            
            var element_disabled = 'disabled';
            if(this.model.get('view_allowed')) {
                element_disabled = '';
            }
            
            new Select_element_view({
                model : this.model,
                el : this.$el.find("#session_focus_select"),
                collection : session_focus_collection,
                first_option_title : '-Select-',
                class_name : '',
                id_name : 'session_focus',
                model_field : 'session_focus',
                element_disabled : element_disabled
            }).render();
        },

    });
            
    return view;
});