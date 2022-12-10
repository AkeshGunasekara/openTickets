<!DOCTYPE html>
<html>
<head>
    <title>Create-Account</title>
</head>
<body>
    <h1>{{ $mailData['title'] }}</h1>
    <p>Email : <span style="font-size: 2rem;">{{ $mailData['email'] }}</span></p>
    <p>Password : <span style="font-size: 2rem;">{{ $mailData['pass'] }}</span></p>
    <p>Thank you</p>
</body>
</html>