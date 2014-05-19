<div id="comments_wrapper" style="display:none;">
    <hr>  
    <label>  
        <span>Appointment Details</span>
        <textarea cols="20" id="trainer_comments" name="comments" rows="2" >
        <?php echo isset($event)?$event->comments:""; ?>
        </textarea>  
     </label>
    <hr>
</div>