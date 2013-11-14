(function($) {
    
    function Goals_frontend(options) {

        //// Goal Model
        Goal_model = Backbone.Model.extend({
            defaults: {
                'pages_number' : 10,
                'list_type' : '0'
            },

            initialize: function(){
                this.listenToOnce(this, "change:saved_item", this.onAddGoal);
            },

            addGoal : function(data) {
                var goal_type = this.get('goal_type');
                var url = this.get('fitness_frontend_url');
                var view = 'goals_periods';

                var task = 'addGoal';
                var table = this.get('goals_db_table');
                data.status = '4';

                if(goal_type == 'mini_goal') {
                    var table = this.get('minigoals_db_table');
                    data.primary_goal_id = this.get('primary_goal_id');

                }

                var self = this;
                this.ajaxCall(data, url, view, task, table, function(output) {
                    self.set("saved_item", output);
                });
            },
            onAddGoal : function() {
                if (this.has("saved_item")){
                    this.sendGoalEmail();
                };
            },
            sendGoalEmail : function() {
                var goal_status = $.status({'calendar_frontend_url' : this.attributes.calendar_frontend_url});
                var id = this.get('saved_item').id;
                var method = 'GoalEvaluating';
                var goal_type = this.get('goal_type');
                if(goal_type == 'mini_goal') {
                    method = 'GoalEvaluatingMini';
                }
                this.sendEmail(id, method);
            },
            
            sendEmail : function(id, method) {
                var data = {};
                var url = this.attributes.fitness_frontend_url;
                var view = '';
                var task = 'ajax_email';
                var table = '';
 
                data.id = id;
                data.view = 'Goal';
                data.method = method;

                var self = this;
                this.ajaxCall(data, url, view, task, table, function(output) {

                });
            },
    
            populateGoals : function() {
                var data = {};
                var url = this.get('fitness_frontend_url');
                var view = 'goals_periods';
                var task = 'populateGoals';
                var table = '';
                var list_type= this.getLocalStorageItem('list_type');
                data.list_type = list_type;
                var self = this;
                this.ajaxCall(data, url, view, task, table, function(output) {
                    //console.log(output);
                    self.set("goals", output);
                });
            },

            ajaxCall : function(data, url, view, task, table, handleData) {
                return $.AjaxCall(data, url, view, task, table, handleData);
            },
            setStatus : function(status) {
                var style_class;
                var text;
                switch(status) {
                    case '1' :
                        style_class = 'goal_status_pending';
                        text = 'PENDING';
                        break;
                    case '2' :
                        style_class = 'goal_status_complete';
                        text = 'COMPLETE';
                        break;
                    case '3' :
                        style_class = 'goal_status_incomplete';
                        text = 'INCOMPLETE';
                        break;
                    case '4' :
                        style_class = 'goal_status_evaluating';
                        text = 'EVALUATING';
                        break;
                    case '5' :
                        style_class = 'goal_status_inprogress';
                        text = 'IN PROGRESS';
                        break;
                    case '6' :
                        style_class = 'goal_status_assessing';
                        text = 'ASSESSING';
                        break;
                    default :
                        style_class = 'goal_status_evaluating';
                        text = 'EVALUATING';
                        break;
                }
                var html = '<a style="cursor:default;" href="javascript:void(0)"  class="status_button ' + style_class + '">' + text + '</a>';
                return html;
            },

            setDefaultText : function(status, string) {
                if(!this.statusReviewed(status)) return this.attributes.pending_review_text;
                return string;
            },

            statusReviewed : function(status) {
                if((status == '4') || (status == '0') || (status == '')) return false;
                return true;
            },
            statusAssessing : function(status) {
                if((status == '6')) return true;
                return false;
            },

            checkLocalStorage : function() {
                if(typeof(Storage)==="undefined") {
                   return false;
                }
                return true;
            },
            setLocalStorageItem : function(name, value) {
                if(!this.checkLocalStorage) return;
                localStorage.setItem(name, value);
            },
            getLocalStorageItem : function(name) {
                var value = this.get(name);
                if(!this.checkLocalStorage) {
                    return value;
                }
                var store_value =  localStorage.getItem(name);
                if(!store_value) return value;
                return store_value;
            }
        });


        //// 
        Goals_graph_model = Backbone.Model.extend({
            defaults: {
            },
            initialize: function(goals){
                this.goal_model = new Goal_model(options);
                this.setGraphData(goals);
            },
            setGraphData : function(goals) {
                var data = {};
                var primary_goals_data = this.setPrimaryGoalsGraphData(goals.primary_goals);
                $.extend(true,data, primary_goals_data);
                var mini_goals_data = this.setMiniGoalsGraphData(goals.mini_goals);
                $.extend(true,data, mini_goals_data);
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

                return data;
            },
            x_axisDateArray : function(data, y_value, field) {
                var x_axis_array = []; 
                for(var i = 0; i < data.length; i++) {
                    var unix_time = new Date(Date.parse(data[i][field])).getTime();
                    x_axis_array[i] = [unix_time, y_value];
                }
                return x_axis_array;
            },
            graphItemDataArray : function(data, type) {
                var items = []; 
                for(var i = 0; i < data.length; i++) {
                    items[i] = data[i][type];
                }
                return items;
            },
            drawGraph : function(client_data) {
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
                for(var i = 0; i < d2.length; i++) {
                    markings[i] =  { xaxis: { from: client_data.mini_goals_start_date[i][0], to: d2[i][0] }, yaxis: { from: 0.25, to: 0.75 }, color: training_period_colors[i]};
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
                        lines: {show: false },
                        points: {show: true, radius: 5, symbol: "circle", fill: true, fillColor: "#0E0704" },
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
                    legend: {show: true, margin: [0, 0], backgroundColor: "none", labelBoxBorderColor:"none"},

                    colors: [
                        "#A3270F",// Primary Goal
                        "#287725", // Mimi Goal
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

                var current_options = {
                    get : function() {return this.options;},
                    set : function(options) {this.options = options}
                };
                // on load
                var graph_period = this.goal_model.getLocalStorageItem('graph_period');

                switch(graph_period) {
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
                $("#whole").click(function() {
                    $(this).addClass('choosen_link');
                    $("#by_year_previous, #by_year, #by_year_next, #by_month").removeClass('choosen_link');
                    self.goal_model.setLocalStorageItem('graph_period', 'options');
                    current_options = options;
                    self.plotAccordingToChoices(data, current_options);
                });
                // by year
                $("#by_year_previous").click(function() {
                    $(this).addClass('choosen_link');
                    $("#whole, #by_year, #by_year_next, #by_month").removeClass('choosen_link');
                    self.goal_model.setLocalStorageItem('graph_period', 'options_year_previous');
                    current_options = options_year_previous;
                    self.plotAccordingToChoices(data, current_options);
                });
                $("#by_year").click(function() {
                    $(this).addClass('choosen_link');
                    $("#whole, #by_year_previous, #by_year_next, #by_month").removeClass('choosen_link');
                    self.goal_model.setLocalStorageItem('graph_period', 'options_year');
                    current_options = options_year;
                    self.plotAccordingToChoices(data, current_options);
                });
                $("#by_year_next").click(function() {
                    $(this).addClass('choosen_link');
                    $("#whole, #by_year_previous, #by_year, #by_month").removeClass('choosen_link');
                    self.goal_model.setLocalStorageItem('graph_period', 'options_year_next');
                    current_options = options_year_next;
                    self.plotAccordingToChoices(data, current_options);
                });
                // by month
                $("#by_month").click(function() {
                    $(this).addClass('choosen_link');
                    $("#whole, #by_year_previous, #by_year, #by_year_next").removeClass('choosen_link');
                    self.goal_model.setLocalStorageItem('graph_period', 'options_month');
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
                    "text-align" : "left",
                }).appendTo("body");

                $("#placeholder").bind("plothover", function (event, pos, item) {
                    if (item) {
                        var data_type = item.datapoint[1];
                        var html = "<p style=\"text-align:center;\"><b>" +  item.series.label + "</b></p>";

                        switch(data_type) {
                            case 1 : // Mini Goals
                                html +=  "Client: " +  client_data.client_mini[item.dataIndex] + "</br>";
                                html +=  "Goal: " +  (client_data.goal_mini[item.dataIndex] || '') + "</br>";
                                html +=  "Start: " +  client_data.start_mini[item.dataIndex] + "</br>";
                                html +=  "Finish: " +  client_data.finish_mini[item.dataIndex] + "</br>";
                                html +=  "Status: " +  (self.getStatusById(client_data.status_mini[item.dataIndex]) || '') + "</br>"; 
                                $("#tooltip").css("background-color", "#287725");
                                break;
                            case 2 : // Primary Goals
                                html +=  "Client: " +  client_data.client_primary[item.dataIndex] + "</br>";
                                html +=  "Goal: " +  (client_data.goal_primary[item.dataIndex] || '') + "</br>";
                                html +=  "Start: " +  client_data.start_primary[item.dataIndex] + "</br>";
                                html +=  "Finish: " +  client_data.finish_primary[item.dataIndex] + "</br>";
                                html +=  "Status: " +  (self.getStatusById(client_data.status_primary[item.dataIndex]) || '') + "</br>"; 
                                $("#tooltip").css("background-color", "#A3270F");
                                break;
                            case 3 : // Current Time
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
                if (data.length > 0) {
                        $.plot("#placeholder", data, options);
                }
            },
            getStatusById : function(id) {
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

        });


        ///// Add view   
        Add_goal_view = Backbone.View.extend({
            initialize: function(){
                this.model = this.options.model;
                this.model.set({'goal_type' : this.options.goal_type, 'primary_goal_id' : this.options.primary_goal_id});
                this.listenToOnce(this.model, "change:saved_item", this.onItemAdded);

                this.render();

            },
            render: function(){
                this.loadTemplate();
                this.loadPlugins();
            },
            loadTemplate : function() {
                var variables = {
                    'title' : this.options.title,
                    'model' : this.model
                }
                var template = _.template( $("#add_goal_template").html(), variables );
                this.$el.html( template );
            },
            onItemAdded : function() {
                if (this.model.has("saved_item")){
                    var default_list_view = new Default_list_view({ el: $("#goal_container") });

                    default_list_view.initialize();

                    this.undelegateEvents();
                };
            },
            loadPlugins: function(){
                var goal_type = this.model.get('goal_type');

                if(goal_type == 'mini_goal') {
                    var model_attr = this.model.attributes;
                    var primary_goal_obj = _.find(model_attr.goals.primary_goals, function(obj) { return obj.id == model_attr.primary_goal_id });
                    var start_date = primary_goal_obj.start_date;
                    var deadline = primary_goal_obj.deadline;
                    var min_date = new Date(Date.parse(start_date));
                    var max_date = new Date(Date.parse(deadline));
                    $( "#start_date, #deadline" ).datepicker({ dateFormat: "yy-mm-dd", minDate: min_date, maxDate: max_date });
                } else {
                    $( "#start_date, #deadline" ).datepicker({ dateFormat: "yy-mm-dd" });
                }
                $("#add_goal_form").validate();
            },
            events: {
                "click #cancel_add_goal" : "cancelAddGoal",
                "submit #add_goal_form" : "addGoal"
            },
            addGoal : function() {

                this.checkOverlapDate();

                this.listenToOnce(this.model, "change:goal_overlap", this.onCheckOverlapDate);

                return false;
            },
            cancelAddGoal : function() {
                this.undelegateEvents();
                var default_list_view = new Default_list_view({ el: $("#goal_container") });
                return false;
            },
            checkOverlapDate : function() {
                var data = {};
                var url = this.model.attributes.fitness_frontend_url;
                var view = 'goals_periods';
                var ajax_task = 'checkOverlapDate';

                var table = '#__fitness_goals';
                data.where_column = 'user_id';
                data.where_value = this.model.attributes.user_id;

                var goal_type = this.model.get('goal_type');
                if(goal_type == 'mini_goal') {
                    var table = '#__fitness_mini_goals';
                    data.where_column = 'primary_goal_id';
                    data.where_value = this.model.attributes.primary_goal_id;
                }

                data.item_id = '';
                data.start_date = $("#start_date").val();
                data.end_date = $("#deadline").val();
                data.start_date_column = 'start_date';
                data.end_date_column = 'deadline';

                var model = this.model;

                this.model.ajaxCall(data, url, view, ajax_task, table, function(output){
                    if(output) {
                         alert('Goal Date is Overlaping!');
                    }
                    model.set("goal_overlap", output);
                    return false;
                });
            },
            onCheckOverlapDate : function() {
                if (!this.model.get("goal_overlap")){
                    var data = {
                        'start_date' : $("#start_date").val(),
                        'deadline' : $("#deadline").val(),
                        'details' : $("#details").val()
                    };
                    this.model.addGoal(data);
                }
                return false;
            }

        });


        /// Goal view
        Goal_view = Backbone.View.extend({
            initialize: function(){
                this.model = this.options.model;
                this.model.set({'goal_type' : this.options.goal_type, 'id' : this.options.id, 'comments' : this.options.comments});
                this.render();
            },
            render: function(){
                this.loadTemplate();
                var comments_html = this.model.attributes.comments.run();
                $("#comments_wrapper").html(comments_html);
            },
            loadTemplate : function() {
                var model = this.model;
                var variables = {
                    'title' : this.options.title,
                    'model' : model,
                }
                var template = _.template( $("#goal_template").html(), variables);
                this.$el.html( template );
            },
            events: {
                "click #cancel_goal" : "cancelGoal",
            },
            cancelGoal : function() {
                this.undelegateEvents();
                var default_list_view = new Default_list_view({ el: $("#goal_container") });
            },
        });




        //// LIst view
        Default_list_view = Backbone.View.extend({
            initialize: function(){
                this.model = new Goal_model(options);
                this.render();
            },
            render: function(){
                this.model.populateGoals();
                this.loadTemplate();
                this.listenToOnce(this.model, "change:goals", this.onPopulateGoals);
            },
            loadTemplate : function() {
                var variables = {

                }
                var template = _.template( $("#default_goal_list_template").html(), variables );
                this.$el.html( template );
                var pages_number = this.model.getLocalStorageItem('pages_number');
                var list_type= this.model.getLocalStorageItem('list_type');
                $("#items_number").val(pages_number);
                $("#list_type").val(list_type);

            },
            events: {
                "click #new_goal" : "addGoal",
                "click .new_mini_goal" : "addMiniGoal",
                "click .open_goal" : "openGoal",
                "click .open_mini_goal" : "openMiniGoal",
                "change #items_number" : "setPagination",
                "change #list_type" : "runList"
            },
            onPopulateGoals : function() {
                if (this.model.has("goals")){
                    var model = this.model;
                    // init Graph
                    this.graph_data = new Goals_graph_model(this.model.attributes.goals);
                    var variables = {
                        'model' : model,
                    }
                    var template = _.template( $("#primary_goal_template").html(), variables);
                    $("#goals_wrapper").html(template);

                };  
            },
            addGoal : function(event) {
                var add_goal_view = new Add_goal_view({ el: $("#goal_container"), 'model' : this.model, 'goal_type' : 'primary_goal', 'title' : 'CREATE PRIMARY GOAL' });
                this.undelegateEvents();
            },
            addMiniGoal : function(event) {
                var primary_goal_id = $(event.target).data('id');
                var add_goal_view = new Add_goal_view({ el: $("#goal_container"), 'model' : this.model, 'goal_type' : 'mini_goal', 'primary_goal_id' : primary_goal_id, 'title' : 'CREATE MINI GOAL'});
                this.undelegateEvents();
            },
            openGoal : function(event) {

                var id = $(event.target).data('id');

                var comment_options = {
                    'item_id' : id,
                    'fitness_administration_url' : this.model.attributes.fitness_frontend_url,
                    'comment_obj' : {'user_name' : this.model.attributes.user_name, 'created' : "", 'comment' : ""},
                    'db_table' : this.model.attributes.goals_comments_db_table,
                    'read_only' : true,
                    'anable_comment_email' : true,
                    'comment_method' : 'GoalComment'
                }
                var comments = $.comments(comment_options, comment_options.item_id, 0);

                var add_goal_view = new Goal_view({ el: $("#goal_container"), 'model' : this.model, 'comments' : comments, 'goal_type' : 'primary_goal', 'id' : id, 'title' : 'MY PRIMARY GOAL' });
                this.undelegateEvents();
            },
            openMiniGoal : function(event) {
                var id = $(event.target).data('id');

                var comment_options = {
                    'item_id' : id,
                    'fitness_administration_url' : this.model.attributes.fitness_frontend_url,
                    'comment_obj' : {'user_name' : this.model.attributes.user_name, 'created' : "", 'comment' : ""},
                    'db_table' : this.model.attributes.minigoals_comments_db_table,
                    'read_only' : true,
                    'anable_comment_email' : true,
                    'comment_method' : 'GoalComment'
                }
                var comments = $.comments(comment_options, comment_options.item_id, 0);

                var add_goal_view = new Goal_view({ el: $("#goal_container"), 'model' : this.model, 'comments' : comments, 'goal_type' : 'mini_goal', 'id' : id, 'title' : 'MY MINI GOAL'});
                this.undelegateEvents();
            },
            setPagination : function(event) {
                var pages_number = $(event.target).val();

                this.initialize();

                $("#items_number").val(pages_number);


                this.model.setLocalStorageItem('pages_number', pages_number);


            },
            runList : function(event) {
                var list_type = $(event.target).val();
                this.model.setLocalStorageItem('list_type', list_type);
                this.initialize();
                $("#list_type").val(list_type);

                //console.log(this.model.getLocalStorageItem('list_type'));
            }
        });

        new Default_list_view({ el: $("#goal_container") });
    }


    $.goals_frontend = function(options) {

        var constr = Goals_frontend(options);

        return constr;
    };


})(jQuery);