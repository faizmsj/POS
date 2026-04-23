<?php

namespace App\Http\Controllers;

class HelpController extends Controller
{
    public function faq()
    {
        return view('help.faq', [
            'faqGroups' => [
                [
                    'title' => 'Akses & Login',
                    'items' => [
                        ['q' => 'Mengapa saya tidak bisa membuka menu tertentu?', 'a' => 'Setiap akun memiliki role dan hak akses berbeda. Kasir hanya melihat menu operasional, sedangkan admin dan owner memiliki akses pengaturan dan master data.'],
                        ['q' => 'Apa yang harus dilakukan jika lupa password?', 'a' => 'Hubungi administrator untuk reset password melalui menu Akses Pengguna.'],
                    ],
                ],
                [
                    'title' => 'Transaksi Kasir',
                    'items' => [
                        ['q' => 'Mengapa transaksi gagal diproses?', 'a' => 'Biasanya karena stok di cabang tidak mencukupi, cabang tidak memiliki akses, atau data produk pada keranjang sudah berubah. Refresh halaman POS lalu coba lagi.'],
                        ['q' => 'Mengapa produk tidak muncul di POS?', 'a' => 'Produk hanya tampil jika aktif, punya relasi ke cabang yang sedang dipilih, dan stoknya lebih dari 0.'],
                        ['q' => 'Bagaimana mencetak ulang nota?', 'a' => 'Buka menu Transaksi, lalu klik Cetak Nota pada transaksi yang diinginkan.'],
                    ],
                ],
                [
                    'title' => 'Stok & Inventaris',
                    'items' => [
                        ['q' => 'Mengapa stok cabang berbeda-beda?', 'a' => 'Sistem ini memakai stok per cabang. Pembelian, penjualan, dan penyesuaian memengaruhi cabang masing-masing.'],
                        ['q' => 'Bagaimana HPP dihitung?', 'a' => 'Sistem menggunakan pendekatan FIFO berdasarkan batch pembelian yang tercatat.'],
                    ],
                ],
                [
                    'title' => 'PPOB & Loyalty',
                    'items' => [
                        ['q' => 'Siapa yang bisa mengatur provider dan produk PPOB?', 'a' => 'Hanya owner dan admin yang bisa membuka pengaturan provider dan katalog PPOB.'],
                        ['q' => 'Bagaimana poin loyalty bertambah?', 'a' => 'Poin diberikan otomatis saat transaksi berhasil untuk pelanggan yang dipilih pada penjualan.'],
                    ],
                ],
            ],
        ]);
    }

    public function sop()
    {
        return view('help.sop', [
            'sections' => [
                [
                    'title' => 'SOP Kasir Harian',
                    'steps' => [
                        'Login menggunakan akun kasir yang sudah diberikan.',
                        'Buka menu Shift Kasir lalu buka shift baru dengan saldo awal yang benar.',
                        'Masuk ke POS Kasir, pilih cabang yang sesuai, lalu tambah produk ke keranjang.',
                        'Periksa pelanggan, diskon, pajak, dan jumlah bayar sebelum klik Bayar.',
                        'Cetak nota jika dibutuhkan pelanggan.',
                        'Saat operasional selesai, kembali ke menu Shift Kasir dan tutup shift dengan saldo akhir aktual.',
                    ],
                ],
                [
                    'title' => 'SOP Administrator',
                    'steps' => [
                        'Periksa dashboard dan filter periode untuk memantau performa harian atau bulanan.',
                        'Kelola user, role, dan penempatan cabang melalui menu Akses Pengguna.',
                        'Pastikan master data cabang, produk, kategori, supplier, dan pembelian selalu mutakhir.',
                        'Tinjau stok menipis dan lakukan pembelian atau transfer stok sesuai kebutuhan.',
                        'Atur printer, loyalty, PPOB, dan preferensi toko melalui menu Pengaturan.',
                        'Lakukan review transaksi dan laporan secara rutin untuk memastikan data operasional konsisten.',
                    ],
                ],
                [
                    'title' => 'Penanganan Masalah Cepat',
                    'steps' => [
                        'Jika menu tidak muncul, cek kembali role akun yang sedang login.',
                        'Jika transaksi gagal, periksa stok cabang dan pastikan produk masih aktif.',
                        'Jika label atau nota belum sesuai, cek menu Pengaturan pada bagian printer.',
                        'Jika akun tidak bisa mengakses cabang tertentu, minta admin memperbarui relasi cabang user.',
                    ],
                ],
            ],
        ]);
    }
}
