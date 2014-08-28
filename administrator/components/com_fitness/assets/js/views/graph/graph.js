define([
    'jquery',
    'underscore',
    'backbone',
    'app',
    'collections/graph/training_periods',
    'collections/exercise_library/business_profiles',
    'collections/programs/trainers',
    'collections/programs/trainer_clients',
    'views/programs/select_element',
    'text!templates/graph/graph.html',
], function(
        $,
        _,
        Backbone,
        app,
        Periods_collection,
        Business_profiles_collection, 
        Trainers_collection,
        Trainer_clients_collection,
        Select_element_view,
        template
        ) {

    var view = Backbone.View.extend({
        
        initialize: function() {
            this.show_client_select = this.options.show.client_select || false;
            this.show = this.options.show || false;
            this.reloads = this.options.reloads || false;
            this.list_type = this.options.list_type || '';
            
            this.business_profile_id = app.options.business_profile_id || localStorage.getItem('business_profile_id');
            
            if(app.collections.training_periods && !this.show_client_select) {
                this.render();
                return;
            }
            
            if(app.collections.training_periods && this.show_client_select) {
                
                if(app.collections.business_profiles && app.collections.trainers) {
                    this.render();
                    return;
                }
            } 
      
            app.collections.training_periods = new Periods_collection();
            app.collections.business_profiles = new Business_profiles_collection();
            app.collections.trainers = new Trainers_collection();
            
                       
            var self = this;
            
            if(!this.show_client_select) {
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
                
            } else {
                
                $.when (
                    app.collections.training_periods.fetch({
                        success : function (collection, response) {
                            //console.log(collection);
                        },
                        error : function (collection, response) {
                            alert(response.responseText);
                        }
                    }),
                    
                    app.collections.business_profiles.fetch({
                        error: function (collection, response) {
                            alert(response.responseText);
                        }
                    }),

                    app.collections.trainers.fetch({
                        error: function (collection, response) {
                            alert(response.responseText);
                        }
                    })
                ).then (function(response) {
                    self.render();
                })
            }
        },
        
        template: _.template(template),
        
        render: function() {
            
            var data = {};
            data.head_title = this.options.head_title || false;
            data.show_client_select = this.show_client_select;
            data.show = this.options.show;
            $(this.el).html(this.template(data));
            
            this.onRender();

            return this;
        },
        
        events : {
            "change #graph_business_profile_id " : "onChangeBusinessName",
            "change #graph_trainer_id" : "onChangeTrainer",
            "change #graph_client_id" : "onChangeClient",
        },
        
        onRender : function() {
            var self = this;
            $(this.el).show('0', function() {
                self.getGraphData();
                self.populateTrainingPerions();
                
                if(self.show_client_select) {
                    self.connectBusinessSelect();
                    
                    var business_profile_id = self.business_profile_id;
                    if(business_profile_id) {
                        self.loadTrainersSelect(business_profile_id);
                    }
                    
                    var trainer_id = $(self.el).find("#graph_trainer_id").val();
                    if(trainer_id) {
                        self.loadClientsSelect(trainer_id);
                    }
                }
                if(self.options.show.choices) {
                    $(self.el).find("#choices").show();
                }
            });
        },
        
        connectBusinessSelect : function() {
            var business_name_collection = new Backbone.Collection;
            
            var element_disabled = '';
            
            if(app.options.is_trainer) {
                business_name_collection.add(app.collections.business_profiles.where({id : this.business_profile_id}));
                element_disabled = 'disabled';
            }
            
            if(app.options.is_superuser) {
                business_name_collection = app.collections.business_profiles;
            }
            
             new Select_element_view({
                model : new Backbone.Model({business_profile_id : this.business_profile_id}),
                el : $(this.el).find("#graph_business_name_select"),
                collection : business_name_collection,
                first_option_title : '- Business profile-',
                id_name : 'graph_business_profile_id',
                model_field : 'business_profile_id',
                element_disabled : element_disabled

            }).render();
        },
        
        onChangeBusinessName : function(event) {
            var business_profile_id = $(event.target).val();
            
            this.business_profile_id = business_profile_id;
            
            localStorage.setItem('business_profile_id', business_profile_id);
   
            this.loadTrainersSelect(business_profile_id);
        },
        
        loadTrainersSelect : function(business_profile_id) {
            var trainers_collection = new Backbone.Collection;
            
            trainers_collection.add(app.collections.trainers.where({business_profile_id : business_profile_id}));
            
            //console.log(trainers_collection);
            
            var element_disabled = '';
            
            //allow select only for trainer administrator
            
            if(app.options.is_simple_trainer) {
                element_disabled = 'disabled';
            }
            
            var trainer_id = localStorage.getItem('trainer_id');
            
            if(app.options.is_trainer && !app.options.is_trainer_administrator) {
                trainer_id = app.options.user_id 
                this.model.set({trainer_id : trainer_id});
            }
            
            new Select_element_view({
                model : new Backbone.Model({trainer_id : trainer_id}),
                el : $(this.el).find("#graph_trainer_select"),
                collection : trainers_collection,
                first_option_title : '-Select-',
                class_name : '',
                id_name : 'graph_trainer_id',
                model_field : 'trainer_id',
                element_disabled : element_disabled
            }).render();
        },
        
        onChangeTrainer : function(event) {
            var trainer_id = $(event.target).val();
            
            this.trainer_id = trainer_id;
            
            localStorage.setItem('trainer_id', trainer_id);
            
            this.loadClientsSelect(trainer_id);
        },
        
        loadClientsSelect : function(trainer_id) {
            var self = this;
            var trainer_clients_collection = new Trainer_clients_collection();
            trainer_clients_collection.fetch({
                data : {trainer_id : trainer_id},
                success : function (collection, response) {
                    new Select_element_view({
                        model : new Backbone.Model({client_id : localStorage.getItem('client_id')}),
                        el : $(self.el).find("#graph_client_select"),
                        collection : collection,
                        value_field : 'client_id',
                        first_option_title : '-Select-',
                        class_name : '',
                        model_field : 'client_id',
                        id_name : 'graph_client_id',
                    }).render();
                },
                error : function (collection, response) {
                    alert(response.responseText);
                }
            })
        },
        
        onChangeClient : function(event) {
            var client_id = $(event.target).val();
            //console.log(client_id);
            localStorage.setItem('client_id', client_id);
            delete app.models.graph_data;
            this.getGraphData();
        },
        
        
        //GRAPH
        getGraphData: function() {
            if(app.models.graph_data && !this.reloads) {
                this.setGraphData( app.models.graph_data.get('data'));
                return;
            } 
            var data = {};
            var url = app.options.ajax_call_url;
            var view = 'goals_periods';
            var task = 'populateGoals';
            var table = '';
            data.list_type = this.list_type;
            
            if(this.show_client_select) {
                data.client_id = localStorage.getItem('client_id');
            }
            
            var self = this;
            $.AjaxCall(data, url, view, task, table, function(output) {
                //console.log(output);
                app.models.graph_data = new Backbone.Model();
                app.models.graph_data.set({data : output});
                
                self.setGraphData(output);
            });
        },
        
        setGraphData: function(response) {
            //console.log(goals);
            var data = {};

            // primary goals
            var primary_goals_data = this.setPrimaryGoalsGraphData(response.primary_goals);
            $.extend(true,data, primary_goals_data);

            // mini goals
            var mini_goals_data = this.setMiniGoalsGraphData(response.mini_goals);
            $.extend(true,data, mini_goals_data);

            // Personal training
            var personal_training_data = this.setAppointmentGraphData('personal_training', response.personal_training, 3);
            $.extend(true,data, personal_training_data);
            //console.log(personal_training_data);

            // Semi-Private Training
            var semi_private_data = this.setAppointmentGraphData('semi_private', response.semi_private, 4);
            $.extend(true,data, semi_private_data);

            // Resistance Workout
            var resistance_workout_data = this.setAppointmentGraphData('resistance_workout', response.resistance_workout, 5);
            $.extend(true,data, resistance_workout_data);

            // Cardio Workout
            var cardio_workout_data = this.setAppointmentGraphData('cardio_workout', response.cardio_workout, 6);
            $.extend(true,data, cardio_workout_data);

            // Assessment
            var assessment_data = this.setAppointmentGraphData('assessment', response.assessment, 7);
            $.extend(true,data, assessment_data);  
            this.drawGraph(data);
        },
        
        setPrimaryGoalsGraphData : function(primary_goals) {
            var data = {};
            data.primary_goals = this.x_axisDateArray(primary_goals, 2, 'deadline');
            data.client_primary = this.graphItemDataArray(primary_goals, 'client_name');
            data.goal_primary = this.graphItemDataArray(primary_goals, 'primary_goal_name');
            data.start_primary = this.graphItemDataArray(primary_goals, 'start_date');
            data.finish_primary = this.graphItemDataArray(primary_goals, 'deadline');
            data.status_primary = this.graphItemDataArray(primary_goals, 'status');

            return data;
        },

        setMiniGoalsGraphData : function(mini_goals) {
            var data = {};
            data.mini_goals_start_date = this.x_axisDateArray(mini_goals, 1, 'start_date');
            data.mini_goals = this.x_axisDateArray(mini_goals, 1, 'deadline');
            data.client_mini = this.graphItemDataArray(mini_goals, 'client_name');
            data.goal_mini = this.graphItemDataArray(mini_goals, 'mini_goal_name');
            data.start_mini = this.graphItemDataArray(mini_goals, 'start_date');
            data.finish_mini = this.graphItemDataArray(mini_goals, 'deadline');
            data.status_mini = this.graphItemDataArray(mini_goals, 'status');
            data.training_period_colors = this.graphItemDataArray(mini_goals, 'training_period_color');
            data.training_period_name = this.graphItemDataArray(mini_goals, 'training_period_name');
            return data;
        },

        setAppointmentGraphData : function(type, appointment, y_axis) {
            var data = {};

            data[type + '_xaxis'] = this.x_axisDateArray(appointment, y_axis, 'starttime');
            data[type + '_session_type'] = this.graphItemDataArray(appointment, 'session_type_name');
            data[type + '_session_focus'] = this.graphItemDataArray(appointment, 'session_focus_name');
            data[type + '_date'] = this.graphItemDataArray(appointment, 'starttime');
            data[type + '_trainer'] = this.graphItemDataArray(appointment, 'trainer_name');
            data[type + '_location'] = this.graphItemDataArray(appointment, 'location_name');
            data[type + '_appointment_color'] = this.graphItemDataArray(appointment, 'color');

            //console.log(data);
            return data;      
        },


        graphItemDataArray : function graphItemDataArray(data, type) {
            var items = []; 
            for(var i = 0; i < data.length; i++) {
                items[i] = data[i][type];
            }
            return items;
        },


        x_axisDateArray : function (data, y_value, field) {
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
                legend: {show: true, margin: [0, 0], backgroundColor : "transparent"},

                colors: []
            };
            
            if(this.options.style == 'dark') {
                options.grid.backgroundColor.colors =  ["#0E0704", "#0E0704"];
                options.grid.markingsColor =  "#0E0704";
                options.grid.color =  "#C0C0C0";
                options.series.points.fillColor =  "#0E0704";
            }

            // set show data
            var data = [];

            if(this.show.primary_goals) {
                data.push({label: "Primary Goal", data: d1});
                options.colors.push("#A3270F");
            }
            if(this.show.mini_goals) {
                data.push({label: "Mini Goal", data: d2});
                options.colors.push("#287725");
            }
            if(this.show.personal_training) {
                data.push({label: "Personal Training", data: d3});
                options.colors.push(client_data.personal_training_appointment_color[0] || "#00BF32");
            }
            if(this.show.semi_private) {
                data.push({label: "Semi-Private Training", data: d4});
                options.colors.push(client_data.semi_private_appointment_color[0] || "#007F01");
            }
            if(this.show.resistance_workout) {
                data.push({label: "Resistance Workout", data: d5});
                options.colors.push(client_data.resistance_workout_appointment_color[0] || "#0070FF");
            }
            if(this.show.cardio_workout) {
                data.push({label: "Cardio Workout", data: d6});
                options.colors.push(client_data.cardio_workout_appointment_color[0] || "#E94E1B");
            }
            if(this.show.assessment) {
                data.push({label: "Assessment", data: d7});
                options.colors.push(client_data.assessment_appointment_color[0] || "#E6007E");
            }
            if(this.show.current_time) {
                data.push({label: "Current Time", data: d8});
                options.colors.push("#FFB01F");
            }
            //

            
            
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

             // TOOGLE
            // insert checkboxes 
            $(this.el).find("#choices").die().empty();
            $.each(data, function(key, val) {
                $(self.el).find("#choices").die().append(
                    "<br/><input type='checkbox' name='" + key +
                    "' checked='checked' id='id" + key + "'></input>" +
                    "<label for='id" + key + "'>"
                    + val.label + "</label>"
                );
            });
            $(this.el).find("#choices").find("input").die().click(function() {
                self.plotAccordingToChoices(data, current_options);
            });
            self.plotAccordingToChoices(data, current_options);
            //END TOOGLE
            
            var tooltip_color = "#000";
            
            if(this.options.style == 'dark') {
                tooltip_color = "#fff";
            }

            $("<div id='tooltip'></div>").css({
                    position: "absolute",
                    display: "none",
                    border: "2px solid #cccccc",
                    "border-radius": "10px",
                    padding: "5px",
                    "background-color": "#fee",
                    opacity: 0.9,
                    color : tooltip_color
            }).appendTo("body");

            $("#placeholder").die().bind("plothover", function (event, pos, item) {
                if (item) {
                    var data_type = item.datapoint[1];
                    var html = "<p style=\"text-align:center;\"><b>" +  item.series.label + "</b></p>";

                    switch(data_type) {
                        case 1 : // Mini Goals
                            html +=  "Training Period: " +  (client_data.training_period_name[item.dataIndex] || '') + "</br>";
                            html +=  "Goal: " +  (client_data.goal_mini[item.dataIndex] || '') + "</br>";
                            html +=  "Start: " +   moment(new Date(Date.parse(client_data.start_mini[item.dataIndex]))).format("ddd, D MMM  YYYY") + "</br>";
                            html +=  "Finish: " +  moment(new Date(Date.parse(client_data.finish_mini[item.dataIndex]))).format("ddd, D MMM  YYYY") + "</br>";
                            $("#tooltip").css("background-color", "#287725");
                            break;
                        case 2 : // Primary Goals
                            html +=  "Goal: " +  (client_data.goal_primary[item.dataIndex]  || '') + "</br>";
                            html +=  "Start: " +  moment(new Date(Date.parse(client_data.start_primary[item.dataIndex]))).format("ddd, D MMM  YYYY") + "</br>";
                            html +=  "Finish: " + moment(new Date(Date.parse(client_data.finish_primary[item.dataIndex]))).format("ddd, D MMM  YYYY")  + "</br>";
                            $("#tooltip").css("background-color", "#A3270F");
                            break;
                        case 3 : // Personal Training
                            html =  self.setAppointmentsTooltip(html, client_data, item, 'personal_training');
                            break;
                        case 4 : // Semi-Private Training
                            html =  self.setAppointmentsTooltip(html, client_data, item, 'semi_private');
                            break;
                        case 5 : // Resistance Workout
                            html =  self.setAppointmentsTooltip(html, client_data, item, 'resistance_workout');
                            break;
                        case 6 : //  Cardio Workout
                            html =  self.setAppointmentsTooltip(html, client_data, item, 'cardio_workout');
                              break;
                        case 7 : // Assessment
                            html =  self.setAppointmentsTooltip(html, client_data, item, 'assessment');
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


        },
        
        plotAccordingToChoices : function(data, options) {
            var data_temp = [];
            var colors = [];
            $(this.el).find("#choices").die().find("input:checked").each(function () {
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
        },
        
        setAppointmentsTooltip : function(html, client_data, item, type) {

           $("#tooltip").css("background-color", client_data[type + '_appointment_color'][0]);

           html +=  "Session Type: " +  client_data[type + '_session_type'][item.dataIndex] + "</br>";
           html +=  "Session Focus: " +  client_data[type + '_session_focus'][item.dataIndex] + "</br>";
           html +=  "Date: " +  moment(new Date(Date.parse(client_data[type + '_date'][item.dataIndex]))).format("ddd, D MMM  YYYY, hh:mm") + "</br></br>";
           html +=  "Trainer: " +  client_data[type + '_trainer'][item.dataIndex] + "</br>";
           html +=  "Location: " +  client_data[type + '_location'][item.dataIndex] + "</br>"; 

           return html;
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
                var html = color + name + '<div class="clr"></div>';
                el.append(html);
            });
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