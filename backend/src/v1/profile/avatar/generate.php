<?php
$id = $_GET['id'];
$hash = hash('md5', $id);
$hash_number = hexdec(substr($hash, 0, 6));

function random_from_folder(int $seed, string $folder): GdImage
{
    $files = scandir($folder);
    $files = array_filter($files, function ($file) {
        return !in_array($file, ['.', '..']);
    });

    $index = $seed % count($files);
    $img = imagecreatefrompng($folder . '/' . $files[$index+2]); // +2 because of . and ..
    // Resize to 300x300
    $resized = imagecreatetruecolor(300, 300);
    // Fill with transparent background
    imagefill($resized, 0, 0, imagecolorallocatealpha($resized, 0, 0, 0, 127));
    imagealphablending($resized, true);
    imagesavealpha($resized, true);
    $src_x = imagesx($img);
    $src_y = imagesy($img);
    imagecopyresampled($resized, $img, 0, 0, 0, 0, 300, 300, $src_x, $src_y);
    imagedestroy($img);
    return $resized;
}

$background = random_from_folder($hash_number >> 0, 'files/backgrounds');
$accessory = random_from_folder($hash_number >> 1, 'files/Accessories');
$body = random_from_folder($hash_number >> 2, 'files/Body');
$cloth = random_from_folder($hash_number >> 3, 'files/Cloth');
$eye = random_from_folder($hash_number >> 4, 'files/Eye');
$eyebrow = random_from_folder($hash_number >> 5, 'files/Eyebrow');
$facialhair = random_from_folder($hash_number >> 6, 'files/FacialHair');
$mouth = random_from_folder($hash_number >> 7, 'files/Mouth');
$top = random_from_folder($hash_number >> 8, 'files/Top');

// Combine the images (bg -> body -> eye -> eyebrow -> mouth -> cloth -> facialhair -> top -> accessory)

$final = imagecreatetruecolor(300, 300);
imagealphablending($final, true);
imagesavealpha($final, true);

imagecopy($final, $background, 0, 0, 0, 0, 300, 300);
imagecopy($final, $body, 0, 0, 0, 0, 300, 300);
imagecopy($final, $eye, 0, 0, 0, 0, 300, 300);
imagecopy($final, $eyebrow, 0, 0, 0, 0, 300, 300);
imagecopy($final, $mouth, 0, 0, 0, 0, 300, 300);
imagecopy($final, $cloth, 0, 0, 0, 0, 300, 300);
imagecopy($final, $facialhair, 0, 0, 0, 0, 300, 300);
imagecopy($final, $top, 0, 0, 0, 0, 300, 300);
imagecopy($final, $accessory, 0, 0, 0, 0, 300, 300);

// Show the image
header('Content-Type: image/png');
imagepng($final);
imagedestroy($final);
imagedestroy($background);
imagedestroy($accessory);
imagedestroy($body);
imagedestroy($cloth);
imagedestroy($eye);
imagedestroy($eyebrow);
imagedestroy($facialhair);
imagedestroy($mouth);
imagedestroy($top);
