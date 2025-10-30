<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TemplatesTableSeeder extends Seeder
{
    /**
     * Jalankan database seeder.
     */
    public function run(): void
    {
        // Kumpulan template (yang lama + yang baru ditambahkan)
        $templates = [
            // --- Sudah ada di seeder kamu ---
            [
                'name'        => 'Perjanjian Sewa Menyewa',
                'description' => 'Perjanjian sewa menyewa properti/ruangan berdasarkan kesepakatan para pihak.',
                'custom_value' => <<<'HTML'
<h2 class="ql-align-center">PERJANJIAN SEWA MENYEWA</h2><p class="ql-align-center">Nomor : {{reference_number}}</p><p>– Pada hari ini, {{today}}</p><p>– tanggal .............................................................</p><p>– Pukul .................................................................</p><p>– Berhadapan dengan saya, {{notaris_name}}, Notaris di {{schedule_place}}, dengan dihadiri oleh para saksi yang saya, Notaris, kenal dan akan disebutkan nama-namanya pada bahagian akhir akta ini:</p><p><strong>I. Tuan {{penghadap1_name}}</strong></p><p> ..............................................................</p><p> ..............................................................</p><p> ..............................................................</p><p><strong>II. Tuan {{penghadap2_name}}</strong></p><p> ..............................................................</p><p> ..............................................................</p><p> ..............................................................</p><p>– menurut keterangannya dalam hal ini bertindak dalam jabatannya selaku Presiden Direktur dari Perseroan Terbatas PT. .........., berkedudukan di Jakarta yang anggaran dasarnya beserta perubahannya telah mendapat persetujuan dari Menteri Kehakiman dan Hak Asasi Manusia berturut-turut:</p><p>..............................................................</p><p> ..............................................................</p><p> ..............................................................</p><p> ..............................................................</p><p>selanjutnya disebut: <strong>Pihak Kedua</strong> atau <strong>Penyewa</strong>.</p><p>– Para penghadap telah saya, Notaris, kenal.</p><p>– Para penghadap menerangkan terlebih dahulu:</p><p>– bahwa Pihak Pertama adalah pemilik dari bangunan Rumah Toko (Ruko) yang hendak disewakan kepada Pihak Kedua yang akan disebutkan di bawah ini dan Pihak Kedua menerangkan menyewa dari Pihak Pertama berupa:</p><p>– 1 (satu) unit bangunan Rumah Toko (Ruko) berlantai 3 (tiga) berikut turutannya, lantai keramik, dinding tembok, atap dak, aliran listrik sebesar 2.200 Watt, dilengkapi air dari jet pump, berdiri di atas sebidang tanah Sertifikat HGB Nomor: ............ seluas ...... m² (....................................), penerbitan sertifikat tanggal ..........................., tercantum atas nama .................. yang telah diuraikan dalam Gambar Situasi tanggal ............ nomor ............; Sertifikat tanah diterbitkan oleh Kantor Pertanahan Kabupaten Bekasi, terletak di Provinsi Jawa Barat, Kabupaten Bekasi, Kecamatan Cibitung, Desa Ganda Mekar, setempat dikenal sebagai Mega Mall MM.2100 Blok B Nomor 8.</p><p>– Berdasarkan keterangan-keterangan tersebut di atas, kedua belah pihak sepakat membuat perjanjian sewa-menyewa dengan syarat-syarat dan ketentuan-ketentuan sebagai berikut:</p><p><strong>----------------------- Pasal 1.</strong></p><p>Perjanjian sewa-menyewa ini berlangsung untuk jangka waktu 2 (dua) tahun terhitung sejak tanggal ............ sampai dengan tanggal ............</p><p>– Penyerahan Ruko akan dilakukan dalam keadaan kosong/tidak dihuni pada tanggal .................. dengan penyerahan semua kunci-kuncinya.</p><p><strong>----------------------- Pasal 2.</strong></p><p>– Uang kontrak sewa disepakati sebesar Rp. ............ (....................................) untuk 2 (dua) tahun masa sewa.</p><p>– Jumlah uang sewa sebesar Rp. ............ (....................................) tersebut dibayar oleh Pihak Kedua kepada Pihak Pertama pada saat penandatanganan akta ini atau pada tanggal .................. dengan kwitansi tersendiri, dan akta ini berlaku sebagai tanda penerimaan yang sah.</p><p><strong>----------------------- Pasal 3.</strong></p><p>– Pihak Kedua hanya akan menggunakan yang disewakan dalam akta ini sebagai tempat kegiatan perkantoran/usaha.</p><p>– Jika diperlukan, Pihak Pertama memberikan surat rekomendasi/keterangan yang diperlukan Pihak Kedua sepanjang tidak melanggar hukum.</p><p>– Pihak Kedua wajib mentaati peraturan-peraturan pihak yang berwajib dan menjamin Pihak Pertama tidak mendapat teguran/tuntutan apapun karenanya.</p><p><strong>----------------------- Pasal 4.</strong></p><p>– Hanya dengan persetujuan tertulis Pihak Pertama, Pihak Kedua boleh mengadakan perubahan/penambahan pada bangunan; seluruh biaya dan tanggung jawab pada Pihak Kedua, dan pada akhir masa kontrak menjadi hak Pihak Pertama.</p><p>– Penyerahan nyata dari yang disewakan oleh Pihak Pertama kepada Pihak Kedua dilakukan pada tanggal .................. dengan penyerahan semua kunci-kunci.</p><p><strong>----------------------- Pasal 5.</strong></p><p>Pihak Pertama memberi izin kepada Pihak Kedua untuk pemasangan/penambahan antara lain:</p><ol><li>Sekat-sekat pada ruangan;</li><li>Antena radio/CD;</li><li>Line telepon;</li><li>Air Conditioner (AC);</li><li>Penambahan daya listrik;</li><li>Saluran fax;</li><li>Internet;</li><li>TV Kabel;</li><li>Shower;</li><li>Penggantian W/C;</li><li>Katrol pengangkut barang lantai 1–3;</li><li>Peralatan keamanan;</li><li>Peralatan pendukung usaha (rak/mesin) tanpa merusak struktur bangunan.</li></ol><p>– Setelah masa kontrak berakhir, Pihak Kedua mengembalikan seperti keadaan semula dengan biaya Pihak Kedua.</p><p>– Pihak Kedua boleh mengganti kunci ruangan di dalam bangunan (kecuali pintu utama); pada akhir masa kontrak, kunci-kunci diserahkan ke Pihak Pertama.</p><p>– Pihak Pertama menjamin yang disewakan adalah miliknya dan bebas dari tuntutan pihak lain.</p><p>– Selama masa sewa, Pihak Pertama boleh memeriksa bangunan sewaktu-waktu.</p><p><strong>----------------------- Pasal 6.</strong></p><p>– Selama masa kontrak, pembayaran langganan listrik/air/telepon dan kewajiban lain terkait pemakaian dibayar Pihak Kedua hingga bulan terakhir dengan bukti pembayaran setiap bulan.</p><p>– Pihak Pertama membayar Pajak Bumi dan Bangunan (PBB) untuk objek sewa.</p><p><strong>----------------------- Pasal 7.</strong></p><p>– Pihak Kedua wajib memelihara yang disewa dengan baik; kerusakan karena kelalaian diperbaiki atas biaya Pihak Kedua.</p><p>– Apabila terjadi force majeure (kebakaran—kecuali kelalaian Pihak Kedua—sabotase, badai, banjir, gempa) sehingga objek musnah, para pihak dibebaskan dari tuntutan.</p><p><strong>----------------------- Pasal 8.</strong></p><p>– Pihak Pertama menjamin tidak ada tuntutan atau gangguan dari pihak lain atas yang disewa selama kontrak.</p><p><strong>----------------------- Pasal 9.</strong></p><p>Pihak Kedua, dengan persetujuan tertulis Pihak Pertama, boleh mengalihkan/memindahkan hak kontrak pada pihak lain, sebagian maupun seluruhnya, selama masa kontrak berlaku.</p><p><strong>----------------------- Pasal 10.</strong></p><p>Pihak Kedua wajib memberi pemberitahuan mengenai berakhir/akan diperpanjangnya kontrak kepada Pihak Pertama selambat-lambatnya 2 (dua) bulan sebelum berakhir.</p><p><strong>----------------------- Pasal 11.</strong></p><p>Pada saat berakhirnya kontrak dan tidak ada perpanjangan, Pihak Kedua menyerahkan kembali objek sewa dalam keadaan kosong, terpelihara baik, dengan semua kunci pada tanggal ..................</p><p>Apabila terlambat, Pihak Kedua dikenakan denda sebesar Rp. 27.500,- per hari selama 7 (tujuh) hari pertama; jika masih tidak diserahkan, Pihak Kedua memberi kuasa kepada Pihak Pertama (dengan hak substitusi) untuk melakukan pengosongan dengan bantuan pihak berwajib, atas biaya dan risiko Pihak Kedua.</p><p><strong>----------------------- Pasal 12.</strong></p><p>Selama masa kontrak belum berakhir, perjanjian ini tidak berakhir karena:</p><ol><li>Meninggalnya salah satu pihak;</li><li>Pihak Pertama mengalihkan hak milik atas objek sewa kepada pihak lain;</li><li>Dalam hal salah satu pihak meninggal dunia, ahli waris/penggantinya wajib melanjutkan perjanjian sampai berakhir; pemilik baru tunduk pada seluruh ketentuan akta ini.</li></ol><p><strong>----------------------- Pasal 13.</strong></p><p>Untuk menjamin pembayaran listrik, air, telepon, keamanan, dan kewajiban lain bulan terakhir, Pihak Kedua menyerahkan uang jaminan sebesar Rp. 2.000.000,- (dua juta rupiah) pada saat penyerahan kunci, dengan kwitansi tersendiri. Kelebihan dikembalikan Pihak Pertama; kekurangan ditambah oleh Pihak Kedua.</p><p><strong>----------------------- Pasal 14.</strong></p><p>Hal-hal yang belum cukup diatur akan dibicarakan kemudian secara musyawarah untuk mufakat.</p><p><strong>----------------------- Pasal 15.</strong></p><p>Pajak-pajak yang mungkin ada terkait akta ini dibayar oleh Pihak Kedua untuk dan atas nama Pihak Pertama.</p><p><strong>----------------------- Pasal 16.</strong></p><p>Biaya-biaya yang berkaitan dengan akta ini dibayar dan menjadi tanggungan Pihak Pertama.</p><p><strong>----------------------- Pasal 17.</strong></p><p>Kedua belah pihak memilih domisili hukum yang sah di Kepaniteraan Pengadilan Negeri Bekasi.</p><p><strong>DEMIKIAN AKTA INI</strong></p><p>– Dibuat dan diresmikan di Bekasi pada hari dan tanggal sebagaimana awal akta ini, dengan dihadiri oleh:</p><ol><li>Nyonya ........................................</li><li>Nyonya ........................................</li></ol><p>Keduanya Karyawan Kantor Notaris, sebagai saksi-saksi.</p><p>– Setelah akta ini dibacakan oleh saya, Notaris, kepada para penghadap dan para saksi, maka segera ditandatangani oleh para penghadap, para saksi, dan saya, Notaris.</p>
HTML,
                'created_at'  => Carbon::parse('2025-09-10 10:22:38'),
                'updated_at'  => Carbon::parse('2025-09-28 04:39:11'),
            ],

            [
                'name'        => 'Hak Waris',
                'description' => 'Penetapan ahli waris dan pembagian harta peninggalan pewaris.',
                // FIX: hapus typo "...</p>s'"
                'custom_value' => <<<'HTML'
<div class="ql-align-center">
  <h2 style="margin:0; font-weight:700;">KETERANGAN HAK WARIS</h2>
  <div style="margin-top:4px;">Nomor: {{reference_number}}</div>
</div>

<p class="ql-align-justify">
  Pada hari ini, hari {{day_name}}, tanggal {{date_long}}, pukul {{time_wib}} WIB ({{time_wib_words}} Waktu Indonesia bagian Barat),
  menghadap di hadapan saya, {{notary_name}}, Sarjana Hukum, Notaris di {{city}}, dengan dihadiri oleh saksi-saksi yang saya,
  Notaris, kenal dan akan disebut pada bagian akhir akta ini.
</p>

<h3>I. PENGHADAP PERTAMA</h3>
<p class="ql-align-justify no-page-break">
  Nyonya {{party1_fullname}} {{party1_alias_opt}}, {{party1_job}}, bertempat tinggal di {{party1_address_full}};
  Kartu Tanda Penduduk Nomor: {{party1_ktp}}.
</p>

<h3>II. PENGHADAP KEDUA</h3>
<p class="ql-align-justify">
  Nyonya {{party2_fullname}} {{party2_alias_opt}}, {{party2_job}}, bertempat tinggal di {{party2_address_full}};
  Kartu Tanda Penduduk Nomor: {{party2_ktp}}.
</p>

<p class="ql-align-justify">
  Para penghadap tersebut telah dikenal oleh saya, Notaris.
</p>

<div class="page-break-after"></div>

<h3>KETERANGAN PARA PENGHADAP</h3>

<p class="ql-align-justify">
  Bahwa almarhum Tuan {{pewaris_name}} {{pewaris_alias_opt}}, Warganegara Indonesia, telah meninggal dunia di {{pewaris_death_city}},
  pada tanggal {{pewaris_death_date_long}} ({{pewaris_death_date_num}}), demikian seperti ternyata dari Akta Kematian tertanggal
  {{akta_kematian_date_long}} ({{akta_kematian_date_num}}) Nomor {{akta_kematian_number}} yang dikeluarkan oleh
  {{akta_kematian_issuer}}; akta mana aslinya diperlihatkan kepada saya, Notaris.
</p>

<p class="ql-align-justify">
  Bahwa almarhum Tuan {{pewaris_name_short}} {{pewaris_alias_opt}} selanjutnya akan disebut juga “pewaris”, menurut keterangan para
  penghadap telah kawin sah dengan Nyonya {{spouse_fullname}} {{spouse_alias_opt}}, demikian berdasarkan Akta Perkawinan/Golongan
  Tionghoa tanggal {{akta_kawin_date_long}} ({{akta_kawin_date_num}}) Nomor {{akta_kawin_number}} yang dikeluarkan oleh
  {{akta_kawin_issuer}}; akta mana aslinya diperlihatkan kepada saya, Notaris.
</p>

<p class="ql-align-justify">
  Bahwa dari perkawinan tersebut telah dilahirkan {{children_count_words}} ({{children_count_num}}) orang anak, yaitu:
</p>

<ol class="ql-align-justify" style="padding-left:24px;">
  <li class="keep-together">
    Penghadap Nyonya {{child1_now_name}}, dahulu bernama {{child1_old_name_opt}}, disebut juga {{child1_alias_opt}},
    yang dilahirkan pada tanggal {{child1_birth_date_long}} ({{child1_birth_date_num}}) di {{child1_birth_city}},
    berdasarkan Akta Kelahiran tanggal {{child1_akta_date_long}} ({{child1_akta_date_num}}) Nomor {{child1_akta_number}}
    yang dikeluarkan oleh {{child1_akta_issuer}}; aslinya diperlihatkan kepada saya, Notaris.
  </li>
  <li class="keep-together">
    Nyonya {{child2_now_name}}, yang dilahirkan di {{child2_birth_city}}, pada tanggal {{child2_birth_date_long}} ({{child2_birth_date_num}}),
    berdasarkan Akta Kelahiran tanggal {{child2_akta_date_long}} ({{child2_akta_date_num}}) Nomor {{child2_akta_number}}
    yang dikeluarkan oleh {{child2_akta_issuer}}; aslinya diperlihatkan kepada saya, Notaris.
  </li>
  <li class="keep-together">
    Nona {{child3_now_name}}, disebut juga {{child3_alias_opt}}, sekarang bernama {{child3_current_name_opt}},
    dilahirkan di {{child3_birth_city}} pada tanggal {{child3_birth_date_long}} ({{child3_birth_date_num}}),
    berdasarkan Akta Kelahiran tanggal {{child3_akta_date_long}} ({{child3_akta_date_num}}) Nomor {{child3_akta_number}}
    yang dikeluarkan oleh {{child3_akta_issuer}}; aslinya diperlihatkan kepada saya, Notaris.
  </li>
  <li class="keep-together">
    Tuan {{child4_now_name}}, dilahirkan pada tanggal {{child4_birth_date_long}} ({{child4_birth_date_num}}),
    berdasarkan Akta Kelahiran tanggal {{child4_akta_date_long}} ({{child4_akta_date_num}}) Nomor {{child4_akta_number}}
    yang dikeluarkan oleh {{child4_akta_issuer}}; aslinya diperlihatkan kepada saya, Notaris.
  </li>
  <li class="keep-together">
    Tuan/Nona {{child5_now_name}}, dilahirkan di {{child5_birth_city}} pada tanggal {{child5_birth_date_long}} ({{child5_birth_date_num}}),
    berdasarkan Akta Kelahiran tanggal {{child5_akta_date_long}} ({{child5_akta_date_num}}) Nomor {{child5_akta_number}}
    yang dikeluarkan oleh {{child5_akta_issuer}}; aslinya diperlihatkan kepada saya, Notaris.
  </li>
</ol>

<p class="ql-align-justify">
  Bahwa “pewaris” tidak meninggalkan turunan atau saudara lain selain dari para penghadap dan {{child2_now_name}},
  {{child3_now_name}} {{child3_current_name_opt}}, {{child4_now_name}}, dan {{child5_now_name}} tersebut.
</p>

<p class="ql-align-justify">
  Bahwa menurut surat dari {{no_will_issuer}} tanggal {{no_will_date_long}} ({{no_will_date_num}}) Nomor {{no_will_number}},
  “pewaris” tidak meninggalkan surat wasiat.
</p>

<div class="page-break-after"></div>

<h3>PERNYATAAN</h3>
<p class="ql-align-justify">
  Para penghadap tersebut di atas selanjutnya dengan ini menerangkan:
</p>
<ul class="ql-align-justify" style="padding-left:24px;">
  <li>Bahwa para penghadap mengetahui dan dapat membenarkan segala sesuatu yang diuraikan di atas;</li>
  <li>Bahwa para penghadap bersedia jika perlu memperkuat segala sesuatu yang diuraikan di atas dengan sumpah.</li>
</ul>

<p class="ql-align-justify">
  Maka sekarang berdasarkan keterangan-keterangan tersebut di atas dan surat-surat yang diperlihatkan kepada saya, Notaris, serta
  berdasarkan hukum yang berlaku bagi para penghadap dan {{child2_now_name}}, {{child3_now_name}} {{child3_current_name_opt}},
  {{child4_now_name}}, dan {{child5_now_name}}, maka saya, Notaris, menerangkan dalam akta ini:
</p>

<h3>PEMBAGIAN HAK ATAS HARTA PENINGGALAN</h3>
<ol class="ql-align-justify" style="padding-left:24px;">
  <li>Nyonya {{spouse_fullname}} mendapat {{portion_spouse}} bagian.</li>
  <li>Nyonya {{party2_fullname_short}} {{party2_alias_opt}} mendapat {{portion_child2}} bagian.</li>
  <li>Nyonya {{child2_now_name}} mendapat {{portion_childB}} bagian.</li>
  <li>Nona {{child3_now_name}} {{child3_alias_opt}} {{child3_current_name_opt}} mendapat {{portion_childC}} bagian.</li>
  <li>Tuan {{child4_now_name}} mendapat {{portion_childD}} bagian.</li>
  <li>Nona/Tuan {{child5_now_name}} mendapat {{portion_childE}} bagian.</li>
</ol>

<p class="ql-align-justify">
  Bahwa para penghadap dan {{child2_now_name}}, {{child3_now_name}} {{child3_current_name_opt}}, {{child4_now_name}}, dan {{child5_now_name}},
  merupakan para ahli waris tersendiri dari “pewaris” dengan mengecualikan siapapun juga, serta berhak untuk menuntut dan menerima
  seluruh barang-barang dan harta kekayaan yang termasuk harta peninggalan “pewaris”. Selanjutnya, mereka berhak memberi
  tanda terima untuk segala penerimaan harta kekayaan dan barang.
</p>

<p class="ql-align-justify">
  Dari segala sesuatu yang tersebut di atas ini dengan segala akibat-akibatnya, para penghadap telah memilih tempat kediaman
  hukum yang sah dan tidak berubah di Kantor Panitera Pengadilan Negeri {{pengadilan_negeri_kota}}.
</p>

<div class="page-break-after"></div>

<h3>PENUTUP</h3>
<p class="ql-align-justify">
  Demikianlah akta ini, dibuat dengan dihadiri oleh Tuan {{witness1_name}} dan Tuan {{witness2_name}}, kedua-duanya Pegawai
  Kantor Notaris, bertempat tinggal di {{city}}, sebagai saksi-saksi.
</p>
<p class="ql-align-justify">
  Segera setelah akta ini dibacakan oleh saya, Notaris, kepada para penghadap dan para saksi, maka ditandatangani oleh para
  penghadap, para saksi, dan saya, Notaris.
</p>
<p class="ql-align-justify">
  Dilangsungkan dengan tanpa perubahan. Dilangsungkan dan diresmikan sebagai minuta di {{city}}, pada hari, tanggal, dan tahun seperti
  disebut pada awal. Minuta akta ini telah ditandatangani dengan sempurna. Diberikan sebagai salinan yang sama bunyinya.
</p>

<p class="ql-align-right" style="margin-top:32px;">
  {{city}}, {{date_long}}<br/>
  {{notary_name}}<br/>
  Notaris di {{city}}
</p>
HTML,
                'created_at'  => Carbon::parse('2025-09-09 17:00:00'),
                'updated_at'  => null,
            ],

            [
                'name'        => 'Perseroan Komanditer',
                'description' => 'Pendirian usaha bersama antara sekutu aktif dan sekutu pasif dalam bentuk CV.',
                'custom_value' => <<<'HTML'
<div style="text-align:center;margin-bottom:8px">
  <h2 style="margin:0">AKTA PENDIRIAN PERSEROAN KOMANDITER</h2>
  <div style="font-size:12px">Nomor : {{reference_number}}</div>
</div>

<p>– Pada hari ini, {{today}}</p>
<p>– tanggal .............................................................</p>
<p>– Pukul .................................................................</p>

<p>
– Berhadapan dengan saya, {{notaris_name}}, Notaris di {{schedule_place}},
dengan dihadiri oleh saksi-saksi yang saya, Notaris, kenal dan akan disebutkan pada bagian akhir akta ini:
</p>

<p><b>I. Tuan {{penghadap1_name}}</b><br/>
{{penghadap1_identitas_line1}}<br/>
{{penghadap1_identitas_line2}}<br/>
{{penghadap1_identitas_line3}}
</p>

<p><b>II. Nyonya {{penghadap2_name}}</b><br/>
{{penghadap2_identitas_line1}}<br/>
{{penghadap2_identitas_line2}}<br/>
{{penghadap2_identitas_line3}}
</p>

<p><b>III. Nyonya/Tuan {{penghadap3_name}}</b><br/>
{{penghadap3_identitas_line1}}<br/>
{{penghadap3_identitas_line2}}<br/>
{{penghadap3_identitas_line3}}
</p>

<p>– Para penghadap telah saya, Notaris, kenal.</p>

<p>
– Para penghadap menerangkan dengan akta ini telah saling setuju dan semufakat untuk mendirikan suatu
Perseroan Komanditer (Commanditaire Vennootschap) dengan Anggaran Dasar sebagai berikut:
</p>

<div class="page-break-after"></div>

<p style="text-align:center"><b>NAMA DAN TEMPAT KEDUDUKAN<br/>Pasal 1</b></p>
<ol style="margin-left:18px">
  <li>Perseroan ini bernama Perseroan Komanditer: <b>{{cv_name_upper}}</b> (selanjutnya disebut “Perseroan”).</li>
  <li>Perseroan berkedudukan di {{domisili_kota}}, dengan cabang/perwakilan di tempat lain yang dianggap perlu oleh (para) Pesero Pengurus.</li>
</ol>

<p style="text-align:center"><b>WAKTU<br/>Pasal 2</b></p>
<p>– Perseroan didirikan untuk waktu yang tidak ditentukan dan mulai berlaku sejak akta ini ditandatangani.</p>

<p style="text-align:center"><b>MAKSUD DAN TUJUAN<br/>Pasal 3</b></p>
<p>Maksud dan tujuan Perseroan sebagai berikut:</p>
<ol style="margin-left:18px">
  <li>Distribusi/supplier/leveransir/grosir/komisioner/keagenan berbagai barang (kecuali keagenan perjalanan);</li>
  <li>Perdagangan umum (impor, ekspor, lokal, antarpulau) sendiri maupun komisi;</li>
  <li>Industri (konveksi/garment, butik, alat rumah tangga, kerajinan, souvenir, kayu, besi);</li>
  <li>Jasa: perawatan/perbaikan elektrikal-mekanikal-teknikal & komputer; warnet/wartel/pos; cleaning service; boga; pengiriman barang;</li>
  <li>Kontraktor/biro bangunan (gedung, perumahan, jalan, jembatan, irigasi), pemasangan aluminium/gypsum/kaca/furnitur & instalasi listrik/air/gas/telekomunikasi;</li>
  <li>Pengadaan alat & kebutuhan kantor; pertamanan/landscaping; interior & eksterior; periklanan & reklame; percetakan/penjilidan/pengepakan;</li>
  <li>Pengangkutan darat; perbengkelan; perkebunan, kehutanan, pertanian, peternakan, perikanan;</li>
  <li>Segala kegiatan lain yang menunjang tujuan Perseroan sepanjang peraturan perundang-undangan.</li>
</ol>
<p>– Perseroan dapat mendirikan/ikut mendirikan badan lain yang sejenis di dalam/luar negeri sesuai peraturan.</p>

<div class="page-break-after"></div>

<p style="text-align:center"><b>MODAL<br/>Pasal 4</b></p>
<ol style="margin-left:18px">
  <li>Modal Perseroan tidak ditentukan besarnya; akan ternyata pada buku Perseroan, termasuk porsi tiap pesero.</li>
  <li>Setoran uang dan/atau inbreng dicatat pada perhitungan modal masing-masing dan diberi tanda bukti yang ditandatangani para pesero.</li>
  <li>(Para) Pesero Pengurus juga mencurahkan tenaga, pikiran, dan keahliannya untuk kepentingan Perseroan.</li>
</ol>

<p style="text-align:center"><b>PENGURUSAN & TANGGUNG JAWAB — (PARA) PESERO PENGURUS<br/>Pasal 5</b></p>
<ol style="margin-left:18px">
  <li>Tuan {{pesero_pengurus_name}} adalah Pesero Pengurus bertanggung jawab penuh; Nyonya/Tuan {{pesero_komanditer1_name}} dan {{pesero_komanditer2_name}} adalah Pesero Komanditer yang bertanggung jawab sampai modal yang dimasukkan.</li>
  <li>
    Tuan {{direktur_name}} selaku Direktur (atau wakil/yang ditunjuk bila berhalangan) mewakili dan mengikat Perseroan, namun untuk:
    <ol style="margin-left:18px">
      <li>Perolehan/pelepasan/pemindahan hak atas benda tetap;</li>
      <li>Meminjam/meminjamkan uang (kecuali penarikan dana Perseroan di bank/tempat lain);</li>
      <li>Menggadaikan/membebani harta Perseroan;</li>
      <li>Mengikat Perseroan sebagai penjamin;</li>
      <li>Mengangkat/mencabut kuasa;</li>
    </ol>
    – harus dengan persetujuan lebih dahulu/ turut ditandatangani Pesero Komanditer.
  </li>
  <li>(Para) Pesero Pengurus memegang buku-buku, uang, dan hal-hal lain usaha Perseroan; berwenang mengangkat/memberhentikan karyawan & menetapkan gaji.</li>
</ol>

<p style="text-align:center"><b>WEWENANG (PARA) PESERO KOMANDITER<br/>Pasal 6</b></p>
<ol style="margin-left:18px">
  <li>Berwenang memasuki aset Perseroan (kantor/gedung/bangunan) dan memeriksa buku-buku, uang, dan keadaan usaha.</li>
  <li>(Para) Pesero Pengurus wajib memberi keterangan yang diminta.</li>
</ol>

<p style="text-align:center"><b>PENGUNDURAN DIRI / MENINGGAL DUNIA / PAILIT<br/>Pasal 7–10</b></p>
<p>
– Ketentuan pengunduran diri (pemberitahuan ≥ 3 bulan), kelanjutan usaha bila pesero meninggal (dengan kuasa ahli waris ≤ 3 bulan), status keluar bila pailit/surseance/pengampuan, serta pembayaran bagian pesero yang keluar menurut neraca terakhir (≤ 3 bulan, tanpa bunga) dan hak pesero tersisa untuk melanjutkan usaha dengan sisa aktiva-pasiva dan tetap memakai nama Perseroan.
</p>

<div class="page-break-after"></div>

<p style="text-align:center"><b>PENUTUPAN BUKU & NERACA<br/>Pasal 11</b></p>
<ol style="margin-left:18px">
  <li>Setiap akhir Desember buku ditutup; paling lambat akhir Maret dibuat neraca & laba-rugi. Pertama kali ditutup: {{first_closing_date_long}} ({{first_closing_date_num}}).</li>
  <li>Dokumen disimpan di kantor Perseroan; dapat dilihat (Para) Pesero Komanditer 14 hari sejak dibuat.</li>
  <li>Jika tidak ada keberatan dalam 14 hari, dianggap sah dan semua pesero menandatangani (acquit et decharge kepada (Para) Pesero Pengurus).</li>
  <li>Bila tidak mufakat, dapat minta hakim menunjuk 3 arbiter; para pesero tunduk pada putusan para arbiter.</li>
</ol>

<p style="text-align:center"><b>KEUNTUNGAN (Pasal 12) — KERUGIAN (Pasal 13) — DANA CADANGAN (Pasal 14)</b></p>
<p>
– Keuntungan dibagi sesuai perbandingan modal; dibayarkan ≤ 1 bulan setelah pengesahan neraca/laba-rugi.
Kerugian ditanggung sesuai perbandingan; Pesero Komanditer hanya sampai modal setorannya. Dana cadangan dapat disisihkan/ digunakan sebagai modal kerja sesuai kesepakatan; hasil/rugi diperhitungkan pada laba-rugi.
</p>

<p style="text-align:center"><b>PENGALIHAN BAGIAN (Pasal 15) — HAL-HAL LAIN (Pasal 16) — DOMISILI (Pasal 17)</b></p>
<p>
– Pengalihan/pembebanan bagian pesero harus dengan persetujuan pesero lain. Hal yang belum cukup diatur diputuskan musyawarah.
Para pesero memilih domisili di Kepaniteraan Pengadilan Negeri {{domisili_kota}}.
</p>

<div class="page-break-after"></div>

<p><b>AKTA INI</b></p>
<p>– Dibuat sebagai minuta dan diresmikan di {{schedule_place}} pada hari dan tanggal seperti pada awal akta ini, dengan saksi-saksi:</p>
<ol style="margin-left:18px">
  <li>{{saksi1_name}}, {{saksi1_identitas_desc}}</li>
  <li>{{saksi2_name}}, {{saksi2_identitas_desc}}</li>
</ol>
<p>Keduanya Karyawan Kantor Notaris, sebagai saksi-saksi.</p>
<p>– Setelah akta ini dibacakan oleh saya, Notaris, kepada para penghadap dan para saksi, maka segera ditandatangani oleh para penghadap, para saksi, dan saya, Notaris.</p>
HTML,
                'created_at'  => Carbon::parse('2025-09-09 17:00:00'),
                'updated_at'  => null,
            ],

            [
                'name'        => 'Pendirian PT',
                'description' => 'Pendirian badan usaha berbentuk Perseroan Terbatas oleh para pendiri sesuai ketentuan hukum.',
                'custom_value' => <<<'HTML'
<h2 class="ql-align-center">AKTA PENDIRIAN PERSEROAN TERBATAS</h2><p class="ql-align-center">Nomor : {{reference_number}}</p><p>– Pada hari ini, {{today}}</p><p>– tanggal .............................................................</p><p>– Pukul .................................................................</p><p>– Berhadapan dengan saya, {{notaris_name}}, Notaris di {{schedule_place}}, dengan dihadiri oleh para saksi yang saya, Notaris, kenal dan akan disebutkan nama-namanya pada bagian akhir akta ini:</p><p><strong>I. {{penghadap1_name}}</strong></p><p>..............................................................</p><p>..............................................................</p><p>..............................................................</p><p><strong>II. {{penghadap2_name}}</strong></p><p>..............................................................</p><p>..............................................................</p><p>..............................................................</p><p>Para penghadap tersebut di atas, bertindak untuk dan atas nama diri mereka sendiri, dengan ini sepakat untuk mendirikan suatu badan hukum berbentuk <strong>Perseroan Terbatas</strong> (selanjutnya disebut “Perseroan”) dengan ketentuan sebagai berikut:</p><p><strong>----------------------- Pasal 1.</strong></p><p><strong>Nama dan Tempat Kedudukan</strong></p><p>Perseroan ini bernama <strong>PT. {{company_name}}</strong> dan berkedudukan di <strong>{{company_city}}</strong>.</p><p><strong>----------------------- Pasal 2.</strong></p><p><strong>Maksud dan Tujuan</strong></p><p>Perseroan didirikan dengan maksud dan tujuan untuk menjalankan usaha di bidang <strong>{{business_field}}</strong> serta kegiatan lain yang berhubungan dan mendukungnya, sesuai dengan ketentuan peraturan perundang-undangan yang berlaku.</p><p><strong>----------------------- Pasal 3.</strong></p><p><strong>Modal Perseroan</strong></p><p>Modal dasar Perseroan ditetapkan sebesar Rp. {{modal_dasar}},– ({{modal_dasar_terbilang}}), yang terbagi atas {{jumlah_saham}} ({{jumlah_saham_terbilang}}) saham, masing-masing bernilai nominal Rp. {{nilai_saham}},– ({{nilai_saham_terbilang}}).</p><p>Modal ditempatkan dan disetor penuh oleh para pendiri sebagai berikut:</p><ol><li>{{penghadap1_name}} sebesar Rp. {{modal_penghadap1}} ({{modal_penghadap1_terbilang}});</li><li>{{penghadap2_name}} sebesar Rp. {{modal_penghadap2}} ({{modal_penghadap2_terbilang}}).</li></ol><p><strong>----------------------- Pasal 4.</strong></p><p><strong>Susunan Pengurus</strong></p><p>Untuk pertama kalinya Perseroan menunjuk dan mengangkat:</p><ol><li>{{direktur_name}} sebagai Direktur;</li><li>{{komisaris_name}} sebagai Komisaris.</li></ol><p>Yang bersangkutan bersedia dan menerima jabatan tersebut.</p><p><strong>----------------------- Pasal 5.</strong></p><p><strong>Jangka Waktu</strong></p><p>Perseroan didirikan untuk jangka waktu yang tidak terbatas, terhitung sejak tanggal akta ini ditandatangani.</p><p><strong>----------------------- Pasal 6.</strong></p><p><strong>Anggaran Dasar dan Ketentuan Lain</strong></p><p>Hal-hal yang belum diatur dalam akta ini akan diatur lebih lanjut dalam anggaran dasar Perseroan dan/atau berdasarkan keputusan Rapat Umum Pemegang Saham sesuai dengan peraturan perundang-undangan yang berlaku.</p><p><strong>----------------------- Pasal 7.</strong></p><p><strong>Pengesahan dan Pendaftaran</strong></p><p>Notaris akan mengajukan permohonan pengesahan badan hukum Perseroan ini kepada Menteri Hukum dan Hak Asasi Manusia Republik Indonesia sesuai dengan ketentuan yang berlaku.</p><p><strong>----------------------- Penutup</strong></p><p>– Para penghadap telah saya, Notaris, kenal.</p><p>– Akta ini dibuat di {{schedule_place}} pada hari, tanggal, bulan, dan tahun sebagaimana tersebut di awal akta ini.</p><p>Setelah akta ini dibacakan oleh saya, Notaris, kepada para penghadap dan para saksi, maka segera ditandatangani oleh para penghadap, para saksi, dan saya, Notaris.</p><p><strong>Saksi-saksi:</strong></p><ol><li>Nyonya ........................................</li><li>Nyonya ........................................</li></ol><p>Demikian akta ini dibuat sebagai alat bukti sah pendirian Perseroan Terbatas.</p>
HTML,
                'created_at'  => Carbon::parse('2025-09-22 22:47:56'),
                'updated_at'  => Carbon::parse('2025-09-22 22:48:24'),
            ],

            // --- YANG BARU DITAMBAHKAN (belum ada di seeder lama) ---

            [
                'name'        => 'Perjanjian Kerja',
                'description' => 'Kesepakatan antara pemberi kerja dan pekerja yang mengatur hak, kewajiban, serta syarat kerja.',
                'custom_value' => <<<'HTML'
<h2 class="ql-align-center">PERJANJIAN KERJA</h2><p class="ql-align-center">Nomor : {{reference_number}}</p><p>– Pada hari ini, {{today}}</p><p>– tanggal .............................................................</p><p>– Pukul .................................................................</p><p>– Berhadapan dengan saya, {{notaris_name}}, Notaris di {{schedule_place}}, dengan dihadiri oleh para saksi yang saya, Notaris, kenal dan akan disebutkan nama-namanya pada bagian akhir akta ini:</p><p><strong>I. {{penghadap1_name}}</strong></p><p>..............................................................</p><p>..............................................................</p><p>..............................................................</p><p>selanjutnya disebut <strong>Pihak Pertama</strong> atau <strong>Pemberi Kerja</strong>.</p><p><strong>II. {{penghadap2_name}}</strong></p><p>..............................................................</p><p>..............................................................</p><p>..............................................................</p><p>selanjutnya disebut <strong>Pihak Kedua</strong> atau <strong>Pekerja</strong>.</p><p>Para penghadap menerangkan bahwa mereka dengan ini sepakat untuk membuat dan menandatangani Perjanjian Kerja dengan ketentuan-ketentuan sebagai berikut:</p><p><strong>----------------------- Pasal 1.</strong></p><p><strong>Jabatan dan Tugas</strong></p><p>Pihak Kedua bekerja pada Pihak Pertama dengan jabatan <strong>{{job_title}}</strong> dan bertanggung jawab melaksanakan tugas sesuai dengan arahan dan ketentuan perusahaan.</p><p><strong>----------------------- Pasal 2.</strong></p><p><strong>Waktu dan Tempat Kerja</strong></p><p>Pihak Kedua mulai bekerja sejak tanggal {{start_date}} dan ditempatkan di {{work_location}} dengan jam kerja sesuai kebijakan perusahaan.</p><p><strong>----------------------- Pasal 3.</strong></p><p><strong>Masa Kontrak</strong></p><p>Perjanjian kerja ini berlaku selama {{contract_duration}} terhitung sejak tanggal {{start_date}} sampai dengan tanggal {{end_date}}, dan dapat diperpanjang berdasarkan kesepakatan kedua belah pihak.</p><p><strong>----------------------- Pasal 4.</strong></p><p><strong>Gaji dan Fasilitas</strong></p><p>Pihak Pertama memberikan gaji sebesar Rp. {{salary}} ({{salary_in_words}}) per bulan kepada Pihak Kedua, dibayarkan setiap akhir bulan. Fasilitas lain dapat diberikan sesuai kebijakan perusahaan.</p><p><strong>----------------------- Pasal 5.</strong></p><p><strong>Kewajiban dan Hak</strong></p><p>Pihak Kedua berkewajiban menaati peraturan kerja, menjaga kerahasiaan data, dan melaksanakan tugas dengan penuh tanggung jawab. Pihak Pertama wajib memberikan hak-hak pekerja sesuai peraturan perundang-undangan ketenagakerjaan.</p><p><strong>----------------------- Pasal 6.</strong></p><p><strong>Pemutusan Hubungan Kerja</strong></p><p>Perjanjian kerja dapat berakhir apabila:</p><ol><li>Berakhirnya jangka waktu perjanjian kerja;</li><li>Salah satu pihak mengundurkan diri atau diberhentikan sesuai peraturan perusahaan;</li><li>Terjadi pelanggaran berat terhadap ketentuan perjanjian kerja atau peraturan yang berlaku.</li></ol><p><strong>----------------------- Pasal 7.</strong></p><p><strong>Penyelesaian Perselisihan</strong></p><p>Segala perselisihan yang timbul akibat pelaksanaan perjanjian kerja ini akan diselesaikan secara musyawarah untuk mufakat. Jika tidak tercapai, maka akan diselesaikan melalui mekanisme hukum sesuai ketentuan perundang-undangan.</p><p><strong>----------------------- Penutup</strong></p><p>Demikian perjanjian ini dibuat dalam rangkap dua, masing-masing memiliki kekuatan hukum yang sama, ditandatangani oleh kedua belah pihak di hadapan Notaris dan para saksi.</p><p><strong>Saksi-saksi:</strong></p><ol><li>Nyonya ........................................</li><li>Nyonya ........................................</li></ol><p>Setelah akta ini dibacakan oleh saya, Notaris, kepada para penghadap dan para saksi, maka segera ditandatangani oleh para penghadap, para saksi, dan saya, Notaris.</p>
HTML,
                'created_at'  => Carbon::parse('2025-09-09 17:00:00'),
                'updated_at'  => Carbon::parse('2025-10-07 19:47:29'),
            ],

            [
                'name'        => 'Perubahan Anggaran Dasar',
                'description' => 'Perubahan data perseroan (nama, tujuan, modal, pengurus) berdasarkan keputusan RUPS.',
                'custom_value' => <<<'HTML'
<h2 class="ql-align-center">AKTA PERUBAHAN ANGGARAN DASAR PERSEROAN TERBATAS</h2><p class="ql-align-center">Nomor : {{reference_number}}</p><p>– Pada hari ini, {{today}}</p><p>– tanggal .............................................................</p><p>– Pukul .................................................................</p><p>– Berhadapan dengan saya, {{notaris_name}}, Notaris di {{schedule_place}}, dengan dihadiri oleh para saksi yang saya, Notaris, kenal dan akan disebutkan nama-namanya pada bagian akhir akta ini:</p><p><strong>I. {{penghadap1_name}}</strong></p><p>..............................................................</p><p>..............................................................</p><p>..............................................................</p><p><strong>II. {{penghadap2_name}}</strong></p><p>..............................................................</p><p>..............................................................</p><p>..............................................................</p><p>Kedua penghadap tersebut bertindak sebagai para pemegang saham dan pendiri <strong>Perseroan Terbatas PT. {{company_name}}</strong>, yang didirikan berdasarkan Akta Nomor {{akta_pendirian_nomor}} tanggal {{akta_pendirian_tanggal}} dibuat di hadapan {{notaris_pendirian}}, Notaris di {{notaris_pendirian_kota}}, dan telah memperoleh pengesahan dari Menteri Hukum dan Hak Asasi Manusia Republik Indonesia berdasarkan Surat Keputusan Nomor {{sk_menkumham_nomor}} tanggal {{sk_menkumham_tanggal}}.</p><p>Para penghadap dengan ini menerangkan bahwa dalam Rapat Umum Pemegang Saham Luar Biasa yang diselenggarakan pada tanggal {{rapat_tanggal}}, para pemegang saham Perseroan telah mengambil keputusan untuk melakukan perubahan terhadap Anggaran Dasar Perseroan, dengan ketentuan sebagai berikut:</p><p><strong>----------------------- Pasal 1.</strong></p><p><strong>Perubahan Nama dan Kedudukan</strong></p><p>Nama Perseroan yang semula <strong>PT. {{old_company_name}}</strong> diubah menjadi <strong>PT. {{new_company_name}}</strong>, dan berkedudukan di <strong>{{company_city}}</strong>.</p><p><strong>----------------------- Pasal 2.</strong></p><p><strong>Perubahan Maksud dan Tujuan</strong></p><p>Maksud dan tujuan Perseroan diubah menjadi untuk menjalankan kegiatan usaha di bidang <strong>{{new_business_field}}</strong>, serta kegiatan lain yang mendukungnya sesuai ketentuan peraturan perundang-undangan.</p><p><strong>----------------------- Pasal 3.</strong></p><p><strong>Perubahan Modal</strong></p><p>Modal dasar Perseroan diubah dari sebesar Rp. {{old_modal_dasar}} ({{old_modal_dasar_terbilang}}) menjadi sebesar Rp. {{new_modal_dasar}} ({{new_modal_dasar_terbilang}}), terbagi atas {{jumlah_saham}} ({{jumlah_saham_terbilang}}) saham dengan nilai nominal Rp. {{nilai_saham}},– ({{nilai_saham_terbilang}}) per saham.</p><p><strong>----------------------- Pasal 4.</strong></p><p><strong>Perubahan Susunan Pengurus</strong></p><p>Susunan pengurus Perseroan diubah dan ditetapkan menjadi sebagai berikut:</p><ol><li>{{direktur_name}} sebagai Direktur;</li><li>{{komisaris_name}} sebagai Komisaris.</li></ol><p><strong>----------------------- Pasal 5.</strong></p><p><strong>Ketentuan Lain</strong></p><p>Hal-hal lain dalam Anggaran Dasar Perseroan yang tidak diubah dengan akta ini tetap berlaku sebagaimana semula.</p><p><strong>----------------------- Pasal 6.</strong></p><p><strong>Pengesahan</strong></p><p>Perubahan Anggaran Dasar ini akan diajukan untuk memperoleh persetujuan dari Menteri Hukum dan Hak Asasi Manusia Republik Indonesia sesuai dengan ketentuan peraturan perundang-undangan yang berlaku.</p><p><strong>----------------------- Penutup</strong></p><p>– Para penghadap telah saya, Notaris, kenal.</p><p>– Akta ini dibuat di {{schedule_place}} pada hari, tanggal, bulan, dan tahun sebagaimana tersebut di awal akta ini.</p><p>Setelah akta ini dibacakan oleh saya, Notaris, kepada para penghadap dan para saksi, maka segera ditandatangani oleh para penghadap, para saksi, dan saya, Notaris.</p><p><strong>Saksi-saksi:</strong></p><ol><li>Nyonya ........................................</li><li>Nyonya ........................................</li></ol><p>Demikian akta ini dibuat untuk digunakan sebagaimana mestinya.</p>
HTML,
                'created_at'  => Carbon::parse('2025-09-09 17:00:00'),
                'updated_at'  => Carbon::parse('2025-10-07 19:27:17'),
            ],
        ];

        // Upsert by 'name' untuk hindari duplikasi.
        foreach ($templates as $tpl) {
            DB::table('templates')->updateOrInsert(
                ['name' => $tpl['name']], // kunci unik berdasarkan nama
                [
                    // 'user_id' => 6, // aktifkan jika kamu memang ingin mengikat ke user tertentu
                    'description' => $tpl['description'],
                    'custom_value' => $tpl['custom_value'],
                    'created_at'  => $tpl['created_at'] ?? now(),
                    'updated_at'  => $tpl['updated_at'] ?? now(),
                ]
            );
        }
    }
}
