<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <title>{{ $details['subject'] ?? 'Notifikasi Jadwal' }}</title>
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <style>
    .wrapper { width:100%; background:#f6f9fc; padding:24px 0; }
    .container { width:100%; max-width:600px; margin:0 auto; background:#fff; border-radius:8px; overflow:hidden; border:1px solid #eaeaea; }
    .header { padding:20px 24px; background:#0256c4; color:#fff; font-family:Arial,sans-serif; }
    .content { padding:24px; font-family:Arial,sans-serif; color:#333; line-height:1.6; }
    .btn { display:inline-block; padding:12px 18px; border-radius:6px; text-decoration:none; background:#0256c4; color:#fff !important; font-weight:bold; }
    .muted { color:#6b7280; font-size:12px; }
    .table { width:100%; border-collapse:collapse; margin:12px 0 4px; }
    .table td { padding:6px 0; vertical-align:top; }
    .badge { display:inline-block; font-size:12px; padding:4px 8px; border-radius:999px; background:#eef2ff; color:#3730a3; font-weight:600; }
  </style>
</head>
<body>
  <div class="wrapper">
    <table role="presentation" class="container" cellpadding="0" cellspacing="0" align="center">
      <tr>
        <td class="header">
          <h2 style="margin:0;">{{ $details['subject'] ?? 'Notifikasi Jadwal' }}</h2>
          <div style="font-size:13px; opacity:.9;">{{ $details['app_name'] ?? config('app.name') }}</div>
        </td>
      </tr>
      <tr>
        <td class="content">
          <p>Halo <strong>{{ $details['recipient_name'] ?? 'Pengguna' }}</strong>,</p>

          @php $type = $details['type'] ?? ''; @endphp

          @if($type === 'created')
            <p>Anda memiliki <strong>jadwal baru</strong> terkait aktivitas berikut:</p>
          @elseif($type === 'updated')
            <p>Jadwal berikut telah <strong>diperbarui</strong>:</p>
          @elseif($type === 'deleted')
            <p>Jadwal berikut telah <strong>dibatalkan</strong>:</p>
          @else
            <p>Perubahan terjadi pada jadwal berikut:</p>
          @endif

          <table class="table">
            <tr>
              <td style="width:150px;">Nama Aktivitas</td>
              <td style="width:18px;">:</td>
              <td>{{ $details['activity_name'] ?? '-' }}</td>
            </tr>
            <tr>
              <td>Kode Tracking</td>
              <td>:</td>
              <td>{{ $details['tracking_code'] ?? '-' }}</td>
            </tr>
            <tr>
              <td>Notaris</td>
              <td>:</td>
              <td>{{ $details['notary_name'] ?? '-' }}</td>
            </tr>
            @if(!empty($details['place']))
            <tr>
              <td>Lokasi</td>
              <td>:</td>
              <td>{{ $details['place'] }}</td>
            </tr>
            @endif
            @if(!empty($details['date_str']))
            <tr>
              <td>Waktu</td>
              <td>:</td>
              <td><span class="badge">{{ $details['date_str'] }}</span></td>
            </tr>
            @endif
            @if(!empty($details['notes']))
            <tr>
              <td>Catatan</td>
              <td>:</td>
              <td>{{ $details['notes'] }}</td>
            </tr>
            @endif
          </table>

          @if(!empty($details['url']))
            <p style="margin:16px 0 20px;">
              <a class="btn" href="{{ $details['url'] }}">Lihat Detail Jadwal</a>
            </p>
            <p class="muted" style="margin-top:10px;">
              Jika tombol tidak berfungsi, salin & tempel URL berikut:<br>
              <span style="word-break:break-all;">{{ $details['url'] }}</span>
            </p>
          @endif

          <p class="muted" style="margin-top:18px;">
            Email ini dikirim otomatis oleh {{ $details['app_name'] ?? config('app.name') }}.
          </p>
        </td>
      </tr>
      <tr>
        <td class="footer" style="padding:16px 24px; text-align:center; font-family:Arial,sans-serif; color:#6b7280; font-size:12px;">
          © {{ now()->year }} {{ $details['app_name'] ?? config('app.name') }} • Semua hak dilindungi.
        </td>
      </tr>
    </table>
  </div>
</body>
</html>
