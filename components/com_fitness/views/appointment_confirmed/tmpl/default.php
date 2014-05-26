<?php
/**
 * @version     1.0.0
 * @package     com_fitness
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Nikolay Korban <niklug@ukr.net> - http://
 */
// no direct access
defined('_JEXEC') or die;
require_once  JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_fitness' . DS .'helpers' . DS . 'fitness.php';

$helper = new FitnessHelper();
        
$event_id = JRequest::getVar('event_id');
$client_id = JRequest::getVar('client_id');

$appointment = $helper->getEvent($event_id);
$trainer = $helper->getPrimaryTrainer($client_id);

$date = JFactory::getDate($appointment->starttime);
$start_date = $date->toFormat('%A, %d %b %Y');

$date = JFactory::getDate($appointment->starttime);

$start_time = $date->format('H:i');

?>
<hr>
<h2>YOUR APPOINTMENT HAS BEEN CONFIRMED</h2>
<hr>
<br>
<table width="400">
    <tr>
        <td width="30%">
            <b>START DATE:</b>
        </td>
        <td>
            <?php echo $start_date; ?>
        </td>
    </tr>
    <tr>
        <td>
            <b>START TIME:</b>
        </td>
        <td>
            <?php echo $start_time; ?>
        </td>
    </tr>
    <tr>
        <td>
            <b>LOCATION:</b>
        </td>
        <td>
            <?php echo $appointment->location_name; ?>
        </td>
    </tr>
    <tr>
        <td colspan="2">&nbsp;</td>
    </tr>
    <tr>
        <td>
            <b>APPOINTMENT:</b>
        </td>
        <td>
            <?php echo $appointment->appointment_name; ?>
        </td>
    </tr>
    <tr>
        <td>
            <b>SESSION TYPE:</b>
        </td>
        <td>
            <?php echo $appointment->session_type_name; ?>
        </td>
    </tr>
    <tr>
        <td>
            <b>SESSION FOCUS:</b>
        </td>
        <td>
            <?php echo $appointment->session_focus_name; ?>
        </td>
    </tr>
    <tr>
        <td colspan="2">&nbsp;</td>
    </tr>
    <tr>
        <td>
            <b>TRAINER:</b>
        </td>
        <td>
            <?php echo $trainer->name; ?>
        </td>
    </tr>
</table>
