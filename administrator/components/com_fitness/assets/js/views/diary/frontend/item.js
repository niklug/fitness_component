define([
	'jquery',
	'underscore',
	'backbone',
        'app',
        'models/nutrition_plan/target',
        'views/diary/frontend/target_block',
        'views/diary/frontend/meal_entries_block',
        'views/diary/frontend/totals',
	'text!templates/diary/frontend/item.html',
        'jqueryui',
        'jquery.flot',
        'jquery.flot.time',
        'jquery.flot.pie',
        'jquery.drawPie',
        'jquery.macronutrientTargets',
        'jquery.timepicker',
        'jquery.gredient_graph',
], function (
        $,
        _,
        Backbone,
        app,
        Target_model,
        Target_block_view,
        Meal_entries_block,
        Totals_view,
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
            
            this.connectTargets(this.active_plan_data.id);
            
            this.connectMealEntries();

            return this;
        },
        
        connectTargets : function(id) {
            if(app.models.target) {
                this.loadTargets(id);
                return;
            }
            
            app.models.target = new Target_model({nutrition_plan_id : id});
            var self = this;
            app.models.target.fetch({
                data : {nutrition_plan_id : id},
                success : function (model, response) {
                    self.loadTargets(id);
                },
                error : function (collection, response) {
                    alert(response.responseText);
                }
            })
        },
        
        loadTargets : function(id) {
            this.connectTotals();
            $(this.el).find("#targets_wrapper").html(new Target_block_view({model : app.models.target, item_model : app.models.active_plan_data}).render().el);
        },

        connectMealEntries : function() {
            this.loadMealEntries();
        },
        
        loadMealEntries : function() {
            app.views.meal_entries_block = new Meal_entries_block({model : this.model, plan_model : app.models.active_plan_data});
            $(this.el).find("#meal_entries_wrapper").html(app.views.meal_entries_block.render().el);
        },
        
        connectTotals : function() {
            app.views.totals = new Totals_view({model : this.model, plan_model : app.models.active_plan_data});
            $(this.el).find("#totals_wrapper").html(app.views.totals.render().el);
        }

 
    });
            
    return view;

});