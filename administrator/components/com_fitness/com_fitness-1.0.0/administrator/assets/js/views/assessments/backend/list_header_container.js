define([
	'jquery',
	'underscore',
	'backbone',
        'app',
        'views/assessments/select_filter_block',
        'views/assessments/backend/search_block',
        'views/graph/graph',
	'text!templates/assessments/backend/list_header_container.html',
        
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
                    personal_training : false,
                    semi_private : false,
                    resistance_workout : false,
                    cardio_workout : false,
                    assessment : true,
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