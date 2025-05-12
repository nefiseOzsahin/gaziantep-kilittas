<?php
$jsonFile = 'image-urls.json';

$data = json_decode(file_get_contents('php://input'), true);
$imageUrl = $data['url'] ?? null;

if ($imageUrl) {
    $currentData = file_exists($jsonFile)
        ? json_decode(file_get_contents($jsonFile), true)
        : [];

    $currentData[] = $imageUrl;

    file_put_contents($jsonFile, json_encode($currentData, JSON_PRETTY_PRINT));
    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'No URL provided']);
}
?>
