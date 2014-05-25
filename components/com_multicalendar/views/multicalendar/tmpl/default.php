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
 * */
// no direct access
defined('_JEXEC') or die('Restricted access');
require_once( JPATH_BASE . '/components/com_multicalendar/DC_MultiViewCal/php/list.inc.php' );
$mainframe = JFactory::getApplication();
$id = $this->params->get('the_calendar_id');
$container = "cdcmv" . $id;
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
$msg = print_scripts($id, $container, $language, $style, $views, $buttons, $edition, $sample, $otherparamsvalue, $palette, $viewdefault, $numberOfMonths, $start_weekday, false, $matches);
?>
<?php if ($this->params->def('show_page_heading', 1)) : ?>
    <h1>
        <?php echo $this->escape($this->params->get('page_heading')); ?>
    </h1>
<?php endif; ?>
<?php if ($this->params->get('show_page_title', 1)) : ?>
    <div class="componentheading<?php echo $this->escape($this->params->get('pageclass_sfx')); ?>">
        <?php echo $this->escape($this->params->get('page_title')); ?>
    </div>
<?php endif; ?>
<?php echo $msg; ?>



<?php
require_once JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_fitness' . DS . 'helpers' . DS . 'fitness.php';

$helper = new FitnessHelper();

$business_profile = $helper->getBusinessProfileId($user->id);

$business_profile_id = $business_profile['data'];

$user_id = JFactory::getUser()->id;

$is_superuser = (bool) FitnessFactory::is_superuser($user_id);
?>

<div class="contentpane<?php echo $this->escape($this->params->get('pageclass_sfx')); ?>">
    <div id="calendar_filters" style="clear: both; overflow: hidden; position: relative;margin-bottom: 5px;" class="multicalendar">
        <input type="hidden" id="business_profile_id" name="filter_business_profile_id" value="<?php echo $business_profile_id ?>"/>
        <form id="calendar_filter_form">
            <div  style="float:left;">
                <?php
                $appointments = $helper->select_filter('#__fitness_categories');
                echo $helper->generateMultipleSelect(
                        $appointments, //data
                        'appointment', //name
                        'filter_appointment', //id
                        '', //selected items
                        'Appointments', //title
                        false, //required
                        'dark_input_style', //class
                        7//size
                );
                ?>
            </div>

            <div style="float:left;">
                <?php
                $session_types = $helper->select_filter('#__fitness_session_type');
                echo $helper->generateMultipleSelect(
                        $session_types, //data
                        'session_type', //name
                        'filter_session_type', //id
                        '', //selected items
                        'Session Type', //title
                        false, //required
                        'dark_input_style', //class
                        7//size
                );
                ?>
            </div>

            <div style="float:left;">
                <?php
                $session_focuses = $helper->select_filter('#__fitness_session_focus');
                echo $helper->generateMultipleSelect(
                        $session_focuses, //data
                        'session_focus', //name
                        'filter_session_focus', //id
                        '', //selected items
                        'Session Focus', //title
                        false, //required
                        'dark_input_style', //class
                        7//size
                );
                ?>
            </div>

            <div class="drag_area" style="float:left;">
                <h6 style="color:#ccc;text-align: center;padding: 5px;">Add Appointment to Calendar</h6>
                <ul style="height: auto;">
                    <?php
                    foreach ($appointments as $appointment) {
                        if ($helper->eventCalendarFrontendReadonly($appointment->id, $user_id)) {
                            continue;
                        }
                        echo '<li data-name="title" data-value="' . $appointment->id . '" class="drag_data" title="' . $appointment->name . '" 
                          style="background-color:' . $appointment->color . '">' . $appointment->name . '</li>';
                    }
                    ?>
                </ul>

            </div>

            <div style="float:left;margin-left: 1px;padding-top: 60px;">
                <input style="margin-left: 10px;" type="button" value="Go" name="find_filtered" id="find_filtered"/>
                <input style="margin-left: 10px;" type="button" value="Reset" name="freset_filtered" id="reset_filtered"/>
            </div>
        </form>
    </div>
    <?php echo print_html($container); ?>
</div>