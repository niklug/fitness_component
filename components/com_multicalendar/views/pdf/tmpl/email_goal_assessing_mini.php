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
                color:#6633FF;
            }
            .darkContainer a:hover {
                color:#6633FF !important;
            }
            .readMore a:hover {
                background-color:#400D60 !important;
                color: #CCC !important;
            }
            body {
                margin:0;
                background-color:#dddddd;
                color:#6633FF;
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
        require_once JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_fitness' . DS . 'helpers' . DS . 'email_templates_data.php';

        $id = &JRequest::getVar('id');
        $goal_type = JRequest::getVar('goal_type'); // 1-> Primary Goal; 2 -> Mini Goal
        $layout = JRequest::getVar('layout');
        // 
        $params = array('method' => 'Goal', 'id' => $id, 'goal_type' => $goal_type, 'layout' => $layout);

        try {
            $obj = EmailTemplateData::factory($params);

            $data = $obj->processing();
        } catch (Exception $exc) {
            echo $exc->getMessage();
            die();
        }

        ?>
        <table width="100%" cellpadding="0" cellspacing="0">
            <tr>
                <td bgcolor="#dddddd" style="padding:20px 0">
                    <table width="630" align="center" cellpadding="0" cellspacing="0" style="border-collapse:collapse; text-align:left; margin:0 auto;">
                        <tr>
                            <td style="font-family:Arial, Helvetica, sans-serif; font-size:11px; line-height:15pt; color:#df833e;">
                                <a href="<?php echo $data->sitelink ?>" style="color:#999999;">Having trouble viewing this email? Click here to open in your web browser.</a>
                            </td>
                        </tr>
                        <tr>
                            <td bgcolor="#ffffff" style="padding:5px; border:1px #aaaaaa solid;">
                                <!--Start Of Company Name And Slogan [row number #1]-->
                                <table width="620" align="center" cellpadding="0" cellspacing="0" style="border-collapse:collapse; margin:0 auto; text-align:left;">
                                    <tr>
                                        <td bgcolor="#6633FF" style="padding:25px 20px; font-family:Arial, Helvetica, sans-serif; font-size:30px; line-height:20pt; color:#482104; font-weight:lighter;">
                                            <img alt="Elite Fitness Training" height="78" src="<?php echo $data->header_image  ?>" width="404" style="border:0; display:block; alignment-adjust: after-edge; float: right;" />
                                        </td>
                                    </tr>
                                </table>
                                <!--End Of Company Name And Slogan [row number #1]-->
                                <!--Start Of Main Content [row number #2]-->
                                <table class="darkContainer" width="620" align="center" cellpadding="0" cellspacing="0" style="border-collapse:collapse; margin:0 auto; text-align:left;">
                                    <tr>
                                        <td height="10" bgcolor="#140901" style="padding:0;" valign="top"><img alt="" height="10" src="<?php echo $data->path ?>/borderTop.png" width="620" vspace="0" hspace="0" style="margin:0;padding:0;border:0;display:block;" /></td>
                                    </tr>
                                    <tr>
                                        <td bgcolor="#140901" style="padding:10px 20px;">

                                            <table width="364" cellpadding="0" cellspacing="0" style="width: 580px;">
                                                <tr>
                                                    <td width="100%" style="padding:0 20px 0 0;" valign="top">
                                                        <table width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse;">
                                                            <tr>
                                                                <td style="margin:0; padding:0 0 15px 0;">
                                                                    <h1 style="padding:0; margin:0; font-family:Arial, Helvetica, sans-serif; font-size:30px; line-height:33pt; color:#FFF; font-weight:lighter; margin-bottom:0 !important;">GOAL ASSESSMENT REQUIRED</h1>	
                                                                </td>
                                                            </tr>
                                                        </table>
                                                        <table width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse;">
                                                            <tr>
                                                                <td colspan="2" style="margin:0; padding:15px 0 15px 0; font-family:Arial, Helvetica, sans-serif; font-size:12px; line-height:15pt; color:#CCC;"><p>Hi <?php echo $data->trainer_name; ?>,</p>
                                                                    <p>Your clients Mini Goal has ended and is due for assessment!</p>
                                                                    <p>Please provide comments and feedback for your client and mark this goal based on your assessment with either a 'Comlete' or 'Incomplete' score.</p></td>
                                                            </tr>
                                                            <tr>
                                                                <td width="29%" style="margin:0; padding:15px 0 15px 0; font-family:Arial, Helvetica, sans-serif; font-size:12px; line-height:15pt; color:#CCC;">CLIENT NAME:</td>
                                                                <td width="71%" style="margin:0; padding:15px 0 15px 0; font-family:Arial, Helvetica, sans-serif; font-size:12px; line-height:15pt; color:#CCC;"><?php echo $data->client_name; ?></td>
                                                            </tr>
                                                            <tr>
                                                                <td style="margin:0; padding:15px 0 15px 0; font-family:Arial, Helvetica, sans-serif; font-size:12px; line-height:15pt; color:#CCC;"><p>PRIMARY GOAL: </p>
                                                                    <p>MINI GOAL:<br />
                                                                        START DATE: <br />
                                                                        ACCOMPLISH BY: </p></td>
                                                                <td style="margin:0; padding:15px 0 15px 0; font-family:Arial, Helvetica, sans-serif; font-size:12px; line-height:15pt; color:#CCC;"><p>
                                                                    <?php echo $data->item->primary_goal_name; ?><p />
                                                                <p><?php echo $data->item->category_name; ?><br />
                                                                    <?php echo $data->date_created; ?><br />
                                                                    <?php echo $data->deadline; ?> </td>
                                                            </tr>
                                                            <tr>
                                                                <td style="margin:0; padding:15px 0 15px 0; font-family:Arial, Helvetica, sans-serif; font-size:12px; line-height:15pt; color:#CCC;">MINI GOAL DETAILS: </td>
                                                                <td style="margin:0; padding:15px 0 15px 0; font-family:Arial, Helvetica, sans-serif; font-size:12px; line-height:15pt; color:#CCC;"><?php echo $data->item->details; ?> </td>
                                                            </tr>
                                                        </table>
                                                        <table width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse;">
                                                            <tr>
                                                                <td class="readMore" width="160" height="22" bgcolor="#130036" valign="middle" style="padding:0px; font-family:Arial, Helvetica, sans-serif; font-size:12px; line-height:100%; color:#FFF; text-align:center;">
                                                                    <a  target="_blank" href="<?php echo $data->open_link ?>" style="display:block; text-decoration:none; height:22px; line-height:22px; color:#6633FF;">CLICK HERE TO OPEN</a>
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
                                        <td height="10" bgcolor="#140901" style="padding:0;" valign="bottom"><img alt="" height="10" src="<?php echo $data->path ?>/borderBottom.png" width="620" vspace="0" hspace="0" style="margin:0;padding:0;border:0;display:block;" /></td>
                                    </tr>
                                </table>
                                <!--End Of Main Content [row number #2]-->
                                <!--Start Of Content [row number #3]--><!--End Of Content [row number #3]-->
                                <!--Start Of two Content Container [row number #4]-->

                                <!--End Of Two Column Container [row number #4]-->
                                <!--Start Of Footer [row number #6]-->
                                <table class="darkContainer" width="620" align="center" cellpadding="0" cellspacing="0" style="border-collapse:collapse; margin:0 auto; text-align:left;">
                                    <tr>
                                        <td height="10" bgcolor="#140901" style="padding:0;" valign="top"><img alt="" height="10" src="<?php echo $data->path ?>/borderTop.png" width="620" vspace="0" hspace="0" style="margin:0;padding:0;border:0;display:block;" /></td>
                                    </tr>
                                    <tr>
                                        <td bgcolor="#140901" style="padding:10px 20px 15px 20px; font-family:Arial, Helvetica, sans-serif; font-size:12px; line-height:15pt; color:#df833e;">

                                            <table align="center" width="580" cellpadding="0" cellspacing="0" style="border-collapse:collapse; text-align:right;">
                                                <tr>
                                                    <td width="20" style="font-family:Arial, Helvetica, sans-serif; font-size:12px; line-height:15pt; color:#df833e; margin:0; padding:0; text-align:left;" valign="top">
                                                        <img alt="image" height="16" src="<?php echo $data->path ?>/bulb.png" width="13" /></td>
                                                    <td width="450" style="font-family:Arial, Helvetica, sans-serif; font-size:12px; line-height:15pt; color:#FFF; margin:0; padding:0 10px 0 0; text-align:left;" valign="top">
                                                        Want to tell your friends about us?...</td>
                                                    <td width="26" valign="middle">
                                                        <a href="http://www.facebook.com/EliteTraining"><img alt="Facebook" height="16" src="<?php echo $data->path ?>/facebook.png" width="16" border="0" vspace="0" hspace="0" /></a></td>
                                                    <td width="26" valign="middle">
                                                        <a href="http://www.twitter.com/EliteMelbourne"><img alt="Twitter" height="16" src="<?php echo $data->path ?>/twitter.png" width="16" border="0" vspace="0" hspace="0" /></a></td>
                                                    <td width="26" valign="middle">
                                                        <a href="http://www.youtube.com/EliteFitnessPT">
                                                            <img alt="YouTube" height="16" src="<?php echo $data->path ?>/youtube.png" width="16" border="0" vspace="0" hspace="0" /></a></td>
                                                    <td width="26" valign="middle">
                                                        <a href="http://www.instagram.com/EliteMelbourne">
                                                            <img alt="Instagram" height="16" src="<?php echo $data->path ?>/instagram.png" width="16" border="0" vspace="0" hspace="0" /></a></td>
                                                    <td width="26" valign="middle">
                                                        <a href="https://plus.google.com/117163734672496632130/about">
                                                            <img alt="Google" height="16" src="<?php echo $data->path ?>/google.png" width="16" border="0" vspace="0" hspace="0" /></a></td>
                                                    <td width="26" valign="middle">
                                                        <a href="http://www.linkedin.com/pub/paul-meier/21/785/b1">
                                                            <img alt="Linkedin" height="16" src="<?php echo $data->path ?>/in.png" width="16" border="0" vspace="0" hspace="0" /></a></td>
                                                </tr>
                                            </table>

                                        </td>
                                    </tr>
                                    <tr>
                                        <td height="2" bgcolor="#140901" style="padding:0 0 15px 0; line-height:0;">
                                            <img alt="" height="2" src="<?php echo $data->path ?>/hr.png" width="620" /></td>
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
