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
                                    <input class="assessment_input required digits"  maxlength="7" type="text" name="as_height" id="as_height">
                                </td>
                                <td>cm</td>
                            </tr>
                            <tr>
                                <td>Weight</td>
                                <td>
                                    <input class="assessment_input required digits" maxlength="7" type="text" name="as_weight" id="as_weight">
                                </td>
                                <td>kg</td>
                            </tr>
                            <tr>
                                <td>Age</td>
                                <td>
                                    <input class="assessment_input required digits" maxlength="2" type="text" name="as_age" id="as_age">
                                </td>
                                <td>years</td>
                            </tr>
                            <tr>
                                <td>Body Fat</td>
                                <td>
                                    <input class="assessment_input required digits" maxlength="3" type="text" name="as_body_fat" id="as_body_fat">
                                </td>
                                <td>%</td>
                            </tr>
                            <tr>
                                <td>Lean Mass</td>
                                <td><input class="assessment_input required digits" maxlength="5" type="text" name="as_lean_mass" id="as_lean_mass"></td>
                                <td>kg</td>
                            </tr>
                        </tbody>
                    </table>
                    Trainer Comments</br>
                    <textarea cols="35" id="as_comments" name="as_comments" rows="3" >

                    </textarea>  
                </td>
                <td>
                    <h4>Health Assessment</h4>
                    <table  width="100%"  border="0">
                        <tbody>
                            <tr>
                                <td >Blood Pressure</td>
                                <td >
                                    <input class="assessment_input  "  maxlength="10" type="text" name="ha_blood_pressure">
                                </td>
                                <td>mm/Hg</td>
                            </tr>
                            <tr>
                                <td>Body Mass Index</td>
                                <td>
                                    <input class="assessment_input digits" maxlength="7" type="text" name="ha_body_mass_index">
                                </td>
                                <td>kg</td>
                            </tr>
                            <tr>
                                <td>Sit & Reach</td>
                                <td>
                                    <input class="assessment_input digits " maxlength="5" type="text" name="ha_sit_reach">
                                </td>
                                <td>cm</td>
                            </tr>
                            <tr>
                                <td>Lung Function</td>
                                <td>
                                    <input class="assessment_input digits" maxlength="5" type="text" name="ha_lung_function">
                                </td>
                                <td>ml</td>
                            </tr>
                            <tr>
                                <td>Aerobic Fitness</td>
                                <td><input class="assessment_input " maxlength="10" type="text" name="ha_aerobic_fitness"></td>
                                <td>V02MAX</td>
                            </tr>
                        </tbody>
                    </table>
                    Trainer Comments</br>
                    <textarea cols="35" id="ha_comments" name="ha_comments" rows="3" >

                    </textarea>  
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
                                    <input class="assessment_input digits"  maxlength="7" type="text" name="am_height">
                                </td>
                                <td width="30px">cm</td>
                                <td>Bicep L</td>
                                <td>
                                    <input class="assessment_input digits"  maxlength="7" type="text" name="am_bicep_l">
                                </td>
                                <td>cm</td>
                            </tr>
                            <tr>
                                <td>Weight</td>
                                <td>
                                    <input class="assessment_input digits"  maxlength="7" type="text" name="am_weight">
                                </td>
                                <td>kg</td>
                                <td>Thigh R</td>
                                <td>
                                    <input class="assessment_input digits"  maxlength="7" type="text" name="am_thigh_r">
                                </td>
                                <td>cm</td>
                            </tr>
                            <tr>
                                <td>Waist</td>
                                <td>
                                    <input class="assessment_input digits"  maxlength="7" type="text" name="am_waist">
                                </td>
                                <td>cm</td>
                                <td>Thigh L</td>
                                <td>
                                    <input class="assessment_input digits"  maxlength="7" type="text" name="am_thigh_l">
                                <td>cm</td>
                            </tr>
                            <tr>
                                <td>Hips</td>
                                <td>
                                     <input class="assessment_input digits"  maxlength="7" type="text" name="am_hips">
                                </td>
                                <td>cm</td>
                                <td>Calf R</td>
                                <td>
                                    <input class="assessment_input digits"  maxlength="7" type="text" name="am_calf_r">
                                </td>
                                <td>cm</td>
                            </tr>
                            <tr>
                                <td>Chest</td>
                                <td>
                                    <input class="assessment_input digits"  maxlength="7" type="text" name="am_chest">
                                </td>
                                <td>cm</td>
                                <td>Calf L</td>
                                <td>
                                    <input class="assessment_input digits"  maxlength="7" type="text" name="am_calf_l">
                                </td>
                                <td>cm</td>
                            </tr>
                            <tr>
                                <td>Bicep R</td>
                                <td>
                                    <input class="assessment_input digits"  maxlength="7" type="text" name="am_bicep_r">
                                </td>
                                <td>cm</td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                        </tbody>
                    </table>
                    Trainer Comments</br>
                    <textarea cols="35" id="am_comments" name="am_comments" rows="3" >

                    </textarea>  
                </td>
                <td>
                    <h4>Bio-Impedience Analysis</h4>
                    <table  width="100%"  border="0">
                        <tbody>
                            <tr>
                                <td>Body Fat</td>
                                <td>
                                    <input class="assessment_input  "  maxlength="3" type="text" name="bia_body_fat">
                                </td>
                                <td>%</td>
                            </tr>
                            <tr>
                                <td>Body Water</td>
                                <td>
                                    <input class="assessment_input digits" maxlength="3" type="text" name="bia_body_water">
                                </td>
                                <td>%</td>
                            </tr>
                            <tr>
                                <td>Muscle Mass</td>
                                <td>
                                    <input class="assessment_input digits " maxlength="5" type="text" name="bia_muscle_mass">
                                </td>
                                <td>kg</td>
                            </tr>
                            <tr>
                                <td>Bone Mass</td>
                                <td>
                                    <input class="assessment_input digits" maxlength="5" type="text" name="bia_bone_mass">
                                </td>
                                <td>kg</td>
                            </tr>
                            <tr>
                                <td>Visceral Fat</td>
                                <td><input class="assessment_input " maxlength="2" type="text" name="bia_visceral_fat"></td>
                                <td>1-59</td>
                            </tr>
                        </tbody>
                    </table>
                    </br>
                    Trainer Comments</br>
                    <textarea style="margin-top: 8px" cols="35" id="bio_comments" name="bio_comments" rows="3" >

                    </textarea>  
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
                                    <input class="assessment_input digits"  maxlength="7" type="text" name="bsm_height">
                                </td>
                                <td >cm</td>
                            </tr>
                            <tr>
                                <td>Weight</td>
                                <td>
                                    <input class="assessment_input digits"  maxlength="7" type="text" name="bsm_weight">
                                </td>
                                <td>kg</td>
                            </tr>
                            <tr>
                                <td>Chin</td>
                                <td>
                                    <input class="assessment_input digits"  maxlength="7" type="text" name="bsm_chin">
                                </td>
                                <td>mm</td>
                            </tr>
                            <tr>
                                <td>Check</td>
                                <td>
                                     <input class="assessment_input digits"  maxlength="7" type="text" name="bsm_check">
                                </td>
                                <td>mm</td>
                               </tr>
                            <tr>
                                <td>Pec</td>
                                <td>
                                    <input class="assessment_input digits"  maxlength="7" type="text" name="bsm_pec">
                                </td>
                                <td>mm</td>
                            </tr>
                            <tr>
                                <td>Tricep</td>
                                <td>
                                    <input class="assessment_input digits"  maxlength="7" type="text" name="bsm_tricep">
                                </td>
                                <td>mm</td>
                            </tr>
                            <tr>
                                <td>Subscapularis</td>
                                <td>
                                    <input class="assessment_input digits"  maxlength="7" type="text" name="bsm_subscapularis">
                                </td>
                                <td>mm</td>
                            <tr>
                                <td>SUM 10</td>
                                <td>
                                    <input class="assessment_input digits"  maxlength="7" type="text" name="bsm_sum10">
                                </td>
                                <td>mm</td>
                            </tr>
                            <tr>
                                <td>SUM 12</td>
                                <td>
                                    <input class="assessment_input digits"  maxlength="7" type="text" name="bsm_sum12">
                                </td>
                                <td>mm</td>
                            </tr>
                            <tr>
                                <td>Midaxillary</td>
                                <td>
                                    <input class="assessment_input digits"  maxlength="7" type="text" name="bsm_midaxillary">
                                </td>
                                <td>mm</td>
                            </tr>
                           <tr>
                                <td>Suprailiac</td>
                                <td>
                                    <input class="assessment_input digits"  maxlength="7" type="text" name="bsm_supraillac">
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
                                    <input class="assessment_input digits"  maxlength="7" type="text" name="bsm_umbilical">
                                <td>mm</td>
                            </tr>
                            <tr>
                                <td>Knee</td>
                                <td>
                                    <input class="assessment_input digits"  maxlength="7" type="text" name="bsm_knee">
                                </td>
                                <td>mm</td>
                            </tr>
                            <tr>
                                <td>Calf</td>
                                <td>
                                    <input class="assessment_input digits"  maxlength="7" type="text" name="bsm_calf">
                                </td>
                                <td>mm</td>
                            </tr>
                            <tr>
                                <td>Quadricep</td>
                                <td><input class="assessment_input digits"  maxlength="7" type="text" name="bsm_quadricep"></td>
                                <td>mm</td>
                            </tr>
                            <tr>
                                <td>Hamstring</td>
                                <td>
                                    <input class="assessment_input digits"  maxlength="7" type="text" name="bsm_hamstring">
                                </td>
                                <td>mm</td>
                            </tr>
                            <tr>
                                <td>Body Fat</td>
                                <td>
                                    <input class="assessment_input digits"  maxlength="7" type="text" name="bsm_body_fat">
                                </td>
                                <td>%</td>
                            </tr>
                            <tr>
                                <td>Lean Mass</td>
                                <td>
                                    <input class="assessment_input digits"  maxlength="3" type="text" name="bsm_lean_mass">
                                <td>kg</td>
                            </tr>
                          </tbody>
                    </table>
                    <div style="margin-top: 7px;">Trainer Comments</br></div>
                    <textarea cols="35" id="bsm_comments" name="bsm_comments" rows="3" >

                    </textarea>  
                 </td>
            </tr>
        </tbody>
    </table>
    <table border="0">
        <tbody>
            <tr>
                <td>
                    Nutrition Protocols</br>
                    <textarea cols="23" id="nutrition_protocols" name="nutrition_protocols" rows="4" >

                    </textarea>  
                </td>
                <td>
                    Supplementation Protocols</br>
                    <textarea cols="24" id="supplementation_protocols" name="supplementation_protocols" rows="4" >

                    </textarea>  
                </td>
                <td>
                    Training Protocols</br>
                    <textarea cols="24" id="training_protocols" name="training_protocols" rows="4" >

                    </textarea>  
                </td>
            </tr>
        </tbody>
    </table>
    <input type="hidden" name="event_id" value="<?php echo $event->id;?>">

</div>

