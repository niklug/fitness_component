define([
	'jquery',
	'underscore',
	'backbone',
        'app',
        'collections/exercise_library/select_filter',
        'views/exercise_library/select_filter',
	'text!templates/exercise_library/select_filter_block.html',
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
      
            app.collections.exercise_type = new Select_filter_collection();
            app.collections.force_type = new Select_filter_collection();
            app.collections.difficulty = new Select_filter_collection();
            app.collections.mechanics_type = new Select_filter_collection();
            app.collections.body_part = new Select_filter_collection();
            app.collections.target_muscles = new Select_filter_collection();
            app.collections.equipment_type = new Select_filter_collection();
                       
            var self = this;
            $.when (
                app.collections.exercise_type.fetch({
                    data : {table : app.options.db_table_exercise_type},
                    error: function (collection, response) {
                        alert(response.responseText);
                    }
                }),
                
                app.collections.force_type.fetch({
                    data : {table : app.options.db_table_force_type},
                    error: function (collection, response) {
                        alert(response.responseText);
                    }
                }),
                
                app.collections.difficulty.fetch({
                    data : {table : app.options.db_table_difficulty},
                    error: function (collection, response) {
                        alert(response.responseText);
                    }
                }),
                
                app.collections.mechanics_type.fetch({
                    data : {table : app.options.db_table_mechanics_type},
                    error: function (collection, response) {
                        alert(response.responseText);
                    }
                }),
                
                app.collections.body_part.fetch({
                    data : {table : app.options.db_table_body_part},
                    error: function (collection, response) {
                        alert(response.responseText);
                    }
                }),
                
                app.collections.target_muscles.fetch({
                    data : {table : app.options.db_table_target_muscles},
                    error: function (collection, response) {
                        alert(response.responseText);
                    }
                }),
                
                app.collections.equipment_type.fetch({
                    data : {table : app.options.db_table_equipment},
                    error: function (collection, response) {
                        alert(response.responseText);
                    }
                })
 
            ).then (function(response) {
                self.loadFilters();
            })
        },
        
        template:_.template(template),
        
        render : function(){
            $(this.el).html(this.template({block_width : this.options.block_width}));
            return this;
        },
        
        loadFilters : function() {
            new Select_filter_fiew({
                model : this.model,
                el : $("#exercise_type_filter_wrapper"),
                collection : app.collections.exercise_type,
                title : 'Exercise Type',
                first_option_title : 'None',
                class_name : '',
                id_name : 'exercise_type_select',
                select_size : 12,
                model_field : 'exercise_type'
            }).render();

            new Select_filter_fiew({
                model : this.model,
                el : $("#force_type_filter_wrapper"),
                collection : app.collections.force_type,
                title : 'Force Type',
                first_option_title : 'Not Applicable',
                class_name : '',
                id_name : 'force_type_select',
                select_size : 12,
                model_field : 'force_type'
            }).render();

            new Select_filter_fiew({
                model : this.model,
                el : $("#difficulty_filter_wrapper"),
                collection : app.collections.difficulty,
                title : 'Difficulty',
                first_option_title : 'None',
                class_name : '',
                id_name : 'difficulty_select',
                select_size : 12,
                model_field : 'difficulty'
            }).render();

            new Select_filter_fiew({
                model : this.model,
                el : $("#mechanics_type_filter_wrapper"),
                collection : app.collections.mechanics_type,
                title : 'Mechanics Type',
                first_option_title : 'Not Applicable',
                class_name : '',
                id_name : 'mechanics_type_select',
                select_size : 12,
                model_field : 'mechanics_type'
            }).render();

            new Select_filter_fiew({
                model : this.model,
                el : $("#body_part_filter_wrapper"),
                collection : app.collections.body_part,
                title : 'Body Part(s)',
                first_option_title : 'None',
                class_name : '',
                id_name : 'body_part_select',
                select_size : 12,
                model_field : 'body_part'
            }).render();

            new Select_filter_fiew({
                model : this.model,
                el : $("#target_mucles_filter_wrapper"),
                collection : app.collections.target_muscles,
                title : 'Target Muscle(s)',
                first_option_title : 'None',
                class_name : '',
                id_name : 'target_muscles_select',
                select_size : 12,
                model_field : 'target_muscles'
            }).render();

            new Select_filter_fiew({
                model : this.model,
                el : $("#equipment_type_filter_wrapper"),
                collection : app.collections.equipment_type,
                title : 'Equipment Type',
                first_option_title : 'None',
                class_name : '',
                id_name : 'equipment_type_select',
                select_size : 12,
                model_field : 'equipment_type'
            }).render();
        }

    });
            
    return view;
});