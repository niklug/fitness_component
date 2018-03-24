define([
	'jquery',
	'underscore',
	'backbone',
        'app',
        'collections/goals/mini_goals',
        'views/goals/backend/list_item',
	'text!templates/goals/backend/list.html'
], function (
        $,
        _, 
        Backbone, 
        app,
        Mini_goals_collection,
        List_item_view,
        template
    ) {

    var view = Backbone.View.extend({
        
        initialize : function() {
            this.collection.bind("add", this.addItem, this);
            this.collection.bind("reset", this.clearItems, this);
            
            app.collections.mini_goals = new Mini_goals_collection();
            var self = this;
            app.collections.mini_goals.fetch({
                wait : true,
                data : {user_id : app.options.client_id},
                success : function (collection, response) {
                    //console.log(collection.toJSON());
                },
                error : function (collection, response) {
                    alert(response.responseText);
                }
            }); 

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
            "click .view" : "onClickView",
            "click .edit_primary_goal" : "onClickEditPrimaryGoal",
            "change #list_type" : "onChangeListType",
            
            "click .trash" : "onClickTrash",
            "click .restore" : "onClickRestore",
            "click .publish_primary" : "onClickPublish",
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
            var readonly_allowed = app.controller.readonly_allowed(model);
            model.set({readonly_allowed : readonly_allowed});
            this.container_el.append(new List_item_view({model : model}).render().el);
            this.container_el.append('<hr>');
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
        
        onClickView : function(event) {
            var id = $(event.target).attr('data-id');
            app.controller.navigate("!/form_primary/" + id, true);
        },
        
        onChangeListType : function(event) {
            var list_type = $(event.target).val();
            app.models.request_params_primary.set({list_type : list_type,  uid : app.getUniqueId()});
            app.controller.connectGraph();
        },
        
        onClickTrash : function(event) {
            var id = $(event.target).attr('data-id');
            
            var model = this.collection.get(id);
            var self = this;
            model.save({state : '-2'}, {
                success: function (model, response) {
                    self.render();
                },
                error: function (model, response) {
                    alert(response.responseText);
                }
            });
        },
        
        onClickRestore : function(event) {
            var id = $(event.target).attr('data-id');
            var model = this.collection.get(id);
            var self = this;
            model.save({state : '1'}, {
                success: function (model, response) {
                    self.render();
                },
                error: function (model, response) {
                    alert(response.responseText);
                }
            });
        },

       
        onClickPublish : function(event) {
            var id = $(event.target).attr('data-id');
            var state = $(event.target).attr('data-state');
                  
            var published = 1;
            
            if(parseInt(state) == '1') {
                published = 0;
            }
            
            var model = this.collection.get(id);
            var self  = this;
            model.save({state : published}, {
                success: function (model, response) {
                    self.render();
                },
                error: function (model, response) {
                    alert(response.responseText);
                }
            });
        },
     
    });
            
    return view;
});