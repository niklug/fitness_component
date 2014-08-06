define([
	'jquery',
	'underscore',
	'backbone',
        'app',
        'collections/diary/meal_entries',
        'models/diary/meal_entry',
	'text!templates/diary/frontend/meal_entry_item.html',
        'jquery.timepicker'
], function (
        $,
        _,
        Backbone,
        app,
        Meal_entries_collection,
        Meal_entry_model,
        template 
    ) {

    var view = Backbone.View.extend({
            initialize: function(){
                
            },
            
            template:_.template(template),
            
            render: function(){
                var data = {item : this.model.toJSON()};
                console.log(this.model.toJSON());
                var template = _.template(this.template(data));
                this.$el.html(template);
                
                $(this.el).find('.meal_time').timepicker({ 'timeFormat': 'H:i', 'step': 15 });

                return this;
            },
            
            events : {
                "click .save_meal_entry" : "onClickSave",
                "click .delete_meal_entry" : "onClickClose"
            },
            
            onClickSave :function(event) {
                var container = $(event.target);
                
                var meal_time_field = $(this.el).find('.meal_time');
                var water_field = $(this.el).find('.meal_water');
                var previous_water_field = $(this.el).find('.meal_previous_water');
                var self  = this;
                var data = {};
                data.meal_time = meal_time_field.val();
                data.water = water_field.val();
                data.previous_water = previous_water_field.val();
                data.nutrition_plan_id = this.options.plan_model.get('id');
                this.model.set(data);
                
                //console.log(this.model.toJSON());

                meal_time_field.removeClass("red_style_border");
                water_field.removeClass("red_style_border");

                if (!this.model.isValid()) {
                    var validate_error = this.model.validationError;

                    if(validate_error == 'meal_time') {
                        meal_time_field.addClass("red_style_border");
                        return false;
                    } else if(validate_error == 'water') {
                        water_field.addClass("red_style_border");
                        return false;
                    }  else {
                        alert(this.model.validationError);
                        return false;
                    }
                }

                this.model.save(null, {
                    success: function (model, response) {
                        app.collections.meal_entries.add(model);
                        self.addHtml(container);
                    },
                    error: function (model, response) {
                        alert(response.responseText);
                    }
                });
            },
            
            onClickClose : function(event) {
                if(this.model.isNew()) {
                    var container = $(event.target);
                    this.addHtml(container);
                    return;
                }

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
            
            addHtml : function(container) {
                container.closest( ".add_meal_entry_container" ).html('<div class="add_meal_entry">click to create a new entry</div>');
            },
            
            close :function() {
                $(this.el).unbind();
                $(this.el).remove();
            },
  
        });
            
    return view;
});