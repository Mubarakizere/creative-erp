<x-mail::message>
# New Contact Form Submission

You have received a new message from the website contact form.

---

**Name:** {{ $contactMessage->first_name }} {{ $contactMessage->last_name }}

**Email:** {{ $contactMessage->email }}

**Phone:** {{ $contactMessage->phone ?? 'Not provided' }}

**Subject:** {{ $contactMessage->subject }}

---

## Message

{{ $contactMessage->message }}

---

<x-mail::button :url="config('app.url') . '/admin'">
View in Dashboard
</x-mail::button>

*This message was sent from the Creative Century Engineering website contact form.*

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
