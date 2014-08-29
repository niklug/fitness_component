define([
	'jquery',
	'underscore',
	'backbone',
        'app',
        'views/programs/select_element',
	'text!templates/diary/backend/batch_process.html'
], function ( $, _, Backbone, app, Select_element_view, template ) {

    var view = Backbone.View.extend({
        
        template : _.template(template),
        
        render : function(){
            var data = {
                title : this.options.title,
                email_title : this.options.email_title
            };
            data.app = app;
            data.$ = $;
            var template = _.template(this.template(data));
            this.$el.append(template);
            
            this.connectStatusFilter();
 
            return this;
        },
        
        events: {
            "click #batch_process" : "onClickProcess",
        },

        connectStatusFilter : function() {
            var collection = new Backbone.Collection();
            _.each(this.options.statuses, function(status) {
                var model = new Backbone.Model(status);
                collection.add(model);
            });
          
             new Select_element_view({
                model : '',
                el : $(this.el).find("#batch_status_wrapper"),
                collection : collection,
                first_option_title : '-Status-',
                class_name : '',
                id_name : 'batch_status_select',
                model_field : ''
            }).render();
        },
        
        onClickProcess : function() {
            var status_field = $(this.el).find("#batch_status_select");
            status_field.removeClass("red_style_border");
            var status = status_field.find("option:selected").val();

            if(!parseInt(status)) {
                status_field.addClass("red_style_border");
                return;
            }
            
            var ids = $(this.options.checkbox_element + ':checked').map(function() { return this.getAttribute("data-id"); }).get();
            
            var self = this;
            _.each(ids, function(id) {
                var model = self.collection.get(id);
                model.set({status : status});
                self.updateStatus(model);
            });
        },
        
        updateStatus : function(model) {
            $(this.options.checkbox_element).prop("checked", false);
            $(this.options.checkbox_element_multiple).prop("checked", false);
            model.save(null, {
                success: function (model, response) {
                    
                },
                error: function (model, response) {
                    alert(response.responseText);
                }
            });
        }
    });
            
    return view;
});