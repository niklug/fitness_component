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

$str = "";
$str .= '<option value="-1">Logged user\'s calendar (*user plugin required)</option>';
for ($i=0;$i<count($this->insert);$i++)
{
	$calendars = $this->insert[$i];
	$str .= '<option value="'.$calendars->id.'">'.$calendars->title.'</option>';
}
$palette = "";
for ($i=0;$i<count($this->palette);$i++)
{
	$palette .= '<option value="'.$i.'">'.$this->palette[$i]["name"].'</option>';
}
?>
<style>
body,td,th,select {
	font-family: Arial, Helvetica, sans-serif;
	font-size:12px;
}
#plugintabledata td
{
    vertical-align:top;
}
</style>

<script type="text/javascript">
			function insertCalendar(editor)
			{
				// Get the pagebreak title
				var tag = "{multicalendar";
				var obj = document.getElementById("calendarid");
				tag += ":" + obj.options[obj.selectedIndex].value;
				var obj = document.getElementById("views0");
				tag += ":" + ((obj.checked)?"1":"0");
				var obj = document.getElementById("views1");
				tag += ((obj.checked)?"1":"0");
				var obj = document.getElementById("views2");
				tag += ((obj.checked)?"1":"0");
				var obj = document.getElementById("views3");
				tag += ((obj.checked)?"1":"0");				
				var obj = document.getElementById("viewdefault");
				tag += ":" + obj.options[obj.selectedIndex].value;
				var obj = document.getElementById("start_weekday");
				tag += ":" + obj.options[obj.selectedIndex].value;
				var obj = document.getElementById("cssStyle");
				tag += ":" + obj.options[obj.selectedIndex].value;
				var obj = document.getElementById("edition");
				tag += ":" + ((obj.checked)?"1":"0");
				var obj = document.getElementById("buttons0");
				tag += ":" + ((obj.checked)?"1":"0");
				var obj = document.getElementById("buttons1");
				tag += ((obj.checked)?"1":"0");
				var obj = document.getElementById("buttons2");
				tag += ((obj.checked)?"1":"0");
				var obj = document.getElementById("numberOfMonths");
				tag += ":" + obj.options[obj.selectedIndex].value;
				var obj = document.getElementById("sample0");
				tag += ":" + ((obj.checked)?"1":"0");
				var obj = document.getElementById("sample1");
				tag += ":" + obj.options[obj.selectedIndex].value;
				var obj = document.getElementById("sample2");
				tag += ":" + ((obj.checked)?"1":"0");
				var obj = document.getElementById("sample4");
				tag += ":" + obj.options[obj.selectedIndex].value;
				var obj = document.getElementById("sample3");
				tag += ":" + obj.value;
				var obj = document.getElementById("palette");
				tag += ":" + obj.options[obj.selectedIndex].value;
				var obj = document.getElementById("otherparams");
				tag += ":" + obj.value;
				tag += "}";
				//alert(tag);
				//return;
				window.parent.jInsertEditorText(tag, '<?php echo preg_replace( '#[^A-Z0-9\-\_\[\]]#i', '', JRequest::getVar('e_name') ); ?>');
				window.parent.SqueezeBox.close()
				return false;
			}
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

		<form>
		<table width="100%" align="center" id="plugintabledata">
			<tr>
				<td class="key">
					<label for="title">
						<?php echo JText::_( 'Calendar' ); ?>
					</label>
				</td>
				<td>
					<select id="calendarid" name="calendarid" ><?php echo $str;?></select>
				</td>
			</tr>
			<tr>
				<td class="key" >
					<label for="alias">
						<?php echo JText::_( 'CALENDAR VIEWS' ); ?>
					</label>
				</td>
				<td>
				    <input id="views0" name="views0" value="viewDay" checked="checked" type="checkbox"> Day<br />
				    <input id="views1" name="views1" value="viewWeek" checked="checked" type="checkbox"> Week<br />
				    <input id="views2" name="views2" value="viewMonth" checked="checked" type="checkbox"> Month<br />
				    <input id="views3" name="views3" value="viewNMonth" checked="checked" type="checkbox"> nMonth<br />
				</td>
			</tr>
			<tr>
				<td class="key">
					<label for="alias">
						<?php echo JText::_( 'Default View' ); ?>
					</label>
				</td>
				<td>
				    <select id="viewdefault" name="viewdefault"> 
                    	<option value="day">Day</option>
                    	<option value="week">Week</option>
                    	<option value="month" selected="selected">Month</option>
                    	<option value="nMonth">nMonth</option>
                    </select>
				</td>
			</tr>
			<tr>
				<td class="key">
					<label for="alias">
						<?php echo JText::_( 'Start day of the week' ); ?>
					</label>
				</td>
				<td>
				    <select id="start_weekday" name="start_weekday">
                    	<option value="0" selected="selected">Sunday</option>
                    	<option value="1">Monday</option>                    
                    	<option value="2">Tuesday</option>
                    	<option value="3">Wednesday</option>
                    	<option value="4">Thursday</option>
                    	<option value="5">Friday</option>
                    	<option value="6">Saturday</option>
                    </select>
				</td>
			</tr>
			<tr>
				<td class="key">
					<label for="title">
						<?php echo JText::_( 'Css Style' ); ?>
					</label>
				</td>
				<td>
					<select id="cssStyle" name="cssStyle">
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
                    </select>
				</td>
			</tr>
			<tr>
				<td class="key">
					<label for="title">
						<?php echo JText::_( 'Palette Color' ); ?>
					</label>
				</td>
				<td>
					<select id="palette" name="palette" ><?php echo $palette;?></select>
				</td>
			</tr>
			<tr>
				<td class="key">
					<label for="title">
						<?php echo JText::_( 'Allow edition' ); ?>
					</label>
				</td>
				<td>
					<input name="edition" id="edition" value="1" type="checkbox">
				</td>
			</tr>
			<tr>
				<td class="key">
					<label for="title">
						<?php echo JText::_( 'Other buttons' ); ?>
					</label>
				</td>
				<td>
					<input id="buttons0" name="buttons0" value="btoday" type="checkbox">Show Today Button<br />
					<input id="buttons1" name="buttons1" value="bnavigation" type="checkbox" checked="checked" >Show Navigation Buttons<br />
					<input id="buttons2" name="buttons2" value="brefresh" type="checkbox">Show Refresh Button<br />
					
				</td>
			</tr>
			<tr>
				<td class="key">
					<label for="title">
						<?php echo JText::_( 'Number of Months for nMonths View' ); ?>
					</label>
				</td>
				<td>
					<select id="numberOfMonths" name="numberOfMonths">
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
                    </select>
				</td>
			</tr>
			<tr>
				<td class="key" colspan="2">
					<label for="title">
						<?php echo JText::_( 'Other parameters for nMonths View' ); ?>
					</label>
				</td>
			</tr>
			<tr>
				<td class="key" colspan="2">
					<div>
					    <input name="sample0" id="sample0" value="1" onclick="javascript:showhide('sample')" type="checkbox"> 
					    Show tooltip on <select name="sample1" id="sample1" onchange="javascript:showhide('sample')">
					    <option value="mouseover">mouse over</option>
					    <option value="click">click</option>
					    </select>
					</div>
					<div id="samplediv">
					    <input name="sample2" id="sample2" value="1" type="checkbox">
					    Go to the url <input name="sample3" id="sample3" value="" type="text"> in <select name="sample4" id="sample4">
					    <option value="new_window">new window</option>
					    <option value="same_window">same window</option>
					    </select>
					 </div>   
				</td>
			</tr>
			<tr>
				<td class="key" colspan="2">
					<label for="title">
						<?php echo JText::_( 'Additional Parameters' ); ?>
					</label>
					<textarea name="otherparams" id="otherparams" style="width:90%;height:35px" ></textarea>
				</td>
			</tr>
		</table>
		</form>
		<button onclick="insertCalendar();"><?php echo JText::_( 'Insert Calendar' ); ?></button>