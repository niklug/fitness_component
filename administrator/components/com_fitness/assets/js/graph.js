(function($) {
    function getGraphData(client_id, url) {
        $.ajax({
            type : "POST",
            url : url,
            data : {
                view : 'goals',
                format : 'text',
                task : 'getGraphData',
                client_id : client_id
              },
            dataType : 'json',
            success : function(response) {
                if(response.status.success != true) {
                    alert(response.status.message);
                    return;
                }
                //console.log(response.data.mini_goals);
                var data = {};
                //console.log(response.data);
                // primary goals
                var primary_goals_data = setPrimaryGoalsGraphData(response.data.primary_goals);
                $.extend(true,data, primary_goals_data);

                // mini goals
                var mini_goals_data = setMiniGoalsGraphData(response.data.mini_goals);
                $.extend(true,data, mini_goals_data);

                // Personal training
                var personal_training_data = setAppointmentGraphData('personal_training', response.data.personal_training, 3);
                $.extend(true,data, personal_training_data);
                //console.log(personal_training_data);

                // Semi-Private Training
                var semi_private_data = setAppointmentGraphData('semi_private', response.data.semi_private, 4);
                $.extend(true,data, semi_private_data);

                // Resistance Workout
                var resistance_workout_data = setAppointmentGraphData('resistance_workout', response.data.resistance_workout, 5);
                $.extend(true,data, resistance_workout_data);

                // Cardio Workout
                var cardio_workout_data = setAppointmentGraphData('cardio_workout', response.data.cardio_workout, 6);
                $.extend(true,data, cardio_workout_data);

                // Assessment
                var assessment_data = setAppointmentGraphData('assessment', response.data.assessment, 7);
                $.extend(true,data, assessment_data);                       

                //console.log(personal_training_data);
                drawGraph(data);

            },
            error: function(XMLHttpRequest, textStatus, errorThrown)
            {
                alert("error");
            }
        });

    }



    function setPrimaryGoalsGraphData(primary_goals) {
        var data = {};
        data.primary_goals = x_axisDateArray(primary_goals, 2, 'deadline');
        data.client_primary = graphItemDataArray(primary_goals, 'client_name');
        data.goal_primary = graphItemDataArray(primary_goals, 'primary_goal_name');
        data.start_primary = graphItemDataArray(primary_goals, 'start_date');
        data.finish_primary = graphItemDataArray(primary_goals, 'deadline');
        data.status_primary = graphItemDataArray(primary_goals, 'status');
        data.training_period_colors = graphItemDataArray(primary_goals, 'training_period_color');
        return data;
    }

    function setMiniGoalsGraphData(mini_goals) {
        var data = {};
        data.mini_goals = x_axisDateArray(mini_goals, 1, 'deadline');
        data.client_mini = graphItemDataArray(mini_goals, 'client_name');
        data.goal_mini = graphItemDataArray(mini_goals, 'mini_goal_name');
        data.start_mini = graphItemDataArray(mini_goals, 'start_date');
        data.finish_mini = graphItemDataArray(mini_goals, 'deadline');
        data.status_mini = graphItemDataArray(mini_goals, 'status');
        return data;
    }


    function setAppointmentGraphData(type, appointment, y_axis) {
        var data = {};

        data[type + '_xaxis'] = x_axisDateArray(appointment, y_axis, 'starttime');
        data[type + '_session_type'] = graphItemDataArray(appointment, 'session_type');
        data[type + '_session_focus'] = graphItemDataArray(appointment, 'session_focus');
        data[type + '_date'] = graphItemDataArray(appointment, 'starttime');
        data[type + '_trainer'] = graphItemDataArray(appointment, 'trainer_name');
        data[type + '_location'] = graphItemDataArray(appointment, 'location');
        data[type + '_appointment_color'] = graphItemDataArray(appointment, 'color');

        //console.log(data);
        return data;      
    }


    function graphItemDataArray(data, type) {
        var items = []; 
        for(var i = 0; i < data.length; i++) {
            items[i] = data[i][type];
        }
        return items;
    }


    function x_axisDateArray(data, y_value, field) {
        var x_axis_array = []; 

        for(var i = 0; i < data.length; i++) {
            //console.log(data[i][field]);
            var unix_time = new Date(Date.parse(data[i][field])).getTime();

            //console.log(unix_time);

            //var date = new Date(unix_time);

            //console.log(date);
            x_axis_array[i] = [unix_time, y_value];
        }
        return x_axis_array;
    }


    /**
    * draw Flot Graph on select client

     * @param {type} data
     * @returns {undefined}     */
    function drawGraph(client_data) {

         //TIME SETTINGS
        var current_time = new Date().getTime();
        var start_year = new Date(new Date().getFullYear(), 0, 1).getTime();
        var end_year = new Date(new Date().getFullYear(), 12, 0).getTime();

        var date = new Date();
        var firstDay = new Date(date.getFullYear(), date.getMonth(), 1).getTime() - 60*59*24 * 1000;
        var lastDay = new Date(date.getFullYear(), date.getMonth() + 1, 0).getTime() + 60*59*24 * 1000;


        //var start_week = 1375056000000;
        //var end_week = 1375574400000;

        var start_week = startAndEndOfWeek(new Date())[0];
        var end_week = startAndEndOfWeek(new Date())[1];

        var start_day = (new Date(date.getFullYear(), date.getMonth(),date.getDate())).getTime();
        var end_day = (new Date(date.getFullYear(), date.getMonth(), date.getDate())).getTime() + 60*60*24 * 1000;
        //alert(date.getDate());

        // END TIME SETTINGS

        // DATA
        // Primary Goals
        //var d1 = [[1377993600 * 1000, 2]];
        var d1 = client_data.primary_goals;

        var training_period_colors = client_data.training_period_colors;


        // Training periods 
        var markings = []; 
        for(var i = 0; i < d1.length - 1; i++) {
            markings[i] =  { xaxis: { from: d1[i][0], to: d1[i + 1][0] }, yaxis: { from: 0.25, to: 0.75 }, color: training_period_colors[i+1]};
        }
        // first Primary Goal marking

        var first_primary_goal_start_date = new Date(client_data.start_primary[0]).getTime();
        if(first_primary_goal_start_date) {
            markings[markings.length] =  { xaxis: { from: first_primary_goal_start_date, to: d1[0][0] }, yaxis: { from: 0.25, to: 0.75 }, color: training_period_colors[0]};
        }
        //console.log(markings);
        //
        // Mini Goals
        //var d2 = [[1320376000 * 1000, 1], [1330376000 * 1000, 1], [1340376000 * 1000, 1], [1350998400 * 1000, 1], [1374710400 * 1000, 1]];
        var d2 = client_data.mini_goals;

        var d3 = client_data.personal_training_xaxis;

        var d4 = client_data.semi_private_xaxis; 

        var d5 = client_data.resistance_workout_xaxis;

        var d6 = client_data.cardio_workout_xaxis;

        var d7 = client_data.assessment_xaxis;

        // Current Time
        var d8 = [[current_time, 8]];

        var data = [
            {label: "Primary Goal", data: d1},
            {label: "Mini Goal", data: d2},
            {label: "Personal Training", data: d3},
            {label: "Semi-Private Training", data: d4},
            {label: "Resistance Workout", data: d5},
            {label: "Cardio Workout", data: d6},
            {label: "Assessment", data: d7},
            {label: "Current Time", data: d8}
        ];
        // END DATA

        // START OPTIONS
        // base common options
        var options = {
            xaxis: {mode: "time", timezone: "browser"},
            yaxis: {show: false},
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
                        markings: markings
            },
            legend: {show: true, margin: [-170, 0]},

            colors: [
                "#A3270F",// Primary Goal
                "#287725", // Mimi Goal
                client_data.personal_training_appointment_color[0] ||"#00BF32",
                client_data.semi_private_appointment_color[0] || "#007F01",
                client_data.resistance_workout_appointment_color[0] || "#0070FF",
                client_data.cardio_workout_appointment_color[0] || "#E94E1B",
                client_data.assessment_appointment_color[0] || "#E6007E",
                "#FFB01F"// Current Time
            ]


        };

        // year options
        var options_year = { xaxis: {tickSize: [1, "month"], min: start_year, max: end_year}};
        $.extend(true,options_year, options);
        // month options
        var options_month = { xaxis: {tickSize: [1, "day"], min:  firstDay, max: lastDay, timeformat: "%d"}};
        $.extend(true,options_month, options);
        // week options
        var options_week= { xaxis: {tickSize: [1, "day"], min:  start_week, max: end_week, timeformat: "%a"}};
        $.extend(true,options_week, options);      
        // day options
        var options_day = { xaxis: {minTickSize: [1, "hour"],min: start_day, max: end_day, twelveHourClock: true}};
        $.extend(true,options_day, options); 

        var current_options = {
            get : function() {return this.options;},
            set : function(options) {this.options = options}
        };
        current_options = options_year;
        // END OPTIONS

        // START RUN BY PERIOD
        // whole 
        $("#whole").click(function() {
            current_options = options;
            plotAccordingToChoices(data, current_options);
        });

         // by year
        $("#by_year").click(function() {
            current_options = options_year;
            plotAccordingToChoices(data, current_options);
        });


       // by month
        $("#by_month").click(function() {
            current_options = options_month;
            plotAccordingToChoices(data, current_options);
        });

        // by week
        $("#by_week").click(function() {
            current_options = options_week;
            plotAccordingToChoices(data, current_options);
        });

        // by day
        $("#by_day").click(function() {
            current_options = options_day
            plotAccordingToChoices(data, current_options);
        });

         // TOOGLE
        // insert checkboxes 
        $.each(data, function(key, val) {
            $("#choices").append("<br/><input type='checkbox' name='" + key +
                    "' checked='checked' id='id" + key + "'></input>" +
                    "<label for='id" + key + "'>"
                    + val.label + "</label>");
        });
        $("#choices").find("input").click(function() {
            plotAccordingToChoices(data, current_options);
        });
        plotAccordingToChoices(data, current_options);
        //END TOOGLE
        //
        // END START RUN BY PERIOD

        $("<div id='tooltip'></div>").css({
                position: "absolute",
                display: "none",
                border: "2px solid #cccccc",
                "border-radius": "10px",
                padding: "5px",
                "background-color": "#fee",
                opacity: 0.9
        }).appendTo("body");

        $("#placeholder").bind("plothover", function (event, pos, item) {
            if (item) {
                var data_type = item.datapoint[1];
                var html = "<p style=\"text-align:center;\"><b>" +  item.series.label + "</b></p>";

                switch(data_type) {
                    case 1 : // Mini Goals
                        html +=  "Client: " +  client_data.client_mini[item.dataIndex] + "</br>";
                        html +=  "Goal: " +  client_data.goal_mini[item.dataIndex] + "</br>";
                        html +=  "Start: " +  client_data.start_mini[item.dataIndex] + "</br>";
                        html +=  "Finish: " +  client_data.finish_mini[item.dataIndex] + "</br>";
                        html +=  "Status: " +  getStatusById(client_data.status_mini[item.dataIndex]) + "</br>"; 
                        $("#tooltip").css("background-color", "#287725");
                        break;
                    case 2 : // Primary Goals
                        html +=  "Client: " +  client_data.client_primary[item.dataIndex] + "</br>";
                        html +=  "Goal: " +  client_data.goal_primary[item.dataIndex] + "</br>";
                        html +=  "Start: " +  client_data.start_primary[item.dataIndex] + "</br>";
                        html +=  "Finish: " +  client_data.finish_primary[item.dataIndex] + "</br>";
                        html +=  "Status: " +  getStatusById(client_data.status_primary[item.dataIndex]) + "</br>"; 
                        $("#tooltip").css("background-color", "#A3270F");
                        break;
                    case 3 : // Personal Training
                        html =  setAppointmentsTooltip(html, client_data, item, 'personal_training');
                        break;
                    case 4 : // Semi-Private Training
                        html =  setAppointmentsTooltip(html, client_data, item, 'semi_private');
                        break;
                    case 5 : // Resistance Workout
                        html =  setAppointmentsTooltip(html, client_data, item, 'resistance_workout');
                        break;
                    case 6 : //  Cardio Workout
                        html =  setAppointmentsTooltip(html, client_data, item, 'cardio_workout');
                          break;
                    case 7 : // Assessment
                        html =  setAppointmentsTooltip(html, client_data, item, 'assessment');
                        break;
                    case 8 : // Current Time
                        html =  "Current Time" ;
                        $("#tooltip").css("background-color", "#FFB01F");
                        break;
                    default :
                        break;
                }

                $("#tooltip").html(html)
                    .css({top: item.pageY+5, left: item.pageX+5})
                    .fadeIn(200);
            } else {
                    $("#tooltip").hide();
            }

        });
    }

    function plotAccordingToChoices(data, options) {
        var data_temp = [];
        var colors = [];
        $("#choices").find("input:checked").each(function () {
                var key = $(this).attr("name");
                if (key && data[key]) {
                        data_temp.push(data[key]);
                        colors.push(options.colors[key]);

                }
        });

        var choosen_options = {};

        $.extend(true, choosen_options, options);

        choosen_options.colors = [];

        choosen_options.colors = colors;

        //console.log(choosen_options.colors);
        //console.log(options.colors);
        if (data_temp.length > 0) {
                $.plot("#placeholder", data_temp, choosen_options);
        }
    }



    function setAppointmentsTooltip(html, client_data, item, type) {

       $("#tooltip").css("background-color", client_data[type + '_appointment_color'][0]);

       html +=  "Session Type: " +  client_data[type + '_session_type'][item.dataIndex] + "</br>";
       html +=  "Session Focus: " +  client_data[type + '_session_focus'][item.dataIndex] + "</br>";
       html +=  "Date: " +  client_data[type + '_date'][item.dataIndex] + "</br></br>";
       html +=  "Trainer: " +  client_data[type + '_trainer'][item.dataIndex] + "</br>";
       html +=  "Location: " +  client_data[type + '_location'][item.dataIndex] + "</br>"; 

       return html;
    }

    function getStatusById(id) {
        var status_name;
        switch(id) {
            case '1' : 
               status_name = 'Pending';
               break;
            case '2' :
               status_name = 'Complete';
               break;
            case '3' :
               status_name = 'Incomplete';
            default :

               break;
        }
        return status_name;
    }

    function startAndEndOfWeek(date) {

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
    
    // Add the  function to the top level of the jQuery object
    $.getGraphData = function(client_id, url) {

        var constr = getGraphData(client_id, url);

        return constr;
    };
})(jQuery);