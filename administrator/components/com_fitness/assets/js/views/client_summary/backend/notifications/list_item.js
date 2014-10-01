define([
	'jquery',
	'underscore',
	'backbone',
        'app',
	'text!templates/client_summary/backend/notifications/list_item.html'
], function ( $, _, Backbone, app, template ) {

    var view = Backbone.View.extend({
        
        tagName : "tr",
        
        template : _.template(template),
        
        render : function(){
            console.log(this.model.toJSON());
            var data = {item : this.model.toJSON()};
            data.app = app;
            data.$ = $;
            var template = _.template(this.template(data));
            
            this.$el.html(template);
            
            this.onRender();
            
            return this;
        },
        
        onRender : function() {
            var self = this;
            $(this.el).show('0', function() {

            });
        },
        
        events: {
            "click .delete_notification" : "onClickDelete",
            "click .read_notification" : "onClickRead",
        },
        
        onClickDelete : function() {
            var self = this;
            this.model.destroy({
                success: function (model) {
                    self.close();
                },
                error: function (model, response) {
                    alert(response.responseText);
                }
            });
        },
        
        onClickRead : function() {
            var self = this;
            this.model.save({readed : '1'},{
                success: function (model) {
                    self.render();
                },
                error: function (model, response) {
                    alert(response.responseText);
                }
            });
        },
        
        close : function() {
            $(this.el).remove();
        }
    });
            
    return view;
});