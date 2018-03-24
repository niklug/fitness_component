define([
	'jquery',
	'underscore',
	'backbone',
        'app',
	'text!templates/status/dialog.html',
], function (
        $,
        _,
        Backbone,
        app,
        template 
    ) {

    var view = Backbone.View.extend({
        template:_.template(template),
        
        className : 'dialog_status_wrapper',
        
        render : function(){
            var data = this.model.toJSON();
            
            data.options = this.options.settings;
            
            var template = _.template(this.template(data));
        
            this.$el.html(template);
            
            return this;
        },
        
        events: {
            "click .dialog_button_element" : "onClickButton",
            "click .close_status_dialog" : "close",
        },
        
        close : function() {
            this.$el.remove();
        },
        
        onClickButton : function(event) {
            this.close();
            var status = $(event.target).attr('data-status');
            this.model.set({status : status, assessed_by : app.options.user_id});
            var self = this;
            this.model.save(null, {
                success: function (model, response) {
                   self.onSaveStatus();
                },
                error: function (model, response) {
                    alert(response.responseText);
                }
            });
        },
        
        onSaveStatus : function() {
            this.emailLogic();
        },
        
        emailLogic : function() {
            var item_id = this.model.get('id');
            var status = this.model.get('status');
            var method = this.options.settings.statuses[status].email_alias;
            
            if(method) {
                this.sendEmail(item_id, method);
            }
        },
        
        sendEmail : function(id, method) {
            var data = {};
            var url = app.options.ajax_call_url;
            var view = '';
            var task = 'ajax_email';
            var table = '';

            data.id = id;
            data.view = this.options.settings.view;
            data.method = method;

            $.AjaxCall(data, url, view, task, table, function(output){ });
        }


    });
            
    return view;
});