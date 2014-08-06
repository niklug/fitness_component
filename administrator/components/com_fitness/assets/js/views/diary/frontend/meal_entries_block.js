define([
	'jquery',
	'underscore',
	'backbone',
        'app',
        'collections/diary/meal_entries',
        'models/diary/meal_entry',
        'views/diary/frontend/meal_entry_item',
	'text!templates/diary/frontend/meal_entries_block.html'
], function (
        $,
        _,
        Backbone,
        app,
        Meal_entries_collection,
        Meal_entry_model,
        Meal_entry_item_view,
        template 
    ) {

    var view = Backbone.View.extend({
            initialize: function(){
                app.collections.meal_entries = new Meal_entries_collection();
                app.collections.meal_entries.bind("add", this.addItem, this);
                app.collections.meal_entries.bind("reset", this.clearItems, this);
                app.collections.meal_entries.fetch({
                    data : {diary_id : this.model.get('id')},
                    success: function (collection, response) {
                        //console.log(collection.toJSON());
                    },
                    error: function (collection, response) {
                        alert(response.responseText);
                    }
                });

            },
            
            template:_.template(template),
            
            render: function(){
                var data = {};
                //console.log(this.model.toJSON());
                var template = _.template(this.template(data));
                this.$el.html(template);
                
                this.container_el = $(this.el).find("#meals_wrapper" );

                return this;
            },
            
            events: {
                "click .add_meal_entry" : "onClickAddMealEntry",

            },
            
            onClickAddMealEntry : function(event) {
                var container = $(event.target);
                
                container.closest( ".add_meal_entry_container" ).html(new Meal_entry_item_view({
                    model : new Meal_entry_model({diary_id : this.model.get('id')}),
                    plan_model : this.options.plan_model
                }).render().el);
            },
            
            addItem : function(model) {
                this.container_el.append(new Meal_entry_item_view({
                    model : model,
                    plan_model : this.options.plan_model
                }).render().el);
            },
            
            clearItems : function() {
                this.container_el.empty();
            },
        });
            
    return view;
});