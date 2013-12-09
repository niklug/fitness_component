/*
 * 
 */
(function($) {
    function BatchStatus(options) {
        this.options = options;
        this.status_class = $.status(options);
    }
    
    
    BatchStatus.prototype.run = function() {
        var html = this.generateHtml();
        $(this.options.target_element).html(html);
        this.setEventListeners();
    }
    
    
    BatchStatus.prototype.setEventListeners = function() {
        var self = this;
        var batch_select =  $("#batch_process_select");

        $("#batch_process_button_run").die().live('click', function() {
            batch_select.removeClass("red_style_border");
            var status = batch_select.val();
            if(!parseInt(status)) {
                batch_select.addClass("red_style_border");
                return false;
            }
            var ids = $('input[type="checkbox"][name="cid\\[\\]"]:checked').map(function() { return this.value; }).get();
            //console.log(ids);
            self.processing(ids, status);
        });
    }
        
        
    BatchStatus.prototype.generateHtml = function() {
        var html = '';
        html += '<div>';
        html += this.options.title;
        html += '<div class="clr"></div>';
        
        html += '<select class="" name="batch_process_select" id="batch_process_select">';
        html += '<option  value="0"> -Select-</option>';
        $.each(this.options.statuses, function(item, value) {
            html += '<option  value="' + item + '">' +  value.label + '</option>';
        });
        html += '</select>';
        
        html += '<div class="clr"></div>';
        
        html += '<input type="checkbox" id="send_email_batch_process" />';
        
        html += '<div style="padding-top:4px;">' + this.options.email_checkbox_title + '</div>';
        
        html += '<div class="clr"></div>';
        
        html += '<input type="button" id="batch_process_button_run" value="Process" />';
        
        html += '</div>';
        return html;
    }
    
    BatchStatus.prototype.processing = function(ids, status) {
        this.status_class.options = this.options;
        
        var send_email_batch_process = $("#send_email_batch_process").is(':checked');
            
        this.status_class.options.send_email_batch_process = send_email_batch_process;
        
        var self = this;
        
        $.each(ids, function(item, value) {
            self.status_class.setStatus(value, status);
        });
        
        $("#send_email_batch_process").prop('checked', false);
        $('input[type="checkbox"][name="cid\\[\\]"]:checked').prop('checked', false);
        $('input[type="checkbox"][name="checkall-toggle"]:checked').prop('checked', false);
        
    }

    
    // Add the  function to the top level of the jQuery object
    $.batch_status = function(options) {

        var constr = new BatchStatus(options);

        return constr;
    };
        
})(jQuery);



