# Aplikasi Sistem Pendukung Keputusan (SPK) Bansos dengan Docker

Aplikasi ini adalah Sistem Pendukung Keputusan (SPK) untuk menentukan kelayakan penerima bantuan sosial (Bansos) menggunakan algoritma **Multinomial Naive Bayes**. Aplikasi ini dikembangkan dengan kombinasi PHP (untuk antarmuka web) dan Python FastAPI (untuk memproses model Machine Learning), serta MySQL (sebagai penyimpanan database).

Proyek ini telah dikonfigurasi sepenuhnya menggunakan **Docker** dan **Docker Compose** agar dapat berjalan secara instan tanpa perlu melakukan instalasi web server lokal (seperti XAMPP) secara manual.

---

## Arsitektur Container

Aplikasi berjalan di atas 3 container utama:
1. **`bansos-web` (PHP 8.2 + Apache)**: Menyediakan panel pengguna dan admin. Berjalan di port `8080`.
2. **`bansos-api` (FastAPI Python 3.11)**: Menyediakan API prediksi kelayakan menggunakan model *machine learning* (.pkl). Berjalan di port `5000`.
3. **`bansos-db` (MySQL 8.0)**: Menyimpan data warga dan admin. Skema database diinisialisasi otomatis menggunakan berkas `db_bansos.sql`. Berjalan di port `3306`.

---

## Prasyarat

Pastikan Anda sudah menginstal aplikasi berikut pada sistem operasi Anda:
* [Docker Desktop](https://www.docker.com/products/docker-desktop/) (Windows/macOS) atau Docker Engine (Linux).
* Docker Compose (sudah termasuk dalam Docker Desktop).

---

## Cara Menjalankan Aplikasi

Ikuti langkah-langkah mudah berikut untuk menjalankan aplikasi:

### 1. Bangun dan Jalankan Container
Buka terminal (PowerShell, Command Prompt, atau Terminal Linux) di direktori root project, lalu jalankan perintah berikut:
```bash
docker compose up --build -d
```
*Opsi `-d` digunakan agar container berjalan di latar belakang (detached mode).*

Perintah ini akan secara otomatis:
* Mengunduh base image yang dibutuhkan.
* Menginstal ekstensi PHP yang diperlukan (`pdo`, `pdo_mysql`, `curl`).
* Menginstal library Python (`FastAPI`, `scikit-learn`, `uvicorn`, dsb).
* Membuat database `db_bansos` dan memuat skema awal dari berkas `db_bansos.sql`.

### 2. Akses Aplikasi di Browser
Setelah proses pembuatan container selesai, Anda dapat mengakses:
* **Halaman Web Utama & Dashboard Admin**: [http://localhost:8080](http://localhost:8080)
* **Dokumentasi API FastAPI**: [http://localhost:5000/docs](http://localhost:5000/docs)

### 3. Informasi Kredensial Login
Untuk masuk ke panel admin:
* **URL Login**: [http://localhost:8080/admin/login.php](http://localhost:8080/admin/login.php)
* **Username**: `admin`
* **Password**: `admin123`

---

## Penggunaan Fitur Prediksi

1. Masuk ke halaman **Admin** -> **Data Warga**.
2. Klik tombol **Proses Prediksi** pada salah satu data warga untuk memprediksi kelayakan warga secara individu. Web PHP akan melakukan *request* ke API FastAPI (`bansos-api`) untuk memproses klasifikasi.
3. Anda juga dapat menggunakan fitur **Proses Semua Warga** untuk melakukan prediksi massal terhadap seluruh data warga dengan status "Proses".

---

## Perintah Penting Docker Lainnya

* **Melihat status container yang berjalan**:
  ```bash
  docker compose ps
  ```
* **Melihat log aplikasi (untuk debugging)**:
  ```bash
  docker compose logs -f
  ```
* **Menghentikan aplikasi**:
  ```bash
  docker compose down
  ```
* **Menghentikan dan menghapus semua data database (volume reset)**:
  ```bash
  docker compose down -v
  ```

---

## Konfigurasi Lanjutan (Environment Variables)

Jika Anda ingin menyesuaikan konfigurasi di luar default, Anda dapat memodifikasi *environment variables* yang terdapat pada berkas `docker-compose.yml`:
* `DB_HOST`: Host database (secara default diarahkan ke nama service `db`).
* `DB_USER` & `DB_PASSWORD`: Kredensial koneksi database MySQL.
* `API_HOST`: Host API Python (secara default diarahkan ke nama service `api`).
* `API_PORT`: Port API Python (default `5000`).
