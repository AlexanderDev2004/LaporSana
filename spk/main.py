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
kriteria_info = {
    'Urgensi': 'benefit',
    'Kerusakan': 'benefit',
    'Jumlah Pelapor': 'benefit',
    'Biaya Perbaikan': 'cost',
    'Poin Derajat': 'benefit'

    # JUMLAH PELAPOR (laporan)
    # TINGKAT_URGENSI (Fasilitas)
    # Poin Role (role)
    # Tingkat Kerusakan (Detail_Tugas)
    # Biaya ( Detail Tugas)
}

# ------------------- FUNGSI PSI -------------------
def hitung_bobot_psi(data, kriteria_info):
    matrix = data.copy()
    for col in kriteria_info:
        total = matrix[col].sum()
        matrix[col] = matrix[col] / total if total != 0 else 0

    entropy = []
    for col in kriteria_info:
        col_vals = matrix[col]
        col_vals = np.where(col_vals == 0, 1e-10, col_vals)
        e = -np.sum(col_vals * np.log(col_vals)) / np.log(len(matrix))
        entropy.append(e)

    d = 1 - np.array(entropy)
    bobot = d / d.sum()
    return dict(zip(kriteria_info.keys(), bobot))

# ------------------- FUNGSI EDAS -------------------
def perangkingan_edas(kriteria_data, bobot, kriteria_info, alternatif_list):
    matrix = kriteria_data.copy()
    avg = matrix.mean()

    spi = []  # positive distance
    sni = []  # negative distance

    for index, row in matrix.iterrows():
        pi = []
        ni = []
        for col in kriteria_info:
            if kriteria_info[col] == 'benefit':
                pi.append(max(0, (row[col] - avg[col]) / avg[col]))
                ni.append(max(0, (avg[col] - row[col]) / avg[col]))
            else:  # cost
                pi.append(max(0, (avg[col] - row[col]) / avg[col]))
                ni.append(max(0, (row[col] - avg[col]) / avg[col]))
        spi.append(pi)
        sni.append(ni)

    spi = np.array(spi)
    sni = np.array(sni)

    weighted_spi = spi * np.array(list(bobot.values()))
    weighted_sni = sni * np.array(list(bobot.values()))

    appraisal_score = 0.5 * weighted_spi.sum(axis=1) + 0.5 * (1 - weighted_sni.sum(axis=1))

    results = pd.DataFrame({
        'Alternatif': alternatif_list,
        'Appraisal Score': appraisal_score
    }).sort_values(by='Appraisal Score', ascending=False)

    return results

# ------------------- API ROUTE -------------------
@app.route('/spk/calculate', methods=['POST'])
def calculate_spk():
    try:
        content = request.json
        data = pd.DataFrame(content['data'])

        alternatif_list = data['Alternatif']
        kriteria_data = data.drop(columns=['Alternatif'])

        bobot_kriteria = hitung_bobot_psi(kriteria_data, kriteria_info)
        ranking = perangkingan_edas(kriteria_data, bobot_kriteria, kriteria_info, alternatif_list)

        return jsonify({
            'bobot': bobot_kriteria,
            'ranking': ranking.to_dict(orient='records')
        })

    except Exception as e:
        return jsonify({'error': str(e)}), 400

if __name__ == '__main__':
    app.run(debug=True, port=5001)
