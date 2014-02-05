define([
	'jquery',
	'underscore',
	'backbone',
        'app',
	'text!templates/exercise_library/select_filter.html',
], function ( $, _, Backbone, app, template ) {

    var view = Backbone.View.extend({
        
        initialize : function() {
            this.collection.bind("add", this.addItem, this);
        },
        
        template:_.template(template),
        
        render : function(){
            $(this.el).html(this.template());
            this.$el.find(".filter_select option[value=0]").attr('selected', true);
            
            var self = this;
            _.each(this.collection.models, function (model) { 
                self.addItem(model);
            }, this);
            
            return this;
        },

        addItem : function(model) {
            this.$el.find(".filter_select").append('<option value="' + model.get('id') + '">' + model.get('name') + '</option>');
        },

        events: {
            "change .filter_select" : "onFilterSelect",
        },


        onFilterSelect : function(event){
            var ids = $(event.target).find(':selected').map(function(){ return this.value }).get().join(",");
            this.models.set({'filter_select_options' : ids});
        }
    });
            
    return view;
});