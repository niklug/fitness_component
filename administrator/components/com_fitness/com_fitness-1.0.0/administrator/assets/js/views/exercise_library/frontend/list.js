define([
	'jquery',
	'underscore',
	'backbone',
        'app',
        'views/exercise_library/frontend/list_item',
	'text!templates/exercise_library/frontend/list.html'
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
            "click .view_exercise" : "onClickViewExercise",
            "click .add_favourite" : "onClickAddFavourite",
            "click .remove_favourites" : "onClickRemoveFavourite",
            "click .trash_exercise" : "onClickTrashExercise",
            "click .delete_exercise" : "onClickDeleteExercise",
            "click .restore_exercise" : "onClickRestoreExercisee",
            "click .add_exercise" : "onClickAddExercise",
        },
        
        addItem : function(model) {
            var edit_allowed = app.controller.edit_allowed(model);
            
            model.set({edit_allowed : edit_allowed});
            
            var current_page = this.model.get('current_page');
            
            if(current_page == 'trash_list' && !edit_allowed) {
                return;
            }
            
            this.item = new List_item_view({el : this.container_el, model : model}).render(); 

            app.models.pagination.set({'items_total' : model.get('items_total')});
            
            this.$el.find( "#items_container tr:odd" ).addClass('row1');
            this.$el.find( "#items_container tr:even" ).addClass('row0');
        },
        
        clearItems : function() {
            this.container_el.empty();
        },
        
        onClickAddFavourite : function(event) {
            var id = $(event.target).attr('data-id');
            app.controller.add_favourite(id);
        },
        
        onClickRemoveFavourite : function(event) {
            var id = $(event.target).attr('data-id');
            app.controller.remove_favourite(id);
        },
        
        onClickTrashExercise : function(event) {
            var id = $(event.target).attr('data-id');
            app.controller.trash_exercise(id);
        },
        
        onClickDeleteExercise : function(event) {
            var id = $(event.target).attr('data-id');
            app.controller.delete_exercise(id);
        },
        
        onClickRestoreExercisee : function(event) {
            var id = $(event.target).attr('data-id');
            app.controller.restore_exercise(id);
        },
        
        onClickViewExercise : function(event) {
            var id = $(event.target).attr('data-id');
            app.controller.navigate("!/item_view/" + id, true);
        },
        onClickAddExercise : function(event) {
            var id = $(event.target).attr('data-id');
            app.controller.add_event_exercise(id);
        }
        
    });
            
    return view;
});