define([
    'jquery',
    'underscore',
    'backbone',
    'app',
    'text!templates/graph/progress_graph.html',
], function(
        $,
        _,
        Backbone,
        app,
        template
        ) {

    var view = Backbone.View.extend({
        
        initialize: function() {
            this.render();
        },
        
        template: _.template(template),
        
        render: function() {
            
            var data = {};
            data.head_title = this.options.head_title || false;
            $(this.el).html(this.template(data));
            
            this.onRender();

            return this;
        },
        
        
        onRender : function() {
            var self = this;
            $(this.el).show('0', function() {
                self.setData();
                self.drawGraph(self.collection.models);
            });
        },
        
        setData : function() {
            var self = this;
            var axises_array = []; 
            _.each(this.collection.models, function(model) {
                var x_axis = model.get(self.options.data_field_x);
                var y_axis = model.get(self.options.data_field_y);
                
                var unix_time = new Date(Date.parse(x_axis)).getTime();
                axises_array.push([unix_time, y_axis]);
            });
            //console.log(axises_array);
            return axises_array;
        },
        

        drawGraph: function(items_data) {
            var self = this;
            //TIME SETTINGS
            var current_time = new Date().getTime();
            var start_year_previous = new Date(new Date().getFullYear() - 1, 0, 1).getTime();
            var start_year = new Date(new Date().getFullYear(), 0, 1).getTime();
            var start_year_next = new Date(new Date().getFullYear() + 1, 0, 1).getTime();

            var end_year_previous = new Date(new Date().getFullYear() - 1, 12, 0).getTime();
            var end_year = new Date(new Date().getFullYear(), 12, 0).getTime();
            var end_year_next = new Date(new Date().getFullYear() + 1, 12, 0).getTime();

            var date = new Date();
            var firstDay = new Date(date.getFullYear(), date.getMonth(), 1).getTime() - 60*59*24 * 1000;
            var lastDay = new Date(date.getFullYear(), date.getMonth() + 1, 0).getTime() + 60*59*24 * 1000;


            //var start_week = 1375056000000;
            //var end_week = 1375574400000;

            var start_week = this.startAndEndOfWeek(new Date())[0];
            var end_week = this.startAndEndOfWeek(new Date())[1];

            var start_day = (new Date(date.getFullYear(), date.getMonth(),date.getDate())).getTime();
            var end_day = (new Date(date.getFullYear(), date.getMonth(), date.getDate())).getTime() + 60*60*24 * 1000;
            //alert(date.getDate());

            // END TIME SETTINGS

            // DATA
            //var d1 = [[1377993600 * 1000, 2], [1378913700 * 1000, 1]];
            var d1 = this.setData();
            
            // base common options
            var options = {
                xaxis: {mode: "time", timezone: "browser"},
                yaxis: {show: true, labelWidth: 30},
                series: {
                    lines: {show: false },
                    points: {show: true, radius: 5, symbol: "circle", fill: true, fillColor: "#FFFFFF" },
                    bars: {show: true, lineWidth: 3},
                },
                grid: {
                            hoverable: true,
                            clickable: true,
                            backgroundColor: {
                                 colors: ["#FFFFFF", "#F0F0F0"]
                            },
                },
                legend: {show: false, margin: [0, 0], backgroundColor : "transparent"},

                colors : [this.options.color]
            };
            
            
            
            if(this.options.style == 'dark') {
                options.grid.backgroundColor.colors =  ["#0E0704", "#0E0704"];
                options.grid.color =  "#C0C0C0";
                options.series.points.fillColor =  "#0E0704";
            }

            // set show data
            var data = [{label: "", data: d1}];


            // year options
            var options_year_previous = {xaxis: {tickSize: [1, "month"], min: start_year_previous, max: end_year_previous}};
            $.extend(true, options_year_previous, options);
            var options_year = {xaxis: {tickSize: [1, "month"], min: start_year, max: end_year}};
            $.extend(true, options_year, options);
            var options_year_next = {xaxis: {tickSize: [1, "month"], min: start_year_next, max: end_year_next}};
            $.extend(true, options_year_next, options);
            // month options
            var options_month = {xaxis: {tickSize: [1, "day"], min: firstDay, max: lastDay, timeformat: "%d"}};
            $.extend(true, options_month, options);

            var current_options = {
                get: function() {
                    return this.options;
                },
                set: function(options) {
                    this.options = options
                }
            };
            // on load
            var graph_period = 'options_month';
            switch (graph_period) {
                case 'options' :
                    current_options = options;
                    $("#whole").addClass('choosen_link');
                    $("#by_year_previous, #by_year, #by_year_next, #by_month").removeClass('choosen_link');
                    break;
                case 'options_year_previous' :
                    current_options = options_year_previous;
                    $("#by_year_previous").addClass('choosen_link');
                    $("#whole, #by_year, #by_year_next, #by_month").removeClass('choosen_link');
                    break;
                case 'options_year' :
                    current_options = options_year;
                    $("#by_year").addClass('choosen_link');
                    $("#whole, #by_year_previous, #by_year_next, #by_month").removeClass('choosen_link');
                    break;
                case 'options_year_next' :
                    current_options = options_year_next;
                    $("#by_year_next").addClass('choosen_link');
                    $("#whole, #by_year_previous, #by_year, #by_month").removeClass('choosen_link');
                    break;
                case 'options_month' :
                    current_options = options_month;
                    $("#by_month").addClass('choosen_link');
                    $("#whole, #by_year_previous, #by_year, #by_year_next").removeClass('choosen_link');
                    break;
                default :
                    current_options = options_year;
                    $("#by_year").addClass('choosen_link');
                    $("#whole, #by_year_previous, #by_year_next, #by_month").removeClass('choosen_link');
                    break;
            }


            var self = this;
            // whole 
            $("#whole").die().click(function() {
                $(this).addClass('choosen_link');
                $("#by_year_previous, #by_year, #by_year_next, #by_month").removeClass('choosen_link');
                current_options = options;
                self.plotAccordingToChoices(data, current_options);
            });
            // by year
            $("#by_year_previous").die().click(function() {
                $(this).addClass('choosen_link');
                $("#whole, #by_year, #by_year_next, #by_month").removeClass('choosen_link');
                current_options = options_year_previous;
                self.plotAccordingToChoices(data, current_options);
            });
            $("#by_year").die().click(function() {
                $(this).addClass('choosen_link');
                $("#whole, #by_year_previous, #by_year_next, #by_month").removeClass('choosen_link');
                current_options = options_year;
                self.plotAccordingToChoices(data, current_options);
            });
            $("#by_year_next").die().click(function() {
                $(this).addClass('choosen_link');
                $("#whole, #by_year_previous, #by_year, #by_month").removeClass('choosen_link');
                current_options = options_year_next;
                self.plotAccordingToChoices(data, current_options);
            });
            // by month
            $("#by_month").die().click(function() {
                $(this).addClass('choosen_link');
                $("#whole, #by_year_previous, #by_year, #by_year_next").removeClass('choosen_link');
                current_options = options_month;
                self.plotAccordingToChoices(data, current_options);
            });


            self.plotAccordingToChoices(data, current_options);
            
            
            //tooltip
            if(typeof this.options.tooltip !== "undefined" && this.options.tooltip == true) {
                $("<div id='tooltip'></div>").css({
                        position: "absolute",
                        display: "none",
                        border: "2px solid #cccccc",
                        "border-radius": "10px",
                        padding: "5px",
                        "background-color": "#287725",
                        opacity: 0.9,
                        color : "#fff"
                }).appendTo("body");


                $("#placeholder").die().bind("plothover", function (event, pos, item) {
                    if (item) {
                        var data_type = item.datapoint[1];
                        var model = items_data[item.dataIndex]
                        //console.log(data);
                        var html = '';
                        var html =  self.options.setTooltipHtml(html, model);
                        $("#tooltip").css("background-color", "#287725");
                        $("#tooltip").html(html)
                            .css({top: item.pageY+5, left: item.pageX+5})
                            .fadeIn(200);
                    } else {
                            $("#tooltip").hide();
                    }
                });
            }
        },

        plotAccordingToChoices : function(data, options) {
            if (data.length > 0) {
                    $.plot("#placeholder", data, options);
                    var yaxisLabel = $("<div class='axisLabel yaxisLabel'></div>").text(this.options.y_title).appendTo($('#placeholder'));
            }
        },


        startAndEndOfWeek : function(date) {
          // If no date object supplied, use current date
          // Copy date so don't modify supplied date
          var now = date? new Date(date) : new Date();

          // set time to some convenient value
          now.setHours(0,0,0,0);

          // Get the previous Monday
          var monday = new Date(now);
          monday.setDate(monday.getDate() - monday.getDay() + 1);

          // Get next Sunday
          var sunday = new Date(now);
          sunday.setDate(sunday.getDate() - sunday.getDay() + 7);

          // Return array of date objects
          return [monday, sunday];
        }

    });

    return view;
});