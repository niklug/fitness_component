define([
	'jquery',
	'underscore',
	'backbone',
        'app',
        'collections/programs/select_filter',
        'views/programs/select_filter',
	'text!templates/programs/select_filter_block.html',
], function (
        $,
        _,
        Backbone,
        app,
        Select_filter_collection,
        Select_filter_fiew,
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
        
        render : function(){
            $(this.el).html(this.template({block_width : this.options.block_width}));

            this.loadFilters();
            return this;
        },
        
        loadFilters : function() {
            //console.log($(this.el));
            var element_disabled = '';
            
            new Select_filter_fiew({
                model : this.model,
                el : $(this.el).find("#appointments_filter_wrapper"),
                collection : app.collections.appointments,
                title : 'Appointment Type',
                first_option_title : 'None',
                class_name : 'dark_input_style',
                id_name : '',
                select_size : 12,
                model_field : 'title',
                element_disabled : element_disabled
            }).render();

            if(! _.include(this.options.not_show, 'locations')) {
                new Select_filter_fiew({
                    model : this.model,
                    el : $(this.el).find("#locations_filter_wrapper"),
                    collection : app.collections.locations,
                    title : 'Location',
                    first_option_title : 'None',
                    class_name : 'dark_input_style',
                    id_name : '',
                    select_size : 12,
                    model_field : 'location',
                    element_disabled : element_disabled
                }).render();
            }
            
            
            
            //
            var session_type_grouped = app.collections.session_types.groupBy('name');

            var session_type_names = _.keys(session_type_grouped);
            
            var session_type_values = _.values(session_type_grouped);

            app.collections.session_types_grouped = new Backbone.Collection();
            
            _.each(session_type_values, function(value, index) {
                var id = (value).map(function(model) { return model.get('id'); }).join(',');
                
                app.collections.session_types_grouped.add([
                    {id : id, name : session_type_names[index]}
                ]);
            });
            
            new Select_filter_fiew({
                model : this.model,
                el : $(this.el).find("#session_type_wrapper"),
                collection : app.collections.session_types_grouped,
                title : 'Session Type',
                first_option_title : 'None',
                class_name : 'dark_input_style',
                id_name : '',
                select_size : 12,
                model_field : 'session_type',
                element_disabled : element_disabled
            }).render();
            /////
            
            
            ///////////
            var session_focus_grouped = app.collections.session_focuses.groupBy('name');

            var session_focus_names = _.keys(session_focus_grouped);
            
            var session_focus_values = _.values(session_focus_grouped);

            app.collections.session_focuses_grouped = new Backbone.Collection();
            
            _.each(session_focus_values, function(value, index) {
                var id = (value).map(function(model) { return model.get('id'); }).join(',');
                
                app.collections.session_focuses_grouped.add([
                    {id : id, name : session_focus_names[index]}
                ]);
            });
            
            
            new Select_filter_fiew({
                model : this.model,
                el : $(this.el).find("#session_focus_filter_wrapper"),
                collection : app.collections.session_focuses_grouped,
                title : 'Session Focus',
                first_option_title : 'None',
                class_name : 'dark_input_style',
                id_name : '',
                select_size : 12,
                model_field : 'session_focus',
                element_disabled : element_disabled
            }).render();
            //////
            
        }
    });
            
    return view;
});