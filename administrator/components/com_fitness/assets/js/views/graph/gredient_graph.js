define([
    'jquery',
    'underscore',
    'backbone',
    'app',
    'text!templates/graph/gredient_graph.html',
], function(
        $,
        _,
        Backbone,
        app,
        template
        ) {

    var view = Backbone.View.extend({
        
        initialize: function() {
            
        },
        
        template: _.template(template),
        
        render: function() {
            var data = {item : this.options.data};
            $(this.el).html(this.template(data));

            return this;
        },
   
    });

    return view;
});