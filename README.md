# 🏢 Sistem Pendukung Keputusan (SPK) Penerima Bantuan Sosial (Bansos) Menggunakan Metode Naive Bayes

Sistem Pendukung Keputusan berbasis web untuk menentukan kelayakan penerima Bantuan Sosial (Bansos) secara obyektif dan akurat menggunakan algoritma Machine Learning **Multinomial Naive Bayes**.

---

## 📌 Fitur Utama

- 🔍 **Cek Status Bansos Publik**: Masyarakat dapat mengecek status penerimaan bansos secara mandiri berdasarkan NIK atau Nama.
- 📊 **Dashboard Administrator**: Statistik data warga, proporsi kelayakan (Layak vs Tidak Layak), dan data per kecamatan.
- 📋 **Manajemen Data Warga**: Pengelolaan data calon penerima bansos (CRUD), lengkap dengan fitur **Import Data Massal via CSV**.
- 🤖 **Prediksi Akurat Naive Bayes**:
  - **Prediksi Individu**: Menghasilkan status kelayakan beserta kalkulasi probabilitas dan 3 faktor pertimbangan utama.
  - **Prediksi Massal (Bulk Processing)**: Memproses seluruh data warga berstatus *Proses* sekaligus secara otomatis.
- 🗺️ **Pengelompokan Wilayah**: Pemetaan dan filter data warga berdasarkan Kecamatan.

---

## 🛠️ Teknologi & Stack

- **Frontend & Backend Web**: PHP (Native), HTML5, CSS3, JavaScript, Bootstrap 5
- **Database**: MySQL / MariaDB
- **Machine Learning API**: Python 3, FastAPI, Uvicorn, PyMySQL
- **Library Machine Learning**: Scikit-Learn (MultinomialNB), Pandas, NumPy, Joblib

---

## 📁 Struktur Direktori

```text
bansos-app/
├── admin/                     # Halaman Panel Admin
│   ├── form_warga.php         # Form Tambah/Edit Data Warga
│   ├── import_warga.php       # Fitur Upload & Import CSV Data Warga
│   ├── index.php              # Dashboard Admin & Ringkasan Statistik
│   ├── login.php              # Halaman Login Admin
│   ├── logout.php             # Logout Session Admin
│   ├── prediksi.php           # Halaman Eksekusi Prediksi Machine Learning
│   ├── warga.php              # Daftar & Filter Data Warga
│   └── warga_kecamatan.php    # Data Warga Per Kecamatan
├── api/                       # Service Machine Learning (FastAPI)
│   ├── app.py                 # Endpoint API FastAPI (Predict & Bulk Predict)
│   ├── requirements.txt       # Dependensi Python
│   └── model/                 # Model Machine Learning (.pkl)
│       └── bansos_multinomial_nb_model.pkl
├── assets/                    # Aset Statis (CSS, Images, Logos)
├── config/                    # Konfigurasi Koneksi Database
│   └── database.php
├── includes/                  # Komponen Header & Footer Web
├── generate_csv.py            # Script Generator Dataset Pelatihan
├── train_model.py             # Script Pelatihan Model Naive Bayes
├── db_bansos.sql              # Schema Database & Data Awal
├── index.php                  # Halaman Utama Publik
└── .gitignore                 # Daftar Berkas Abaikan Git
```

---

## 🚀 Panduan Instalasi & Penggunaan

Anda dapat menjalankan aplikasi ini menggunakan **Docker (Direkomendasikan)** atau secara **Manual menggunakan XAMPP**.

### 🐳 Opsi A: Menggunakan Docker (Direkomendasikan)

Dengan Docker, semua service (Web, API, dan Database) berjalan dalam container yang saling terhubung secara otomatis.

#### 1. Prasyarat Sistem
* [Docker Desktop](https://www.docker.com/products/docker-desktop/) (Windows/macOS) atau Docker Engine (Linux).
* Docker Compose.

#### 2. Cara Menjalankan
1. Buka terminal di direktori root project, lalu jalankan perintah berikut:
   ```bash
   docker compose up --build -d
   ```
2. Buka browser dan kunjungi:
   - **Halaman Web Utama (Publik)**: [http://localhost:8080](http://localhost:8080)
   - **Halaman Admin**: [http://localhost:8080/admin/login.php](http://localhost:8080/admin/login.php)
   - **Dokumentasi API FastAPI**: [http://localhost:5000/docs](http://localhost:5000/docs)
3. Informasi Login Admin:
   - **Username**: `admin`
   - **Password**: `admin123`

> [!NOTE]
> Port MySQL internal dipetakan ke **3307** pada sisi host untuk menghindari bentrok port jika PC Anda menjalankan MySQL lokal (XAMPP). Komunikasi internal antar container tetap menggunakan port standar 3306.

---

### 💻 Opsi B: Menggunakan XAMPP (Manual)

#### 1. Prasyarat Sistem
- **XAMPP** (Apache & MySQL/MariaDB)
- **Python 3.11+**

#### 2. Setup Database & Web Server
1. Clone repositori ini ke folder `htdocs` XAMPP Anda:
   ```bash
   git clone https://github.com/Raflioioi/TA-INFORMATIKA-221111022-BANSOS-NAIVE-BAYES.git bansos-app
   ```
2. Jalankan **Apache** dan **MySQL** di XAMPP Control Panel.
3. Buka `phpMyAdmin` (`http://localhost/phpmyadmin`) lalu buat database baru dengan nama `db_bansos`.
4. Import berkas `db_bansos.sql` ke dalam database `db_bansos`.

#### 3. Setup Microservice Machine Learning (Python FastAPI)
1. Buka terminal/command prompt, masuk ke folder `api`:
   ```bash
   cd c:\xampp\htdocs\bansos-app\api
   ```
2. *(Opsional)* Buat virtual environment:
   ```bash
   python -m venv venv
   # Windows
   .\venv\Scripts\activate
   ```
3. Install dependensi Python:
   ```bash
   pip install -r requirements.txt
   ```
4. Jalankan FastAPI server:
   ```bash
   python app.py
   ```
   API akan berjalan di `http://127.0.0.1:5000`.

#### 4. Menjalankan Halaman Utama Publik
1. Buka browser dan kunjungi:
   - **Halaman Publik (Cek Bansos)**: `http://localhost/bansos-app/`
   - **Halaman Admin**: `http://localhost/bansos-app/admin/login.php`

---

## 🛠️ Perintah Penting Docker Lainnya

* **Melihat status container**:
  ```bash
  docker compose ps
  ```
* **Melihat log aplikasi**:
  ```bash
  docker compose logs -f
  ```
* **Menghentikan aplikasi**:
  ```bash
  docker compose down
  ```
* **Mereset database (volume reset)**:
  ```bash
  docker compose down -v
  ```

---

## ⚙️ Konfigurasi Lanjutan (Environment Variables)

Jika Anda menggunakan Opsi Docker, Anda dapat menyesuaikan konfigurasi pada berkas `docker-compose.yml`:
* `DB_HOST`: Host database (secara default diarahkan ke nama service `db`).
* `DB_USER` & `DB_PASSWORD`: Kredensial koneksi database MySQL.
* `API_HOST`: Host API Python (secara default diarahkan ke nama service `api`).
* `API_PORT`: Port API Python (default `5000`).

---

## 📝 Lisensi & Kredit

Dikembangkan untuk Tugas Akhir Informatika.
- **Penulis / Pembuat**: Raflioioi
- **NIM**: 221111022
