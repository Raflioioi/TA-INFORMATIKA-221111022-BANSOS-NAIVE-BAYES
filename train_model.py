import csv
import numpy as np
import joblib
from sklearn.preprocessing import LabelEncoder
from sklearn.naive_bayes import MultinomialNB
from sklearn.metrics import accuracy_score, precision_score, recall_score, f1_score
from sklearn.model_selection import cross_val_score
import os

# Define all possible options for categorical features
all_possible_values = {
    'jenis_kelamin': ['Laki-laki', 'Perempuan'],
    'status_perkawinan': ['Belum Kawin', 'Kawin', 'Cerai Hidup', 'Cerai Mati'],
    'pekerjaan': ['Tidak Bekerja', 'Petani / Buruh Tani', 'Buruh Pabrik / Bangunan', 'Wiraswasta / Pedagang', 'Karyawan Swasta', 'PNS / TNI / POLRI'],
    'penghasilan': ['< Rp 500.000', 'Rp 500.000 - Rp 1.000.000', 'Rp 1.000.000 - Rp 2.000.000', 'Rp 2.000.000 - Rp 3.000.000', 'Rp 3.000.000 - Rp 4.000.000', '> Rp 4.000.000'],
    'jumlah_tanggungan': ['0 - 2 Orang', '3 - 4 Orang', '> 4 Orang'],
    'kondisi_lantai': ['Tanah / Kayu Kualitas Rendah', 'Semen Kasar / Papan Kayu Biasa', 'Keramik / Marmer / Granit'],
    'kondisi_dinding': ['Bilik Bambu / Rumbia / Terpal', 'Kayu Murah / Setengah Tembok (Bata tanpa plester)', 'Tembok Penuh (Diplester & Dicat)'],
    'kondisi_atap': ['Ijuk / Rumbia / Daun', 'Seng Karatan / Asbes Tua', 'Genteng Tanah Liat / Baja Ringan / Genteng Keramik'],
    'status_kepemilikan_rumah': ['Milik Sendiri / Warisan', 'Sewa / Kontrak (Membayar bulanan/tahunan)', 'Numpang / Bebas Sewa (Fasum / Lahan orang lain)'],
    'pendidikan_terakhir': ['Tidak Sekolah', 'Tamat SD', 'Tamat SMP', 'Tamat SMA / SMK', 'Sarjana / Perguruan Tinggi'],
    'jumlah_anak_sekolah': ['Tidak Ada Anak Sekolah', '1 - 2 Anak Sekolah', 'Lebih dari 2 Anak Sekolah'],
    'kepemilikan_aset': [
        'Tidak Ada Aset Sama Sekali',
        'Hanya Aset Kecil (Sepeda / HP Murah / Unggas)',
        'Aset Menengah Bawah (Motor Tua < Rp 3 Juta / Kambing)',
        'Aset Menengah Atas (Motor Baru / Ternak Sapi)',
        'Aset Mewah (Mobil / Lahan Kosong / Emas Murni)'
    ],
    'pengeluaran_bulanan': ['< Rp 500.000', 'Rp 500.000 - Rp 1.000.000', 'Rp 1.000.000 - Rp 2.000.000', 'Rp 2.000.000 - Rp 3.000.000', 'Rp 3.000.000 - Rp 4.000.000', '> Rp 4.000.000'],
    'akses_listrik': ['Numpang / Tidak Ada Listrik', 'Listrik 450 VA', 'Listrik 900 VA', 'Listrik 1300 VA', 'Listrik > 1300 VA'],
    'akses_air': ['Mata Air Tidak Terlindung / Sungai / Air Hujan', 'Sumur Gali Terlindung / Pompa Tangan', 'PAM / Leding Meteran / Air Kemasan Bermerk'],
    'kondisi_kesehatan': ['Sehat Fisik & Jasmani', 'Rentan Sakit / Lansia / Ibu Hamil', 'Disabilitas Berat / Penyakit Menahun (Stroke/TBC)']
}

feature_columns = [
    'jenis_kelamin', 'status_perkawinan', 'pekerjaan', 'penghasilan', 'jumlah_tanggungan',
    'kondisi_lantai', 'kondisi_dinding', 'kondisi_atap', 'status_kepemilikan_rumah',
    'pendidikan_terakhir', 'jumlah_anak_sekolah', 'kepemilikan_aset', 'pengeluaran_bulanan',
    'akses_listrik', 'akses_air', 'kondisi_kesehatan'
]

# Read CSV data
csv_path = 'data_training_1000.csv'
data_rows = []
with open(csv_path, mode='r', encoding='utf-8') as f:
    reader = csv.DictReader(f)
    for row in reader:
        data_rows.append(row)

print(f"Loaded {len(data_rows)} rows from {csv_path}")

# Initialize and fit encoders
label_encoders = {}
for col in feature_columns:
    le = LabelEncoder()
    # Combine user predefined list and unique values from CSV (just in case)
    csv_vals = set(row[col] for row in data_rows if row[col] is not None)
    union_vals = sorted(list(set(all_possible_values[col]).union(csv_vals)))
    le.fit(union_vals)
    label_encoders[col] = le

target_encoder = LabelEncoder()
target_encoder.fit(['Layak', 'Tidak Layak'])

# Prepare X and y
X = []
y = []
for row in data_rows:
    encoded_row = []
    for col in feature_columns:
        val = row[col]
        encoded_row.append(label_encoders[col].transform([val])[0])
    X.append(encoded_row)
    y.append(target_encoder.transform([row['status_bantuan']])[0])

X = np.array(X)
y = np.array(y)

# Train Multinomial Naive Bayes
model = MultinomialNB(alpha=0.01, fit_prior=True)
model.fit(X, y)

# Evaluate model
y_pred = model.predict(X)
acc = accuracy_score(y, y_pred)
precision = precision_score(y, y_pred, average='weighted')
recall = recall_score(y, y_pred, average='weighted')
f1 = f1_score(y, y_pred, average='weighted')

cv_scores = cross_val_score(model, X, y, cv=5)
cv_best = cv_scores.mean()

print("\n--- Model Training Metrics ---")
print(f"Accuracy: {acc:.6f}")
print(f"Precision (weighted): {precision:.6f}")
print(f"Recall (weighted): {recall:.6f}")
print(f"F1-Score (weighted): {f1:.6f}")
print(f"CV 5-Fold Mean Accuracy: {cv_best:.6f}")
print("------------------------------")

# Save model dictionary
model_dict = {
    'model': model,
    'model_params': {'alpha': 0.01, 'fit_prior': True},
    'label_encoders': label_encoders,
    'target_encoder': target_encoder,
    'feature_columns': feature_columns,
    'accuracy': acc,
    'precision': precision,
    'recall': recall,
    'f1_score': f1,
    'cv_best_score': cv_best
}

output_dir = os.path.join('api', 'model')
os.makedirs(output_dir, exist_ok=True)
output_path = os.path.join(output_dir, 'bansos_multinomial_nb_model.pkl')
joblib.dump(model_dict, output_path)
print(f"Successfully saved trained model to {output_path}")
