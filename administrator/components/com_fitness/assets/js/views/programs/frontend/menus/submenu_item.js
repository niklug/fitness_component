define([
	'jquery',
	'underscore',
	'backbone',
        'app',
	'text!templates/programs/frontend/menus/submenu_item.html'
], function ( $, _, Backbone, app, template ) {

    var view = Backbone.View.extend({
        
        template:_.template(template),
        
        render: function(){
            var data  = this.options.request_params_model.toJSON();
            data.app = app;
            _.extend(data, this.model.toJSON());
            var template = _.template(this.template(data));
            this.$el.html(template);
            return this;
        },

        events: {
            "click .close_item" : "onClickClose",
            "click .add_favourite" : "onClickAddFavourite",
            "click .remove_favourite" : "onClickRemoveFavourite",
            "click .edit_item" : "onClickEditItem",
            "click .delete_item" : "onClickDeleteItem",
            "click .copy_item" : "onClickCopy",
        },

        onClickClose : function() {
            var current_page = this.options.request_params_model.get('current_page');
            if(!current_page) {
                current_page = 'my_workouts';
            }
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
        
        onClickDeleteItem: function(event) {
            var self  = this;
            this.model.destroy({
                success: function (model) {
                    app.controller.navigate("!/my_workouts", true);
                },
                error: function (model, response) {
                    alert(response.responseText);
                }
            });

        },
        
        onClickEditItem : function(event) {
            var id = $(event.target).attr('data-id');
            app.controller.navigate("!/form_view/" + id, true);
        },
        
        onClickCopy : function(event) {
            var id = $(event.target).attr('data-id');
            this.copy_item(id);
        },
        
        copy_item : function(id) {
            var self = this;
            var data = {};
            var url = app.options.ajax_call_url;
            var view = 'Programs';
            var task = 'copyEvent';
            var table = '';
            data.id = id;
            $.AjaxCall(data, url, view, task, table, function(output){
                
            });
        },
    });
            
    return view;
});