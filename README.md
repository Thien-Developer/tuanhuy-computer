<p align="center">
  <img src="https://capsule-render.vercel.app/api?type=waving&color=E53935&height=200&section=header&text=Tu%E1%BA%A5n%20Huy%20Computer&fontSize=50&fontColor=FFFFFF&fontAlignY=38&animation=fadeIn" width="100%"/>
</p>

<p align="center">
  Website thương mại điện tử bán linh kiện máy tính, xây dựng bằng PHP thuần và MySQL — không dùng framework, không Composer, không npm.<br/>
  Tích hợp AI hỗ trợ nghiệp vụ, đăng nhập Google OAuth, xây dựng cấu hình PC tùy chỉnh và hệ thống quản trị phân quyền nhiều cấp.
</p>

<p align="center">
  <img src="https://img.shields.io/badge/PHP-8.1-777BB4?style=flat-square&logo=php&logoColor=white" alt="PHP"/>
  <img src="https://img.shields.io/badge/MySQL-8.0-4479A1?style=flat-square&logo=mysql&logoColor=white" alt="MySQL"/>
  <img src="https://img.shields.io/badge/License-Proprietary-lightgrey?style=flat-square" alt="License"/>
  <img src="https://img.shields.io/badge/Status-In%20Development-orange?style=flat-square" alt="Status"/>
</p>

<p align="center">
  <img src="https://skillicons.dev/icons?i=php,mysql,js,css,html,git,github,apache" alt="Tech stack"/>
</p>

---

## 📸 Demo

| Trang chủ | Đăng nhập |
|:---:|:---:|
| ![Trang chủ](assets/images/screenshot-home.png) | ![Đăng nhập](assets/images/screenshot-login.png) |

| Chi tiết sản phẩm | Trang quản trị |
|:---:|:---:|
| ![Sản phẩm](assets/images/screenshot-product.png) | ![Admin](assets/images/screenshot-admin.png) |

**Demo trực tuyến:** _(sẽ cập nhật khi có domain chính thức)_

---

## ✨ Tính năng nổi bật

|  | Tính năng | Mô tả |
|:---:|---|---|
| 🛒 | Giỏ hàng & Thanh toán | Thêm giỏ hàng, áp mã giảm giá, đặt hàng, thanh toán chuyển khoản Techcombank VietQR hoặc MoMo deeplink |
| 🔐 | Đăng nhập Google OAuth | Đăng ký / đăng nhập truyền thống hoặc qua Google OAuth 2.0 |
| 🖥️ | Xây dựng PC tùy chỉnh | Công cụ build cấu hình PC theo linh kiện, kiểm tra tương thích |
| 🤖 | Trợ lý AI | Groq API sinh tên/mô tả sản phẩm, tìm ảnh, xóa nền ảnh, phát hiện trùng lặp, chatbot hỗ trợ nghiệp vụ |
| 🛠️ | Trang quản trị | Quản lý sản phẩm, danh mục, đơn hàng, khách hàng, tồn kho, banner, thống kê doanh thu |
| 📨 | Thông báo Telegram | Bot Telegram gửi thông báo đơn hàng/sự kiện theo thời gian thực, xử lý qua cron/daemon |
| ⚡ | Tối ưu hiệu năng | Nén gzip và cache trình duyệt cấu hình qua `.htaccess` |
| 🔁 | CI/CD tự động | GitHub Actions tự động deploy lên server khi push vào `main` |

---

## 🛠️ Công nghệ sử dụng

**Backend**
- PHP 7.4+ / 8.1 — thuần, không framework
- MySQL 5.7+ / 8.0 (MariaDB tương thích)
- Apache + `mod_rewrite`

**Frontend**
- HTML5, CSS3, JavaScript thuần (Vanilla JS)
- Không dùng framework/thư viện UI ngoài

**Tích hợp**
- Groq API (`llama-3.2-11b-vision-preview`) — AI sinh nội dung & phân tích ảnh
- `@imgly/background-removal` (ESM qua esm.sh) — xóa nền ảnh phía trình duyệt
- Tìm ảnh: SerpApi → Bing → Pexels → Pixabay → Google (fallback chain)
- Google OAuth 2.0 — đăng nhập bên thứ ba
- Telegram Bot API — thông báo real-time
- SMTP Gmail — gửi email
- Techcombank VietQR, MoMo deeplink — thanh toán

**DevOps**
- GitHub Actions — CI/CD tự động deploy
- Apache trên EC2 (Ubuntu) — production hosting

---

## 🚀 Cài đặt local

### Yêu cầu

- AppServ 2.6+ hoặc XAMPP với PHP 7.4+ và MySQL
- `mod_rewrite` bật trong Apache

### Các bước

```bash
# 1. Clone vào web root
git clone https://github.com/Thien-Developer/tuanhuy-computer.git C:/AppServ/www/tuanhuy_computer

# 2. Tạo database
# Mở phpMyAdmin → tạo DB tên "mpc" → import
mysql -u root -p mpc < database/migrations.sql
```

```bash
# 3. Tạo file cấu hình môi trường
cp .env.local.example .env.local   # hoặc tạo mới (xem mẫu bên dưới)
```

```bash
# 4. Tạo thư mục cần thiết
mkdir -p uploads/products storage
```

Truy cập: `http://localhost/tuanhuy_computer`

### Biến môi trường — `.env.local` (local, không commit)

```env
APP_URL=http://localhost/tuanhuy_computer

DB_HOST=localhost
DB_NAME=mpc
DB_USER=root
DB_PASS=your_db_password

AI_API_KEY=gsk_...          # Groq API key
MAIL_USER=you@gmail.com
MAIL_PASS=xxxx xxxx xxxx xxxx   # Gmail App Password
TELEGRAM_BOT_TOKEN=123456:ABC...
TELEGRAM_ADMIN_CHAT=7329986368
```

### Hằng số toàn cục — `config/app.php`

| Hằng số | Mô tả |
|---|---|
| `APP_URL` | Base URL của site |
| `AI_API_KEY` | Groq API key |
| `TELEGRAM_BOT_TOKEN` | Token Telegram bot |
| `TELEGRAM_ADMIN_CHAT` | Chat ID nhận thông báo |
| `TELEGRAM_CRON_SECRET` | Token bảo vệ endpoint cron |
| `MAIL_USER` / `MAIL_PASS` | Gmail + App Password |
| `GOOGLE_CLIENT_ID` / `SECRET` | Google OAuth credentials |
| `SERPAPI_KEY` | Tìm ảnh qua SerpApi |
| `BING_SEARCH_KEY` | Bing Image Search |
| `REMOVEBG_KEY` | remove.bg API (fallback server-side) |
| `BANK_NO` | Số tài khoản Techcombank |
| `MOMO_NO` | Số điện thoại MoMo |

`display_errors` tự bật khi `APP_URL` chứa `localhost`, tắt trên production.

### `config/database.php`

```php
// Chỉnh trực tiếp nếu không dùng .env.local
'host'     => 'localhost',
'dbname'   => 'mpc',
'username' => 'root',
'password' => '',
```

### Tài khoản Admin mặc định (dữ liệu seed local)

| Role | Email | Mật khẩu |
|---|---|---|
| Admin | admin@tuanhuycomputer.com | `admin123` |

> **Quan trọng:** Đổi mật khẩu ngay sau khi cài đặt/deploy lần đầu tại `/admin/staff`.

---

## 📁 Cấu trúc dự án

```
tuanhuy_computer/
├── index.php               # Router chính
├── .htaccess               # Rewrite rules
├── .env.local              # Biến môi trường local (không commit)
├── config/
│   ├── app.php             # Hằng số + helper functions
│   └── database.php        # Singleton PDO
├── app/
│   ├── Controllers/        # AccountController, AdminController, ApiController, ...
│   ├── Models/
│   │   ├── Models.php      # User, Cart, Order, Category
│   │   └── ProductModel.php
│   ├── Views/
│   │   ├── admin/          # Giao diện quản trị
│   │   ├── home/           # Trang chủ
│   │   ├── products/       # Danh sách & chi tiết sản phẩm
│   │   ├── auth/           # Login / Register
│   │   ├── cart/ checkout/ account/
│   │   └── layouts/        # header.php, footer.php
│   ├── Helpers/            # AITools, TelegramBot, Mailer, ...
│   └── Middleware/
│       └── RoleGuard.php   # Phân quyền Admin/Manager/Staff
├── assets/
│   └── images/             # Logo, ảnh tĩnh
├── uploads/
│   └── products/           # Ảnh sản phẩm (không commit)
├── storage/                # Cache, log, JSON state (không commit)
├── database/
│   └── migrations.sql      # Schema đầy đủ + migrations gộp
├── deploy/                 # Scripts deploy lên server
└── scripts/                # Tiện ích Python (fix data, remove bg)
```

---

## 🧭 Routing

Mọi request đều qua `index.php` (via `.htaccess`).

```
/{controller}/{action}/{param}
```

- Kebab-case → camelCase: `cancel-order` → `cancelOrder()`
- `/products/{slug}` → `ProductController->index($slug)`
- `/api/{action}/{param}` → `ApiController->{action}($param)`
- `/admin/ai/generator` → `AdminController->ai('generator')`

---

## 🔌 API nội bộ

Tất cả endpoint dưới `/api/{action}`. Request body: JSON. Response luôn HTTP 200:

```json
{ "success": true, "...": "..." }
```

Nhóm endpoint: `auth`, `cart`, `coupon`, `review`, `ai` (generate, save-image, search-image, remove-bg, add-watermark, check-duplicate, save-product, upload-extra-images, reorder-images, update-extra-image).

---

## 🖥️ Các trang Admin

| URL | Chức năng |
|---|---|
| `/admin` | Dashboard — doanh thu, đơn hàng mới, sản phẩm bán chạy, biểu đồ |
| `/admin/products` | Danh sách sản phẩm — tìm kiếm, lọc theo danh mục/trạng thái |
| `/admin/products/create` | Thêm sản phẩm mới (form + AI hỗ trợ) |
| `/admin/categories` | Quản lý danh mục, thêm/sửa/xóa |
| `/admin/orders` | Danh sách đơn hàng, lọc theo trạng thái |
| `/admin/orders/view/{id}` | Chi tiết đơn hàng, cập nhật trạng thái, in PDF |
| `/admin/customers` | Danh sách khách hàng, thống kê chi tiêu, lọc theo trạng thái |
| `/admin/inventory` | Quản lý tồn kho — nhập hàng, điều chỉnh số lượng |
| `/admin/staff` | Quản lý nhân sự (Admin only) — tạo/sửa/khóa tài khoản |
| `/admin/stats` | Thống kê doanh thu theo ngày/tháng/năm, top sản phẩm |
| `/admin/logs` | Audit log — lịch sử thao tác toàn bộ hệ thống |
| `/admin/ai/generator` | AI Generator — tạo tên/mô tả sản phẩm, tìm ảnh, xóa nền |
| `/admin/ai/assistant` | AI Assistant — chatbot hỗ trợ nghiệp vụ |
| `/admin/ai/report` | Báo cáo AI — phân tích sản phẩm, đề xuất giá |
| `/admin/assets` | Quản lý banner trang chủ và ảnh showcase |
| `/admin/telegram-bot` | Cấu hình Telegram bot, test gửi thông báo |

---

## 🔐 Phân quyền

| Role | Giá trị | Quyền chi tiết |
|---|---|---|
| Admin | 1 | Toàn quyền — bao gồm xóa, quản lý nhân sự, xem logs |
| Manager | 2 | Tạo + sửa tất cả (sản phẩm, đơn hàng, danh mục, tồn kho); **không xóa** |
| Staff | 3 | Chỉ tạo/sửa **sản phẩm** trong vòng **15 phút** sau khi tạo |

**Bảng so sánh chi tiết:**

| Chức năng | Admin | Manager | Staff |
|---|:---:|:---:|:---:|
| Xem dashboard | ✓ | ✓ | ✓ |
| Thêm sản phẩm | ✓ | ✓ | ✓ |
| Sửa sản phẩm | ✓ | ✓ | ✓ (15 phút) |
| Xóa sản phẩm | ✓ | — | — |
| Quản lý đơn hàng | ✓ | ✓ | — |
| Quản lý khách hàng | ✓ | ✓ | — |
| Quản lý danh mục | ✓ | ✓ | — |
| Quản lý tồn kho | ✓ | ✓ | — |
| Xem thống kê | ✓ | ✓ | — |
| Xem audit logs | ✓ | — | — |
| Quản lý nhân sự | ✓ | — | — |
| AI Generator | ✓ | ✓ | ✓ |

### Tạo tài khoản Manager / Staff

Chỉ Admin mới thực hiện được:

1. Vào `/admin/staff`
2. Nhấn **Thêm nhân sự**
3. Điền họ tên, email, số điện thoại, mật khẩu (tối thiểu 6 ký tự)
4. Chọn **Vai trò**: Manager hoặc Staff
5. Nhấn **Lưu**

Để khóa / mở khóa tài khoản: nhấn nút toggle trên danh sách nhân sự.
Để đặt lại mật khẩu: nhấn **Reset mật khẩu** trong form sửa.

> Không thể thay đổi vai trò hoặc tự khóa tài khoản đang đăng nhập.

---

## 🛡️ Bảo mật

- Luôn dùng parameterized queries — không bao giờ nội suy input vào SQL
- `config/`, `deploy/`, `database/`, `storage/` bị chặn bởi `.htaccess` trên production
- File cấu hình chứa thông tin nhạy cảm (`.env.local`, khóa SSH...) nằm trong `.gitignore`, không commit lên repo
- `display_errors` tắt hoàn toàn trên production
- Mật khẩu hash bằng `password_hash()` / `password_verify()`
- Session name tùy chỉnh: `TH_SESS`
- API keys nhạy cảm nên đặt qua biến môi trường, không hardcode trong code

---

## 👤 Tác giả

**Thiên** — [github.com/Thien-Developer](https://github.com/Thien-Developer)

---

<p align="center">
  <img src="https://capsule-render.vercel.app/api?type=waving&color=E53935&height=120&section=footer&animation=fadeIn" width="100%"/>
</p>
