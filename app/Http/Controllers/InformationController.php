<?php

namespace App\Http\Controllers;

class InformationController extends Controller
{
    public function index()
    {
        $topics = collect(self::topics());

        return view('information', compact('topics'));
    }

    public function show(string $slug)
    {
        $topics = collect(self::topics());

        if (!$topics->has($slug)) {
            abort(404);
        }

        $topic = $topics->get($slug);

        return view('information_show', compact('topic', 'topics'));
    }

    private static function topics(): array
    {
        return [
            'how-to-order' => [
                'icon' => 'shopping_bag',
                'title' => 'How to Order',
                'note' => 'Panduan belanja',
                'description' => 'Langkah mudah memesan produk WHATEVERLOVE langsung dari website: pilih produk, checkout, bayar online, sampai pesanan tiba.',
                'sections' => [
                    ['heading' => '1. Pilih Produk & Tambah ke Keranjang', 'body' => 'Jelajahi katalog kami melalui menu CATALOG atau lewat halaman Lookbook dan Article. Di halaman produk, pilih ukuran dan jumlah, lalu tekan tombol tambah ke keranjang (icon tas). Daftar belanjaanmu bisa dicek lewat ikon tas di navbar atau halaman Cart.'],
                    ['heading' => '2. Checkout & Isi Alamat', 'body' => 'Tekan tombol Checkout. Pastikan kamu sudah login dengan akun terverifikasi. Isi nama, nomor HP/WhatsApp, dan alamat lengkap, kemudian pilih provinsi, kota, dan kecamatan. Ongkos kirim dihitung otomatis oleh sistem.'],
                    ['heading' => '3. Pilih Pengiriman JNE', 'body' => 'Setelah kecamatan dipilih, muncul pilihan layanan JNE (misalnya REG, YES, JTR) lengkap dengan biaya dan estimasi perjalanan (ETD). Estimasi dihitung sejak paket dikirim dari Bogor, dan sudah termasuk hari cadangan supaya paket cenderung datang lebih cepat dari perkiraan.'],
                    ['heading' => '4. Bayar Online', 'body' => 'Kamu akan diarahkan ke halaman pembayaran aman Midtrans untuk menyelesaikan pembayaran, misalnya dengan kartu debit/kredit. Begitu pembayaran tercatat, status otomatis berubah menjadi "Pembayaran Dikonfirmasi".'],
                    ['heading' => '5. Pesanan Dikemas & Dikirim', 'body' => 'Tim kami mengemas pesananmu dengan hati-hati (umumnya 1-3 hari kerja). Nomor resi JNE akan dimasukkan oleh admin dan langsung tampil di halaman pesananmu.'],
                    ['heading' => '6. Lacak Sampai Tiba', 'body' => 'Pantau status pesanan kapan saja lewat menu PESANAN di navbar. Ada kendala? Hubungi kami lewat halaman Contact Us dan tim kami siap membantu.'],
                ],
            ],
            'faq' => [
                'icon' => 'help',
                'title' => 'FAQ',
                'note' => 'Pertanyaan umum',
                'description' => 'Jawaban untuk pertanyaan yang paling sering ditanyakan seputar produk, pembayaran, dan pengiriman.',
                'sections' => [
                    ['heading' => 'Apakah produk WHATEVERLOVE original?', 'body' => 'Ya, semua produk WHATEVERLOVE adalah karya original dari tim kami. Kami mengelola seluruh proses mulai dari desain, pemilihan material, hingga produksi untuk memastikan kualitas terbaik.'],
                    ['heading' => 'Bagaimana cara mengetahui ukuran yang pas?', 'body' => 'Setiap halaman produk menampilkan keterangan ukuran dan panduan fit. Jika ragu, silakan hubungi kami melalui halaman Contact Us dan tim kami akan membantu kamu memilih ukuran yang tepat.'],
                    ['heading' => 'Apa saja metode pembayaran yang tersedia?', 'body' => 'Pembayaran dilakukan dengan aman melalui halaman pembayaran Midtrans langsung di website. Saat ini tersedia pembayaran kartu kredit/debit; metode lain (QRIS, transfer bank, e-wallet) akan menyusul. Atau hubungi kami melalui halaman Contact Us untuk cara bayar lainnya.'],
                    ['heading' => 'Berapa lama proses pengiriman?', 'body' => 'Pesanan diproses dalam 1-3 hari kerja. Pengiriman domestik biasanya memakan waktu 2-7 hari kerja tergantung lokasi, sedangkan pengiriman internasional bervariasi tergantung negara tujuan.'],
                    ['heading' => 'Apakah bisa memesan secara custom?', 'body' => 'Untuk saat ini kami tidak menerima pesanan custom. Namun, dengarkan terus drop terbaru kami karena kami rutin meluncurkan koleksi baru yang unik.'],
                ],
            ],
            'refund-return' => [
                'icon' => 'assignment_return',
                'title' => 'Refund & Return',
                'note' => 'Kebijakan retur',
                'description' => 'Informasi lengkap tentang kebijakan pengembalian barang dan dana di WHATEVERLOVE.',
                'sections' => [
                    ['heading' => 'Jangka Waktu Retur', 'body' => 'Kamu dapat mengajukan retur dalam 7 hari kalender setelah barang diterima. Pastikan barang belum dipakai, tidak rusak, dan semua tag (label) masih terpasang.'],
                    ['heading' => 'Kondisi Produk', 'body' => 'Produk yang diajukan retur harus dalam kondisi asli: belum dipakai, belum dicuci, tidak berbau, dan tidak ada tanda pemakaian. Produk dengan embel-embel khusus (final sale) tidak dapat diretur kecuali ada cacat produksi.'],
                    ['heading' => 'Cara Mengajukan Retur', 'body' => 'Hubungi kami melalui halaman Contact Us dengan menyertakan nomor pesanan, foto produk, dan alasan retur. Tim kami akan memberikan instruksi pengembalian dan alamat tujuan. Biaya pengiriman retur ditanggung pembeli kecuali ada kesalahan dari pihak kami.'],
                    ['heading' => 'Refund', 'body' => 'Setelah produk diterima dan diperiksa, refund akan diproses dalam 3-7 hari kerja. Dana akan dikembalikan ke metode pembayaran awal kamu. Khusus COD, refund dilakukan melalui transfer bank atas nama kamu.'],
                ],
            ],
        ];
    }
}