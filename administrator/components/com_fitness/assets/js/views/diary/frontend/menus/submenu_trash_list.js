define([
	'jquery',
	'underscore',
	'backbone',
        'app',
        'models/diary/diary',
	'text!templates/diary/frontend/menus/submenu_trash_list.html'
], function ( $, _, Backbone, app, Diary_model, template ) {

    var view = Backbone.View.extend({

        template:_.template(template),
        
        render: function(){
            var template = _.template(this.template());
            this.$el.html(template);
            return this;
        },

        events: {
            "click #close_trash" : "onClickCloseTrash",
            "click #trash" : "onClickTrash",
            "click #delete_selected" : "onClickDeleteSelected",
        },

        onClickCloseTrash : function() {
            app.controller.navigate("!/list_view", true);
        },

        onClickDeleteSelected : function() {
            var selected = new Array();
            $('.trash_checkbox:checked').each(function() {
                selected.push($(this).attr('data-id'));
            });

            var self = this;
            if(selected.length > 0) {
                _.each(selected, function(item, key){ 
                    self.deleteItem(item);
                });
            }
            $("#select_trashed").prop("checked", false);
        },
        
        deleteItem : function(id) {
            this.model = new Diary_model();
            this.model.set({id : id});
            var self = this;
            this.model.destroy({
                success: function (model, response) {
                    app.collections.items.remove(model);
                    self.hide_items(id);
                },
                error: function (model, response) {
                    alert(response.responseText);
                }
            });
        },
        
        hide_items : function(items) {
            var self = this;
            var items = items.split(",");
            _.each(items, function(item, key){ 
                $(".diary_row[data-id=" + item + "]").fadeOut();
            });
        },

    });
            
    return view;
});