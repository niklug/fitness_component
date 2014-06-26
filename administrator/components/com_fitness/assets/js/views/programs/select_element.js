define([
	'jquery',
	'underscore',
	'backbone',
        'app',
	'text!templates/programs/select_element.html',
], function ( $, _, Backbone, app, template ) {

    var view = Backbone.View.extend({
        
        initialize : function() {
            this.collection.bind("add", this.addItem, this);
            
            if(this.model) {
                var selected_items = this.model.get(this.options.model_field);
                if((typeof selected_items !== 'undefined') && selected_items) {
                    if (typeof selected_items != 'string') {
                        this.selected_items = selected_items;
                        return;
                    }
                    this.selected_items = selected_items.split(',');
                }
            }
            
        },
        
        template:_.template(template),
        
        render : function(){
            var data = {
                first_option_title : this.options.first_option_title,
                class_name : this.options.class_name,
                id_name : this.options.id_name,
                select_size : this.options.select_size,
                element_disabled : this.options.element_disabled || ''
            };
            $(this.el).html(this.template(data));
            
            
            if(typeof this.selected_items === 'undefined') {
                this.$el.find(".filter_select option[value=0]").attr('selected', true);
            }
            
            var self = this;
            _.each(this.collection.models, function (model) { 
                self.addItem(model);
            }, this);
            
            return this;
        },

        addItem : function(model) {
            var id = model.get('id');
            
            var name = model.get('name');
            
            if(typeof this.options.value_field !== 'undefined') {
                id = model.get(this.options.value_field);
            }
            
            if(typeof this.options.text_field !== 'undefined') {
                name = model.get(this.options.text_field);
            }
            
            var selected = '';

            if(typeof this.selected_items !== 'undefined') {
                if(_.contains(this.selected_items, id)) {
                    selected = 'selected ="selected"';
                }
            }
    
            this.$el.find("select").append('<option ' + selected + ' value="' + id + '">' + name + '</option>');
        },

        events: {
            "change .select_element" : "onChange",
        },


        onChange : function(event){
            var ids = $(event.target).find(':selected').map(function(){ return this.value }).get().join(",");
            var model_field = this.options.model_field;
            var option = {};
            option[model_field] = ids;
            
            if(this.model) {
                this.model.set(option);
            }
        }
    });
            
    return view;
});