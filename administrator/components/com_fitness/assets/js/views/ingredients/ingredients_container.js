define([
	'jquery',
	'underscore',
	'backbone',
        'app',
        'views/ingredients/ingredient_item',
	'text!templates/ingredients/ingredients_container.html',
], function (
        $,
        _,
        Backbone,
        app,
        Meal_ingredient_item_view,
        template 
    ) {

    var view = Backbone.View.extend({
        template:_.template(template),
        
        initialize : function() {
            this.connectIngredients();
            this.collection.bind("sync", this.onCollectionChange, this);
            this.collection.bind("remove", this.onCollectionChange, this);
        },
        
        render : function(){
            var data = {item : this.model.toJSON()};
            data.item.edit_mode = this.options.edit_mode;
            var template = _.template(this.template(data));
        
            this.$el.html(template);

            return this;
        },
        
        events: {
            "click .add_meal_ingredient" : "onClickAddIngredient",
        },
        
        connectIngredients : function() {
            var self = this;
            this.collection.fetch({
                data : self.options.request_data,
                success: function (collection, response) {
                    //self.onGetData(collection);
                },
                error: function (collection, response) {
                    alert(response.responseText);
                }
            })
        },
        
        onGetData : function(collection) {
            this.calculateTotals(collection);
            this.render();
            this.populateIngredients(collection);
        },
        
        populateIngredients : function(collection) {
            var self = this;
            _.each(collection.models, function(model) {
                self.addIngredientItem(model);
            });
        }, 
        
        addIngredientItem : function(model) {
            model.set({edit_mode : this.options.edit_mode});
            $(this.el).find(".meal_ingredients_wrapper").append(new Meal_ingredient_item_view({request_data : this.options.request_data, model : model, collection : this.collection, recipe_ingredients_collection : this.options.recipe_ingredients_collection}).render().el);
        },
        
        calculateTotals : function(collection) {
            //console.log(this.collection.toJSON());
            var protein_totals = this.getCollectionNameAmount('protein', collection);
            var fats_totals = this.getCollectionNameAmount('fats', collection);
            var carbs_totals = this.getCollectionNameAmount('carbs', collection);
            var calories_totals = this.getCollectionNameAmount('calories', collection);
            var energy_totals = this.getCollectionNameAmount('energy', collection);
            var saturated_fat_totals = this.getCollectionNameAmount('saturated_fat', collection);
            var total_sugars_totals = this.getCollectionNameAmount('total_sugars', collection);
            var sodium_totals = this.getCollectionNameAmount('sodium', collection);

            this.model.set({
                protein_totals : protein_totals,
                fats_totals : fats_totals,
                carbs_totals : carbs_totals,
                calories_totals : calories_totals,
                energy_totals : energy_totals,
                saturated_fat_totals : saturated_fat_totals,
                total_sugars_totals : total_sugars_totals,
                sodium_totals : sodium_totals,
            });
        },

        getCollectionNameAmount : function( name, collection) {
            var value =  collection.reduce(function(memo, value) { return parseFloat(memo) + parseFloat(value.get(name)) }, 0);
            return value.toFixed(2);
        },
        
        onClickAddIngredient : function() {
            var model = new this.options.ingredient_model(this.options.ingredient_model_data);
            this.collection.add(model);
            this.addIngredientItem(model);
        },
        
        onCollectionChange : function() {
            this.onGetData(this.collection);
        }

        
    });
            
    return view;
});