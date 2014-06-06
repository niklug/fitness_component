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
            
            app.controller.connectStatus(this.model.get('id'), this.model.get('status'), this.$el);
            
            return this;
        },
        
        
        events: {
            "click .submit_mini_goal" : "onClickSubmit",
            "click .delete_mini_goal" : "onClickDelete"
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
        },
        
        onClickDelete : function(event) {
            var self = this;
            this.model.destroy({
                success: function (model) {
                    app.collections.mini_goals.remove(model);
                    self.close();
                },
                error: function (model, response) {
                    alert(response.responseText);
                }
            });
        },
        
        close :function() {
            $(this.el).unbind();
            $(this.el).remove();
        },

    });
            
    return view;
});