<!DOCTYPE html>
<html>
<head>
    <title>Open-Tickets</title>
</head>
<body>
    <h1>{{ $mailData['title'] }}</h1>
    <p>Ticket-ID : <span style="font-size: 2rem;">#{{ $mailData['ticketId'] }}</span></p>
    <p>Response : <span style="font-size: 1rem;">#{{ $mailData['reply'] }}</span></p>
    <p>Thank you</p>
</body>
</html>