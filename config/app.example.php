<?php
// config/app.php — copy file này thành config/app.php và điền thông tin thật
// KHÔNG commit config/app.php lên git (đã có trong .gitignore)

// ── Load .env.local (local development overrides) ─────────────────────────────
$_envFile = __DIR__ . '/../.env.local';
if (file_exists($_envFile)) {
    foreach (file($_envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $_envLine) {
        if ($_envLine === '' || $_envLine[0] === '#' || strpos($_envLine, '=') === false) continue;
        list($_envK, $_envV) = explode('=', $_envLine, 2);
        $_envK = trim($_envK); $_envV = trim($_envV);
        if ($_envK !== '' && getenv($_envK) === false) putenv("$_envK=$_envV");
    }
    unset($_envFile, $_envLine, $_envK, $_envV);
}

define('APP_URL',        getenv('APP_URL') ?: 'http://localhost/tuanhuy_computer');
define('APP_NAME',       'Tuấn Huy Computer');
define('UPLOAD_PATH',    __DIR__ . '/../uploads/products/');
define('UPLOAD_URL',     APP_URL . '/uploads/products/');
define('ITEMS_PER_PAGE', 12);

// ── AI / Image APIs ───────────────────────────────────────────────────────────
// Groq: console.groq.com → API Keys
define('AI_API_KEY',        getenv('AI_API_KEY')        ?: 'your-groq-api-key');
// Anthropic (nếu dùng): console.anthropic.com
define('ANTHROPIC_API_KEY', getenv('ANTHROPIC_API_KEY') ?: '');
define('AI_ACCOUNT_ID',   '');
define('AI_MODEL', 'llama-3.2-11b-vision-preview');

// Bing Image Search: portal.azure.com → Cognitive Services → Bing Search v7
define('BING_SEARCH_KEY',    getenv('BING_SEARCH_KEY')    ?: 'your-bing-search-key');
// SerpApi: serpapi.com → Dashboard → API Key
define('SERPAPI_KEY',        getenv('SERPAPI_KEY')        ?: 'your-serpapi-key');
// remove.bg: remove.bg/dashboard → API Keys
define('REMOVEBG_KEY',       getenv('REMOVEBG_KEY')       ?: 'your-removebg-key');
// Pixabay: pixabay.com/api/docs → API key
define('PIXABAY_KEY',        getenv('PIXABAY_KEY')        ?: 'your-pixabay-key');
// Pexels: pexels.com/api → Your API Key
define('PEXELS_KEY',         getenv('PEXELS_KEY')         ?: 'your-pexels-key');
// Google Custom Search: console.cloud.google.com → Custom Search API
define('GOOGLE_SEARCH_KEY',  getenv('GOOGLE_SEARCH_KEY')  ?: 'your-google-api-key');
define('GOOGLE_SEARCH_CX',   getenv('GOOGLE_SEARCH_CX')   ?: 'your-search-engine-cx');

// ── Google OAuth ──────────────────────────────────────────────────────────────
// Tạo tại: console.cloud.google.com → APIs & Services → Credentials → OAuth 2.0
// Authorized redirect URI: APP_URL . '/auth/google-callback'
define('GOOGLE_CLIENT_ID',     getenv('GOOGLE_CLIENT_ID')     ?: 'your-google-client-id');
define('GOOGLE_CLIENT_SECRET', getenv('GOOGLE_CLIENT_SECRET') ?: 'your-google-client-secret');

// ── Payment account info ──────────────────────────────────────────────────────
define('BANK_NAME',    'Techcombank');
define('BANK_BIN',     'TCB');
define('BANK_NO',      getenv('BANK_NO')  ?: 'your-bank-account-number');
define('BANK_ACCOUNT', 'YOUR STORE NAME');
define('MOMO_NO',      getenv('MOMO_NO')  ?: 'your-momo-phone-number');
define('MOMO_ACCOUNT', 'Your Store Name');

// ── Zalo OA ───────────────────────────────────────────────────────────────────
// Hướng dẫn: oa.zalo.me → Cài đặt → Tích hợp API → tạo access token
define('ZALO_OA_TOKEN',  '');   // Access token OA (hết hạn sau 90 ngày, cần renew)
define('ZALO_ADMIN_ID',  '');   // Zalo User ID của Admin
define('ZALO_ADMIN_PHONE','');  // SĐT admin

// ── Telegram Bot ──────────────────────────────────────────────────────────────
// Hướng dẫn: nhắn @BotFather trên Telegram → /newbot → lấy token
// Chat ID: nhắn tin cho bot rồi truy cập https://api.telegram.org/bot{TOKEN}/getUpdates
define('TELEGRAM_BOT_TOKEN',    getenv('TELEGRAM_BOT_TOKEN')    ?: 'your-telegram-bot-token');
define('TELEGRAM_ADMIN_CHAT',   getenv('TELEGRAM_ADMIN_CHAT')   ?: 'your-admin-chat-id');
define('TELEGRAM_CRON_SECRET',  getenv('TELEGRAM_CRON_SECRET')  ?: 'your-cron-secret');

// ── Email (SMTP) ──────────────────────────────────────────────────────────────
// Dùng Gmail App Password: myaccount.google.com → Security → App passwords
define('MAIL_HOST',      'smtp.gmail.com');
define('MAIL_PORT',      587);
define('MAIL_USER',      getenv('MAIL_USER') ?: 'your-email@gmail.com');
define('MAIL_PASS',      getenv('MAIL_PASS') ?: 'your-gmail-app-password');
define('MAIL_FROM',      getenv('MAIL_FROM') ?: 'your-email@gmail.com');
define('MAIL_FROM_NAME', APP_NAME);

if (session_status() === PHP_SESSION_NONE) {
    session_name('TH_SESS');
    session_start();
}
$_appIsDev = defined('APP_URL') && strpos(APP_URL, 'localhost') !== false;
ini_set('display_errors', $_appIsDev ? 1 : 0);
error_reporting($_appIsDev ? E_ALL : 0);
unset($_appIsDev);

function formatPrice($price) {
    return number_format((float)$price, 0, ',', '.') . 'đ';
}
function setFlash($type, $msg) {
    $_SESSION['flash'] = array('type' => $type, 'msg' => $msg);
}
function getFlash() {
    if (isset($_SESSION['flash'])) {
        $f = $_SESSION['flash']; unset($_SESSION['flash']); return $f;
    }
    return null;
}
function isLoggedIn()  { return isset($_SESSION['user_id']); }
function isAdmin()     { return isset($_SESSION['user_role']) && (int)$_SESSION['user_role'] === 1; }
function isStaff()     { return isset($_SESSION['user_role']) && in_array((int)$_SESSION['user_role'], [1,2,3]); }
function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: ' . APP_URL . '/auth/login'); exit;
    }
}
function requireAdmin() {
    requireLogin();
    if (!isStaff()) { header('Location: ' . APP_URL . '/'); exit; }
}
function sanitize($str) {
    return htmlspecialchars(trim((string)$str), ENT_QUOTES, 'UTF-8');
}
function makeSlug($str) {
    $str = mb_strtolower($str, 'UTF-8');
    $map = array(
        'à'=>'a','á'=>'a','ả'=>'a','ã'=>'a','ạ'=>'a','ă'=>'a','ắ'=>'a','ặ'=>'a',
        'ằ'=>'a','ẳ'=>'a','ẵ'=>'a','â'=>'a','ấ'=>'a','ầ'=>'a','ẩ'=>'a','ẫ'=>'a',
        'ậ'=>'a','đ'=>'d','è'=>'e','é'=>'e','ẻ'=>'e','ẽ'=>'e','ẹ'=>'e','ê'=>'e',
        'ế'=>'e','ề'=>'e','ể'=>'e','ễ'=>'e','ệ'=>'e','ì'=>'i','í'=>'i','ỉ'=>'i',
        'ĩ'=>'i','ị'=>'i','ò'=>'o','ó'=>'o','ỏ'=>'o','õ'=>'o','ọ'=>'o','ô'=>'o',
        'ố'=>'o','ồ'=>'o','ổ'=>'o','ỗ'=>'o','ộ'=>'o','ơ'=>'o','ớ'=>'o','ờ'=>'o',
        'ở'=>'o','ỡ'=>'o','ợ'=>'o','ù'=>'u','ú'=>'u','ủ'=>'u','ũ'=>'u','ụ'=>'u',
        'ư'=>'u','ứ'=>'u','ừ'=>'u','ử'=>'u','ữ'=>'u','ự'=>'u','ỳ'=>'y','ý'=>'y',
        'ỷ'=>'y','ỹ'=>'y','ỵ'=>'y',
    );
    $str = strtr($str, $map);
    $str = preg_replace('/[^a-z0-9\s-]/', '', $str);
    $str = preg_replace('/[\s-]+/', '-', $str);
    return trim($str, '-');
}
function getCartCount() {
    $db = Database::getInstance();
    if (isLoggedIn()) {
        $row = $db->fetch("SELECT SUM(quantity) as cnt FROM cart WHERE user_id=?",
            array($_SESSION['user_id']));
    } else {
        $row = $db->fetch("SELECT SUM(quantity) as cnt FROM cart WHERE session_id=?",
            array(session_id()));
    }
    return (int)(isset($row['cnt']) ? $row['cnt'] : 0);
}
function calcCartSubtotal($items) {
    $total = 0;
    foreach ($items as $i) $total += (float)$i['unit_price'] * (int)$i['quantity'];
    return $total;
}

/**
 * Resize and compress an image using GD.
 */
function compressImage($srcPath, $destPath = null, $maxDim = 1200, $quality = 85) {
    if (!function_exists('imagecreatefromjpeg') || !file_exists($srcPath)) return false;
    $info = @getimagesize($srcPath);
    if (!$info) return false;
    $w = $info[0]; $h = $info[1]; $type = $info[2];
    $out = $destPath ?: $srcPath;
    if ($w <= $maxDim && $h <= $maxDim) {
        if ($out !== $srcPath) copy($srcPath, $out);
        return true;
    }
    switch ($type) {
        case IMAGETYPE_JPEG: $src = @imagecreatefromjpeg($srcPath); break;
        case IMAGETYPE_PNG:  $src = @imagecreatefrompng($srcPath);  break;
        case IMAGETYPE_WEBP: $src = function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($srcPath) : false; break;
        default: return false;
    }
    if (!$src) return false;
    $ratio = min($maxDim / $w, $maxDim / $h);
    $nw = (int)round($w * $ratio);
    $nh = (int)round($h * $ratio);
    $dst = imagecreatetruecolor($nw, $nh);
    if ($type === IMAGETYPE_PNG) {
        imagealphablending($dst, false);
        imagesavealpha($dst, true);
        $transparent = imagecolorallocatealpha($dst, 0, 0, 0, 127);
        imagefilledrectangle($dst, 0, 0, $nw, $nh, $transparent);
    }
    imagecopyresampled($dst, $src, 0, 0, 0, 0, $nw, $nh, $w, $h);
    imagedestroy($src);
    switch ($type) {
        case IMAGETYPE_JPEG: $ok = imagejpeg($dst, $out, $quality); break;
        case IMAGETYPE_PNG:  $ok = imagepng($dst, $out, (int)round((100 - $quality) / 10)); break;
        case IMAGETYPE_WEBP: $ok = function_exists('imagewebp') ? imagewebp($dst, $out, $quality) : false; break;
        default: $ok = false;
    }
    imagedestroy($dst);
    return (bool)$ok;
}
