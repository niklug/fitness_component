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

        initialize: function(){
            this.controller = app.routers.nutrition_plan;
        },

        render: function(){
            $(this.el).html(this.template());
            
            var self = this;

            this.mealListItemViews = {};

            this.collection.on("add", function(meal) {
                app.views.example_day_meal = new Example_day_meal_view({collection : this,  model : meal}); 
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
            var example_day_id = this.options.example_day_id;
            this.controller.navigate("!/example_day/" + example_day_id);
            this.controller.navigate("!/add_example_day_meal/" + example_day_id, true);
        }

    });
            
    return view;
});