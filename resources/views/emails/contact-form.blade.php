@component('mail::message')
    <dl>
        <dt>Name</dt>
        <dd>{{ $data->name }}</dd>
        <dt>Email</dt>
        <dd>{{ $data->email }}</dd>
        <dt>Message</dt>
        <dd>{{ $data->message }}</dd>
    </dl>

    Thanks,
    {{ config('app.name') }}
@endcomponent
