define([
	'jquery',
	'underscore',
	'backbone',
        'app',
	'text!templates/diary/backend/menus/submenu_item.html'
], function ( $, _, Backbone, app, template ) {

    var view = Backbone.View.extend({
        
        initialize : function() {
        },
        
        template:_.template(template),
        
        render: function(){
            var template = _.template(this.template(this.model.toJSON()));
            this.$el.html(template);
            return this;
        },

        events: {
            "click #close" : "onClickClose",
            "click #trash" : "onClickTrash",
        },

        onClickClose : function() {
            app.controller.navigate("!/list_view", true);
            
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