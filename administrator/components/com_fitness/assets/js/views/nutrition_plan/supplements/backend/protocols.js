define([
	'jquery',
	'underscore',
	'backbone',
        'app',
        'views/nutrition_plan/supplements/backend/protocol',
        'text!templates/nutrition_plan/supplements/backend/protocols_wrapper.html'
], function ( $, _, Backbone, app, Protocol_view, template ) {

     var view = Backbone.View.extend({
         
        template:_.template(template),
        
        initialize : function() {
            this.collection.bind("add", this.addItem, this);
        },
         
        render: function(){
            var template = _.template(this.template({'id' : this.options.nutrition_plan_id}));
            this.$el.html(template);

            this.onRender(); 

            return this;
        },

        events:{
            "click #add_protocol": "onAddProtocol"
        },
        
        onRender : function() {
            var self = this;
            $(this.el).show('0', function() {
                self.populateItems();
            });
        },
        
        populateItems : function() {
            var self = this;
            _.each(this.collection.models, function(model) {
                self.addItem(model);
            });
        },
        
        addItem :function(model) {
            $(this.el).find("#protocol_list").append(new Protocol_view({collection : this.collection,  model : model, 'nutrition_plan_id' : this.options.nutrition_plan_id}).render().el );
        },

        onAddProtocol:function () {
            app.controller.navigate("");
            app.controller.navigate("!/add_supplement_protocol/" + this.options.nutrition_plan_id, true);
        }

    });
            
    return view;
});