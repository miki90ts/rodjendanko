<!doctype html>
<html lang="sr">
<body style="margin:0;padding:0;background:{{ $background }};color:#172b54;font-family:Arial,Helvetica,sans-serif;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="width:100%;background:{{ $background }};">
        <tr>
            <td align="center" style="padding:34px 14px;">
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="width:100%;max-width:620px;background:{{ $card }};border:1px solid {{ $border }};border-radius:30px;overflow:hidden;">
                    <tr>
                        <td align="center" style="padding:34px 24px 8px;">
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                    <td align="left" style="font-size:34px;line-height:1;">🎈</td>
                                    <td align="center" style="font-size:78px;line-height:1;letter-spacing:4px;">🎂</td>
                                    <td align="right" style="font-size:34px;line-height:1;">🎈</td>
                                </tr>
                            </table>
                            <p style="margin:18px 0 0;font-size:15px;line-height:1;letter-spacing:4px;color:{{ $accent }};font-weight:700;">✦ &nbsp; {{ $emoji }} &nbsp; ✦</p>
                            <h1 style="margin:15px 0 0;color:#172b54;font-family:Georgia,'Times New Roman',serif;font-size:39px;line-height:1.12;font-weight:700;">Srećan rođendan! 🎉</h1>
                            <table role="presentation" cellpadding="0" cellspacing="0" border="0" style="margin:20px auto 0;">
                                <tr>
                                    <td style="height:2px;width:48px;background:{{ $accent }};"></td>
                                    <td style="padding:0 11px;color:#f43f7f;font-size:18px;line-height:1;">♥</td>
                                    <td style="height:2px;width:48px;background:{{ $accent }};"></td>
                                </tr>
                            </table>
                            <p style="max-width:455px;margin:22px auto 0;color:#263e68;font-size:19px;line-height:1.55;">
                                Dragi/a <strong style="color:#ef477e;">{{ $contact->first_name }}</strong>, danas je tvoj poseban dan — želim ti mnogo sreće, zdravlja, ljubavi i osmeha.
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:20px 28px 0;">
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="width:100%;border:1px solid {{ $border }};border-radius:22px;background:#fffdfb;">
                                <tr>
                                    <td align="center" style="padding:28px 26px;">
                                        <p style="margin:0 0 8px;color:#fb83a8;font-family:Georgia,'Times New Roman',serif;font-size:42px;line-height:.65;">“</p>
                                        <p style="margin:0;color:#172b54;font-family:Georgia,'Times New Roman',serif;font-size:23px;line-height:1.55;font-weight:700;">
                                            {!! nl2br(e($messageText ?? '')) !!}
                                        </p>
                                        <table role="presentation" cellpadding="0" cellspacing="0" border="0" style="margin:20px auto 0;">
                                            <tr>
                                                <td style="height:1px;width:30px;background:{{ $accent }};"></td>
                                                <td style="padding:0 10px;color:#fb5d91;font-size:21px;line-height:1;">♥</td>
                                                <td style="height:1px;width:30px;background:{{ $accent }};"></td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td align="center" style="padding:32px 24px 30px;">
                            <p style="margin:0;color:#263e68;font-size:18px;line-height:1.45;">Sa puno lepih želja,</p>
                            <p style="margin:5px 0 0;color:#ef477e;font-family:Georgia,'Times New Roman',serif;font-size:25px;line-height:1.2;font-weight:700;">{{ $senderName }}</p>
                            <p style="margin:8px 0 0;color:#a976f5;font-size:22px;line-height:1;">♥</p>
                        </td>
                    </tr>
                    <tr>
                        <td align="center" style="padding:19px 24px;background:#faf7ff;border-top:1px solid {{ $border }};">
                            <p style="margin:0;color:#70809b;font-size:12px;line-height:1.55;">Ova čestitka je poslata sa pažnjom putem Rođendanka.</p>
                            <p style="margin:5px 0 0;color:#70809b;font-size:12px;line-height:1.55;">© Rođendanko</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
