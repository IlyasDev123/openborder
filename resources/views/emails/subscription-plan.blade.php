<!DOCTYPE html>
<html>

<head>
    <title></title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <style>
        h2.text-clr {
            color: grey !important;
        }
    </style>
</head>

<body style="background-color: #f4f4f4; margin: 0 !important; padding: 0 !important;">
    <!-- HIDDEN PREHEADER TEXT -->
    <table border="0" cellpadding="0" cellspacing="0" width="100%">
        <!-- LOGO -->

        <tr>
            <td bgcolor="#FFA73B" align="center" style="padding: 0px 10px 0px 10px;">
                <table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 600px;">
                    <tr>
                        <td bgcolor="#ffffff" align="center" valign="top"
                            style="padding: 40px 20px 20px 20px; border-radius: 4px 4px 0px 0px; color: #111111; font-family: 'Lato', Helvetica, Arial, sans-serif; font-size: 48px; font-weight: 400; letter-spacing: 4px; line-height: 48px;">
                            <p><span class="acuity-redactor-fix"><a href="Border Crossing Law Firm" target="_blank"
                                        rel="noreferrer noopener"><img
                                            src="https://cdn-s.acuityscheduling.com/upload-fb26e8175912f85de4875671d66fa471.png"
                                            width="496" height="343"
                                            style="width:496px;height:343px;display:block;margin:auto;"
                                            alt=""></a></span>
                            </p>
                            {{-- <img src=" https://img.icons8.com/clouds/100/000000/handshake.png" width="125" height="120" style="display: block; border: 0px;" /> --}}
                        </td>
                    </tr>
                    <tr>
                        <td bgcolor="#f4f4f4" align="center" style="padding: 0px 10px 0px 10px;">
                            <table border="0" cellpadding="0" cellspacing="0" width="100%"
                                style="max-width: 600px;">
                                <tr>
                                    <td bgcolor="#ffffff" align="left"
                                        style="padding: 20px 30px 40px 30px; color: #666666; font-family: 'Lato', Helvetica, Arial, sans-serif; font-size: 18px; font-weight: 400; line-height: 25px;">
                                        <p style="margin: 0;"><b>{{ $user->first_name ?? '' }} {{ $user->last_name ?? '' }}
                                                Successfully buy a package.</b></b></p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td bgcolor="#f4f4f4" align="center" style="padding: 0px 10px 0px 10px;">
                            <table border="0" cellpadding="0" cellspacing="0" width="100%"
                                style="max-width: 600px;">

                                <tr class="mt-3">
                                    <td class="ml-2"> Package Name: {{ $plan->plan_name ?? '' }}</td>
                                </tr>
                                <tr>
                                    <td class="ml-2">Duration: {{ $plan->duration ?? '' }} {{ $plan->recurring_period }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="ml-2">Amount: {{ $plan->amount ?? '' }}</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
     <!-- Advocating -->
     <hr />
     <section class="advocate-section">
         <div class="wrapper">
             <div class="advocating">
                 <div class="advocating-text">
                     <br><img
                         style="border-width: 100px; margin-left: 10px; margin-right: 20px"
                         src="{{asset('logs/bclf-favicon.png')}}"
                         {{-- src="https://www.dropbox.com/s/m3c8p85aj55vq28/bclf-favicon.png?raw=1" --}}
                         alt="" width="100" height="100" />
                     <p style="color:black;">
                     <h2>
                         <p style="color:black;"><b>Advocating for
                                 immigrants.</b></p>
                     </h2>
                     <p style="color:black;">The Border Crossing Law Firm is a
                        full-service immigration law firm, offering help with
                        visas, green cards, citizenship, and deportation
                        proceedings. We have been committed to the immigrant
                        community for two decades, representing thousands of
                        immigrants and their families across the country.</p>
                     <br>
                     <hr>
                 </div>
                 <!-- Footer -->
                 <footer>
                     <div class="wrapper">
                         <div class="footer-content">
                             <div class="contact-info">
                                 <p style="color:black;"><span>(406) 594-2004 |
                                         (888) 595-2004
                                         <br>Attorney@bordercrossinglaw.com
                                         <br></span>
                                     <span>618 Highland Street, Helena, Montana
                                         59601</span>
                             </div>
                             <div class="ft-rights">
                                 <p style="color:black;">
                                     © 2008-2022 Border Crossing Law Firm, P.C.
                                     All Rights Reserved.</p> <a
                                     href="https://bordercrossinglaw.com/legaldisclaimersandtermsofuse"style="color:#000000">
                                     Click here to review our Legal Disclaimers
                                     and Terms of Use Agreement.</a><br>
                                 <p style="color:black;">Attorney Advertising.
                                 </p>
                                 </p>
                             </div>
                         </div>
                     </div>
</body>

</html>
