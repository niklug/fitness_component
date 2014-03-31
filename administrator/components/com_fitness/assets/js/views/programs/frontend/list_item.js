define([
	'jquery',
	'underscore',
	'backbone',
        'app',
	'text!templates/programs/frontend/list_item.html'
], function ( $, _, Backbone, app, template ) {

    var view = Backbone.View.extend({
        
        template : _.template(template),
        
        render : function(){
            var data = this.model.toJSON();
            data.app = app;
            data.current_page = app.models.request_params.get('current_page');
            data.$ = $;
            var template = _.template(this.template(data));
            this.$el.append(template);
            this.connectStatus();
            return this;
        },
  
        connectStatus : function() {
            var id = this.model.get('id');
            var status = this.model.get('status');

            var options = _.extend({}, app.options.status_options);
            if(id) {
                var status_change_allowed = this.model.get('status_change_allowed');
                console.log(status_change_allowed);
                
                if(status_change_allowed == false) {
                    options.status_button = 'status_button_not_active';
                }

                var status_obj = $.status(options);

                this.$el.find("#status_button_place_" + id).html(status_obj.statusButtonHtml(id, status));

                status_obj.run();
            }
        },
    });
            
    return view;
});