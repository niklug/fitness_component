define([
	'jquery',
	'underscore',
	'backbone',
        'app',
	'text!templates/client_summary/backend/notifications/list_item.html'
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
            "click .delete_notification" : "onClickHide",
            "click .read_notification" : "onClickRead",
            "click .notifiction_open_list" : "onClickOpenList",
            "click .notifiction_open_form" : "onClickOpenForm",
        },
        
        onClickHide : function() {
            var user_id = app.options.user_id;
            
            var hidden = this.model.get('hidden');
   
            if(parseInt(hidden)) {
                hidden = hidden.split(",");
            } else {
                hidden = [];
            }
            
            var index = hidden.indexOf(user_id);
            
            if(index == '-1') {
                hidden.push(user_id);
            } else {
                hidden.splice(index, 1)
            }

            hidden = hidden.join(",");
            
            var self = this;
            this.model.save({hidden : hidden},{
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
            
            console.log(this.model.toJSON());
            
            this.backend_list_url = model.get('backend_list_url');
            this.backend_form_url = model.get('backend_form_url');
            this.frontend_list_url = model.get('frontend_list_url');
            this.frontend_form_url = model.get('frontend_form_url');
            
            if(this.backend_list_url) {
                this.backend_list_url = this.backend_list_url
                    .replace("{url_id_1}", this.model.get('url_id_1'))
                    .replace("{url_id_2}", this.model.get('url_id_2'));
            }
            
            if(this.backend_form_url) {
                this.backend_form_url = this.backend_form_url
                    .replace("{url_id_1}", this.model.get('url_id_1'))
                    .replace("{url_id_2}", this.model.get('url_id_2'));
            }
            
            if(this.frontend_list_url) {
                this.frontend_list_url = this.frontend_list_url
                    .replace("{url_id_1}", this.model.get('url_id_1'))
                    .replace("{url_id_2}", this.model.get('url_id_2'));
            }
            
            if(this.frontend_form_url) {
                this.frontend_form_url = this.frontend_form_url
                    .replace("{url_id_1}", this.model.get('url_id_1'))
                    .replace("{url_id_2}", this.model.get('url_id_2'));
            }

            template = template
                    .replace("{created_by}", this.model.get('created_by_name'))
                    .replace("{object}", this.model.get('object'))
                    .replace("{date}", moment(new Date(Date.parse(this.model.get('date')))).format("ddd, D MMM YYYY"))
                    .replace("{url_id_1}", this.model.get('url_id_1'))
                    .replace("{url_id_2}", this.model.get('url_id_2'));
            
            if(this.model.get('user_id') == this.model.get('created_by')) {
                template = template.replace("{user_id}", "");
            }
            
            if(app.options.is_trainer || app.options.is_superuser) {
                template = template.replace("{user_id}", 'for ' + this.model.get('user_name'))
            }
            
            if(app.options.is_client) {
                template = template.replace("{user_id}", "");
            }
              
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
        
        onClickOpenList : function() {
            var url = this.frontend_list_url;
            if(app.options.is_backend) {
                url = this.backend_list_url;
            }
            window.open(url, '_blank');
        },
        
        onClickOpenForm : function() {
            var url = this.frontend_form_url;
            if(app.options.is_backend) {
                url = this.backend_form_url;
            }
            window.open(url, '_blank');
        },
        
        close : function() {
            $(this.el).remove();
        }
    });
            
    return view;
});