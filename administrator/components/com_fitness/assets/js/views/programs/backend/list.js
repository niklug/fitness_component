define([
	'jquery',
	'underscore',
	'backbone',
        'app',
        'views/programs/backend/list_item',
	'text!templates/programs/backend/list.html'
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
        
        render : function () {
            var data = {};
            data.$ = $;
            data.app = app;
            $(this.el).html(this.template(data));
            
            this.container_el = this.$el.find("#items_container");
            
            var self = this;
            if(this.collection.length) {
                _.each(this.collection.models, function(model) {
                    self.addItem(model);
                });
            }
            
            
            return this;
        },
        
        events: {
            "click #sort_exercise_name" : "onClickSortExerciseName",
            "click #sort_created" : "onClickSortCreated",
            "click #sort_created_by" : "onClickSortCreatedBy",
            "click #sort_status" : "onClickSortStatus",
            "click .trash" : "onClickTrash",
            "click .restore" : "onClickRestore",
            "click .delete" : "onClickDelete",
            "click .view" : "onClickView",
            "click .copy_exercise" : "onClickCopyExercise",
            "click #select_trashed" : "onClickSelectTrashed",
        },
        
        addItem : function(model) {
            
            model.set({edit_allowed : app.controller.edit_allowed(model)});
            
            this.item = new List_item_view({el : this.container_el, model : model}).render(); 

            app.models.pagination.set({'items_total' : model.get('items_total')});
            
            this.$el.find( "#items_container tr:odd" ).addClass('row1');
            this.$el.find( "#items_container tr:even" ).addClass('row0');
        },
        
        clearItems : function() {
            this.container_el.empty();
        },
        
    });
            
    return view;
});