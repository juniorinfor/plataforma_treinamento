<x-emails.layout>
    <h2>Bem-vindo(a) à plataforma, {{ $user->name }}!</h2>

    <p>
        Você foi convidado(a) por <strong>{{ $companyName }}</strong> para acessar a plataforma de
        treinamento e desenvolvimento <strong>{{ config('app.name') }}</strong>.
    </p>

    <p>Suas credenciais de acesso são:</p>

    <div class="highlight">
        <table style="width:100%; border-collapse:collapse;">
            <tr>
                <td style="color:#6b7280; font-size:13px; padding: 4px 0; width:30%;">E-mail</td>
                <td style="font-weight:600; color:#111827;">{{ $user->email }}</td>
            </tr>
            <tr>
                <td style="color:#6b7280; font-size:13px; padding: 4px 0;">Senha temporária</td>
                <td><strong style="color:#4f46e5; font-size:17px; letter-spacing:1px;">{{ $temporaryPassword }}</strong></td>
            </tr>
        </table>
    </div>

    <p>
        <a href="{{ route('login') }}" class="btn">Acessar a plataforma</a>
    </p>

    <hr class="divider">

    <p class="small">
        ⚠️ Por segurança, <strong>altere sua senha</strong> após o primeiro acesso.<br>
        Se você não esperava este convite, ignore este e-mail.
    </p>
</x-emails.layout>
