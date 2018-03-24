define([
	'jquery',
	'underscore',
	'backbone',
        'app',
	'text!templates/exercise_library/frontend/menus/submenu_item.html'
], function ( $, _, Backbone, app, template ) {

    var view = Backbone.View.extend({
        
        template:_.template(template),
        
        render: function(){
            var data  = this.model.toJSON();
            data.app = app;
            _.extend(data, this.options.request_params_model.toJSON());
            var template = _.template(this.template(data));
            this.$el.html(template);
            return this;
        },

        events: {
            "click .close_exercise" : "onClickClose",
            "click .add_favourite" : "onClickAddFavourite",
            "click .remove_favourites" : "onClickRemoveFavourites",
            "click .edit_exercise" : "onClickEditExercise",
            "click .trash_exercise" : "onClickTrashExercise",
            "click .delete_exercise" : "onClickDeleteExercise",
            "click .add_exercise" : "onClickAddExercise",
        },

        onClickClose : function() {
            var current_page = this.options.request_params_model.get('current_page');
            app.controller.navigate("!/" + current_page, true);
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
            app.controller.back();
        },
        
        onClickDeleteExercise : function(event) {
            var id = $(event.target).attr('data-id');
            app.controller.delete_exercise(id);
        },
        
        onClickEditExercise : function(event) {
            var id = $(event.target).attr('data-id');
            app.controller.navigate("!/form_view/" + id, true);
        },
        
        onClickAddExercise : function(event) {
            var id = $(event.target).attr('data-id');
            app.controller.add_event_exercise(id);
        }
    });
            
    return view;
});