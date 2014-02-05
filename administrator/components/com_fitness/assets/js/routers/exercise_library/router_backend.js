define([
	'jquery',
	'underscore',
	'backbone',
        'app'
], function (
        $,
        _,
        Backbone,
        app
    ) {

    var Controller = Backbone.Router.extend({
        
        initialize: function(){
            // history
            this.routesHit = 0;
            Backbone.history.on('route', function() { this.routesHit++; }, this);
            //
            
            //unique id
            app.getUniqueId = function() {
                return new Date().getUTCMilliseconds();
            }
            //
        },

        routes: {
            "": "form_view", 
            "!/form_view": "form_view", 
        },
        
        back: function() {
            if(this.routesHit > 1) {
              window.history.back();
            } else {
              this.navigate('', {trigger:true, replace:true});
            }
        },
          
        form_view : function() {
            console.log('form..');
        }
    });

    return Controller;
});