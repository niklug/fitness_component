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

// no direct access
defined('_JEXEC') or die('Restricted access');
require_once( JPATH_BASE.'/components/com_multicalendar/DC_MultiViewCal/php/list.inc.php' );
$mainframe = JFactory::getApplication();
$id = $this->params->get('the_calendar_id');
$container = "cdcmv".$id;
$language = $mainframe->getCfg('language');
$style = $this->params->get('cssStyle');
$views = $this->params->get('views');
$buttons = $this->params->get('buttons');
$edition = $this->params->get('edition');
$sample = $this->params->get('sample');
$otherparamsvalue = $this->params->get('otherparams');
$palette = $this->params->get('palette');
$viewdefault = $this->params->get('viewdefault');
$numberOfMonths = $this->params->get('numberOfMonths');
$start_weekday = $this->params->get("start_weekday");
$matches = array();
$msg = print_scripts($id,$container,$language,$style,$views,$buttons,$edition,$sample,$otherparamsvalue,$palette,$viewdefault,$numberOfMonths,$start_weekday,false,$matches);
?>
<?php if ($this->params->def('show_page_heading', 1)) : ?>
<h1>
	<?php echo $this->escape($this->params->get('page_heading')); ?>
</h1>
<?php endif; ?>
<?php if ($this->params->get( 'show_page_title', 1)) : ?>
<div class="componentheading<?php echo $this->escape($this->params->get('pageclass_sfx')); ?>">
	<?php echo $this->escape($this->params->get('page_title')); ?>
</div>
<?php endif; ?>
<?php echo $msg;?>
<div class="contentpane<?php echo $this->escape($this->params->get('pageclass_sfx')); ?>">
<?php echo print_html($container);?>
</div>