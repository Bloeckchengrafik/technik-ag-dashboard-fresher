<?php

use function Modules\Utils\Api\init;
use function Modules\Utils\database;

include_once '../../Modules/Autoload.php';

init();
# New Captcha
$width = 150;
$height = 50;

$image = imagecreatetruecolor($width, $height);
imagealphablending($image, true);
imagesavealpha($image, true);

$bg_color_r = rand(0, 255);
$bg_color_g = rand(0, 255);
$bg_color_b = rand(0, 255);

imagefill($image, 0, 0, imagecolorallocate($image, $bg_color_r, $bg_color_g, $bg_color_b));

// Get a contrast color
$contrast_color = imagecolorallocate($image, 255 - $bg_color_r, 255 - $bg_color_g, 255 - $bg_color_b);

// Get contrast between background and contrast color
$contrast = (max($bg_color_r, $bg_color_g, $bg_color_b) - min($bg_color_r, $bg_color_g, $bg_color_b)) / 255;
if ($contrast < 0.5) {
    $contrast_color = imagecolorallocate($image, 0, 0, 0);
}

// Add fake text
$low_contrast_color = imagecolorallocatealpha($image, 0, 0, 0, 5);
for ($i = 0; $i < 10; $i++) {
    $x = rand(0, $width);
    $y = rand(0, $height);

    $angle = rand(-30, 30);

    $random_char = chr(rand(65, 90));

    imagettftext($image, 5, $angle, $x, $y, $low_contrast_color, './Poppins-Regular.ttf', "$random_char");
}

$full_text = '';

// Generate a random string
for ($i = 0; $i < 4; $i++) {
    $random_char = chr(rand(65, 90));
    $full_text .= $random_char;

    $x = ($i + 1) * 30 - 10;
    $y = rand(20, 40);

    $angle = rand(-30, 30);

    imagettftext($image, 20, $angle, $x, $y, $contrast_color, './Poppins-Regular.ttf', "$random_char");
}

// Add some noise
for ($i = 0; $i < 100; $i++) {
    $x = rand(0, $width);
    $y = rand(0, $height);

    imagesetpixel($image, $x, $y, imagecolorallocate($image, rand(0, 255), rand(0, 255), rand(0, 255)));
}

// Add some lines
for ($i = 0; $i < 10; $i++) {
    $x1 = rand(0, $width);
    $y1 = rand(0, $height);

    $x2 = rand(0, $width);
    $y2 = rand(0, $height);

    imageline($image, $x1, $y1, $x2, $y2, imagecolorallocate($image, rand(0, 255), rand(0, 255), rand(0, 255)));
}

// Add some arcs
for ($i = 0; $i < 10; $i++) {
    $x = rand(0, $width);
    $y = rand(0, $height);

    $width = rand(0, $width);
    $height = rand(0, $height);

    $start = rand(0, 360);
    $end = rand(0, 360);

    imagearc($image, $x, $y, $width, $height, $start, $end, imagecolorallocate($image, rand(0, 255), rand(0, 255), rand(0, 255)));
}

$db = database();
$db->query('DELETE FROM CompletelyAutomatedPublicTuringTestToTellComputersAndHumansApart WHERE timeout < NOW()');
$stmt = $db->prepare('INSERT INTO CompletelyAutomatedPublicTuringTestToTellComputersAndHumansApart (answer) VALUES (:value)');
$stmt->execute([
    ':value' => $full_text,
]);

// Show the image
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Content-Type: image/png');
header('X-Captcha-ID: ' . $db->lastInsertId());

imagepng($image);
imagedestroy($image);
