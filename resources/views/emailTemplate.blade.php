<div>
    <p>Dear {{ $emailData['name'] ?? 'User' }},<br>
    Good morning, we received a request from this email: {{ $emailData['email'] ?? 'No email provided' }}<br>
    @if(!empty($emailData['phone']))
    With this phone number {{ $emailData['phone'] }}
    @endif
    </p>
    
    <p>Thank you for your request!</p>
    @if(isset($emailData['message']))
        <p>We also received the following message:<br>
        {{ $emailData['message'] }}</p>
    @endif

    @if(isset($emailData['files']) && !empty($emailData['files']))
        <p>We also added the files that you sent to us here.</p>
    @endif
    
    <p>Best Regards,<br>
    Team {{ config('app.name') }}</p>
</div>
