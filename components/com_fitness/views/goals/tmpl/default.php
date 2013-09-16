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
        <h3>CREATE PRIMARY GOAL</h3>
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




<script type="text/javascript">
    
    (function($) {
        
        var options = {
            'fitness_frontend_url' : '<?php echo JURI::root();?>index.php?option=com_fitness&tmpl=component&<?php echo JSession::getFormToken(); ?>=1',
            'calendar_frontend_url' : '<?php echo JURI::root()?>index.php?option=com_multicalendar&task=load&calid=0',
        }
        //// Goal Model
        Goal_model = Backbone.Model.extend({
            defaults: {},
            
            initialize: function(){ },
            
            addGoal : function(data) {
                var url = this.get('fitness_frontend_url');
                var view = 'goals';
                var task = 'addPrimaryGoal';
                var table = '#__fitness_goals';
                var self = this;
                this.ajaxCall(data, url, view, task, table, function(output) {
                    self.set("saved_item", output);
                });
            },
                    
            deleteGoal : function(o) {},
            
            populateGoals : function(o) {},

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
                        if(!response.status.IsSuccess) {
                            alert(response.status.Msg);
                            return;
                        }
                        handleData(response.data);
                    },
                    error: function(XMLHttpRequest, textStatus, errorThrown)
                    {
                        alert( task + " error");
                    }
                }); 
            }
        });

        ///// Add view   
        Add_goal_view = Backbone.View.extend({
            initialize: function(){
                this.model = new Goal_model(options);
               
                this.listenToOnce(this.model, "change:saved_item", this.onItemAdded);
                
                this.render();
                
            },
            render: function(){
                this.loadTemplate();
                this.loadPlugins();
            },
            loadTemplate : function() {
                var template = _.template( $("#add_goal_template").html(), {} );
                this.$el.html( template );
            },
            onItemAdded : function() {
                if (this.model.has("saved_item")){
                    console.log(this.model);
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
                this.render();
            },
            render: function(){
                // Compile the template using underscore
                var template = _.template( $("#default_goal_list_template").html(), {} );
                // Load the compiled HTML into the Backbone "el"
                this.$el.html( template );
            },
            events: {
                "click #new_goal": "addGoal"
            },
                    
            addGoal : function(event) {
               var add_goal_view = new Add_goal_view({ el: $("#goal_container") });
               this.undelegateEvents();
            }
        });

        var default_list_view = new Default_list_view({ el: $("#goal_container") });

        
        
    })($js);

    
</script>



