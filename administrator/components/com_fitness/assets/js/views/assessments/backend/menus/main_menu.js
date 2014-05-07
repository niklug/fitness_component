define([
	'jquery',
	'underscore',
	'backbone',
        'app',
	'text!templates/assessments/backend/menus/main_menu.html',
        'backbone.syphon'
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
            "click #save" : "onClickSave",
            "click #save_template" : "onClickSaveTemplate",
            "click #save_close" : "onClickSaveClose",
            "click #save_new" : "onClickSaveNew",
            "click #save_copy" : "onClickSaveCopy",
            "click #cancel" : "onClickCancel",
        },

        onClickSave : function() {
            this.save_method = 'save';
            this.saveItem();
        },

        onClickSaveClose : function() {
            this.save_method = 'save_close';
            this.saveItem();
        },

        onClickSaveNew : function() {
            this.save_method = 'save_new';
            this.saveItem();
        },
        
        onClickSaveCopy : function() {
            var id = this.model.get('id');
            var data = {};
            var url = app.options.ajax_call_url;
            var view = 'Programs';
            var task = 'copyEvent';
            var table = '';
            data.id = id;
            $.AjaxCall(data, url, view, task, table, function(output){
                console.log(output);
                app.controller.navigate("!/form_view/" + output, true);
            });
        },

        onClickCancel : function() {
            app.controller.navigate("!/list_view", true);
        },
        
        
        saveItem : function() {
            var self = this;
            
            $("#program_form" ).die().live('submit', function(event) {
                event.preventDefault();
                
                var data = Backbone.Syphon.serialize(this);
                
                var description = data.description;
             
                if(typeof description !== 'undefined') {
                    description = encodeURIComponent(description);
                } else {
                    description = '';
                }
                data.description = description;
                
                data.starttime  = data.start_date + ' ' + data.start_time ;
            
                data.endtime = data.finish_date + ' ' + data.finish_time ;

                if(!self.model.get('id')) {
                    data.endtime = data.start_date + ' ' + data.finish_time ;
                    data.owner = app.options.user_id;
                }
                
                data.video = $("#video_container").attr('data-videopath');


                self.model.set(data);


                console.log(self.model.toJSON());

                $(' #finish_date,  #finish_time').removeClass("red_style_border");

                //start validation
                if (!self.model.isValid()) {
                    var validate_error = self.model.validationError;

                    if(validate_error == 'end_date_time') {
                        $('#finish_date').addClass("red_style_border");
                        $('#finish_time').addClass("red_style_border");
                        return false;
                    } else {
                        alert(self.model.validationError);
                        return false;
                    }
                }


                //end validation

                self.model.save(null, {
                    success: function (model, response) {
                        var id = model.get('id');
                        if(self.save_method == 'save') {
                            app.controller.navigate("!/form_view/" + id, true);
                        } else if(self.save_method == 'save_close') {
                            app.controller.navigate("!/list_view", true);
                        } else if(self.save_method == 'save_new') {
                            app.controller.navigate("!/form_view/0", true);
                        } else {
                            app.controller.navigate("!/list_view", true);
                        }
                    },
                    error: function (model, response) {
                        alert(response.responseText);
                    }
                });

            });

            $("#program_form" ).submit();
        },
        
        onClickSaveTemplate : function() {
            var id = this.model.get('id');
            var data = {};
            var url = app.options.ajax_call_url;
            var view = 'Programs';
            var task = 'saveAsTemplate';
            var table = '';
            data.id = id;
            $.AjaxCall(data, url, view, task, table, function(output){
                //console.log(output);
            });
        }
        
        

    });
            
    return view;
});