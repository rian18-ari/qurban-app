# 🕌 Qurban App Documentation

Sistem Manajemen Qurban berbasis Modular (Laravel 11, Inertia, Reverb, WhatsApp Queue).

## 🚀 Persiapan Awal
1. **Jalankan App:** `./vendor/bin/sail up -d`
2. **Setup Database:** `./vendor/bin/sail artisan migrate:fresh --seed`
3. **Login Dummy:**
   - Email: `admin@qurban.test`
   - Pass: `password`

## 🛠️ Cara Menjalankan Mesin (PENTING!)
Agar fitur real-time dan WhatsApp berjalan, jalankan command ini di terminal terpisah:

1. **Real-time (Reverb):**
   ```bash
   ./vendor/bin/sail artisan reverb:start
   ```
2. **WhatsApp & Background Job (Queue):**
   ```bash
   ./vendor/bin/sail artisan queue:work
   ```

---

## 📱 Integrasi WhatsApp (Fonnte)
Aplikasi ini menggunakan Fonnte sebagai Gateway. Untuk mengaktifkannya:
1. Daftar di [fonnte.com](https://fonnte.com).
2. Hubungkan nomor WA Masjid di dashboard Fonnte.
3. Ambil **API Token** kamu.
4. Buka file `.env` di project ini, lalu update:
   ```env
   WHATSAPP_TOKEN=isi_token_fonnte_disini
   ```
*Catatan: Sistem sudah dilengkapi Smart Delay (2-5 detik) agar nomor tidak mudah di-banned.*

---

## 📋 Alur Operasional (Step-by-Step)

### 1. Konfigurasi (Admin)
- Masuk menu **Pengaturan**.
- Pastikan ada periode aktif dan tentukan biaya patungan sapi/kambing.

### 2. Pendaftaran (Bendahara)
- Masuk menu **Mudhohi**.
- Daftar peserta satu per satu. Sistem akan otomatis membagi ke kelompok "Sapi 01", "Sapi 02", dst jika slot 7 orang terpenuhi.

### 3. Data Warga (Panitia)
- Masuk menu **Mustahiq**.
- Buat **Sesi Antrean** (misal: Sesi 1 Jam 08.00).
- Klik **Import Excel**, upload data warga. Sistem otomatis membagi warga ke sesi yang kuotanya masih tersedia.

### 4. Eksekusi Hari-H (Jagal)
- Buka menu **Jagal** di tablet/HP.
- Klik **Sembelih** saat hewan dieksekusi.
- **Efek:** Mudhohi otomatis terima WA, dan dashboard Panitia lain terupdate real-time.

### 5. Distribusi Daging (Gatekeeper)
- Buka menu **Gatekeeper**.
- Scan QR Code (atau input Kode Kupon) warga. 
- Sistem akan menolak jika: Sesi salah, Kode tidak ada, atau Daging sudah pernah diambil.
- **Catatan:** Kode Kupon otomatis di-generate saat import jika kolom NIK di Excel kosong.

### 6. Laporan (Ketua Panitia)
- Masuk menu **Keuangan**.
- Catat pemasukan/pengeluaran lainnya.
- Klik **Export LPJ (Excel)** untuk mendapatkan dokumen lengkap laporan pertanggungjawaban.
