<?php
/**
 * ImageOptimizer — downsize oversized images in place (GD, no extra deps)
 *
 * Usage:
 *   ImageOptimizer::resize('/full/path/to/file.jpg');
 */
class ImageOptimizer {

    /** Resize a file in place if either dimension exceeds $maxDim. Keeps original format. */
    public static function resize(string $path, int $maxDim = 1600, int $jpegQuality = 85): bool {
        if (!file_exists($path)) return false;
        $info = @getimagesize($path);
        if (!$info) return false;
        [$w, $h, $type] = $info;
        if ($w <= $maxDim && $h <= $maxDim) return false; // already small enough

        self::ensureMemory($w, $h);

        $src = self::load($path, $type);
        if (!$src) return false;

        $ratio = min($maxDim / $w, $maxDim / $h);
        $nw = max(1, (int)round($w * $ratio));
        $nh = max(1, (int)round($h * $ratio));

        $dst = imagecreatetruecolor($nw, $nh);
        if ($type === IMAGETYPE_PNG || $type === IMAGETYPE_GIF || $type === IMAGETYPE_WEBP) {
            imagealphablending($dst, false);
            imagesavealpha($dst, true);
            $transparent = imagecolorallocatealpha($dst, 0, 0, 0, 127);
            imagefilledrectangle($dst, 0, 0, $nw, $nh, $transparent);
        }
        imagecopyresampled($dst, $src, 0, 0, 0, 0, $nw, $nh, $w, $h);

        $ok = self::save($dst, $path, $type, $jpegQuality);
        imagedestroy($src);
        imagedestroy($dst);
        return $ok;
    }

    /** GD needs ~2 full decoded buffers in memory; bump the limit for large source images. */
    private static function ensureMemory(int $w, int $h): void {
        $current = ini_get('memory_limit');
        if ($current === '-1') return; // unlimited
        $needed = (int)($w * $h * 4 * 2.2) + 16 * 1024 * 1024;
        if (self::toBytes($current) < $needed) {
            @ini_set('memory_limit', (int)ceil($needed / 1024 / 1024) . 'M');
        }
    }

    private static function toBytes(string $val): int {
        $val = trim($val);
        if ($val === '') return 0;
        $unit = strtolower(substr($val, -1));
        $num  = (int)$val;
        switch ($unit) {
            case 'g': return $num * 1024 * 1024 * 1024;
            case 'm': return $num * 1024 * 1024;
            case 'k': return $num * 1024;
            default:  return $num;
        }
    }

    private static function load(string $path, int $type) {
        switch ($type) {
            case IMAGETYPE_JPEG: return @imagecreatefromjpeg($path);
            case IMAGETYPE_PNG:  return @imagecreatefrompng($path);
            case IMAGETYPE_GIF:  return @imagecreatefromgif($path);
            case IMAGETYPE_WEBP: return function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($path) : false;
            default: return false;
        }
    }

    private static function save($im, string $path, int $type, int $jpegQuality): bool {
        switch ($type) {
            case IMAGETYPE_JPEG: return imagejpeg($im, $path, $jpegQuality);
            case IMAGETYPE_PNG:  return imagepng($im, $path, 6);
            case IMAGETYPE_GIF:  return imagegif($im, $path);
            case IMAGETYPE_WEBP: return function_exists('imagewebp') ? imagewebp($im, $path, $jpegQuality) : false;
            default: return false;
        }
    }
}
