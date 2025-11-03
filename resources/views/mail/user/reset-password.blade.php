<x-mail::message>
    # Hello {{ $user->name }},

    Please click the button below to reset your password:

    <x-mail::button :url="route('password.reset', ['email' => $user->email, 'token' => $hash])">
        Reset Password
    </x-mail::button>


    If this doesn't work, copy and paste following link in your browser:
    {{route('password.reset', ['email' => $user->email, 'token' => $hash])}}

    This link will expire in **60 minutes**.

    If you did not request a new password, you can safely delete this email.
    Thanks,<br>
    {{ config('app.name') }}
</x-mail::message>
