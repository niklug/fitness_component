define([
	'jquery',
	'underscore',
	'backbone',
        'app',
        'views/goals/frontend/form_mini',
	'text!templates/goals/frontend/list_item_mini.html'
], function ( $, _, Backbone, app, Form_mini_view, template ) {

    var view = Backbone.View.extend({
        
        template : _.template(template),
        
        render : function(){
            var data = {item : this.model.toJSON()};
            data.item.primary_goal = this.options.primary_goal_model.toJSON();
            data.app = app;
            data.$ = $;
            var template = _.template(this.template(data));
            this.$el.html(template);
            
            this.connectStatus(this.model.get('id'), this.model.get('status'));
            
            return this;
        },
        
        
        events: {
            "click .submit_mini_goal" : "onClickSubmit"
        },
        
        connectStatus : function(id, status) {
            var status_obj = $.status(app.options.status_options);
              
            var html =  status_obj.statusButtonHtml(id, status);

            this.$el.find("#status_button_place_" + id).html(html);

            //status_obj.run();
        },
        
        onClickSubmit : function() {
            var self = this;
            this.model.save({status : app.options.statuses.EVELUATING_GOAL_STATUS.id}, {
                success: function (model, response) {
                    app.collections.mini_goals.add(model);
                    app.controller.sendGoalEmail(model.get('id'), 'GoalEvaluatingMini');
                    self.render();
                },
                error: function (model, response) {
                    alert(response.responseText);
                }
            });
        }

    });
            
    return view;
});