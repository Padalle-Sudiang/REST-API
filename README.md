# 🚗 REST API - ALPR Gate System

Sistem REST API untuk manajemen parkir kendaraan berbasis **Raspberry Pi + Kamera ALPR**. Sistem ini mendukung prediksi plat nomor otomatis, pengelolaan membership, pencatatan kendaraan ilegal, serta log riwayat parkir dan validasi pembayaran.

---

**Domain** `http://tkj-3b.com/`  

## 📦 Capture & Prediksi Plat
Kirim gambar ke server Raspberry Pi untuk diproses oleh model ALPR.

**POST** `tkj-3b.com/opengate/predict-plate.php`  

#### Request (multipart/form-data)

```bash
Content-Type: multipart/form-data
Body:
- image: (file) gambar plat kendaraan hasil capture
```
#### Response (200 OK)

```json
{
  "plate_number": "DD1234AB",
  "plate_type": "Plat Biasa",
  "status": "new",
  "entry_time": "2025-06-07 10:45:12",
  "image_path": "http://tkj-3b.com/tkj-3b.com/opengate/uploads/DD1234AB_20250607_104512.jpg",
  "illegal_status": {
    "is_illegal": false,
    "description": null
  },
  "membership": {
    "is_member": false,
    "owner_name": null
  },
  "open_gate": true
}
```
## 📥 Tambah Membership
Menambahkan data kendaraan yang menjadi member.

**POST** `tkj-3b.com/opengate/membership.php`  

#### Request (JSON)

```json
{
  "plate_number": "DD 1215 ES",
  "owner_name": "Rezki Andika",
  "membership_expiry": "2026-1-24"
}
```
#### Response (200 OK)

```json
{
  "message": "Membership berhasil ditambahkan.",
  "plate_number": "DD 1000 XX"
}
```
## 📋 Daftar Membership
Ambil daftar kendaraan yang berstatus member.

**GET** `tkj-3b.com/opengate/membership.php`  

#### Response (200 OK)

```json
[
    {
        "plate_number": "B 1001 ZZZ",
        "owner_name": "Raffi Fadlika",
        "membership_expiry": "2025-12-19"
    }
]
```
## 🚫 Tambah Plat Ilegal
Tambahkan kendaraan ilegal ke dalam daftar pencarian.

**POST** `tkj-3b.com/opengate/illegal-plates.php`  

#### Request (JSON)

```json
{
  "plate_number": "DD 1215 ES",
  "nama_pelapor": "Rudi Tabuti",
  "no_wa": "088363537",
  "description": "Hilang 1 tahun lalu"
}
```
#### Response (200 OK)

```json
{
  "message": "Plat ilegal berhasil ditambahkan.",
  "plate_number": "DD 1215 ES"
}
```
## 📋 Daftar Plat Ilegal
Ambil daftar kendaraan yang berstatus ilegal.

**GET** `tkj-3b.com/opengate/illegal-plates.php`  

#### Response (200 OK)

```json
[
    {
        "plate_number": "DD 1215 ES",
        "nama_pelapor": "Rudi Tabuti",
        "no_wa": "08825344876",
        "description": "Hilang Kemarin",
        "created_at": "2025-06-06 19:30:35"
    }
]
```
## 🕓 Riwayat Masuk/Keluar Kendaraan
Melihat status kendaraan berdasarkan plat.

**GET** `tkj-3b.com/opengate/parking-logs.php?plate_number=B 6118 PZZ`  

#### Response (200 OK)

```json
{
    "plate_number": "B 6118 PZZ",
    "entry_time": "2025-06-06 20:10:36",
    "exit_time": "2025-06-06 12:12:46",
    "parking_fee": 5000
}
```
## 🕓 Riwayat Masuk/Keluar Kendaraan
Melihat status kendaraan berdasarkan plat.

**GET** `tkj-3b.com/opengate/parking-logs.php?plate_number=B 6118 PZZ`  

#### Response (200 OK)

```json
{
    "plate_number": "B 6118 PZZ",
    "entry_time": "2025-06-06 20:10:36",
    "exit_time": "2025-06-06 12:12:46",
    "parking_fee": 5000
}
```
## 💸 Validasi Pembayaran
Update jam keluar + hitung total biaya parkir.

**POST** `tkj-3b.com/opengate/parking-payment.php`  

#### Request (JSON)

```json
{
  "plate_number": "B 1234 WLG",
  "exit_time": "2025-06-06T19:10:00Z",
  "amount_paid": 5000
}
```
#### Response (200 OK)

```json
{
  "message": "Pembayaran berhasil diproses.",
  "total_fee": 3000,
  "change": 2000,
  "open_gate": true
}
```