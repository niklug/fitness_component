<?php
defined('_JEXEC') or die('Restricted access'); 

?>

<?php
	$cid = JRequest::getVar( 'cid', array(0), '', 'array' );
	$edit=JRequest::getVar( 'edit', true );
	JArrayHelper::toInteger($cid, array(0));

	$text = ( $edit ? JText::_( 'Edit' ) : JText::_( 'New' ) );

	JToolBarHelper::title(  JText::_( 'COMMULTICALENDAR_CPCALENDAR' ).': <small><small>[ ' . $text.' ]</small></small>', $edit ? "multicalendar-edit" : "multicalendar-new" );
	//if ($edit) { JToolBarHelper::Preview('../index.php?option=com_multicalendar&id='.$cid[0]);}
	JToolBarHelper::save();
	JToolBarHelper::apply();
	if ($edit) {
		// for existing items the button is renamed `close`
		JToolBarHelper::cancel( 'cancel', 'Close' );
	} else {
		JToolBarHelper::cancel();
	}
	if ($edit) {
		// for existing items the button is renamed `close`
		JToolBarHelper::help( 'screen.multicalendar.edit', true );
	} else {
		JToolBarHelper::help( 'screen.multicalendar.new', true );
	}
?>

<?php
JFilterOutput::objectHTMLSafe( $this->calendar, ENT_QUOTES );
?>
<link type="text/css" href="components/com_multicalendar/views/configuration/tmpl/css/cupertino/jquery-ui-1.8.20.custom.css" rel="stylesheet" />
<link type="text/css" href="components/com_multicalendar/views/configuration/tmpl/css/cupertino/jquery-ui-1.8.20.custom.css" rel="stylesheet" />

<script type="text/javascript" src="components/com_multicalendar/views/configuration/tmpl/js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="components/com_multicalendar/views/configuration/tmpl/js/jquery-ui-1.8.20.custom.min.js"></script>

<script language="javascript" type="text/javascript">
	Joomla.submitbutton = function(pressbutton) {
	    var form = document.adminForm;
		if (pressbutton == 'cancel') {
			submitform( pressbutton );
			return;
		}
		// do field validation
		if (form.title.value == "") {
			alert( "<?php echo JText::_( 'COMMULTICALENDAR_ALERT_TITLE_CONSTRAINT', true ); ?>" );
		
		} else {
		    var str = "";
		    str += "groups1=" + $("#groups1").val() + ";";
		    str += "users1=" + $("#users1").val() + ";";
		    str += "groups2=" + $("#groups2").val() + ";";
		    str += "users2=" + $("#users2").val() + ";";
		    str += "groups3=" + $("#groups3").val() + ";";
		    str += "users3=" + $("#users3").val() + ";";
		    $("#permissions").attr("value",str);
		    submitform( pressbutton );
		}
		return false;
	}
	$(document).ready(function(){
       if ($("#permissions").val()!="")
       {
           var p = $('#permissions').val().split(";");
           for (var i=0;i< p.length-1;i++)
           {
               var pair = p[i].split("=");
               var v = pair[1].split(",");
               $('#'+pair[0]+' option').each(function(index){                   
                   if (jQuery.inArray($(this).val(), v)!=-1)
                   {
                       $(this).attr("selected","selected");
                   }    
                   else
                   {
                       $(this).removeAttr("selected");    
                   }    
                   
               })
           }
       }
       $('#groups1 > option').each(function(index){ if (index==0 ) $(this).css("border-bottom","2px dotted #555"); })
       $('#groups2 > option').each(function(index){ if (index==0 || index==1 ) $(this).css("border-bottom","2px dotted #555"); })
       $('#groups3 > option').each(function(index){ if (index==0 || index==1 ) $(this).css("border-bottom","2px dotted #555"); })
    });
</script>
<style>
#published0-lbl{clear:none}
#published1-lbl{clear:none}
</style>
<form action="index.php" method="post" name="adminForm" id="adminForm">
<div class="width-100 fltlft">
	<fieldset class="adminform">
	<legend><?php echo JText::_( 'Details' ); ?></legend>
	<table class="admintable">
		<tr>
			<td width="110" class="key">
				<label for="title">
					<?php echo JText::_( 'Title' ); ?>:
				</label>
			</td>
			<td>
				<input class="inputbox" type="text" name="title" id="title" size="60" value="<?php echo $this->calendar->title; ?>" />
			</td>
		</tr>
		<tr>
			<td width="110" class="key" valign="top">
				<label for="title">
					<?php echo JText::_( 'PERMISSIONS IN FRONTEND IF EDITION IS TRUE' ); ?>:
				</label>
				<input name="permissions" id="permissions" type="hidden" value="<?php echo $this->calendar->permissions; ?>" />
			</td>
			<td>
				<div class="width-30 fltlft">
                	<fieldset id="user-groups1" class="adminform" style="border:5px">
                			<legend><?php echo JText::_('WHO CAN ADD'); ?>?</legend>
                			<?php
                			echo "<div>".JText::_('SELECT GROUPS')."<br />".JHTML::_('select.genericlist',   $this->groupsAdd, "groups1[]", 'multiple="multiple" size="6" style="width:100%"', 'value', 'text', array("1") )."</div>";			 
                			echo "<div style=\"text-align:center;clear:both\"> - <?php echo JText::_('OR'); ?> -</div>";			 
                            echo "<div>".JText::_('SELECT USERS')."<br />".JHTML::_('select.genericlist',   $this->users, "users1[]", 'multiple="multiple" size="6" style="width:100%"', 'value', 'text', array("0") )."</div>";
                            ?>
                    </fieldset>
                </div>
                <div class="width-30 fltlft">
                	<fieldset id="user-groups2" class="adminform" style="border:5px">
                			<legend><?php echo JText::_('WHO CAN EDIT'); ?>?</legend>
                			<?php
                			echo "<div>".JText::_('SELECT GROUPS')."<br />".JHTML::_('select.genericlist',   $this->groups, "groups2[]", 'multiple="multiple" size="6" style="width:100%"', 'value', 'text', array("1") )."</div>";
                			echo "<div style=\"text-align:center;clear:both\"> - <?php echo JText::_('OR'); ?> -</div>";			 
                            echo "<div>".JText::_('SELECT USERS')."<br />".JHTML::_('select.genericlist',   $this->users, "users2[]", 'multiple="multiple" size="6" style="width:100%"', 'value', 'text', array("0") )."</div>";
                            ?>
                    </fieldset>
                </div>
                <div class="width-30 fltlft">
                	<fieldset id="user-groups3" class="adminform" style="border:5px">
                			<legend><?php echo JText::_('WHO CAN DELETE'); ?>?</legend>
                			<?php
                			echo "<div>".JText::_('SELECT GROUPS')."<br />".JHTML::_('select.genericlist',   $this->groups, "groups3[]", 'multiple="multiple" size="6" style="width:100%"', 'value', 'text', array("1") )."</div>";
                			echo "<div style=\"text-align:center;clear:both\"> - <?php echo JText::_('OR'); ?> -</div>";			 
                            echo "<div>".JText::_('SELECT USERS')."<br />".JHTML::_('select.genericlist',   $this->users, "users3[]", 'multiple="multiple" size="6" style="width:100%"', 'value', 'text', array("0") )."</div>";
                            ?>
                    </fieldset>
                </div>
			</td>
		</tr>
		<tr>
			<td width="120" class="key">
				<?php echo JText::_( 'Published' ); ?>:
			</td>
			<td>
				<?php echo JHTML::_( 'select.booleanlist',  'published', 'class="inputbox"', $this->calendar->published ); ?>
			</td>
		</tr>
		
	</table>
	</fieldset>
</div>

<div class="clr"></div>

	<input type="hidden" name="task" value="save" />
	<input type="hidden" name="option" value="com_multicalendar" />
	<input type="hidden" name="id" value="<?php echo $this->calendar->id; ?>" />
	<input type="hidden" name="cid[]" value="<?php echo $this->calendar->id; ?>" />
	<input type="hidden" name="textfieldcheck" value="<?php echo $n; ?>" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>
