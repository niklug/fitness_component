<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

    <head>
        <meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
        <meta content="en-us" http-equiv="Content-Language" />
        <title>Appointment Confirmation</title>
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
                color:#df833e;
            }
            .darkContainer a:hover {
                color:#ffba87 !important;
            }
            .readMore a:hover {
                background-color:#562704 !important;
                color: #fe720a !important;
            }
            body {
                margin:0;
                background-color:#dddddd;
                color:#df833e;
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


$path = JUri::base() . 'components/com_multicalendar/views/pdf/tmpl/images/';
$sitelink = JUri::base() . 'index.php?option=com_multicalendar&view=pdf&layout=email_reminder&tpml=component&event_id=' . $event_id;
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
                                        <td bgcolor="#e76708" style="padding:25px 20px; font-family:Arial, Helvetica, sans-serif; font-size:30px; line-height:20pt; color:#482104; font-weight:lighter;">
                                            <img alt="Elite Fitness Training" height="78" src="<?php echo $path ?>logo.png" width="404" style="border:0; display:block; alignment-adjust: after-edge; float: right;" />
                                        </td>
                                    </tr>
                                </table>
                                <!--End Of Company Name And Slogan [row number #1]-->
                                <!--Start Of Main Content [row number #2]-->
                                <table class="darkContainer" width="620" align="center" cellpadding="0" cellspacing="0" style="border-collapse:collapse; margin:0 auto; text-align:left;">
                                    <tr>
                                        <td height="10" bgcolor="#140901" style="padding:0;" valign="top"><img alt="" height="10" src="<?php echo $path ?>borderTop.png" width="620" vspace="0" hspace="0" style="margin:0;padding:0;border:0;display:block;" /></td>
                                    </tr>
                                    <tr>
                                        <td bgcolor="#140901" style="padding:10px 20px;">

                                            <table width="364" cellpadding="0" cellspacing="0" style="width: 580px;">
                                                <tr>
                                                    <td width="100%" style="padding:0 20px 0 0;" valign="top">
                                                        <table width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse;">
                                                            <tr>
                                                                <td style="margin:0; padding:0 0 15px 0;">
                                                                    <h1 style="padding:0; margin:0; font-family:Arial, Helvetica, sans-serif; font-size:40px; line-height:33pt; color:#df833e; font-weight:lighter; margin-bottom:0 !important;">Appointment Confirmation!</h1>	
                                                                </td>
                                                            </tr>
                                                        </table>
                                                        <table width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse;">
                                                            <tr>
                                                                <td style="margin:0; padding:15px 0 15px 0; font-family:Arial, Helvetica, sans-serif; font-size:12px; line-height:15pt; color:#df833e;"><p>Hi <?php echo $client_name;?>, your appointment details are as follows...</p>
                                                                    <p>START DATE: <?php echo $start_date;?> <br />
                                                                        START TIME: <?php echo $start_time;?><br />
                                                                        LOCATION: <?php echo $event_data->location;?></p>
                                                                    <p>APPOINTMENT: <?php echo $event_data->title;?><br />
                                                                        SESSION TYPE: <?php echo $event_data->session_type;?><br />
                                                                        SESSION FOCUS: <?php echo $event_data->session_focus;?></p>
                                                                    <p>TRAINER NAME: <?php echo $trainer_name;?></p></td>
                                                            </tr>
                                                        </table>
                                                        <table width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse;">
                                                            <tr>
                                                                <td class="readMore" width="160" height="22" bgcolor="#241002" valign="middle" style="padding:0px; font-family:Arial, Helvetica, sans-serif; font-size:12px; line-height:100%; color:#df833e; text-align:center;">
                                                                    <a href="<?php echo JUri::base() ?>index.php?option=com_multicalendar&task=confirm_email&event_id=<?php echo base64_encode($event_id) ?>" style="display:block; text-decoration:none; height:22px; line-height:22px; color:#b65106;">CLICK HERE TO CONFIRM</a>
                                                                </td>
                                                                <td width="396">&nbsp;</td>
                                                            </tr>
                                                        </table>
                                                    </td>
                                                </tr>
                                            </table>

                                        </td>
                                    </tr>
                                    <tr>
                                        <td height="10" bgcolor="#140901" style="padding:0;" valign="bottom"><img alt="" height="10" src="<?php echo $path ?>borderBottom.png" width="620" vspace="0" hspace="0" style="margin:0;padding:0;border:0;display:block;" /></td>
                                    </tr>
                                </table>
                                <!--End Of Main Content [row number #2]-->
                                <!--Start Of Content [row number #3]-->
                                <table class="lightContainer" width="620" align="center" cellpadding="0" cellspacing="0" style="border-collapse:collapse; margin:0 auto; text-align:left;">
                                    <tr>
                                        <td bgcolor="#e76708" style="padding:20px;">
                                            <h2 style="margin:0; padding:0; font-family:Arial, Helvetica, sans-serif; line-height:17pt; font-size:17px; color:#482104; font-weight:lighter; margin-bottom:0 !important;">What you need to know (and do) before your appointment...</h2>
                                            <table width="580" cellpadding="0" cellspacing="0" style="border-collapse:collapse;">
                                                <tr>
                                                    <td width="100" style="padding:15px 0 0 0; font-family:Arial, Helvetica, sans-serif; font-size:12px; line-height:15pt; color:#482104;" valign="top" rowspan="2">
                                                        <img alt="image" height="125" src="<?php echo $path ?>icon6.png" width="100" border="0" vspace="0" hspace="0" /></td>
                                                    <td valign="top" style="padding:10px 0 0 20px; font-family:Arial, Helvetica, sans-serif; font-size:12px; line-height:15pt; color:#482104;">
                                                        <p>Make sure you bring your workout clothes and appropriate shoes with you! You should also bring a sweat towel and water bottle to stay hydrated.<br />
                                                            This will keep unnecessary trips to the water fountain to a minimum and reduce breaks in the workout.</p>
                                                        <p>Don't forget to arrive 10 minutes early and warm up before your scheduled appointment time. Doing so will maximise training time with your coach!</p>
                                                        <p>If you are scheduled for an assessment, please DO NOT perform a warm up!</p></td>
                                                </tr>

                                            </table>
                                        </td>
                                    </tr>
                                </table>
                                <!--End Of Content [row number #3]-->
                                <!--Start Of two Content Container [row number #4]-->
                                <table class="darkContainer" width="620" align="center" cellpadding="0" cellspacing="0" style="border-collapse:collapse; margin:0 auto; text-align:left;">
                                    <tr>
                                        <td height="10" bgcolor="#140901" style="padding:0;" valign="top"><img alt="" height="10" src="<?php echo $path ?>borderTop.png" width="620" vspace="0" hspace="0" style="margin:0;padding:0;border:0;display:block;" /></td>
                                    </tr>
                                    <tr>
                                        <td bgcolor="#140901" style="padding:0 20px;">

                                            <table width="580" cellpadding="0" cellspacing="0" style="border-collapse:collapse;">
                                                <tr>
                                                    <td width="360" height="10" style="height:10px; max-height:10px; line-height:0;padding:0;margin:0;">&nbsp;
                                                    </td>
                                                    <td width="10" height="10" style="height:10px; max-height:10px; line-height:0;padding:0;margin:0;">&nbsp;
                                                    </td>
                                                    <td width="8" valign="middle" style="height:8px; max-height:8px; line-height:0;padding:0;margin:0;">&nbsp;
                                                    </td>
                                                    <td width="1" height="10" bgcolor="#090400" style="width:1px; max-width:1px; line-height:0;padding:0;margin:0;" valign="top">
                                                        <img alt="" height="10" src="<?php echo $path ?>middleLineTop1.png" width="1" border="0" vspace="0" hspace="0" /></td>
                                                    <td width="1" height="10" bgcolor="#231002" style="width:1px; max-width:1px; line-height:0;padding:0;margin:0;" valign="top">
                                                        <img alt="" height="10" src="<?php echo $path ?>middleLineTop2.png" width="1" border="0" vspace="0" hspace="0" /></td>
                                                    <td width="200"  height="10" style="height:10px; max-height:10px; line-height:0;padding:0;margin:0;">&nbsp;
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <!--Start Of Left Column-->
                                                    <td width="360" valign="top">
                                                        <!--Start Of Content #1 in the Left Column [Don't delete or duplicate, rather delete or duplicate the next content]-->

                                                        <table width="360" cellpadding="0" cellspacing="0" style="border-collapse:collapse;">
                                                            <tr>
                                                                <td style="padding:0 0 10px 0;">
                                                                    <h2 style="margin:0; padding:0; font-family:Arial, Helvetica, sans-serif; line-height:17pt; font-size:17px; color:#df833e; font-weight:lighter; margin-bottom:0 !important;">Appointment Calendar &amp; Workout Planning</h2>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>

                                                                    <table width="360" cellpadding="0" cellspacing="0" style="border-collapse:collapse;">
                                                                        <tr>
                                                                            <td width="50" valign="middle">
                                                                                <img alt="image" height="50" src="<?php echo $path ?>planner.png" width="50"  border="0" vspace="0" hspace="0" /></td>
                                                                            <td style="padding:0 0 0 20px; font-family:Arial, Helvetica, sans-serif; font-size:12px; line-height:15pt; color:#df833e;" valign="top">
                                                                                Dont forget to login to your VIP Client account to access your online Appointment Calendar.<br />
                                                                                From here you can view your entire upcoming schedule including future workout planning. You can also schedule your own training sessions!</td>
                                                                        </tr>
                                                                    </table>

                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td valign="top" style="padding:15px 0 0 70px; font-family:Arial, Helvetica, sans-serif; font-size:12px; line-height:15pt; color:#df833e;">
                                                                    <table width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse;">
                                                                        <tr>
                                                                            <td class="readMore" width="75" height="22" bgcolor="#241002" valign="middle" style="padding:0px ; font-family:Arial, Helvetica, sans-serif; font-size:12px; line-height:100%; color:#df833e; text-align:center;">
                                                                                <a href="#" style="display:block; text-decoration:none; height:22px; line-height:22px; color:#b65106;">LOGIN</a>
                                                                            </td>
                                                                            <td>&nbsp;</td>
                                                                        </tr>
                                                                    </table>
                                                                </td>
                                                            </tr>
                                                        </table>
                                                        <!--End Of Content #1 in the Left Column-->
                                                        <!--Start Of Content #2 in the Left Column [you can delete/ duplicate this content row]-->
                                                        <table width="360" cellpadding="0" cellspacing="0" style="border-collapse:collapse;">
                                                            <tr>
                                                                <td style="padding:18px 0 10px 0;">
                                                                    <h2 style="margin:0; padding:0; font-family:Arial, Helvetica, sans-serif; line-height:17pt; font-size:17px; color:#df833e; font-weight:lighter; margin-bottom:0 !important;">Nutrition Planning &amp; Daily Nutrition Diary</h2>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>

                                                                    <table width="360" cellpadding="0" cellspacing="0" style="border-collapse:collapse;">
                                                                        <tr>
                                                                            <td width="50" valign="middle">
                                                                                <img alt="image" height="50" src="<?php echo $path ?>diary.png" width="50" border="0" vspace="0" hspace="0" /></td>
                                                                            <td style="padding:0 0 0 20px; font-family:Arial, Helvetica, sans-serif; font-size:12px; line-height:15pt; color:#df833e;" valign="top">Review your nutrition plan on a regular basis and fuel your body to ignite the fat burning and muscle building process. Allow your trainer to guide you by making daily nutrition diary entries.</td>
                                                                        </tr>
                                                                    </table>

                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td valign="top" style="padding:15px 0 0 70px; font-family:Arial, Helvetica, sans-serif; font-size:12px; line-height:15pt; color:#df833e;">
                                                                    <table width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse;">
                                                                        <tr>
                                                                            <td class="readMore" width="75" height="22" bgcolor="#241002" valign="middle" style="padding:0px ; font-family:Arial, Helvetica, sans-serif; font-size:12px; line-height:100%; color:#df833e; text-align:center;">
                                                                                <a href="#" style="display:block; text-decoration:none; height:22px; line-height:22px; color:#b65106;">LOGIN</a>
                                                                            </td>
                                                                            <td>&nbsp;</td>
                                                                        </tr>
                                                                    </table>
                                                                </td>
                                                            </tr>
                                                        </table>
                                                        <!--End Of Content #2 in the Left Column-->
                                                    </td>
                                                    <!--End Of Left Column-->
                                                    <td width="10">&nbsp;</td>
                                                    <td width="8" valign="middle">
                                                        <img alt="" height="355" src="<?php echo $path ?>middleLineShadow.png" width="8"  border="0" vspace="0" hspace="0" /></td>
                                                    <td width="1" bgcolor="#090400" style="width:1px; max-width:1px; line-height:0;padding:0;margin:0;">&nbsp;
                                                    </td>
                                                    <td width="1" bgcolor="#231002" style="width:1px; max-width:1px; line-height:0;padding:0;margin:0;">&nbsp;
                                                    </td>
                                                    <!--Start Of Right Sidebar-->
                                                    <td width="200" valign="top">

                                                        <table width="200" cellpadding="0" cellspacing="0" style="border-collapse:collapse; font-family:Arial, Helvetica, sans-serif; font-size:12px; line-height:15pt; color:#df833e;">
                                                            <tr>
                                                                <td style="padding:0 0 15px 20px;">
                                                                    <h2 style="margin:0; padding:0; font-family:Arial, Helvetica, sans-serif; line-height:17pt; font-size:17px; color:#df833e; font-weight:lighter; margin-bottom:0 !important;">Useful  Links...</h2>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td height="1" valign="top" style="padding:0 0 10px 0; line-height:0;">
                                                                    <img alt="" height="1" src="<?php echo $path ?>hLine.png" width="200" border="0" vspace="0" hspace="0" /></td>
                                                            </tr>
                                                            <tr>
                                                                <td style="padding:0 0 10px 20px;">
                                                                    <a href="www.elitefit.com.au/index.php/train-elite/elite-progression" style="color:#df833e;">Elite Fitness Progression
                                                                    </a>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td height="1" valign="top" style="padding:0 0 10px 0; line-height:0;">
                                                                    <img alt="" height="1" src="<?php echo $path ?>hLine.png" width="200" border="0" vspace="0" hspace="0" /></td>
                                                            </tr>
                                                            <tr>
                                                                <td style="padding:0 0 10px 20px;">
                                                                    <a href="www.elitefit.com.au/index.php/team-elite/semi-private-training" style="color:#df833e;">Semi-Private Training</a>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td height="1" valign="top" style="padding:0 0 10px 0; line-height:0;">
                                                                    <img alt="" height="1" src="<?php echo $path ?>hLine.png" width="200" border="0" vspace="0" hspace="0" /></td>
                                                            </tr>
                                                            <tr>
                                                                <td style="padding:0 0 10px 20px;">
                                                                    <a href="www.elitefit.com.au/index.php/train-elite/biosignature-modulation" style="color:#df833e;">BioSignature Modulation</a>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td height="1" valign="top" style="padding:0 0 10px 0; line-height:0;">
                                                                    <img alt="" height="1" src="<?php echo $path ?>hLine.png" width="200" border="0" vspace="0" hspace="0" /></td>
                                                            </tr>
                                                            <tr>
                                                                <td style="padding:0 0 10px 20px;">
                                                                    <a href="www.elitefit.com.au/index.php/elite-online" style="color:#df833e;">Transformation Programmes
                                                                    </a>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td height="1" valign="top" style="padding:0 0 10px 0; line-height:0;">
                                                                    <img alt="" height="1" src="<?php echo $path ?>hLine.png" width="200" border="0" vspace="0" hspace="0" /></td>
                                                            </tr>
                                                            <tr>
                                                                <td valign="top" style="padding:5px 0 10px 20px; font-size:8pt; line-height:100%; color:#5e2b06;">
                                                                    Advertisement
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td valign="top" style="padding:0 0 0 20px;">
                                                                    <a href="www.elitefit.com.au/index.php/shop">
                                                                        <img alt="image" height="100" src="<?php echo $path ?>advertisment.png" width="180" border="0" vspace="0" hspace="0" /></a></td>
                                                            </tr>
                                                        </table>
                                                    </td>
                                                    <!--End Of Right Sidebar-->
                                                </tr>
                                                <tr>
                                                    <td width="360" height="10" style="height:10px; max-height:10px; line-height:0;padding:0;margin:0;">&nbsp;
                                                    </td>
                                                    <td width="10" height="10" style="height:10px; max-height:10px; line-height:0;padding:0;margin:0;">&nbsp;
                                                    </td>
                                                    <td width="8" valign="middle" style="height:8px; max-height:8px; line-height:0;padding:0;margin:0;">&nbsp;
                                                    </td>
                                                    <td width="1" height="10" bgcolor="#090400" style="width:1px; max-width:1px; line-height:0;padding:0;margin:0;" valign="top">
                                                        <img alt="" height="10" src="<?php echo $path ?>middleLineBottom1.png" width="1" border="0" vspace="0" hspace="0" /></td>
                                                    <td width="1" height="10" bgcolor="#231002" style="width:1px; max-width:1px; line-height:0;padding:0;margin:0;" valign="top">
                                                        <img alt="" height="10" src="<?php echo $path ?>middleLineBottom2.png" width="1" border="0" vspace="0" hspace="0" /></td>
                                                    <td width="200"  height="10" style="height:10px; max-height:10px; line-height:0;padding:0;margin:0;">&nbsp;
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td height="10" bgcolor="#140901" style="padding:0;" valign="bottom"><img alt="" height="10" src="<?php echo $path ?>borderBottom.png" width="620" vspace="0" hspace="0" style="margin:0;padding:0;border:0;display:block;" /></td>
                                    </tr>
                                </table>
                                <!--End Of Two Column Container [row number #4]-->
                                <!--Start Of Content [row number #5]-->
                                <table class="lightContainer" width="620" align="center" cellpadding="0" cellspacing="0" style="border-collapse:collapse; margin:0 auto; text-align:left;">
                                    <tr>
                                        <td bgcolor="#e76708" style="padding:20px;">
                                            <h2 style="margin:0; padding:0; font-family:Arial, Helvetica, sans-serif; line-height:17pt; font-size:17px; color:#482104; font-weight:lighter; margin-bottom:0 !important;">CANCELLATION POLICY</h2>
                                            <table width="580" cellpadding="0" cellspacing="0" style="border-collapse:collapse;">
                                                <tr>
                                                    <td valign="top" style="padding:10px 0 0 20px; font-family:Arial, Helvetica, sans-serif; font-size:12px; line-height:15pt; color:#482104;"><p>24 hours notice is required to reschedule or cancel any training session or appointment.</p>
                                                        <p> If less than 24 hours notice is given, FULL FEES will apply for that appointment, unless...<br />
                                                            - your appointment time can be filled by another client.<br />
                                                            - your appointment is able to be rescheduled to another time on the same day (availability depending).</p>
                                                        <p>If you are late to your appointment, your appointment will still end at the scheduled time.</p>
                                                        <p>If you do no show up to your appointment or cancel last minute, FULL FEES apply.</p>
                                                        <p>If you are training early to mid-morning and you need to cancel or reschedule your appointment, you must do so BEFORE 3pm the preceding day to allow time to fill your appointment. Failure to do so will incur FULL SESSION FEES if you cancel too late!</p></td>
                                                </tr>

                                            </table>
                                            <h2 style="margin:0; padding:0; font-family:Arial, Helvetica, sans-serif; line-height:17pt; font-size:17px; color:#482104; font-weight:lighter; margin-bottom:0 !important;">&nbsp;</h2>
                                            <h2 style="margin:0; padding:0; font-family:Arial, Helvetica, sans-serif; line-height:17pt; font-size:17px; color:#482104; font-weight:lighter;">3 'STRIKES' POLICY</h2>
                                            <table width="580" cellpadding="0" cellspacing="0" style="border-collapse:collapse;">
                                                <tr>
                                                    <td valign="top" style="padding:10px 0 0 20px; font-family:Arial, Helvetica, sans-serif; font-size:12px; line-height:15pt; color:#482104;"><p>If you break any of the before mentioned policies, one 'Strike' will be recorded against you.</p>
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
                                        <td height="10" bgcolor="#140901" style="padding:0;" valign="top"><img alt="" height="10" src="<?php echo $path ?>borderTop.png" width="620" vspace="0" hspace="0" style="margin:0;padding:0;border:0;display:block;" /></td>
                                    </tr>
                                    <tr>
                                        <td bgcolor="#140901" style="padding:10px 20px 15px 20px; font-family:Arial, Helvetica, sans-serif; font-size:12px; line-height:15pt; color:#df833e;">

                                            <table align="center" width="580" cellpadding="0" cellspacing="0" style="border-collapse:collapse; text-align:right;">
                                                <tr>
                                                    <td width="20" style="font-family:Arial, Helvetica, sans-serif; font-size:12px; line-height:15pt; color:#df833e; margin:0; padding:0; text-align:left;" valign="top">
                                                        <img alt="image" height="16" src="<?php echo $path ?>bulb.png" width="13" /></td>
                                                    <td width="450" style="font-family:Arial, Helvetica, sans-serif; font-size:12px; line-height:15pt; color:#df833e; margin:0; padding:0 10px 0 0; text-align:left;" valign="top">
                                                        Want to tell your friends about us?...</td>
                                                    <td width="26" valign="middle">
                                                        <a href="http://www.facebook.com/EliteTraining"><img alt="Facebook" height="16" src="<?php echo $path ?>facebook.png" width="16" border="0" vspace="0" hspace="0" /></a></td>
                                                    <td width="26" valign="middle">
                                                        <a href="http://www.twitter.com/EliteMelbourne"><img alt="Twitter" height="16" src="<?php echo $path ?>twitter.png" width="16" border="0" vspace="0" hspace="0" /></a></td>
                                                    <td width="26" valign="middle">
                                                        <a href="http://www.youtube.com/EliteFitnessPT">
                                                            <img alt="YouTube" height="16" src="<?php echo $path ?>youtube.png" width="16" border="0" vspace="0" hspace="0" /></a></td>
                                                    <td width="26" valign="middle">
                                                        <a href="http://www.instagram.com/EliteMelbourne">
                                                            <img alt="Instagram" height="16" src="<?php echo $path ?>instagram.png" width="16" border="0" vspace="0" hspace="0" /></a></td>
                                                    <td width="26" valign="middle">
                                                        <a href="https://plus.google.com/117163734672496632130/about">
                                                            <img alt="Google" height="16" src="<?php echo $path ?>google.png" width="16" border="0" vspace="0" hspace="0" /></a></td>
                                                    <td width="26" valign="middle">
                                                        <a href="http://www.linkedin.com/pub/paul-meier/21/785/b1">
                                                            <img alt="Linkedin" height="16" src="<?php echo $path ?>in.png" width="16" border="0" vspace="0" hspace="0" /></a></td>
                                                </tr>
                                            </table>

                                        </td>
                                    </tr>
                                    <tr>
                                        <td height="2" bgcolor="#140901" style="padding:0 0 15px 0; line-height:0;">
                                            <img alt="" height="2" src="<?php echo $path ?>hr.png" width="620" /></td>
                                    </tr>
                                    <tr>
                                        <td bgcolor="#140901" style="padding:0px 20px 15px 20px; font-family:Arial, Helvetica, sans-serif; font-size:12px; line-height:15pt; color:#df833e;">
                                            Copyright © ELITE FITNESS TRAINING<br />
                                            <a href="www.elitefit.com.au" style="color:#df833e;">www.elitefit.com.au</a> | <a href="mailto:info@elitefit.com.au" style="color:#df833e;">info@elitefit.com.au</a> | +64 2205 0590<br />
                                            Having trouble viewing this email? <a href="<?php echo $sitelink ?>" style="color:#df833e;">Click Here</a> to open in your web browser.
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






























































<!--
<html xmlns="http://www.w3.org/1999/xhtml" >
  <head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
  </head>
    <body>
<?php
defined('_JEXEC') or die('Restricted access');
require_once( JPATH_COMPONENT . '/DC_MultiViewCal/php/functions.php' );
require_once( JPATH_BASE . '/components/com_multicalendar/DC_MultiViewCal/php/list.inc.php' );

$event_id = &JRequest::getVar('event_id');

$client_id = &JRequest::getVar('client_id');

$event_data = getEmailPdfData($event_id);

if (!$client_id)
    $client_id = $event_data->client_id;

$width = '900px';

$height = '1500px';
?>   

        <div  style="
              background-image: url('<?php echo JUri::base() ?>components/com_multicalendar/DC_MultiViewCal/css/<?php echo $path ?>email_reminder_bgr.png');
              position: absolute;
              width:<?php echo $width; ?>;
              height:<?php echo $height; ?>;
              background-size:<?php echo $width; ?>;
              background-repeat: no-repeat;
              font-family: Times New Roman;
              ">

           
            <div style="margin-left: 40px; margin-top: 0; padding: 10px; width: 615px;font-size:14px;">
                  
              
                 <table style="width: 500px;margin-top: 450px;position:relative;font-size: 14px;"  border="0">
                    <tbody>
                        <tr>
                            <td style="color:#ffffff;" width="110px">Client Name:</td>
                            <td style="font-weight: bold;color:#D14F16;padding-left: 10px;">
                                <i>
<?php
$user = &JFactory::getUser($client_id);
echo $user->name;
?>
                                </i>
                            </td>
                        </tr>
                        <tr>
                            <td style="color:#ffffff;">Trainer Name:</td>
                            <td style="font-weight: bold;color:#D14F16;padding-left: 10px;">
                                <i>
<?php
$user = &JFactory::getUser($event_data->trainer_id);
echo $user->name;
?>
                                </i>
                            </td>
                        </tr>
                        <tr>
                            <td style="color:#ffffff;">Location:</td>
                            <td style="font-weight: bold;color:#D14F16;padding-left: 10px;">
                                <i>
<?php
echo $event_data->location;
?>
                                </i>
                            </td>
                        </tr>
                    </tbody>
                </table>
                    

                 <table style="width: 675px; margin-top: 5px;font-size: 14px;" border="0">
                    <tbody>
                        <tr>
                            <td style="width: 110px;color:#ffffff;">Start Date:</td>
                            <td style="font-weight: bold;color:#D14F16;padding-left: 10px;">
                                <i>
<?php
$date = JFactory::getDate($event_data->starttime);
echo $date->toFormat('%A, %d %b %Y');
?>
                                </i>
                            </td>
                            <td style="padding-left: 50px;color:#ffffff;">Finish Date:</td>
                            <td style="font-weight: bold;width:200px;color:#D14F16;padding-left: 10px;">
                                <i>
<?php
$date = JFactory::getDate($event_data->endtime);
echo $date->toFormat('%A, %d %b %Y');
?>
                                </i>
                            </td>
                        </tr>
                        <tr>
                            <td style="color:#ffffff;">Start Time:</td>
                            <td style="font-weight: bold;width:200px;color:#D14F16;padding-left: 10px;">
                                <i>
<?php
$date = JFactory::getDate($event_data->starttime);
echo $date->format('H:i:s');
?>
                                </i>
                            </td>
                            <td style="padding-left: 50px;color:#ffffff;">Finish Time:</td>
                            <td style="font-weight: bold;width:200px;color:#D14F16;padding-left: 10px;">
                                <i>
<?php
$date = JFactory::getDate($event_data->endtime);
echo $date->format('H:i:s');
?>
                                </i>
                            </td>
                        </tr>
                    </tbody>
                </table>
                    
                <table style="width: 500px; margin-top: 5px;font-size: 14px;"  border="0">
                    <tbody>
                        <tr>
                            <td style="color:#ffffff;" width="110px;">Appointment:</td>
                            <td style="font-weight: bold;color:#D14F16;padding-left: 10px;">
                                <i>
<?php
echo $event_data->title;
?>
                                </i>
                            </td>
                        </tr>
                        <tr>
                            <td style="color:#ffffff;">Session Type:</td>
                            <td style="font-weight: bold;color:#D14F16;padding-left: 10px;">
                                <i>
<?php
echo $event_data->session_type;
?>
                                </i>
                            </td>
                        </tr>
                        <tr>
                            <td style="color:#ffffff;">Session Focus:</td>
                            <td style="font-weight: bold;color:#D14F16;padding-left: 10px;">
                                <i>
<?php
echo $event_data->session_focus;
?>
                                </i>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div style="font-size: 14px;  color: #FFFFFF;  margin-left: 40px; margin-top: 0;font-size: 14px; padding: 10px; width: 615px;">
                (please arrive at least 5 mins before your scheduled appointment time. If you are training or doing anything physical in your appointment, please be sure to do a warm-up by walking, jogging or rowing for 5-10 mins - unless otherwise instructed not to do so)
            </div>
            
            
                <table style="width: 100%; margin-top: 25px;"  border="0">
                    <tr>
                        <td align="center">
                            <a target="_blank" href="<?php echo JUri::base() ?>index.php?option=com_multicalendar&task=confirm_email&event_id=<?php echo base64_encode($event_id) ?>" 
                                 style="
                                 display: block;
                                 height: 68px;
                                 margin-right: 146px;
                                 width: 361px;
                              "></a>  
                        </td>
                    </tr>
                </table>
                
            
            
            
            <div style="
            color: #FFFFFF;
            font-size: 12px;
            margin-left: 40px;
            margin-top: 87px;
            padding: 10px;
            width: 615px;
           ">
                <p style="color:#D14F16;margin: 5px 0;">CANCELLATION POLICY</p>
                <p style="margin: 5px 0;">24 hours notice is required to reschedule or cancel any training session or appointment.</p>
                <p style="margin: 5px 0;">If less than 24 hours notice is given, FULL FEES will apply for that appointment, unless...</p>
                <p style="margin: 5px 0;"> - your appointment time can be filled by another client.<br /> - your appointment is able to be rescheduled to another time on the same day (availability depending).</p>
                <p style="margin: 5px 0;">If you are late to your appointment, your appointment will still end at the scheduled time.<br /><br />If you do no show up to your appointment or cancel last minute, FULL FEES apply.</p>
                <p style="margin: 5px 0;">If you are training early to mid-morning and you need to cancel or reschedule your appointment, you must do so BEFORE 3pm the preceding day to allow time to fill your appointment. Failure to do so will incur FULL SESSION FEES if you cancel too late!</p>
                <p style="color:#D14F16;margin: 5px 0;">3 ‘STRIKES’ POLICY</p>
                <p style="margin: 5px 0;">If you break any of the before mentioned policies, one ‘Strike' will be recorded against you.</p>
                <p style="margin: 5px 0;">Each 'Strike' is cumulative and will remain on your record for a period of 1 month.</p>
                <p style="margin: 5px 0;">Gaining a 3rd ’Strike' will result in your training being suspended for a length of time determined by your trainer. If this continues, you will no longer be able to continue training with Elite Fitness.</p>
            </div>
        </div>
        
    </body>
</html>

-->
