<!DOCTYPE html>
<html>

<head>
    <title>Bugs Report</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <style>
        h2.text-clr {
            color: grey !important;
        }
    </style>
</head>

<body>

    <p><span class="acuity-redactor-fix"><a href="Border Crossing Law Firm" target="_blank" rel="noreferrer noopener"><img
                    src="https://cdn-s.acuityscheduling.com/upload-fb26e8175912f85de4875671d66fa471.png" width="496"
                    height="343" style="width:496px;height:343px;display:block;margin:auto;"
                    alt=""></a></span>
    </p>
    <table cellpadding="0" cellspacing="0" width="100%" border="0" style="max-width:700px;padding:30px 10px;"
        class="container" align="center">
        <tbody>
            <tr>
                <td style="padding:20px 25px;margin:0;border:1px solid #efefef;border-radius:3px;"
                    class="padding-wrapper" bgcolor="#ffffff">
                    <span class="acuity-redactor-fix"></span>
                    <table border="0" cellpadding="0" cellspacing="0" style="width:100%;">
                        <tbody>
                            <tr>
                                <td align="center"
                                    style="padding:35px 0 5px 0;font-size:30px;font-family:sans-serif;color:#999999;font-weight:bold;"
                                    class="header-padding"><span style="color:rgb(0,0,0);"> &#128028; Bug Reported
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                        <tbody>
                            <tr>
                                <td colspan="2" align="left"
                                    style="padding:10px 0 10px 0;font-size:16px;line-height:25px;font-family:sans-serif;color:#999999;"
                                    class="custom-confirmation"><span style="color:rgb(0,0,0);"><span
                                            style="font-size:18px;"><br><br>
                                            <hr /><br>

                                            <div class="m-2">Reported by : <strong>{{ $user['first_name'] }}</strong>
                                            </div>

                                            <div>
                                                <?php
                                                $nodeLables = json_decode($node_url);
                                                ?>
                                            </div>
                                            <div class="spacing">
                                                @foreach ($nodeLables as $lable)
                                                    <div class="">
                                                        <ul>
                                                            <li class="m-2">{{ $lable }}</li>
                                                        </ul>
                                                    </div>
                                                @endforeach
                                            </div>
                                            <div class="m-1">Current Node:
                                                {{ isset($current_node) ? $current_node : null }}</div>
                                            <div class="m-1">Full Name: {{ $user['first_name'] }}
                                                {{ isset($user['last_name']) ? $user['last_name'] : null }}</div>
                                            <div class="m-1">Email: {{ $user['email'] }}</div>
                                            <div class="m-1">Description: {{ $description }}</div>
                                            <div class="m-1">Bugs Report Date: {{ $created_at }}</div>
                                            </div>

                                            <br><br>
                                            <hr />
                                            <br>
                                            <!--Button-->
                                            <center>
                                                <table align="center" cellspacing="0" cellpadding="0" width="100%">
                                                    <tr>
                                                        <td align="center" style="padding: 10px;">
                                                            <table border="0" class="mobile-button" cellspacing="0"
                                                                cellpadding="0">
                                                                <tr>
                                                                    <td align="center" bgcolor="#2b3138"
                                                                        style="background-color: #2b3138; margin: auto; max-width: 600px; -webkit-border-radius: 5px; -moz-border-radius: 5px; border-radius: 5px; padding: 15px 20px; "
                                                                        width="100%">
                                                                        <!--[if mso]>&nbsp;<![endif]-->
                                                                        <a href="mailto:Shahid@bordercrossinglaw.com"
                                                                            target="_blank"
                                                                            style="16px; font-family: Helvetica, Arial, sans-serif; color: #ffffff; font-weight:normal; text-align:center; background-color: #2b3138; text-decoration: none; border: none; -webkit-border-radius: 5px; -moz-border-radius: 5px; border-radius: 5px; display: inline-block;">
                                                                            <span
                                                                                style="font-size: 16px; font-family: Helvetica, Arial, sans-serif; color: #ffffff; font-weight:normal; line-height:1.5em; text-align:center;">💬
                                                                                If you you would like to schedule a
                                                                                consultation with us, please click
                                                                                here.</span>
                                                                        </a>
                                                                        <!--[if mso]>&nbsp;<![endif]-->
                                                                    </td>
                                                                </tr>
                                                            </table>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </center>
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
