# 🚗 REST API - ALPR Gate System

Sistem REST API untuk manajemen parkir kendaraan berbasis **Raspberry Pi + Kamera ALPR**. Sistem ini mendukung prediksi plat nomor otomatis, pengelolaan membership, pencatatan kendaraan ilegal, serta log riwayat parkir dan validasi pembayaran.

---

## 📦 1. Endpoint: Capture & Prediksi Plat

**POST** `/opengate/predict-plate.php`  

### Request (multipart/form-data)

```bash
curl -X POST http://tkj-3b.com/tkj-3b.com/opengate/predict-plate.php \
  -F "image=@mobil.jpg" \
  -F "plate_number=B1234XYZ" \
  -F "plate_type=Plate Biasa"
```
### Response (200 OK):

```json
{
  "plate_number": "DD1234AB",
  "plate_type": "motorcycle",
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
