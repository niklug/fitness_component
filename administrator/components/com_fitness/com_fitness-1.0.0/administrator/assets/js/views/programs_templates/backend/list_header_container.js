define([
	'jquery',
	'underscore',
	'backbone',
        'app',
        'views/programs/select_filter_block',
        'views/programs_templates/backend/search_block',
	'text!templates/programs/backend/list_header_container.html'
], function (
        $,
        _,
        Backbone,
        app, 
        Select_filter_block_view,
        Search_block_view,
        template 
    ) {

    var view = Backbone.View.extend({
        
        template:_.template(template),
        
        render: function(){
            var template = _.template(this.template());
            this.$el.html(template);
            
            this.connectFiltersBlock();
            
            this.connectSearchBlock();
            
            return this;
        },

        connectFiltersBlock : function() {
            
            new Select_filter_block_view({el : this.$el.find("#select_filter_wrapper"), model : this.model, block_width : '180px', not_show : ['locations']});
        },
        
        connectSearchBlock : function() {
            new Search_block_view({el : this.$el.find("#search_wrapper"), model : this.model, collection : this.collection});
        }
    });
            
    return view;
});