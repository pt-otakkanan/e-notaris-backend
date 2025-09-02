<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <title>Verifikasi Akun Anda</title>
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <style>
    /* Banyak klien email menghapus <style> kompleks—tetap minimal */
    .wrapper { width: 100%; background: #f6f9fc; padding: 24px 0; }
    .container { width: 100%; max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 8px; overflow: hidden; border: 1px solid #eaeaea; }
    .header { padding: 20px 24px; background: #0256c4; color: #ffffff; font-family: Arial, sans-serif; }
    .content { padding: 24px; font-family: Arial, sans-serif; color: #333333; line-height: 1.6; }
    .table { width: 100%; border-collapse: collapse; margin: 12px 0 4px; }
    .table td { padding: 6px 0; vertical-align: top; }
    .code-box { font-family: "Courier New", monospace; font-size: 22px; letter-spacing: 2px; background: #f1f5f9; padding: 12px 16px; text-align: center; border-radius: 6px; border: 1px dashed #cbd5e1; }
    .btn { display: inline-block; padding: 12px 18px; border-radius: 6px; text-decoration: none; background: #0256c4; color: #ffffff !important; font-weight: bold; }
    .muted { color: #6b7280; font-size: 12px; }
    .footer { padding: 16px 24px; text-align: center; font-family: Arial, sans-serif; color: #6b7280; font-size: 12px; }
    .preheader { display:none!important; visibility:hidden; opacity:0; color:transparent; height:0; width:0; overflow:hidden; }
  </style>
</head>
<body>
  <!-- Preheader (teks kecil yang terlihat di preview email) -->
  <div class="preheader">
    Kode verifikasi Anda: {{ $details['kode'] ?? 'XXXXXXX' }}. Berlaku hingga {{ isset($details['expires_at']) ? \Carbon\Carbon::parse($details['expires_at'])->locale('id')->translatedFormat('d M Y H:i') : '1 jam ke depan' }}.
  </div>

  <div class="wrapper">
    <table role="presentation" class="container" cellpadding="0" cellspacing="0" align="center">
      <tr>
        <td class="header">
          <h2 style="margin:0;">Verifikasi Akun Anda</h2>
          <div style="font-size:13px; opacity:.9;">
            {{ $details['website'] ?? config('app.name') }}
          </div>
        </td>
      </tr>
      <tr>
        <td class="content">
          <p>Halo <strong>{{ $details['name'] ?? 'Pengguna' }}</strong>,</p>
          <p>Terima kasih telah mendaftar. Berikut ringkasan data Anda:</p>

          <table class="table">
            <tr>
              <td style="width:110px;">Nama</td>
              <td style="width:18px;">:</td>
              <td>{{ $details['name'] ?? '-' }}</td>
            </tr>
            <tr>
              <td>Sebagai</td>
              <td>:</td>
              <td>{{ $details['role_label'] ?? 'User' }}</td>
            </tr>
            @if(!empty($details['website']))
            <tr>
              <td>Website</td>
              <td>:</td>
              <td>{{ $details['website'] }}</td>
            </tr>
            @endif
          </table>

          <p>Kode verifikasi Anda:</p>
          <div class="code-box">
            {{ $details['kode'] ?? 'XXXXXXX' }}
          </div>

          <p style="margin:16px 0 20px;">
            Kode berlaku hingga
            <strong>
              {{ isset($details['expires_at'])
                  ? \Carbon\Carbon::parse($details['expires_at'])->locale('id')->translatedFormat('d M Y, H:i')
                  : '1 jam dari sekarang' }}
            </strong>.
          </p>

          @if(!empty($details['url']))
            <p style="margin:0 0 14px;">
              <a class="btn" href="{{ $details['url'] }}">Buka Halaman Verifikasi</a>
            </p>
            <p class="muted" style="margin-top:10px;">
              Jika tombol tidak berfungsi, salin dan tempel URL berikut di peramban Anda:<br>
              <span style="word-break: break-all;">{{ $details['url'] }}</span>
            </p>
          @endif

          <p class="muted" style="margin-top:18px;">
            Jika Anda tidak merasa membuat akun, abaikan email ini.
          </p>
        </td>
      </tr>
      <tr>
        <td class="footer">
          © {{ now()->year }} {{ $details['website'] ?? config('app.name') }} • Semua hak dilindungi.
        </td>
      </tr>
    </table>
  </div>
</body>
</html>
