<script type="text/javascript">  
$(document).ready(function() {
    var DATA_FEED_URL = "<?php echo $datafeed?>&calid=<?php echo $_GET["calid"]?>";
    
    $("#email_button").on('click', function() {
        var event_id = '<?php echo $event->id; ?>';
        var url = DATA_FEED_URL+ "&method=send_appointment_email";
        $.ajax({
            type : "POST",
            url : url,
            data : {
               event_id : event_id
            },
            dataType : 'text',
            success : function(response) {
                alert(response);
            },
            error: function(XMLHttpRequest, textStatus, errorThrown)
            {
                alert("error");
            }
        });
    });
    
    $("#pdf_button").on('click', function() {
        var htmlPage = '<?php echo JURI::base() ?>index.php?option=com_multicalendar&view=pdf&tpml=component&event_id=<?php echo $event->id?>';
        printPage(htmlPage);
    });
    
    function printPage(htmlPage)
        {
            var w = window.open(htmlPage);
            w.print();
        }
});  

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
                if (isset($event->status)) {
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