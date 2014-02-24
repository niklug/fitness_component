define([
	'jquery',
	'underscore',
	'backbone',
        'app',
	'text!templates/exercise_library/backend/business_client_item.html'
], function ( $, _, Backbone, app, template ) {

    var view = Backbone.View.extend({
        
        template:_.template(template),
        
        render: function(){
            var data = this.model.toJSON();
            data.$ = $;
            data.app = app;
            var template = _.template(this.template(data));
            this.$el.html(template);
            
            this.setMyExerciseClients();

            return this;
        },
        
        setMyExerciseClients : function() {
            var client_id = this.model.get('user_id');
            var my_exercise_clients = this.options.item_model.get('my_exercise_clients');
            
            if((typeof my_exercise_clients === 'undefined') || my_exercise_clients == null) {
                return;
            }
            
            var my_exercise_clients = my_exercise_clients.split(",");

            if(_.include(my_exercise_clients, client_id)) {
                this.$el.find(".bisiness_client").attr('checked', true);
            }
        }
    });
            
    return view;
});