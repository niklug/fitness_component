<script type="text/javascript">  
$(document).ready(function() {

});  

</script>  

<div id="assessment_wrapper" style="display:none;">
    <hr>
    <table border="0" width="100%">
        <tbody>
            <tr>
                <td>
                    <h4>Assessment Summary</h4>
                    <table width="100%"  border="0">
                        <tbody>
                            <tr>
                                <td >Height</td>
                                <td>
                                    <input class="assessment_input required number" min="1" maxlength="7" type="text" name="as_height" value="<?php echo $event->as_height?>" id="as_height">
                                </td>
                                <td>cm</td>
                            </tr>
                            <tr>
                                <td>Weight</td>
                                <td>
                                    <input class="assessment_input required number" min="1" maxlength="7" type="text" name="as_weight" value="<?php echo $event->as_weight?>" id="as_weight">
                                </td>
                                <td>kg</td>
                            </tr>
                            <tr>
                                <td>Age</td>
                                <td>
                                    <input class="assessment_input required digits" min="1" maxlength="2" type="text" name="as_age" value="<?php echo $event->as_age?>" id="as_age">
                                </td>
                                <td>years</td>
                            </tr>
                            <tr>
                                <td>Body Fat</td>
                                <td>
                                    <input class="assessment_input required number" min="1" maxlength="5" type="text" name="as_body_fat" value="<?php echo $event->as_body_fat?>" id="as_body_fat">
                                </td>
                                <td>%</td>
                            </tr>
                            <tr>
                                <td>Lean Mass</td>
                                <td><input class="assessment_input required number" min="1" maxlength="5" type="text" name="as_lean_mass" value="<?php echo $event->as_lean_mass?>" id="as_lean_mass"></td>
                                <td>kg</td>
                            </tr>
                        </tbody>
                    </table>
                    Trainer Comments</br>
                    <textarea style="height: 60px; width: 260px;" cols="35"  name="as_comments" rows="3" ><?php echo $event->as_comments?></textarea> 
 
                </td>
                <td>
                    <h4>Health Assessment</h4>
                    <table  width="100%"  border="0">
                        <tbody>
                            <tr>
                                <td >Blood Pressure</td>
                                <td >
                                    <input class="assessment_input  "  maxlength="10" type="text" name="ha_blood_pressure" value="<?php echo $event->ha_blood_pressure?>">
                                </td>
                                <td>mm/Hg</td>
                            </tr>
                            <tr>
                                <td>Body Mass Index</td>
                                <td>
                                    <input class="assessment_input number" maxlength="7" type="text" name="ha_body_mass_index" value="<?php echo $event->ha_body_mass_index ? $event->ha_body_mass_index : ''?>">
                                </td>
                                <td>kg</td>
                            </tr>
                            <tr>
                                <td>Sit & Reach</td>
                                <td>
                                    <input class="assessment_input number " maxlength="5" type="text" name="ha_sit_reach" value="<?php echo $event->ha_sit_reach ? $event->ha_sit_reach : ''?>">
                                </td>
                                <td>cm</td>
                            </tr>
                            <tr>
                                <td>Lung Function</td>
                                <td>
                                    <input class="assessment_input number" maxlength="5" type="text" name="ha_lung_function" value="<?php echo $event->ha_lung_function ? $event->ha_lung_function : ''?>">
                                </td>
                                <td>ml</td>
                            </tr>
                            <tr>
                                <td>Aerobic Fitness</td>
                                <td><input class="assessment_input " maxlength="10" type="text" name="ha_aerobic_fitness" value="<?php echo $event->ha_aerobic_fitness?>"></td>
                                <td>V02MAX</td>
                            </tr>
                        </tbody>
                    </table>
                    Trainer Comments</br>
                    <textarea style="height: 60px; width: 260px;"  cols="35"  name="ha_comments" rows="3" ><?php echo $event->ha_comments?></textarea> 
                </td>
            </tr>
            <tr>
                <td>
                    <h4>Anatomical Measurements</h4>
                    <table border="0">
                        <tbody>
                            <tr>
                                <td>Height</td>
                                <td>
                                    <input class="assessment_input number"  maxlength="7" type="text" name="am_height" value="<?php echo $event->am_height ? $event->am_height : ''?>">
                                </td>
                                <td width="30px">cm</td>
                                <td>Bicep L</td>
                                <td>
                                    <input class="assessment_input number"  maxlength="7" type="text" name="am_bicep_l" value="<?php echo $event->am_bicep_l ?  $event->am_bicep_l : ''?>">
                                </td>
                                <td>cm</td>
                            </tr>
                            <tr>
                                <td>Weight</td>
                                <td>
                                    <input class="assessment_input number"  maxlength="7" type="text" name="am_weight" value="<?php echo $event->am_weight ? $event->am_weight : ''?>">
                                </td>
                                <td>kg</td>
                                <td>Thigh R</td>
                                <td>
                                    <input class="assessment_input number"  maxlength="7" type="text" name="am_thigh_r" value="<?php echo $event->am_thigh_r ? $event->am_thigh_r : ''?>">
                                </td>
                                <td>cm</td>
                            </tr>
                            <tr>
                                <td>Waist</td>
                                <td>
                                    <input class="assessment_input number"  maxlength="7" type="text" name="am_waist" value="<?php echo $event->am_waist ? $event->am_waist : ''?>">
                                </td>
                                <td>cm</td>
                                <td>Thigh L</td>
                                <td>
                                    <input class="assessment_input number"  maxlength="7" type="text" name="am_thigh_l" value="<?php echo $event->am_thigh_l ? $event->am_thigh_l : ''?>">
                                <td>cm</td>
                            </tr>
                            <tr>
                                <td>Hips</td>
                                <td>
                                     <input class="assessment_input number"  maxlength="7" type="text" name="am_hips" value="<?php echo $event->am_hips ? $event->am_hips : ''?>">
                                </td>
                                <td>cm</td>
                                <td>Calf R</td>
                                <td>
                                    <input class="assessment_input number"  maxlength="7" type="text" name="am_calf_r" value="<?php echo $event->am_calf_r ? $event->am_calf_r : ''?>">
                                </td>
                                <td>cm</td>
                            </tr>
                            <tr>
                                <td>Chest</td>
                                <td>
                                    <input class="assessment_input number"  maxlength="7" type="text" name="am_chest" value="<?php echo $event->am_chest ? $event->am_chest : ''?>">
                                </td>
                                <td>cm</td>
                                <td>Calf L</td>
                                <td>
                                    <input class="assessment_input number"  maxlength="7" type="text" name="am_calf_l" value="<?php echo $event->am_calf_l ? $event->am_calf_l : ''?>">
                                </td>
                                <td>cm</td>
                            </tr>
                            <tr>
                                <td>Bicep R</td>
                                <td>
                                    <input class="assessment_input number"  maxlength="7" type="text" name="am_bicep_r" value="<?php echo $event->am_bicep_r ?  $event->am_bicep_r : ''?>">
                                </td>
                                <td>cm</td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                        </tbody>
                    </table>
                    Trainer Comments</br>
                    <textarea style="height: 60px; width: 260px;"  cols="35"  name="am_comments" rows="3" ><?php echo $event->am_comments?></textarea> 
                </td>
                <td>
                    <h4>Bio-Impedience Analysis</h4>
                    <table  width="100%"  border="0">
                        <tbody>
                            <tr>
                                <td>Body Fat</td>
                                <td>
                                    <input class="assessment_input  "  maxlength="5" type="text" name="bia_body_fat" value="<?php echo $event->bia_body_fat ? $event->bia_body_fat : ''?>">
                                </td>
                                <td>%</td>
                            </tr>
                            <tr>
                                <td>Body Water</td>
                                <td>
                                    <input class="assessment_input number" maxlength="5" type="text" name="bia_body_water" value="<?php echo $event->bia_body_water ? $event->bia_body_water : ''?>">
                                </td>
                                <td>%</td>
                            </tr>
                            <tr>
                                <td>Muscle Mass</td>
                                <td>
                                    <input class="assessment_input number " maxlength="7" type="text" name="bia_muscle_mass" value="<?php echo $event->bia_muscle_mass ? $event->bia_muscle_mass : ''?>">
                                </td>
                                <td>kg</td>
                            </tr>
                            <tr>
                                <td>Bone Mass</td>
                                <td>
                                    <input class="assessment_input number" maxlength="7" type="text" name="bia_bone_mass" value="<?php echo $event->bia_bone_mass ? $event->bia_bone_mass : ''?>">
                                </td>
                                <td>kg</td>
                            </tr>
                            <tr>
                                <td>Visceral Fat</td>
                                <td><input class="assessment_input digits"  min="1" max="59" maxlength="2" type="text"  name="bia_visceral_fat" value="<?php echo $event->bia_visceral_fat ? $event->bia_visceral_fat : ''?>"></td>
                                <td>1-59</td>
                            </tr>
                        </tbody>
                    </table>
                    </br>
                    Trainer Comments</br>
                    <textarea  style="height: 60px; width: 260px; margin-top: 8px;"    cols="35"  name="bio_comments" rows="3" ><?php echo $event->bio_comments?></textarea> 
                </td>
            </tr>
            <tr>
                <td >
                    <h4>Bio-Signature Modulation</h4>
                    <table  width="100%" b border="0">
                        <tbody>
                            <tr>
                                <td>Height</td>
                                <td> 
                                    <input class="assessment_input number"  maxlength="7" type="text" name="bsm_height" value="<?php echo $event->bsm_height ? $event->bsm_height : ''?>">
                                </td>
                                <td >cm</td>
                            </tr>
                            <tr>
                                <td>Weight</td>
                                <td>
                                    <input class="assessment_input number"  maxlength="7" type="text" name="bsm_weight" value="<?php echo $event->bsm_weight ? $event->bsm_weight : ''?>">
                                </td>
                                <td>kg</td>
                            </tr>
                            <tr>
                                <td>Chin</td>
                                <td>
                                    <input class="assessment_input number"  maxlength="7" type="text" name="bsm_chin" value="<?php echo $event->bsm_chin ? $event->bsm_chin : ''?>">
                                </td>
                                <td>mm</td>
                            </tr>
                            <tr>
                                <td>Check</td>
                                <td>
                                     <input class="assessment_input number"  maxlength="7" type="text" name="bsm_check" value="<?php echo $event->bsm_check ? $event->bsm_check : ''?>">
                                </td>
                                <td>mm</td>
                               </tr>
                            <tr>
                                <td>Pec</td>
                                <td>
                                    <input class="assessment_input number"  maxlength="7" type="text" name="bsm_pec" value="<?php echo $event->bsm_pec ? $event->bsm_pec : ''?>">
                                </td>
                                <td>mm</td>
                            </tr>
                            <tr>
                                <td>Tricep</td>
                                <td>
                                    <input class="assessment_input number"  maxlength="7" type="text" name="bsm_tricep" value="<?php echo $event->bsm_tricep  ? $event->bsm_tricep : ''?>">
                                </td>
                                <td>mm</td>
                            </tr>
                            <tr>
                                <td>Subscapularis</td>
                                <td>
                                    <input class="assessment_input number"  maxlength="7" type="text" name="bsm_subscapularis" value="<?php echo $event->bsm_subscapularis ? $event->bsm_subscapularis : ''?>">
                                </td>
                                <td>mm</td>
                            <tr>
                                <td>SUM 10</td>
                                <td>
                                    <input class="assessment_input number"  maxlength="7" type="text" name="bsm_sum10" value="<?php echo $event->bsm_sum10 ? $event->bsm_sum10 : ''?>">
                                </td>
                                <td>mm</td>
                            </tr>
                            <tr>
                                <td>SUM 12</td>
                                <td>
                                    <input class="assessment_input number"  maxlength="7" type="text" name="bsm_sum12" value="<?php echo $event->bsm_sum12 ? $event->bsm_sum12 : ''?>">
                                </td>
                                <td>mm</td>
                            </tr>
                            <tr>
                                <td>Midaxillary</td>
                                <td>
                                    <input class="assessment_input number"  maxlength="7" type="text" name="bsm_midaxillary" value="<?php echo $event->bsm_midaxillary ? $event->bsm_midaxillary : ''?>">
                                </td>
                                <td>mm</td>
                            </tr>
                           <tr>
                                <td>Suprailiac</td>
                                <td>
                                    <input class="assessment_input number"  maxlength="7" type="text" name="bsm_supraillac" value="<?php echo $event->bsm_supraillac ? $event->bsm_supraillac : ''?>">
                                </td>
                                <td>mm</td>
                            </tr>
                          </tbody>
                    </table>
      
                </td>
                <td>
      
                    <table width="100%" border="0">
                        <tbody>
                            <tr>
                                <td style="height: 45px;">
                                    
                                </td>
                            </tr>
                            <tr>
                                <td>Umbilical</td>
                                <td>
                                    <input class="assessment_input number"  maxlength="7" type="text" name="bsm_umbilical" value="<?php echo $event->bsm_umbilical ? $event->bsm_umbilical : ''?>">
                                <td>mm</td>
                            </tr>
                            <tr>
                                <td>Knee</td>
                                <td>
                                    <input class="assessment_input number"  maxlength="7" type="text" name="bsm_knee" value="<?php echo $event->bsm_knee ? $event->bsm_knee : ''?>">
                                </td>
                                <td>mm</td>
                            </tr>
                            <tr>
                                <td>Calf</td>
                                <td>
                                    <input class="assessment_input number"  maxlength="7" type="text" name="bsm_calf" value="<?php echo $event->bsm_calf ? $event->bsm_calf : ''?>">
                                </td>
                                <td>mm</td>
                            </tr>
                            <tr>
                                <td>Quadricep</td>
                                <td><input class="assessment_input number"  maxlength="7" type="text" name="bsm_quadricep" value="<?php echo $event->bsm_quadricep ? $event->bsm_quadricep : ''?>"></td>
                                <td>mm</td>
                            </tr>
                            <tr>
                                <td>Hamstring</td>
                                <td>
                                    <input class="assessment_input number"  maxlength="7" type="text" name="bsm_hamstring" value="<?php echo $event->bsm_hamstring ? $event->bsm_hamstring : ''?>">
                                </td>
                                <td>mm</td>
                            </tr>
                            <tr>
                                <td>Body Fat</td>
                                <td>
                                    <input class="assessment_input number"  maxlength="7" type="text" name="bsm_body_fat" value="<?php echo $event->bsm_body_fat ? $event->bsm_body_fat : ''?>">
                                </td>
                                <td>%</td>
                            </tr>
                            <tr>
                                <td>Lean Mass</td>
                                <td>
                                    <input class="assessment_input number"  maxlength="5" type="text" name="bsm_lean_mass" value="<?php echo $event->bsm_lean_mass ? $event->bsm_lean_mass : ''?>">
                                <td>kg</td>
                            </tr>
                          </tbody>
                    </table>
                    <div style="margin-top: 7px;">Trainer Comments</br></div>
                    <textarea style="height: 60px; width: 260px;"  cols="35"  name="bsm_comments" rows="3" ><?php echo $event->bsm_comments?></textarea> 
                 </td>
            </tr>
        </tbody>
    </table>
    <table border="0">
        <tbody>
            <tr>
                <td>
                    Nutrition Protocols</br>
                    <textarea style="height: 70px; width: 176px;"  cols="23"  name="nutrition_protocols" rows="4" ><?php echo $event->nutrition_protocols?></textarea> 
                </td>
                <td>
                    Supplementation Protocols</br>
                    <textarea style="height: 70px; width: 176px;"  cols="23"  name="supplementation_protocols" rows="4" ><?php echo $event->supplementation_protocols?></textarea> 
                </td>
                <td>
                    Training Protocols</br>
                    <textarea style="height: 70px; width: 176px;"  cols="23"  name="training_protocols" rows="4" ><?php echo $event->training_protocols?></textarea> 
                </td>
            </tr>
        </tbody>
    </table>
    <input type="hidden" name="event_id" value="<?php echo $event->id;?>">
    <input type="hidden" id="assessment_form" name="assessment_form" value="">

</div>

