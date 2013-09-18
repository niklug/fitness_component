<?php
/**
 * @version     1.0.0
 * @package     com_fitness
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Nikolay Korban <niklug@ukr.net> - http://
 */
// no direct access
defined('_JEXEC') or die;

?>
<div id="goal_container" class="fitness_wrapper">

</div>

<script type="text/template" id="default_goal_list_template">
    <h2>GOALS & TRAINING PERIODS</h2>
    <div class="fitness_block_wrapper" style="min-height:200px;">
        <h3  style="float:left;">MY PRIMARY GOALS</h3>
        <h3 style="float:right;">MY MINI GOALS</h3>
        <div class="clr"></div>
        <hr class="orange_line">
        <div style="width:100%;" id="goals_wrapper">
        
        </div>
        <div class="internal_wrapper">
            <div style="width:100%;text-align: center;">
                <a style="cursor: pointer;" id="new_goal" onclick="javascript:void(0)">[New Goal]</a>
            </div>
        </div>
        <br/>
        <div style="width:100%;text-align: center;">
            Display # 
            <select name="items_number" id="items_number">
                <option value="1">1</option>
                <option value="5">5</option>
                <option selected="selected" value="10">10</option>
                <option value="20">20</option>
                <option value="all">All</option>
            </select> 
        </div>
    </div>
</script>

<script type="text/template" id="add_goal_template">
    <h2>GOALS & TRAINING PERIODS</h2>
    <div class="fitness_block_wrapper" style="min-height:200px;">
        <h3><%= title %></h3>
        <div class="clr"></div>
        <hr class="orange_line">
        <div class="internal_wrapper">
            <form action="" id="add_goal_form">
                <table>
                    <tr>
                        <td>
                            Start Date
                        </td>
                        <td>
                            <input type="text" name="start_date" id="start_date" size="6" class="required">
                        </td>
                        <td>
                            (when will you begin training for this goal?)
                        </td>
                    </tr>
                    <tr>
                        <td>
                            Achieve By 
                        </td>
                        <td>
                            <input type="text" name="deadline" id="deadline" size="6" class="required">
                        </td>
                        <td>
                            (when do you wish to achieve your results?)
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3">
                            Goal Details (describe in as much detail as possible the results you envision and what it is you wish to achieve...)
                        </td>
                    </tr>
                    <tr>
                        <td></td>
                        <td colspan="2">
                           <textarea name="details" id="details" rows="20" cols="90" class="required"></textarea> 
                        </td>
                    </tr>
                </table>
                <br/>
                <button type="submit" id="add_goal">Submit</button>
                <button type="submit" id="cancel_add_goal">Cancel</button>
            </form>
        </div>
    </div>
</script>


<script type="text/template" id="primary_goal_template">
    <%
    var pages_number = model.getPagesNumber();
    var counter = 0;
    _.each(model.attributes.goals.primary_goals, function(item, key){
    
    counter++;
    
    if((counter > pages_number) && (pages_number != 'all')) return;
    %>
        <table width="100%">
            <tr>
                <td width="50%">
                    <table width="100%">
                        <tr>
                            <td style="width:120px;">
                                Primary Goal 
                            </td>
                            <td class="grey_title">
                                <%= model.setDefaultText(item.status, item.primary_goal_name) %>
                                <% if(model.statusReviewed(item.status)) { %>
                                    <div style="width:50px;float: right;">
                                        <a data-id="<%= item.id %>" style="cursor: pointer;" class="open_goal" onclick="javascript:void(0)">[OPEN]</a>
                                    </div>
                                <% } %>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                Start Date 
                            </td>
                            <td class="grey_title">
                                <%
                                var d1 = new Date(Date.parse(item.start_date));            
                                var start_date = moment(d1).format("dddd, D MMMM  YYYY");      
                                %>
                                <%= 
                                    model.setDefaultText(item.status, start_date) 
                                 %>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                Achieve By 
                            </td>
                            <td class="grey_title">
                                <%
                                    var d2 = new Date(Date.parse(item.deadline));            
                                    var deadline = moment(d2).format("dddd, D MMMM  YYYY");      
                                %>
                                <%= model.setDefaultText(item.status, deadline) %>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                Status 
                            </td>
                            <td>
                                <%= model.setStatus(item.status) %>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                Goal Details 
                            </td>
                            <td class="grey_title">
                                <%= item.details %>
                            </td>
                        </tr>
                    </table>
                </td>
                <td>
                    <div class="minigoals_wrapper" style="width:100%;">
                    <% 
                    var primary_goal_id = item.id;
                    _.each(model.attributes.goals.mini_goals, function(item, key){ %>
                         <% if(primary_goal_id == item.primary_goal_id) { %>
                                <table width="100%">
                                    <tr>
                                        <td style="width:120px;">
                                            Mini Goal 
                                        </td>
                                        <td class="grey_title">
                                            <%= model.setDefaultText(item.status, item.mini_goal_name) %>
                                                <% if(model.statusReviewed(item.status)) { %>
                                                <div style="width:50px;float: right;">
                                                    <a data-id="<%= item.id %>" style="cursor: pointer;" class="open_mini_goal" onclick="javascript:void(0)">[OPEN]</a>
                                                </div>
                                            <% } %>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            Training Period 
                                        </td>
                                        <td class="grey_title">
                                            <%= model.setDefaultText(item.status, item.training_period_name) %>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            Start Date 
                                        </td>
                                        <td class="grey_title">
                                            <%
                                            var d1 = new Date(Date.parse(item.start_date));            
                                            var start_date = moment(d1).format("dddd, D MMMM  YYYY");      
                                            %>
                                            <%= 
                                                model.setDefaultText(item.status, start_date) 
                                             %>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            Achieve By 
                                        </td>
                                        <td class="grey_title">
                                            <%
                                                var d2 = new Date(Date.parse(item.deadline));            
                                                var deadline = moment(d2).format("dddd, D MMMM  YYYY");      
                                            %>
                                            <%= model.setDefaultText(item.status, deadline) %>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            Status 
                                        </td>
                                        <td>
                                            <%= model.setStatus(item.status) %>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            Goal Details 
                                        </td>
                                        <td class="grey_title">
                                            <%= item.details %>
                                        </td>
                                    </tr>
                                </table>
                                <br/>
                                <hr>
                                <br/>
                            <%
                            }
                         %>
                    <% }) %>
                    </div>
                    <div style="width:100%;text-align: right;">
                        <a data-id="<%= primary_goal_id %>" style="cursor: pointer;" class="new_mini_goal" onclick="javascript:void(0)">[New Mini Goal]</a>
                    </div>
                </td>
            </tr>
        </table>
        <br/>
        <hr>
        <br/>
    <% }) %>
</script>


<script type="text/template" id="goal_template">
    <h2>GOALS & TRAINING PERIODS</h2>
    <div class="fitness_block_wrapper" style="min-height:200px;">
        
        <h3><%= title %></h3>
        <div class="clr"></div>
        <hr class="orange_line">
        <div class="internal_wrapper">
            <table width="100%">
                <tr>
                    <td width="50%">
                    <%
                        
                        var goal_type = model.attributes.goal_type;
                        var id = model.attributes.id;
                        
                        
                        var goals = model.attributes.goals.primary_goals
                        if(goal_type == 'mini_goal') {
                            goals = model.attributes.goals.mini_goals
                        }
                        
                        _.each(goals, function(item, key){
                            if(item.id == id) {
                                %>
                                <table width="100%">
                                    <% if(goal_type == 'primary_goal') { %>
                                    <tr>
                                        <td style="width:120px;">
                                            Primary Goal 
                                        </td>
                                        <td class="grey_title">
                                            <%= item.primary_goal_name %>
                                        </td>
                                    </tr>
                                    <% } else { %>
                                    <tr>
                                        <td style="width:120px;">
                                            Mini Goal 
                                        </td>
                                        <td class="grey_title">
                                            <%= item.mini_goal_name %>
                                        </td>
                                    </tr>                                    
                                    <% } %>
                                    
                                    <% if(goal_type == 'mini_goal') { %>
                                    <tr>
                                        <td>
                                            Training Period 
                                        </td>
                                        <td class="grey_title">
                                            <%= item.training_period_name %>
                                        </td>
                                    </tr>
                                    <% } %>
                                    <tr>
                                        <td>
                                            Start Date 
                                        </td>
                                        <td class="grey_title">
                                            <%
                                            var d1 = new Date(Date.parse(item.start_date));            
                                            var start_date = moment(d1).format("dddd, D MMMM  YYYY");      
                                            %>
                                            <%= 
                                                start_date 
                                             %>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            Achieve By 
                                        </td>
                                        <td class="grey_title">
                                            <%
                                                var d2 = new Date(Date.parse(item.deadline));            
                                                var deadline = moment(d2).format("dddd, D MMMM  YYYY");      
                                            %>
                                            <%= deadline %>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            Status 
                                        </td>
                                        <td>
                                            <%= model.setStatus(item.status) %>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            Goal Details 
                                        </td>
                                        <td class="grey_title">
                                            <%= item.details %>
                                        </td>
                                    </tr>
                                </table>
                                <%
                            }
                        });
                    
                    %>
                        
                        <button id="cancel_goal">Cancel</button>
                    </td>
                    <td>
                        <div id="comments_wrapper" style="width:100%"></div>
                        <div class="clr"></div>
                        <br/>
                        <input id="add_comment_0" class="" type="button" value="Add Comment" >
                        <div class="clr"></div>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</script>

<script type="text/javascript">
    
    (function($) {
        
        var options = {
            'fitness_frontend_url' : '<?php echo JURI::root();?>index.php?option=com_fitness&tmpl=component&<?php echo JSession::getFormToken(); ?>=1',
            'calendar_frontend_url' : '<?php echo JURI::root()?>index.php?option=com_multicalendar&task=load&calid=0',
            'pending_review_text' : 'Pending Review',
            'user_name' : '<?php echo JFactory::getUser()->name;?>',
            'goals_db_table' : '#__fitness_goals',
            'minigoals_db_table' : '#__fitness_mini_goals',
            'goals_comments_db_table' : '#__fitness_goal_comments',
            'minigoals_comments_db_table' : '#__fitness_mini_goal_comments'
        }
        

        //// Goal Model
        Goal_model = Backbone.Model.extend({
            defaults: {
                'pages_number' : 10
            },
            
            initialize: function(){

            },
            
            addGoal : function(data) {
                
                var goal_type = this.get('goal_type');
                var url = this.get('fitness_frontend_url');
                var view = 'goals_periods';
                
                var task = 'addGoal';
                var table = this.get('goals_db_table');
                
                if(goal_type == 'mini_goal') {
                    var table = this.get('minigoals_db_table');
                    data.primary_goal_id = this.get('primary_goal_id')
                }
                
                var self = this;
                this.ajaxCall(data, url, view, task, table, function(output) {
                    self.set("saved_item", output);
                });
            },

            populateGoals : function() {
                var data = {};
                var url = this.get('fitness_frontend_url');
                var view = 'goals_periods';
                var task = 'populateGoals';
                var table = '';
                var self = this;
                this.ajaxCall(data, url, view, task, table, function(output) {
                    //console.log(output);
                    self.set("goals", output);
                });
            },

            ajaxCall : function(data, url, view, task, table, handleData) {
                var data_encoded = JSON.stringify(data);
                $.ajax({
                    type : "POST",
                    url : url,
                    data : {
                        view : view,
                        task : task,
                        format : 'text',
                        data_encoded : data_encoded,
                        table : table
                    },
                    dataType : 'json',
                    success : function(response) {
                        if(!response.status.success) {
                            alert(response.status.message);
                            return;
                        }
                        handleData(response.data);
                    },
                    error: function(XMLHttpRequest, textStatus, errorThrown)
                    {
                        alert( task + " error");
                    }
                }); 
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
                    default :
                        style_class = 'goal_status_evaluating';
                        text = 'EVALUATING';
                        break;
                }
                var html = '<a href="javascript:void(0)"  class="status_button ' + style_class + '">' + text + '</a>';
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
            
            checkLocalStorage : function() {
                if(typeof(Storage)==="undefined") {
                   return false;
                }
                return true;
            },
            
            setPagesNumber : function(pages_number) {
                if(!this.checkLocalStorage) return;
                localStorage.setItem('pages_number', pages_number);
            },
            getPagesNumber : function() {
                var pages_number = this.get('pages_number');
                if(!this.checkLocalStorage) {
                    return pages_number;
                }
                
                var store_pages_number =  localStorage.getItem('pages_number');
                
                if(!store_pages_number) return pages_number;
                
                return localStorage.getItem('pages_number');
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
                    'title' : this.options.title
                }
                var template = _.template( $("#add_goal_template").html(), variables );
                this.$el.html( template );
            },
            onItemAdded : function() {
                if (this.model.has("saved_item")){
                    //console.log(this.model);
                };
            },
            loadPlugins: function(){
                $( "#start_date, #deadline" ).datepicker({ dateFormat: "yy-mm-dd" });
                $("#add_goal_form").validate();
            },
            events: {
                "click #cancel_add_goal" : "cancelAddGoal",
                "submit #add_goal_form" : "addGoal"
            },
            addGoal : function() {
                
                var data = {
                    'start_date' : $("#start_date").val(),
                    'deadline' : $("#deadline").val(),
                    'details' : $("#details").val()
                };

                this.model.addGoal(data);
                
                var default_list_view = new Default_list_view({ el: $("#goal_container") });
                
                this.undelegateEvents();
            },
            cancelAddGoal : function() {
                this.undelegateEvents();
                var default_list_view = new Default_list_view({ el: $("#goal_container") });
            },

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
                var pages_number = this.model.getPagesNumber();
                $("#items_number").val(pages_number);
            },
            events: {
                "click #new_goal" : "addGoal",
                "click .new_mini_goal" : "addMiniGoal",
                "click .open_goal" : "openGoal",
                "click .open_mini_goal" : "openMiniGoal",
                "change #items_number" : "setPagination" 
            },
            onPopulateGoals : function() {
                if (this.model.has("goals")){
                     //console.log(goals);
                    var model = this.model;
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
                    'read_only' : true
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
                    'read_only' : true
                }
                var comments = $.comments(comment_options, comment_options.item_id, 0);
                
                var add_goal_view = new Goal_view({ el: $("#goal_container"), 'model' : this.model, 'comments' : comments, 'goal_type' : 'mini_goal', 'id' : id, 'title' : 'MY MINI GOAL'});
                this.undelegateEvents();
            },
            setPagination : function(event) {
                var pages_number = $(event.target).val();
                        
                this.initialize();

                $("#items_number").val(pages_number);
                
                       
                this.model.setPagesNumber(pages_number);

 
            }
        });

        var default_list_view = new Default_list_view({ el: $("#goal_container") });

        
        
    })($js);

    
</script>



