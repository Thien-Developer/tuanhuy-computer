<?php
/**
 * MailService — Simple SMTP mailer (Gmail STARTTLS, no Composer)
 * All strings use direct UTF-8; body is base64-encoded before sending.
 */
class MailService {

    /* ── SMTP core ──────────────────────────────────────────────────── */
    private static function smtp($to, $subject, $htmlBody) {
        $host     = defined('MAIL_HOST') ? MAIL_HOST : '';
        $port     = defined('MAIL_PORT') ? (int)MAIL_PORT : 587;
        $user     = defined('MAIL_USER') ? MAIL_USER : '';
        $pass     = defined('MAIL_PASS') ? MAIL_PASS : '';
        $from     = defined('MAIL_FROM') ? MAIL_FROM : $user;
        $fromName = defined('MAIL_FROM_NAME') ? MAIL_FROM_NAME : APP_NAME;
        if (!$host || !$user || !$pass) return false;

        $errno = 0; $errstr = '';
        $sock = @fsockopen($host, $port, $errno, $errstr, 10);
        if (!$sock) return false;

        $read = function() use ($sock) {
            $out = '';
            while (!feof($sock)) {
                $line = fgets($sock, 512);
                $out .= $line;
                if (strlen($line) >= 4 && substr($line, 3, 1) === ' ') break;
            }
            return $out;
        };
        $cmd = function($c) use ($sock, $read) {
            fputs($sock, $c . "\r\n");
            return $read();
        };

        $read();
        $cmd("EHLO localhost");           // SMTP protocol — must stay "localhost"
        $cmd("STARTTLS");
        stream_socket_enable_crypto($sock, true, STREAM_CRYPTO_METHOD_TLS_CLIENT);
        $cmd("EHLO localhost");
        $cmd("AUTH LOGIN");
        $cmd(base64_encode($user));
        $r = $cmd(base64_encode($pass));
        if (strpos($r, '235') === false) { fclose($sock); return false; }

        $cmd("MAIL FROM:<{$from}>");
        $cmd("RCPT TO:<{$to}>");
        $cmd("DATA");

        $boundary = md5(uniqid());
        $headers  = "From: =?UTF-8?B?" . base64_encode($fromName) . "?= <{$from}>\r\n"
                  . "To: {$to}\r\n"
                  . "Subject: =?UTF-8?B?" . base64_encode($subject) . "?=\r\n"
                  . "MIME-Version: 1.0\r\n"
                  . "Content-Type: multipart/alternative; boundary=\"{$boundary}\"\r\n"
                  . "Date: " . date('r') . "\r\n";

        $plain = strip_tags(str_replace(['<br>', '<br/>', '</p>', '</div>', '</tr>'], "\n", $htmlBody));
        $body  = "--{$boundary}\r\n"
               . "Content-Type: text/plain; charset=UTF-8\r\n"
               . "Content-Transfer-Encoding: base64\r\n\r\n"
               . chunk_split(base64_encode($plain)) . "\r\n"
               . "--{$boundary}\r\n"
               . "Content-Type: text/html; charset=UTF-8\r\n"
               . "Content-Transfer-Encoding: base64\r\n\r\n"
               . chunk_split(base64_encode($htmlBody)) . "\r\n"
               . "--{$boundary}--\r\n";

        fputs($sock, $headers . "\r\n" . $body . "\r\n.\r\n");
        $r2 = $read();
        $cmd("QUIT");
        fclose($sock);
        return strpos($r2, '250') !== false;
    }

    /* ── Xác nhận đặt hàng ─────────────────────────────────────────── */
    public static function sendOrderConfirmation($order) {
        $to = isset($order['email']) ? $order['email'] : '';
        if (!$to || !filter_var($to, FILTER_VALIDATE_EMAIL)) return false;

        $appUrl   = APP_URL;
        $code     = htmlspecialchars($order['order_code']);
        $name     = htmlspecialchars($order['fullname'] ?: 'bạn');
        $phone    = htmlspecialchars($order['phone'] ?? '');
        $addrParts = [$order['address'] ?? '', $order['district'] ?? '', $order['city'] ?? ''];
        $address  = htmlspecialchars(trim(implode(', ', array_filter($addrParts))));
        $payMap   = [
            'cod'    => 'COD - Thu tiền khi giao',
            'bank'   => 'Chuyển khoản ngân hàng',
            'momo'   => 'MoMo',
            'vnpay'  => 'VNPay',
        ];
        $payKey   = strtolower($order['payment_method'] ?? '');
        $payLabel = $payMap[$payKey] ?? strtoupper($order['payment_method'] ?? '');
        $total    = number_format((float)($order['total'] ?? 0), 0, ',', '.');
        $date     = date('d/m/Y H:i', strtotime($order['created_at'] ?? 'now'));
        $shipFee  = ($order['shipping_fee'] ?? 0) > 0
            ? number_format((float)$order['shipping_fee'], 0, ',', '.') . 'đ'
            : '<span style="color:#22c55e;font-weight:700">Miễn phí</span>';

        // Build items rows
        $uploadUrl = rtrim(defined('UPLOAD_URL') ? UPLOAD_URL : ($appUrl . '/uploads/products/'), '/') . '/';
        $itemsHtml = '';
        foreach ((array)($order['items'] ?? []) as $it) {
            $pname   = htmlspecialchars($it['product_name'] ?? '');
            $qty     = (int)($it['quantity'] ?? 1);
            $price   = number_format((float)($it['price'] ?? 0), 0, ',', '.');
            $sub     = number_format((float)($it['subtotal'] ?? 0), 0, ',', '.');
            $imgFile = trim($it['image'] ?? '');
            $imgSrc  = ($imgFile !== '' && $imgFile !== 'default.jpg')
                ? $uploadUrl . rawurlencode($imgFile)
                : '';
            $imgCell = $imgSrc
                ? '<img src="' . $imgSrc . '" width="52" height="52"'
                  . ' style="width:52px;height:52px;border-radius:7px;border:1px solid #eee;display:block;object-fit:cover" alt="">'
                : '<div style="width:52px;height:52px;background:#f3f4f6;border-radius:7px;border:1px solid #eee;'
                  . 'text-align:center;font-size:22px;line-height:52px">💻</div>';
            $itemsHtml .=
                '<tr>'
                . '<td style="padding:10px 12px;border-bottom:1px solid #f3f4f6;vertical-align:middle">'
                .   '<table cellpadding="0" cellspacing="0" border="0"><tr>'
                .     '<td style="padding-right:10px;vertical-align:middle">' . $imgCell . '</td>'
                .     '<td style="vertical-align:middle">'
                .       '<div style="font-size:13px;font-weight:600;color:#222;line-height:1.4;max-width:190px">'
                .         $pname
                .       '</div>'
                .     '</td>'
                .   '</tr></table>'
                . '</td>'
                . '<td style="padding:10px 12px;border-bottom:1px solid #f3f4f6;text-align:right;'
                .   'font-size:12px;color:#888;white-space:nowrap;vertical-align:middle">' . $price . 'đ</td>'
                . '<td style="padding:10px 12px;border-bottom:1px solid #f3f4f6;text-align:center;'
                .   'font-size:13px;font-weight:600;color:#444;vertical-align:middle">x' . $qty . '</td>'
                . '<td style="padding:10px 12px;border-bottom:1px solid #f3f4f6;text-align:right;'
                .   'font-size:13px;font-weight:700;color:#e30000;white-space:nowrap;vertical-align:middle">'
                .   $sub . 'đ'
                . '</td>'
                . '</tr>';
        }

        $body =
            '<p style="margin:0 0 6px;font-size:15px;color:#333">'
            .   'Xin chào <strong>' . $name . '</strong>,'
            . '</p>'
            . '<p style="margin:0 0 20px;font-size:14px;color:#555">'
            .   'Cảm ơn bạn đã đặt hàng tại <strong style="color:#e30000">Tuấn Huy Computer</strong>!'
            .   ' Đơn hàng đang chờ xác nhận.'
            . '</p>'
            . '<div style="background:#fff7f7;border:1px solid #ffd5d5;border-radius:10px;padding:14px 16px;margin-bottom:20px">'
            .   '<table width="100%" cellpadding="0" cellspacing="0">'
            .     '<tr><td style="font-size:13px;color:#888;width:130px">Mã đơn hàng</td>'
            .         '<td style="font-size:14px;font-weight:800;color:#e30000">#' . $code . '</td></tr>'
            .     '<tr><td style="font-size:13px;color:#888;padding-top:5px">Ngày đặt</td>'
            .         '<td style="font-size:13px;color:#333;padding-top:5px">' . $date . '</td></tr>'
            .     '<tr><td style="font-size:13px;color:#888;padding-top:5px">Điện thoại</td>'
            .         '<td style="font-size:13px;color:#333;padding-top:5px">' . $phone . '</td></tr>'
            .     ($address
                ? '<tr><td style="font-size:13px;color:#888;padding-top:5px">Địa chỉ</td>'
                .      '<td style="font-size:13px;color:#333;padding-top:5px">' . $address . '</td></tr>'
                : '')
            .     '<tr><td style="font-size:13px;color:#888;padding-top:5px">Thanh toán</td>'
            .         '<td style="font-size:13px;color:#333;padding-top:5px">' . $payLabel . '</td></tr>'
            .   '</table>'
            . '</div>'
            . '<h3 style="font-size:13px;font-weight:700;color:#333;margin:0 0 8px;'
            .   'text-transform:uppercase;letter-spacing:.5px">Sản phẩm đặt hàng</h3>'
            . '<table width="100%" cellpadding="0" cellspacing="0"'
            .   ' style="border:1px solid #eee;border-radius:8px;overflow:hidden;margin-bottom:20px">'
            .   '<thead><tr style="background:#f8f9fa">'
            .     '<th style="padding:10px 12px;font-size:11px;text-align:left;color:#888;'
            .       'font-weight:700;text-transform:uppercase;letter-spacing:.5px">Sản phẩm</th>'
            .     '<th style="padding:10px 12px;font-size:11px;text-align:right;color:#888;'
            .       'font-weight:700;text-transform:uppercase;letter-spacing:.5px">Đơn giá</th>'
            .     '<th style="padding:10px 12px;font-size:11px;text-align:center;color:#888;'
            .       'font-weight:700;text-transform:uppercase;letter-spacing:.5px">SL</th>'
            .     '<th style="padding:10px 12px;font-size:11px;text-align:right;color:#888;'
            .       'font-weight:700;text-transform:uppercase;letter-spacing:.5px">Thành tiền</th>'
            .   '</tr></thead>'
            .   '<tbody>' . $itemsHtml . '</tbody>'
            .   '<tfoot>'
            .     '<tr><td colspan="3" style="padding:8px 12px;font-size:13px;color:#888;text-align:right">'
            .       'Phí vận chuyển:</td>'
            .       '<td style="padding:8px 12px;font-size:13px;text-align:right">' . $shipFee . '</td></tr>'
            .     '<tr style="background:#fff7f7">'
            .       '<td colspan="3" style="padding:10px 12px;font-size:14px;font-weight:800;'
            .         'text-align:right;color:#333">Tổng cộng:</td>'
            .       '<td style="padding:10px 12px;font-size:15px;font-weight:900;'
            .         'text-align:right;color:#e30000">' . $total . 'đ</td>'
            .     '</tr>'
            .   '</tfoot>'
            . '</table>'
            . '<p style="font-size:13px;color:#555;margin-bottom:10px">'
            .   'Chúng tôi sẽ liên hệ xác nhận trong thời gian sớm nhất.'
            . '</p>'
            . '<a href="' . $appUrl . '/account/orders"'
            .   ' style="display:inline-block;background:#e30000;color:#fff;text-decoration:none;'
            .   'padding:10px 22px;border-radius:8px;font-size:13px;font-weight:700">'
            .   'Xem đơn hàng của tôi'
            . '</a>';

        $html = self::wrapEmail('Xác nhận đặt hàng #' . $code, $body);
        return self::smtp($to, 'Xác nhận đơn hàng #' . $code . ' - Tuấn Huy Computer', $html);
    }

    /* ── Gửi OTP đặt lại / đổi mật khẩu ──────────────────────────── */
    public static function sendOtp($to, $name, $otp, $type = 'forgot') {
        if (!$to || !filter_var($to, FILTER_VALIDATE_EMAIL)) return false;
        $name    = htmlspecialchars($name ?: 'bạn');
        $typeStr = $type === 'change' ? 'đổi mật khẩu' : 'đặt lại mật khẩu';
        $body =
            '<p style="margin:0 0 8px;font-size:15px;color:#333">'
            .   'Xin chào <strong>' . $name . '</strong>,'
            . '</p>'
            . '<p style="margin:0 0 20px;font-size:14px;color:#555">'
            .   'Bạn vừa yêu cầu <strong>' . $typeStr . '</strong>'
            .   ' tại <strong style="color:#e30000">Tuấn Huy Computer</strong>.'
            . '</p>'
            . '<div style="background:#fff7f7;border:2px dashed #e30000;border-radius:12px;'
            .   'padding:24px;text-align:center;margin-bottom:24px">'
            .   '<div style="font-size:12px;color:#888;margin-bottom:8px;'
            .     'text-transform:uppercase;letter-spacing:1px">Mã xác thực OTP</div>'
            .   '<div style="font-size:42px;font-weight:900;color:#e30000;'
            .     'letter-spacing:10px;font-family:monospace">' . htmlspecialchars($otp) . '</div>'
            .   '<div style="font-size:12px;color:#999;margin-top:10px">'
            .     'Mã có hiệu lực trong <strong>10 phút</strong>'
            .   '</div>'
            . '</div>'
            . '<p style="font-size:13px;color:#888;margin:0">'
            .   'Nếu bạn không yêu cầu điều này, hãy bỏ qua email này.'
            .   ' Tài khoản của bạn vẫn an toàn.'
            . '</p>';
        $html = self::wrapEmail('Mã OTP ' . $typeStr, $body);
        return self::smtp($to, 'Mã OTP ' . $typeStr . ' - Tuấn Huy Computer', $html);
    }

    /* ── Cập nhật trạng thái đơn hàng ─────────────────────────────── */
    public static function sendOrderStatusUpdate($order, $newStatus) {
        $to = isset($order['email']) ? $order['email'] : '';
        if (!$to || !filter_var($to, FILTER_VALIDATE_EMAIL)) return false;

        $appUrl = APP_URL;
        $code   = htmlspecialchars($order['order_code']);
        $name   = htmlspecialchars($order['fullname'] ?: 'bạn');

        $statusInfo = [
            'confirmed'  => ['Đơn hàng đã được xác nhận',   '#1e40af', '#dbeafe',
                             'Đơn hàng của bạn đã được xác nhận và đang được chuẩn bị.'],
            'processing' => ['Đơn hàng đang được xử lý',     '#9a3412', '#fde8d8',
                             'Đơn hàng của bạn đang trong quá trình đóng gói.'],
            'shipping'   => ['Đơn hàng đang được giao',       '#075985', '#e0f2fe',
                             'Đơn hàng của bạn đang trên đường giao đến bạn.'],
            'delivered'  => ['Đơn hàng đã giao thành công',  '#166534', '#dcfce7',
                             'Đơn hàng đã giao thành công. Cảm ơn bạn đã mua hàng!'],
            'cancelled'  => ['Đơn hàng đã bị hủy',            '#991b1b', '#fee2e2',
                             'Đơn hàng của bạn đã bị hủy. Vui lòng liên hệ nếu có thắc mắc.'],
        ];

        if (!isset($statusInfo[$newStatus])) return false;
        [$title, $color, $bgColor, $desc] = $statusInfo[$newStatus];

        $delivHtml = '';
        if ($newStatus === 'confirmed' || $newStatus === 'processing') {
            $ts = time(); $added = 0;
            while ($added < 3) { $ts += 86400; if ((int)date('N', $ts) < 6) $added++; }
            $delivDate = date('d/m/Y', $ts);
            $delivHtml =
                '<div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:10px;'
                . 'padding:14px 16px;margin:18px 0">'
                . '<div style="font-size:13px;font-weight:700;color:#166534;margin-bottom:4px">'
                .   '🚚 Thời gian giao hàng dự kiến'
                . '</div>'
                . '<div style="font-size:16px;font-weight:900;color:#15803d">' . $delivDate . '</div>'
                . '<div style="font-size:12px;color:#4ade80;margin-top:3px">'
                .   '(trong vòng 3 ngày làm việc)'
                . '</div>'
                . '</div>';
        }

        $body =
            '<p style="margin:0 0 16px;font-size:15px;color:#333">'
            .   'Xin chào <strong>' . $name . '</strong>,'
            . '</p>'
            . '<div style="background:' . $bgColor . ';border:1px solid ' . $bgColor . ';'
            .   'border-radius:10px;padding:16px;margin-bottom:16px;text-align:center">'
            .   '<div style="font-size:13px;font-weight:700;color:' . $color . '">' . $title . '</div>'
            .   '<div style="font-size:22px;font-weight:900;color:' . $color . ';margin:6px 0">#' . $code . '</div>'
            .   '<div style="font-size:13px;color:#555">' . $desc . '</div>'
            . '</div>'
            . $delivHtml
            . '<a href="' . $appUrl . '/account/orders"'
            .   ' style="display:inline-block;background:#e30000;color:#fff;text-decoration:none;'
            .   'padding:10px 22px;border-radius:8px;font-size:13px;font-weight:700;margin-top:6px">'
            .   'Xem chi tiết đơn hàng'
            . '</a>';

        $html = self::wrapEmail($title . ' #' . $code, $body);
        return self::smtp($to, $title . ' #' . $code . ' - Tuấn Huy Computer', $html);
    }

    /* ── HTML wrapper chung ────────────────────────────────────────── */
    private static function wrapEmail($title, $body) {
        $appName = defined('APP_NAME') ? APP_NAME : 'Tuấn Huy Computer';
        $appUrl  = APP_URL;
        $year    = date('Y');
        return '<!DOCTYPE html>'
            . '<html lang="vi">'
            . '<head>'
            .   '<meta http-equiv="Content-Type" content="text/html; charset=utf-8">'
            .   '<meta charset="UTF-8">'
            .   '<meta name="viewport" content="width=device-width,initial-scale=1">'
            .   '<title>' . strip_tags($title) . '</title>'
            . '</head>'
            . '<body style="margin:0;padding:0;background:#f3f4f6;font-family:Arial,sans-serif">'
            . '<table width="100%" cellpadding="0" cellspacing="0"'
            .   ' style="background:#f3f4f6;padding:30px 0">'
            .   '<tr><td align="center">'
            .     '<table width="600" cellpadding="0" cellspacing="0"'
            .       ' style="max-width:600px;width:100%">'
            .       '<tr><td style="background:linear-gradient(135deg,#e30000,#b00000);'
            .         'border-radius:14px 14px 0 0;padding:22px 28px;text-align:center">'
            .         '<div style="font-size:22px;margin-bottom:4px">💻</div>'
            .         '<div style="color:#fff;font-weight:900;font-size:17px;letter-spacing:.5px">'
            .           'TUẤN HUY COMPUTER'
            .         '</div>'
            .         '<div style="color:rgba(255,255,255,.7);font-size:11px;'
            .           'letter-spacing:1px;margin-top:2px">'
            .           'TIN HỌC - LINH KIỆN MÁY TÍNH'
            .         '</div>'
            .       '</td></tr>'
            .       '<tr><td style="background:#fff;padding:28px 32px">'
            .         '<h2 style="margin:0 0 18px;font-size:15px;font-weight:800;color:#111;'
            .           'border-bottom:2px solid #f3f4f6;padding-bottom:12px">' . $title . '</h2>'
            .         $body
            .       '</td></tr>'
            .       '<tr><td style="background:#f8f9fa;border-radius:0 0 14px 14px;'
            .         'padding:16px 28px;text-align:center">'
            .         '<p style="margin:0 0 4px;font-size:12px;color:#999">'
            .           'Tuấn Huy Computer &mdash; Uy tín, chất lượng, giá tốt'
            .         '</p>'
            .         '<p style="margin:0;font-size:11px;color:#bbb">'
            .           '&copy; ' . $year . ' ' . htmlspecialchars($appName)
            .           ' &middot; <a href="' . $appUrl . '"'
            .             ' style="color:#e30000;text-decoration:none">' . $appUrl . '</a>'
            .         '</p>'
            .       '</td></tr>'
            .     '</table>'
            .   '</td></tr>'
            . '</table>'
            . '</body></html>';
    }
}
