<html xmlns="http://www.w3.org/1999/xhtml" >
  <head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
  </head>
    <body onload="//javascript:print()">
 
        <div  style="position: absolute;width:800px;border:4px solid #000000; background-color: #ECEBE9;border-radius: 10px; padding: 10px;">
            <a href="//<?php echo $data->business_profile->website_url ?>">
                <img style="float: left; width:265px; height: 70px; background-size: 265px auto;" src="<?php echo $data->header_image  ?>">
            </a>
            <div style="font-size: 11px;float: right; position: inherit; right: 20px; text-align: right; top: 3px;">
                <span><?php echo $data->business_profile->website_url ?></span><br/>
                <span><?php echo $data->business_profile->email ?></span><br/>
                <span><?php echo $data->business_profile->contact_number ?></span><br/><br/>

                <span><?php echo $data->business_profile->facebook_url ?></span><br/>
                <span><?php echo $data->business_profile->twitter_url ?></span><br/>
                <span><?php echo $data->business_profile->youtube_url ?></span><br/>
                <span><?php echo $data->business_profile->instagram_url ?></span><br/>
                <span><?php echo $data->business_profile->google_plus_url ?></span><br/>
                <span><?php echo $data->business_profile->linkedin_url ?></span>
            </div>

        </div>
    
    </body>
</html>


