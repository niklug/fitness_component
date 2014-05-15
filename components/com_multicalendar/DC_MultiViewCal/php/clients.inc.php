<script type="text/javascript">  
$(document).ready(function() {
        var DATA_FEED_URL = "<?php echo $datafeed?>&calid=<?php echo $_GET["calid"]?>";
        


    });  

</script>  
<?php if (isset($event->id)) { ?>
    <div id="clients_wrapper">
        <hr>
        <table border="0">
            <tbody id="clients_html">

            </tbody>
        </table>
        <br/>
        <a id="add_client_button" href="javascript:void(0)">[ADD CLIENT]</a>
    </div>
<?php }   ?>
