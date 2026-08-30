<?php
require_once __DIR__ . '/koneksi.php';

header('Content-Type: application/json; charset=utf-8');

$page = trim((string) ($_GET['page'] ?? 'home'));
$ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
$userAgent = substr((string) ($_SERVER['HTTP_USER_AGENT'] ?? 'unknown'), 0, 255);
$referrer = substr((string) ($_SERVER['HTTP_REFERER'] ?? ''), 0, 255);

$stmt = $koneksi->prepare('INSERT INTO site_visits (page, ip_address, user_agent, referrer) VALUES (?, ?, ?, ?)');
if ($stmt) {
    $stmt->bind_param('ssss', $page, $ip, $userAgent, $referrer);
    $stmt->execute();
    $stmt->close();
}

$result = $koneksi->query('SELECT COUNT(*) AS total_visits FROM site_visits');
$total = 0;
if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $total = (int) ($row['total_visits'] ?? 0);
}

echo json_encode([
    'page' => $page,
    'total_visits' => $total,
    'client_ip' => $ip,
    'success' => true,
], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
