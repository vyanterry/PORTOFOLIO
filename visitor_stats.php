<?php
require_once __DIR__ . '/koneksi.php';

$summaryStmt = $koneksi->query('SELECT tanggal, total_kunjungan, unik_pengunjung FROM v_portfolio_visit_summary ORDER BY tanggal DESC LIMIT 30');
$summary = [];
if ($summaryStmt) {
    while ($row = $summaryStmt->fetch_assoc()) {
        $summary[] = $row;
    }
}

$totalStats = $koneksi->query('SELECT COUNT(*) AS total_kunjungan, COUNT(DISTINCT ip_address) AS unik_pengunjung FROM site_visits')->fetch_assoc();
$totalKunjungan = (int) ($totalStats['total_kunjungan'] ?? 0);
$totalUnik = (int) ($totalStats['unik_pengunjung'] ?? 0);

function aman($value) {
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Statistik Pengunjung</title>
    <style>
        :root {
            --dark: #182622;
            --cream: #f4f0e8;
            --green: #d6e96b;
            --orange: #c96e52;
            --gray: #69756f;
            --paper: #fffdf9;
            --line: #d8ddd4;
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: var(--cream);
            color: var(--dark);
        }
        .wrap {
            width: min(980px, calc(100% - 32px));
            margin: 40px auto;
            background: var(--paper);
            border: 1px solid var(--line);
            box-shadow: 10px 10px 0 rgba(183, 154, 103, 0.3);
            padding: 28px;
        }
        h1 {
            margin: 0 0 14px;
            font-size: clamp(28px, 4vw, 42px);
            letter-spacing: -1px;
        }
        .meta {
            color: var(--gray);
            margin-bottom: 24px;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 16px;
            margin-bottom: 30px;
        }
        .stat {
            background: var(--dark);
            color: var(--cream);
            padding: 18px;
            border-left: 4px solid var(--green);
        }
        .stat-label {
            display: block;
            font-size: 11px;
            letter-spacing: 1.3px;
            text-transform: uppercase;
            color: rgba(244, 240, 232, 0.7);
        }
        .stat-value {
            display: block;
            margin-top: 10px;
            font-size: clamp(28px, 3vw, 40px);
            font-weight: 700;
            letter-spacing: -0.08em;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 12px;
        }
        th, td {
            text-align: left;
            padding: 12px 10px;
            border-bottom: 1px solid var(--line);
        }
        th {
            background: rgba(24, 38, 34, 0.04);
            font-size: 12px;
            letter-spacing: 1.1px;
            text-transform: uppercase;
            color: var(--gray);
        }
        .note {
            margin-top: 18px;
            font-size: 13px;
            color: var(--gray);
        }
        @media (max-width: 700px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }
            .wrap {
                padding: 20px 16px;
            }
        }
    </style>
</head>
<body>
    <div class="wrap">
        <h1>Statistik Pengunjung Portfolio</h1>
        <div class="meta">Lihat berapa banyak orang yang membuka link portfolio Anda.</div>

        <div class="stats-grid">
            <div class="stat">
                <span class="stat-label">Total kunjungan</span>
                <span class="stat-value"><?= number_format($totalKunjungan, 0, ',', '.') ?></span>
            </div>
            <div class="stat">
                <span class="stat-label">Pengunjung unik</span>
                <span class="stat-value"><?= number_format($totalUnik, 0, ',', '.') ?></span>
            </div>
            <div class="stat">
                <span class="stat-label">Hari terakhir</span>
                <span class="stat-value"><?= !empty($summary) ? date('d/m', strtotime($summary[0]['tanggal'])) : '--' ?></span>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Total Kunjungan</th>
                    <th>Pengunjung Unik</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!$summary): ?>
                    <tr>
                        <td colspan="3">Belum ada data kunjungan.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($summary as $row): ?>
                        <tr>
                            <td><?= aman($row['tanggal']) ?></td>
                            <td><?= number_format((int) $row['total_kunjungan'], 0, ',', '.') ?></td>
                            <td><?= number_format((int) $row['unik_pengunjung'], 0, ',', '.') ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>

        <div class="note">Catatan: setiap kali halaman portfolio dibuka, data akan tercatat otomatis.</div>
    </div>
</body>
</html>
