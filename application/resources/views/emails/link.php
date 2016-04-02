<p>{{ $user ? $user->getNameOrEmail() : App::make('Settings')->get('siteName') }} have shared a link with you.<br />
<br />
@if($emailMessage)<br />
<br />
{{ $emailMessage }}<br />
<br />
@endif<br />
<br />
<a href="{{ $link }}" target="_blank">Click here to view</a>.</p>
