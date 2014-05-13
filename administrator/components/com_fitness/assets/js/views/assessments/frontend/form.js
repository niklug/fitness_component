define([
	'jquery',
	'underscore',
	'backbone',
        'app',
        'collections/programs/select_filter',
        'collections/programs/trainers',
        'collections/programs/exercises/items',
        'models/programs/exercises/item', 
        'views/programs/select_element',
        'views/programs/exercises/list',
        'views/assessments/frontend/form_video',
        'views/assessments/frontend/photo_block/list',
	'text!templates/programs/frontend/form.html',
        'jquery.timepicker'
], function (
        $,
        _, 
        Backbone, 
        app,
        Select_filter_collection,
        Trainers_collection, 
        Exercises_collection,
        Exercise_model,
        Select_element_view,
        Exercises_list_view,
        Form_video_view,
        Photo_block_view,
        template
    ) {

    var view = Backbone.View.extend({
        
        initialize : function() {
            if( 
                app.collections.appointments 
                && app.collections.locations
                && app.collections.session_types
                && app.collections.session_focuses
                && app.collections.trainers
            ) {
                this.render();
                return;
            } 
      
            app.collections.appointments = new Select_filter_collection();
            app.collections.locations = new Select_filter_collection();
            app.collections.session_types = new Select_filter_collection();
            app.collections.session_focuses = new Select_filter_collection();
            app.collections.trainers = new Trainers_collection();
                       
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
                }),
                
                app.collections.trainers.fetch({
                    data : {primary_only : true, client_id : app.options.user_id},
                    error: function (collection, response) {
                        alert(response.responseText);
                    }
                })

            ).then (function(response) {
                self.render();
            })
        },

        template:_.template(template),
        
        render : function () {
            var data = this.model.toJSON();
            data.$ = $;
            data.app = app;
            $(this.el).html(this.template(data));
            
            this.onRender();
            
            $(this.el).find("#program_form").validate();
         
            return this;
        },
        
        onRender : function() {
            var self = this;
            $(this.el).show('0', function() {
                
                self.loadAppointment();
                //
                var category_id = self.model.get('title');

                if(category_id) {
                    self.loadSessionType(category_id);
                }
                //
                var session_type_id = self.model.get('session_type');

                if(session_type_id) {
                    self.loadSessionFocus(session_type_id);
                }
                //
                $(self.el).find("#start_date, #finish_date").datepicker({ dateFormat: "yy-mm-dd"});
            
                $(self.el).find("#start_time, #finish_time").timepicker({ 'timeFormat': 'H:i', 'step': 15 });
                //

                $.fitness_helper.connectEditor($(self.el), "#description");

                //
                self.loadLocations();
                
                
                var frontend_published = self.model.get('frontend_published');
                if(parseInt(frontend_published)) {
                    app.controller.loadAssessmentsForm(self.model.get('session_focus_name'), self.model, {readonly : false});

                    self.connectExercises();

                    new Form_video_view({el : $("#video_block"), model : self.model, readonly : false});

                    new Photo_block_view({el : $("#photo_block"), model : self.model, readonly : false});

                    app.controller.connectComments(self.model, $(self.el));
                }
            });
        },
        
        
        events : {
            "click #pdf_button" : "onClickPdf",
            "click #email_button" : "onClickEmail",
            "change #title" : "onChangeAppointment",
            "change #session_type" : "onChangeSessionType",
            "change #start_time" : "onChangeStarttime",
            "change #start_date" : "onChangeStartDate",
        },
        
        
        
        onClickPdf : function() {
            var htmlPage = app.options.base_url + 'index.php?option=com_multicalendar&view=pdf&tpml=component&layout=email_pdf_workout&event_id=' + this.model.get('id') + '&client_id=' + app.options.user_id;
            $.fitness_helper.printPage(htmlPage);
        },
        
        onClickEmail : function() {
            var data = {};
            data.url = app.options.ajax_call_url;
            data.view = '';
            data.task = 'ajax_email';
            data.table = '';

            data.id =  this.model.get('id');
            data.client_id =  app.options.user_id;
            data.view = 'Programs';
            data.method = 'Workout';
            $.fitness_helper.sendEmail(data);
        },

        connectExercises : function() {
            new Exercises_list_view({
                el : $(this.el).find("#exercises_list"),
                model : this.model,
                exercise_model : Exercise_model,
                exercises_collection : Exercises_collection,
                readonly : false,
                search_videos : true,
                title : 'PHYSICAL ASSESSMENT DETAILS',
                
            });
        },
        
        loadAppointment : function() {
            var appointments_collection = new Backbone.Collection;
          
            appointments_collection.add([
                app.collections.appointments.get(5)
            ]);
            
            //allow edit field only for creator
            var element_disabled = 'disabled';
            if(app.controller.is_item_owner(this.model)) {
                element_disabled = '';
            }
            
            new Select_element_view({
                model : this.model,
                el : $(this.el).find("#appointment_select"),
                collection : appointments_collection,
                first_option_title : '-Select-',
                class_name : 'dark_input_style',
                id_name : 'title',
                model_field : 'title',
                element_disabled : element_disabled
            }).render();
            
            
        },
        
        onChangeAppointment : function(event) {
            var id = $(event.target).val();
            $(this.el).find("#session_focus_select").empty();
            this.loadSessionType(id);
            this.setEndInterval(id);
        },
        
        loadSessionType : function(id) {
            var session_type_collection = new Backbone.Collection;
            
            session_type_collection.add(app.collections.session_types.where({category_id : id}));
            
            //allow edit field only for creator
            var element_disabled = 'disabled';
            if(app.controller.is_item_owner(this.model)) {
                element_disabled = '';
            }
            
            new Select_element_view({
                model : this.model,
                el : $(this.el).find("#session_type_select"),
                collection : session_type_collection,
                first_option_title : '-Select-',
                class_name : 'dark_input_style',
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
            
            //allow edit field only for creator
            var element_disabled = 'disabled';
            if(app.controller.is_item_owner(this.model)) {
                element_disabled = '';
            }
            
            new Select_element_view({
                model : this.model,
                el : $(this.el).find("#session_focus_select"),
                collection : session_focus_collection,
                first_option_title : '-Select-',
                class_name : 'dark_input_style',
                id_name : 'session_focus',
                model_field : 'session_focus',
                element_disabled : element_disabled
            }).render();
        },
        
        loadLocations : function() {
            new Select_element_view({
                model : this.model,
                el : $(this.el).find("#location_select"),
                collection : app.collections.locations,
                first_option_title : '-Select-',
                class_name : 'dark_input_style required',
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
            var start_time = $(this.el).find("#start_time").val();
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
            var appointment_id = $(this.el).find("#title").val();
            this.setEndInterval(appointment_id);
        },
        
        onChangeStartDate : function(event) {
            var value  = $(event.target).val();
            $(this.el).find("#finish_date").val(value);
        }
        
       
    });
            
    return view;
});