<?php

// Which email is this?

$msg = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<!--[if gte mso 9]><xml>
  <o:OfficeDocumentSettings>
    <o:AllowPNG/>
    <o:PixelsPerInch>96</o:PixelsPerInch>
  </o:OfficeDocumentSettings>
</xml><![endif]-->
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
<meta name="viewport" content="width=device-width; initial-scale=1.0; maximum-scale=1.0; user-scalable=no;"/>
<meta http-equiv="X-UA-Compatible" content="IE=9; IE=8; IE=7; IE=EDGE"/>
<title>fundraiser</title>
<style type="text/css">
	/* Some resets and issue fixes */
body {
width: 100%;
margin: 0 auto;
background-color:#c6c6c6;
}
* {
-webkit-text-size-adjust: none;
}
.ExternalClass {
width: 100%;
line-height: normal;
}
img {
border: 0;
}
.ReadMsgBody {
width: 100%;
	}
/* Forces Hotmail to display emails at full width */ /*Hotmail table centering fix*/
p {
margin: 1em 0;
}
 /*This resolves the Outlook 07, 10, and Gmail td padding issue fix*/
table td {
border-collapse: collapse;
} 
.round {border-radius: 100%;}
/*viewport width scaling for all devices*/
@-ms-viewport {
width: device-width;
} 
/*This resolves the issue when iphone puts links on dates, etc.*/
.appleLinks {
color: inherit;
text-decoration: none;
}
.appleLinks a {
color: inherit;
text-decoration: none;
}
a:visited {
color: inherit !important;
}
[class*="mobile-only"] {
display:none!important;
}
/* End reset */
@media only screen and (max-width: 767px) {
*[class*="mobile-only"] {
display:block!important;
width:auto!important;
overflow:visible!important;
float:none !important;
height:inherit!important;
max-height:inherit!important;
font-size:inherit!important;
}
*[class*="hide"] {
display: none!important;
}
*[class*="main-table"] {
width: 300px!important;
}
*[class*="inner-table"] {
width: 280px!important;
}
*[class*="header1"] {
padding:0px 0px 5px 10px !important;
}
*[class*="header2"] {
padding:0px 10px 5px 10px !important;
font-size:13px !important;
line-height:20px !important;
}
*[class*="inner-table"] {
width: 280px!important;
}
*[class*=".yshortcuts a"] {
border-bottom: none !important;
}
*[class*="help"] {
font-size:13px !important;
line-height:30px !important;
padding: 10px 0px 10px 0px !important;
}	
*[class*="sharing"] {
padding: 0px 10px 5px 10px !important;
}
*[class*="footer"] {
padding:25px 5px 20px 5px !important;
}
    }
</style>
</head>
<body bgcolor="#c6c6c6" style="padding:0; margin:0;">
<div class="preview-div" style="display:none;font-size:0px;line-height:0px;max-height:0px;max-width:0px;opacity:0;overflow:hidden;float:left">
	 Please help by donating or sharing for my ' . get_the_title($fundraiser_id) . '. Thank you so much for your support!
</div>
<div class="ExternalClass">
	<table width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#c6c6c6">
	<tr>
		<td align="center">
			<!-- Start Main Wrap -->
			<table width="550" border="0" cellspacing="0" cellpadding="0" class="main-table">
			<!-- Start Preheader -->
			<!--End Preheader-->
			<!--Start Main Content-->
			<tr>
				<td>
					<table cellpadding="0" cellspacing="0" border="0" width="100%">
					<tr>
						<td valign="top" align="left" style="color:#524940; font-family:Arial, Helvetica, sans-serif; font-size:14px; mso-line-height-rule: exactly;">
							<!-- Start Desktop Version-->
							<table border="0" cellpadding="0" cellspacing="0" width="100%">
							<tr>
								<td style="padding:20px 0px 0px 0px">
									<table border="0" cellpadding="0" cellspacing="0" width="100%">
									<tr>
										<td align="center" valign="top" width="550" style="font-size:25px; line-height:30px; font-family:Arial, Helvetica, sans-serif;border-radius:4px;box-shadow:0 -3px 0 #d4d2d2" bgcolor="#FFFFFF">
											<table cellpadding="0" cellspacing="0" border="0" width="100%">
											<tr>
												<td valign="top" align="center">
													<table border="0" cellpadding="0" cellspacing="0" width="480" style="border-bottom:1px solid green;" class="inner-table">
													<tr>
														<td align="left" valign="bottom" style="padding:0px 0px 5px 0px;font-size:12px;" class="header1">
															<table cellpadding="0" cellspacing="0" border="0" width="96" style="width:96px; max-width:96px;">
															<tr>
																<td valign="top" align="center" width="96" style="width:96px;">
																	<span>
																	' . get_avatar($pd[0], 96) . ' </span>
																</td>
															</tr>
															</table>
														</td>
														<td align="left" valign="bottom" style="padding:0px 0px 5px 5px; font-size:15px;line-height:24px;color:#000000;font-family: Arial,sans-serif" class="header2">
															<strong>
															Please help by donating or sharing for my ' . get_the_title($fundraiser_id) . '. Thank you so much for your support!</strong><br/>
															<span style="font-family: gloria hallelujah, Arial, sans-serif">- ' . $user_name . '</span>
														</td>
													</tr>
													</table>
												</td>
											</tr>
											<tr>
												<td align="center" valign="top" style="font-size:15px;line-height:24px;color:#007b0b;font-family: Arial,sans-serif; text-decoration:underline; padding:5px 0px 0px 0px" class="help">
													<strong>You can help by donating and/or sharing:</strong>
												</td>
											</tr>
											<tr>
												<td valign="top" align="center">
													<table border="0" cellpadding="0" cellspacing="0" width="480" style="" class="inner-table">
													<tr>
														<td align="left" valign="top" style="font-size:15px;line-height:21px;font-family:Arial,sans-serif;; padding:0px 0px 5px 0px; color:#3b3b3b;" class="sharing">
															<span style=" text-decoration:underline;color:#ef4c1b;">Donating:<br/>
															</span>
															Please click the button below to visit my fundraising page. Please donate whatever you can; even the smallest donation makes a difference
														</td>
													</tr>
													<tr>
														<td align="left" valign="top" style="font-size:15px;line-height:21px;font-family:Arial,sans-serif;; padding:5px 0px 5px 0px; color:#3b3b3b;" class="sharing">
															<span style=" text-decoration:underline;color:#ef4c1b;">Sharing:<br/>
															</span>
															You can also help by sharing the fundraiser on Facebook by clicking the Share on Facebook button on the fundraiser page and/or forward this email to your contacts
														</td>
													</tr>
													<tr>
														<td align="left" valign="top" style="font-size:15px;line-height:21px;font-family:Arial,sans-serif;; padding:0px 0px 5px 0px; color:#3b3b3b;" class="sharing">
															 -' . $user_name . '
														</td>
													</tr>
													<tr>
														<td align="left" valign="top" style="font-size:15px;line-height:21px;font-family:Arial,sans-serif;; padding:20px 0px 25px 0px; color:#3b3b3b;">
															<table border="0" cellpadding="0" cellspacing="0" width="100%">
															<!--Start Button-->
															<tr>
																<td align="center" valign="top">
																	<table border="0" cellpadding="0" cellspacing="0" width="200" class="width_button">
																	<tr>
																		<td align="center" width="275" valign="middle" style="padding:9px 0px 9px 0px; background-color:#52b968; font-family:Arial, Helvetica, sans-serif; font-size:15px; line-height:20px; border-radius:5px;">
																			<a href="' . get_permalink($fundraiser_id) . 'email/' . $pd[0] . '">Go to Fundraiser</strong></span></a>
																		</td>
																	</tr>
																	</table>
																</td>
															</tr>
															</table>
														</td>
													</tr>
													</table>
												</td>
											</tr>
											</table>
										</td>
									</tr>
									<tr>
										<td>
										</td>
									</tr>
									</table>
								</td>
							</tr>
							</table>
							<!--End Desktop Version-->
						</td>
					</tr>
					</table>
				</td>
			</tr>
			<!--End Main Content-->
			<!--Start Footer-->
			<tr>
				<td valign="top" style="padding:20px 0px 20px 0px;" class="footer">
					<table cellpadding="0" cellspacing="0" border="0" width="100%">
					<tr>
						<td valign="top" align="center" style="color:#000000; font-family:Arial,sans-serif;font-size:12px; line-height:17px" class="appleLinks">
							<a href="https://www.wefund4u.com" style="color:#000000; text-decoration:none;" target="_blank"><span style="color:#000000;text-decoration:none;"><strong style="font-weight:bold;">W&#8203;eF&#8203;un&#8203;d&#8203;4&#8203;u</strong></span></a><br/>
							14&#8203;24 Sher&#8203;man A&#8203;ve &#35;40&#8203;0, Coe&#8203;ur d&rsquo;Ale&#8203;ne, I&#8203;D 838&#8203;14
						</td>
					</tr>
					</table>
				</td>
			</tr>
			<!--End Footer-->
			</table>
			<!--End Main Wrap-->
		</td>
	</tr>
	</table>
</div>
<!-- Gmail App Fix -->
<div class="hide">
	<table width="600" border="0" cellpadding="0" cellspacing="0" class="container" id="spacer-600" style="width:600px;max-width:600px;min-width:600px; background-color:#c6c6c6;">
	<tr>
		<td>
			<div class="em_dn" style="font:20px courier; color:#c6c6c6; background-color:#c6c6c6;">
				 &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
			</div>
		</td>
	</tr>
	</table>
</div>
<!-- Gmail App Fix -->
</body>
</html>';