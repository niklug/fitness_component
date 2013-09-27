/*
 * class provide comments system
 */
(function($) {
    function Comments(options, item_id, sub_item_id) {
        this.options = options;
        this.item_id = item_id;
        this.sub_item_id = sub_item_id;
    }

    Comments.prototype.run = function() {
        var comments_wrapper = this.generateHtml();
        this.setEventListeners();
        return comments_wrapper;
    }

    Comments.prototype.setEventListeners = function() {
        var self = this;
        $("#add_comment_" + this.sub_item_id).die().live('click', function() {
            var comment_template = self.createCommentTemplate(self.options.comment_obj);
            $("#comments_wrapper_" + self.sub_item_id).append(comment_template);
        });

        $("#save_comment_" + this.sub_item_id).die().live('click', function() {
            var comment_wrapper = $(this).closest("table").parent();
            var id = comment_wrapper.attr("data-id");
            var comment_text = $(this).closest("table").find("textarea.comment_textarea").val();
            var date = $(this).closest("table").find(".comment_date").text();
            var time = $(this).closest("table").find(".comment_time").text();
            var created = date + ' ' + time;
            var obj = {'id' : id, 'comment' : comment_text, 'item_id' : self.item_id, 'sub_item_id' : self.sub_item_id, 'created' : created};
            self.savePlanComment(obj, function(output){
                var comment_obj = output;
                var comment_html = self.createCommentTemplate(comment_obj);
                comment_wrapper.replaceWith(comment_html);
                alert('fuck');
                //console.log(comment_obj);
           });
        });

        $("#delete_comment_" + this.sub_item_id).die().live('click', function(){
            var comment_wrapper = $(this).closest("table").parent();
            var id = comment_wrapper.attr('data-id');
            self.deletePlanComment(id, function(output) {
                if(output) {
                    comment_wrapper.remove();
                }
            });
        });


        this.populatePlanComments(function(comments) {
            if(!comments) return;
            var html = '';
            comments.each(function(comment_obj){
                html += self.createCommentTemplate(comment_obj);
            });
            $("#comments_wrapper_" + self.sub_item_id).append(html);

        });

    }

    Comments.prototype.generateHtml = function() {
        var html = 'QUESTIONS / COMMENTS / INSTRUCTIONS';
        html += '<div id="comments_wrapper_' + this.sub_item_id + '">';
        html += '</div>';
        return html;
    }

    Comments.prototype.createCommentTemplate = function(comment_obj) {
        var d1 = new Date();
        if(comment_obj.created) {
            d1 = new Date(Date.parse(comment_obj.created));
        }
        var current_time = this.getCurrentDate(d1);
        var comment_template = '<div data-id="' + comment_obj.id + '" class="comment_wrapper">';
        comment_template += '<table width="100%">';
        comment_template += '<tr>';
        comment_template += '<td><b>Comment by: </b><span class="comment_by">' + comment_obj.user_name +  '</span></td>';
        comment_template += '<td><b>Date: </b> <span class="comment_date">' + current_time.date +  '</span></td>';
        comment_template += '<td><b>Time: </b> <span class="comment_time">' + current_time.time_short +  '</span></td>';
        
        if(!comment_obj.id) {
            comment_template += '<td><input id="save_comment_' + this.sub_item_id + '" class="save_comment" type="button"  value="Save"></td>'
        }
        
        if(!this.options.read_only) {
            comment_template += '<td align="center"><a href="javascript:void(0)" class="delete_comment" id="delete_comment_' + this.sub_item_id + '" title="delete"></a></td>';
        }
        
        comment_template += '</tr>';
        comment_template += '<tr>';
        comment_template += '<td colspan="5"><textarea  class="comment_textarea" cols="100" rows="3">' + comment_obj.comment +  '</textarea></td>';
        comment_template += '</tr>';
        comment_template += '</table>';
        comment_template += '</div>';
        return comment_template;
    }


    Comments.prototype.getCurrentDate = function(d1) {
        var date = d1.getFullYear() + "-" + (this.pad(d1.getMonth()+1)) + "-" + this.pad(d1.getDate()); 
        var time = this.pad(d1.getHours()) + ":" + this.pad(d1.getMinutes()) + ":" + this.pad(d1.getSeconds());
        var time_short = this.pad(d1.getHours()) + ":" + this.pad(d1.getMinutes());
        return {'date' : date, 'time' : time, 'time_short' : time_short};
    }

    Comments.prototype.pad = function pad(d) {
        return (d < 10) ? '0' + d.toString() : d.toString();
    }

    Comments.prototype.savePlanComment = function(o, handleData) {
        var table = this.options.db_table;
        if(o.id === 'undefined')  o.id = "";
        var data_encoded = JSON.stringify(o); 
        var url = this.options.fitness_administration_url;
        $.ajax({
            type : "POST",
            url : url,
            data : {
                view : 'nutrition_plan',
                format : 'text',
                task : 'savePlanComment',
                data_encoded : data_encoded,
                table : table
            },
            dataType : 'json',
            success : function(response) {
                if(!response.status.success) {
                    alert(response.status.Msg);
                    return;
                }
                handleData(response.data);
              },
            error: function(XMLHttpRequest, textStatus, errorThrown)
            {
                alert("error savePlanComment");
            }
        }); 
    }



    Comments.prototype.deletePlanComment = function(id, handleData) {
        var table = this.options.db_table;
        var url = this.options.fitness_administration_url;
        $.ajax({
            type : "POST",
            url : url,
            data : {
                view : 'nutrition_plan',
                format : 'text',
                task : 'deletePlanComment',
                id : id,
                table : table
              },
            dataType : 'json',
            success : function(response) {
                if(!response.status.success) {
                    alert(response.status.Msg);
                    return;
                }
                handleData(response.id);
                },
            error: function(XMLHttpRequest, textStatus, errorThrown)
            {
                alert("error deletePlanComment");
            }
        }); 
    }


    Comments.prototype.populatePlanComments = function(handleData) {
        var table = this.options.db_table;
        var url = this.options.fitness_administration_url;
        var item_id = this.options.item_id;
        var sub_item_id = this.sub_item_id;
        $.ajax({
            type : "POST",
            url : url,
            data : {
                view : 'nutrition_plan',
                format : 'text',
                task : 'populatePlanComments',
                item_id : item_id,
                sub_item_id : sub_item_id,
                table : table
            },
            dataType : 'json',
            success : function(response) {
                if(!response.status.success) {
                    alert(response.status.Msg);
                    return;
                }
                handleData(response.comments);
            },
            error: function(XMLHttpRequest, textStatus, errorThrown)
            {
                alert("error populatePlanComments");
            }
        }); 
    }
    // Add the  function to the top level of the jQuery object
    $.comments = function(options, item_id, sub_item_id) {

        var constr = new Comments(options, item_id, sub_item_id);

        return constr;
    };

})(jQuery);