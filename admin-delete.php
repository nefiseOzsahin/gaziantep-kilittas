<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Admin - Görselleri Sil</title>
</head>
<body>
  <h1>Yüklenen Görseller</h1>
  <?php
    $jsonFile ="image-urls.json";

    if (!file_exists($jsonFile)) {
        echo "<p><strong>image-urls.json bulunamadı.</strong></p>";
        exit;
    }

    $urls = json_decode(file_get_contents($jsonFile), true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        echo "<p><strong>JSON okunamadı:</strong> " . json_last_error_msg() . "</p>";
        exit;
    }

    if (empty($urls)) {
        echo "<p>Henüz görsel yok.</p>";
    } else {
        foreach ($urls as $url) {
            echo '<div style="margin-bottom:20px">';
            echo '<img src="' . $url . '" style="max-width:200px"><br>';
            echo '<form method="post" action="delete-image.php">';
            echo '<input type="hidden" name="url" value="' . $url . '">';
            echo '<button type="submit">Sil</button>';
            echo '</form>';
            echo '</div>';
        }
    }
  ?>
</body>
</html>
