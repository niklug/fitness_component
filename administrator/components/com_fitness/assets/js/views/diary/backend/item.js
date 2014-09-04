define([
	'jquery',
	'underscore',
	'backbone',
        'app',
        'views/diary/frontend/target_block',
        'views/diary/frontend/meal_entries_block',
        'views/diary/frontend/totals',
        'views/status/index',
	'text!templates/diary/backend/item.html',
        'jqueryui',
        'jquery.flot',
        'jquery.flot.time',
        'jquery.flot.pie',
        'jquery.drawPie',
        'jquery.timepicker'
        
], function (
        $,
        _,
        Backbone,
        app,
        Target_block_view,
        Meal_entries_block,
        Totals_view,
        Status_view,
        template 
    ) {

    var view = Backbone.View.extend({
        
        initialize : function() {
            this.active_plan_data = app.models.active_plan_data.toJSON();
        },
        
        template : _.template(template),

        render : function () {
            
            var data = {item : this.model.toJSON()};
            data.active_plan_data = this.active_plan_data;
            data.$ = $;
            $(this.el).html(this.template(data));
            
            this.connectTargets();
            
            this.connectTotals();
            
            this.connectMealEntries();
            
            this.connectPrimaryGoalStatus(new Backbone.Model({status : app.models.active_plan_data.get('primary_goal_status')}), "#primary_goal_status");
            
            this.connectMiniGoalStatus(new Backbone.Model({status : app.models.active_plan_data.get('mini_goal_status')}), "#mini_goal_status");

            return this;
        },
        
        connectPrimaryGoalStatus : function(model, target) {
            app.options.primary_goal_status_options.button_not_active = true;
            $(this.el).find(target).html(new Status_view({
                model : model,
                settings : app.options.primary_goal_status_options
            }).render().el);
        },
        
        connectMiniGoalStatus : function(model, target) {
            app.options.mini_goal_status_options.button_not_active = true;
            $(this.el).find(target).html(new Status_view({
                model : model,
                settings : app.options.mini_goal_status_options
            }).render().el);
        },

        connectTargets : function() {
            $(this.el).find("#targets_wrapper").html(new Target_block_view({model : this.model}).render().el);
        },

        connectMealEntries : function() {
            this.loadMealEntries();
        },
        
        loadMealEntries : function() {
            app.views.meal_entries_block = new Meal_entries_block({model : this.model, plan_model : app.models.active_plan_data});
            $(this.el).find("#meal_entries_wrapper").html(app.views.meal_entries_block.render().el);
        },
        
        connectTotals : function() {
            
            app.views.totals = new Totals_view({model : this.model});
            $(this.el).find("#totals_wrapper").html(app.views.totals.render().el);
        }

 
    });
            
    return view;

});