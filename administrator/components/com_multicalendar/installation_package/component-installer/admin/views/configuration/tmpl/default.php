<?php 

/**
* @Copyright Copyright (C) 2010 CodePeople, www.codepeople.net
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*
* This file is part of Multi Calendar for Joomla <www.joomlacalendars.com>.
* 
* Multi Calendar for Joomla is free software: you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation, either version 3 of the License, or
* (at your option) any later version.
*
* Multi Calendar for Joomla  is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License
* along with Multi Calendar for Joomla.  If not, see <http://www.gnu.org/licenses/>.
*
**/

defined('_JEXEC') or die('Restricted access'); 

?>

<?php
	$cid = JRequest::getVar( 'cid', array(0), '', 'array' );
	$edit=JRequest::getVar( 'edit', true );
	JArrayHelper::toInteger($cid, array(0));

	$text = ( $edit ? JText::_( 'Edit' ) : JText::_( 'New' ) );

	JToolBarHelper::title(  JText::_( 'COMMULTICALENDAR_CPCALENDAR' ).': <small><small>[ ' . JText::_( 'Configuration' ).' ]</small></small>', $edit ? "multicalendar-edit" : "multicalendar-new" );
	
?>

<?php
JFilterOutput::objectHTMLSafe( $this->calendar, ENT_QUOTES );
$tab2 = unserialize ($this->configuration[0]->palettes);
$tab1 = unserialize ($this->configuration[0]->administration);


?>



<link type="text/css" href="components/com_multicalendar/views/configuration/tmpl/css/cupertino/jquery-ui-1.8.20.custom.css" rel="stylesheet" />
<link rel="stylesheet" href="components/com_multicalendar/views/configuration/tmpl/css/colorpicker.css" type="text/css" />


<script type="text/javascript" src="components/com_multicalendar/views/configuration/tmpl/js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="components/com_multicalendar/views/configuration/tmpl/js/jquery-ui-1.8.20.custom.min.js"></script>
<script type="text/javascript" src="components/com_multicalendar/views/configuration/tmpl/js/colorpicker.js"></script>
<script type="text/javascript" src="components/com_multicalendar/views/configuration/tmpl/js/eye.js"></script>
<script type="text/javascript" src="components/com_multicalendar/views/configuration/tmpl/js/utils.js"></script>
<script type="text/javascript" src="components/com_multicalendar/views/configuration/tmpl/js/jquery.validate.js"></script>

<script type="text/javascript">
$(function(){
			$('#tabs').tabs();			
//////////////////////////// tab1 /////////////////////////////////////////				
			$("#tabs-1Form").validate({
			    //ignore: "",
                submitHandler: function(form) {
                    if ($.post("index.php?option=com_multicalendar&view=configuration&task=ajax&tab=1", $("#tabs-1Form").serialize()))
                        alert('Configuration successfully updated!');
                }
            });
            var tab1 = new Array();
			tab1 = <?php echo json_encode($tab1);?>;
			$(".views").each(function(){
			    if (jQuery.inArray($(this).attr("value"), tab1.views)==-1)
			        $(this).removeAttr("checked");    
			    else
			        $(this).attr("checked","checked");    
			});
			$(".views").each(function(){
			    (jQuery.inArray($(this).attr("value"), tab1.views)==-1)?$(this).removeAttr("checked"):$(this).attr("checked","checked");
			});
			$("#viewdefault option").each(function(){
			    ($(this).attr("value") != tab1.viewdefault)?$(this).removeAttr("selected"):$(this).attr("selected","selected");
			});
			$("#language option").each(function(){
			    ($(this).attr("value") != tab1.language)?$(this).removeAttr("selected"):$(this).attr("selected","selected");
			});
			$("#start_weekday option").each(function(){
			    ($(this).attr("value") != tab1.start_weekday)?$(this).removeAttr("selected"):$(this).attr("selected","selected");
			});
			$("#cssStyle option").each(function(){
			    ($(this).attr("value") != tab1.cssStyle)?$(this).removeAttr("selected"):$(this).attr("selected","selected");
			});
			$("#paletteColor option").each(function(){
			    ($(this).attr("value") != tab1.paletteColor)?$(this).removeAttr("selected"):$(this).attr("selected","selected");
			});
			($("#btoday").attr("value")!= tab1.btoday)?$("#btoday").removeAttr("checked"):$("#btoday").attr("checked","checked");
			($("#bnavigation").attr("value")!= tab1.bnavigation)?$("#bnavigation").removeAttr("checked"):$("#bnavigation").attr("checked","checked");
			($("#brefresh").attr("value")!= tab1.brefresh)?$("#brefresh").removeAttr("checked"):$("#brefresh").attr("checked","checked");
			
			$("#numberOfMonths option").each(function(){
			    ($(this).attr("value") != tab1.numberOfMonths)?$(this).removeAttr("selected"):$(this).attr("selected","selected");
			});
			($("#sample0").attr("value")!= tab1.sample0)?$("#sample0").removeAttr("checked"):$("#sample0").attr("checked","checked");
			$("#sample1 option").each(function(){
			    ($(this).attr("value") != tab1.sample1)?$(this).removeAttr("selected"):$(this).attr("selected","selected");
			});
			($("#sample2").attr("value")!= tab1.sample2)?$("#sample2").removeAttr("checked"):$("#sample2").attr("checked","checked");
			$("#sample3").val(tab1.sample3);
			$("#sample4 option").each(function(){
			    ($(this).attr("value") != tab1.sample4)?$(this).removeAttr("selected"):$(this).attr("selected","selected");
			});
			showhide("sample");
//////////////////////////// tab2 /////////////////////////////////////////
			var items = new Array();
			items = <?php echo json_encode($tab2);?>;
			function loadTable()
			{
			    var optionsTab1 = "";
			    var $tbl = $('<table>').attr('id', 'basicTable').attr('cellpadding', '0').attr('cellspacing', '0').attr('width', '440').addClass("tablelist");
			    $tbl.html('<tr><th width="123"><?php echo JText::_( 'NAME' );?></th><th width="152"><?php echo JText::_( 'COLORS' );?></th><th width="70">&nbsp;</th><th width="95">&nbsp;</th></tr>');
			    for (var i=0;i<items.length;i++)
			    {
			        var $tr = $('<tr>').append( $('<td>').text(items[i].name));
			        var $td = $('<td>');
			        for (var j=0;j<items[i].colors.length;j++)
			        {
			            style = "";
			            if (items[i].default==items[i].colors[j])
                        {
                            
                            style =";border:3px solid black;width:6px;height:6px";
                        }
			            
			            $td.append('<div class="color-drag0" c="'+items[i].colors[j]+'" style="background-color: #'+items[i].colors[j]+style+'"></div>');
			        }    
			        $tr.append( $td,$('<td>').html('<input type="button" class="sbtn btnEdition" idx="'+i+'" value="<?php echo JText::_( 'EDIT' );?>"/>') ) ; 
			        if (i!=0)
			            $tr.append( $('<td>').html('<input type="button" class="sbtn btnRemove" idx="'+i+'" value="<?php echo JText::_( 'REMOVE' );?>"/>')  ) ;
			        else    
			            $tr.append( $('<td>').html('&nbsp;')  ) ;
			        $tbl.append($tr);
			        optionsTab1 += '<option value="'+i+'">'+items[i].name+'</option>';                                 
			    }   
			    $('#paletteList').empty();
			    $('#paletteList').append($tbl);
			    $( ".sbtn" ).button().css("margin","5px 0px 5px 0px");
			    $( ".btnEdition" ).click(function() {
				    loadPalette($(this).attr("idx"));
				    $("#paletteEdition").removeClass("mvhide");
				    $("#paletteEdition").addClass("mvdisplay");
				});
				$( ".btnRemove" ).click(function() {
				    items.splice($(this).attr("idx"), 1);
				    loadTable();
				    $.post("index.php?option=com_multicalendar&view=configuration&task=ajax&tab=2", {'items':items});
				});
				var s = $('#paletteColor').val();
			    $('#paletteColor').empty();
			    $('#paletteColor').html(optionsTab1);
			    $('#paletteColor > option').each(function () {
			        if (parseInt($(this).attr("value"))==parseInt(s))
			            $(this).attr("selected","selected");
                });
				
				
		    }  
		    loadTable();
		    var colors = [];
		    var d = "FFF FCC FC9 FF9 FFC 9F9 9FF CFF CCF FCF " +
                    "CCC F66 F96 FF6 FF3 6F9 3FF 6FF 99F F9F " +
                    "BBB F00 F90 FC6 FF0 3F3 6CC 3CF 66C C6C " +
                    "999 C00 F60 FC3 FC0 3C0 0CC 36F 63F C3C " +
                    "666 900 C60 C93 990 090 399 33F 60C 939 " +
                    "333 600 930 963 660 060 366 009 339 636 " +
                    "000 300 630 633 330 030 033 006 309 303";
                        
		    for (var i = 0; i < d.length; i = i + 4) {
                colors.push(d.substr(i, 3));
                $("#colors").append('<div class="color-drag" style="background-color: #'+d.substr(i, 3)+'" c="'+d.substr(i, 3)+'"></div>');
            }
			$('#colorpickerField1').ColorPicker({
                	onSubmit: function(hsb, hex, rgb, el) {
                		$(el).val(hex);
                		$('#colorpickerField1').css("background-color","#"+hex);
                		if (parseInt(rgb.r)+parseInt(rgb.g)+parseInt(rgb.b) < 255*3/2)
                		    $('#colorpickerField1').css("color","#fff");
                		else
                		    $('#colorpickerField1').css("color","#000");    
                		$(el).ColorPickerHide();
                	},
                	onBeforeShow: function () {
                		$(this).ColorPickerSetColor(this.value);
                	}
                }).bind('keyup', function(){
                	$(this).ColorPickerSetColor(this.value);
                });
		    $( ".color-drag" ).draggable({
                revert: "invalid",
                helper: "clone",
		        cursor: "move"
                }).bind("click",function(){
                    makedrop($( "#palette"),$(this));
                });
            $( "#recicle").droppable({
                accept: ".deletable",
		    	activeClass: "ui-state-hover",
		    	hoverClass: "ui-state-active",
		    	drop: function( event, ui ) {
		    	    ui.draggable.remove();
		    	    $("#paletteCounts").val(parseInt($("#paletteCounts").val())-1);
		    	    $("#tabs-2Form").valid();
		    	}
            });
            function makedrop(i1,i2)
            {
                        
		    	        var $copy = i2.clone().draggable({
                                                                 revert: "invalid",
                                                                 helper: "clone",
		                                                         cursor: "move"
                                                                 }).bind("click",function(){
                                                                     makedrop($( "#palette"),$(this));
                                                                 });
                                                                 
                        i2.parent().append($copy);
                        i2.unbind("click").click(function(){
                                                                     $(".deletable").css("border","none");
                                                                     $(".deletable").css("width","25px");
                                                                     $(".deletable").css("height","25px");
                                                                     $(this).css("border","2px solid black");
                                                                     $(this).css("width","21px");
                                                                     $(this).css("height","21px");                                                                     
                                                                 });;
		    	        i2.addClass("deletable");
		    	        i2.removeClass("color-drag");
		    	        i1.append(i2);
		    	        $("#paletteCounts").val(parseInt($("#paletteCounts").val())+1);
		    	        $("#tabs-2Form").valid();
		    	        
            }		        
		    $( "#palette").droppable({
		    	accept: ".color-drag",
		    	activeClass: "ui-state-hover",
		    	hoverClass: "ui-state-active",
		    	drop: function( event, ui ) {
		    	    makedrop($(this),ui.draggable);
		    	}
		    });
			$( ".sbtn" ).button().css("margin","5px 0px 5px 0px"); 
			$( "#btnAdd" ).click(function() { 
			    $('<div/>', {
                    class: 'color-drag1 deletable',
                    c:$('#colorpickerField1').val(),
                    style: 'width:25px;height:25px;background-color:#'+$('#colorpickerField1').val()
                }).appendTo('#palette').draggable({revert: "invalid",helper: "clone",cursor: "move"}).click(function(){
                                                                     $(".deletable").css("border","none");
                                                                     $(".deletable").css("width","25px");
                                                                     $(".deletable").css("height","25px");
                                                                     $(this).css("border","2px solid black");
                                                                     $(this).css("width","21px");
                                                                     $(this).css("height","21px");                                                                     
                                                                 });
                $("#paletteCounts").val(parseInt($("#paletteCounts").val())+1);
                $("#tabs-2Form").valid();
			    return false; 
			});
			$( "#btnNewP" ).click(function() { 
			    loadPalette(-1);
			    $("#paletteEdition").removeClass("mvhide");
			    $("#paletteEdition").addClass("mvdisplay");
			});
			$( "#btnCancel" ).click(function() { 
			    $("#paletteEdition").removeClass("mvdisplay");
			    $("#paletteEdition").addClass("mvhide");
			});
			
			
			function loadPalette(id)
			{
			    $("#palettename").val("");
			    $("#palette").html("");
			    $("#paletteID").val(id);
			    $("#paletteCounts").val(0);
			    
			    if (id!=-1)
			    {
			        $("#palettename").val(items[id].name);
			        $("#palette").html("");
			        for (var j=0;j<items[id].colors.length;j++)
			        {
			            
			            $node = $('<div/>', {
                            class: 'color-drag1 deletable',
                            c:items[id].colors[j],
                            style: 'width:25px;height:25px;background-color:#'+items[id].colors[j]
                        }).appendTo('#palette').draggable({revert: "invalid",helper: "clone",cursor: "move"}).click(function(){
                                                                     $(".deletable").css("border","none");
                                                                     $(".deletable").css("width","25px");
                                                                     $(".deletable").css("height","25px");
                                                                     $(this).css("border","2px solid black");
                                                                     $(this).css("width","21px");
                                                                     $(this).css("height","21px");                                                                     
                                                                 });
                        if (items[id].default==items[id].colors[j])
                        {
                            $node.css("border","2px solid black").css("width","21px").css("height","21px");
                        }                                          
			        }
			        $("#paletteCounts").val(items[id].colors.length);
			        
			    }        
			}
			jQuery.validator.addMethod("paletteValid", function(value, element) { 
			    return (parseInt(value)>=1)
                }, "Please add at least one color");
			$("#tabs-2Form").validate({
			    ignore: "",
                submitHandler: function(form) {
                    //nothing
                    savePalette($("#paletteID").val());

                }
            });
            
			function savePalette(id)
			{
			    if (id == -1)
			    {
			        id = items.length;
			        items[id] = {name:$("#palettename").val(),colors:new Array()};
			    }    
			    items[id].name = $("#palettename").val();
			    items[id].colors = new Array();
			    $('#palette > div').each(function () {
			        items[id].colors.push($(this).attr("c"));
			        if ($(this).css("width")=="21px")
			            items[id].default = $(this).attr("c");    
                });  
                loadTable();
                $("#paletteEdition").removeClass("mvdisplay");
			    $("#paletteEdition").addClass("mvhide");
			    $.post("index.php?option=com_multicalendar&view=configuration&task=ajax&tab=2", {'items':items});
			}    

});
function showhide(id)
{
    var obj1 = document.getElementById(id+"0");
    var obj2 = document.getElementById(id+"1");
    var obj3 = document.getElementById(id+"div");
    if ((obj1.checked) && (obj2.selectedIndex==1))
        obj3.style.display = "none";
    else        
        obj3.style.display = "";
}		
</script>
<style type="text/css">
		body{ font: 12px arial;}
			
.color-drag0{float:left;margin:1px;border:1px solid black;width:10px;height:10px;}
.color-drag{float:left;margin:1px;border:1px solid black;width:10px;height:10px;}
.mvhide{display:none}
.mvdisplay{display:''}
.containerP{width:25px;height:25px;display:block;float:left;margin:1px;border:1px solid black}
#palette{width:270px;height:189px;float:left;border:1px solid black;overflow: auto;background:#fff}
.deletable{width:25px;height:25px;margin:1px;float:left;}
.tablelist{table-layout:fixed;border:0px;border-left:1px solid #ccc;border-top:1px solid #ccc}
.tablelist th,.tablelist td{border-right:1px solid #ccc;border-bottom:1px solid #ccc;padding:5px;}
.tablelist th {background-color:#4DB2E5;color:#fff}
label { float: left;clear:both; }
label.error { float: none; color: red; padding-left: .5em; vertical-align: top; }
p { clear: both; }
.submit { margin-left: 12em; }
em { font-weight: bold; padding-right: 1em; vertical-align: top; }
#tabs-1 label{width:200px;margin-top:10px;}
.field{float:left;margin-left:20px;margin-top:10px;}
		</style>		



    <div id="tabs">
			<ul>
				<li><a href="#tabs-1"><?php echo JText::_( 'CALENDAR ADMINISTRATION' )?></a></li>
				<li><a href="#tabs-2"><?php echo JText::_( 'PALETTE COLORS' )?></a></li>
			</ul>
			<div id="tabs-1">
			<form action="index.php" method="post" name="tabs-1Form" id="tabs-1Form">
			    <label><?php echo JText::_( 'CALENDAR VIEWS' )?></label>
			    <div class="field">
			        <input id="views0" name="views[]" class="views" value="viewDay" checked="checked" type="checkbox" class="required"> <?php echo JText::_( 'DAY' )?><br />
				    <input id="views1" name="views[]" class="views" value="viewWeek" checked="checked" type="checkbox" class="required"> <?php echo JText::_( 'WEEK' )?><br />
				    <input id="views2" name="views[]" class="views" value="viewMonth" checked="checked" type="checkbox" class="required"> <?php echo JText::_( 'MONTH' )?><br />
				    <input id="views3" name="views[]" class="views" value="viewNMonth" checked="checked" type="checkbox" class="required"> <?php echo JText::_( 'NMONTH' )?><br />
			    </div>
			    <label><?php echo JText::_( 'DEFAULT VIEW' )?></label>
			    <div class="field"><select id="viewdefault" name="viewdefault"> 
                    	<option value="day"><?php echo JText::_( 'DAY' )?></option>
                    	<option value="week"><?php echo JText::_( 'WEEK' )?></option>
                    	<option value="month" selected="selected"><?php echo JText::_( 'MONTH' )?></option>
                    	<option value="nMonth"><?php echo JText::_( 'NMONTH' )?></option>
                    </select></div>
			    <label><?php echo JText::_( 'START DAY OF THE WEEK' )?></label>
			    <div class="field"><select id="start_weekday" name="start_weekday">
                    	<option value="0" selected="selected"><?php echo JText::_( 'Sunday' )?></option>
                    	<option value="1"><?php echo JText::_( 'Monday' )?></option>                    
                    	<option value="2"><?php echo JText::_( 'Tuesday' )?></option>
                    	<option value="3"><?php echo JText::_( 'Wednesday' )?></option>
                    	<option value="4"><?php echo JText::_( 'Thursday' )?></option>
                    	<option value="5"><?php echo JText::_( 'Friday' )?></option>
                    	<option value="6"><?php echo JText::_( 'Saturday' )?></option>
                    </select></div>
			    <label><?php echo JText::_( 'CSS STYLE' )?></label>
			    <div class="field"><select id="cssStyle" name="cssStyle">
                    	<option value="ui-lightness">UI lightness</option>
                    	<option value="ui-darkness">UI darkness</option>
                    	<option value="smoothness">Smoothness</option>
                    	<option value="start">Start</option>
                    	<option value="redmond">Redmond</option>
                    	<option value="sunny">Sunny</option>
                    	<option value="overcast">Overcast</option>
                    	<option value="le-frog">Le Frog</option>
                    	<option value="flick">Flick</option>
                    	<option value="pepper-grinder">Pepper Grinder</option>
                    	<option value="eggplant">Eggplant</option>
                    	<option value="dark-hive">Dark Hive</option>
                    	<option value="cupertino" selected="selected">Cupertino</option>
                    	<option value="south-street">South Street</option>
                    	<option value="blitzer">Blitzer</option>
                    	<option value="humanity">Humanity</option>
                    	<option value="hot-sneaks">Hot sneaks</option>
                        <option value="excite-bike">Excite Bike</option>
                    	<option value="vader">Vader</option>
                    	<option value="mint-choc">Mint Choc</option>
                    	<option value="black-tie">Black Tie</option>
                    	<option value="trontastic">Trontastic</option>
                    	<option value="swanky-purse">Swanky Purse</option>
                    </select></div>
                <label><?php echo JText::_( 'PALETTE COLOR' )?></label>
                <div class="field"><select id="paletteColor" name="paletteColor">
                    	<?php
                    	for ($i=0;$i<count($tab2);$i++)
                    	{
                    	    echo '<option value="'.$i.'" >'.$tab2[$i]["name"].'</option>';
                    	}
                    	?>
                    </select></div>    
			    
			    <label><?php echo JText::_( 'OTHER BUTTONS' )?></label>
			    <div class="field"><input id="btoday" name="btoday" value="1" type="checkbox"><?php echo JText::_( 'SHOW TODAY BUTTON' )?><br />
					<input id="bnavigation" name="bnavigation" value="1" type="checkbox" checked="checked" ><?php echo JText::_( 'SHOW NAVIGATION BUTTONS' )?><br />
					<input id="brefresh" name="brefresh" value="1" type="checkbox"><?php echo JText::_( 'SHOW REFRESH BUTTON' )?><br /></div>
			    <label><?php echo JText::_( 'NUMBER OF MONTHS FOR NMONTHS VIEW' )?></label>
			    <div class="field"><select id="numberOfMonths" name="numberOfMonths">
                    	<option value="1">1</option>
                    	<option value="2">2</option>
                    	<option value="3">3</option>
                    	<option value="4">4</option>
                    
                    	<option value="5">5</option>
                    	<option value="6" selected="selected">6</option>
                    	<option value="7">7</option>
                    	<option value="8">8</option>
                    	<option value="9">9</option>
                    	<option value="10">10</option>
                    
                    	<option value="11">11</option>
                    	<option value="12">12</option>
                    	<option value="13">13</option>
                    	<option value="14">14</option>
                    	<option value="15">15</option>
                    	<option value="16">16</option>
                    
                    	<option value="17">17</option>
                    	<option value="18">18</option>
                    	<option value="19">19</option>
                    	<option value="20">20</option>
                    	<option value="21">21</option>
                    	<option value="22">22</option>
                    
                    	<option value="23">23</option>
                    	<option value="24">24</option>
                    </select></div>
			    <label><?php echo JText::_( 'OTHER PARAMETERS FOR NMONTHS VIEW' )?></label>
			    <div class="field">
			        <div>
					    <input name="sample0" id="sample0" value="1" onclick="javascript:showhide('sample')" type="checkbox"> 
					    <?php echo JText::_( 'SHOW TOOLTIP ON' )?> <select name="sample1" id="sample1" onchange="javascript:showhide('sample')">
					    <option value="mouseover"><?php echo JText::_( 'MOUSE OVER' )?></option>
					    <option value="click"><?php echo JText::_( 'CLICK' )?></option>
					    </select>
					</div>
					<div id="samplediv">
					    <input name="sample2" id="sample2" value="1" type="checkbox">
					    <?php echo JText::_( 'GO TO THE URL' )?> <input name="sample3" id="sample3" value="" type="text"> <?php echo JText::_( 'IN' )?> <select name="sample4" id="sample4">
					    <option value="new_window"><?php echo JText::_( 'NEW WINDOW' )?></option>
					    <option value="same_window"><?php echo JText::_( 'SAME WINDOW' )?></option>
					    </select>
					 </div>
			    </div>
			    <div style="clear:both"></div>
			    <input type="submit" class="sbtn submit" id="btnSave" value="<?php echo JText::_( 'SAVE' )?>"/>
			</form>    
			</div>
			<div id="tabs-2">
			<form action="index.php" method="post" name="tabs-2Form" id="tabs-2Form">    
				<div style="float:left;">
				    <h2><?php echo JText::_( 'EXISTING PALETTES' )?></h2>
				    <div id="paletteList"></div>
				    <p><input type="button" class="sbtn" id="btnNewP" value="<?php echo JText::_( 'ADD NEW PALETTE' )?>"/></p>
				</div>
			    <div style="float:left;width:440px;border-left:1px dotted #888;padding-left:10px;margin-left:10px;" class="mvhide" id="paletteEdition">
			        <h2><?php echo JText::_( 'NEW PALETTE' )?></h2>
			        <input type="hidden" name="paletteID" id="paletteID" value=""/>
			        <div><label for="palettename"><?php echo JText::_( 'NAME' )?></label>: <input type="text" name="palettename" id="palettename" class="required"/></div>
                    <div><?php echo JText::_( 'CLICK OR DRAG A COLOR INTO A PALETTE CONTAINER BELOW' )?> </div>               
                    <div id="colors"></div>
				    <div style="clear:both"></div>
				    <div><?php echo JText::_( 'OR ADD A COLOR FROM HERE' )?> <input type="text" id="colorpickerField1" value="8cc63f" style="border:1px solid #000;background:#8CC63F;width:50px" /> <input type="button" class="sbtn" id="btnAdd" value="<?php echo JText::_( 'ADD' )?>"/></div>
				    <div><?php echo JText::_( 'PALETTE CONTAINER' )?> (<?php echo JText::_( 'CLICK COLOR BY DEFAULT' )?>) <input type="hidden" class="paletteValid" id="paletteCounts" value="0"></div>				
				    <div id="palette" class="required" ></div><img id="recicle" src="components/com_multicalendar/views/configuration/tmpl/images/recycle_bin.png" width="100" style="padding:25px">
				    <div style="clear:both"></div>
				    <input type="submit" class="sbtn submit" id="btnSave" value="<?php echo JText::_( 'SAVE' )?>"/>
				    <input type="button" class="sbtn" id="btnCancel" value="<?php echo JText::_( 'CANCEL' )?>"/>
				</div>
				<div style="clear:both"></div>
			</form>	    
			</div>
			
		</div>	



<div class="clr"></div>

	<input type="hidden" name="task" value="save" />
	<input type="hidden" name="option" value="com_multicalendar" />
	<input type="hidden" name="id" value="<?php echo $this->calendar->id; ?>" />
	<input type="hidden" name="cid[]" value="<?php echo $this->calendar->id; ?>" />
	<input type="hidden" name="textfieldcheck" value="<?php echo $n; ?>" />
	<?php echo JHTML::_( 'form.token' ); ?>
