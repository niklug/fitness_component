define([
	'jquery',
	'underscore',
	'backbone',
        'app',
	'text!templates/nutrition_plan/nutrition_guide/daily_targets.html',
], function ( 
        $,
        _,
        Backbone,
        app,
        template
    ) {

    var view = Backbone.View.extend({
        
        initialize : function() {
            this.collection.bind("add", this.calculateTotals, this);
            this.collection.bind("remove", this.calculateTotals, this);
            this.collection.bind("reset", this.calculateTotals, this);
            
            this.model.set({
                calories_amount : '0',
                protein_amount : '0',
                fats_amount : '0',
                carbs_amount : '0',
                total_sugars_amount : '0'
            });
        },
        
        template : _.template(template),

        render: function(){
            //console.log(this.model.toJSON());
            var data = {
                item : this.model.toJSON(
            )};
            $(this.el).html(this.template(data));
            return this;
        },

        calculateTotals : function() {
            //console.log(this.collection.toJSON());
            var calories_amount = this.getCollectionNameAmount('calories');
            var protein_amount = this.getCollectionNameAmount('protein');
            var fats_amount = this.getCollectionNameAmount('fats');
            var carbs_amount = this.getCollectionNameAmount('carbs');
            var total_sugars_amount = this.getCollectionNameAmount('total_sugars');
            
            this.model.set({
                calories_amount : calories_amount,
                protein_amount : protein_amount,
                fats_amount : fats_amount,
                carbs_amount : carbs_amount,
                total_sugars_amount : total_sugars_amount
            });
            
            this.render();
        },
        
        getCollectionNameAmount : function(name) {
            var value =  this.collection.reduce(function(memo, value) { return parseFloat(memo) + parseFloat(value.get(name)) }, 0);
            return value.toFixed(0);
        }
    });
            
    return view;
});