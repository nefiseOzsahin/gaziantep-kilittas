<?php
require 'vendor/autoload.php';

\Cloudinary\Configuration\Configuration::instance([
    'cloud' => [
        'cloud_name' => 'diaavuqa1',
        'api_key'    => '557345822633746',
        'api_secret' => 'x6WPjTdJOdBHOv-9eIyp-eDSZXs',
    ],
]);

use Cloudinary\Api\Admin\AdminApi;

$api = new AdminApi();

// Sadece belirli bir klasörü (örneğin: "gallery") al
$resources = $api->resources([
    'type' => 'upload',
    'prefix' => 'gallery/'
]);

$images = $resources['resources'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Delete Images</title>
</head>
<body>
  <h1>Uploaded Images</h1>
  <?php foreach ($images as $image): ?>
    <div style="margin-bottom:20px">
      <img src="<?= $image['secure_url'] ?>" style="max-width:200px"><br>
      <form method="post" action="delete-image.php">
        <input type="hidden" name="public_id" value="<?= $image['public_id'] ?>">
        <input type="hidden" name="secure_url" value="<?= $image['secure_url'] ?>">
        <button type="submit">Sil</button>
      </form>
    </div>
  <?php endforeach; ?>
</body>
</html>
