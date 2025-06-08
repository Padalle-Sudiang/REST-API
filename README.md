# 🚗 REST API - ALPR Gate System

Sistem REST API untuk manajemen parkir kendaraan berbasis **Raspberry Pi + Kamera ALPR**. Sistem ini mendukung prediksi plat nomor otomatis, pengelolaan membership, pencatatan kendaraan ilegal, serta log riwayat parkir dan validasi pembayaran.

---

**Domain** `http://tkj-3b.com/`  

## Capture & Prediksi Plat
Kirim gambar ke server Raspberry Pi untuk diproses oleh model ALPR.

**POST** `tkj-3b.com/opengate/predict-plate.php`  

#### Request (multipart/form-data)

```bash
Content-Type: multipart/form-data
Body:
- image: (file) gambar plat kendaraan hasil capture
- plate_number (text)
- plate_type (text)
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
## Tambah Membership
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
  "plate_number": "DD 1215 ES"
}
```
## Update Membership
Update data kendaraan yang menjadi member.

**PUT** `tkj-3b.com/opengate/membership.php`  

#### Request (JSON)

```json
{
  "plate_number": "DD 1215 ES",
  "owner_name": "Rezki Andika",
  "membership_expiry": "2027-1-24"
}
```
#### Response (200 OK)

```json
{
  "message": "Membership berhasil diperbarui.",
  "plate_number": "DD 1215 ES"
}
```
## Daftar Membership
Ambil daftar kendaraan yang berstatus member.

**GET** `tkj-3b.com/opengate/membership.php`  

#### Response (200 OK)

```json
{
  "plate_number": "B 1001 ZZZ",
  "owner_name": "Raffi Fadlika",
  "membership_expiry": "2025-12-19"
}
```
## Tambah Plat Ilegal
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
## Update Plat Ilegal
Update kendaraan ilegal ke dalam daftar pencarian.

**PUT** `tkj-3b.com/opengate/illegal-plates.php`  

#### Request (JSON)

```json
{
  "plate_number": "DD 1215 ES",
  "nama_pelapor": "Andi Surya",
  "no_wa": "088363537",
  "description": "Hilang 1 tahun lalu"
}
```
#### Response (200 OK)

```json
{
  "message": "Data plat ilegal berhasil diperbarui.",
  "plate_number": "DD 1215 ES"
}
```
## Delete Plat Ilegal
Hapus daftar kendaraan yang berstatus ilegal.

**DELETE** `tkj-3b.com/opengate/illegal-plates.php?plate_number=DD 1215 ES`  

#### Response (200 OK)

```json
{
  "message": "Data plat ilegal berhasil dihapus.",
  "plate_number": "DD 1215 ES"
}
```
## Daftar Logs Plat Ilegal
Ambil daftar logs kendaraan yang berstatus ilegal.

**GET** `tkj-3b.com/opengate/get_illegal_logs.php`  

#### Response (200 OK)

```json
 "status": "success",
    "logs": [
        {
            "log_id": "389",
            "plate_number": "B 1234 WLG",
            "entry_time": "2025-06-07 20:14:10",
            "exit_time": null,
            "img_path": "http://tkj-3b.com/tkj-3b.com/opengate/uploads/B1234WLG_20250607_201410.jpg",
            "log_created_at": "2025-06-07 19:14:10"
        }
    ]
```
## Daftar Plat Ilegal
Ambil daftar kendaraan yang berstatus ilegal.

**GET** `tkj-3b.com/opengate/illegal-plates.php`  

#### Response (200 OK)

```json
{
  "plate_number": "DD 1215 ES",
  "nama_pelapor": "Rudi Tabuti",
  "no_wa": "08825344876",
  "description": "Hilang Kemarin",
  "created_at": "2025-06-06 19:30:35"
}
```
## Daftar Kendaraan 
Ambil daftar kendaraan yang terdaftar.

**GET** `tkj-3b.com/opengate/get-vehicles.php`  

#### Response (200 OK)

```json
{
  "id": "148",
  "plate_number": "DB 8010 LM",
  "plate_type": "Plat Biasa",
  "is_member": "0",
  "image_path": "http://tkj-3b.com/tkj-3b.com/opengate/uploads/DB8010LM_20250607_185522.jpg",
  "created_at": "2025-06-07 17:55:22"
}
```
## Daftar Gate Logs 
Ambil daftar gate logs.

**GET** `tkj-3b.com/opengate/gate-logs.php`  

#### Response (200 OK)

```json
{
  "id": "275",
  "plate_number": "DN 5961 IB",
  "action": "open",
  "source": "Payment Verified",
  "timestamp": "2025-06-07 19:11:15"
}
```
## Riwayat Masuk/Keluar Kendaraan
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
## Riwayat Masuk/Keluar Kendaraan
Melihat semua status kendaraan.

**GET** `tkj-3b.com/opengate/parking-logs.php`  

#### Response (200 OK)

```json
{
  "plate_number": "B 1234 WLG",
  "is_member": "0",
  "entry_time": "2025-06-07 20:14:10",
  "exit_time": null,
  "parking_fee": null,
  "img_path": "http://tkj-3b.com/tkj-3b.com/opengate/uploads/B1234WLG_20250607_201410.jpg",
  "img_path_exit": null
},
{
  "plate_number": "DB 8010 LM",
  "is_member": "0",
  "entry_time": "2025-06-07 19:03:00",
  "exit_time": "2025-06-07 20:08:01",
  "parking_fee": "6000",
  "img_path": "http://tkj-3b.com/tkj-3b.com/opengate/uploads/DB8010LM_20250607_190300.jpg",
  "img_path_exit": "http://tkj-3b.com/tkj-3b.com/opengate/uploads/DB8010LM_exit_20250607_200751.jpg"
}
```
## Validasi Pembayaran
Update jam keluar + hitung total biaya parkir.

**POST** `tkj-3b.com/opengate/parking-payment.php`  

#### Request (JSON)

```json
{
  "plate_number": "B 1234 WLG",
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
## Capture & Prediksi Plat Yang Keluar
Kirim gambar ke server Raspberry Pi untuk diproses oleh model ALPR.

**POST** `tkj-3b.com/opengate/predict-exit.php`  

#### Request (multipart/form-data)

```bash
Content-Type: multipart/form-data
Body:
- image: (file) gambar plat kendaraan hasil capture
- plate_number (text)
- plate_type (text)
```
#### Response (200 OK)

```json
{
  "status": "success",
  "message": "Gambar kendaraan keluar berhasil disimpan.",
  "plate_number": "http://tkj-3b.com/tkj-3b.com/opengate/uploads/A6380ZH_exit_20250607_155904.jpg"
}
```