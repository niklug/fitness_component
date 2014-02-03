define([
	'jquery',
	'underscore',
	'backbone',
        'app',
	'text!templates/diary/frontend/form.html',
        'jquery.validate',
        'jqueryui',
        'jquery.flot',
        'jquery.flot.time',
        'jquery.flot.pie',
        'jquery.drawPie'
], function (
        $,
        _,
        Backbone,
        app,
        template 
    ) {

    var view = Backbone.View.extend({
        
        initialize : function() {
            this.heavy_target = this.collection.findWhere({type : 'heavy'}).toJSON();
            this.light_target = this.collection.findWhere({type : 'light'}).toJSON();
            this.rest_target = this.collection.findWhere({type : 'rest'}).toJSON();
        },
        
        template : _.template(template),

        render : function () {
            $(this.el).html(this.template());
            this.loadPlugins();
            return this;
        },
        
        loadPlugins : function() {
            $("#create_item_form").validate();
            this.setCalendar();
        },
        
        setCalendar : function() {
            var self = this;
            $( "#entry_date" ).datepicker({ dateFormat: "yy-mm-dd",  minDate : -5, beforeShowDay: self.disableDays});
        },
        
        disableDays : function(date) {
            var disabledDays = app.models.diary_days.toJSON();
            var calendar_date = moment(date).format("YYYY-MM-DD");
            var result =  [true];
            if(_.contains(disabledDays, calendar_date)) {
                result =  [false];
            }
            return result
        },
            
        events: {
            "click .activity_level" : "onChooseTrainingDay"
        },
        
        onChooseTrainingDay : function(event) {
            this.activity_level = $(event.target).val();
            this.setTargetData(this.activity_level);
            $("#next").show();
        },
        
        setTargetData : function(activity_level) {
            var activity_data;
            if(activity_level == '1') activity_data = this.heavy_target;
            if(activity_level == '2') activity_data = this.light_target;
            if(activity_level == '3') activity_data = this.rest_target;

            var calories = activity_data.calories;
            var water = activity_data.water;

            $("#calories_value").html(calories);
            $("#water_value").html(water);

            $("#pie_td, .calories_td").css('visibility', 'visible');


            //console.log(activity_data);
            var data = [
                {label: "Protein:", data: [[1, activity_data.protein]]},
                {label: "Carbs:", data: [[1, activity_data.carbs]]},
                {label: "Fat:", data: [[1, activity_data.fats]]}
            ];

            var container = $("#placeholder_targets");

            var targets_pie = $.drawPie(data, container, {'no_percent_label' : false});

            targets_pie.draw(); 
        },
 
    });
            
    return view;
});