import csv
import random
import datetime

kelurahan_data = {
    "Klojen": ["Klojen", "Rampal Celaket", "Samaan", "Kiduldalem", "Sukoharjo", "Kasin", "Oro-oro Dowo", "Bareng", "Gading Kasri", "Penanggungan", "Kauman"],
    "Blimbing": ["Blimbing", "Balearjosari", "Arjosari", "Purwodadi", "Polowijen", "Pandanwangi", "Purwantoro", "Bunulrejo", "Kesatrian", "Polehan", "Jodipan"],
    "Lowokwaru": ["Tasikmadu", "Tunggulwulung", "Merjosari", "Tlogomas", "Dinoyo", "Sumbersari", "Ketawanggede", "Jatimulyo", "Tunjungsekar", "Mojolangu", "Tulusrejo", "Lowokwaru"],
    "Sukun": ["Ciptomulyo", "Gadang", "Bandungrejosari", "Sukun", "Tanjungrejo", "Pisangcandi", "Bandulan", "Karangbesuki", "Mulyorejo", "Bakalankrajan", "Kebonsari"],
    "Kedungkandang": ["Kedungkandang", "Wonokoyo", "Buring", "Kotalama", "Mergosono", "Bumiayu", "Arjowinangun", "Tlogowaru", "Lesanpuro", "Sawojajar", "Madyopuro"]
}

jalan_real = {
    "Klojen": ["Jl. Cokroaminoto", "Jl. Trunojoyo", "Jl. Patimura", "Jl. Dr. Sutomo"],
    "Rampal Celaket": ["Jl. W.R. Supratman", "Jl. Tretes", "Jl. Celaket"],
    "Samaan": ["Jl. Kaliurang", "Jl. Tawangmangu", "Jl. Samaan"],
    "Kiduldalem": ["Jl. Majapahit", "Jl. Gatot Subroto", "Jl. Aris Munandar"],
    "Sukoharjo": ["Jl. Laksamana Martadinata", "Jl. Kopral Usman", "Jl. Halmahera"],
    "Kasin": ["Jl. Arif Margono", "Jl. Yulius Usman", "Jl. Kasin"],
    "Oro-oro Dowo": ["Jl. Guntur", "Jl. Muria", "Jl. BS Riadi", "Jl. Brigjen Slamet Riadi"],
    "Bareng": ["Jl. Bareng Kartini", "Jl. Bareng Tengah", "Jl. Terusan Bareng"],
    "Gading Kasri": ["Jl. Galunggung", "Jl. Gading Pesantren", "Jl. Pahlawan Trip"],
    "Penanggungan": ["Jl. Mayjen Panjaitan", "Jl. Bogor", "Jl. Jakarta"],
    "Kauman": ["Jl. Kauman", "Jl. KH Hasyim Ashari", "Jl. Kawi"],
    "Blimbing": ["Jl. L.A. Sucipto", "Jl. Borobudur", "Jl. Tenaga"],
    "Balearjosari": ["Jl. Pahlawan", "Jl. Karanglo Indah"],
    "Arjosari": ["Jl. Raden Intan", "Jl. Teluk Etna", "Jl. Teluk Peleng"],
    "Purwodadi": ["Jl. Purwodadi", "Jl. Ahmad Yani"],
    "Polowijen": ["Jl. Polowijen", "Jl. A. Yani Utara"],
    "Pandanwangi": ["Jl. Simpang L.A. Sucipto", "Jl. Laksda Adi Sucipto"],
    "Purwantoro": ["Jl. Sulfat", "Jl. Bengawan Solo", "Jl. Sanan"],
    "Bunulrejo": ["Jl. Hamid Rusdi", "Jl. Warinoi", "Jl. Sebuku"],
    "Kesatrian": ["Jl. Kesatrian", "Jl. Hamid Rusdi Timur"],
    "Polehan": ["Jl. Puntodewo", "Jl. Sadewo", "Jl. Nakula"],
    "Jodipan": ["Jl. Muharto", "Jl. Jodipan Wetan"],
    "Tasikmadu": ["Jl. Tasikmadu", "Jl. Golf"],
    "Tunggulwulung": ["Jl. Akordion", "Jl. Saxophone", "Jl. Candi Mendut"],
    "Merjosari": ["Jl. Joyo Agung", "Jl. Joyo Tambaksari", "Jl. Mertojoyo"],
    "Tlogomas": ["Jl. Raya Tlogomas", "Jl. Baiduri Pandan", "Jl. Koral"],
    "Dinoyo": ["Jl. MT Haryono", "Jl. Tata Surya", "Jl. Gajayana"],
    "Sumbersari": ["Jl. Sumbersari", "Jl. Bendungan Sutami", "Jl. Sigura-gura"],
    "Ketawanggede": ["Jl. Kertosono", "Jl. Watu Gong", "Jl. Gajayana"],
    "Jatimulyo": ["Jl. Pisang Kipas", "Jl. Bunga Coklat", "Jl. Kalpataru"],
    "Tunjungsekar": ["Jl. Ikan Tombro", "Jl. Ikan Piranha", "Jl. Ikan Kakap"],
    "Mojolangu": ["Jl. Sudimoro", "Jl. Candi Panggung", "Jl. Puncak Borobudur"],
    "Tulusrejo": ["Jl. Kendalsari", "Jl. Cengger Ayam", "Jl. Bunga Cengkeh"],
    "Lowokwaru": ["Jl. Tlogo Indah", "Jl. Sarangan", "Jl. Tretes"],
    "Ciptomulyo": ["Jl. Kolonel Sugiono", "Jl. Ciptomulyo"],
    "Gadang": ["Jl. Raya Gadang", "Jl. Gadang Gang"],
    "Bandungrejosari": ["Jl. S. Supriadi", "Jl. Kemirahan"],
    "Sukun": ["Jl. S. Supriadi", "Jl. Rajawali", "Jl. Janti"],
    "Tanjungrejo": ["Jl. Tanjung", "Jl. Ir. Rais", "Jl. Gempol"],
    "Pisangcandi": ["Jl. Raya Candi", "Jl. Pisang Candi"],
    "Bandulan": ["Jl. Bandulan", "Jl. Raya Bandulan"],
    "Karangbesuki": ["Jl. Candi Badut", "Jl. Puncak Mandala", "Jl. Tidar"],
    "Mulyorejo": ["Jl. Raya Mulyorejo", "Jl. Budi Utomo", "Jl. Brawijaya"],
    "Bakalankrajan": ["Jl. Bakalan Krajan", "Jl. Pelabuhan"],
    "Kebonsari": ["Jl. Kebonsari", "Jl. S. Supriadi", "Jl. Parseh"],
    "Kedungkandang": ["Jl. Ki Ageng Gribig", "Jl. Mayjen Sungkono"],
    "Wonokoyo": ["Jl. Wonokoyo", "Jl. Kalisari"],
    "Buring": ["Jl. Buring", "Jl. Citra Garden", "Jl. Mayjen Sungkono"],
    "Kotalama": ["Jl. Kebalen Wetan", "Jl. Kolonel Sugiono", "Jl. Zaenal Zakse"],
    "Mergosono": ["Jl. Mergosono", "Jl. Kolonel Sugiono"],
    "Bumiayu": ["Jl. Bumiayu", "Jl. Kyai Parseh Jaya"],
    "Arjowinangun": ["Jl. Raya Arjowinangun", "Jl. Parseh Jaya"],
    "Tlogowaru": ["Jl. Raya Tlogowaru", "Jl. Sekar Putih"],
    "Lesanpuro": ["Jl. Danau Jonge", "Jl. Lesanpuro", "Jl. Ki Ageng Gribig"],
    "Sawojajar": ["Jl. Danau Toba", "Jl. Danau Bratan", "Jl. Danau Sentani", "Jl. Danau Maninjau"],
    "Madyopuro": ["Jl. Danau Ranau", "Jl. Madyopuro", "Jl. Ki Ageng Gribig"]
}

def get_random(array):
    return random.choice(array)

def generate_profile(id, is_layak):
    # NIK dan KK
    nik_prefix = "333333" if is_layak else "444444"
    nik = f"{nik_prefix}3333{str(id).zfill(6)}"
    
    kk_prefix = "333333" if is_layak else "444444"
    kk = f"{kk_prefix}3333{str(id + 100).zfill(6)}"
    
    tahun = random.randint(1960 if is_layak else 1975, 1990 if is_layak else 1995)
    bulan = str(random.randint(1, 12)).zfill(2)
    hari = str(random.randint(1, 28)).zfill(2)
    ttl = f"{tahun}-{bulan}-{hari}"
    
    jk = get_random(['Laki-laki', 'Perempuan'])
    kecamatan = get_random(list(kelurahan_data.keys()))
    kelurahan = get_random(kelurahan_data[kecamatan])
    
    if is_layak:
        nama = f"Data Warga Layak {id}"
        status_kawin = get_random(['Kawin', 'Cerai Hidup', 'Cerai Mati'])
        
        rt = str(random.randint(1, 15)).zfill(2)
        rw = str(random.randint(1, 10)).zfill(2)
        
        base_jalan = get_random(jalan_real[kelurahan])
        if random.choice([True, False]):
            alamat = f"{base_jalan} Gg. {random.randint(1, 10)} RT {rt}/RW {rw}"
        else:
            alamat = f"{base_jalan} Dalam RT {rt}/RW {rw}"
        
        pekerjaan = get_random(['Tidak Bekerja', 'Petani / Buruh Tani', 'Buruh Pabrik / Bangunan'])
        if pekerjaan == 'Tidak Bekerja':
            penghasilan = '< Rp 500.000'
            pengeluaran = get_random(['< Rp 500.000', 'Rp 500.000 - Rp 1.000.000'])
        else:
            penghasilan = get_random(['< Rp 500.000', 'Rp 500.000 - Rp 1.000.000'])
            pengeluaran = penghasilan
            
        tanggungan = get_random(['3 - 4 Orang', '> 4 Orang'])
        kondisi_lantai = get_random(['Tanah / Kayu Kualitas Rendah', 'Semen Kasar / Papan Kayu Biasa'])
        kondisi_dinding = get_random(['Bilik Bambu / Rumbia / Terpal', 'Kayu Murah / Setengah Tembok (Bata tanpa plester)'])
        kondisi_atap = get_random(['Ijuk / Rumbia / Daun', 'Seng Karatan / Asbes Tua'])
        status_rumah = get_random(['Numpang / Bebas Sewa (Fasum / Lahan orang lain)', 'Sewa / Kontrak (Membayar bulanan/tahunan)'])
        pendidikan = get_random(['Tidak Sekolah', 'Tamat SD', 'Tamat SMP'])
        anak_sekolah = get_random(['1 - 2 Anak Sekolah', 'Lebih dari 2 Anak Sekolah'])
        aset = get_random(['Tidak Ada Aset Sama Sekali', 'Hanya Aset Kecil (Sepeda / HP Murah / Unggas)'])
        listrik = get_random(['Numpang / Tidak Ada Listrik', 'Listrik 450 VA'])
        air = get_random(['Mata Air Tidak Terlindung / Sungai / Air Hujan', 'Sumur Gali Terlindung / Pompa Tangan'])
        kesehatan = get_random(['Disabilitas Berat / Penyakit Menahun (Stroke/TBC)', 'Rentan Sakit / Lansia / Ibu Hamil', 'Sehat Fisik & Jasmani'])
        status_bantuan = 'Layak'
        
    else:
        nama = f"Data Warga Tidak Layak {id}"
        status_kawin = get_random(['Kawin', 'Belum Kawin'])
        
        rt = str(random.randint(1, 10)).zfill(2)
        rw = str(random.randint(1, 10)).zfill(2)
        
        base_jalan = get_random(jalan_real[kelurahan])
        if random.choice([True, False]):
            nama_perum = base_jalan.replace("Jl. ", "").replace("Raya ", "")
            suffix = get_random(["Indah", "Asri", "Permai", "Megah", "Residences", "Mas"])
            alamat = f"Perum. {nama_perum} {suffix} Blok {chr(random.randint(65, 75))}{random.randint(1, 20)} RT {rt}/RW {rw}"
        else:
            alamat = f"{base_jalan} No. {random.randint(1, 150)} RT {rt}/RW {rw}"
        
        pekerjaan = get_random(['Wiraswasta / Pedagang', 'Karyawan Swasta', 'PNS / TNI / POLRI'])
        penghasilan = get_random(['Rp 2.000.000 - Rp 3.000.000', 'Rp 3.000.000 - Rp 4.000.000', '> Rp 4.000.000'])
        
        if penghasilan == '> Rp 4.000.000':
            pengeluaran = get_random(['Rp 3.000.000 - Rp 4.000.000', '> Rp 4.000.000'])
        else:
            pengeluaran = penghasilan
            
        tanggungan = get_random(['0 - 2 Orang', '3 - 4 Orang'])
        kondisi_lantai = get_random(['Semen Kasar / Papan Kayu Biasa', 'Keramik / Marmer / Granit'])
        kondisi_dinding = get_random(['Tembok Penuh (Diplester & Dicat)'])
        kondisi_atap = get_random(['Genteng Tanah Liat / Baja Ringan / Genteng Keramik'])
        status_rumah = get_random(['Milik Sendiri / Warisan'])
        pendidikan = get_random(['Tamat SMA / SMK', 'Sarjana / Perguruan Tinggi'])
        anak_sekolah = get_random(['Tidak Ada Anak Sekolah', '1 - 2 Anak Sekolah'])
        aset = get_random(['Aset Menengah Atas (Motor Baru / Ternak Sapi)', 'Aset Mewah (Mobil / Lahan Kosong / Emas Murni)'])
        listrik = get_random(['Listrik 900 VA', 'Listrik 1300 VA', 'Listrik > 1300 VA'])
        air = get_random(['PAM / Leding Meteran / Air Kemasan Bermerk'])
        kesehatan = get_random(['Sehat Fisik & Jasmani'])
        status_bantuan = 'Tidak Layak'
        
    return [nik, kk, nama, ttl, jk, status_kawin, alamat, kecamatan, kelurahan, pekerjaan, penghasilan, tanggungan, 
            kondisi_lantai, kondisi_dinding, kondisi_atap, status_rumah, pendidikan, anak_sekolah, 
            aset, pengeluaran, listrik, air, kesehatan, status_bantuan]

# Generate 3000 data (1500 Layak, 1500 Tidak Layak)
headers = [
    "nik", "no_kk", "nama", "tempat_tanggal_lahir", "jenis_kelamin", "status_perkawinan", "alamat", "kecamatan", "kelurahan",
    "pekerjaan", "penghasilan", "jumlah_tanggungan", "kondisi_lantai", "kondisi_dinding", "kondisi_atap", 
    "status_kepemilikan_rumah", "pendidikan_terakhir", "jumlah_anak_sekolah", "kepemilikan_aset", 
    "pengeluaran_bulanan", "akses_listrik", "akses_air", "kondisi_kesehatan", "status_bantuan"
]

data = []
for i in range(1, 402):
    data.append(generate_profile(i, True))
for i in range(1, 8940):
    data.append(generate_profile(i + 401, False))

# Simpan ke CSV
with open("data_training_1000.csv", mode='w', newline='', encoding='utf-8') as file:
    writer = csv.writer(file, delimiter=',', quotechar='"', quoting=csv.QUOTE_MINIMAL)
    writer.writerow(headers)
    writer.writerows(data)

print("Berhasil membuat data_training_1000.csv dengan 9340 baris data (401 Layak, 8939 Tidak Layak).")
