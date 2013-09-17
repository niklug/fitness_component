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
    <% _.each(goals.primary_goals, function(item, key){ %>
        <table width="100%">
            <tr>
                <td width="50%">
                    <table>
                        <tr>
                            <td style="width:120px;">
                                Primary Goal 
                            </td>
                            <td class="grey_title">
                                <%= model.setDefaultText(item.status, item.primary_goal_name) %>
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
                    _.each(goals.mini_goals, function(item, key){ %>
                         <% if(primary_goal_id == item.primary_goal_id) { %>
                                <table>
                                    <tr>
                                        <td style="width:120px;">
                                            Mini Goal 
                                        </td>
                                        <td class="grey_title">
                                            <%= model.setDefaultText(item.status, item.primary_goal_name) %>
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

<script type="text/javascript">
    
    (function($) {
        
        var options = {
            'fitness_frontend_url' : '<?php echo JURI::root();?>index.php?option=com_fitness&tmpl=component&<?php echo JSession::getFormToken(); ?>=1',
            'calendar_frontend_url' : '<?php echo JURI::root()?>index.php?option=com_multicalendar&task=load&calid=0',
            'pending_review_text' : 'Pending Review'
        }


        //// Goal Model
        Goal_model = Backbone.Model.extend({
            defaults: {},
            
            initialize: function(){ },
            
            addGoal : function(data) {
                
                var goal_type = this.get('goal_type');
                var url = this.get('fitness_frontend_url');
                var view = 'goals_periods';
                
                var task = 'addGoal';
                var table = '#__fitness_goals'; 
                
                if(goal_type == 'mini_goal') {
                    var table = '#__fitness_mini_goals';
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
                if((status == '4') || (status == '0') || (status == '')) return this.attributes.pending_review_text;
                return string;
            }
        });





        ///// Add view   
        Add_goal_view = Backbone.View.extend({
            initialize: function(){
                this.model = new Goal_model(options);
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



        
        //// LIst view
        Default_list_view = Backbone.View.extend({
            initialize: function(){
                this.model = new Goal_model(options);
                this.model.populateGoals();
                this.listenToOnce(this.model, "change:goals", this.onPopulateGoals);
                this.render();
            },
            render: function(){
                this.loadTemplate();
            },
            loadTemplate : function() {
                var template = _.template( $("#default_goal_list_template").html(), {} );
                this.$el.html( template );
            },
            events: {
                "click #new_goal": "addGoal",
                "click .new_mini_goal": "addMiniGoal"
            },
            onPopulateGoals : function() {
                if (this.model.has("goals")){
                    var goals = this.model.attributes.goals;
                    //console.log(goals);
                    var model = this.model;
                    var variables = {
                        'goals' : goals, 
                        'model' : model,
                    }
                    var template = _.template( $("#primary_goal_template").html(), variables);
                    $("#goals_wrapper").html(template);
                    
                };  
            },
            addGoal : function(event) {
                var add_goal_view = new Add_goal_view({ el: $("#goal_container"), 'goal_type' : 'primary_goal', 'title' : 'CREATE PRIMARY GOAL' });
                this.undelegateEvents();
            },
            addMiniGoal : function(event) {
                var primary_goal_id = $(event.target).data('id');
                
                var add_goal_view = new Add_goal_view({ el: $("#goal_container"), 'goal_type' : 'mini_goal', 'primary_goal_id' : primary_goal_id, 'title' : 'CREATE MINI GOAL'});
                this.undelegateEvents();
            }
        });

        var default_list_view = new Default_list_view({ el: $("#goal_container") });

        
        
    })($js);

    
</script>



