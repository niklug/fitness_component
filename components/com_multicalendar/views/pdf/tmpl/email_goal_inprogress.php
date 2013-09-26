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
                color:#000;
            }
            .lightContainer a:hover {
                color:#0e0601 !important;
            }
            .darkContainer a, .darkContainer a:visited {
                color:#CCC;
            }
            .darkContainer a:hover {
                color:#FFA600 !important;
            }
            .readMore a:hover {
                background-color:#5D3B01 !important;
                color: #FFA600 !important;
            }
            body {
                margin:0;
                background-color:#dddddd;
                color:#FFA600;
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

$id = &JRequest::getVar('id');
$goal_type = JRequest::getVar('goal_type');// 1-> Primary Goal; 2 -> Mini Goal
$layout = JRequest::getVar('layout');

$goal_data = getGoalData($id, $goal_type);

$user = &JFactory::getUser($goal_data->user_id);
$client_name = $user->name;

$user = &JFactory::getUser($goal_data->primary_trainer);
$trainer_name =  $user->name;

$date = JFactory::getDate($goal_data->start_date);
$date_created =  $date->toFormat('%A, %d %b %Y') ;

$date = JFactory::getDate($goal_data->deadline);
$deadline =  $date->toFormat('%A, %d %b %Y') ;

$description = $goal_data->description;

$path = JUri::base() . 'components/com_multicalendar/views/pdf/tmpl/images/';
$sitelink = JUri::base() . 'index.php?option=com_multicalendar&view=pdf&layout=' . $layout . '&tpml=component&id=' . $id;
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
                                        <td bgcolor="#FFA600" style="padding:25px 20px; font-family:Arial, Helvetica, sans-serif; font-size:30px; line-height:20pt; color:#482104; font-weight:lighter;">
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
                                                                <td style="margin:0; padding:0 0 15px 0;"><h1 style="padding:0; margin:0; font-family:Arial, Helvetica, sans-serif; font-size:30px; line-height:33pt; color:#FFF; font-weight:lighter; margin-bottom:0 !important;">NEW GOAL 'IN PROGRESS'</h1>	
                                                                </td>
                                                            </tr>
                                                        </table>
                                                        <table width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse;">
                                                            <tr>
                                                                <td colspan="2" style="margin:0; padding:15px 0 15px 0; font-family:Arial, Helvetica, sans-serif; font-size:12px; line-height:15pt; color:#CCC;"><p>Hi <?php echo $client_name;?>,</p>
                                                                    <p>You have now begun a brand new training period and training focus with your sights firmly set on  new and exciting challenges... train hard and good luck!</p>
                                                                    <p>Please now review your original goal details and take into account your trainers comments and action any requests or instructions.</p></td>
                                                            </tr>
                                                            <tr>
                                                                <td width="29%" style="margin:0; padding:15px 0 15px 0; font-family:Arial, Helvetica, sans-serif; font-size:12px; line-height:15pt; color:#CCC;">PRIMARY GOAL: <br />
                                                                    START DATE: <br />
                                                                    ACCOMPLISH BY: </td>
                                                                <td width="71%" style="margin:0; padding:15px 0 15px 0; font-family:Arial, Helvetica, sans-serif; font-size:12px; line-height:15pt; color:#CCC;">
                                                                    <?php echo $goal_data->category_name;?><br />
                                                                    <?php echo $date_created;?><br />
                                                                    <?php echo $deadline;?> </td>
                                                            </tr>
                                                            <tr>
                                                                <td style="margin:0; padding:15px 0 15px 0; font-family:Arial, Helvetica, sans-serif; font-size:12px; line-height:15pt; color:#CCC;">GOAL DETAILS: </td>
                                                                <td style="margin:0; padding:15px 0 15px 0; font-family:Arial, Helvetica, sans-serif; font-size:12px; line-height:15pt; color:#CCC;"><?php echo $goal_data->details;?> </td>
                                                            </tr>
                                                        </table>
                                                        <table width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse;">
                                                            <tr>
                                                                <td class="readMore" width="160" height="22" bgcolor="#241002" valign="middle" style="padding:0px; font-family:Arial, Helvetica, sans-serif; font-size:12px; line-height:100%; color:#FFA600; text-align:center;">
                                                                    <a href="#" style="display:block; text-decoration:none; height:22px; line-height:22px; color:#FFA600;">CLICK HERE TO OPEN</a>
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
                                        <td height="10" bgcolor="#140901" style="padding:0;" valign="bottom"><img alt="" height="10" src="<?php echo $path ?>/borderBottom.png" width="620" vspace="0" hspace="0" style="margin:0;padding:0;border:0;display:block;" /></td>
                                    </tr>
                                </table>
                                <!--End Of Main Content [row number #2]-->
                                <!--Start Of Content [row number #3]-->
                                <table class="lightContainer" width="620" align="center" cellpadding="0" cellspacing="0" style="border-collapse:collapse; margin:0 auto; text-align:left;">
                                    <tr>
                                        <td bgcolor="#FFA600" style="padding:20px;">
                                            <h2 style="margin:0; padding:0; font-family:Arial, Helvetica, sans-serif; line-height:17pt; font-size:17px; color:#482104; font-weight:lighter; margin-bottom:0 !important;">You've set your goals... now what?</h2>
                                            <table width="580" cellpadding="0" cellspacing="0" style="border-collapse:collapse;">
                                                <tr>
                                                    <td width="100" style="padding:15px 0 0 0; font-family:Arial, Helvetica, sans-serif; font-size:12px; line-height:15pt; color:#482104;" valign="top" rowspan="2">
                                                        <img alt="image" height="100" src="<?php echo $path ?>/goals.png" width="100" border="0" vspace="0" hspace="0" /></td>
                                                    <td valign="top" style="padding:10px 0 0 20px; font-family:Arial, Helvetica, sans-serif; font-size:12px; line-height:15pt; color:#482104;">
                                                        <p>Get focused! Talk to your trainer about ways to improve your mindset and subsequently change your lifestyle.</p>
                                                        <p>Begin by making small changes and encourage new healthy habits that will pave the way to health and fitness!</p></td>
                                                </tr>

                                            </table>
                                        </td>
                                    </tr>
                                </table>
                                <!--End Of Content [row number #3]-->
                                <!--Start Of two Content Container [row number #4]-->

                                <!--End Of Two Column Container [row number #4]-->
                                <!--Start Of Content [row number #5]-->
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
                                        <td bgcolor="#140901" style="padding:0px 20px 15px 20px; font-family:Arial, Helvetica, sans-serif; font-size:12px; line-height:15pt; color:#CCC;">
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
