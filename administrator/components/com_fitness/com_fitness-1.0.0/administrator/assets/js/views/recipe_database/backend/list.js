define([
	'jquery',
	'underscore',
	'backbone',
        'app',
        'views/recipe_database/backend/list_item',
	'text!templates/recipe_database/backend/list.html'
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
            "click #sort_recipe_name" : "sort_recipe_name",
            "click #sort_created_by" : "sort_created_by",
            "click #sort_created" : "sort_created",
            "click #sort_calories" : "sort_calories",
            "click #sort_energy" : "sort_energy",
            "click #sort_protein" : "sort_protein",
            "click #sort_fats" : "sort_fats",
            "click #sort_saturated_fat" : "sort_saturated_fat",
            "click #sort_carbs" : "sort_carbs",
            "click #sort_total_sugars" : "sort_total_sugars",
            "click #sort_sodium" : "sort_sodium",
            "click #sort_status" : "sort_status",
   
            "click .trash" : "onClickTrash",
            "click .restore" : "onClickRestore",
            "click .delete" : "onClickDelete",
            "click .view" : "onClickView",
            "click #select_trashed" : "onClickSelectTrashed",
            
            "click .publish" : "onClickPublish",
            
            "click .copy_item" : "onClickCopy",
           
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
            model.set({edit_allowed : app.controller.edit_allowed(model)});
            new List_item_view({el : this.container_el, model : model}); 
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
        
        sort_recipe_name : function() {
            this.model.set({sort_by : 'a.recipe_name', order_dirrection : 'ASC'});
        },
        
        sort_created_by : function() {
            this.model.set({sort_by : 'author', order_dirrection : 'ASC'});
        },
        
        sort_created : function() {
            this.model.set({sort_by : 'a.created', order_dirrection : 'DESC'});
        },
        
        sort_calories : function() {
            this.model.set({sort_by : 'calories', order_dirrection : 'DESC'});
        },
        
        sort_energy : function() {
            this.model.set({sort_by : 'energy', order_dirrection : 'DESC'});
        },
        
        sort_protein : function() {
            this.model.set({sort_by : 'protein', order_dirrection : 'DESC'});
        },
        
        sort_fats : function() {
            this.model.set({sort_by : 'fats', order_dirrection : 'DESC'});
        },
        
        sort_saturated_fat : function() {
            this.model.set({sort_by : 'saturated_fat', order_dirrection : 'DESC'});
        },
        
        sort_carbs : function() {
            this.model.set({sort_by : 'carbs', order_dirrection : 'DESC'});
        },
        
        sort_total_sugars : function() {
            this.model.set({sort_by : 'total_sugars', order_dirrection : 'DESC'});
        },
        
        sort_sodium : function() {
            this.model.set({sort_by : 'sodium', order_dirrection : 'DESC'});
        },
        
        sort_status : function() {
            this.model.set({sort_by : 'a.status', order_dirrection : 'ASC'});
        },
        
        onClickTrash : function(event) {
            var id = $(event.target).attr('data-id');
            var model = this.collection.get(id);
            var self  = this;
            model.save({state : '-2'}, {
                success: function (model, response) {
                    app.controller.update_list();
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
                    app.controller.update_list();
                },
                error: function (model, response) {
                    alert(response.responseText);
                }
            });
        },

        onClickDelete : function(event) {
            var id = $(event.target).attr('data-id');
            var model = this.collection.get(id);
            var self = this;
            model.destroy({
                success: function (model) {
                    app.controller.update_list();
                },
                error: function (model, response) {
                    alert(response.responseText);
                }
            });
        },
        
        
        onClickSelectTrashed : function(event) {
            $(".trash_checkbox").prop("checked", false);

            if($(event.target).attr("checked")) {
                $(".trash_checkbox").prop("checked", true);
            }
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
                    app.controller.update_list();
                },
                error: function (model, response) {
                    alert(response.responseText);
                }
            });
        },
        
        onClickCopy : function(event) {
            var id = $(event.target).attr('data-id');
            app.controller.copy_recipe(id, true);
        },
       
        onClickView : function(event) {
            var id = $(event.target).attr('data-id');
            app.controller.navigate("!/form_view/" + id, true);
        },
    });
            
    return view;
});