define([
	'jquery',
	'underscore',
	'backbone',
        'app',
        'views/programs/select_filter_block',
        'views/programs/backend/search_block',
        'views/graph/graph',
	'text!templates/programs/backend/list_header_container.html',
        
        'jquery.flot',
        'jquery.flot.time'
], function (
        $,
        _,
        Backbone,
        app, 
        Select_filter_block_view,
        Search_block_view,
        Graph_view,
        template 
    ) {

    var view = Backbone.View.extend({
        
        template:_.template(template),
        
        render: function(){
            var template = _.template(this.template());
            this.$el.html(template);
            
            this.onRender();
            
            return this;
        },
        
        onRender : function() {
            var self = this;
            $(this.el).show('0', function() {
                self.connectFiltersBlock();
            
                self.connectSearchBlock();

                self.connectGraph();
            });
        },
        
        connectGraph : function() {
            new Graph_view({
                el : "#graph_container",
                model : this.model,
                show : {
                    primary_goals : true,
                    mini_goals : true,
                    personal_training : true,
                    semi_private : true,
                    resistance_workout : true,
                    cardio_workout : true,
                    assessment : false,
                    current_time : true,
                    
                    client_select : true,
                    choices : true
                },
                style : ''
            });
        },


        connectFiltersBlock : function() {
            
            new Select_filter_block_view({el : this.$el.find("#select_filter_wrapper"), model : this.model, block_width : '180px'});
        },
        
        connectSearchBlock : function() {
            new Search_block_view({el : this.$el.find("#search_wrapper"), model : this.model, collection : this.collection});
        }
    });
            
    return view;
});