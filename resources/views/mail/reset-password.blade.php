<!-- resources/views/mail/reset-password.blade.php -->
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <title>Reset Kata Sandi</title>
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <style>
    .wrapper{background:#f6f9fc;padding:24px 0}.container{max-width:600px;margin:0 auto;background:#fff;border:1px solid #eaeaea;border-radius:8px;overflow:hidden}
    .header{background:#0256c4;color:#fff;padding:20px 24px;font-family:Arial}
    .content{padding:24px;font-family:Arial;color:#333;line-height:1.6}
    .btn{display:inline-block;background:#0256c4;color:#fff!important;text-decoration:none;padding:12px 18px;border-radius:6px;font-weight:bold}
    .muted{color:#6b7280;font-size:12px}
    .footer{padding:16px 24px;text-align:center;font-family:Arial;color:#6b7280;font-size:12px}
  </style>
</head>
<body>
  <div class="wrapper">
    <table class="container" role="presentation" cellpadding="0" cellspacing="0">
      <tr><td class="header"><h2 style="margin:0">Reset Kata Sandi</h2></td></tr>
      <tr><td class="content">
        <p>Halo <strong>{{ $details['name'] ?? 'Pengguna' }}</strong>,</p>
        <p>Anda menerima email ini karena kami menerima permintaan reset kata sandi untuk akun Anda.</p>
        <p>
          <a href="{{ $details['url'] }}" class="btn">Atur Ulang Kata Sandi</a>
        </p>
        <p class="muted">Tautan akan kadaluarsa pada <strong>
          {{ \Carbon\Carbon::parse($details['expires_at'])->locale('id')->translatedFormat('d M Y, H:i') }}
        </strong>.</p>
        <p class="muted" style="margin-top:10px;">
          Jika tombol tidak bekerja, salin URL berikut ke peramban Anda:<br>
          <span style="word-break:break-all">{{ $details['url'] }}</span>
        </p>
        <p class="muted" style="margin-top:18px;">Jika Anda tidak meminta reset, abaikan email ini.</p>
      </td></tr>
      <tr><td class="footer">© {{ now()->year }} {{ config('app.name') }} • Semua hak dilindungi.</td></tr>
    </table>
  </div>
</body>
</html>
