define([
	'jquery',
	'underscore',
	'backbone',
        'app',
        'views/nutrition_plan/supplements/backend/protocol'
], function ( $, _, Backbone, app, Protocol_view ) {

     var view = Backbone.View.extend({

        render: function(){
            
            var self = this;

            this.protocolListItemViews = {};

            this.collection.on("add", function(protocol) {
                app.views.protocol = new Protocol_view({collection : this,  model : protocol}); 
                self.$el.find(".protocols_wrapper").append( app.views.protocol.render().el );
                self.protocolListItemViews[ protocol.cid ] = app.views.protocol;
            });

            this.collection.on("remove", function(protocol, options) {
                self.protocolListItemViews[ protocol.cid ].close();
                delete self.protocolListItemViews[ protocol.cid ];
            });
            
            

            return this;
        },

        events: {
            "click #add_protocol" : "onClickAddProtocol",
        },

    });
            
    return view;
});