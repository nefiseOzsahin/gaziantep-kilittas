<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$jsonFile = "image-urls.json";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['url'])) {
    $urlToDelete = $_POST['url'];

    if (!file_exists($jsonFile)) {
        die("JSON dosyası bulunamadı.");
    }

    $urls = json_decode(file_get_contents($jsonFile), true);

    if (($key = array_search($urlToDelete, $urls)) !== false) {
        unset($urls[$key]);
        $urls = array_values($urls); // indexleri sıfırla

        file_put_contents($jsonFile, json_encode($urls, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        echo "Görsel başarıyla silindi.<br>";
    } else {
        echo "URL bulunamadı.<br>";
    }

    echo '<a href="admin-delete.php">Geri dön</a>';
} else {
    echo "Geçersiz istek.";
}
