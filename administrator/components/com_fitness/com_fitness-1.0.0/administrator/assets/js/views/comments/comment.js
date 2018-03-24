define([
	'jquery',
	'underscore',
	'backbone',
        'app',
        'models/notifications/notification',
	'text!templates/comments/comment.html',
        'jquery.cleditor'
        
], function (
        $,
        _,
        Backbone,
        app,
        Notification_model,
        template 
    ) {

    var view = Backbone.View.extend({
        
        initialize : function() {
            this.edit_mode = this.options.edit_mode || false;
        },
        
        template : _.template(template),

        render : function () {
            var data = {
                item : this.model.toJSON(),
                options : this.options,
                edit_mode : this.edit_mode
            };
            data.$ = $;
            $(this.el).html(this.template(data));
            
            this.onRender();

            return this;
        },
        
        onRender : function() {
            var self = this;
            $(this.el).show('0', function() {
                if(self.options.show_editor && self.edit_mode) {
                    self.connectEditor($(self.el), ".comment_textarea", false);
                }
                
                if(!self.model.isNew()) {
                    self.editAllowLoggic();
                }
            });
        },
        
        events: {
            "click .save_parent_comment": "onClickSaveComment",
            "click .edit_parent_comment": "onClickEditComment",
            "click .delete_child_comment": "onClickDeleteComment",
        },
        
        editAllowLoggic : function() {
            var created_by_client = this.model.get('created_by_client');

            var user_id = app.options.user_id;
            var created_by = this.model.get('created_by');


            if(app.options.is_superuser) {
                this.showEditButton();
            }

            if(app.options.is_trainer_administrator) {
                if((user_id == created_by) || created_by_client) {
                    this.showEditButton();
                }
            }
            
            if(app.options.is_simple_trainer) {
                if(user_id == created_by) {
                    this.showEditButton();
                }
            }

            if(app.options.is_client) {
                if(user_id == created_by) {
                    this.showEditButton();
                }
            }  
        },
        
        showEditButton : function() {
            $(this.el).find(".delete_child_comment, .edit_parent_comment").show();
        },
        
        onClickEditComment : function() {
            this.edit_mode = true;
            this.render();
        },
        
        connectEditor : function(element, selector, disabled) {
            element.find(selector).cleditor({width:'98%', height:100, useCSS:true})[0];
            
            var font_color = '#fff';
            
            if(app.options.is_backend) {
                font_color = '#000';
            }

            element.find("iframe").contents().find("body").css('color', font_color);

            element.find(".cleditorMain").css('background-color', 'rgba(255, 255, 255, 0.1)');
            
            var element = element.find(selector).cleditor()[0];
            if(element) {
                element.disable(disabled);
            }
        },
        
        onClickSaveComment : function() {
            this.edit_mode = false;
            
            var comment = $(this.el).find(".comment_textarea").val();
             
            if(typeof comment !== 'undefined') {
                comment = encodeURIComponent(comment);
            } else {
                comment = '';
            }
            
            var saved_comment = this.model.get('comment');
            var is_new = true;
            
            if(saved_comment) {
                is_new = false;
            }
            //console.log(is_new);
            
            this.model.set({comment : comment});
            
            if (!this.model.isValid()) {
                var validate_error = this.model.validationError;
                alert(this.model.validationError);
                return;
            }
            var self = this;
            
            this.model.save(null, {
                success: function(model, response) {
                    self.render();
                    if(is_new && self.options.comment_options.anable_comment_email !== 'undefined' && self.options.comment_options.anable_comment_email == true) {
                        self.commentEmail(model);
                    }
                    
                    self.connectNotification();
                },
                error: function(model, response) {
                    alert(response.responseText);
                }
            });
        },
        
        onClickDeleteComment : function() {
            var self = this;
            this.model.destroy({
                success: function(model, response) {
                    self.close();
                },
                error: function(model, response) {
                    alert(response.responseText);
                }
            });
        },
        
        commentEmail : function(model) {
            var data = model.toJSON();
            var url = app.options.ajax_call_url;
            var view = '';
            var task = 'ajax_email';
            var table = '';

            data.view = 'Comment';
            data.method = this.options.comment_options.comment_method;
            data.table = this.options.comment_options.db_table;


            $.AjaxCall(data, url, view, task, table, function(output){
                console.log(output);
            });
        },
        
        connectNotification : function() {
            var options = {
                db_table : this.options.comment_options.db_table,
                date : this.options.comment_options.item_model.get('entry_date'),
                user_id : this.options.comment_options.item_model.get('client_id'),
                created : this.model.get('created')
            };
      
            var model = new Notification_model(options);
        },
        
        close : function() {
            $(this.el).remove();
        }
        
    });
            
    return view;

});