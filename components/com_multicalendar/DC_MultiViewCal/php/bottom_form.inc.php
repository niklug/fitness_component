          <input id="timezone" name="timezone" type="hidden" value="" />
          <br /> 
          
          <a href="#" id="savebtn">Save</a>
          <?php if(isset($event) && (JRequest::getVar("delete")=="1")){ ?>
        <a href="#" id="deletebtn">Delete</a>
        <?php } ?>  
          <a href="#" id="closebtn">Close</a>
          <?php
            if (isset($event->status)) {
          ?>
            
          <label class="checkp">
              <input id="frontend_published" name="frontend_published" type="checkbox" value="1" <?php if (isset($event) && $event->frontend_published != "0") {
              echo "checked";
          } ?>/><span class="inl">Published</span>
          </label> 
          <label class="checkp">
              <input id="repeatcheckbox" name="repeatcheckbox" type="checkbox" value="1" <?php if (isset($event) && $event->rrule != "") {
              echo "checked";
          } ?>/><span class="inl"><span id="repeat1" class="inl">Repeat</span>: <span id="repeatspan" class="inl"></span> <a href="#" id="repeatanchor">Edit</a></span>
          </label> 
           <br /> 
          <?php
            }
          ?>
           
      </form>  
    </div>  
    <div id="repeatsave">
        <h2 id="rsh2">Edit recurring event</h2>
        <p id="rsp1">Would you like to change only this event, all events in the series, or this and all following events in the series?</p>
        <div style="clear:both"><a href="#" id="r_save_one" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only ui-state-focus">Only this event</a> <span id="rss1">All other events in the series will remain the same.</span></div>
        <div style="clear:both"><a href="#" id="r_save_following" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only ui-state-focus">Following events</a> <span id="rss2">This and all the following events will be changed.</span><br />
        <span id="rss3">Any changes to future events will be lost.</span></div>
        <div style="clear:both"><a href="#" id="r_save_all" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only ui-state-focus">All events</a> <span id="rss4">All events in the series will be changed.</span><br />
        <span id="rss5">Any changes made to other events will be kept.</span></div>
        <div style="clear:both;float:right"><a href="#" id="r_save_cancel" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only ui-state-focus">Cancel this change</a></div>
        <div style="clear:both"></div>
    </div>  
    <div id="repeatdelete">
        <h2 id="rdh2">Delete recurring event</h2>
        <p id="rdp1">Would you like to delete only this event, all events in the series, or this and all future events in the series?</p>
        <div style="clear:both"><a href="#" id="r_delete_one" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only ui-state-focus">Only this instance</a> <span id="rds1">All other events in the series will remain.</span></div>
        <div style="clear:both"><a href="#" id="r_delete_following" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only ui-state-focus">All following</a> <span id="rds2">This and all the following events will be deleted.</span></div>
        <div style="clear:both"><a href="#" id="r_delete_all" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only ui-state-focus">All events in the series</a> <span id="rds3">All events in the series will be deleted.</span></div>
        <div style="clear:both;float:right"><a href="#" id="r_delete_cancel" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only ui-state-focus">Cancel this change</a></div>
        <div style="clear:both"></div>
    </div>  
    <div id="repeat">  
        <div>  
            <label id="rl1">Repeats</label>
            <select id="freq">
                <option id="opt0" value="0">Daily</option>
                <option id="opt1" value="1">Every weekday (Monday to Friday)</option>
                <option id="opt2" value="2">Every Monday, Wednesday, and Friday</option>
                <option id="opt3" value="3">Every Tuesday, and Thursday</option>
                <option id="opt4" value="4">Weekly</option>
                <option id="opt5" value="5">Monthly</option>
                <option id="opt6" value="6">Yearly</option>
            </select>  
        </div>  
        <div id="intervaldiv">
            <label id="rl2">Repeat every:</label>
            <select id="interval"></select> <span id="interval_label">weeks</span>
        </div>  
        <div id="bydayweek">
            <label id="rl3">Repeat on:</label>
            <input id="bydaySU" class="bydayw" name="SU" type="checkbox"><span id="chk0">SU</span>
            <input id="bydayMO" class="bydayw" name="MO" type="checkbox"><span id="chk1">MO</span>
            <input id="bydayTU" class="bydayw" name="TU" type="checkbox"><span id="chk2">TU</span>
            <input id="bydayWE" class="bydayw" name="WE" type="checkbox"><span id="chk3">WE</span>
            <input id="bydayTH" class="bydayw" name="TH" type="checkbox"><span id="chk4">TH</span>
            <input id="bydayFR" class="bydayw" name="FR" type="checkbox"><span id="chk5">FR</span>
            <input id="bydaySA" class="bydayw" name="SA" type="checkbox"><span id="chk6">SA</span>
        </div>  
        <div id="bydaymonth">
            <label id="rl4">Repeat by:</label>
            <input id="byday_m" class="bydaym" name="bydaym" type="radio" value="1" checked="checked"> <span id="bydaymonth1">day of the month</span>
            <input id="byday_w" class="bydaym" name="bydaym" type="radio" value="2"> <span id="bydaymonth2">day of the week</span>
        </div>  
        <div>  
            <label id="rl5">Starts on:</label>
            <label id="starts"><?php echo $stpartdate; ?></label>
        </div>  
        <div class="clear"></div>
        <div>  
            <label id="rl6">Ends:</label>
            <div class="fl">
                <div><input id="end_never" name="end" checked="" title="Ends never" type="radio"> <span id="end1">Never</span></div>
                <div><input id="end_count" name="end" title="Ends after a number of occurrences" type="radio"> <span id="end21">After</span> <select id="end_after"></select> <span id="end22">occurrences</span></div>
                <div><input id="end_until" name="end" title="Ends on a specified date" type="radio"> <span id="end3">On</span> <input size="10" id="end_until_input" value="5/14/2013"></div>
            </div>  
        </div>  
        <div class="clear"></div>
        <div>  
            <label id="rl7">Summary:</label>
            <span id="summary"></span>
        </div> 
        
        <input type="hidden" id="format" value="" size=55 />
        <a href="#" id="savebtnRepeat">Save</a>
        <a href="#" id="closebtnRepeat">Close</a>
        <br />  
        <br />  
    </div>  
      <div class="event_status_wrapper">
          <img class="hideimage " src="<?php echo JUri::base() ?>administrator/components/com_fitness/assets/images/close.png" alt="close" title="close" onclick="hide_event_status_wrapper()">
              <a data-status="1" class="set_status event_status_pending event_status__button" href="javascript:void(0)">pending</a>
              <a data-status="2" class="set_status event_status_attended event_status__button" href="javascript:void(0)">attended</a>
              <a data-status="3" class="set_status event_status_cancelled event_status__button" href="javascript:void(0)">cancelled</a>
              <a data-status="4" class="set_status event_status_latecancel event_status__button" href="javascript:void(0)">late cancel</a>
              <a data-status="5" class="set_status event_status_noshow event_status__button" href="javascript:void(0)">no show</a>
      </div>
    <a id="bbit-cs-editLink" href=""></a>  
    
    
    
    <div class="entry-form">
    <form name="exercise_fields" id="exercise_fields"> 
        <table width="100%" border="0" cellpadding="4" cellspacing="0">
            <tr>
                <td colspan="2" align="right"><img id="close_add_exercise_box" class="hideimage " src="<?php echo JUri::base() ?>administrator/components/com_fitness/assets/images/close.png" alt="close" title="close"></td>
            </tr>
            <tr>
                <td>Title</td>
                <td><input  maxlength="255" type="text" name="title"></td>
            </tr>
            <tr>
                <td>Speed</td>
                <td><input maxlength="10" type="text" name="speed"></td>
            </tr>
            <tr>
                <td>Weight</td>
                <td><input maxlength="10" type="text" name="weight"></td>
            </tr>
            <tr>
                <td>Reps</td>
                <td><input maxlength="255" type="text" name="reps"></td>
            </tr>
            <tr>
                <td>Time</td>
                <td><input maxlength="20" type="text" name="time"></td>
            </tr>
            <tr>
                <td>Sets</td>
                <td><input maxlength="255" type="text" name="sets"></td>
            </tr>
            <tr>
                <td>Rest</td>
                <td><input maxlength="255" type="text" name="rest"></td>
            </tr>
            <tr>
                <td align="right"></td>
                <td><input type="button" value="Save" id="save_exercise"><input type="button" value="Cancel" id="cancel_exercise"></td>
            </tr>
         </table>
        <input  type="hidden" name="event_id" value="<?php echo $event->id;?>">
       </form>
    </div>
  </body>  
</html>  
