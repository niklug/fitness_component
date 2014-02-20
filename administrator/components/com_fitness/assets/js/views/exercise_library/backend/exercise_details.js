define([
	'jquery',
	'underscore',
	'backbone',
        'app',
	'text!templates/exercise_library/backend/exercise_details.html'
], function ( $, _, Backbone, app, template ) {

    var view = Backbone.View.extend({
        
        initialize : function() {
            this.edit_allowed = this.model.get('edit_allowed');
        },
        
        template:_.template(template),
        
        render: function(){
            var data = this.model.toJSON();
            data.$ = $;
            var template = _.template(this.template(data));
            this.$el.html(template);
            this.connectStatus();
            return this;
        },
        
        connectStatus : function() {
            var id = this.model.get('id');
            var status = this.model.get('status');
            var options = _.extend({}, app.options.status_options);
            
            if(id) {
                var edit_allowed = this.model.get('edit_allowed');
                
                if(edit_allowed == false) {
                    options.status_button = 'status_button_not_active';
                }

                var status_obj = $.status(options);

                this.$el.find("#status_button_place_" + id).html(status_obj.statusButtonHtml(id, status));

                status_obj.run();
            }
        }

    });
            
    return view;
});