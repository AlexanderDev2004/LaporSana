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
    'Urgensi': 'benefit',           # m_fasilitas.tingkat_urgensi
    'Kerusakan': 'benefit',         # m_tugas_detail.tingkat_kerusakan
    'Jumlah Pelapor': 'benefit',    # jumlah laporan untuk fasilitas tersebut
    'Biaya Perbaikan': 'cost',      # m_tugas_detail.biaya_perbaikan
    'Poin Derajat': 'benefit'       # m_roles.poin_roles dari pelapor
}

# ------------------- FUNGSI PSI -------------------
# Fungsi untuk menghitung bobot kriteria berdasarkan metode PSI
def hitung_bobot_psi(data, kriteria_info):
    # 1. Normalisasi matriks keputusan
    norm_matrix = data.copy()
    for col in kriteria_info:
        total = norm_matrix[col].sum()
        norm_matrix[col] = norm_matrix[col] / total if total != 0 else 0

    # 2. Menghitung nilai rata-rata setiap kriteria (opsional untuk analisis)
    rata_rata = norm_matrix.mean()

    # 3. Menghitung variasi preferensi (opsional)
    variasi = norm_matrix.var(ddof=0)

    # 4. Deviasi nilai preferensi (standar deviasi)
    deviasi = norm_matrix.std(ddof=0)

    # 5. Bobot dihitung berdasarkan proporsi deviasi
    total_deviasi = deviasi.sum()
    bobot = deviasi / total_deviasi if total_deviasi != 0 else np.zeros(len(deviasi))

    return dict(zip(kriteria_info.keys(), bobot))

# ------------------- FUNGSI EDAS -------------------
# Fungsi untuk menghitung perangkingan menggunakan metode EDAS
def perangkingan_edas(kriteria_data, bobot, kriteria_info, alternatif_list):
    # 1. Matriks keputusan sudah diterima dari Laravel
    matrix = kriteria_data.copy()

    # 2. Menghitung solusi rata-rata (average solution)
    avg = matrix.mean()

    # 3. Hitung Positive Distance (PDA) dan Negative Distance (NDA)
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

    pda = np.array(pda)
    nda = np.array(nda)

    # 4. SP dan SN: total nilai positif dan negatif yang sudah diberi bobot
    sp = pda * np.array(list(bobot.values()))
    sn = nda * np.array(list(bobot.values()))
    sum_sp = sp.sum(axis=1)
    sum_sn = sn.sum(axis=1)

    # 5. Normalisasi nilai SP (NSP) dan SN (NSN)
    nsp = sum_sp / max(sum_sp) if max(sum_sp) != 0 else sum_sp
    nsn = 1 - (sum_sn / max(sum_sn)) if max(sum_sn) != 0 else 1 - sum_sn

    # 6. Appraisal Score: rata-rata dari NSP dan NSN
    appraisal_score = 0.5 * nsp + 0.5 * nsn

    # 7. Ranking akhir berdasarkan Appraisal Score
    results = pd.DataFrame({
        'Alternatif': alternatif_list,
        'Appraisal Score': appraisal_score
    }).sort_values(by='Appraisal Score', ascending=False)

    return results

# ------------------- API ROUTE -------------------
# Endpoint yang menerima data dari Laravel dan menjalankan perhitungan SPK
@app.route('/spk/calculate', methods=['POST'])
def calculate_spk():
    try:
        # Terima data JSON dari Laravel
        content = request.json
        data = pd.DataFrame(content['data'])

        # Pisahkan nama alternatif dan data kriteria
        alternatif_list = data['Alternatif']
        kriteria_data = data.drop(columns=['Alternatif'])

        # 1. Hitung bobot otomatis menggunakan metode PSI
        bobot_kriteria = hitung_bobot_psi(kriteria_data, kriteria_info)

        # 2. Hitung perangkingan alternatif menggunakan EDAS
        ranking = perangkingan_edas(kriteria_data, bobot_kriteria, kriteria_info, alternatif_list)

        # 3. Ambil 5 alternatif terbaik
        top_5 = ranking.head(5)

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
