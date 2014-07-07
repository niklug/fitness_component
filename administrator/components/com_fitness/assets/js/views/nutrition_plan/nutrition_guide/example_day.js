define([
	'jquery',
	'underscore',
	'backbone',
        'app',
        'views/nutrition_plan/nutrition_guide/example_day_meal',
	'text!templates/nutrition_plan/nutrition_guide/example_day.html',
        'jquery.timepicker'
], function ( $, _, Backbone, app, Example_day_meal_view, template ) {

    var view = Backbone.View.extend({
        
        template:_.template(template),

        render: function(){
            var menu_plan = app.models.menu_plan.toJSON();
            
            $(this.el).html(this.template({ menu_plan : menu_plan}));
            
            var self = this;

            this.mealListItemViews = {};

            this.collection.on("add", function(meal) {
                app.views.example_day_meal = new Example_day_meal_view({collection : this,  model : meal, nutrition_plan_id : self.options.nutrition_plan_id}); 
                self.$el.find("#example_day_meal_list").append( app.views.example_day_meal.render().el );

                self.mealListItemViews[ meal.cid ] = app.views.example_day_meal;
            });

            this.collection.on("remove", function(meal, options) {
                self.mealListItemViews[ meal.cid ].close();
                delete self.mealListItemViews[ meal.cid ];
            });
       
            return this;
        },

        events:{
            "click #add_meal": "add_meal"
        },

        add_meal:function () {
            //console.log(this.options.nutrition_plan_id);
            var example_day_id = this.options.example_day_id;
            app.controller.navigate("!/example_day/" + example_day_id + "/" + this.options.nutrition_plan_id);
            app.controller.navigate("!/add_example_day_meal/" + example_day_id + "/" + this.options.nutrition_plan_id, true);
        }

    });
            
    return view;
});