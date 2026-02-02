<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket Completed</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            background-color: #4CAF50;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 5px 5px 0 0;
        }

        .content {
            background-color: #f9f9f9;
            padding: 30px;
            border: 1px solid #ddd;
        }

        .ticket-info {
            background-color: white;
            padding: 15px;
            margin: 20px 0;
            border-left: 4px solid #4CAF50;
        }

        .ticket-info p {
            margin: 10px 0;
        }

        .ticket-info strong {
            color: #555;
        }

        .button {
            display: inline-block;
            padding: 12px 30px;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
            text-align: center;
        }

        .button:hover {
            background-color: #45a049;
        }

        .footer {
            text-align: center;
            padding: 20px;
            color: #777;
            font-size: 12px;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>🎉 Ticket Completed!</h1>
    </div>

    <div class="content">
        <p>Dear Valued Client,</p>

        <p>We're pleased to inform you that your support ticket has been successfully completed.</p>

        <div class="ticket-info">
            <p><strong>Ticket Number:</strong> {{ $ticketNumber }}</p>
            <p><strong>Subject:</strong> {{ $subject }}</p>
            <p><strong>Support Type:</strong> {{ $supportType }}</p>
            <p><strong>Completed At:</strong> {{ $completedAt }}</p>
            @if ($remarks)
                <p><strong>Remarks:</strong></p>
                <p>{{ $remarks }}</p>
            @endif
        </div>

        <p>We would greatly appreciate your feedback on how we handled your request.</p>

        <div style="text-align: center;">
            <a href="{{ $reviewUrl }}" class="button">Leave a Review</a>
        </div>

        <p>Your feedback helps us improve our services and provide better support to all our clients.</p>

        <p>Thank you for using our support system!</p>

        <p>Best regards,<br>
            <strong>Support Team</strong>
        </p>
    </div>

    <div class="footer">
        <p>This is an automated email. Please do not reply to this message.</p>
        <p>&copy; {{ date('Y') }} NTG Ticketing System. All rights reserved.</p>
    </div>
</body>

</html>
