<!doctype html>
<html lang="sr">
<body style="margin:0;padding:0;background:#f8f7ff;color:#172b54;font-family:Arial,Helvetica,sans-serif;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="width:100%;background:#f8f7ff;">
        <tr>
            <td align="center" style="padding:34px 14px;">
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="width:100%;max-width:620px;background:#ffffff;border:1px solid #eee9ff;border-radius:30px;overflow:hidden;">
                    <tr>
                        <td align="center" style="padding:34px 24px 8px;">
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                    <td align="left" style="font-size:34px;line-height:1;">🎈</td>
                                    <td align="center" style="font-size:72px;line-height:1;">💌</td>
                                    <td align="right" style="font-size:34px;line-height:1;">🎈</td>
                                </tr>
                            </table>
                            <p style="margin:18px 0 0;font-size:39px;line-height:1;">🎂</p>
                            <h1 style="margin:13px 0 0;color:#172b54;font-family:Georgia,'Times New Roman',serif;font-size:37px;line-height:1.14;font-weight:700;">Podsetnik za rođendan</h1>
                            <table role="presentation" cellpadding="0" cellspacing="0" border="0" style="margin:19px auto 0;">
                                <tr>
                                    <td style="height:2px;width:48px;background:#f85e96;"></td>
                                    <td style="padding:0 11px;color:#f43f7f;font-size:18px;line-height:1;">♥</td>
                                    <td style="height:2px;width:48px;background:#f85e96;"></td>
                                </tr>
                            </table>
                            <p style="max-width:455px;margin:22px auto 0;color:#263e68;font-size:18px;line-height:1.55;">
                                Zdravo {{ $senderName }}, podsećamo te da se približava rođendan osobe iz tvoje liste.
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:20px 28px 0;">
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="width:100%;border:1px solid #f2d8bd;border-radius:22px;background:#fffdfb;">
                                <tr>
                                    <td style="padding:8px 22px;">
                                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                                            <tr>
                                                <td style="padding:14px 0;border-bottom:1px solid #f3e6da;font-size:16px;line-height:1.4;">👤 <strong style="padding-left:10px;">Osoba:</strong></td>
                                                <td align="right" style="padding:14px 0;border-bottom:1px solid #f3e6da;color:#ef477e;font-size:17px;line-height:1.4;font-weight:700;">{{ $contact->display_name }}</td>
                                            </tr>
                                            <tr>
                                                <td style="padding:14px 0;border-bottom:1px solid #f3e6da;font-size:16px;line-height:1.4;">📅 <strong style="padding-left:10px;">Datum rođendana:</strong></td>
                                                <td align="right" style="padding:14px 0;border-bottom:1px solid #f3e6da;color:#7350c8;font-size:16px;line-height:1.4;font-weight:700;">{{ $birthdayDate?->format('d.m.Y.') ?? '—' }}</td>
                                            </tr>
                                            <tr>
                                                <td style="padding:14px 0;border-bottom:1px solid #f3e6da;font-size:16px;line-height:1.4;">🕘 <strong style="padding-left:10px;">Podsetnik:</strong></td>
                                                <td align="right" style="padding:14px 0;border-bottom:1px solid #f3e6da;color:#2563b8;font-size:16px;line-height:1.4;font-weight:700;">{{ $daysBefore === 0 ? 'na sam dan' : ($daysBefore ? $daysBefore.' dana ranije' : 'zakazan') }}{{ $scheduledAt ? ' u '.$scheduledAt->format('H:i') : '' }}</td>
                                            </tr>
                                            <tr>
                                                <td style="padding:14px 0 10px;font-size:16px;line-height:1.4;vertical-align:top;">✉️ <strong style="padding-left:10px;">Način čestitke:</strong></td>
                                                <td align="right" style="padding:14px 0 10px;color:#d99111;font-size:16px;line-height:1.4;font-weight:700;">{{ $messagePlan['label'] }}</td>
                                            </tr>
                                            <tr>
                                                <td colspan="2" style="padding:0 0 14px;color:#6f7d95;font-size:13px;line-height:1.5;">{{ $messagePlan['description'] }}</td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td align="center" style="padding:28px 30px 34px;">
                            <p style="margin:0;color:#263e68;font-size:18px;line-height:1.55;">{{ $messageText }}</p>
                            <p style="margin:16px 0 0;color:#7350c8;font-size:17px;line-height:1.5;">Imaš dovoljno vremena da pripremiš lepu poruku. 🎉</p>
                        </td>
                    </tr>
                    <tr>
                        <td align="center" style="padding:19px 24px;background:#faf7ff;border-top:1px solid #eee9ff;">
                            <p style="margin:0;color:#70809b;font-size:12px;line-height:1.55;">Ovaj podsetnik je poslat putem Rođendanka.</p>
                            <p style="margin:5px 0 0;color:#70809b;font-size:12px;line-height:1.55;">© Rođendanko</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
