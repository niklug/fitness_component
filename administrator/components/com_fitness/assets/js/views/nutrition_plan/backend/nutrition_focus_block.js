define([
	'jquery',
	'underscore',
	'backbone',
        'app',
        'collections/programs/select_filter',
        'views/programs/select_element',
	'text!templates/nutrition_plan/backend/nutrition_focus_block.html'
], function ( $,
              _,
              Backbone,
              app,
              Select_filter_collection,
              Select_element_view,
              template 
   ) {

    var view = Backbone.View.extend({
        
        template:_.template(template),
            
        render: function(){
            var data = {item : this.model.toJSON()};
            var template = _.template(this.template(data));
            
            this.$el.html(template);
            
            this.onRender();

            return this;
        },
        
        onRender : function() {
            var self = this;
            $(this.el).show('0', function() {
                self.loadNutritionFocus();
                
                self.connectEditor();
            });
        },
        
        loadNutritionFocus : function() {
            if( 
                app.collections.nutrition_focuses
            ) {
                this.populateNutritionFocusSelect(app.collections.nutrition_focuses);
                return;
            } 
            app.collections.nutrition_focuses = new Select_filter_collection();
            var self = this;
            app.collections.nutrition_focuses.fetch({
                data : {table : '#__fitness_nutrition_focus', by_business_profile : true},
                success : function (collection, response) {
                    self.populateNutritionFocusSelect(collection);
                },
                error : function (collection, response) {
                    alert(response.responseText);
                }
            });
        },
        
        populateNutritionFocusSelect : function(collection) {
            new Select_element_view({
                model : this.model,
                el : $(this.el).find("#nutrition_focus_wrapper"),
                collection : collection,
                first_option_title : '-Select-',
                class_name : 'filter_select',
                id_name : 'nutrition_focus',
                model_field : 'nutrition_focus'
            }).render();
        },
        
        connectEditor : function() {
            $(this.el).find("#trainer_comments").cleditor({width:'100%', height:150, useCSS:true})[0];
        }
    });
            
    return view;
});