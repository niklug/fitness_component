define([
	'jquery',
	'underscore',
	'backbone',
        'app',
        'views/recipe_database/frontend/nutrition_database/list_item',
	'text!templates/recipe_database/frontend/nutrition_database/list.html'
], function ( $, _, Backbone, app, List_item_view, template ) {

    var view = Backbone.View.extend({
        
        initialize : function() {
            this.collection.bind("add", this.addItem, this);
            this.collection.bind("reset", this.clearItems, this);
            app.models.pagination = $.backbone_pagination({});
        },

        
        template:_.template(template),

        render : function(){
            var template = _.template(this.template());
            this.$el.html(template);
            this.container_el = this.$el.find("#ingredients_items");
            return this;
        },

        events: {
            "click .search_ingredients" : "onClickSearch",
            "keypress #search_field": "filterOnEnter",
            "click .clear" : "onClickClear",
        },
        
        addItem : function(model) {
            this.list_item = new List_item_view({el : this.container_el, model : model}); 
            this.list_item.render(); 

            app.models.pagination.set({'items_total' : model.get('items_total')});
        },

        clearItems : function() {
            this.container_el.empty();
            app.models.pagination.set({'items_total' : 0});
        },
        
        filterOnEnter : function(event) { 
            if(event.which === 13) {
                this.onClickSearch();
            }
        },

        onClickSearch : function() {
            app.models.request_params_ingredients.set({page : 1, search : $("#search_field").val()});
        },

        onClickClear : function(){
            $("#search_field").val('');
            app.models.request_params_ingredients.set({page : 1, search : ''});
            this.container_el.empty();
        }
    });
            
    return view;
});