define([
	'jquery',
	'underscore',
	'backbone',
        'app',
        'views/client_progress/backend/sub_search_item',
	'text!templates/client_progress/backend/sub_search_container.html'

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
        },

        
        template:_.template(template),
        
        render: function(){
            var data = {item : {}};
            //console.log(data);
            data.app = app;
            data.$ = $;
            var template = _.template(this.template(data));
            this.$el.html(template);
            
            this.container_el = this.$el.find("#sub_search_items");
            
            this.onRender();
            
            return this;
        },
        
        events : {

            "click #search_sub" : "search",
            "click #clear_sub" : "clear",
        },
        
        onRender : function() {
            var self = this;
            $(this.el).show('0', function() {
                self.loadItems();
            });
        },
        
        loadItems : function() {
            var self = this;
            _.each(this.collection.models, function(model) {
                self.addItem(model);
            });
        },
        
        addItem : function(model) {
            this.container_el.append(new List_item_view({ model : model}).render().el); 
        },
        
        search : function() {

        },
        
        clear : function(){

        },
  
    });
            
    return view;
});