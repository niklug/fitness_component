define([
	'jquery',
	'underscore',
	'backbone',
        'app',
        'views/goals/backend/list_item',
	'text!templates/goals/backend/list.html'
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
            
            this.status_obj = $.status(app.options.status_options);

        },
        
        template:_.template(template),
        
        render : function () {
            var data = {};
            data.$ = $;
            data.app = app;
            $(this.el).html(this.template(data));

            this.container_el = this.$el.find("#items_container");
            
            this.onRender();

            return this;
        },
        
        events: {
            "click #new_primary_goal" : "onClickNewPrimaryGoal",
            "click .edit_primary_goal" : "onClickEditPrimaryGoal"
        },
        
        onRender : function() {
            var self = this;
            $(this.el).show('0', function() {
                self.populateItems();
                self.connectPagination();
     
            });
        },
        
        connectPagination : function() {
            app.models.pagination = $.backbone_pagination({});
            
            var self = this;
            this.collection.once("add", function(model) {
                app.models.pagination.set({'items_total' : model.get('items_total')});
                
            });
            
            if(this.collection.models.length){
                app.models.pagination.set({'items_total' : this.collection.models[0].get('items_total')});
            }
            
              
            app.models.pagination.bind("change:currentPage", this.set_params_model, this);

            app.models.pagination.bind("change:items_number", this.set_params_model, this);
            
        },
        
        populateItems : function() {
            var self = this;
            _.each(this.collection.models, function(model) {
                self.addItem(model);
            });
        },
        
        
        addItem : function(model) {
            
            this.container_el.append(new List_item_view({model : model}).render().el); 
            this.$el.find( "#items_container tr:odd" ).addClass('row1');
            this.$el.find( "#items_container tr:even" ).addClass('row0');
        },
        
        clearItems : function() {
            this.container_el.empty();
        },
        
        set_params_model : function() {
            this.collection.reset();
            this.model.set({"page" : app.models.pagination.get('currentPage') || 1, "limit" : localStorage.getItem('items_number') || 10, uid : app.getUniqueId()});
        },
        
        onClickNewPrimaryGoal : function() {
            app.controller.navigate("!/form_primary/0", true);
        },
        
        onClickEditPrimaryGoal : function(event) {
            var id = $(event.target).attr('data-id');
            app.controller.navigate("!/form_primary/" + id, true);
        },

     
    });
            
    return view;
});