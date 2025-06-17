# import pandas as pd
# import numpy as np

# # ------------------- DATA DUMMY -------------------
# data = pd.DataFrame({
#     'Alternatif': ['A1', 'A2', 'A3'],
#     'Urgensi': [4, 2, 3],
#     'Kerusakan': [3, 4, 2],
#     'Jumlah Pelapor': [5, 3, 4],
#     'Biaya Perbaikan': [2.5, 3.0, 1.5],
#     'Poin Derajat': [4, 3, 5]
# })

# # Tipe kriteria: benefit/cost
# kriteria_info = {
#     'Urgensi': 'benefit',
#     'Kerusakan': 'benefit',
#     'Jumlah Pelapor': 'benefit',
#     'Biaya Perbaikan': 'cost',
#     'Poin Derajat': 'benefit'
# }

# # ------------------- FUNGSI PSI (Bobot) -------------------
# def hitung_bobot_psi(data, kriteria_info):
#     matrix = data.copy()
#     for col in kriteria_info:
#         total = matrix[col].sum()
#         matrix[col] = matrix[col] / total if total != 0 else 0

#     entropy = []
#     for col in kriteria_info:
#         col_vals = matrix[col]
#         col_vals = np.where(col_vals == 0, 1e-10, col_vals)
#         e = -np.sum(col_vals * np.log(col_vals)) / np.log(len(matrix))
#         entropy.append(e)

#     d = 1 - np.array(entropy)
#     bobot = d / d.sum()
#     return dict(zip(kriteria_info.keys(), bobot))

# # ------------------- FUNGSI EDAS (Perangkingan) -------------------
# def perangkingan_edas(kriteria_data, bobot, kriteria_info, alternatif_list):
#     matrix = kriteria_data.copy()
#     avg = matrix.mean()

#     spi = []  # positive distance
#     sni = []  # negative distance

#     for index, row in matrix.iterrows():
#         pi = []
#         ni = []
#         for col in kriteria_info:
#             if kriteria_info[col] == 'benefit':
#                 pi.append(max(0, (row[col] - avg[col]) / avg[col]))
#                 ni.append(max(0, (avg[col] - row[col]) / avg[col]))
#             else:  # cost
#                 pi.append(max(0, (avg[col] - row[col]) / avg[col]))
#                 ni.append(max(0, (row[col] - avg[col]) / avg[col]))
#         spi.append(pi)
#         sni.append(ni)

#     spi = np.array(spi)
#     sni = np.array(sni)

#     weighted_spi = spi * np.array(list(bobot.values()))
#     weighted_sni = sni * np.array(list(bobot.values()))

#     appraisal_score = 0.5 * weighted_spi.sum(axis=1) + 0.5 * (1 - weighted_sni.sum(axis=1))

#     results = pd.DataFrame({
#         'Alternatif': alternatif_list,
#         'Appraisal Score': appraisal_score
#     }).sort_values(by='Appraisal Score', ascending=False)

#     return results

# # ------------------- EKSEKUSI -------------------

# # Simpan kolom Alternatif
# alternatif_col = data['Alternatif']
# kriteria_data = data.drop(columns=['Alternatif'])

# # Hitung bobot PSI
# bobot_kriteria = hitung_bobot_psi(kriteria_data, kriteria_info)

# # Cetak bobot
# print("Bobot Kriteria (PSI):")
# for k, v in bobot_kriteria.items():
#     print(f" - {k}: {v:.4f}")

# # Hitung perangkingan dengan EDAS
# ranking = perangkingan_edas(kriteria_data, bobot_kriteria, kriteria_info, alternatif_col)

# # Cetak hasil
# print("\nHasil Perangkingan (EDAS):")
# print(ranking.to_string(index=False))

# # Simpan ke file jika diperlukan
# # ranking.to_excel("hasil_perangkingan.xlsx", index=False)


from flask import Flask, request, jsonify
import pandas as pd
import numpy as np

app = Flask(__name__)
# ------------------- KONFIGURASI -------------------
# Menentukan tipe kriteria: benefit atau cost
# Mewakili asal data dari tabel-tabel di database Laravel
kriteria_info = {
    'Urgensi': 'benefit',           # m_laporan_detail  (fasilitas_id -> tingkat_urgensi)
    'Kerusakan': 'benefit',         # m_tugas_detail (tingkat_kerusakan)
    'Jumlah Pelapor': 'benefit',    # m_laporan  (jumlah_pelapor)
    'Biaya Perbaikan': 'cost',      # m_tugas_detail  (biaya_perbaikan)
    'Poin Derajat': 'benefit'       # m_laporan  (user_id -> role -> poin_roles)
}

# ------------------- FUNGSI PSI -------------------
# Fungsi untuk menghitung bobot kriteria berdasarkan metode PSI
def hitung_bobot_psi(data, kriteria_info):
    print("\n=== [1] Metode PSI ===")
    A = data.copy()
    m, n = A.shape
    print("1. Matriks Keputusan (A):\n", A)

    # 2. Normalisasi
    R = pd.DataFrame(index=A.index, columns=A.columns)
    for col in A.columns:
        if kriteria_info[col] == 'benefit':
            R[col] = A[col] / A[col].max()
        else:
            R[col] = A[col].min() / A[col]

    print("\n2. Matriks Normalisasi (R):\n", R)

    # 🔢 Total per kolom setelah normalisasi
    R_total_per_kriteria = R.sum()
    print("\n2b. Total per Kriteria (Jumlah Normalisasi Kolom):\n", R_total_per_kriteria)

    # 3. Mean tiap kriteria
    R_mean = R.mean()
    print("\n3. Rata-rata (Ēₖ):\n", R_mean)

    # 4. Preference Variation PVj
    PV = ((R - R_mean) ** 2).sum()
    print("\n4. Preference Variation (PVₖ):\n", PV)

    # 5. Deviation Φj = 1 - PVj
    PHI = abs(1- PV)
    # PHI = PV / R_mean
    print("\n5. Deviation (Φₖ):\n", PHI)

    # 6. Preference Index ψj = Φj / ΣΦ
    psi = PHI / PHI.sum()
    print("\n6. Overall Preference (ψₖ) - Bobot Kriteria:\n", psi)

    return psi.to_dict()

# ------------------- FUNGSI EDAS -------------------
# Fungsi untuk menghitung bobot kriteria berdasarkan metode EDAS
def perangkingan_edas(kriteria_data, bobot, kriteria_info, alternatif_list):
    print("\n=== [2] Metode EDAS ===")
    matrix = kriteria_data.copy()
    print("\n=== [1] Matriks Keputusan ===")
    print(matrix)

    # 2. Menghitung Solusi Rata-rata (AVG)
    avg = matrix.mean()
    print("\n=== [2] Solusi Rata-rata (AVG) per Kriteria ===")
    print(avg)

    # 3. Hitung PDA dan NDA
    pda, nda = [], []
    for _, row in matrix.iterrows():
        pi, ni = [], []
        for col in kriteria_info:
            if kriteria_info[col] == 'benefit':
                pi.append(max(0, (row[col] - avg[col]) / avg[col]))
                ni.append(max(0, (avg[col] - row[col]) / avg[col]))
            else:  # cost
                pi.append(max(0, (avg[col] - row[col]) / avg[col]))
                ni.append(max(0, (row[col] - avg[col]) / avg[col]))
        pda.append(pi)
        nda.append(ni)

    pda_df = pd.DataFrame(pda, columns=kriteria_info.keys(), index=alternatif_list)
    nda_df = pd.DataFrame(nda, columns=kriteria_info.keys(), index=alternatif_list)

    print("\n=== [3] Positive Distance from Average (PDA) ===")
    print(pda_df)
    print("\n=== [4] Negative Distance from Average (NDA) ===")
    print(nda_df)

    # 4. SP dan SN: Total nilai positif dan negatif yang sudah diberi bobot
    bobot_array = np.array(list(bobot.values()))
    sp = pda_df.values * bobot_array
    sn = nda_df.values * bobot_array

    sp_df = pd.DataFrame(sp, columns=kriteria_info.keys(), index=alternatif_list)
    sn_df = pd.DataFrame(sn, columns=kriteria_info.keys(), index=alternatif_list)

    print("\n=== [5] SP (Weighted PDA) ===")
    print(sp_df)
    print("\n=== [6] SN (Weighted NDA) ===")
    print(sn_df)

    sum_sp = sp.sum(axis=1)
    sum_sn = sn.sum(axis=1)

    print("\n=== [7] Total SP per Alternatif ===")
    print(pd.Series(sum_sp, index=alternatif_list))
    print("\n=== [8] Total SN per Alternatif ===")
    print(pd.Series(sum_sn, index=alternatif_list))

    # 5. Normalisasi nilai SP (NSP) dan SN (NSN)
    max_sp = max(sum_sp) if max(sum_sp) != 0 else 1
    max_sn = max(sum_sn) if max(sum_sn) != 0 else 1

    nsp = sum_sp / max_sp
    nsn = 1 - (sum_sn / max_sn)

    print("\n=== [9] Normalized SP (NSP) ===")
    print(pd.Series(nsp, index=alternatif_list))
    print("\n=== [10] Normalized SN (NSN) ===")
    print(pd.Series(nsn, index=alternatif_list))

    # 6. Appraisal Score
    appraisal_score = 0.5 * nsp + 0.5 * nsn

    print("\n=== [11] Appraisal Score ===")
    print(pd.Series(appraisal_score, index=alternatif_list))

    # 7. Hasil akhir ranking
    results = pd.DataFrame({
        'Alternatif': alternatif_list,
        'AppraisalScore': appraisal_score,
        # 'Ranking': (-appraisal_score).aegsort().argsort() + 1
    }).sort_values(by='AppraisalScore', ascending=False)
    results['Ranking'] = results['AppraisalScore'].rank(ascending=False, method='min').astype(int)
    results = results.sort_values(by='AppraisalScore', ascending=False)
    print("\n=== [12] Hasil Perangkingan Akhir ===")
    print(results)

    return results

# ------------------- API ROUTE -------------------
# Endpoint yang menerima data dari Laravel dan menjalankan perhitungan SPK
@app.route('/spk/calculate', methods=['POST'])
def calculate_spk():
    try:
        # Terima data JSON dari Laravel
        content = request.json

        # Debug isi payload
        print("== Payload dari Laravel ==")
        print(content)

        # Pastikan kunci 'data' ada
        if 'data' not in content:
            return jsonify({'error': "Payload tidak mengandung kunci 'data'"}), 400

        data = pd.DataFrame(content['data'])

        # Debug isi DataFrame awal
        print("== DataFrame Diterima ==")
        print(data.head())

        # Cek apakah kolom 'Alternatif' ada
        if 'Alternatif' not in data.columns:
            return jsonify({'error': "Kolom 'Alternatif' tidak ditemukan di data"}), 400

        # Pisahkan nama alternatif dan data kriteria
        alternatif_list = data['Alternatif']
        kriteria_data = data.drop(columns=['Alternatif', 'fasilitas_id'], errors='ignore')

        # 1. Hitung bobot otomatis menggunakan metode PSI
        bobot_kriteria = hitung_bobot_psi(kriteria_data, kriteria_info)

        # 2. Hitung perangkingan alternatif menggunakan EDAS
        ranking = perangkingan_edas(kriteria_data, bobot_kriteria, kriteria_info, alternatif_list)

        # 3. Ambil 5 alternatif terbaik
        top_5 = ranking.head(10)

        # 4. Kembalikan hasil dalam format JSON
        return jsonify({
            'bobot': bobot_kriteria,
            'ranking': top_5.to_dict(orient='records')
        })

    except Exception as e:
        # Tangani error jika input tidak valid atau terjadi kegagalan
        return jsonify({'error': str(e)}), 400

# ------------------- MENJALANKAN SERVER -------------------
if __name__ == '__main__':
    app.run(debug=True, port=5001)
