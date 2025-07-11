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

# Konfigurasi kriteria
kriteria_info = {
    'Urgensi': 'benefit',
    'Kerusakan': 'benefit',
    'Jumlah Pelapor': 'benefit',
    'Biaya Perbaikan': 'cost',
    'Poin Derajat': 'benefit'
}

# Fungsi untuk menghitung bobot kriteria berdasarkan metode PSI
def hitung_bobot_psi(data, kriteria_info):
    steps = {}
    # 1. Matriks Keputusan
    A = data.copy()
    m, n = A.shape
    print("1. Matriks Keputusan (A):\n", A)
    steps['step_1_matriks_keputusan'] = {
        'description': 'Matriks Keputusan (A)\nRumus: A = [a_ij], i=1..m, j=1..n',
        'data': A.reset_index().to_dict(orient='records'),
        'shape': {'rows': m, 'columns': n}
    }

    # 2. Normalisasi
    R = pd.DataFrame(index=A.index, columns=A.columns)
    for col in A.columns:
        if kriteria_info[col] == 'benefit':
            max_val = A[col].max()
            R[col] = A[col] / max_val if max_val != 0 else 0
        else:
            min_val = A[col].min()
            R[col] = min_val / A[col] if min_val != 0 else 0

    print("\n2. Matriks Normalisasi (R):\n", R)
    steps['step_2_matriks_normalisasi'] = {
        'description': 'Matriks Normalisasi (R)\nRumus: r_ij = a_ij / max(a_j) untuk benefit, r_ij = min(a_j) / a_ij untuk cost',
        'data': R.astype(float).reset_index().to_dict(orient='records')
    }

    # 2b. Total per kolom setelah normalisasi
    R_total_per_kriteria = R.sum()
    print("\n2b. Total per Kriteria (Jumlah Normalisasi Kolom):\n", R_total_per_kriteria)
    steps['step_2b_total_per_kriteria'] = {
        'description': 'Total per Kriteria (Jumlah Normalisasi Kolom)\nRumus: Σ r_ij untuk setiap kriteria j',
        'data': R_total_per_kriteria.to_dict()
    }

    # 3. Mean tiap kriteria
    R_mean = R.mean()
    print("\n3. Rata-rata (Ēₖ):\n", R_mean)
    steps['step_3_rata_rata'] = {
        'description': 'Rata-rata (Ēₖ)\nRumus: Ēₖ = (1/m) Σ r_ij untuk setiap kriteria k',
        'data': R_mean.to_dict()
    }

    # 4. Preference Variation PVj
    PV = ((R - R_mean) ** 2).sum()
    print("\n4. Preference Variation (PVₖ):\n", PV)
    steps['step_4_preference_variation'] = {
        'description': 'Preference Variation (PVₖ)\nRumus: PVₖ = Σ (r_ik - Ēₖ)^2 untuk setiap kriteria k',
        'data': PV.to_dict()
    }

    # 5. Deviation Φj = 1 - PVj
    PHI = abs(1 - PV)
    print("\n5. Deviation (Φₖ):\n", PHI)
    steps['step_5_deviation'] = {
        'description': 'Deviation (Φₖ)\nRumus: Φₖ = |1 - PVₖ|',
        'data': PHI.to_dict()
    }

    # 6. Preference Index ψj = Φj / ΣΦ
    psi = PHI / PHI.sum()
    print("\n6. Overall Preference (ψₖ) - Bobot Kriteria:\n", psi)
    steps['step_6_preference_index'] = {
        'description': 'Overall Preference (ψₖ) - Bobot Kriteria\nRumus: ψₖ = Φₖ / ΣΦₖ',
        'data': psi.to_dict()
    }

    return psi.to_dict(), steps

def perangkingan_edas(kriteria_data, bobot, kriteria_info, alternatif_list):
    steps = {}
    # 1. Matriks Keputusan
    matrix = kriteria_data.copy()
    print("\n=== [1] Matriks Keputusan ===")
    print(matrix)
    steps['step_1_matriks_keputusan'] = {
        'description': 'Matriks Keputusan (A)\nRumus: A = [a_ij], i=1..m, j=1..n',
        'data': matrix.reset_index().to_dict(orient='records'),
        'shape': {'rows': len(matrix), 'columns': len(matrix.columns)}
    }

    # 2. Menghitung Solusi Rata-rata (AVG)
    avg = matrix.mean()
    print("\n=== [2] Solusi Rata-rata (AVG) per Kriteria ===")
    print(avg)
    steps['step_2_solusi_rata_rata'] = {
        'description': 'Solusi Rata-rata (AVG) per Kriteria\nRumus: AVG_j = (1/m) Σ a_ij',
        'data': avg.to_dict()
    }

    # 3. Hitung PDA dan NDA
    pda, nda = [], []
    for _, row in matrix.iterrows():
        pi, ni = [], []
        for col in kriteria_info:
            if kriteria_info[col] == 'benefit':
                pi.append(max(0, (row[col] - avg[col]) / avg[col] if avg[col] != 0 else 0))
                ni.append(max(0, (avg[col] - row[col]) / avg[col] if avg[col] != 0 else 0))
            else:  # cost
                pi.append(max(0, (avg[col] - row[col]) / avg[col] if avg[col] != 0 else 0))
                ni.append(max(0, (row[col] - avg[col]) / avg[col] if avg[col] != 0 else 0))
        pda.append(pi)
        nda.append(ni)

    pda_df = pd.DataFrame(pda, columns=kriteria_info.keys(), index=alternatif_list)
    nda_df = pd.DataFrame(nda, columns=kriteria_info.keys(), index=alternatif_list)

    print("\n=== [3] Positive Distance from Average (PDA) ===")
    print(pda_df)
    steps['step_3_positive_distance'] = {
        'description': 'Positive Distance from Average (PDA)\nRumus: PDA_ij = max(0, (a_ij - AVG_j) / AVG_j) untuk benefit, max(0, (AVG_j - a_ij) / AVG_j) untuk cost',
        'data': pda_df.reset_index().to_dict(orient='records')
    }
    print("\n=== [4] Negative Distance from Average (NDA) ===")
    print(nda_df)
    steps['step_4_negative_distance'] = {
        'description': 'Negative Distance from Average (NDA)\nRumus: NDA_ij = max(0, (AVG_j - a_ij) / AVG_j) untuk benefit, max(0, (a_ij - AVG_j) / AVG_j) untuk cost',
        'data': nda_df.reset_index().to_dict(orient='records')
    }

    # 4. SP dan SN: Total nilai positif dan negatif yang sudah diberi bobot
    bobot_array = np.array(list(bobot.values()))
    sp = pda_df.values * bobot_array
    sn = nda_df.values * bobot_array

    sp_df = pd.DataFrame(sp, columns=kriteria_info.keys(), index=alternatif_list)
    sn_df = pd.DataFrame(sn, columns=kriteria_info.keys(), index=alternatif_list)

    print("\n=== [5] SP (Weighted PDA) ===")
    print(sp_df)
    steps['step_5_weighted_positive_distance'] = {
        'description': 'SP (Weighted PDA)\nRumus: SP_i = Σ (PDA_ij * w_j)',
        'data': sp_df.reset_index().to_dict(orient='records')
    }
    print("\n=== [6] SN (Weighted NDA) ===")
    print(sn_df)
    steps['step_6_weighted_negative_distance'] = {
        'description': 'SN (Weighted NDA)\nRumus: SN_i = Σ (NDA_ij * w_j)',
        'data': sn_df.reset_index().to_dict(orient='records')
    }

    sum_sp = sp.sum(axis=1)
    sum_sn = sn.sum(axis=1)

    print("\n=== [7] Total SP per Alternatif ===")
    print(pd.Series(sum_sp, index=alternatif_list))
    steps['step_7_total_sp'] = {
        'description': 'Total SP per Alternatif\nRumus: Total SP_i = Σ SP_ij',
        'data': pd.Series(sum_sp, index=alternatif_list).to_dict()
    }
    print("\n=== [8] Total SN per Alternatif ===")
    print(pd.Series(sum_sn, index=alternatif_list))
    steps['step_8_total_sn'] = {
        'description': 'Total SN per Alternatif\nRumus: Total SN_i = Σ SN_ij',
        'data': pd.Series(sum_sn, index=alternatif_list).to_dict()
    }

    # 5. Normalisasi nilai SP (NSP) dan SN (NSN)
    max_sp = max(sum_sp) if max(sum_sp) != 0 else 1
    max_sn = max(sum_sn) if max(sum_sn) != 0 else 1

    nsp = sum_sp / max_sp
    nsn = 1 - (sum_sn / max_sn)

    print("\n=== [9] Normalized SP (NSP) ===")
    print(pd.Series(nsp, index=alternatif_list))
    steps['step_9_normalized_sp'] = {
        'description': 'Normalized SP (NSP)\nRumus: NSP_i = SP_i / max(SP)',
        'data': pd.Series(nsp, index=alternatif_list).to_dict()
    }
    print("\n=== [10] Normalized SN (NSN) ===")
    print(pd.Series(nsn, index=alternatif_list))
    steps['step_10_normalized_sn'] = {
        'description': 'Normalized SN (NSN)\nRumus: NSN_i = 1 - (SN_i / max(SN))',
        'data': pd.Series(nsn, index=alternatif_list).to_dict()
    }

    # 6. Appraisal Score
    appraisal_score = 0.5 * nsp + 0.5 * nsn

    print("\n=== [11] Appraisal Score ===")
    print(pd.Series(appraisal_score, index=alternatif_list))
    steps['step_11_appraisal_score'] = {
        'description': 'Appraisal Score\nRumus: AS_i = 0.5 * NSP_i + 0.5 * NSN_i',
        'data': pd.Series(appraisal_score, index=alternatif_list).to_dict()
    }

    # 7. Hasil akhir ranking
    results = pd.DataFrame({
        'Alternatif': alternatif_list,
        'AppraisalScore': appraisal_score
    }).sort_values(by='AppraisalScore', ascending=False)
    results['Ranking'] = results['AppraisalScore'].rank(ascending=False, method='min').astype(int)
    results = results.sort_values(by='AppraisalScore', ascending=False)

    print("\n=== [12] Hasil Perangkingan Akhir ===")
    print(results)
    steps['step_12_hasil_perangkingan'] = {
        'description': 'Hasil Perangkingan Akhir\nRumus: Urutkan berdasarkan Appraisal Score tertinggi',
        'data': results.reset_index(drop=True).to_dict(orient='records')
    }

    return results, steps

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
        bobot_kriteria, psi_steps = hitung_bobot_psi(kriteria_data, kriteria_info)

        # 2. Hitung perangkingan alternatif menggunakan EDAS
        ranking, edas_steps = perangkingan_edas(kriteria_data, bobot_kriteria, kriteria_info, alternatif_list)

        # 3. Ambil 10 alternatif terbaik
        top_10 = ranking.head(10)

        # 4. Kembalikan hasil dalam format JSON
        return jsonify({
            'bobot': bobot_kriteria,
            'ranking': top_10.to_dict(orient='records'),
            'psi_steps': psi_steps,
            'edas_steps': edas_steps
        })

    except Exception as e:
        # Tangani error jika input tidak valid atau terjadi kegagalan
        return jsonify({'error': str(e)}), 400

# MENJALANKAN SERVER
if __name__ == '__main__':
    app.run(debug=True, port=5001)
