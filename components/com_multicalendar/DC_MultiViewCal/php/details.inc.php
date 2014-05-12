<script type="text/javascript">  
    $(document).ready(function() {
        $("#email_button").on('click', function() {
            var event_id = '<?php echo $event->id; ?>';
            sendSessionDetailsEmail(event_id, 'Workout');
        });

        $("#pdf_button").on('click', function() {
            var htmlPage = '<?php echo JURI::base() ?>index.php?option=com_multicalendar&view=pdf&tpml=component&layout=email_pdf_workout&event_id=<?php echo $event->id?>&client_id=<?php echo JRequest::getVar( 'cid' )?>';
            printPage(htmlPage);
        });
    });  
    
    function sendSessionDetailsEmail(id, method) {
        var data = {};
        var url = '<?php echo JURI::base();?>index.php?option=com_fitness&tmpl=component&<?php echo JSession::getFormToken(); ?>=1';
        var view = '';
        var task = 'ajax_email';
        var table = '';

        data.id = id;
        data.view = 'Programs';
        data.method = method;


        $.AjaxCall(data, url, view, task, table, function(output){
            var emails = output.split(',');
            var message = 'Emails were sent to: ' +  "</br>";
            $.each(emails, function(index, email) { 
                message += email +  "</br>";
            });
            $("#emais_sended").append(message);
        });
    }



    function printPage(htmlPage) {
        var w = window.open(htmlPage);
        setTimeout(function(){w.print()},3000);
        return false
    }

</script> 
<div id="details_wrapper">
    <hr>  
    <table border="0">
        <tbody>
            <tr>
                <td width="80%">
                    <label>  
                        <span id="s_remark1">Details / Instructions</span>
                        <textarea cols="20" id="Description" name="Description" rows="2" >
                        <?php echo isset($event)?$event->description:""; ?>
                        </textarea>  
                     </label>
                </td>
                <?php
                if (isset($event->id)) {
                ?>
                <td width="10%">
                    <a title="Send by Email" href="javascript:void(0)" id="email_button"></a>
                </td>
                <td width="10%">
                    <a title="Save in PDF" href="javascript:void(0)" id="pdf_button"></a>
                </td>
                <?php
                }
                ?>
            </tr>
        </tbody>
    </table>
    <hr>
</div>