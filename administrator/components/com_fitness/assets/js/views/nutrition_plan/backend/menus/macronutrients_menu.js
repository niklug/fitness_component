define([
	'jquery',
	'underscore',
	'backbone',
        'app',
	'text!templates/nutrition_plan/backend/menus/form_menu.html'
], function ( $, _, Backbone, app, template ) {

    var view = Backbone.View.extend({

        template:_.template(template),
        
        render: function(){
            var template = _.template(this.template(this.model.toJSON()));
            this.$el.html(template);
            return this;
        },

        events: {
            "click #save" : "onClickSave",
            "click #save_close" : "onClickSaveClose",
            "click #cancel" : "onClickCancel",
        },

        onClickSave : function() {
            this.save_method = 'save';
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
            var data = {};
            
            //
            var allowed_proteins = $("#allowed_proteins").val();
            if(typeof allowed_proteins !== 'undefined') {
                allowed_proteins = encodeURIComponent(allowed_proteins);
            } else {
                allowed_proteins = '';
            }
            data.allowed_proteins = allowed_proteins;
            
            //
            var allowed_fats = $("#allowed_fats").val();
            if(typeof allowed_fats !== 'undefined') {
                allowed_fats = encodeURIComponent(allowed_fats);
            } else {
                allowed_fats = '';
            }
            data.allowed_fats = allowed_fats;
            
            //
            var allowed_carbs = $("#allowed_carbs").val();
            if(typeof allowed_carbs !== 'undefined') {
                allowed_carbs = encodeURIComponent(allowed_carbs);
            } else {
                allowed_carbs = '';
            }
            data.allowed_carbs = allowed_carbs;
            
            //
            var allowed_liquids = $("#allowed_liquids").val();
            if(typeof allowed_liquids !== 'undefined') {
                allowed_liquids = encodeURIComponent(allowed_liquids);
            } else {
                allowed_liquids = '';
            }
            data.allowed_liquids = allowed_liquids;
            
            //
            var other_recommendations = $("#other_recommendations").val();
            if(typeof other_recommendations !== 'undefined') {
                other_recommendations = encodeURIComponent(other_recommendations);
            } else {
                other_recommendations = '';
            }
            data.other_recommendations = other_recommendations;
            //

            console.log(data);
              
            this.model.set(data);
            

            var id = this.model.get('id');
            var self = this;
            this.model.save(null, {
                success: function (model, response) {
                    if(self.save_method == 'save') {
                        app.controller.navigate("");
                        app.controller.navigate("!/macronutrients/" + model.get('id'), true);
                    } else if(self.save_method == 'save_close') {
                        app.controller.navigate("!/list_view", true);
                    } else {
                        app.controller.navigate("!/list_view", true);
                    }
                },
                error: function (model, response) {
                    alert(response.responseText);
                }
            });
        },

    });
            
    return view;
});