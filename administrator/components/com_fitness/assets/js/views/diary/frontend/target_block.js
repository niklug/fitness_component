define([
	'jquery',
	'underscore',
	'backbone',
	'text!templates/diary/frontend/target_block.html'
], function ( $, _, Backbone, template ) {

    var view = Backbone.View.extend({
            initialize: function(){
                _.bindAll(this, 'setTargetData', 'render');
            },
            
            template:_.template(template),
            
            render: function(){
                var data = {item : this.model.toJSON()};
                //console.log(this.model.toJSON());
                var template = _.template(this.template(data));
                this.$el.html(template);
                
                this.onRender();

                return this;
            },
            
            onRender : function() {
            var self = this;
                $(this.el).show('0', function() {
                    self.setTargetData();
                });
            },
            
            setTargetData : function() {
                //console.log(this.model.toJSON());
                var data = [
                    {label: "Protein:", data: [[1, this.model.get('target_protein_percent')]]},
                    {label: "Carbs:", data: [[1, this.model.get('target_carbs_percent')]]},
                    {label: "Fat:", data: [[1, this.model.get('target_fats_percent')]]}
                ];

                var container = this.$el.find(".placeholder_pie");
                
                var targets_pie = $.drawPie(data, container, {'no_percent_label' : false});

                targets_pie.draw(); 
                
            },
        });
            
    return view;
});