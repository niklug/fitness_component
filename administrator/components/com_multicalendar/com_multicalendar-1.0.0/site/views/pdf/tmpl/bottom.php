                                            <table align="center" width="580" cellpadding="0" cellspacing="0" style="border-collapse:collapse; text-align:right;">
                                                <tr>
                                                    <td width="20" style="font-family:Arial, Helvetica, sans-serif; font-size:12px; line-height:15pt; color:#df833e; margin:0; padding:0; text-align:left;" valign="top">
                                                        <img alt="image" height="16" src="<?php echo $data->path ?>/bulb.png" width="13" /></td>
                                                    <td width="450" style="font-family:Arial, Helvetica, sans-serif; font-size:12px; line-height:15pt; color:#FFF; margin:0; padding:0 10px 0 0; text-align:left;" valign="top">
                                                        Want to tell your friends about us?...</td>
                                                    <td width="26" valign="middle">
                                                        <a target="_blank" href="//<?php echo $data->business_profile->facebook_url ?>"><img alt="Facebook" height="16" src="<?php echo $data->path ?>/facebook.png" width="16" border="0" vspace="0" hspace="0" /></a></td>
                                                    <td width="26" valign="middle">
                                                        <a target="_blank" href="//<?php echo $data->business_profile->twitter_url ?>"><img alt="Twitter" height="16" src="<?php echo $data->path ?>/twitter.png" width="16" border="0" vspace="0" hspace="0" /></a></td>
                                                    <td width="26" valign="middle">
                                                        <a target="_blank" href="//<?php echo $data->business_profile->youtube_url ?>">
                                                            <img alt="YouTube" height="16" src="<?php echo $data->path ?>/youtube.png" width="16" border="0" vspace="0" hspace="0" /></a></td>
                                                    <td width="26" valign="middle">
                                                        <a target="_blank" href="//<?php echo $data->business_profile->instagram_url ?>">
                                                            <img alt="Instagram" height="16" src="<?php echo $data->path ?>/instagram.png" width="16" border="0" vspace="0" hspace="0" /></a></td>
                                                    <td width="26" valign="middle">
                                                        <a target="_blank" href="//<?php echo $data->business_profile->google_plus_url ?>">
                                                            <img alt="Google" height="16" src="<?php echo $data->path ?>/google.png" width="16" border="0" vspace="0" hspace="0" /></a></td>
                                                    <td width="26" valign="middle">
                                                        <a target="_blank" href="//<?php echo $data->business_profile->linkedin_url ?>">
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
                                            Copyright © <?php echo $data->business_profile->name ?><br />
                                            <a target="_blank" href="//<?php echo $data->business_profile->website_url ?>" style="color:#FFF;"><?php echo $data->business_profile->website_url ?></a> |
                                            <a  href="mailto:<?php echo $data->business_profile->email ?>" style="color:#FFF;"><?php echo $data->business_profile->email ?></a> |
                                            <?php echo $data->business_profile->contact_number ?><br />
                                            Having trouble viewing this email? <a href="<?php echo $data->sitelink ?>" style="color:#FFF;">Click Here</a> to open in your web browser.
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
