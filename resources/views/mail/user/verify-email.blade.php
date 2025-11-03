<x-mail::message>
    # Hello {{ $userName }},

    Please click the button below to verify your email address:

    <x-mail::button :url="route('verification.verify', ['id' => $userId, 'hash' => $hash])">
        Verify Email Address
    </x-mail::button>

    If the button above does not work, you can also verify your email by clicking the link below:

    {{route('verification.verify', ['id' => $userId, 'hash' => $hash])}}

    This verification link will expire in **60 minutes**.

    If you did not create an account, no further action is required.

    Thanks,<br>
    {{ config('app.name') }}
</x-mail::message>
