/*
 * class provide comments system
 */
(function (factory) {
	if (typeof define === 'function' && define.amd) {
		// AMD. Register as an anonymous module.
		define(['jquery'], factory);
	} else {
		// Browser globals
		factory(jQuery);
	}
}(function ($) {
    function Comments(options, item_id, sub_item_id) {
        this.options = options;
        this.item_id = item_id;
        if(typeof item_id === 'undefined' || ! item_id) {
            console.log('Comment class error: No item_id');
        }
        this.sub_item_id = sub_item_id;
    }

    Comments.prototype.run = function() {
        if(typeof this.item_id  === 'undefined' || ! this.item_id ) {
            return;
        }
        var comments_wrapper = this.generateHtml();
        this.setEventListeners();
        return comments_wrapper;
    }

    Comments.prototype.setEventListeners = function() {
        var self = this;
        
        $("#add_comment_" + this.sub_item_id).die().live('click', function() {
            
            var comment_obj = self.options.comment_obj;
            comment_obj.parent_id = 0;

            var comment_template = '<hr>';

            comment_template += self.createCommentTemplate(comment_obj);

            $(".comments_wrapper_" + self.sub_item_id).append(comment_template);

        });

        $("#save_comment_" + this.sub_item_id).die().live('click', function() {
            var comment_wrapper = $(this).closest("table").parent();
            var id = comment_wrapper.attr("data-id");
            var comment_text = $(this).closest("table").find("textarea.comment_textarea").val();

            var parent_id = $(this).attr('data-parent_id') || '0';

            if(comment_text == '') return;
            var date = $(this).closest("table").find(".comment_date").text();
            var time = $(this).closest("table").find(".comment_time").text();
            var created = date + ' ' + time;
            var obj = {'id' : id, 'parent_id' : parent_id,  'comment' : comment_text, 'item_id' : self.item_id, 'sub_item_id' : self.sub_item_id, 'created' : created};
            self.savePlanComment(obj, function(output){
                var comment_obj = output;
                var comment_html = self.createCommentTemplate(comment_obj);
                comment_wrapper.replaceWith(comment_html);

                // send comment email
                if(self.options.anable_comment_email !== 'undefined' && self.options.anable_comment_email == true) {
                    self.commentEmail(comment_obj);
                }

           });
        });

        $("#delete_comment_" + this.sub_item_id).die().live('click', function(){
            var comment_wrapper = $(this).closest("table").parent();

            $(this).closest("table").parent().prev("hr").remove();

            //console.log($(this).closest("table").parent().prev("hr"));

            var id = comment_wrapper.attr('data-id');

            self.deletePlanComment(id, function(output) {
                if(output) {
                    comment_wrapper.remove();
                }
            });
        });

        $("#reply_comment_" + this.sub_item_id).die().live('click', function() {
            var parent_id = $(this).attr('data-id');
            var comment_obj = self.options.comment_obj;
            comment_obj.parent_id = parent_id;
            var comment_template = self.createCommentTemplate(comment_obj);

            var items = parseInt($(".comments_wrapper_" + self.sub_item_id + ' .comment_wrapper[data-parent_id="' + parent_id + '"]').length);

            //  after last reply item
            if(parent_id && (items != 0)) {
                $(".comments_wrapper_" + self.sub_item_id + ' .comment_wrapper[data-parent_id="' + parent_id + '"]').last().after(comment_template);
            } 

            // if no reply items yet
            if (items == 0) {
                $(".comments_wrapper_" + self.sub_item_id + ' .comment_wrapper[data-id="' + parent_id + '"]').last().after(comment_template);
            }


        });


        this.populatePlanComments(function(comments) {
            if(!comments) return;
            var html = '';
            comments.each(function(comment_obj){

                if(parseInt(comment_obj.parent_id) == 0) {
                    html += '<hr>';
                }
                html += self.createCommentTemplate(comment_obj);
            });
            
            $(".comments_wrapper_" + self.sub_item_id).empty();

            $(".comments_wrapper_" + self.sub_item_id).append(html);

        });

    }


    Comments.prototype.generateHtml = function() {
        var html = '<h5>QUESTIONS / COMMENTS / INSTRUCTIONS</h5>';
        html += '<br/>';
        html += '<div style="position:relative;" class="comments_wrapper_' + this.sub_item_id + '">';
        html += '</div>';
        return html;
    }

    Comments.prototype.createCommentTemplate = function(comment_obj) {
        var d1 = new Date();
        if(comment_obj.created) {
            d1 = new Date(Date.parse(comment_obj.created));
        }
        var parent_id = parseInt(comment_obj.parent_id);


        var current_time = this.getCurrentDate(d1);

        var comment_template = '';

        comment_template += '<div data-id="' + comment_obj.id + '"  data-parent_id="' + parent_id+ '" class="comment_wrapper">';


        comment_template += '<table width="100%">';
        comment_template += '<tr>';

        comment_template += '<td>';
        comment_template += '</td>';

        comment_template += '<td width="33%"><b>Comment by: </b><span class="comment_by">' + comment_obj.user_name +  '</span></td>';
        comment_template += '<td width="33%"><b>Date: </b> <span class="comment_date">' + current_time.date +  '</span></td>';
        comment_template += '<td><b>Time: </b> <span class="comment_time">' + current_time.time_short +  '</span></td>';

        if(!comment_obj.id) {
            comment_template += '<td><input data-parent_id="' + comment_obj.parent_id + '" id="save_comment_' + this.sub_item_id + '" class="save_comment" type="button"  value="Save"></td>'
        }

        if(!this.options.read_only) {
            comment_template += '<td align="center"><a href="javascript:void(0)" class="delete_comment" id="delete_comment_' + this.sub_item_id + '" title="delete"></a></td>';
        }

        var anable_readonly = '';
        if(comment_obj.id) {
            anable_readonly = 'readonly';
        }

        comment_template += '</tr>';
        comment_template += '<tr>';

        comment_template += '<td>';
        if(parent_id) {
             comment_template += '<div class="chat_image"></div>';
        }
        comment_template += '</td>';


        comment_template += '<td colspan="5"><textarea ' + anable_readonly + '  class="comment_textarea" cols="100" rows="3">' + comment_obj.comment +  '</textarea></td>';
        comment_template += '</tr>';
        comment_template += '</table>';

        if(comment_obj.id && !parent_id) {
            comment_template += '<input style="margin-left:7px;" data-id="' + comment_obj.id + '" id="reply_comment_' + this.sub_item_id + '" class="reply_comment" type="button"  value="Reply">'

        }


        comment_template += '<div class="clr"></div>';
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
                alert("error saveComment");
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
                alert("error deleteComment");
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
                    alert(response.status.message);
                    return;
                }
                handleData(response.comments);
            },
            error: function(XMLHttpRequest, textStatus, errorThrown)
            {
                alert("error populateComments");
            }
        }); 
    }


    Comments.prototype.commentEmail = function(comment_obj) {
        var data = comment_obj;
        var url = this.options.fitness_administration_url;
        var view = '';
        var task = 'ajax_email';
        var table = '';

        data.view = 'Comment';
        data.method = this.options.comment_method;
        data.table = this.options.db_table;


        $.AjaxCall(data, url, view, task, table, function(output){
            var emails = output.split(',');
            var message = 'Emails were sent to: ' +  "</br>";
            $.each(emails, function(index, email) { 
                message += email +  "</br>";
            });
            $("#emais_sended").append(message);
        });
    }



    // Add the  function to the top level of the jQuery object
    $.comments = function(options, item_id, sub_item_id) {

        var constr = new Comments(options, item_id, sub_item_id);

        return constr;
    };

}));
