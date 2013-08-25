/*
 * class provide comments system
 */
function NutritionComment(options, nutrition_plan_id, meal_id) {
    this.options = options;
    this.nutrition_plan_id = nutrition_plan_id;
    this.meal_id = meal_id;
}

NutritionComment.prototype.run = function() {
    var comments_wrapper = this.generateHtml();
    this.setEventListeners();
    return comments_wrapper;
}

NutritionComment.prototype.setEventListeners = function() {
    var self = this;
    $("#add_comment_" + this.meal_id).live('click', function() {
        var comment_template = self.createCommentTemplate(self.options.comment_obj);
        $("#comments_wrapper_" + self.meal_id).append(comment_template);
    });

    $("#save_comment_" + this.meal_id).live('click', function() {
        var comment_wrapper = $(this).closest("table").parent();
        var id = comment_wrapper.attr("data-id");
        var comment_text = $(this).closest("table").find("textarea.comment_textarea").val();
        var date = $(this).closest("table").find(".comment_date").text();
        var time = $(this).closest("table").find(".comment_time").text();
        var created = date + ' ' + time;
        var obj = {'id' : id, 'comment' : comment_text, 'nutrition_plan_id' : self.nutrition_plan_id, 'meal_id' : self.meal_id, 'created' : created};
        self.savePlanComment(obj, function(output){
            var comment_obj = output;
            var comment_html = self.createCommentTemplate(comment_obj);
            comment_wrapper.replaceWith(comment_html);
            //console.log(comment_obj);
       });
    });

    $("#delete_comment_" + this.meal_id).live('click', function(){
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
        $("#comments_wrapper_" + self.meal_id).append(html);

    });

}

NutritionComment.prototype.generateHtml = function() {
    var html = 'QUESTIONS / COMMENTS / INSTRUCTIONS';
    html += '<div id="comments_wrapper_' + this.meal_id + '">';
    html += '</div>';
    return html;
}

NutritionComment.prototype.createCommentTemplate = function(comment_obj) {
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
    comment_template += '<td><input id="save_comment_' + this.meal_id + '" class="save_comment" type="button"  value="Save"></td>'
    comment_template += '<td align="center"><a href="javascript:void(0)" class="delete_comment" id="delete_comment_' + this.meal_id + '" title="delete"></a></td>';
    comment_template += '</tr>';
    comment_template += '<tr>';
    comment_template += '<td colspan="5"><textarea  class="comment_textarea" cols="100" rows="3">' + comment_obj.comment +  '</textarea></td>';
    comment_template += '</tr>';
    comment_template += '</table>';
    comment_template += '</div>';
    return comment_template;
}


NutritionComment.prototype.getCurrentDate = function(d1) {
    var date = d1.getFullYear() + "-" + (this.pad(d1.getMonth()+1)) + "-" + this.pad(d1.getDate()); 
    var time = this.pad(d1.getHours()) + ":" + this.pad(d1.getMinutes()) + ":" + this.pad(d1.getSeconds());
    var time_short = this.pad(d1.getHours()) + ":" + this.pad(d1.getMinutes());
    return {'date' : date, 'time' : time, 'time_short' : time_short};
}

NutritionComment.prototype.pad = function pad(d) {
    return (d < 10) ? '0' + d.toString() : d.toString();
}

NutritionComment.prototype.savePlanComment = function(o, handleData) {
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
            data_encoded : data_encoded
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
            alert("error savePlanComment");
        }
    }); 
}



NutritionComment.prototype.deletePlanComment = function(id, handleData) {
    var url = this.options.fitness_administration_url;
    $.ajax({
        type : "POST",
        url : url,
        data : {
            view : 'nutrition_plan',
            format : 'text',
            task : 'deletePlanComment',
            id : id
          },
        dataType : 'json',
        success : function(response) {
            if(!response.status.IsSuccess) {
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


NutritionComment.prototype.populatePlanComments = function(handleData) {
    var url = this.options.fitness_administration_url;
    var nutrition_plan_id = this.options.nutrition_plan_id;
    var meal_id = this.meal_id;
    $.ajax({
        type : "POST",
        url : url,
        data : {
            view : 'nutrition_plan',
            format : 'text',
            task : 'populatePlanComments',
            nutrition_plan_id : nutrition_plan_id,
            meal_id : meal_id
        },
        dataType : 'json',
        success : function(response) {
            if(!response.status.IsSuccess) {
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
