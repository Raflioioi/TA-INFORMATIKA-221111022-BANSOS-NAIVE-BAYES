from fastapi import FastAPI, Query
from fastapi.responses import JSONResponse
import uvicorn
import pymysql
import os
import joblib
import numpy as np
import re

app = FastAPI()

# Config Database
DB_HOST = os.environ.get('DB_HOST', 'localhost')
DB_USER = os.environ.get('DB_USER', 'root')
DB_PASSWORD = os.environ.get('DB_PASSWORD', '')
DB_NAME = os.environ.get('DB_NAME', 'db_bansos')

def get_db_connection():
    return pymysql.connect(
        host=DB_HOST,
        user=DB_USER,
        password=DB_PASSWORD,
        database=DB_NAME,
        cursorclass=pymysql.cursors.DictCursor
    )

# Load Pre-trained Model
MODEL_PATH = os.path.join(os.path.dirname(__file__), 'model', 'bansos_multinomial_nb_model.pkl')
try:
    model_dict = joblib.load(MODEL_PATH)
    model = model_dict['model']
    label_encoders = model_dict['label_encoders']
    target_encoder = model_dict['target_encoder']
    feature_columns = model_dict['feature_columns']
    model_loaded = True
except Exception as e:
    print(f"Error loading model: {e}")
    model_loaded = False

def clean_value(col, val, classes):
    val_str = str(val).strip()
    if val_str in classes:
        return val_str
    
    # Try case-insensitive matching
    for cls in classes:
        if cls.lower() == val_str.lower():
            return cls
            
    # Try normalized matching (remove non-alphanumeric, case-insensitive)
    def normalize(s):
        return re.sub(r'[^a-zA-Z0-9]', '', s).lower()
        
    norm_val = normalize(val_str)
    for cls in classes:
        if normalize(cls) == norm_val:
            return cls
            
    return val_str

@app.get('/predict')
def predict(id_warga: str = Query(None)):
    if not id_warga:
        return JSONResponse(status_code=400, content={"error": "id_warga is required"})

    if not model_loaded:
        return JSONResponse(status_code=500, content={"error": "Model Machine Learning (.pkl) tidak berhasil dimuat."})

    conn = get_db_connection()
    try:
        with conn.cursor() as cursor:
            # 1. Ambil target
            cursor.execute("SELECT * FROM warga WHERE id_warga = %s", (id_warga,))
            target = cursor.fetchone()
            
            if not target:
                return JSONResponse(status_code=404, content={"error": "Data warga tidak ditemukan."})

            # 2. Proses dan normalisasi fitur untuk model
            encoded_features = []
            for col in feature_columns:
                val = target.get(col)
                if val is None:
                    return JSONResponse(status_code=400, content={"error": f"Kolom data '{col}' tidak ditemukan pada profil warga."})
                
                classes = list(label_encoders[col].classes_)
                cleaned_val = clean_value(col, val, classes)
                
                try:
                    encoded_val = label_encoders[col].transform([cleaned_val])[0]
                    encoded_features.append(encoded_val)
                except Exception as e:
                    return JSONResponse(status_code=400, content={"error": f"Nilai '{val}' pada kriteria '{col}' tidak dapat dikenali oleh model."})
            
            # 3. Prediksi menggunakan model MultinomialNB
            X = np.array([encoded_features])
            pred = model.predict(X)
            predicted_status = target_encoder.inverse_transform(pred)[0]
            
            # Dapatkan probabilitas masing-masing kelas
            proba = model.predict_proba(X)[0]
            prob_dict = {cls: float(pb) for cls, pb in zip(target_encoder.classes_, proba)}
            
            score_layak = prob_dict.get('Layak', 0.0)
            score_tidak_layak = prob_dict.get('Tidak Layak', 0.0)

            # 4. Alasan Utama (3 fitur paling berkontribusi pada keputusan)
            class_idx = list(target_encoder.classes_).index(predicted_status)
            other_class_idx = 1 - class_idx
            
            feature_labels = {
                'jenis_kelamin': 'Jenis Kelamin',
                'status_perkawinan': 'Status Perkawinan',
                'pekerjaan': 'Pekerjaan',
                'penghasilan': 'Penghasilan',
                'jumlah_tanggungan': 'Jumlah Tanggungan',
                'kondisi_lantai': 'Kondisi Lantai',
                'kondisi_dinding': 'Kondisi Dinding',
                'kondisi_atap': 'Kondisi Atap',
                'status_kepemilikan_rumah': 'Status Kepemilikan Rumah',
                'pendidikan_terakhir': 'Pendidikan Terakhir',
                'jumlah_anak_sekolah': 'Jumlah Anak Sekolah',
                'kepemilikan_aset': 'Kepemilikan Aset',
                'pengeluaran_bulanan': 'Pengeluaran Bulanan',
                'akses_listrik': 'Daya Listrik',
                'akses_air': 'Sumber Air',
                'kondisi_kesehatan': 'Kondisi Kesehatan'
            }

            feature_contrib = []
            for i, col in enumerate(feature_columns):
                val = encoded_features[i]
                log_prob_pred = model.feature_log_prob_[class_idx][i]
                log_prob_other = model.feature_log_prob_[other_class_idx][i]
                contrib = (val + 1) * (log_prob_pred - log_prob_other)
                feature_contrib.append((feature_labels.get(col, col), contrib))

            sorted_faktor = sorted(feature_contrib, key=lambda item: item[1], reverse=True)
            top_faktor = [k for k, v in sorted_faktor[:3]]
            alasan = ", ".join(top_faktor)

            # Response ke PHP
            return {
                "predicted_status": predicted_status,
                "score_layak": score_layak,
                "score_tidak_layak": score_tidak_layak,
                "alasan": alasan
            }

    finally:
        conn.close()

@app.post('/predict_all')
def predict_all():
    if not model_loaded:
        return JSONResponse(status_code=500, content={"error": "Model Machine Learning (.pkl) tidak berhasil dimuat."})

    conn = get_db_connection()
    try:
        with conn.cursor() as cursor:
            # 1. Ambil semua warga dengan status 'Proses'
            cursor.execute("SELECT * FROM warga WHERE status_bantuan = 'Proses'")
            warga_list = cursor.fetchall()
            
            if not warga_list:
                return {"success_count": 0, "layak_count": 0, "tidak_layak_count": 0}

            # 2. Proses dan encode fitur
            X_list = []
            valid_warga_ids = []
            
            for target in warga_list:
                encoded_features = []
                valid = True
                for col in feature_columns:
                    val = target.get(col)
                    if val is None:
                        valid = False
                        break
                    
                    classes = list(label_encoders[col].classes_)
                    cleaned_val = clean_value(col, val, classes)
                    
                    try:
                        encoded_val = label_encoders[col].transform([cleaned_val])[0]
                        encoded_features.append(encoded_val)
                    except Exception:
                        valid = False
                        break
                
                if valid:
                    X_list.append(encoded_features)
                    valid_warga_ids.append(target['id_warga'])

            if not X_list:
                return {"success_count": 0, "layak_count": 0, "tidak_layak_count": 0}

            # 3. Prediksi sekaligus (Bulk Prediction)
            X = np.array(X_list)
            preds = model.predict(X)
            predicted_statuses = target_encoder.inverse_transform(preds)

            # 4. Update status ke database secara massal
            success_count = 0
            layak_count = 0
            tidak_layak_count = 0
            
            for id_warga, status in zip(valid_warga_ids, predicted_statuses):
                cursor.execute("UPDATE warga SET status_bantuan = %s WHERE id_warga = %s", (status, id_warga))
                success_count += 1
                if status == 'Layak':
                    layak_count += 1
                else:
                    tidak_layak_count += 1
            
            conn.commit()
            
            return {
                "success_count": success_count,
                "layak_count": layak_count,
                "tidak_layak_count": tidak_layak_count
            }

    except Exception as e:
        conn.rollback()
        return JSONResponse(status_code=500, content={"error": f"Gagal memproses prediksi massal: {str(e)}"})
    finally:
        conn.close()

if __name__ == '__main__':
    host = os.environ.get('API_BIND_HOST', '0.0.0.0')
    port = int(os.environ.get('API_PORT', 5000))
    uvicorn.run(app, host=host, port=port)

