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
                color:#df833e;
            }
            .darkContainer a:hover {
                color:#ffba87 !important;
            }
            .readMore a:hover {
                background-color:#71330A !important;
                color: #FFF !important;
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
        require_once JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_fitness' . DS . 'helpers' . DS . 'email_templates_data.php';

        $event_id = &JRequest::getVar('event_id');
        $client_id = &JRequest::getVar('client_id');
        $layout = JRequest::getVar('layout');
        // 
        $params = array('method' => 'Appointment', 'id' => $event_id, 'client_id' => $client_id, 'layout' => $layout);

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
                                        <td bgcolor="#e76708" style="padding:25px 20px; font-family:Arial, Helvetica, sans-serif; font-size:30px; line-height:20pt; color:#482104; font-weight:lighter;">
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
                                                                    <h1 style="padding:0; margin:0; font-family:Arial, Helvetica, sans-serif; font-size:27px; line-height:33pt; color:#FFF; font-weight:lighter; margin-bottom:0 !important;">PLEASE CONFIRM YOUR APPOINTMENT</h1>	
                                                                </td>
                                                            </tr>
                                                        </table>
                                                        <table width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse;">
                                                            <tr>
                                                                <td colspan="2" style="margin:0; padding:15px 0 0 0; font-family:Arial, Helvetica, sans-serif; font-size:12px; line-height:15pt; color:#CCC;"><p>Hi  <?php echo $data->client_name;?>,</p>
                                                                    <p>Please click 'Confirm' to secure your appointment day and time. If you are unable to attend, contact your coach  as soon as possible to avoid late cancellation penalties. </p></td>
                                                            </tr>
                                                            <tr>
                                                                <td width="29%" style="margin:0; padding:15px 0 0 0; font-family:Arial, Helvetica, sans-serif; font-size:12px; line-height:15pt; color:#CCC;">
                                                                    <p>START DATE:<br />
                                                                        START TIME:<br />
                                                                        LOCATION:
                                                                    </p></td>
                                                                <td width="71%" style="margin:0; padding:15px 0 0 0; font-family:Arial, Helvetica, sans-serif; font-size:12px; line-height:15pt; color:#CCC;">
                                                                    <p><?php echo $data->start_date;?><br />
                                                                        <?php echo $data->start_time ;?><br />
                                                                        <?php echo $data->item->location_name;?>
                                                                    </p></td>
                                                            </tr>
                                                            <tr>
                                                                <td style="margin:0; padding:15px 0 0 0; font-family:Arial, Helvetica, sans-serif; font-size:12px; line-height:15pt; color:#CCC;">
                                                                    APPOINTMENT:<br />
                                                                    SESSION TYPE:<br />
                                                                    SESSION FOCUS:</td>
                                                                <td style="margin:0; padding:15px 0 0 0; font-family:Arial, Helvetica, sans-serif; font-size:12px; line-height:15pt; color:#CCC;">
                                                                    <?php echo $data->item->appointment_name;?><br />
                                                                    <?php echo $data->item->session_type_name;?><br />
                                                                    <?php echo $data->item->session_focus_name;?></td>
                                                            </tr>
                                                            <tr>
                                                                <td style="margin:0; padding:15px 0 20px 0; font-family:Arial, Helvetica, sans-serif; font-size:12px; line-height:15pt; color:#CCC;">
                                                                    TRAINER NAME:</td>
                                                                <td style="margin:0; padding:15px 0 20px 0; font-family:Arial, Helvetica, sans-serif; font-size:12px; line-height:15pt; color:#CCC;">
                                                                    <?php echo $data->trainer_name;?></td>
                                                            </tr>
                                                        </table>
                                                        <table width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse;">
                                                            <tr>
                                                                <td class="readMore" width="160" height="22" bgcolor="#241002" valign="middle" style="padding:0px; font-family:Arial, Helvetica, sans-serif; font-size:12px; line-height:100%; color:#df833e; text-align:center;">
                                                                    <a href="<?php echo JUri::root() ?>index.php?option=com_multicalendar&task=confirm_email&event_id=<?php echo base64_encode($data->item->id) ?>&client_id=<?php echo base64_encode($client_id) ?>" style="display:block; text-decoration:none; height:22px; line-height:22px; color:#b65106;">CLICK HERE TO CONFIRM</a>
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
                                        <td bgcolor="#e76708" style="padding:20px;">
                                            <h2 style="margin:0; padding:0; font-family:Arial, Helvetica, sans-serif; line-height:17pt; font-size:17px; color:#000; font-weight:lighter; margin-bottom:0 !important;">TERMS &amp; CONDITIONS</h2>
                                            <table width="580" cellpadding="0" cellspacing="0" style="border-collapse:collapse;">
                                                <tr>
                                                    <td width="100" style="padding:15px 0 0 0; font-family:Arial, Helvetica, sans-serif; font-size:12px; line-height:15pt; color:#482104;" valign="top" rowspan="2">
                                                        <img alt="image" height="125" src="<?php echo $data->path ?>/icon6.png" width="100" border="0" vspace="0" hspace="0" /></td>
                                                    <td valign="top" style="padding:10px 0 0 20px; font-family:Arial, Helvetica, sans-serif; font-size:12px; line-height:15pt; color:#000;">
                                                        <p>With every professional business there is a serious side...</p>
                                                        <p>Thank you for taking the time to read, understand and appreciate how we continually maintain the highest standard of customer service to all our clients. Please help us maintain this professional standard by providing us with the following small courtesies that are detailed in our Cancelation Policy.</p></td>
                                                </tr>

                                            </table>
                                        </td>
                                    </tr>
                                </table>

                                <table class="lightContainer" width="620" align="center" cellpadding="0" cellspacing="0" style="border-collapse:collapse; margin:0 auto; text-align:left;">
                                    <tr>
                                        <td bgcolor="#e76708" style="padding:20px;">
                                            <table width="580" cellpadding="0" cellspacing="0" style="border-collapse:collapse;">
                                                <tr>
                                                    <td valign="top" style="padding:10px 0 0 20px; font-family:Arial, Helvetica, sans-serif; font-size:12px; line-height:15pt; color:#000;"><p><?php echo $data->business_profile->terms_conditions;?></p></td>
                                                </tr>

                                            </table>
                                        </td>
                                    </tr>
                                </table>

                                <table class="darkContainer" width="620" align="center" cellpadding="0" cellspacing="0" style="border-collapse:collapse; margin:0 auto; text-align:left;">
                                    <tr>
                                        <td height="10" bgcolor="#140901" style="padding:0;" valign="top"><img alt="" height="10" src="<?php echo $data->path ?>/borderTop.png" width="620" vspace="0" hspace="0" style="margin:0;padding:0;border:0;display:block;" /></td>
                                    </tr>
                                    <tr>
                                        <td bgcolor="#140901" style="padding:10px 20px 15px 20px; font-family:Arial, Helvetica, sans-serif; font-size:12px; line-height:15pt; color:#df833e;">
                                         <?php include __DIR__ . DS . 'bottom.php'; ?>  
                                            