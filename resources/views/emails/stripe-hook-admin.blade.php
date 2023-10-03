<!DOCTYPE html>
<html>

<head>
    <title>Subscription Failed Email</title>
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
    <h1>Subscription Failed</h1>
    <p>Dear Admin,</p>
    <p>The subscription has failed:</p>
    <ul>
        <li>Name: {{ $data['name'] }}</li>
        <li>Email: {{ $data['email'] }}</li>
        <li>Plan: {{ $data['plan_name'] }}</li>
    </ul>
    <p>Please follow up with the user to resolve any issues with their subscription.</p>
    <p>Thank you.</p>

</body>

</html>
