<table border="0">
    <tbody>
        <tr>
            <td>
                <table border="0"  style="margin-right:25px;">
                    <tbody>
                        <tr>
                            <td>Appointment:</td>
                            <td>
                                <?php
                                if (isset($appointments[0])) {
                                    echo '<select style="float:left;" id="Subject" name="Subject" class="required safe inputtext" ">';
                                    for ($i = 0; $i < count($appointments[0]); $i++) {
                                        echo '<option data-catid="' . $appointments[2][$i] . '" id="' . $appointments[1][$i] . '" value="' . ($appointments[0][$i]) . '" ' . ((isset($event) && (trim($event->title) == trim($appointments[0][$i]))) ? "selected" : "") . '>' . $appointments[0][$i] . '</option>';
                                    }
                                    echo '</select>';
                                }

                                ?>  
                            </td>
                        </tr>
                        <?php
                        if (isset($event->status)) {
                        ?>
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
                        <?php
                        }
                        ?>
                    </tbody>
                </table>
            </td>

            <?php
                if (isset($event->status)) {
            ?>
            <td>
                <table border="0">
                    <tbody>
                        <tr>
                            <td>Client:</td>
                            <td>
                                <?php
                                if (isset($clients[0]->name)) {
                                    echo '<select style="float:left;" id="client" name="client_id" class="required safe inputtext" ">';
                                    echo '<option> -Select-</option>';
                                    for ($i = 0; $i < count( $clients); $i++) {
                                        echo '<option " id="' .  $clients[$i]->user_id . '" value="' . ( $clients[$i]->user_id) . '" ' . ((isset($event) && (trim($event->client_id) == trim( $clients[$i]->user_id))) ? "selected" : "") . '>' .  $clients[$i]->name . '</option>';
                                    }
                                    echo '</select>';
                                }

                                ?>  
                            </td>
                        </tr>
                        <tr>
                            <td>Trainer:</td>
                            <td>
                                <select  id="trainer" name="trainer_id" class="required safe inputtext" ></select>
                            </td>
                        </tr>
                        <tr>
                            <td>Location:</td>
                            <td> <?php
                                if (isset($dc_locations)) {
                                    echo '<select  id="Location" name="Location" class="required safe inputtext" >';
                                    for ($i = 0; $i < count($dc_locations); $i++) {
                                        echo '<option value="' . ($dc_locations[$i]) . '" ' . ((isset($event) && ($event->location == trim($dc_locations[$i]))) ? "selected" : "") . '>' . $dc_locations[$i] . '</option>';
                                    }
                                    echo '</select>';
                                }

                                ?>  </td>
                        </tr>
                    </tbody>
                </table>
            </td>
            <?php
                }
            ?>
        </tr>
    </tbody>
  </table>

<input id="colorvalue" name="colorvalue" type="hidden" value="<?php echo isset($event)?$event->color:"" ?>" />
<input type="hidden" id="rrule" name="rrule" value="<?php echo $event->rrule?>" size=55 />
<input type="hidden" id="rruleType" name="rruleType" value="" size=55 />