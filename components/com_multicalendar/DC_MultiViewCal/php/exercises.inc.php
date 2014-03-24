<script type="text/javascript">  
    (function($){
        $(document).ready(function() {
            /* start execise table */
            // drag and drop table rows
            attachDragExerciseRows();

            $("#add_exercise").click(function(){
                addExercise();
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
                copyExercise();
            });




            $("#exercise_table td").live('click', function() {
                    onCellKeypress($(this));

            });


            $("#exercise_table td input").live('blur', function() {
                stopCellEdit($(this));
            });

            $("#exercise_table td input").live('keypress', function(e) {
                if (e.keyCode == 27) {
                  stopCellEdit($(this)); 
                }

            });

            $("#exercise_table td input").live('keypress', function(e) {
                onCellInputKeypress(e, $(this));
            });

        }); 

        ////FUNCTIONS
        function attachDragExerciseRows() {
            $("#exercise_table").tableDnD({
                onDragStart: function(table, row) {
                    //$("#debugArea").html("Started dragging row "+row.id);
                },
                onDrop: function(table, row) {
                    //console.log('fuck');
                    var rows = table.tBodies[0].rows;

                    var debugStr = "Row dropped was "+row.id+". New order: ";

                    for (var i=0; i<rows.length; i++) {
                        debugStr += rows[i].id+" ";
                        setEventExerciseOrder(rows[i].id.replace('exercise_row_', ''), i);
                    }
                    //console.log(debugStr);
                    //$("#debugArea").html(debugStr);
                }
            });
        }

        function setEventExerciseOrder(row_id, order) {
            var DATA_FEED_URL = "<?php echo $datafeed?>&calid=<?php echo $_GET["calid"]?>";
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
                    //alert("error");
                }
            });
        }

        function addExercise() {
            var order = $("#exercise_table tr").length;
            //console.log(order);
            //$(".entry-form").fadeIn("fast");
            var obj = new Object();
            obj.title = '';
            obj.speed = '';
            obj.weight = '';
            obj.reps = '';
            obj.time = '';
            obj.sets = '';
            obj.rest = '';
            obj.order = order;
            obj.event_id = $("input[name=event_id]").val();
            var data = $.param(obj)+"&method=add_exercise";
            //console.log(data);
            ajax_exercise("add_exercise", '', data);
        }

        function copyExercise() {
            var rows = $('#exercise_table  input:checked');
            var order = $("#exercise_table tr").length;
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
              obj.order = order;
              obj.event_id = $("input[name=event_id]").val();
              var data = $.param(obj)+"&method=add_exercise";
              ajax_exercise("add_exercise", '', data);
              rows.attr('checked', false);
            }); 
        }

        function ajax_exercise(action, id, data){
            var DATA_FEED_URL = "<?php echo $datafeed?>&calid=<?php echo $_GET["calid"]?>";
            var url = DATA_FEED_URL;
            $.ajax({
                type : "POST",
                url : url,
                data : data,
                dataType : 'json',
                success : function(response) {
                    //console.log(response);
                    $("#save_exercise").attr("disabled", false);
                    if(!response.success) {
                        alert(response.message);
                        return;
                    }
                    if(action =="add_exercise") {

                        $(".entry-form").fadeOut("fast");
                        $(".table-list").append("<tr id='exercise_row_" + response.id +"'>\n\
                            <td width='15' class='drag_exercise_item'></td>\n\
                            <td width='180'>"+response.title+"</td>\n\
                            <td width='40'>"+response.speed+"</td>\n\
                            <td width='40'>"+response.weight+"</td>\n\
                            <td width='40'>"+response.reps+"</td>\n\
                            <td width='40'>"+response.time+"</td>\n\
                            <td width='40'>"+response.sets+"</td>\n\
                            <td width='40'>"+response.rest+"</td>\n\
                            <td width='10'><input type='checkbox' name='exercise_checked[]' value='" + response.id + "'></td>\n\
                            <td width='10'><a href='javascript:void(0)' data-id='"+response.id+"' class='delete_exercise'></a></td></tr>");
                        $(".table-list tr:last").effect("highlight", {color: '#4BADF5'}, 1000);
                        $(".entry-form input[type='text']").each(function(){$(this).val("");});
                        var height = $('#exercise_table_wrapper')[0].scrollHeight;
                        var iframe_height = $(document).height();
                        $(document).scrollTop(iframe_height);
                        //console.log(iframe_height );
                        $("#exercise_table_wrapper").scrollTop(height);

                    }  else if(action == "delete_exercise"){
                        var row_id = response.exercise_id;
                        $("a[data-id='"+row_id+"']").closest("tr").effect("highlight", {
                                color: '#4BADF5'
                        }, 1000);
                        var item =  $("a[data-id='"+row_id+"']").closest("tr");
                        item.fadeOut();
                        setTimeout(function(){item.remove()}, 1000);
                        var iframe_height = $(document).height();
                        $(document).scrollTop(iframe_height);

                    }
                    attachDragExerciseRows();
                },
                error: function(XMLHttpRequest, textStatus, errorThrown)
                {
                    console.log("error");
                }
        });



        }

        function onCellKeypress(this_obj)  {
            var inputtext = this_obj.children().val();
            if(inputtext) return;
            var exercise_id = this_obj.closest("tr").attr("id").replace('exercise_row_', '');
            var exercise_column = $(this).index();

            var currentindex = this_obj.index();
            if((currentindex == 0) || (currentindex == 8) || (currentindex == 9)) return;
            var OriginalContent = this_obj.text();
            var OriginalContent;
            if(OriginalContent =='') {
                if(inputtext == undefined) {
                    OriginalContent = '';
                } else {
                    OriginalContent = inputtext;
                }


            }

            this_obj.addClass("cellEditing");
            this_obj.html("<input style='width:100%' type='text' value='" + OriginalContent + "' />");
            this_obj.children().first().focus();
        }

        function stopCellEdit(cell) {
                var currentindex = cell.parent().index();
                if((currentindex == 0) || (currentindex == 8) || (currentindex == 9)) return;
                var exercise_id = cell.closest("tr").attr("id").replace('exercise_row_', '');
                var exercise_column = cell.parent().index();
                var newContent = cell.val();
                update_exercise_field(exercise_id, exercise_column, newContent);
                cell.parent().text(newContent);
                cell.parent().removeClass("cellEditing");
        }

        function onCellInputKeypress(e, this_obj) {
            var keynext = 13;
            var keyback = 38;
            if ((e.keyCode == keynext) || (e.keyCode == keyback)) {

                  var nexttd = this_obj.parent().next("td");
                  if (e.keyCode == keyback) {
                    nexttd = this_obj.parent().prev("td");
                  }
                  //console.log(nexttd);

                  var newContent = this_obj.val();
                  var exercise_id = nexttd.closest("tr").attr("id").replace('exercise_row_', '');
                  var exercise_column = this_obj.parent().index();
                  var nextindex =  nexttd.index();
                  if((nextindex == 9)) return false;
                  if((nextindex == 0)) {
                        if(nexttd.closest("tr").prev().length == 0) {
                          this_obj.parent().text(newContent);
                          this_obj.parent().removeClass("cellEditing");
                          update_exercise_field(exercise_id, Math.abs(exercise_column), newContent);
                          return false;
                        }
                        var nexttd = nexttd.closest("tr").prev().find("td:eq(7)");

                  }
                  if((nextindex == 8)) {
                        if(nexttd.closest("tr").next().length == 0) {
                          this_obj.parent().text(newContent);
                          this_obj.parent().removeClass("cellEditing");
                          update_exercise_field(exercise_id, Math.abs(exercise_column), newContent);
                          return false;
                        }
                        var nexttd = nexttd.closest("tr").next().find("td:eq(1)");

                  }

                  var nextOriginalText = nexttd.text();
                  nexttd.html("<input style='width:100%' type='text' value='" + nextOriginalText + "' />");
                  nexttd.children().focus();

                  this_obj.parent().text(newContent);
                  this_obj.parent().removeClass("cellEditing");
                  update_exercise_field(exercise_id, Math.abs(exercise_column), newContent);
                  //console.log(exercise_column);
            }
        }

        function update_exercise_field(exercise_id, exercise_column, new_value) {
            var DATA_FEED_URL = "<?php echo $datafeed?>&calid=<?php echo $_GET["calid"]?>";
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
                    //alert("error");
                }
            });
        }
    })($);

</script>  
<div id="exercises_wrapper" style="display:none;">
    <table  id="header_exercise_table" width="100%" border="0" cellpadding="0" cellspacing="0" >
            <thead
                <tr>
                    <th width="15"><a title="Drag, move and drop the row to change the ordering." href="javascript:void(0)" id="drag_exercise"></a></th>
                    <th width="150" title="Exercise/Description/Notes"><a title="Add new exercise" href="javascript:void(0)" id="add_exercise"></a><div style="padding-top: 7px;">Exercise/Notes</div></th>
                    <th width="40">Speed</th>
                    <th width="40">Weight</th>
                    <th width="40">Reps</th>
                    <th width="40">Time</th>
                    <th width="40">Sets</th>
                    <th width="40">Rest</th>
                    <th width="10"><a href="javascript:void(0)" title="Copy selected items" data-id="'.$exercise->id.'" class="copy_exercise"></a></th>
                    <th width="10"><a href="javascript:void(0)" title="Trash selected items" data-id="'.$exercise->id.'" class="trash_exercise"></a></th>
                </tr>
            </thead>
    </table>
    <div id="exercise_table_wrapper">
        <table id="exercise_table" width="100%" border="0" cellpadding="0" cellspacing="0" >
            <tbody class="table-list">
            <?php
               $exercises = getExercises($event->id);
               $c = 0;
               foreach ($exercises as $exercise) {
                   echo '<tr id="exercise_row_' . $exercise->id . '">
                            <td width="15" class="drag_exercise_item"></td>
                            <td width="180" >'.$exercise->title.'</td>
                            <td width="40" >'.$exercise->speed.'</td>
                            <td width="40">'.$exercise->weight.'</td>
                            <td width="40">'.$exercise->reps.'</td>
                            <td width="40">'.$exercise->time.'</td>
                            <td width="40">'.$exercise->sets.'</td>
                            <td width="40">'.$exercise->rest.'</td>
                            <td width="10"><input type="checkbox" name="exercise_checked[]" value="'.$exercise->id.'"></td>
                            <td width="10"><a href="javascript:void(0)" title="delete" data-id="'.$exercise->id.'" class="delete_exercise"></a></td>
                        </tr>';
                   $c++;
               }
            ?>
            </tbody>
        </table>
    </div>
</div>
