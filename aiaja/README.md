# Sistem Informasi Pembayaran Online dengan AI

## AI Setup (Zero-Cost via Ollama)

This project supports local AI using Ollama (free, runs fully offline on your machine).

### 1) Install Ollama (Windows)
- Download and install from `https://ollama.com/download`
- After installation, ensure the service is running (Ollama listens on `http://127.0.0.1:11434`).

### 2) Pull recommended models
Open PowerShell and run:

```bash
ollama pull llama3.1
ollama pull nomic-embed-text
```

CPU-only is fine; these are small enough for development.

### 3) Configure Laravel
Add the following to your `.env` (or leave defaults):

```env
AI_PROVIDER=openrouter
OPENROUTER_API_KEY=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
OPENROUTER_CHAT_MODEL=mistralai/mixtral-8x7b-instruct
CHATBOT_DEFAULT_LANGUAGE=auto
CHATBOT_PUBLIC_API_KEY=your_public_chatbot_key
```

Config file: `config/ai.php`.

### 4) Test AI health (requires admin + Sanctum)
GET `/api/ai/health` → should return `{ ok: true, provider: "ollama" }` when Ollama is running.

### 5) Using the AI features
- Chatbot: POST `/api/chatbot/message` with `{ message: "..." }` (auth required)
- Anomaly detection: happens automatically on `POST /api/bills/{bill}/pay`
- Scholarship recommendations batch: `php artisan ai:recommend-scholarships --notify`
- Early delay warnings: `php artisan ai:early-warnings --threshold=60 --channel=email`

Sistem pembayaran online untuk universitas yang terintegrasi dengan AI untuk deteksi transaksi ilegal, prediksi keterlambatan pembayaran, chatbot asisten, dan rekomendasi beasiswa.

## Fitur Utama

### 1. AI Deteksi Transaksi Ilegal
- Analisis pola transaksi untuk mendeteksi anomali
- Deteksi jumlah tidak wajar, login mencurigakan, IP asing
- Notifikasi real-time ke admin & user
- Blokir sementara transaksi berisiko tinggi

### 2. AI Prediksi Keterlambatan Pembayaran
- Analisis profil pengguna (nilai akademik, riwayat pembayaran, kondisi ekonomi)
- Memberikan skor risiko keterlambatan
- Notifikasi dini ke user agar tidak terlambat

### 3. AI Chatbot Asisten
- Asisten virtual untuk membantu user
- Integrasi dengan data user untuk response yang personalized

### 4. AI Rekomendasi Beasiswa
- Menganalisis profil pengguna
- Memberi rekomendasi beasiswa yang sesuai kriteria
- Integrasi dengan database beasiswa internal/eksternal

### 5. Sistem Pembayaran
- Buat & kirim tagihan otomatis (SPP, uang kegiatan, cicilan)
- Ringkasan pemasukan, tunggakan, beasiswa yang diberikan
- Laporan keuangan bulanan/tahunan (export ke Excel/PDF)
- Grafik tren pembayaran & keterlambatan
- Reminder jatuh tempo via WhatsApp/email/app notification

## Teknologi

- **Backend**: Laravel 11
- **AI**: OpenAI GPT-3.5-turbo
- **Database**: MySQL
- **Notifications**: Twilio (SMS/WhatsApp), Email
- **Reports**: Excel, PDF export
- **Authentication**: Laravel Sanctum

## Instalasi

1. Clone repository
```bash
git clone <repository-url>
cd aiaja
```

2. Install dependencies
```bash
composer install
npm install
```

3. Setup environment
```bash
cp .env.example .env
php artisan key:generate
```

4. Configure database di `.env`
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=aiaja
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

5. Configure AI services di `.env`
```env
OPENAI_API_KEY=your_openai_key
TWILIO_SID=your_twilio_sid
TWILIO_TOKEN=your_twilio_token
TWILIO_PHONE_NUMBER=your_twilio_number
TWILIO_WHATSAPP_NUMBER=your_whatsapp_number
```

6. Run migrations & seeders
```bash
php artisan migrate
php artisan db:seed
```

7. Build assets
```bash
npm run build
```

## Penggunaan

### Menjalankan Aplikasi
```bash
php artisan serve
```

### Command untuk Generate Bills
```bash
# Generate SPP bills
php artisan bills:generate --type=spp --month=2024-11

# Generate activity bills
php artisan bills:generate --type=kegiatan

# Generate installment bills
php artisan bills:generate --type=cicilan
```

### Command untuk Send Reminders
```bash
# Send email reminders 7 days before due date
php artisan reminders:send --days=7 --channel=email

# Send WhatsApp reminders 3 days before due date
php artisan reminders:send --days=3 --channel=whatsapp
```

### Command untuk Warning Admin
```bash
# Kirim ringkasan tagihan jatuh tempo/overdue ke admin via email
php artisan bills:warn-admin --days=3
```

### Command untuk AI Scholarship Recommendations
```bash
# Process recommendations for all users
php artisan ai:recommend-scholarships --notify

# Process for specific user
php artisan ai:recommend-scholarships --user_id=1 --notify
```

## API Endpoints

### Authentication
- `POST /api/login` - Login
- `POST /api/register` - Register

### Payments
- `GET /api/bills/my-bills` - Get user's bills
- `POST /api/bills/{bill}/pay` - Pay a bill

### Scholarships
- `GET /api/scholarships/recommendations` - Get recommendations
- `POST /api/scholarships/{scholarship}/apply` - Apply for scholarship

### Reports (Admin only)
- `GET /api/reports/financial-summary` - Financial summary
- `GET /api/reports/payment-trends` - Payment trends
- `GET /api/reports/export/excel` - Export to Excel
- `GET /api/reports/export/pdf` - Export to PDF

### Chatbot
- `POST /api/chatbot/message` - Send message to chatbot (auth user, optional `language=auto|id|en|both`)
- `POST /api/chatbot/public` - Send message via external integration header `X-Chatbot-Key`

## Database Schema

### Tables
- `users` - User profiles with academic info
- `transactions` - Transaction monitoring
- `scholarships` - Available scholarships
- `scholarship_recommendations` - AI recommendations
- `bills` - Automatic bills
- `payments` - Payment records
- `notifications` - System notifications
- `reminders` - Payment reminders

## Testing

```bash
php artisan test
```

## Deployment

1. Setup production environment
2. Configure web server (Apache/Nginx)
3. Setup SSL certificate
4. Configure cron jobs for automated tasks
5. Setup queue worker for notifications

## Contributing

1. Fork the repository
2. Create feature branch
3. Commit changes
4. Push to branch
5. Create Pull Request

## License

This project is licensed under the MIT License.
