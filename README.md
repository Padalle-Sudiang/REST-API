# 🚗 REST API - ALPR Gate System

Sistem REST API untuk manajemen parkir kendaraan berbasis Raspberry Pi + Kamera ALPR. Sistem ini mendukung prediksi plat nomor otomatis, pengelolaan membership, pencatatan kendaraan ilegal, serta log riwayat parkir dan validasi pembayaran.

---

## 📦 1. Endpoint: Capture & Prediksi Plat

**POST** `/api/predict-plate`  
Mengirim gambar ke server Raspberry Pi untuk diproses oleh model ALPR.

### Request (multipart/form-data):
```http
POST /api/predict-plate
Content-Type: multipart/form-data

Body:
- image: (file) gambar plat kendaraan hasil capture

