define([
	'jquery',
	'underscore',
	'backbone',
        'app',
        'models/diary/meal_entry',
        'views/diary/frontend/meal_entry_item',
	'text!templates/diary/frontend/meal_entries_block.html'
], function (
        $,
        _,
        Backbone,
        app,
        Meal_entry_model,
        Meal_entry_item_view,
        template 
    ) {

    var view = Backbone.View.extend({
            initialize: function(){
                app.collections.meal_entries.bind("add", this.onAddModel, this);
            },
            
            template:_.template(template),
            
            render: function(){
                var data = {item : this.model.toJSON()};
                //console.log(this.model.toJSON());
                var template = _.template(this.template(data));
                this.$el.html(template);
                
                this.container_el = $(this.el).find("#meals_wrapper" );
                
                this.loadItems();

                return this;
            },
            
            events: {
                "click .add_meal_entry" : "onClickAddMealEntry",

            },
            
            onClickAddMealEntry : function(event) {
                var container = $(event.target);
                
                container.closest( ".add_meal_entry_container" ).html(new Meal_entry_item_view({
                    model : new Meal_entry_model({diary_id : this.model.get('id')}),
                    diary_model : this.model,
                    plan_model : this.options.plan_model
                }).render().el);
            },
            
            loadItems : function() {
                app.collections.meal_entries.sort();
                this.clearItems();
                var self = this;
                _.each(app.collections.meal_entries.models, function(model) {
                    self.addItem(model);
                });
            },
            
            addItem : function(model) {
                this.container_el.append(new Meal_entry_item_view({
                    model : model,
                    diary_model : this.model,
                    plan_model : this.options.plan_model
                }).render().el);
            },
            
            clearItems : function() {
                this.container_el.empty();
            },
            
            onAddModel : function(model) {
                this.addItem(model);
            }
        });
            
    return view;
});