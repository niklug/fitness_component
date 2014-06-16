define([
	'jquery',
	'underscore',
	'backbone',
        'app',
        'collections/goals/mini_goals',
        'views/goals/frontend/list_mini',
	'text!templates/goals/frontend/list_item.html'
], function (
        $,
        _,
        Backbone,
        app,
        Mini_goals_collection,
        List_mini_view,
        template
    ) {

    var view = Backbone.View.extend({
        
        template : _.template(template),
        
        render : function(){
            var data = {item : this.model.toJSON()};
            data.app = app;
            data.$ = $;
            var template = _.template(this.template(data));
            this.$el.html(template);
            
            if(app.collections.mini_goals) {
                this.onRender();
                return this;
            }
            app.collections.mini_goals = new Mini_goals_collection();
            var self = this;
            app.collections.mini_goals.fetch({
                wait : true,
                data : {user_id : app.options.user_id},
                success : function (collection, response) {
                    self.onRender();
                },
                error : function (collection, response) {
                    alert(response.responseText);
                }
            }); 

            return this;
        },
        
        events: {
            "click .submit_primary_goal" : "onClickSubmitPrimaryGoal"
        },
        
        onRender : function() {
            var self = this;
            $(this.el).show('0', function() {
                app.controller.connectStatus(self.model.get('id'), self.model.get('status'), self.$el);
                
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
        }
        
    });
            
    return view;
});