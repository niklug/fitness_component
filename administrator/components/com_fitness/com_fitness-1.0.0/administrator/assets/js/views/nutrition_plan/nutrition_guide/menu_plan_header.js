define([
	'jquery',
	'underscore',
	'backbone',
        'app',
        'models/nutrition_plan/target',
        'views/status/index',
	'text!templates/nutrition_plan/nutrition_guide/menu_plan_header.html'
], function ( 
        $,
        _, 
        Backbone,
        app,
        Target_model,
        Status_view,
        template
    ) {

    var view = Backbone.View.extend({
        
        template:_.template(template),

        render: function(){
            this.is_submitted();
            var data = this.model.toJSON();
            data.$ = $;
            data.app = app;
            var template = _.template(this.template(data));
            this.$el.html(template);
            
            this.connectStatus(this.model);
            
            return this;
        },

        events: {
            "click #delete_menu_plan" : "onClickDelete",
            "click #save_menu_plan" : "onClickSave",
            "click #close_menu_plan" : "onClickClose",
            "click #submit_menu_plan" : "onClickSubmit",
        },
        
        is_submitted : function() {
            var is_submitted = false;
            var status = this.model.get('status');
            if(parseInt(this.model.get('id')) && ((status == 2) || (status == 3) || (status == 5))) {
                is_submitted = true;
            }
            this.model.set({is_submitted : is_submitted});
        },
        
        
        onClickSave : function(event) {
            event.preventDefault();
            var data = Backbone.Syphon.serialize(this);
            data.created_by = app.options.client_id;
            data.nutrition_plan_id = this.options.nutrition_plan_id;
            
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
                app.models.target = new Target_model({nutrition_plan_id : this.options.nutrition_plan_id});
                var self = this;
                app.models.target.fetch({
                    wait : true,
                    data : {nutrition_plan_id : this.options.nutrition_plan_id},
                    success : function (model, response) {
                        //console.log(model.toJSON());
                        self.createItem(model);
                    },
                    error : function (collection, response) {
                        alert(response.responseText);
                    }
                });

            } else {
                this.model.save(null, {
                    success: function (model, response) {
                        var id = model.get('id');
                        app.controller.navigate("!/menu_plan/" + id + "/" + self.options.nutrition_plan_id, true);
                    },
                    error: function (model, response) {
                        alert(response.responseText);
                    }
                });
            }
        },
        
        createItem : function(model) {
            this.model.set({
                protein : model.get('protein'),
                fats : model.get('fats'),
                carbs : model.get('carbs'),
                calories : model.get('calories'),
                created_by : app.options.user_id,
                created_by_name : app.options.user_name,
            });
            
            var self = this;
            this.collection.create(this.model, {
                wait: true,
                success: function (model, response) {
                    //console.log(model.toJSON());
                    var id = model.get('id');
                    app.controller.navigate("!/menu_plan/" + id + "/" + self.options.nutrition_plan_id, true);
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
            var self = this;
            this.model.destroy( {
                success: function (model, response) {
                    app.controller.navigate("!/nutrition_guide/" +  self.options.nutrition_plan_id, true);
                },
                error: function (model, response) {
                    alert(response.responseText);
                }
            });
        },
        
        onClickClose : function() {
            app.controller.navigate("!/nutrition_guide/" + this.options.nutrition_plan_id, true);
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
                    app.controller.navigate("!/nutrition_guide/" + self.options.nutrition_plan_id, true);
                },
                error: function (model, response) {
                    alert(response.responseText);
                }
            });
        },
        
        connectStatus : function(model) {
            app.options.menu_status_options.button_not_active = true;
            $(this.el).find(".status_container").html(new Status_view({
                model : model,
                settings : app.options.menu_status_options
            }).render().el);
        },
    });
            
    return view;
});