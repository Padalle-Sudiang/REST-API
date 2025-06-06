# 🚗 REST API - ALPR Gate System

Sistem REST API untuk manajemen parkir kendaraan berbasis Raspberry Pi + Kamera ALPR. Sistem ini mendukung prediksi plat nomor otomatis, pengelolaan membership, pencatatan kendaraan ilegal, serta log riwayat parkir dan validasi pembayaran.

---

## 📦 1. Endpoint: Capture & Prediksi Plat

**POST** `/opengate/predict-plate.php`  
Mengirim gambar ke server Raspberry Pi untuk diproses oleh model ALPR.

### Request (multipart/form-data):
```http
POST http://tkj-3b.com/tkj-3b.com/opengate/predict-plate.php
Content-Type: multipart/form-data

Body:
- image: (file) gambar plat kendaraan hasil capture
