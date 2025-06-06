# 🚗 REST API - ALPR Gate System

Sistem REST API untuk manajemen parkir kendaraan berbasis Raspberry Pi + Kamera ALPR. Sistem ini mendukung prediksi plat nomor otomatis, pengelolaan membership, pencatatan kendaraan ilegal, serta log riwayat parkir dan validasi pembayaran.

---

## 📦 1. Endpoint: Capture & Prediksi Plat

**POST** `/opengate/predict-plate.php`  
Mengirim gambar ke server Raspberry Pi untuk diproses oleh model ALPR.

### Request (multipart/form-data):
```bash
Body:
curl -X POST http://tkj-3b.com/tkj-3b.com/opengate/api.php \
  -F "image=@mobil.jpg" \
  -F "plate_number=B1234XYZ" \
  -F "plate_type=Plate Biasa"

**Response (200 OK):**
```json
{
  "plate_number": "B1234XYZ",
  "plate_type": "pribadi",
  "status": "new",
  "entry_time": "2025-06-06 14:12:33",
  "image_path": "http://tkj-3b.com/tkj-3b.com/opengate/uploads/B1234XYZ_20250606_141233.jpg",
  "illegal_status": {
    "is_illegal": false,
    "description": null
  },
  "membership": {
    "is_member": true,
    "owner_name": "Rudi Hartono"
  },
  "open_gate": true
}
