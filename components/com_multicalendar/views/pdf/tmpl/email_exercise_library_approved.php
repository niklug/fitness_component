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
                color:#FFF;
            }
            .lightContainer a:hover {
                color:#005A00 !important;
            }
            .darkContainer a, .darkContainer a:visited {
                color:#FFF;
            }
            .darkContainer a:hover {
                color:#005A00 !important;
            }
            .readMore a:hover {
                background-color:#002500 !important;
                color: #FFF !important;
            }
            body {
                margin:0;
                background-color:#dddddd;
                color:#005A00;
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
        
        $comment_id = &JRequest::getVar('comment_id');
        // 
        $params = array('method' => 'ExerciseLibrary', 'id' => $id, 'comment_id' => $comment_id);

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
                                        <td bgcolor="#005A00" style="padding:25px 20px; font-family:Arial, Helvetica, sans-serif; font-size:30px; line-height:20pt; color:#482104; font-weight:lighter;">
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
                                                                    <h1 style="padding:0; margin:0; font-family:Arial, Helvetica, sans-serif; font-size:30px; line-height:33pt; color:#FFF; font-weight:lighter; margin-bottom:0 !important;">WELL DONE - VIDEO APPROVED!</h1>	
                                                                </td>
                                                            </tr>
                                                        </table>
                                                        <table width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse;">
                                                            <tr>
                                                                <td colspan="2" style="margin:0; padding:15px 0 15px 0; font-family:Arial, Helvetica, sans-serif; font-size:12px; line-height:15pt; color:#CCC;"><p>Hi <?php echo $data->user_name;?>,</p>
                                                                    <p>You have attainded a satisfactory score for this exercise video entry... WELL DONE!<br />
                                                                        You can now log-in to view your results and trainer feedback.</p>
                                                                    <p>You should discuss with your trainer what you can do to further improve your technique. This is a vital part of achieving your envisioned results!</p>
                                                                    <p>Please take a few moments to review your  entry, take into account your trainers comments and action any requests or instructions.</p></td>
                                                            </tr>
                                                            <tr>
                                                                <td width="160" style="margin:0; padding:15px 0 15px 0; font-family:Arial, Helvetica, sans-serif; font-size:12px; line-height:15pt; color:#CCC;">
                                                                    <p>EXERCISE NAME: <br />
                                                                        AUTHOR NAME: <br />
                                                                        CREATED ON: <br />
                                                                        CURRENT STATUS:<br />
                                                                        ASSESSED BY: </p></td>
                                                                <td width="396" style="margin:0; padding:15px 0 15px 0; font-family:Arial, Helvetica, sans-serif; font-size:12px; line-height:15pt; color:#CCC;">
                                                                    <?php echo $data->item->exercise_name;?> <br />
                                                                    <?php echo $data->item->created_by_name;?><br />
                                                                    <?php echo $data->created;?><br />
                                                                    <?php echo $data->item->status_html ?><br />
                                                                    <?php echo $data->item->assessed_by_name ? $data->item->assessed_by_name : '-';?></td>
                                                            </tr>
                                                        </table>
                                                        <table width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse;">
                                                            <tr>
                                                                <td class="readMore" width="160" height="22" bgcolor="#0A1C00" valign="middle" style="padding:0px; font-family:Arial, Helvetica, sans-serif; font-size:12px; line-height:100%; color:#005A00; text-align:center;">
                                                                    <a target="_blank" href="<?php echo $data->open_link ?>" style="display:block; text-decoration:none; height:22px; line-height:22px; color:#006801;">CLICK HERE TO OPEN</a>
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
                                <!--Start Of Content [row number #3]-->
                                <table class="lightContainer" width="620" align="center" cellpadding="0" cellspacing="0" style="border-collapse:collapse; margin:0 auto; text-align:left;">
                                    <tr>
                                        <td bgcolor="#005A00" style="padding:20px;">
                                            <h2 style="margin:0; padding:0; font-family:Arial, Helvetica, sans-serif; line-height:17pt; font-size:17px; color:#FFF; font-weight:lighter; margin-bottom:0 !important;">Focus on your goals and keep a strong mindset!</h2>
                                            <table width="580" cellpadding="0" cellspacing="0" style="border-collapse:collapse;">
                                                <tr>
                                                    <td width="100" style="padding:15px 0 0 0; font-family:Arial, Helvetica, sans-serif; font-size:12px; line-height:15pt; color:#482104;" valign="top" rowspan="2"><img alt="image" height="100" src="<?php echo $data->path ?>/goals.png" width="100" border="0" vspace="0" hspace="0" /></td>
                                                    <td valign="top" style="padding:10px 0 0 20px; font-family:Arial, Helvetica, sans-serif; font-size:12px; line-height:15pt; color:#CCC;">
                                                        <p>Get focused! Talk to your trainer about ways to improve your mindset and subsequently change your lifestyle.</p>
                                                        <p>Begin by making small changes and encourage new healthy habits that will pave the way to health and fitness!</p></td>
                                                </tr>

                                            </table>
                                        </td>
                                    </tr>
                                </table>
                                <!--End Of Content [row number #3]-->
                                <!--Start Of Footer [row number #6]-->
                                <table class="darkContainer" width="620" align="center" cellpadding="0" cellspacing="0" style="border-collapse:collapse; margin:0 auto; text-align:left;">
                                    <tr>
                                        <td height="10" bgcolor="#140901" style="padding:0;" valign="top"><img alt="" height="10" src="<?php echo $data->path ?>/borderTop.png" width="620" vspace="0" hspace="0" style="margin:0;padding:0;border:0;display:block;" /></td>
                                    </tr>
                                    <tr>
                                        <td bgcolor="#140901" style="padding:10px 20px 15px 20px; font-family:Arial, Helvetica, sans-serif; font-size:12px; line-height:15pt; color:#df833e;">

                                        <?php include __DIR__ . DS . 'bottom.php'; ?>  
