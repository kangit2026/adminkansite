<!doctype html>
<html lang="uk">
<head>
    <meta charset="utf-8">
    <title>Новий відгук із сайту</title>
</head>
<body style="margin:0;padding:24px;background:#e8eaef;font-family:Arial,Helvetica,sans-serif;color:#12141c;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:560px;margin:0 auto;background:#ffffff;border:1px solid #d5d9e2;border-radius:12px;">
        <tr>
            <td style="padding:28px 32px;">
                <p style="margin:0 0 6px;font-size:13px;font-weight:bold;color:#120f82;">KAN · Розділ «Кар’єра»</p>
                <h1 style="margin:0 0 18px;font-size:21px;line-height:1.3;">
                    {{ $candidate->vacancy_title ? 'Новий відгук із сайту' : 'Нове резюме до загальної бази' }}
                </h1>
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="font-size:14px;line-height:1.5;border-top:1px solid #eceef2;">
                    @foreach ([
                        'Кандидат' => $candidate->name,
                        'Вакансія' => $candidate->vacancyLabel().(! $candidate->vacancy_title && $candidate->direction ? ' · напрям: '.$candidate->direction : ''),
                        'Телефон' => $candidate->phone,
                        'Email' => $candidate->email,
                        'Надіслано' => $candidate->created_at->format('d.m.Y').' о '.$candidate->created_at->format('H:i'),
                        'Резюме' => $candidate->cv_path ? 'додано' : 'не додано',
                    ] as $label => $value)
                        <tr>
                            <td style="padding:9px 12px 9px 0;border-bottom:1px solid #eceef2;color:#5c6370;width:110px;vertical-align:top;">{{ $label }}</td>
                            <td style="padding:9px 0;border-bottom:1px solid #eceef2;">{{ $value }}</td>
                        </tr>
                    @endforeach
                </table>
                <p style="margin:22px 0 0;">
                    <a href="{{ route('admin.candidates.show', $candidate) }}" style="display:inline-block;padding:12px 20px;background:#120f82;color:#ffffff;border-radius:9px;font-size:14px;font-weight:bold;text-decoration:none;">Відкрити картку кандидата</a>
                </p>
                <p style="margin:18px 0 0;font-size:12px;color:#5c6370;">Резюме й контакти зберігаються в адмінці. Щоб їх відкрити, увійдіть під своїм обліковим записом.</p>
            </td>
        </tr>
    </table>
</body>
</html>
