<?php
require_once __DIR__ . '/koneksi.php';

$projects = [];
$result = $koneksi->query('SELECT p.* FROM projects p INNER JOIN (SELECT title, MAX(id) AS id FROM projects GROUP BY title) latest ON latest.id = p.id ORDER BY p.created_at DESC');
if ($result) {
	while ($row = $result->fetch_assoc()) {
		$projects[] = $row;
	}
}

$notice = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$name = trim($_POST['name'] ?? '');
	$email = trim($_POST['email'] ?? '');
	$message = trim($_POST['message'] ?? '');

	if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || $message === '') {
		$notice = 'Mohon isi nama, email yang valid, dan pesan.';
	} else {
		$stmt = $koneksi->prepare('INSERT INTO messages (name, email, message) VALUES (?, ?, ?)');
		$stmt->bind_param('sss', $name, $email, $message);
		$notice = $stmt->execute() ? 'Pesan berhasil dikirim.' : 'Pesan gagal disimpan.';
		$stmt->close();
	}
}

function aman($text) {
	return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}
?>
<!doctype html>
<html lang="id">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Portfolio | Web Developer</title>
	<link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
	<nav><a class="logo" href="#home">Levyan Terry<b>.</b></a><ul><li><a href="#karya">Karya</a></li><li><a href="#tentang">Tentang</a></li><li><a href="#kontak">Kontak</a></li></ul></nav>
	<aside class="music-player" aria-label="Pemutar musik">
		<div class="music-copy"><span class="music-status">NOW PLAYING</span><strong>Playlist Levyan</strong></div>
		<button class="music-toggle" type="button" aria-label="Putar musik" aria-pressed="false">Play</button>
		<button class="music-mute" type="button" aria-label="Matikan suara" aria-pressed="false">Mute</button>
		<label class="volume-control" for="volume-php">Volume<input id="volume-php" type="range" min="0" max="1" step="0.05" value="0.35"></label>
		<audio id="site-audio" autoplay loop preload="auto"><source src="lagu.mp3" type="audio/mpeg">Browser kamu tidak mendukung audio.</audio>
	</aside>
	<main id="home">
		<div class="hero"><div><div class="label">Web Developer / Surabaya</div><h1>Levyan <i>Terry</i></h1><p class="lead">Content creator yang berbagi cerita, lifestyle, dan berbagai momen menarik dengan cara yang autentik.</p><div class="buttons"><a class="button main" href="#karya">Lihat Sosial Media &darr;</a><a class="button" href="#kontak">Mari ngobrol &rarr;</a></div></div><div class="visual"><img src="terry.jpg" alt="Foto Terry"><strong>✳</strong></div></div>
		<section id="karya"><div class="heading"><div><div class="label">01 / Portfolio</div><h2>Sosial <i>Media</i></h2></div><p class="muted"></p></div><div class="projects">
			<?php foreach ($projects as $number => $project): ?><?php $judul = $project['title'] === 'INTSAGRAM' ? 'INSTAGRAM' : $project['title']; $gambar = !empty($project['image_url']) ? $project['image_url'] : ($project['title'] === 'TIKTOK' ? 'vyan.jpg' : ($project['title'] === 'INTSAGRAM' ? 'ig.jpg' : ($project['title'] === 'GITHUB' ? 'github.jpg' : ''))); $deskripsi = $project['title'] === 'TIKTOK' ? "✨ Just sharing my little world\n📩 Business: DM\n📸 Instagram: ryyvyan" : $project['description']; $link = $project['title'] === 'TIKTOK' ? 'https://www.tiktok.com/@vyanterry' : ($project['title'] === 'INTSAGRAM' ? 'https://www.instagram.com/ryyvyan' : ($project['title'] === 'GITHUB' ? 'https://github.com/vyanterry' : $project['project_url'])); $labelLink = $project['title'] === 'INTSAGRAM' ? 'Buka Instagram' : ($project['title'] === 'GITHUB' ? 'Buka GitHub' : 'Buka TikTok'); ?><article class="project"><div class="project-number"><?php if ($gambar): ?><img src="<?= aman($gambar) ?>" alt="Gambar <?= aman($judul) ?>"><?php else: ?>0<?= $number + 1 ?><?php endif; ?></div><h3><?= aman($judul) ?></h3><p><?= nl2br(aman($deskripsi)) ?></p><div class="tech"><?= aman($project['tech_stack']) ?></div><?php if ($link !== '#'): ?><a class="project-link" href="<?= aman($link) ?>" target="_blank" rel="noopener noreferrer"><?= $labelLink ?> &rarr;</a><?php endif; ?></article><?php endforeach; ?>
		</div></section>
		<section id="tentang"><div class="two-col"><div><div class="label">02 / Tentang saya</div><h2>Lebih Dari Sekedar <i>Membuat Konten</i></h2></div><div><p class="about-text">Saya percaya bahwa karya yang baik bukan hanya tentang apa yang terlihat, tetapi juga tentang cerita dan nilai yang ada di baliknya.

Melalui konten, saya senang membagikan cerita, pengalaman, dan berbagai momen dengan cara yang sederhana, autentik, dan relevan. Bagi saya, setiap karya adalah kesempatan untuk menciptakan sesuatu yang bukan hanya menarik untuk dilihat, tetapi juga meninggalkan kesan.

Saya terus belajar, bereksplorasi, dan berkembang untuk menciptakan karya yang memiliki karakter dan makna.</p></div></div></section>
		<section id="kontak"><div class="two-col"><div><div class="label">03 / Hubungi saya</div><h2>Mari <i>ngobrol.</i></h2><p class="muted">Mohon Diisi Yaa, Nanti Saya Baca</p></div><div><?php if ($notice): ?><div class="notice"><?= aman($notice) ?></div><?php endif; ?><form method="post"><div><label for="name">NAMA</label><input id="name" name="name" required></div><div><label for="email">EMAIL</label><input id="email" name="email" type="email" required></div><div><label for="message">PESAN</label><textarea id="message" name="message" required></textarea></div><button class="button main" type="submit">Kirim pesan &rarr;</button></form></div></div></section>
	</main>
	<footer><span>ryyvyan &copy; 2026</span><span>Dibuat dengan rasa ingin tahu</span></footer>
</div>
<script>
	const audio = document.querySelector('#site-audio');
	const toggle = document.querySelector('.music-toggle');
	const mute = document.querySelector('.music-mute');
	const volume = document.querySelector('.volume-control input');
	audio.volume = volume.value;
	audio.play().then(() => {
		toggle.textContent = 'Pause';
		toggle.setAttribute('aria-pressed', 'true');
	}).catch(() => { toggle.textContent = 'Play'; });
	toggle.addEventListener('click', async () => {
		if (audio.paused) {
			try { await audio.play(); } catch (error) { toggle.textContent = 'Tambah lagu'; return; }
			toggle.textContent = 'Pause';
			toggle.setAttribute('aria-label', 'Jeda musik');
			toggle.setAttribute('aria-pressed', 'true');
		} else {
			audio.pause();
			toggle.textContent = 'Play';
			toggle.setAttribute('aria-label', 'Putar musik');
			toggle.setAttribute('aria-pressed', 'false');
		}
	});
	volume.addEventListener('input', () => { audio.volume = volume.value; });
	mute.addEventListener('click', () => {
		audio.muted = !audio.muted;
		mute.textContent = audio.muted ? 'Unmute' : 'Mute';
		mute.setAttribute('aria-label', audio.muted ? 'Nyalakan suara' : 'Matikan suara');
		mute.setAttribute('aria-pressed', String(audio.muted));
	});
</script>
</body>
</html>
