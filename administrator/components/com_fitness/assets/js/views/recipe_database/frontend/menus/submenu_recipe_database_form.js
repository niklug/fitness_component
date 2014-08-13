define([
	'jquery',
	'underscore',
	'backbone',
        'app',
	'text!templates/recipe_database/frontend/menus/submenu_recipe_database_form.html'
], function ( $, _, Backbone, app, template ) {

    var view = Backbone.View.extend({
        
        initialize : function() {
            this.controller = app.routers.recipe_database;
        },

        template:_.template(template),
        
        render: function(){
            var template = _.template(this.template(this.model.toJSON()));
            this.$el.html(template);
            return this;
        },

        events: {
            "click #save" : "onClickSave",
            "click #save_close" : "onClickSaveClose",
            "click #save_new" : "onClickSaveNew",
            "click #cancel" : "onClickCancel",
        },

        onClickSave : function() {
            this.save_method = 'save';
            this.saveRecipe();
        },

        onClickSaveClose : function() {
            this.save_method = 'save_close';
            this.saveRecipe();
        },

        onClickSaveNew : function() {
            this.save_method = 'save_new';
            this.saveRecipe();
        },

        onClickCancel : function() {
            if(this.model.get('id')) {
                this.controller.navigate("!/nutrition_recipe/" + this.model.get('id'), true);
            } else {
                this.controller.navigate("!/my_recipes", true);
            }
        },
        
        
        saveRecipe : function() {
            var recipe_name_field = $("#recipe_name");
            var recipe_type_field = $("#recipe_type");
            var recipe_variation_field = $("#recipe_variation");
            var number_serves_field = $("#number_serves");
            
            var data = {};
            
            data.recipe_name = recipe_name_field.val();

            data.recipe_type = recipe_type_field.find(':selected').map(function(){ return this.value }).get().join(",");
            
            data.recipe_variation = recipe_variation_field.find(':selected').map(function(){ return this.value }).get().join(",");
 
            data.number_serves = number_serves_field.val();
            
            data.image = $(".preview_image").attr('data-imagepath');
            
            data.video = $("#video_container").attr('data-videopath');
            
            var instructions = $("#instructions").val();
             
            if(typeof instructions !== 'undefined') {
                instructions = encodeURIComponent(instructions);
            } else {
                instructions = '';
            }
            data.instructions = instructions;

            data.state = '1';
            
            if(this.model.isNew()) {
                data.created_by = app.options.client_id;
                
                data.created = moment(new Date()).format("YYYY-MM-DD HH:mm:ss");  
                
                data.business_profile_id = app.options.business_profile_id;
                
                data.status = '1';
            }
            
            if(this.model.get('status') == app.options.statuses.APPROVED_RECIPE_STATUS.id) {
                data.status = app.options.statuses.PENDING_RECIPE_STATUS.id;
            }
            
            //console.log(data);
            
            this.model.set(data);
            
            //console.log(this.model.toJSON());
            
            //validation
            recipe_name_field.removeClass("red_style_border");
            recipe_type_field.removeClass("red_style_border");
            recipe_variation_field.removeClass("red_style_border");
            number_serves_field.removeClass("red_style_border");
            
            if (!this.model.isValid()) {
                var validate_error = this.model.validationError;

                if(validate_error == 'recipe_name') {
                    recipe_name_field.addClass("red_style_border");
                    return false;
                } else if(validate_error == 'recipe_type') {
                    recipe_type_field.addClass("red_style_border");
                    return false;
                } else if(validate_error == 'recipe_variation') {
                    recipe_variation_field.addClass("red_style_border");
                    return false;
                } else if(validate_error == 'number_serves') {
                    number_serves_field.addClass("red_style_border");
                    return false;
                } else {
                    alert(this.model.validationError);
                    return false;
                }
            }
            
            var id = this.model.get('id');
            var self = this;
            app.collections.recipes.create(this.model, {
                success: function (model, response) {
                    console.log(model.toJSON());
                    app.collections.recipes.reset();
                    app.collections.recipes.fetch({
                        data : app.models.get_recipe_params.toJSON(),
                        success: function (collection, response) {
                            if(self.save_method == 'save') {
                                self.controller.navigate("");
                                self.controller.navigate("!/edit_recipe/" + model.get('id'), true);
                            } else if(self.save_method == 'save_close') {
                                self.controller.navigate("!/nutrition_recipe/" + model.get('id'), true);
                            } else if(self.save_method == 'save_new') {
                                self.controller.navigate("");
                                self.controller.navigate("!/edit_recipe/0", true);
                            } else {
                                self.controller.navigate("!/my_recipes", true);
                            }
                        },
                        error: function (collection, response) {
                            alert(response.responseText);
                        }
                    }); 
                },
                error: function (model, response) {
                    alert(response.responseText);
                }
            });
        },
        
        
    });
            
    return view;
});