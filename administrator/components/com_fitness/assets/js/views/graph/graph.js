define([
    'jquery',
    'underscore',
    'backbone',
    'app',
    'collections/graph/training_periods',
    'text!templates/graph/graph.html',
], function(
        $,
        _,
        Backbone,
        app,
        Periods_collection,
        template
        ) {

    var view = Backbone.View.extend({
        
        initialize: function() {
            if( 
                app.collections.training_periods 
            ) {
                this.render();
                return;
            } 
      
            app.collections.training_periods = new Periods_collection();
                       
            var self = this;
            $.when (
                app.collections.training_periods.fetch({
                    success : function (collection, response) {
                        //console.log(collection);
                    },
                    error : function (collection, response) {
                        alert(response.responseText);
                    }
                })
                
                

            ).then (function(response) {
                self.render();
            })
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
                self.getGraphData();
                self.populateTrainingPerions();
            });
        },
        
        getGraphData: function() {
            if(app.models.graph_data) {
                this.setGraphData( app.models.graph_data.get('data'));
                return;
            } 

            var data = {};
            var url = app.options.fitness_frontend_url;
            var view = 'goals_periods';
            var task = 'populateGoals';
            var table = '';
            var list_type = '';
            data.list_type = list_type;
            var self = this;
            $.AjaxCall(data, url, view, task, table, function(output) {
                //console.log(output);
                app.models.graph_data = new Backbone.Model();
                app.models.graph_data.set({data : output});
                
                self.setGraphData(output);
            });
        },
        
        setGraphData: function(goals) {
            console.log(goals);
            var data = {};
            var primary_goals_data = this.setPrimaryGoalsGraphData(goals.primary_goals);
            $.extend(true, data, primary_goals_data);
            var mini_goals_data = this.setMiniGoalsGraphData(goals.mini_goals);
            $.extend(true, data, mini_goals_data);
            this.drawGraph(data);
        },
        
        setPrimaryGoalsGraphData: function(primary_goals) {
            var data = {};
            data.primary_goals = this.x_axisDateArray(primary_goals, 2, 'deadline');
            data.client_primary = this.graphItemDataArray(primary_goals, 'client_name');
            data.goal_primary = this.graphItemDataArray(primary_goals, 'primary_goal_name');
            data.start_primary = this.graphItemDataArray(primary_goals, 'start_date');
            data.finish_primary = this.graphItemDataArray(primary_goals, 'deadline');
            data.status_primary = this.graphItemDataArray(primary_goals, 'status');
            return data;
        },
        
        setMiniGoalsGraphData: function(mini_goals) {
            var data = {};
            data.mini_goals_start_date = this.x_axisDateArray(mini_goals, 1, 'start_date');
            data.mini_goals = this.x_axisDateArray(mini_goals, 1, 'deadline');
            data.client_mini = this.graphItemDataArray(mini_goals, 'client_name');
            data.goal_mini = this.graphItemDataArray(mini_goals, 'mini_goal_name');
            data.start_mini = this.graphItemDataArray(mini_goals, 'start_date');
            data.finish_mini = this.graphItemDataArray(mini_goals, 'deadline');
            data.status_mini = this.graphItemDataArray(mini_goals, 'status');
            data.training_period_colors = this.graphItemDataArray(mini_goals, 'training_period_color');

            return data;
        },
        
        x_axisDateArray: function(data, y_value, field) {
            var x_axis_array = [];
            for (var i = 0; i < data.length; i++) {
                var unix_time = new Date(Date.parse(data[i][field])).getTime();
                x_axis_array[i] = [unix_time, y_value];
            }
            return x_axis_array;
        },
        
        graphItemDataArray: function(data, type) {
            var items = [];
            for (var i = 0; i < data.length; i++) {
                items[i] = data[i][type];
            }
            return items;
        },
        
        drawGraph: function(client_data) {
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
            var firstDay = new Date(date.getFullYear(), date.getMonth(), 1).getTime() - 60 * 59 * 24 * 1000;
            var lastDay = new Date(date.getFullYear(), date.getMonth() + 1, 0).getTime() + 60 * 59 * 24 * 1000;
            // END TIME SETTINGS

            // DATA
            // Primary Goals
            var d1 = client_data.primary_goals;

            //console.log(d1);

            // Mini Goals
            var d2 = client_data.mini_goals;
            // Current Time
            var d8 = [[current_time, 3]];
            // Training Periods colors
            var training_period_colors = client_data.training_period_colors;

            // Training periods 
            var markings = [];
            for (var i = 0; i < d2.length; i++) {
                markings[i] = {xaxis: {from: client_data.mini_goals_start_date[i][0], to: d2[i][0]}, yaxis: {from: 0.25, to: 0.75}, color: training_period_colors[i]};
            }


            var data = [
                {label: "Primary Goal", data: d1},
                {label: "Mini Goal", data: d2},
                {label: "Current Time", data: d8}
            ];
            // END DATA

            // START OPTIONS
            // base common options
            var options = {
                xaxis: {mode: "time", timezone: "browser"},
                yaxis: {show: false},
                series: {
                    lines: {show: false},
                    points: {show: true, radius: 5, symbol: "circle", fill: true, fillColor: "#0E0704"},
                    bars: {show: true, lineWidth: 3},
                },
                grid: {
                    hoverable: true,
                    clickable: true,
                    backgroundColor: {
                        colors: ["#0E0704", "#0E0704"]
                    },
                    markings: markings,
                    markingsColor: "#0E0704",
                    color: "#C0C0C0"
                },
                legend: {show: true, margin: [0, 0], backgroundColor: "none", labelBoxBorderColor: "none"},
                colors: [
                    "#A3270F", // Primary Goal
                    "#287725", // Mimi Goal
                    "#FFB01F"// Current Time
                ]
            };
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
            var graph_period = localStorage.getItem('graph_period');

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
                localStorage.setItem('graph_period', 'options');
                current_options = options;
                self.plotAccordingToChoices(data, current_options);
            });
            // by year
            $("#by_year_previous").die().click(function() {
                $(this).addClass('choosen_link');
                $("#whole, #by_year, #by_year_next, #by_month").removeClass('choosen_link');
                localStorage.setItem('graph_period', 'options_year_previous');
                current_options = options_year_previous;
                self.plotAccordingToChoices(data, current_options);
            });
            $("#by_year").die().click(function() {
                $(this).addClass('choosen_link');
                $("#whole, #by_year_previous, #by_year_next, #by_month").removeClass('choosen_link');
                localStorage.setItem('graph_period', 'options_year');
                current_options = options_year;
                self.plotAccordingToChoices(data, current_options);
            });
            $("#by_year_next").die().click(function() {
                $(this).addClass('choosen_link');
                $("#whole, #by_year_previous, #by_year, #by_month").removeClass('choosen_link');
                localStorage.setItem('graph_period', 'options_year_next');
                current_options = options_year_next;
                self.plotAccordingToChoices(data, current_options);
            });
            // by month
            $("#by_month").die().click(function() {
                $(this).addClass('choosen_link');
                $("#whole, #by_year_previous, #by_year, #by_year_next").removeClass('choosen_link');
                localStorage.setItem('graph_period', 'options_month');
                current_options = options_month;
                self.plotAccordingToChoices(data, current_options);
            });

            this.plotAccordingToChoices(data, current_options);

            $("<div id='tooltip'></div>").css({
                position: "absolute",
                display: "none",
                border: "2px solid #cccccc",
                "border-radius": "10px",
                padding: "5px",
                "background-color": "#fee",
                opacity: 0.9,
                color: "#fff",
                "text-align": "left",
            }).appendTo("body");

            $("#placeholder").bind("plothover", function(event, pos, item) {
                if (item) {
                    var data_type = item.datapoint[1];
                    var html = "<p style=\"text-align:center;\"><b>" + item.series.label + "</b></p>";

                    switch (data_type) {
                        case 1 : // Mini Goals
                            html += "Client: " + client_data.client_mini[item.dataIndex] + "</br>";
                            html += "Goal: " + (client_data.goal_mini[item.dataIndex] || '') + "</br>";
                            html += "Start: " + client_data.start_mini[item.dataIndex] + "</br>";
                            html += "Finish: " + client_data.finish_mini[item.dataIndex] + "</br>";
                            html += "Status: " + (self.getStatusById(client_data.status_mini[item.dataIndex]) || '') + "</br>";
                            $("#tooltip").css("background-color", "#287725");
                            break;
                        case 2 : // Primary Goals
                            html += "Client: " + client_data.client_primary[item.dataIndex] + "</br>";
                            html += "Goal: " + (client_data.goal_primary[item.dataIndex] || '') + "</br>";
                            html += "Start: " + client_data.start_primary[item.dataIndex] + "</br>";
                            html += "Finish: " + client_data.finish_primary[item.dataIndex] + "</br>";
                            html += "Status: " + (self.getStatusById(client_data.status_primary[item.dataIndex]) || '') + "</br>";
                            $("#tooltip").css("background-color", "#A3270F");
                            break;
                        case 3 : // Current Time
                            html = "Current Time";
                            $("#tooltip").css("background-color", "#FFB01F");
                            break;
                        default :
                            break;
                    }

                    $("#tooltip").html(html)
                            .css({top: item.pageY + 5, left: item.pageX + 5})
                            .fadeIn(200);
                } else {
                    $("#tooltip").hide();
                }

            });


        },
        
        plotAccordingToChoices: function(data, options) {
            if (data.length > 0) {
                $.plot("#placeholder", data, options);
            }
        },
        
        getStatusById: function(id) {
            var status_name;
            switch (id) {
                case '1' :
                    status_name = 'Pending';
                    break;
                case '2' :
                    status_name = 'Complete';
                    break;
                case '3' :
                    status_name = 'Incomplete';
                    break;
                case '4' :
                    status_name = 'Evaluating';
                    break;
                case '5' :
                    status_name = 'In Progress';
                    break;
                case '6' :
                    status_name = 'Assessing';
                    break;
                default :
                    status_name = 'Evaluating';
                    break;
            }
            return status_name;
        },
        
        populateTrainingPerions : function() {
            var el = $(this.el).find("#training_period_container");
            
            _.each(app.collections.training_periods.models, function(model) {
                var color =  '<div style="float:left;margin-right:5px;width:15px; height:15px;background-color:' + model.get('color') + '" ></div>';
                var name = '<div class="grey_title"> ' + model.get('name') + '</div>';
                var html = color + name;
                el.append(html);
            });
        }

    });

    return view;
});