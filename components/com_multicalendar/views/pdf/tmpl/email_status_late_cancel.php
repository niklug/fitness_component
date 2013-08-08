<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

    <head>
        <meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
        <meta content="en-us" http-equiv="Content-Language" />
        <title>Elite Fitness Training</title>
        <style type="text/css">
            a, a:link, a:visited {
                text-decoration:underline;
            }
            a:hover {
                text-decoration:none;
            }
            .lightContainer a, .lightContainer a:visited{
                color:#482104;
            }
            .lightContainer a:hover {
                color:#0e0601 !important;
            }
            .darkContainer a, .darkContainer a:visited {
                color:#790010;
            }
            .darkContainer a:hover {
                color:#790010 !important;
            }
            .readMore a:hover {
                background-color:#562704 !important;
                color: #790010 !important;
            }
            body {
                margin:0;
                background-color:#dddddd;
                color:#790010;
                font-family:Arial, Helvetica, sans-serif;
                font-size:12px;
                -webkit-text-size-adjust: none;
            }
            img {
                border: 0;
            }
            .ReadMsgBody { 
                width: 100%;
            }
            .ExternalClass {
                width: 100%;
            }
        </style>
    </head>
    <body>
<?php
require_once( JPATH_COMPONENT . '/DC_MultiViewCal/php/functions.php' );
require_once( JPATH_BASE . '/components/com_multicalendar/DC_MultiViewCal/php/list.inc.php' );

$event_id = &JRequest::getVar('event_id');

$client_id = &JRequest::getVar('client_id');

$event_data = getEmailPdfData($event_id);

if (!$client_id)
$client_id = $event_data->client_id;

$user = &JFactory::getUser($client_id);
$client_name = $user->name;

$user = &JFactory::getUser($event_data->trainer_id);
$trainer_name =  $user->name;

$date = JFactory::getDate($event_data->starttime);
$start_date =  $date->toFormat('%A, %d %b %Y');

$date = JFactory::getDate($event_data->starttime);
$start_time = $date->format('H:i');

$description = $event_data->description;

$path = JUri::base() . 'components/com_multicalendar/views/pdf/tmpl/images/';
$sitelink = JUri::base() . 'index.php?option=com_multicalendar&view=pdf&layout=email_status_late_cancel&tpml=component&event_id=' . $event_id . '&client_id=' . $client_id;
?>
        <table width="100%" cellpadding="0" cellspacing="0">
            <tr>
                <td bgcolor="#dddddd" style="padding:20px 0">
                    <table width="630" align="center" cellpadding="0" cellspacing="0" style="border-collapse:collapse; text-align:left; margin:0 auto;">
                        <tr>
                            <td style="font-family:Arial, Helvetica, sans-serif; font-size:11px; line-height:15pt; color:#df833e;">
                                <a href="<?php echo $sitelink ?>" style="color:#999999;">Having trouble viewing this email? Click here to open in your web browser.</a>
                            </td>
                        </tr>
                        <tr>
                            <td bgcolor="#ffffff" style="padding:5px; border:1px #aaaaaa solid;">
                                <!--Start Of Company Name And Slogan [row number #1]-->
                                <table width="620" align="center" cellpadding="0" cellspacing="0" style="border-collapse:collapse; margin:0 auto; text-align:left;">
                                    <tr>
                                        <td bgcolor="#78000D" style="padding:25px 20px; font-family:Arial, Helvetica, sans-serif; font-size:30px; line-height:20pt; color:#482104; font-weight:lighter;">
                                            <img alt="Elite Fitness Training" height="78" src="<?php echo $path ?>/logo.png" width="404" style="border:0; display:block; alignment-adjust: after-edge; float: right;" />
                                        </td>
                                    </tr>
                                </table>
                                <!--End Of Company Name And Slogan [row number #1]-->
                                <!--Start Of Main Content [row number #2]-->
                                <table class="darkContainer" width="620" align="center" cellpadding="0" cellspacing="0" style="border-collapse:collapse; margin:0 auto; text-align:left;">
                                    <tr>
                                        <td height="10" bgcolor="#140901" style="padding:0;" valign="top"><img alt="" height="10" src="<?php echo $path ?>/borderTop.png" width="620" vspace="0" hspace="0" style="margin:0;padding:0;border:0;display:block;" /></td>
                                    </tr>
                                    <tr>
                                        <td bgcolor="#140901" style="padding:10px 20px;">

                                            <table width="364" cellpadding="0" cellspacing="0" style="width: 580px;">
                                                <tr>
                                                    <td width="100%" style="padding:0 20px 0 0;" valign="top">
                                                        <table width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse;">
                                                            <tr>
                                                                <td style="margin:0; padding:0 0 15px 0;">
                                                                    <h1 style="padding:0; margin:0; font-family:Arial, Helvetica, sans-serif; font-size:30px; line-height:33pt; color:#FFF; font-weight:lighter; margin-bottom:0 !important;">LATE CANCELLATION!</h1>	
                                                                </td>
                                                            </tr>
                                                        </table>
                                                        <table width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse;">
                                                            <tr>
                                                                <td colspan="2" style="margin:0; padding:15px 0 15px 0; font-family:Arial, Helvetica, sans-serif; font-size:12px; line-height:15pt; color:#FFF;"><p>Hi <?php echo $client_name;?>,<br />
                                                                        <br />
                                                                        You have cancelled your appointment past the minimum notice period. Please review the Elite Fitness Terms and Conditions stated below in this email. <br />
                                                                    </p></td>
                                                            </tr>
                                                            <tr>
                                                                <td width="29%" style="margin:0; padding:15px 0 15px 0; font-family:Arial, Helvetica, sans-serif; font-size:12px; line-height:15pt; color:#FFF;">
                                                                    <p>START DATE: <br />
                                                                        START TIME: <br />
                                                                        LOCATION: </p>
                                                                    <p>APPOINTMENT: <br />
                                                                        SESSION TYPE: <br />
                                                                        SESSION FOCUS: </p>
                                                                    <p>TRAINER NAME: </p></td>
                                                                <td width="71%" style="margin:0; padding:15px 0 15px 0; font-family:Arial, Helvetica, sans-serif; font-size:12px; line-height:15pt; color:#FFF;">
                                                                    <p> <?php echo $start_date;?> <br />
                                                                        <?php echo $start_time;?> <br />
                                                                        <?php echo $event_data->location;?> </p>
                                                                    <p><?php echo $event_data->title;?> <br />
                                                                        <?php echo $event_data->session_type;?> <br />
                                                                        <?php echo $event_data->session_focus;?> </p>
                                                                    <p><?php echo $trainer_name;?> </p></td>
                                                            </tr>
                                                        </table>
                                                        <table width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse;">

                                                        </table>
                                                    </td>
                                                </tr>
                                            </table>

                                        </td>
                                    </tr>
                                    <tr>
                                        <td height="10" bgcolor="#140901" style="padding:0;" valign="bottom"><img alt="" height="10" src="<?php echo $path ?>/borderBottom.png" width="620" vspace="0" hspace="0" style="margin:0;padding:0;border:0;display:block;" /></td>
                                    </tr>
                                </table>
                                <!--End Of Main Content [row number #2]-->
                                <!--Start Of Content [row number #3]-->
                                <table class="lightContainer" width="620" align="center" cellpadding="0" cellspacing="0" style="border-collapse:collapse; margin:0 auto; text-align:left;">
                                    <tr>
                                        <td bgcolor="#78000D" style="padding:20px;">
                                            <h2 style="margin:0; padding:0; font-family:Arial, Helvetica, sans-serif; line-height:17pt; font-size:17px; color:#FFF; font-weight:lighter; margin-bottom:0 !important;">ELITE FITNESS TERMS &amp; CONDITIONS</h2>
                                            <table width="580" cellpadding="0" cellspacing="0" style="border-collapse:collapse;">
                                                <tr>
                                                    <td width="100" style="padding:15px 0 0 0; font-family:Arial, Helvetica, sans-serif; font-size:12px; line-height:15pt; color:#482104;" valign="top" rowspan="2">
                                                        <img alt="image" height="125" src="<?php echo $path ?>/clock_green.png" width="100" border="0" vspace="0" hspace="0" /></td>
                                                    <td valign="top" style="padding:10px 0 0 20px; font-family:Arial, Helvetica, sans-serif; font-size:12px; line-height:15pt; color:#CCC;">
                                                        <p>With every professional business there is a serious side...</p>
                                                        <p>Thank you for taking the time to read, understand and appreciate how we continually maintain the highest standard of customer service to all our clients. Please help us maintain this professional standard by providing us with the following small courtesies that are detailed in our Cancelation Policy.</p></td>
                                                </tr>

                                            </table>
                                        </td>
                                    </tr>
                                </table>
                                <!--End Of Content [row number #3]-->
                                <!--Start Of two Content Container [row number #4]-->

                                <!--End Of Two Column Container [row number #4]-->
                                <!--Start Of Content [row number #5]-->
                                <table class="lightContainer" width="620" align="center" cellpadding="0" cellspacing="0" style="border-collapse:#810109; margin:0 auto; text-align:left;">
                                    <tr>
                                        <td bgcolor="#78000D" style="padding:20px;">
                                            <h2 style="margin:0; padding:0; font-family:Arial, Helvetica, sans-serif; line-height:17pt; font-size:17px; color:#FFF; font-weight:lighter; margin-bottom:0 !important;">CANCELLATION POLICY</h2>
                                            <table width="580" cellpadding="0" cellspacing="0" style="border-collapse:collapse;">
                                                <tr>
                                                    <td valign="top" style="padding:10px 0 0 20px; font-family:Arial, Helvetica, sans-serif; font-size:12px; line-height:15pt; color:#CCC;"><p>24 hours notice is required to reschedule or cancel any training session or appointment.</p>
                                                        <p> If less than 24 hours notice is given, FULL FEES will apply for that appointment, unless...<br />
                                                            - your appointment time can be filled by another client.<br />
                                                            - your appointment is able to be rescheduled to another time on the same day (availability depending).</p>
                                                        <p>If you are late to your appointment, your appointment will still end at the scheduled time.</p>
                                                        <p>If you do no show up to your appointment or cancel last minute, FULL FEES apply.</p>
                                                        <p>If you are training early to mid-morning and you need to cancel or reschedule your appointment, you must do so BEFORE 3pm the preceding day to allow time to fill your appointment. Failure to do so will incur FULL SESSION FEES if you cancel too late!</p></td>
                                                </tr>

                                            </table>
                                            <h2 style="margin:0; padding:0; font-family:Arial, Helvetica, sans-serif; line-height:17pt; font-size:17px; color:#482104; font-weight:lighter; margin-bottom:0 !important;">&nbsp;</h2>
                                            <h2 style="margin:0; padding:0; font-family:Arial, Helvetica, sans-serif; line-height:17pt; font-size:17px; color:#FFF; font-weight:lighter;">3 'STRIKES' POLICY</h2>
                                            <table width="580" cellpadding="0" cellspacing="0" style="border-collapse:collapse;">
                                                <tr>
                                                    <td valign="top" style="padding:10px 0 0 20px; font-family:Arial, Helvetica, sans-serif; font-size:12px; line-height:15pt; color:#CCC;"><p>If you break any of the before mentioned policies, one 'Strike' will be recorded against you.</p>
                                                        <p>Each 'Strike' is cumulative and will remain on your record for a period of 1 month.</p>
                                                        <p>Gaining a 3rd 'Strike' will result in your training being suspended for a length of time determined by your trainer. If this continues, you will no longer be able to continue training with Elite Fitness.</p></td>
                                                </tr>

                                            </table>
                                        </td>
                                    </tr>
                                </table>
                                <!--End Of Content [row number #5]-->
                                <!--Start Of Footer [row number #6]-->
                                <table class="darkContainer" width="620" align="center" cellpadding="0" cellspacing="0" style="border-collapse:collapse; margin:0 auto; text-align:left;">
                                    <tr>
                                        <td height="10" bgcolor="#140901" style="padding:0;" valign="top"><img alt="" height="10" src="<?php echo $path ?>/borderTop.png" width="620" vspace="0" hspace="0" style="margin:0;padding:0;border:0;display:block;" /></td>
                                    </tr>
                                    <tr>
                                        <td bgcolor="#140901" style="padding:10px 20px 15px 20px; font-family:Arial, Helvetica, sans-serif; font-size:12px; line-height:15pt; color:#df833e;">

                                            <table align="center" width="580" cellpadding="0" cellspacing="0" style="border-collapse:collapse; text-align:right;">
                                                <tr>
                                                    <td width="20" style="font-family:Arial, Helvetica, sans-serif; font-size:12px; line-height:15pt; color:#df833e; margin:0; padding:0; text-align:left;" valign="top">
                                                        <img alt="image" height="16" src="<?php echo $path ?>/bulb.png" width="13" /></td>
                                                    <td width="450" style="font-family:Arial, Helvetica, sans-serif; font-size:12px; line-height:15pt; color:#FFF; margin:0; padding:0 10px 0 0; text-align:left;" valign="top">
                                                        Want to tell your friends about us?...</td>
                                                    <td width="26" valign="middle">
                                                        <a href="http://www.facebook.com/EliteTraining"><img alt="Facebook" height="16" src="<?php echo $path ?>/facebook.png" width="16" border="0" vspace="0" hspace="0" /></a></td>
                                                    <td width="26" valign="middle">
                                                        <a href="http://www.twitter.com/EliteMelbourne"><img alt="Twitter" height="16" src="<?php echo $path ?>/twitter.png" width="16" border="0" vspace="0" hspace="0" /></a></td>
                                                    <td width="26" valign="middle">
                                                        <a href="http://www.youtube.com/EliteFitnessPT">
                                                            <img alt="YouTube" height="16" src="<?php echo $path ?>/youtube.png" width="16" border="0" vspace="0" hspace="0" /></a></td>
                                                    <td width="26" valign="middle">
                                                        <a href="http://www.instagram.com/EliteMelbourne">
                                                            <img alt="Instagram" height="16" src="<?php echo $path ?>/instagram.png" width="16" border="0" vspace="0" hspace="0" /></a></td>
                                                    <td width="26" valign="middle">
                                                        <a href="https://plus.google.com/117163734672496632130/about">
                                                            <img alt="Google" height="16" src="<?php echo $path ?>/google.png" width="16" border="0" vspace="0" hspace="0" /></a></td>
                                                    <td width="26" valign="middle">
                                                        <a href="http://www.linkedin.com/pub/paul-meier/21/785/b1">
                                                            <img alt="Linkedin" height="16" src="<?php echo $path ?>/in.png" width="16" border="0" vspace="0" hspace="0" /></a></td>
                                                </tr>
                                            </table>

                                        </td>
                                    </tr>
                                    <tr>
                                        <td height="2" bgcolor="#140901" style="padding:0 0 15px 0; line-height:0;">
                                            <img alt="" height="2" src="<?php echo $path ?>/hr.png" width="620" /></td>
                                    </tr>
                                    <tr>
                                        <td bgcolor="#140901" style="padding:0px 20px 15px 20px; font-family:Arial, Helvetica, sans-serif; font-size:12px; line-height:15pt; color:#FFF;">
                                            Copyright © ELITE FITNESS TRAINING<br />
                                            <a href="www.elitefit.com.au" style="color:#FFF;">www.elitefit.com.au</a> | <a href="mailto:info@elitefit.com.au" style="color:#FFF;">info@elitefit.com.au</a> | +64 2205 0590<br />
                                            Having trouble viewing this email? <a href="<?php echo $sitelink ?>" style="color:#FFF;">Click Here</a> to open in your web browser.
                                        </td>
                                    </tr>
                                </table>
                                <!--End Of Footer [row number #6]-->
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </body>
</html>
