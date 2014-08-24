define([
	'jquery',
	'underscore',
	'backbone',
        'app',
	'text!templates/diary/frontend/menus/submenu_item.html'
], function ( $, _, Backbone, app, template ) {

    var view = Backbone.View.extend({
        
        initialize : function() {
            this.active_plan_data = app.models.active_plan_data.toJSON();
        },
        
        template:_.template(template),
        
        render: function(){
            var template = _.template(this.template(this.model.toJSON()));
            this.$el.html(template);
            return this;
        },

        events: {
            "click #close" : "onClickClose",
            "click #submit" : "onClickSubmit",
            "click #delete" : "onClickDelete",
            "click #trash" : "onClickTrash",
        },

        onClickClose : function() {
            app.controller.navigate("!/list_view", true);
            
        },
        
        onClickDelete : function() {
            var self = this;
            this.model.destroy({
                success: function (model, response) {
                    app.collections.items.remove(model);
                    self.onClickClose();
                },
                error: function (model, response) {
                    alert(response.responseText);
                }
            });
        },
        
        onClickDelete : function() {
            var self = this;
            this.model.destroy({
                success: function (model, response) {
                    app.collections.items.remove(model);
                    self.onClickClose();
                },
                error: function (model, response) {
                    alert(response.responseText);
                }
            });
        },
        
        onClickTrash : function() {
            var self = this;
            this.model.save({state : '-2'}, {
                success: function (model, response) {
                    self.onClickClose();
                },
                error: function (model, response) {
                    alert(response.responseText);
                }
            });
        },
    });
            
    return view;
});