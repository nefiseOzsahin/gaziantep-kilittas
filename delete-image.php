<?php
require 'vendor/autoload.php';

\Cloudinary\Configuration\Configuration::instance([
    'cloud' => [
        'cloud_name' => 'diaavuqa1',
        'api_key'    => '557345822633746',
        'api_secret' => 'x6WPjTdJOdBHOv-9eIyp-eDSZXs',
    ],
]);

use Cloudinary\Api\Upload\UploadApi;

$publicId = $_POST['public_id'];
$secureUrl = $_POST['secure_url'];

// 1. Cloudinary'den sil
$api = new UploadApi();
$api->destroy($publicId);

// 2. img-urls.json'dan kaldır
$jsonPath = 'img-urls.json';
$urls = json_decode(file_get_contents($jsonPath), true);
$updated = array_filter($urls, function($url) use ($secureUrl) {
    return $url !== $secureUrl;
});
file_put_contents($jsonPath, json_encode(array_values($updated), JSON_PRETTY_PRINT));

// 3. Geri yönlendir
header('Location: admin-delete.php');
exit;
