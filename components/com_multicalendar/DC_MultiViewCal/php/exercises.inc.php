<div id="exercise_table" style="margin-top: 15px;">
    <input type="button" value="Add Record" id="add_exercise"><p>
            <table width="100%" border="0" cellpadding="0" cellspacing="0" class="table-list">
                <tr>
                    <th width="40%">Execise/Description/Notes</th>
                    <th width="10%">Speed</th>
                    <th width="10%">Weight</th>
                    <th width="10%">Reps</th>
                    <th width="10%">Time</th>
                    <th width="10%">Sets</th>
                    <th width="10%">Rest</th>
                </tr>
                <?php
                   $exercises = getExercises($event->id);

                   foreach ($exercises as $exercise) {
                       echo '<tr>
                                <td>'.$exercise->title.'</td>
                                <td>'.$exercise->speed.'</td>
                                <td>'.$exercise->weight.'</td>
                                <td>'.$exercise->reps.'</td>
                                <td>'.$exercise->time.'</td>
                                <td>'.$exercise->sets.'</td>
                                <td>'.$exercise->rest.'</td>
                                <td><a href="#" id="'.$exercise->id.'" class="del">Delete</a></td>
                            </tr>';

                   }
                ?>
            </table>
</div>
