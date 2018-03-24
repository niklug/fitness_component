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
            var data = this.model.toJSON();
            data.app = app;
            var template = _.template(this.template(data));
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
        
        onClickSubmit : function() {
            var data = {};

            data.submit_date = moment(new Date()).format("YYYY-MM-DD HH:mm:ss"); 
            data.status =  app.options.statuses.SUBMITTED_DIARY_STATUS.id;

            this.model.set(data);

            if (!this.model.isValid()) {
                alert(this.model.validationError);
                return false;
            }
            
            var self = this;
            this.model.save(null, {
                success: function (model, response) {
                    self.sendSubmitEmail(model.get('id'));
                    self.onClickClose();
                },
                error: function (model, response) {
                    alert(response.responseText);
                }
            });
        },
        
        sendSubmitEmail : function(id){
            var data = {};
            var url = app.options.ajax_call_url;
            var view = '';
            var task = 'ajax_email';
            var table = '';

            data.id = id;
            data.view = 'NutritionDiary';
            data.method = 'DiarySubmitted';

            var self = this;
            $.AjaxCall(data, url, view, task, table, function(output) {
                //console.log(output);
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