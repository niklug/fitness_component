define([
	'jquery',
	'underscore',
	'backbone',
        'app',
        'views/nutrition_plan/nutrition_guide/backend/menu_plan_item',
	'text!templates/nutrition_plan/nutrition_guide/backend/menu_plan_list.html'
], function (
        $,
        _, 
        Backbone,
        app, 
        List_item_view,
        template
    ) {

    var view = Backbone.View.extend({
        initialize : function() {
            this.collection.bind("add", this.addItem, this);
            this.collection.bind("reset", this.clearItems, this);
        },
        
        
        template:_.template(template),

        render: function(){
            var template = _.template(this.template());
            this.$el.html(template);
            this.container_el = this.$el.find("#items_container");
            this.onRender();  
            return this;
        },
        
        onRender : function() {
            var self = this;
            $(this.el).show('0', function() {
                self.populateItems();
            });
        },

        populateItems : function() {
            var self = this;
            _.each(this.collection.models, function(model) {
                self.addItem(model);
            });
        },
        
        addItem : function(model) {
            this.container_el.append(new List_item_view({nutrition_plan_id : this.options.nutrition_plan_id, model : model, collection : this.collection}).render().el); 
        },
        
        clearItems : function() {
            this.container_el.empty();
        },
    });
            
    return view;
});