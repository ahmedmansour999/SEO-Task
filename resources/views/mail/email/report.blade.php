@component('mail::message')
# Daily Report

- **New Users:** {{ $newUsers }}
- **New Posts:** {{ $newPosts }}

Thanks,<br>
{{ config('app.name') }}
@endcomponent
