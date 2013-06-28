<script type="text/javascript">  
$(document).ready(function() {
        var DATA_FEED_URL = "<?php echo $datafeed?>&calid=<?php echo $_GET["calid"]?>";
        /* start execise table */
        // drag and drop table rows
            function attachDragExerciseRows() {
                $("#exercise_table").tableDnD({
                    onDragStart: function(table, row) {
                        $("#debugArea").html("Started dragging row "+row.id);
                    },
                    onDrop: function(table, row) {
                        var rows = table.tBodies[0].rows;
                        var debugStr = "Row dropped was "+row.id+". New order: ";
                        for (var i=1; i<rows.length; i++) {
                            debugStr += rows[i].id+" ";
                            setEventExerciseOrder(rows[i].id.replace('exercise_row_', ''), i);
                        }
                        //$("#debugArea").html(debugStr);
                    }
                });
            }
            attachDragExerciseRows();

            function setEventExerciseOrder(row_id, order) {
                var url = DATA_FEED_URL+ "&method=set_event_exircise_order";
                $.ajax({
                    type : "POST",
                    url : url,
                    data : {
                       row_id : row_id,
                       order  : order
                    },
                    dataType : 'json',
                    success : function(response) {
                        if(!response.success) alert(response.message);
                    },
                    error: function(XMLHttpRequest, textStatus, errorThrown)
                    {
                        alert("error");
                    }
                });
            }


            $("#add_exercise").click(function(){
                //$(".entry-form").fadeIn("fast");
                var obj = new Object();
                obj.title = '';
                obj.speed = '';
                obj.weight = '';
                obj.reps = '';
                obj.time = '';
                obj.sets = '';
                obj.rest = '';
                obj.event_id = $("input[name=event_id]").val();
                var data = $.param(obj)+"&method=add_exercise";
                ajax_exercise("add_exercise", '', data);
            });

            $("#close_add_exercise_box").click(function(){
                $(".entry-form").fadeOut("fast");	
            });
            $(".delete_exercise").live("click",function(){
                 var data = "&method=delete_exercise&exercise_id=" + $(this).data("id");
                 ajax_exercise("delete_exercise",$(this).data("id"), data);
            });

            $("#cancel_exercise").click(function(){
                $(".entry-form").fadeOut("fast");	
            });

            $("#save_exercise").click(function(){
                ajax_exercise("add_exercise");
                $("#save_exercise").attr("disabled", true);
                var data = $("#exercise_fields").serialize()+"&method=add_exercise";
                ajax_exercise("add_exercise", '', data);
            });

            $('.trash_exercise').click(function(){
                $('#exercise_table  input:checked').each(function(i){;
                  var row_id  = $(this).val();
                  var data = "&method=delete_exercise&exercise_id=" + row_id;
                  ajax_exercise("delete_exercise",row_id, data);
                });
            });

            $('.copy_exercise').click(function(){
                var rows = $('#exercise_table  input:checked');

                rows.each(function(i){;
                  var row_id  = $(this).val();
                  var obj = new Object();
                  obj.title = $(this).closest("tr").find("td:eq(1)").html();
                  obj.speed = $(this).closest("tr").find("td:eq(2)").html();
                  obj.weight = $(this).closest("tr").find("td:eq(3)").html();
                  obj.reps = $(this).closest("tr").find("td:eq(4)").html();
                  obj.time = $(this).closest("tr").find("td:eq(5)").html();
                  obj.sets = $(this).closest("tr").find("td:eq(6)").html();
                  obj.rest = $(this).closest("tr").find("td:eq(7)").html();
                  obj.event_id = $("input[name=event_id]").val();
                  var data = $.param(obj)+"&method=add_exercise";
                  ajax_exercise("add_exercise", '', data);
                });
            });

            function ajax_exercise(action, id, data){

                var url = DATA_FEED_URL;
                $.ajax({
                    type : "POST",
                    url : url,
                    data : data,
                    dataType : 'json',
                    success : function(response) {
                        $("#save_exercise").attr("disabled", false);
                        if(!response.success) {
                            alert(response.message);
                            return;
                        }
                        if(action =="add_exercise") {

                            $(".entry-form").fadeOut("fast");
                            $(".table-list").append("<tr id='exercise_row_" + response.id +"'><td class='drag_exercise_item'></td><td>"+response.title+"</td><td>"+response.speed+"</td><td>"+response.weight+"</td><td>"+response.reps+"</td><td>"+response.time+"</td><td>"+response.sets+"</td><td>"+response.rest+"</td><td><input type='checkbox' name='exercise_checked[]' value='" + response.id + "'></td><td><a href='#' data-id='"+response.id+"' class='delete_exercise'></a></td></tr>");
                            $(".table-list tr:last").effect("highlight", {color: '#4BADF5'}, 1000);
                            $(".entry-form input[type='text']").each(function(){$(this).val("");});
                        }  else if(action == "delete_exercise"){
                            var row_id = response.exercise_id;
                            $("a[data-id='"+row_id+"']").closest("tr").effect("highlight", {
                                    color: '#4BADF5'
                            }, 1000);
                            var item =  $("a[data-id='"+row_id+"']").closest("tr");
                            item.fadeOut();
                            setTimeout(function(){item.remove()}, 1000);
                            
                        }
                        attachDragExerciseRows();
                    },
                    error: function(XMLHttpRequest, textStatus, errorThrown)
                    {
                        alert("error");
                    }
            });



        }
        
        
        
        
        $("#exercise_table td").live('dblclick', function() {
                var exercise_id = $(this).closest("tr").attr("id").replace('exercise_row_', '');
                var exercise_column = $(this).index();
                
                var OriginalContent = $(this).text();
                $(this).addClass("cellEditing");
                $(this).html("<input type='text' value='" + OriginalContent + "' />");
                $(this).children().first().focus();

                $(this).children().first().keypress(function(e) {
                    if (e.which == 13) {
                        var newContent = $(this).val();
                        $(this).parent().text(newContent);
                        $(this).parent().removeClass("cellEditing");
                        
                        update_exercise_field(exercise_id, exercise_column, newContent);
                        //alert(event_id + ' ' + exercise_column);
                    }
                });

                $(this).children().first().blur(function() {
                    $(this).parent().text(OriginalContent);
                    $(this).parent().removeClass("cellEditing");
                });

        });

        function update_exercise_field(exercise_id, exercise_column, new_value) {
            var url = DATA_FEED_URL+ "&method=update_exercise_field";
            $.ajax({
                type : "POST",
                url : url,
                data : {
                   exercise_id : exercise_id,
                   exercise_column  : exercise_column,
                   new_value : new_value
                },
                dataType : 'json',
                success : function(response) {
                    if(!response.success) alert(response.message);
                },
                error: function(XMLHttpRequest, textStatus, errorThrown)
                {
                    alert("error");
                }
            });
        }
         /* end execise table */


    });  

</script>  
<div id="exercise_table_wrapper">
            <table id="exercise_table" width="100%" border="0" cellpadding="0" cellspacing="0" >
                <thead
                    <tr>
                        <th width="4%"><a title="Drag, move and drop the row to change the ordering." href="javascript:void(0)" id="drag_exercise"></a></th>
                        <th width="40%" title="Execise/Description/Notes"><a title="Add new exercise" href="javascript:void(0)" id="add_exercise"></a><div style="padding-top: 7px;">Execise/Notes</div></th>
                        <th width="10%">Speed</th>
                        <th width="10%">Weight</th>
                        <th width="10%">Reps</th>
                        <th width="10%">Time</th>
                        <th width="10%">Sets</th>
                        <th width="10%">Rest</th>
                        <th width="5%"><a href="#" title="Copy selected items" data-id="'.$exercise->id.'" class="copy_exercise"></a></th>
                        <th width="5%"><a href="#" title="Trash selected items" data-id="'.$exercise->id.'" class="trash_exercise"></a></th>
                    </tr>
                </thead>
                <tbody class="table-list">
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
                </tbody>
            </table>
</div>
