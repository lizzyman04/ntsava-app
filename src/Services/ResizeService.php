<?php

namespace Source\Services;

use Source\Models\User;
use Source\Models\File;
use App\Core\ORMHelper;

class ResizeService
{
    private string $cacheDir;
    private array $allowedFilters = ['grayscale', 'sepia', 'blur', 'brightness', 'contrast', 'sharpen', 'edges', 'emboss', 'negate', 'smooth', 'pixelate'];
    private array $allowedFormats = ['jpg', 'jpeg', 'png', 'webp', 'gif', 'avif'];
    private array $qualityMap = [
        'jpg' => 85,
        'jpeg' => 85,
        'png' => 8,
        'webp' => 80,
        'gif' => 85,
        'avif' => 70
    ];

    public function __construct()
    {
        $this->cacheDir = base_path('storage/cache');
        if (!is_dir($this->cacheDir)) {
            mkdir($this->cacheDir, 0755, true);
        }

        $this->cleanOldCache(604800);
    }

    public function process(User $user, string $path, array $params): array
    {
        // Find file
        $fileRepo = ORMHelper::getRepository(File::class);
        $storagePath = $user->getStoragePath() . '/' . ltrim($path, '/');
        $file = $fileRepo->findOne(['userId' => $user->getId(), 'storagePath' => $storagePath]);

        if (!$file || $file->isDeleted()) {
            return ['success' => false, 'error' => 'File not found'];
        }

        // Check if it's an image
        if (!$file->isImage()) {
            return ['success' => false, 'error' => 'Resize only available for images'];
        }

        $sourcePath = base_path('storage/' . $file->getStoragePath());

        if (!file_exists($sourcePath)) {
            return ['success' => false, 'error' => 'Source file not found'];
        }

        // Validate and sanitize params
        $params = $this->sanitizeParams($params);

        // Generate cache key
        $cacheKey = $this->generateCacheKey($file->getUuid(), $params);
        $cachePath = $this->cacheDir . '/' . $cacheKey;

        // Check cache
        if (file_exists($cachePath)) {
            return [
                'success' => true,
                'url' => $this->getCacheUrl($cacheKey),
                'path' => $cachePath,
                'cached' => true,
                'size' => filesize($cachePath),
                'dimensions' => $this->getImageDimensions($cachePath)
            ];
        }

        // Process image
        try {
            $result = $this->processImage($sourcePath, $cachePath, $params);
            return array_merge([
                'success' => true,
                'url' => $this->getCacheUrl($cacheKey),
                'path' => $cachePath,
                'cached' => false
            ], $result);
        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    private function sanitizeParams(array $params): array
    {
        $sanitized = [];

        // Dimensions
        if (isset($params['w'])) {
            $sanitized['w'] = max(1, min(4096, (int) $params['w']));
        }
        if (isset($params['h'])) {
            $sanitized['h'] = max(1, min(4096, (int) $params['h']));
        }

        // Crop
        if (isset($params['crop']) && in_array($params['crop'], ['center', 'top', 'bottom', 'left', 'right', 'smart'])) {
            $sanitized['crop'] = $params['crop'];
        }

        // Fit mode
        if (isset($params['fit']) && in_array($params['fit'], ['contain', 'cover', 'fill', 'inside', 'outside'])) {
            $sanitized['fit'] = $params['fit'];
        }

        // Quality
        if (isset($params['q'])) {
            $sanitized['q'] = max(1, min(100, (int) $params['q']));
        }

        // Format
        if (isset($params['format']) && in_array(strtolower($params['format']), $this->allowedFormats)) {
            $sanitized['format'] = strtolower($params['format']);
        }

        // Filters
        if (isset($params['filter']) && in_array($params['filter'], $this->allowedFilters)) {
            $sanitized['filter'] = $params['filter'];
        }

        // Blur
        if (isset($params['blur'])) {
            $sanitized['blur'] = max(1, min(20, (int) $params['blur']));
        }

        // Brightness
        if (isset($params['brightness'])) {
            $sanitized['brightness'] = max(-255, min(255, (int) $params['brightness']));
        }

        // Contrast
        if (isset($params['contrast'])) {
            $sanitized['contrast'] = max(-100, min(100, (int) $params['contrast']));
        }

        // Rotate
        if (isset($params['rotate'])) {
            $sanitized['rotate'] = (int) $params['rotate'] % 360;
        }

        // Flip
        if (isset($params['flip']) && in_array($params['flip'], ['h', 'v', 'both'])) {
            $sanitized['flip'] = $params['flip'];
        }

        // Pixelate
        if (isset($params['pixelate'])) {
            $sanitized['pixelate'] = max(1, min(50, (int) $params['pixelate']));
        }

        // Smooth
        if (isset($params['smooth'])) {
            $sanitized['smooth'] = max(1, min(10, (int) $params['smooth']));
        }

        // Background color (for transparent images)
        if (isset($params['bg'])) {
            $sanitized['bg'] = $this->validateColor($params['bg']);
        }

        // Watermark
        if (isset($params['watermark']) && filter_var($params['watermark'], FILTER_VALIDATE_URL)) {
            $sanitized['watermark'] = $params['watermark'];
        }

        // Auto-orient (fix EXIF orientation)
        if (isset($params['auto_orient'])) {
            $sanitized['auto_orient'] = filter_var($params['auto_orient'], FILTER_VALIDATE_BOOLEAN);
        }

        return $sanitized;
    }

    private function validateColor(string $color): ?string
    {
        // Hex color
        if (preg_match('/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/', $color)) {
            return $color;
        }

        // RGB color (r,g,b)
        if (preg_match('/^(\d{1,3}),(\d{1,3}),(\d{1,3})$/', $color, $matches)) {
            $r = max(0, min(255, $matches[1]));
            $g = max(0, min(255, $matches[2]));
            $b = max(0, min(255, $matches[3]));
            return "{$r},{$g},{$b}";
        }

        // Named colors
        $namedColors = [
            'white' => '255,255,255',
            'black' => '0,0,0',
            'red' => '255,0,0',
            'green' => '0,255,0',
            'blue' => '0,0,255',
            'transparent' => 'transparent'
        ];

        return $namedColors[strtolower($color)] ?? null;
    }

    private function processImage(string $source, string $target, array $params): array
    {
        // Load with EXIF auto-orientation
        $image = $this->loadImageWithOrientation($source, $params['auto_orient'] ?? true);

        $sourceWidth = imagesx($image);
        $sourceHeight = imagesy($image);

        // Calculate target dimensions
        [$width, $height, $cropParams] = $this->calculateDimensions($sourceWidth, $sourceHeight, $params);

        // Create target image
        $targetImage = $this->createTargetImage($width, $height, $params);

        // Apply crop if needed
        if ($cropParams) {
            imagecopyresampled(
                $targetImage,
                $image,
                0,
                0,
                $cropParams['x'],
                $cropParams['y'],
                $width,
                $height,
                $cropParams['w'],
                $cropParams['h']
            );
        } else {
            imagecopyresampled($targetImage, $image, 0, 0, 0, 0, $width, $height, $sourceWidth, $sourceHeight);
        }

        // Apply filters and effects
        $this->applyFilters($targetImage, $params);
        $this->applyRotateFlip($targetImage, $params);
        $this->applyPixelate($targetImage, $params);
        $this->applySmooth($targetImage, $params);
        $this->applyWatermark($targetImage, $params);

        // Save image
        $format = $params['format'] ?? 'jpg';
        $quality = $params['q'] ?? $this->qualityMap[$format] ?? 85;
        $this->saveImage($targetImage, $target, $format, $quality);

        // Get result info
        $result = [
            'width' => $width,
            'height' => $height,
            'format' => $format,
            'size' => filesize($target)
        ];

        // Free memory
        imagedestroy($image);
        imagedestroy($targetImage);

        return $result;
    }

    private function loadImageWithOrientation(string $path, bool $autoOrient = true)
    {
        $info = getimagesize($path);
        if (!$info) {
            throw new \Exception('Invalid image file');
        }

        $type = $info[2];
        $image = $this->loadImage($path, $type);

        if (!$image) {
            throw new \Exception('Failed to load image');
        }

        // Auto-orient based on EXIF data (JPEG only)
        if ($autoOrient && $type === IMAGETYPE_JPEG && function_exists('exif_read_data')) {
            $exif = @exif_read_data($path);
            if ($exif && isset($exif['Orientation'])) {
                $image = $this->orientImage($image, $exif['Orientation']);
            }
        }

        return $image;
    }

    private function orientImage($image, int $orientation)
    {
        switch ($orientation) {
            case 2: // Flip horizontal
                imageflip($image, IMG_FLIP_HORIZONTAL);
                break;
            case 3: // Rotate 180
                $image = imagerotate($image, 180, 0);
                break;
            case 4: // Flip vertical
                imageflip($image, IMG_FLIP_VERTICAL);
                break;
            case 5: // Rotate 90 + flip
                $image = imagerotate($image, -90, 0);
                imageflip($image, IMG_FLIP_HORIZONTAL);
                break;
            case 6: // Rotate 90
                $image = imagerotate($image, -90, 0);
                break;
            case 7: // Rotate -90 + flip
                $image = imagerotate($image, 90, 0);
                imageflip($image, IMG_FLIP_HORIZONTAL);
                break;
            case 8: // Rotate -90
                $image = imagerotate($image, 90, 0);
                break;
        }
        return $image;
    }

    private function calculateDimensions(int $srcW, int $srcH, array $params): array
    {
        $targetW = $params['w'] ?? $srcW;
        $targetH = $params['h'] ?? $srcH;
        $fit = $params['fit'] ?? 'cover';
        $crop = $params['crop'] ?? null;
        $cropParams = null;

        // If both dimensions are set
        if (isset($params['w']) && isset($params['h'])) {
            $ratioSrc = $srcW / $srcH;
            $ratioDst = $targetW / $targetH;

            switch ($fit) {
                case 'contain':
                    if ($ratioSrc > $ratioDst) {
                        $targetH = $targetW / $ratioSrc;
                    } else {
                        $targetW = $targetH * $ratioSrc;
                    }
                    break;

                case 'cover':
                    if ($ratioSrc > $ratioDst) {
                        $cropW = $srcH * $ratioDst;
                        $cropParams = $this->calculateCrop($srcW, $srcH, $cropW, $srcH, $crop);
                        $srcW = $cropParams['w'];
                        $srcH = $cropParams['h'];
                    } else {
                        $cropH = $srcW / $ratioDst;
                        $cropParams = $this->calculateCrop($srcW, $srcH, $srcW, $cropH, $crop);
                        $srcW = $cropParams['w'];
                        $srcH = $cropParams['h'];
                    }
                    break;

                case 'fill':
                    // No adjustment, image will be stretched
                    break;

                case 'inside':
                    if ($srcW <= $targetW && $srcH <= $targetH) {
                        $targetW = $srcW;
                        $targetH = $srcH;
                    } elseif ($srcW / $targetW > $srcH / $targetH) {
                        $targetH = $targetW / $ratioSrc;
                    } else {
                        $targetW = $targetH * $ratioSrc;
                    }
                    break;

                case 'outside':
                    if ($srcW >= $targetW && $srcH >= $targetH) {
                        $targetW = $srcW;
                        $targetH = $srcH;
                    } elseif ($srcW / $targetW < $srcH / $targetH) {
                        $targetH = $targetW / $ratioSrc;
                    } else {
                        $targetW = $targetH * $ratioSrc;
                    }
                    break;
            }
        } elseif (isset($params['w']) && !isset($params['h'])) {
            // Width only, maintain aspect ratio
            $ratio = $srcH / $srcW;
            $targetH = (int) ($targetW * $ratio);
        } elseif (!isset($params['w']) && isset($params['h'])) {
            // Height only, maintain aspect ratio
            $ratio = $srcW / $srcH;
            $targetW = (int) ($targetH * $ratio);
        }

        return [(int) $targetW, (int) $targetH, $cropParams];
    }

    private function calculateCrop(int $srcW, int $srcH, int $cropW, int $cropH, ?string $position): array
    {
        $x = 0;
        $y = 0;

        switch ($position) {
            case 'center':
            case 'smart':
                $x = (int) (($srcW - $cropW) / 2);
                $y = (int) (($srcH - $cropH) / 2);
                break;
            case 'top':
                $x = (int) (($srcW - $cropW) / 2);
                $y = 0;
                break;
            case 'bottom':
                $x = (int) (($srcW - $cropW) / 2);
                $y = $srcH - $cropH;
                break;
            case 'left':
                $x = 0;
                $y = (int) (($srcH - $cropH) / 2);
                break;
            case 'right':
                $x = $srcW - $cropW;
                $y = (int) (($srcH - $cropH) / 2);
                break;
            default:
                $x = (int) (($srcW - $cropW) / 2);
                $y = (int) (($srcH - $cropH) / 2);
        }

        return ['x' => $x, 'y' => $y, 'w' => $cropW, 'h' => $cropH];
    }

    private function createTargetImage(int $width, int $height, array $params)
    {
        $image = imagecreatetruecolor($width, $height);

        // Handle transparency
        $format = $params['format'] ?? 'jpg';
        $bgColor = $params['bg'] ?? null;

        if ($format === 'png' || $format === 'webp' || $format === 'gif') {
            imagealphablending($image, false);
            imagesavealpha($image, true);

            if ($bgColor && $bgColor !== 'transparent') {
                $color = $this->parseRgbColor($bgColor);
                $bg = imagecolorallocate($image, $color['r'], $color['g'], $color['b']);
                imagefilledrectangle($image, 0, 0, $width, $height, $bg);
            } else {
                $transparent = imagecolorallocatealpha($image, 0, 0, 0, 127);
                imagefilledrectangle($image, 0, 0, $width, $height, $transparent);
            }
        } else {
            // JPG - fill with background or white
            if ($bgColor) {
                $color = $this->parseRgbColor($bgColor);
                $bg = imagecolorallocate($image, $color['r'], $color['g'], $color['b']);
            } else {
                $bg = imagecolorallocate($image, 255, 255, 255);
            }
            imagefilledrectangle($image, 0, 0, $width, $height, $bg);
        }

        return $image;
    }

    private function parseRgbColor(string $color): array
    {
        if (strpos($color, ',') !== false) {
            $parts = explode(',', $color);
            return [
                'r' => (int) $parts[0],
                'g' => (int) $parts[1],
                'b' => (int) $parts[2]
            ];
        }

        // Hex to RGB
        $color = ltrim($color, '#');
        if (strlen($color) === 3) {
            $r = hexdec($color[0] . $color[0]);
            $g = hexdec($color[1] . $color[1]);
            $b = hexdec($color[2] . $color[2]);
        } else {
            $r = hexdec($color[0] . $color[1]);
            $g = hexdec($color[2] . $color[3]);
            $b = hexdec($color[4] . $color[5]);
        }

        return ['r' => $r, 'g' => $g, 'b' => $b];
    }

    private function loadImage(string $path, int $type)
    {
        switch ($type) {
            case IMAGETYPE_JPEG:
                return imagecreatefromjpeg($path);
            case IMAGETYPE_PNG:
                return imagecreatefrompng($path);
            case IMAGETYPE_GIF:
                return imagecreatefromgif($path);
            case IMAGETYPE_WEBP:
                if (function_exists('imagecreatefromwebp')) {
                    return imagecreatefromwebp($path);
                }
                throw new \Exception('WEBP support not available');
            case IMAGETYPE_AVIF:
                if (function_exists('imagecreatefromavif')) {
                    return imagecreatefromavif($path);
                }
                throw new \Exception('AVIF support not available');
            default:
                throw new \Exception('Unsupported image type');
        }
    }

    private function applyFilters($image, array $params): void
    {
        if (!isset($params['filter'])) {
            return;
        }

        switch ($params['filter']) {
            case 'grayscale':
                imagefilter($image, IMG_FILTER_GRAYSCALE);
                break;
            case 'sepia':
                imagefilter($image, IMG_FILTER_GRAYSCALE);
                imagefilter($image, IMG_FILTER_COLORIZE, 100, 50, 0);
                break;
            case 'blur':
                $level = $params['blur'] ?? 1;
                for ($i = 0; $i < $level; $i++) {
                    imagefilter($image, IMG_FILTER_GAUSSIAN_BLUR);
                }
                break;
            case 'brightness':
                $level = $params['brightness'] ?? 0;
                imagefilter($image, IMG_FILTER_BRIGHTNESS, $level);
                break;
            case 'contrast':
                $level = $params['contrast'] ?? 0;
                imagefilter($image, IMG_FILTER_CONTRAST, $level);
                break;
            case 'sharpen':
                $matrix = [
                    [-1, -1, -1],
                    [-1, 16, -1],
                    [-1, -1, -1]
                ];
                imageconvolution($image, $matrix, 8, 0);
                break;
            case 'edges':
                imagefilter($image, IMG_FILTER_EDGEDETECT);
                break;
            case 'emboss':
                imagefilter($image, IMG_FILTER_EMBOSS);
                break;
            case 'negate':
                imagefilter($image, IMG_FILTER_NEGATE);
                break;
        }
    }

    private function applyRotateFlip($image, array $params): void
    {
        if (isset($params['rotate']) && $params['rotate'] !== 0) {
            $bgColor = imagecolorallocate($image, 255, 255, 255);
            $rotated = imagerotate($image, $params['rotate'], $bgColor);
            if ($rotated) {
                // Copy back to original resource
                imagesx($image);
                imagesy($image);
                imagecopy($image, $rotated, 0, 0, 0, 0, imagesx($rotated), imagesy($rotated));
                imagedestroy($rotated);
            }
        }

        if (isset($params['flip'])) {
            switch ($params['flip']) {
                case 'h':
                    imageflip($image, IMG_FLIP_HORIZONTAL);
                    break;
                case 'v':
                    imageflip($image, IMG_FLIP_VERTICAL);
                    break;
                case 'both':
                    imageflip($image, IMG_FLIP_BOTH);
                    break;
            }
        }
    }

    private function applyPixelate($image, array $params): void
    {
        if (isset($params['pixelate'])) {
            imagefilter($image, IMG_FILTER_PIXELATE, $params['pixelate'], true);
        }
    }

    private function applySmooth($image, array $params): void
    {
        if (isset($params['smooth'])) {
            imagefilter($image, IMG_FILTER_SMOOTH, $params['smooth']);
        }
    }

    private function applyWatermark($image, array $params): void
    {
        if (!isset($params['watermark'])) {
            return;
        }

        // Load watermark image
        $watermark = @imagecreatefrompng($params['watermark']);
        if (!$watermark) {
            return;
        }

        $wWidth = imagesx($watermark);
        $wHeight = imagesy($watermark);
        $iWidth = imagesx($image);
        $iHeight = imagesy($image);

        // Position: bottom-right corner with 10px padding
        $destX = $iWidth - $wWidth - 10;
        $destY = $iHeight - $wHeight - 10;

        // Apply watermark with 50% opacity
        imagecopymerge($image, $watermark, $destX, $destY, 0, 0, $wWidth, $wHeight, 50);

        imagedestroy($watermark);
    }

    private function saveImage($image, string $path, string $format, int $quality): void
    {
        $success = false;

        switch ($format) {
            case 'webp':
                if (function_exists('imagewebp')) {
                    $success = imagewebp($image, $path, $quality);
                }
                break;
            case 'png':
                // PNG quality: 0-9 (0 = no compression, 9 = max)
                $pngQuality = (int) round(9 - ($quality / 100) * 9);
                $success = imagepng($image, $path, $pngQuality);
                break;
            case 'gif':
                $success = imagegif($image, $path);
                break;
            case 'avif':
                if (function_exists('imageavif')) {
                    $success = imageavif($image, $path, $quality);
                }
                break;
            case 'jpg':
            case 'jpeg':
            default:
                $success = imagejpeg($image, $path, $quality);
                break;
        }

        if (!$success) {
            throw new \Exception("Failed to save image as {$format}");
        }

        chmod($path, 0644);
    }

    private function getImageDimensions(string $path): ?array
    {
        $info = getimagesize($path);
        if (!$info) {
            return null;
        }

        return [
            'width' => $info[0],
            'height' => $info[1],
            'mime' => $info['mime']
        ];
    }

    private function generateCacheKey(string $uuid, array $params): string
    {
        ksort($params);
        $key = $uuid . '_' . http_build_query($params);
        $hash = md5($key);
        $format = $params['format'] ?? 'jpg';
        return "{$hash}.{$format}";
    }

    private function getCacheUrl(string $cacheKey): string
    {
        return 'https://cdn.tudocomlizzyman.com/cache/' . $cacheKey;
    }

    private function cleanOldCache(int $olderThan = 604800): void
    {
        $now = time();
        $files = glob($this->cacheDir . '/*');

        foreach ($files as $file) {
            if (is_file($file) && ($now - filectime($file)) > $olderThan) {
                @unlink($file);
            }
        }
    }

    public function clearCache(string $uuid = null): int
    {
        $deleted = 0;
        $pattern = $uuid ? "{$this->cacheDir}/" . md5($uuid) . "*" : "{$this->cacheDir}/*";

        foreach (glob($pattern) as $file) {
            if (is_file($file) && unlink($file)) {
                $deleted++;
            }
        }

        return $deleted;
    }
}