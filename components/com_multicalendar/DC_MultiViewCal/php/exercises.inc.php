<div  style="margin-top: 15px;">
            <table id="exercise_table" width="100%" border="0" cellpadding="0" cellspacing="0" class="table-list">
                <tr>
                    <th width="4%"><a title="Drag, move and drop the row to change the ordering." href="javascript:void(0)" id="drag_exercise"></a></th>
                    <th width="40%"><a title="Add new exercise" href="javascript:void(0)" id="add_exercise"></a>Execise/Description/Notes</th>
                    <th width="10%">Speed</th>
                    <th width="10%">Weight</th>
                    <th width="10%">Reps</th>
                    <th width="10%">Time</th>
                    <th width="10%">Sets</th>
                    <th width="10%">Rest</th>
                    <th width="5%"><a href="#" title="Copy selected items" data-id="'.$exercise->id.'" class="copy_exercise"></a></th>
                    <th width="5%"><a href="#" title="Trash selected items" data-id="'.$exercise->id.'" class="trash_exercise"></a></th>
                </tr>
                <?php
                   $exercises = getExercises($event->id);
                   $c = 0;
                   foreach ($exercises as $exercise) {
                       echo '<tr id="exercise_row_' . $exercise->id . '">
                                <td class="drag_exercise_item"></td>
                                <td>'.$exercise->title.'</td>
                                <td>'.$exercise->speed.'</td>
                                <td>'.$exercise->weight.'</td>
                                <td>'.$exercise->reps.'</td>
                                <td>'.$exercise->time.'</td>
                                <td>'.$exercise->sets.'</td>
                                <td>'.$exercise->rest.'</td>
                                <td><input type="checkbox" name="exercise_checked[]" value="'.$exercise->id.'"></td>
                                <td><a href="#" title="delete" data-id="'.$exercise->id.'" class="delete_exercise"></a></td>
                            </tr>';
                       $c++;
                   }
                ?>
            </table>
</div>
