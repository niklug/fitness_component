<table border="0">
    <tbody>
        <tr>
            <td>
                <table border="0"  style="margin-right:17px;">
                    <tbody>
                        <tr><td colspan="2"></td></tr>
                        <tr>
                            <td>Appointment:</td>
                            <td>
                                <?php
                                if (isset($appointments[0])) {
                                    echo '<select style="float:left;" id="Subject" name="Subject" class="required safe inputtext" ">';
                                    echo '<option  value="" >-Select-</option>';
  
                                    foreach ($appointments as $appointment) {
                                        echo '<option data-catid="' . $appointment->id . '" id="' . $appointment->color . '" value="' . ($appointment->id) . '" ' . ((isset($event) && trim($event->title == $appointment->id )) ? "selected" : "") . '>' . $appointment->name . '</option>';
                                    }
                                    
                                    
                                    echo '</select>';
                                }

                                ?>  
                            </td>
                        </tr>
                       <tr>
                            <td>Session Type:</td>
                            <td> 
                                <select  id="session_type" name="session_type" class="required safe inputtext" ></select> 
                            </td>
                        </tr>
                        <tr>
                            <td>Session Focus:</td>
                            <td> 
                                <select  id="session_focus" name="session_focus" class="required safe inputtext" ></select>
                            </td>
                        </tr>
                        <tr>
                            <td>Location:</td>
                            <td> <?php
                                if (isset($dc_locations)) {
                                    echo '<select  id="Location" name="Location" class="required safe inputtext" >';
                                    echo '<option>-Select-</option>';

                                    foreach ($dc_locations as $dc_location) {
                                        echo '<option value="' . ($dc_location->id) . '" ' . ((isset($event) && ($event->location == $dc_location->id)) ? "selected" : "") . '>' . $dc_location->name . '</option>';
                                    }
                                    
                                    
                                    echo '</select>';
                                }

                                ?>  </td>
                        </tr>
                    </tbody>
                </table>
            </td>


            <td style="vertical-align: top;">
                <table border="0">
                    <tbody>
                        
                        <?php if($is_client) { ?>
                            <input name="business_profile_id" id="business_profile_id" type="hidden"  value="<?php echo $business_profile_id; ?>"/>
                            <input name="client_id" id="client_id" type="hidden"  value="<?php echo $user->id; ?>"/>
                        <?php } else { ?>
                            <tr>
                                <td>Business Name:</td>
                                <td>
                                    <?php
                                    echo $helper->generateSelect($helper->getBusinessProfileList($user->id), 'business_profile_id', 'business_profile_id', $event->business_profile_id , '', true, "required safe inputtext"); ?>
                                </td>
                            </tr>
                        <?php } ?>
                        
                        <?php if($event->owner) { ?>
                        <tr>
                            <td>Author:</td>
                            <td>
                                <?php echo JFactory::getUser($event->owner)->name; ?>
                            </td>
                        </tr>
                        <?php } ?>
                        
                        <tr id="trainer_select_tr">
                            <td>Trainer:</td>
                            <td>
                                <select  id="trainer" name="trainer_id" class="required safe inputtext" ></select>
                            </td>
                        </tr>
                       
                    </tbody>
                </table>
            </td>

        </tr>
    </tbody>
  </table>

<input id="colorvalue" name="colorvalue" type="hidden" value="<?php echo isset($event)?$event->color:"" ?>" />
<input type="hidden" id="rrule" name="rrule" value="<?php echo $event->rrule?>" size=55 />
<input type="hidden" id="rruleType" name="rruleType" value="" size=55 />
<input type="hidden" id="cid" name="cid" value="<?php echo JRequest::getVar('cid'); ?>" size=55 />


<script type="text/javascript">
    (function($) {
        var business_profile_id = parseInt('<?php echo $business_profile_id; ?>');
        var user_id = '<?php echo $user->id; ?>';
        var trainer_id = '<?php echo $event->trainer_id; ?>';
        var is_superuser = Boolean('<?php echo $is_superuser; ?>');
        var is_simple_trainer = Boolean('<?php echo $is_simple_trainer; ?>');
        var is_client = Boolean('<?php echo $is_client; ?>');

        
        if(!is_superuser) {
            $("#business_profile_id").val(business_profile_id);
        }
        
        if(is_simple_trainer) {
            trainer_id = user_id;
        }
        
        // connect helper class
        var helper_options = {
            'ajax_call_url' : '<?php echo JURI::root();?>index.php?option=com_fitness&tmpl=component&<?php echo JSession::getFormToken(); ?>=1',
        }
        var fitness_helper = $.fitness_helper(helper_options);
        
        
        //if client logged
        if(is_client) {
            fitness_helper.populateTrainersSelect('#trainer', trainer_id, user_id);
            return;
        }
        
        
        $("#business_profile_id").die().on('change', function() {
            var business_profile_id = $(this).val();
            businessLogic(business_profile_id);
        });
        
        
        function businessLogic(business_profile_id) {

            if(!parseInt(business_profile_id)) {
                return;
            }
            
            fitness_helper.populateTrainersSelectOnBusiness('goals_periods', business_profile_id, '#trainer', trainer_id, user_id);
        }
        
        
        var event_business_profile_id = parseInt('<?php echo $event->business_profile_id;?>');
        
        
    
        if(!event_business_profile_id) {
            event_business_profile_id = business_profile_id;
        }
        
        if(event_business_profile_id) {
            $("#business_profile_id").val(event_business_profile_id);
            businessLogic(event_business_profile_id);
        }
        
    })($);


</script>






