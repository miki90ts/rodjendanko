@php
    $isElegant = $invitation->template->value === 'elegant';
    $accent = $isElegant ? '#b88a3b' : '#7c3aed';
    $heading = $isElegant ? '#14213d' : '#3b176f';
    $date = $invitation->starts_at->copy()->timezone($invitation->timezone);
@endphp
<!doctype html>
<html lang="sr">
<body style="margin:0;padding:0;background:#f6f4fb;color:#24324d;font-family:Arial,Helvetica,sans-serif;">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
    <tr>
        <td align="center" style="padding:32px 14px;">
            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="max-width:640px;background:#ffffff;border-radius:28px;overflow:hidden;border:1px solid #ece7f4;">
                <tr>
                    <td align="center" background="{{ $backgroundUrl }}" style="padding:52px 28px;background-color:#fff9f5;background-image:url('{{ $backgroundUrl }}');background-position:center;background-size:cover;">
                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background:rgba(255,255,255,.91);border-radius:24px;">
                            <tr>
                                <td align="center" style="padding:34px 24px;">
                                    <p style="margin:0;color:{{ $accent }};font-size:14px;letter-spacing:4px;font-weight:700;text-transform:uppercase;">Pozivnica za rođendan</p>
                                    <h1 style="margin:13px 0 0;color:{{ $heading }};font-family:Georgia,'Times New Roman',serif;font-size:38px;line-height:1.17;">{{ $invitation->title }}</h1>
                                    <p style="margin:15px 0 0;color:#4d5d78;font-size:18px;line-height:1.55;">{{ $guest->name ? 'Dragi/a '.$guest->name.',' : 'Dragi prijatelji,' }}<br>dođite da zajedno proslavimo poseban dan za <strong>{{ $invitation->honoree_name }}</strong>! 🎉</p>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td style="padding:30px 28px 12px;">
                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background:#fbf9ff;border:1px solid #eee8fb;border-radius:20px;">
                            <tr><td style="padding:18px 20px;border-bottom:1px solid #ece8f4;font-size:16px;">📅 <strong style="padding-left:9px;">Datum</strong></td><td align="right" style="padding:18px 20px;border-bottom:1px solid #ece8f4;color:{{ $accent }};font-size:16px;font-weight:700;">{{ $date->format('d.m.Y.') }}</td></tr>
                            <tr><td style="padding:18px 20px;border-bottom:1px solid #ece8f4;font-size:16px;">🕐 <strong style="padding-left:9px;">Vreme</strong></td><td align="right" style="padding:18px 20px;border-bottom:1px solid #ece8f4;color:{{ $accent }};font-size:16px;font-weight:700;">{{ $date->format('H:i') }}</td></tr>
                            <tr><td style="padding:18px 20px;font-size:16px;vertical-align:top;">📍 <strong style="padding-left:9px;">Mesto</strong></td><td align="right" style="padding:18px 20px;color:{{ $accent }};font-size:15px;line-height:1.45;font-weight:700;">{{ $invitation->venue_name }}<br><span style="color:#6e7990;font-weight:400;">{{ $invitation->address }}, {{ $invitation->city }}</span></td></tr>
                        </table>
                    </td>
                </tr>
                @if($invitation->description)
                    <tr><td align="center" style="padding:20px 34px 4px;color:#47556f;font-family:Georgia,'Times New Roman',serif;font-size:19px;line-height:1.65;">{!! nl2br(e($invitation->description)) !!}</td></tr>
                @endif
                @if($invitation->dress_code || $invitation->gift_note)
                    <tr><td align="center" style="padding:18px 30px 0;color:#6a7488;font-size:14px;line-height:1.6;">@if($invitation->dress_code)👗 Dress code: <strong>{{ $invitation->dress_code }}</strong>@endif @if($invitation->gift_note)<br>🎁 {{ $invitation->gift_note }}@endif</td></tr>
                @endif
                <tr>
                    <td align="center" style="padding:24px 24px 8px;">
                        <p style="margin:0 0 17px;color:#536079;font-size:15px;line-height:1.5;">Molimo vas da potvrdite dolazak{{ $invitation->reply_deadline ? ' do '.$invitation->reply_deadline->format('d.m.Y.') : '' }}.</p>
                        <table role="presentation" cellpadding="0" cellspacing="0" border="0"><tr>
                            <td style="padding:5px;"><a href="{{ $attendingUrl }}" style="display:inline-block;padding:14px 20px;border-radius:13px;background:{{ $accent }};color:#ffffff;text-decoration:none;font-size:15px;font-weight:700;">✓ Potvrdi dolazak</a></td>
                            <td style="padding:5px;"><a href="{{ $decliningUrl }}" style="display:inline-block;padding:13px 20px;border-radius:13px;border:1px solid #d9d2e8;color:#5e5870;text-decoration:none;font-size:15px;font-weight:700;">Ne mogu da dođem</a></td>
                        </tr></table>
                        <p style="margin:16px 0 0;"><a href="{{ $mapUrl }}" style="color:{{ $accent }};font-size:14px;font-weight:700;text-decoration:none;">🗺️ Otvori lokaciju na mapi</a></p>
                    </td>
                </tr>
                <tr><td align="center" style="padding:28px 24px 30px;color:#536079;font-size:15px;line-height:1.5;">Radujemo se druženju!<br><strong style="color:{{ $heading }};font-size:18px;">{{ $invitation->host_name }}</strong>@if($invitation->host_phone)<br>{{ $invitation->host_phone }}@endif</td></tr>
                <tr><td align="center" style="padding:18px 24px;background:#faf8fe;border-top:1px solid #eee9f6;color:#8490a5;font-size:12px;">Pozivnica je napravljena putem Rođendanka.</td></tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>
