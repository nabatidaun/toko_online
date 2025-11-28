# 🛒 Toko Online - E-Commerce CI4 + Midtrans

E-Commerce website built with CodeIgniter 4 and Midtrans payment gateway integration.

## ✨ Features

### Customer

- 🛍️ Product catalog with categories
- 🛒 Shopping cart
- 💳 Multiple payment methods (Midtrans)
- 📦 Order tracking
- ✅ Delivery confirmation

### Admin

- 📊 Analytics dashboard
- 📦 Order management
- 🏷️ Product management (CRUD)
- 💰 Payment status sync
- 📈 Sales reports

## 🚀 Tech Stack

- **Backend:** CodeIgniter 4
- **Database:** MySQL/MariaDB
- **Payment:** Midtrans (Sandbox & Production)
- **Frontend:** Bootstrap 4, jQuery

## 📋 Requirements

- PHP 8.1+
- MySQL 5.7+ / MariaDB 10.3+
- Composer
- Midtrans Account

## 🔧 Installation

### 1. Clone Repository

```bash
git clone https://github.com/YOUR_USERNAME/toko-online.git
cd toko-online
```

### 2. Install Dependencies

```bash
composer install
```

### 3. Setup Environment

```bash
cp .env.example .env
```

Edit `.env` file:

```ini
database.default.database = toko_online
database.default.username = root
database.default.password = your_password

midtrans.serverKey = Your-Server-Key
midtrans.clientKey = Your-Client-Key
```

### 4. Import Database

```bash
# Create database
mysql -u root -p -e "CREATE DATABASE toko_online"

# Import schema
mysql -u root -p toko_online < database/schema.sql

# Or run migrations
php spark migrate
```

### 5. Run Application

```bash
php spark serve
```

Access: `http://localhost:8080`

## 📁 Project Structure

```
toko-online/
├── app/
│   ├── Controllers/
│   │   ├── Dashboard.php
│   │   ├── Payment.php
│   │   ├── Pesanan.php
│   │   └── Admin/
│   ├── Models/
│   │   ├── Model_barang.php
│   │   ├── Model_invoice.php
│   │   ├── Model_kategori.php
│   │   └── Model_analytics.php
│   ├── Views/
│   └── Config/
│       └── Midtrans.php
├── database/
│   └── schema.sql
├── public/
│   ├── assets/
│   └── uploads/
├── .env.example
├── .gitignore
└── README.md
```

## 🔐 Security

- ✅ Never commit `.env` file
- ✅ Change Midtrans keys for production
- ✅ Enable HTTPS in production
- ✅ Update `app.baseURL` in production

## ⚡ Features Roadmap

- [ ] User authentication system
- [ ] Product review & rating
- [ ] Wishlist feature
- [ ] Coupon & discount system
- [ ] Email notifications
- [ ] Advanced analytics
- [ ] Multi-vendor support

## 🐛 Troubleshooting

**Payment not updating?**

- Check Midtrans notification URL
- Verify server key is correct
- Check logs in `writable/logs/`

**Database error?**

- Verify credentials in `.env`
- Ensure MySQL is running
- Import `schema.sql`

## 📄 License

MIT License

## 👨‍💻 Developer

Developed with Bintang

## 🙏 Credits

- CodeIgniter 4
- Midtrans Payment Gateway
- Bootstrap Framework
