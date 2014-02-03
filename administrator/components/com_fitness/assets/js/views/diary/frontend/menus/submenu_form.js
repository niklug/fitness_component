define([
	'jquery',
	'underscore',
	'backbone',
        'app',
	'text!templates/diary/frontend/menus/submenu_form.html'
], function ( $, _, Backbone, app, template ) {

    var view = Backbone.View.extend({
        
        initialize : function() {
            this.active_plan_data = app.models.active_plan_data.toJSON();
        },
        
        template:_.template(template),
        
        render: function(){
            var template = _.template(this.template());
            this.$el.html(template);
            return this;
        },

        events: {
            "click #next" : "onClickNext",
            "click #cancel" : "onClickCancel",
        },

        onClickNext : function() {
            var self  = this;
            $("#create_item_form" ).die().live('submit', function(event) {
                event.preventDefault();
                var data = Backbone.Syphon.serialize(this);
                data.nutrition_plan_id = self.active_plan_data.id;
                data.client_id = self.active_plan_data.client_id;
                data.trainer_id = self.active_plan_data.trainer_id;
                data.goal_category_id = self.active_plan_data.mini_goal;
                data.nutrition_focus = self.active_plan_data.nutrition_focus;
                data.created = moment(new Date()).format("YYYY-MM-DD HH:mm:ss"); 
                data.state = '1';
                
                self.model.set(data);
                
                if (!self.model.isValid()) {
                    alert(self.model.validationError);
                    return false;
                }

                self.model.save(null, {
                    success: function (model, response) {
                        app.controller.navigate("!/item_view/" + model.get('id'), true);
                    },
                    error: function (model, response) {
                        alert(response.responseText);
                    }
                });
            });
             
            $("#create_item_form").submit();
        },

        onClickCancel : function() {
            app.controller.navigate("!/list_view", true);
        },
    });
            
    return view;
});