define([
	'jquery',
	'underscore',
	'backbone',
        'app',
	'text!templates/nutrition_plan/backend/goals_periods_block.html'
], function ( $, _, Backbone, app, template ) {

    var view = Backbone.View.extend({
        
        template:_.template(template),
            
        render: function(){
            var data = {item : this.model.toJSON()};
            var template = _.template(this.template(data));
            this.$el.html(template);
            
            this.$el.find("#active_start, #active_finish").datepicker({ dateFormat: "yy-mm-dd"});
            
            this.setOverrideDates(!!parseInt(this.model.get('override_dates')));
            
            if(this.model.get('active_finish') == '9999-12-31') {
                $(this.el).find("#no_end_date").attr('checked', true);
            }
              
            return this;
        },
        
        events : {
            "change #override_dates" : "onChangeOverrideDates",
            "change #no_end_date" : "onChangeNoEndDate"
        },
        
        onChangeOverrideDates : function(event) {
            this.setOverrideDates($(event.target).is(":checked"));
        },
        
        setOverrideDates : function(state) {
            $(this.el).find("#override_dated_area").toggle(state);
            $(this.el).find("#override_dates").attr('checked', state);
        },
        
        onChangeNoEndDate : function(event) {
            this.setNoEndDate($(event.target).is(":checked"));
        },
        
        setNoEndDate : function(state) {
            if(state) {
                $(this.el).find("#active_finish").val('9999-12-31');
            } else {
                $(this.el).find("#active_finish").val(this.model.get('deadline_mini'));
            }
        },
        
    });
            
    return view;
});