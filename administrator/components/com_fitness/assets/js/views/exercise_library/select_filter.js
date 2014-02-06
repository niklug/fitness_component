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
            var data = {
                title : this.options.title,
                first_option_title : this.options.first_option_title,
                class_name : this.options.class_name,
                id_name : this.options.id_name,
                select_size : this.options.select_size
            };
            $(this.el).html(this.template(data));
            
            if(typeof this.options.selected_items !== 'undefined') {
                if(this.options.selected_items.length == 0) {
                    this.$el.find(".filter_select option[value=0]").attr('selected', true);
                }
            }
            
            var self = this;
            _.each(this.collection.models, function (model) { 
                self.addItem(model);
            }, this);
            
            return this;
        },

        addItem : function(model) {
            var id = model.get('id');
            var selected = '';
            if(typeof this.options.selected_items !== 'undefined') {
                if(_.contains(this.options.selected_items, id)) {
                    selected = 'selected ="selected"';
                }
            }
    
            this.$el.find(".filter_select").append('<option ' + selected + ' value="' + model.get('id') + '">' + model.get('name') + '</option>');
        },

        events: {
            "change .filter_select" : "onFilterSelect",
        },


        onFilterSelect : function(event){
            var ids = $(event.target).find(':selected').map(function(){ return this.value }).get().join(",");
            var model_field = this.options.model_field;
            var option = {};
            option[model_field] = ids;
            this.model.set(option);
        }
    });
            
    return view;
});