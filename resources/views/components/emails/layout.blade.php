<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $subject ?? config('app.name') }}</title>
    <style>
        body { margin: 0; padding: 0; background: #f3f4f6; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; }
        .wrapper { max-width: 560px; margin: 40px auto; background: #fff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 24px rgba(0,0,0,.06); }
        .header { background: linear-gradient(135deg, #4f46e5, #7c3aed); padding: 32px 40px 28px; text-align: center; }
        .header-logo { color: #fff; font-size: 22px; font-weight: 800; letter-spacing: -.5px; }
        .body { padding: 36px 40px; }
        .footer { padding: 20px 40px; background: #f9fafb; border-top: 1px solid #e5e7eb; text-align: center; font-size: 12px; color: #9ca3af; }
        h2 { color: #111827; font-size: 20px; margin: 0 0 12px; }
        p { color: #4b5563; font-size: 15px; line-height: 1.7; margin: 0 0 16px; }
        .btn { display: inline-block; padding: 13px 28px; background: #4f46e5; color: #fff !important; text-decoration: none; border-radius: 10px; font-weight: 700; font-size: 15px; margin: 8px 0 20px; }
        .divider { border: none; border-top: 1px solid #e5e7eb; margin: 24px 0; }
        .small { font-size: 13px; color: #9ca3af; }
        .highlight { background: #f0f9ff; border: 1px solid #bae6fd; border-radius: 8px; padding: 14px 18px; margin: 16px 0; }
        .highlight strong { color: #0369a1; font-size: 17px; letter-spacing: 1px; }
    </style>
</head>
<body>
<div class="wrapper">
    <div class="header">
        <div class="header-logo">{{ config('app.name', 'Executive Map') }}</div>
    </div>
    <div class="body">
        {{ $slot }}
    </div>
    <div class="footer">
        © {{ date('Y') }} {{ config('app.name', 'Executive Map') }} · Todos os direitos reservados<br>
        Este e-mail foi enviado automaticamente. Por favor, não responda.
    </div>
</div>
</body>
</html>
