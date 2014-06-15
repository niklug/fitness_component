define([
	'jquery',
	'underscore',
	'backbone',
        'app',
        'views/goals/backend/periodization/session_list',
	'text!templates/goals/backend/periodization/list_item.html'
], function (
        $,
        _,
        Backbone,
        app,
        Session_list_view,
        template
    ) {

    var view = Backbone.View.extend({
        
        template : _.template(template),
        
        render : function(){
            var data = {item : this.model.toJSON()};
            data.app = app;
            data.$ = $;
            var template = _.template(this.template(data));
            this.$el.html(template);
            this.onRender();
            return this;
        },
        
        events: {
            "click .save_period" : "onClickSave",
            "click .delete_period" : "onClickDelete",
            "click .copy_period" : "onClickCopy",
        },
        
        onRender : function() {
            var self = this;
            $(this.el).show('0', function() {
                self.loadSessionList();
            });
        },
        
        loadSessionList : function() {
            $(this.el).find(".session_wrapper").html(new Session_list_view({model : this.model}).render().el);
        },
        
        onClickDelete : function(event) {
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
        
        onClickSave : function() {
            var period_focus_field = $(this.el).find('.period_focus');
            var comments_field = $(this.el).find('.comments');

            period_focus_field.removeClass("red_style_border");
            
            var period_focus = period_focus_field.val();
            var comments= comments_field.val();

            
            this.model.set({
                    period_focus : period_focus, 
                    comments : comments, 
            });

            if (!this.model.isValid()) {
                var validate_error = this.model.validationError;

                if(validate_error == 'period_focus') {
                    period_focus_field.addClass("red_style_border");
                    return false;
                } else {
                    alert(this.model.validationError);
                    return false;
                }

            }
            
            var self = this;
            
            if (this.model.isNew()) {
                this.collection.create(this.model, {
                    wait: true,
                    success: function (model, response) {
                        self.render();
                    },
                    error: function (model, response) {
                        alert(response.responseText);
                    }
                })
            } else {
                this.model.save(null, {
                    success: function (model, response) {
                        self.render();
                    },
                    error: function (model, response) {
                        alert(response.responseText);
                    }
                });
            }
        },
        
        onClickCopy : function() {
            var id = this.model.get('id');
            var advance_period = $(this.el).find(".advance_period").val();
            var data = {}
            data.id = id;
            data.advance_period = advance_period;
            var url = app.options.ajax_call_url;
            var view = 'goals';
            var task = 'copySessionPeriod';
            var table = '';
            var self = this;
            $.AjaxCall(data, url, view, task, table, function(output){
                self.collection.reset();
            });
         },
        
        close : function() {
            $(this.el).unbind();
            $(this.el).remove();
        },
        
    });
            
    return view;
});