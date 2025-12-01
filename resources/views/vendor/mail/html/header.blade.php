@props(['url'])
<tr>
    <td class="header">
        <a href="{{ $url }}"
            style="display: inline-block; color: #18181b; font-size: 26px; font-weight: 700; text-decoration: none; letter-spacing: -0.03em;">
            @if (trim($slot) === 'Laravel')
                <img src="https://laravel.com/img/notification-logo.png" class="logo" alt="Laravel Logo">
            @else
                {!! $slot !!}
            @endif
        </a>
    </td>
</tr>
