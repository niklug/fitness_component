define([
	'jquery',
	'underscore',
	'backbone',
        'app',
	'text!templates/nutrition_plan/nutrition_guide/menu_plan_header.html'
], function ( $, _, Backbone, app, template ) {

    var view = Backbone.View.extend({
        
        template:_.template(template),

        initialize: function(){
            this.controller = app.routers.nutrition_plan;
        },

        render: function(){
            var template = _.template(this.template(this.model.toJSON()));
            this.$el.html(template);
            return this;
        },

        events: {
            "click #delete_menu_plan" : "onClickDelete",
            "click #save_menu_plan" : "onClickSave",
        },
        
        onClickSave : function(event) {
            event.preventDefault();
            var data = Backbone.Syphon.serialize(this);
            data.created_by = app.options.client_id;
            this.model.set(data);
            
            this.model.unset('assessed_by_name');
            this.model.unset('created_by_name');
            //console.log(this.model.toJSON());
            //validation
            var menu_name_field = this.$el.find('#menu_name');
            menu_name_field.removeClass("red_style_border");
            var start_date_field = this.$el.find('#start_date');
            start_date_field.removeClass("red_style_border");
            if (!this.model.isValid()) {
                var validate_error = this.model.validationError;

                if(validate_error == 'menu_name') {
                    menu_name_field.addClass("red_style_border");
                    return false;
                } else if(validate_error == 'start_date') {
                    start_date_field.addClass("red_style_border");
                    return false;
                } else {
                    alert(this.model.validationError);
                    return false;
                }
            }
            //
            var self = this;
            if (this.model.isNew()) {
                this.collection.create(this.model, {
                    wait: true,
                    success: function (model, response) {
                        console.log(model);
                    },
                    error: function (model, response) {
                        alert(response.responseText);
                    }
                })
            } else {
                this.model.save(null, {
                    success: function (model, response) {
                        console.log(model);
                    },
                    error: function (model, response) {
                        alert(response.responseText);
                    }
                });
            }
            
            
        },

        onClickDelete : function(event) {
            this.model.destroy( {
                success: function (model, response) {
                    app.routers.nutrition_plan.navigate("!/nutrition_guide", true);
                },
                error: function (model, response) {
                    alert(response.responseText);
                }
            });
        },
    });
            
    return view;
});