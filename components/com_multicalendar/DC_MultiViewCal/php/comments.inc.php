<div id="comments_wrapper">
    <hr>  
    <label>  
        <span>Trainer Feedback / Comments</span>
        <textarea cols="20" id="trainer_comments" name="comments" rows="2" >
        <?php echo isset($event)?$event->comments:""; ?>
        </textarea>  
     </label>
    <hr>
</div>