define([
	'jquery',
	'underscore',
	'backbone',
        'app',
	'text!templates/client_summary/frontend/notifications/list_item.html'
], function (
        $,
        _,
        Backbone,
        app,
        template
    ) {

    var view = Backbone.View.extend({
        
        tagName : "tr",
        
        template : _.template(template),
        
        initialize : function() {
            
        },
        
        render : function(){
            //console.log(this.model.toJSON());

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
                self.parseNotificationTemplate();
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
            var user_id = app.options.user_id;
            
            var readed = this.model.get('readed');
   
            if(parseInt(readed)) {
                readed = readed.split(",");
            } else {
                readed = [];
            }
            
            var index = readed.indexOf(user_id);
            
            if(index == '-1') {
                readed.push(user_id);
            } else {
                readed.splice(index, 1)
            }

            readed = readed.join(",");
            
            console.log(readed);
            
            var self = this;
            this.model.save({readed : readed},{
                success: function (model) {
                    self.render();
                },
                error: function (model, response) {
                    alert(response.responseText);
                }
            });
        },
        
        parseNotificationTemplate : function() {
            var template_id = this.model.get('template_id');
            var model = this.collection.get(template_id);
            
            var template = model.get('template');
            
            template = template
                    .replace("{created_by}", this.model.get('created_by_name'))
                    .replace("{user_id}", this.model.get('user_name'))
                    .replace("{object}", this.model.get('object'))
                    .replace("{date}", moment(new Date(Date.parse(this.model.get('date')))).format("ddd, D MMM YYYY"));
            
            
            var readed = this.model.get('readed');
            
            if(parseInt(readed)) {
                readed = readed.split(",");
            }

            readed = readed.indexOf(app.options.user_id);
            
            if(readed == '-1') {
                template = '<b>' + template + '</b>';
            }
            
            $(this.el).find(".template_container").html(template);
        },
        
        close : function() {
            $(this.el).remove();
        }
    });
            
    return view;
});