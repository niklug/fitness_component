define([
	'jquery',
	'underscore',
	'backbone',
        'app',
        'views/programs/select_element',
	'text!templates/programs/backend/event_client_item.html'
], function ( $, _, Backbone, app, Select_element_view, template ) {

    var view = Backbone.View.extend({
        
        template : _.template(template),
        
        render : function(){
            var data = this.model.toJSON();
            console.log(data);
            data.app = app;
            data.$ = $;
            var template = _.template(this.template(data));
            this.$el.append(template);
            
            this.loadClientSelect();
            
            this.connectStatus();
            
            return this;
        },
        
        loadClientSelect : function() {
            new Select_element_view({
                model : this.model,
                el : this.$el.find(".event_client_select"),
                collection : this.collection,
                first_option_title : '-Select-',
                class_name : '',
                id_name : '',
                model_field : 'id',
                element_disabled : 'disabled'
            }).render();
        },
        
        connectStatus : function() {
            var id = this.model.get('item_id');

            var status = this.model.get('status');

            var status_obj = $.status(app.options.status_options_ec);

            this.$el.find("#status_button_place_" + id).html(status_obj.statusButtonHtml(id, status));

            status_obj.run();
        },
        

    });
            
    return view;
});