define([
	'jquery',
	'underscore',
	'backbone',
        'app',
        'views/goals/backend/list_mini',
	'text!templates/goals/backend/list_item.html'
], function (
        $,
        _,
        Backbone,
        app,
        List_mini_view,
        template
    ) {

    var view = Backbone.View.extend({
        
        template : _.template(template),
        
        tagName : "table",
        
        className : "width_100",
        
        render : function(){
            var data = {item : this.model.toJSON()};
            data.app = app;
            data.$ = $;
            var template = _.template(this.template(data));
            this.$el.html(template);
            
            this.onRender();
            
            return this;
        },
        
        events: {
            "click .submit_primary_goal" : "onClickSubmitPrimaryGoal",
            "click .delete_primary_goal" : "onClickDelete"
        },
        
        onRender : function() {
            var self = this;
            $(this.el).show('0', function() {
                app.controller.connectStatus(self.model, self.$el, 'primary');
                
                self.loadMinigoalslist();
     
            });
        },
        
      
        loadMinigoalslist : function() {
            $(this.el).find(".minigoals_wrapper").html(new List_mini_view({collection : app.collections.mini_goals, model : this.model}).render().el);
        },
        
        onClickSubmitPrimaryGoal : function() {
            var self = this;
            this.model.save({status : app.options.statuses.EVELUATING_GOAL_STATUS.id}, {
                success: function (model, response) {
                    app.collections.primary_goals.add(model);
                    app.controller.sendGoalEmail(model.get('id'), 'GoalEvaluating');
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
                    app.collections.primary_goals.remove(model);
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