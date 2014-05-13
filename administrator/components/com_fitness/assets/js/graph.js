(function($) {
    function getGraphData(data, client_id, url) {
        var data_encoded = JSON.stringify(data);
        $.ajax({
            type : "POST",
            url : url,
            data : {
                view : 'goals',
                format : 'text',
                task : 'getGraphData',
                client_id : client_id,
                data_encoded : data_encoded
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
        
        return data;
    }

    function setMiniGoalsGraphData(mini_goals) {
        var data = {};
        data.mini_goals_start_date = x_axisDateArray(mini_goals, 1, 'start_date');
        data.mini_goals = x_axisDateArray(mini_goals, 1, 'deadline');
        data.client_mini = graphItemDataArray(mini_goals, 'client_name');
        data.goal_mini = graphItemDataArray(mini_goals, 'mini_goal_name');
        data.start_mini = graphItemDataArray(mini_goals, 'start_date');
        data.finish_mini = graphItemDataArray(mini_goals, 'deadline');
        data.status_mini = graphItemDataArray(mini_goals, 'status');
        data.training_period_colors = graphItemDataArray(mini_goals, 'training_period_color');
        data.training_period_name = graphItemDataArray(mini_goals, 'training_period_name');
        return data;
    }


    function setAppointmentGraphData(type, appointment, y_axis) {
        var data = {};

        data[type + '_xaxis'] = x_axisDateArray(appointment, y_axis, 'starttime');
        data[type + '_session_type'] = graphItemDataArray(appointment, 'session_type_name');
        data[type + '_session_focus'] = graphItemDataArray(appointment, 'session_focus_name');
        data[type + '_date'] = graphItemDataArray(appointment, 'starttime');
        data[type + '_trainer'] = graphItemDataArray(appointment, 'trainer_name');
        data[type + '_location'] = graphItemDataArray(appointment, 'location_name');
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

        //console.log(markings);
        //
        // Mini Goals
        //var d2 = [[1320376000 * 1000, 1], [1330376000 * 1000, 1], [1340376000 * 1000, 1], [1350998400 * 1000, 1], [1374710400 * 1000, 1]];
        var d2 = client_data.mini_goals;
        
        var training_period_colors = client_data.training_period_colors;

       // Training periods 
        var markings = []; 
        for(var i = 0; i < d2.length; i++) {
            markings[i] =  { xaxis: { from: client_data.mini_goals_start_date[i][0], to: d2[i][0] }, yaxis: { from: 0.25, to: 0.75 }, color: training_period_colors[i]};
        }


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
                        markings: markings,
                        markingsColor: "#F2F2F2"
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
        var options_year_previous = { xaxis: {tickSize: [1, "month"], min: start_year_previous, max: end_year_previous}};
        $.extend(true,options_year_previous, options);
        var options_year = { xaxis: {tickSize: [1, "month"], min: start_year, max: end_year}};
        $.extend(true,options_year, options);
        var options_year_next = { xaxis: {tickSize: [1, "month"], min: start_year_next, max: end_year_next}};
        $.extend(true,options_year_next, options);
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
        
        var graph_period = getLocalStorageItem('graph_period');
        // END OPTIONS

        // START RUN BY PERIOD
        
        switch(graph_period) {
            case 'options' :
                current_options = options;
                $("#whole").addClass('choosen_link');
                $("#all_goals, #current_primary_goal, #by_year_previous, #by_year, #by_year_next, #by_month, #by_week, #by_day").removeClass('choosen_link');
               break;
            case 'options_year_previous' :
                current_options = options_year_previous;
                $("#by_year_previous").addClass('choosen_link');
                $("#all_goals, #current_primary_goal, #whole, #by_year, #by_year_next, #by_month, #by_week, #by_day").removeClass('choosen_link');
               break;
            case 'options_year' :
                current_options = options_year;
                $("#by_year").addClass('choosen_link');
                $("#all_goals, #current_primary_goal, #whole, #by_year_previous, #by_year_next, #by_month, #by_week, #by_day").removeClass('choosen_link');
               break;
            case 'options_year_next' :
                current_options = options_year_next;
                $("#by_year_next").addClass('choosen_link');
                $("#all_goals, #current_primary_goal, #whole, #by_year_previous, #by_year, #by_month, #by_week, #by_day").removeClass('choosen_link');
               break;
            case 'options_month' :
                current_options = options_month;
                $("#by_month").addClass('choosen_link');
                $("#all_goals, #current_primary_goal, #whole, #by_year_previous, #by_year, #by_year_next, #by_week, #by_day").removeClass('choosen_link');
               break;
            case 'options_week' :
                current_options = options_week;
                $("#by_week").addClass('choosen_link');
                $("#all_goals, #current_primary_goal, #whole, #by_year_previous, #by_year, #by_month, #by_day").removeClass('choosen_link');
               break;
            case 'options_day' :
                current_options = options_day;
                $("#by_day").addClass('choosen_link');
                $("#all_goals, #current_primary_goal, #whole, #by_year_previous, #by_year, #by_year_next, #by_week").removeClass('choosen_link');
               break;
            case 'current_primary_goal' :
                current_options = options_year;
                $("#current_primary_goal").addClass('choosen_link');
                $("#all_goals, #whole, #by_year_previous, #by_year, #by_year_next, #by_month, #by_week, #by_day").removeClass('choosen_link');
                break;
            case 'all_goals' :
                current_options = options;
                $("#all_goals").addClass('choosen_link');
                $("#current_primary_goal, #whole, #by_year_previous, #by_year, #by_year_next, #by_month, #by_week, #by_day").removeClass('choosen_link');
            break;
            default :
                current_options = options_year;
                $("#by_year").addClass('choosen_link');
                $("#whole, #by_year_previous, #by_year_next, #by_month, #by_week, #by_day").removeClass('choosen_link');
                break;
        }


        var self = this;
        // whole 
        $("#whole").click(function() {
            $(this).addClass('choosen_link');
            $("#all_goals, #current_primary_goal, #by_year_previous, #by_year, #by_year_next, #by_month, #by_week, #by_day").removeClass('choosen_link');
            setLocalStorageItem('graph_period', 'options');
            current_options = options;
            plotAccordingToChoices(data, current_options);
        });
        // by year
        $("#by_year_previous").click(function() {
            $(this).addClass('choosen_link');
            $("#all_goals, #current_primary_goal,  #whole, #by_year, #by_year_next, #by_month, #by_week, #by_day").removeClass('choosen_link');
            setLocalStorageItem('graph_period', 'options_year_previous');
            current_options = options_year_previous;
            plotAccordingToChoices(data, current_options);
        });
        $("#by_year").click(function() {
            $(this).addClass('choosen_link');
            $("#all_goals,  #current_primary_goal, #whole, #by_year_previous, #by_year_next, #by_month, #by_week, #by_day").removeClass('choosen_link');
            setLocalStorageItem('graph_period', 'options_year');
            current_options = options_year;
            plotAccordingToChoices(data, current_options);
        });
        $("#by_year_next").click(function() {
            $(this).addClass('choosen_link');
            $("#all_goals, #current_primary_goal,  #whole, #by_year_previous, #by_year, #by_month, #by_week, #by_day").removeClass('choosen_link');
            setLocalStorageItem('graph_period', 'options_year_next');
            current_options = options_year_next;
            plotAccordingToChoices(data, current_options);
        });
        // by month
        $("#by_month").click(function() {
            $(this).addClass('choosen_link');
            $("#all_goals, #current_primary_goal,  #whole, #by_year_previous, #by_year, #by_year_next, #by_week, #by_day").removeClass('choosen_link');
            setLocalStorageItem('graph_period', 'options_month');
            current_options = options_month;
            plotAccordingToChoices(data, current_options);
        });
        
        // by week
        $("#by_week").click(function() {
            $(this).addClass('choosen_link');
            $("#all_goals,  #current_primary_goal, #whole, #by_year_previous, #by_year, #by_year_next, #by_month, #by_day").removeClass('choosen_link');
            setLocalStorageItem('graph_period', 'options_week');
            current_options = options_week;
            plotAccordingToChoices(data, current_options);
        });

        // by day
        $("#by_day").click(function() {
            $(this).addClass('choosen_link');
            $("#all_goals, #current_primary_goal,  #whole, #by_year_previous, #by_year, #by_year_next, #by_month, #by_week").removeClass('choosen_link');
            setLocalStorageItem('graph_period', 'options_day');
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
        // END  RUN BY PERIOD

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
                        html +=  "Training Period: " +  (client_data.training_period_name[item.dataIndex] || '') + "</br>";
                        html +=  "Goal: " +  (client_data.goal_mini[item.dataIndex] || '') + "</br>";
                        html +=  "Start: " +  moment(new Date(Date.parse(client_data.start_mini[item.dataIndex]))).format("ddd, D MMM  YYYY, hh:mm") + "</br>";
                        html +=  "Finish: " + moment(new Date(Date.parse(client_data.finish_mini[item.dataIndex]))).format("ddd, D MMM  YYYY, hh:mm") + "</br>";
                        html +=  "Status: " +  (getStatusById(client_data.status_mini[item.dataIndex]) || '') + "</br>"; 
                        $("#tooltip").css("background-color", "#287725");
                        break;
                    case 2 : // Primary Goals
                        html +=  "Goal: " +  (client_data.goal_primary[item.dataIndex] || '') + "</br>";
                        html +=  "Start: " +  moment(new Date(Date.parse(client_data.start_primary[item.dataIndex]))).format("ddd, D MMM  YYYY, hh:mm") + "</br>";
                        html +=  "Finish: " + moment(new Date(Date.parse(client_data.finish_primary[item.dataIndex]))).format("ddd, D MMM  YYYY, hh:mm") + "</br>";
                        html +=  "Status: " +  (getStatusById(client_data.status_primary[item.dataIndex]) || '') + "</br>"; 
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
       html +=  "Date: " +  moment(new Date(Date.parse(client_data[type + '_date'][item.dataIndex]))).format("ddd, D MMM  YYYY, hh:mm") + "</br></br>";
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
    
        // localStorage functions
    function checkLocalStorage() {
        if(typeof(Storage)==="undefined") {
           return false;
        }
        return true;
    }

    function setLocalStorageItem(name, value) {
        if(!checkLocalStorage) return;
        localStorage.setItem(name, value);
    }

    function getLocalStorageItem(name) {
        if(!checkLocalStorage) {
            return false;
        }
        var store_value =  localStorage.getItem(name);
        if(!store_value) return false;
        return store_value;
    }
    //
    
    // Add the  function to the top level of the jQuery object
    $.getGraphData = function(data, client_id, url) {

        var constr = getGraphData(data, client_id, url);

        return constr;
    };
})(jQuery);