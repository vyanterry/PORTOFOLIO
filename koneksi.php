<?php
mysqli_report(MYSQLI_REPORT_OFF);
$dbHost = 'localhost';
$dbUser = 'root';
$dbPass = '';
$dbName = 'portofolio';

$koneksi = new mysqli($dbHost, $dbUser, $dbPass);
if ($koneksi->connect_errno) {
	die('Koneksi MySQL gagal: ' . $koneksi->connect_error);
}
$koneksi->set_charset('utf8mb4');
$koneksi->query("CREATE DATABASE IF NOT EXISTS `$dbName` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
$koneksi->select_db($dbName);
$koneksi->query("CREATE TABLE IF NOT EXISTS projects (
	id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
	title VARCHAR(120) NOT NULL,
	description TEXT NOT NULL,
	tech_stack VARCHAR(255) NOT NULL,
	project_url VARCHAR(255) DEFAULT '#',
	image_url VARCHAR(500) DEFAULT '',
	created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");
$koneksi->query("CREATE TABLE IF NOT EXISTS messages (
	id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
	name VARCHAR(100) NOT NULL,
	email VARCHAR(150) NOT NULL,
	message TEXT NOT NULL,
	created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

$projectCount = $koneksi->query('SELECT COUNT(*) AS total FROM projects');
if ($projectCount && (int) $projectCount->fetch_assoc()['total'] === 0) {
	$seed = $koneksi->prepare('INSERT INTO projects (title, description, tech_stack, project_url, image_url) VALUES (?, ?, ?, ?, ?)');
	$samples = [
		['Nusa Commerce', 'Toko online lokal dengan alur belanja yang cepat dan dashboard stok yang ringkas.', 'PHP, MySQL, JavaScript', '#', ''],
		['Ruang Tumbuh', 'Platform komunitas untuk mengatur acara, artikel, dan pendaftaran anggota.', 'Laravel, MySQL, Tailwind', '#', ''],
		['Fintrack', 'Aplikasi pencatatan keuangan personal dengan insight pengeluaran yang mudah dibaca.', 'PHP, Chart.js, MySQL', '#', ''],
	];
	foreach ($samples as $sample) {
		$seed->bind_param('sssss', ...$sample);
		$seed->execute();
	}
	$seed->close();
}
