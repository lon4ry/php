<?php
declare(strict_types=1);

const CHARS = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789'; // без похожих символов: I, O, 0, 1
const FONT = __DIR__ . '/fonts/DejaVuSans-Bold.ttf';
const BACKGROUND = __DIR__ . '/noise.jpg';
const WIDTH = 270;
const HEIGHT = 70;
const PADDING = 3;
const STEP = 40; // расстояние между символами
const MIN_SIZE = 18;
const MAX_SIZE = 30;

session_start();

$length = random_int(5, 6);
$code = '';
for ($i = 0; $i < $length; $i++) {
    $code .= CHARS[random_int(0, strlen(CHARS) - 1)];
}
$_SESSION['captcha'] = $code;

// noise.jpg (210x39) растягивается на холст, чтобы поместились 6 символов по 30 пт с шагом 40
$noise = imagecreatefromjpeg(BACKGROUND);
$image = imagecreatetruecolor(WIDTH, HEIGHT);
imagecopyresampled($image, $noise, 0, 0, 0, 0, WIDTH, HEIGHT, imagesx($noise), imagesy($noise));

$x = 10;
for ($i = 0; $i < $length; $i++) {
    $color = imagecolorallocate($image, random_int(0, 100), random_int(0, 100), random_int(0, 100));
    $size = random_int(MIN_SIZE, MAX_SIZE);
    $angle = random_int(-20, 20);
    // Базовая линия выбирается так, чтобы повёрнутый символ целиком влез по высоте
    $box = imagettfbbox($size, $angle, FONT, $code[$i]);
    $minY = PADDING - min($box[1], $box[3], $box[5], $box[7]);
    $maxY = HEIGHT - 1 - PADDING - max($box[1], $box[3], $box[5], $box[7]);
    $y = $minY <= $maxY ? random_int($minY, $maxY) : intdiv($minY + $maxY, 2);
    imagettftext($image, $size, $angle, $x, $y, $color, FONT, $code[$i]);
    $x += STEP;
}

header('Content-Type: image/png');
header('Cache-Control: no-store');
imagepng($image);
