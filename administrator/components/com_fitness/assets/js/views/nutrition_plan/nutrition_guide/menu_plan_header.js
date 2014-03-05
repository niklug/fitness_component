define([
	'jquery',
	'underscore',
	'backbone',
        'app',
	'text!templates/nutrition_plan/nutrition_guide/menu_plan_header.html'
], function ( $, _, Backbone, app, template ) {

    var view = Backbone.View.extend({
        
        template:_.template(template),

        initialize: function(){
            this.controller = app.routers.nutrition_plan;
        },

        render: function(){
            var data = this.model.toJSON();
            data.$ = $;
            var template = _.template(this.template(data));
            this.$el.html(template);
            return this;
        },

        events: {
            "click #delete_menu_plan" : "onClickDelete",
            "click #save_menu_plan" : "onClickSave",
            "click #close_menu_plan" : "onClickClose",
            "click #submit_menu_plan" : "onClickSubmit",
        },
        
        onClickSave : function(event) {
            event.preventDefault();
            var data = Backbone.Syphon.serialize(this);
            data.created_by = app.options.client_id;
            
            if(typeof app.options.is_backend !== 'undefined' && app.options.is_backend == true) {
                data.status = 1;
            }
            
            this.model.set(data);
            
            this.model.unset('assessed_by_name');
            this.model.unset('created_by_name');
            //console.log(this.model.toJSON());
            //validation
            var menu_name_field = this.$el.find('#menu_name');
            menu_name_field.removeClass("red_style_border");
            var start_date_field = this.$el.find('#start_date');
            start_date_field.removeClass("red_style_border");
            if (!this.model.isValid()) {
                var validate_error = this.model.validationError;

                if(validate_error == 'menu_name') {
                    menu_name_field.addClass("red_style_border");
                    return false;
                } else if(validate_error == 'start_date') {
                    start_date_field.addClass("red_style_border");
                    return false;
                } else {
                    alert(this.model.validationError);
                    return false;
                }
            }
            //
            var self = this;
            if (this.model.isNew()) {
                this.collection.create(this.model, {
                    wait: true,
                    success: function (model, response) {
                        var id = model.get('id');
                        app.routers.nutrition_plan.navigate("!/menu_plan/" + id, true);
                        console.log(app.options.is_trainer);
                        if(app.options.is_trainer) {
                            self.send_status_email(model.get('id'), 'menu_plan_pending');
                        }
                        
                        if(app.options.is_client) {
                            self.send_status_email(model.get('id'), 'menu_plan_inprogress');
                        }
                        
                    },
                    error: function (model, response) {
                        alert(response.responseText);
                    }
                })
            } else {
                this.model.save(null, {
                    success: function (model, response) {
                        var id = model.get('id');
                        app.routers.nutrition_plan.navigate("!/menu_plan/" + id, true);
                    },
                    error: function (model, response) {
                        alert(response.responseText);
                    }
                });
            }
            
            
        },
        
        send_status_email : function(id, method) {
            var data = {};
            var url = app.options.ajax_call_url;
            var view = '';
            var task = 'ajax_email';
            var table = '';

            data.id = id;
            data.view = 'MenuPlan';
            data.method = method;

            $.AjaxCall(data, url, view, task, table, function(output){
                //console.log(output);
                var emails = output.split(',');
                var message = 'Emails were sent to: ' +  "</br>";
                $.each(emails, function(index, email) { 
                    message += email +  "</br>";
                });
                $("#emais_sended").append(message);
           });
        },
        
        onClickDelete : function(event) {
            this.model.destroy( {
                success: function (model, response) {
                    app.routers.nutrition_plan.navigate("!/nutrition_guide", true);
                },
                error: function (model, response) {
                    alert(response.responseText);
                }
            });
        },
        
        onClickClose : function() {
            app.routers.nutrition_plan.navigate("!/nutrition_guide", true);
        },
        
        onClickSubmit : function() {
            this.model.set({status : '5'});
            var submit_date = moment(new Date()).format("YYYY-MM-DD HH:mm:ss");
            this.model.set({submit_date : submit_date});
            this.model.unset('assessed_by_name');
            this.model.unset('created_by_name');
            var self = this;
            this.model.save(null, {
                success: function (model, response) {
                    app.routers.nutrition_plan.navigate("!/nutrition_guide", true);
                    if(app.options.is_client) {
                        self.send_status_email(model.get('id'), 'menu_plan_submitted');
                    }
                },
                error: function (model, response) {
                    alert(response.responseText);
                }
            });
        }
    });
            
    return view;
});