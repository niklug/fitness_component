define([
	'jquery',
	'underscore',
	'backbone',
        'app',
	'text!templates/programs/backend/list_item.html'
], function ( $, _, Backbone, app, template ) {

    var view = Backbone.View.extend({
        
        template : _.template(template),
        
        render : function(){
            var data = this.model.toJSON();
            data.app = app;
            data.$ = $;
            var template = _.template(this.template(data));
            this.$el.append(template);
            this.connectClientStatuses();
            
            return this;
        },
        
        connectStatus : function(id, status) {
            var status_obj = $.status(app.options.status_options);
              
            var html =  status_obj.statusButtonHtml(id, status);

            this.$el.find("#status_button_place_" + id).html(html);

            status_obj.run();
        },
        
        connectClientStatuses : function() {
            var group_clients_data = this.model.get('group_clients_data');
            var self = this;
            _.each(group_clients_data, function(item){ 
                self.connectStatus(item.id, item.status);
            })
        }

    });
            
    return view;
});