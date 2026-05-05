@component('mail::message')
Dear {{$data['name']}},<br>

Pendaftaran user anda sudah berhasil.<br>

User ID  : {{$data['username']}}<br>
Password : {{$data['password']}}<br>

@component('mail::button', ['url' => 'http://36.95.52.99/mkt/login'])
Link Login
@endcomponent

<br>
Thanks,<br>
PT Masaji Kargosentra Tama
@endcomponent