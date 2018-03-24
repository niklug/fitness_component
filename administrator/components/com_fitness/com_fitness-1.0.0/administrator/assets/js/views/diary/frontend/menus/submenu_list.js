define([
	'jquery',
	'underscore',
	'backbone',
        'app',
        'models/diary/diary',
	'text!templates/diary/frontend/menus/submenu_list.html'
], function ( $, _, Backbone, app, Diary_model, template ) {

    var view = Backbone.View.extend({

        template:_.template(template),
        
        render: function(){
            var template = _.template(this.template());
            this.$el.html(template);
            return this;
        },

        events: {
            "click #add" : "onClickAdd",
            "click #view_trash" : "onClickViewTrash",
            "click #trash_selected" : "onClickTrashSelected",
        },

        onClickAdd : function() {
            app.controller.navigate("!/create_item", true);
        },

        onClickViewTrash : function() {
            app.controller.navigate("!/trash_list", true);
        },

        onClickTrashSelected : function() {
            var selected = new Array();
            $('.trash_checkbox:checked').each(function() {
                selected.push($(this).attr('data-id'));
            });
   
            var self = this;
            if(selected.length > 0) {
                _.each(selected, function(item, key){ 
                    self.trashItem(item);
                });
            }
            $("#select_trashed").prop("checked", false);
        },
        
        trashItem : function(id) {
            this.model = new Diary_model();

            var self  = this;
            this.model.save({id : id, state : '-2'}, {
                success: function (model, response) {
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