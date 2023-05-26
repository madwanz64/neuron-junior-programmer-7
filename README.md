# Training Junior Programmer 7

1. Pelajari Modul PHP #3 Part1, Part2, Part3
2. Buat Aplikasi menggunakan framework Laravel dengan ketentuan:
   - Menampilkan data dari database berupa tabel
   - Menerapkan CRUD (Create, Read, Update, Delete)
   - Terdapat fungsi pencarian data
   - Terdapat validasi input (minimal karakter, validasi angka)
   - Lolos scan dengan rule dari https://rules.sonarsource.com/php
3. Project yang telah dibuat, diupload ke github masing-masing beserta file .sql datanya
4. Assign link github ke lokasi yang sudah disediakan
5. Submit!

## Catatan

Cara menjalankan project di local

1. Clone repository terlebih dahulu
   ```
   git clone https://github.com/madwanz64/neuron-junior-programmer-7
   ```
2. Masuk ke root folder project dan install dependency
   ```
   composer install
   ```
3. Duplikat dari file env.example untuk membuat file .env baru
4. Sesuaikan konfigurasi di file .env dengan pengaturan di local
5. Buat application key baru
   ```
   php artisan key:generate
   ```
6. Jalankan script migrasi dan seed 
   ```
   php artisan migrate --seed
   ```
7. Jalankan project laravel
   ```
   php artisan serve
   ```

Akun yang dapat digunakan untuk login : 
```
email    : text@example.com 
password : password 
```
