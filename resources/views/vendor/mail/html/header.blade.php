@props(['url'])
<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block;">
@if (trim($slot) === 'Laravel')
<img src="{{ asset('assets/images/brand/logo-3.png') }}" style="max-height: 50px" alt="Ministerio de Salud Pública de Corrientes">
@else
{{ $slot }}
@endif
</a>
</td>
</tr>
