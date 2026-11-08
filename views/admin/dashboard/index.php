<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Dashboard | KosManager</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&display=swap" rel="stylesheet">
<script src="https://cdn.tailwindcss.com"></script>
<script src="<?= base_url('/public/assets/js/admin-theme.js') ?>"></script>
<link rel="stylesheet" href="<?= base_url('/public/assets/css/style.css') ?>">
<link rel="stylesheet" href="<?= base_url('/public/assets/css/admin.css') ?>">
</head>
<body class="bg-background font-body-md text-on-surface antialiased">
<?php $activeAdminPage = 'dashboard'; include __DIR__ . '/../layout/sidebar.php'; ?>
<div class="pl-72">
<?php include __DIR__ . '/../layout/admin-header.php'; ?>
<main class="relative pt-16 bg-background w-full px-gutter-md py-gutter-md">
<div class="max-w-7xl mx-auto space-y-space-lg">
<header>
<h1 class="font-headline-lg text-on-surface">Dashboard Pengelolaan Properti</h1>
<p class="text-body-md text-on-surface-variant">Ringkasan kamar dan penghuni dari data operasional.</p>
</header>
<?php $flash = get_flash(); if ($flash !== null): ?>
<div class="p-space-md rounded-xl flex items-center gap-space-sm <?= $flash['type'] === 'error' ? 'bg-error-container text-on-error-container' : 'bg-tertiary-fixed text-on-tertiary-fixed' ?>" role="alert">
    <span class="material-symbols-outlined text-[20px]"><?= $flash['type'] === 'error' ? 'error' : 'check_circle' ?></span>
    <span class="font-label-md"><?= $escape($flash['message']) ?></span>
</div>
<?php endif; ?>
<section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-space-md" aria-label="Ringkasan">
<?php foreach ([
    'Total Kos' => $stats['kos'],
    'Total Kamar' => $stats['kamar'],
    'Kamar Kosong' => $stats['kosong'],
    'Kamar Terisi' => $stats['terisi'],
] as $label => $value): ?>
<article class="bg-surface-container-lowest p-space-lg rounded-xl shadow-sm">
<h2 class="font-label-md text-on-surface-variant"><?= $escape($label) ?></h2>
<p class="mt-space-sm font-display-hero text-secondary"><?= (int) $value ?></p>
</article>
<?php endforeach; ?>
</section>
<section class="bg-surface-container-lowest rounded-xl shadow-sm overflow-hidden">
<div class="p-space-lg flex items-center justify-between gap-space-sm">
<div><h2 class="font-headline-md text-on-surface">Penghuni Terbaru</h2><p class="text-body-sm text-on-surface-variant">Maksimal lima data terakhir.</p></div>
<a class="px-space-md py-2 rounded-lg bg-secondary text-on-secondary font-label-md" href="<?= base_url('/penghuni') ?>">Kelola penghuni</a>
</div>
<div class="overflow-x-auto">
<table class="w-full text-left">
<thead class="bg-surface-container-low text-on-surface-variant"><tr>
<th class="p-space-md font-label-sm">Nama</th><th class="p-space-md font-label-sm">NIK</th><th class="p-space-md font-label-sm">Kos / Kamar</th>
</tr></thead>
<tbody class="divide-y divide-surface-container">
<?php foreach ($recentResidents as $resident): ?>
<tr><td class="p-space-md"><?= $escape($resident['nama']) ?></td><td class="p-space-md"><?= $escape($resident['nik']) ?></td>
<td class="p-space-md"><?= $escape($resident['nama_kos'] ?? '-') ?> / <?= $escape($resident['nomor_kamar'] ?? '-') ?></td></tr>
<?php endforeach; ?>
<?php if ($recentResidents === []): ?><tr><td class="p-space-md text-on-surface-variant" colspan="3">Belum ada penghuni.</td></tr><?php endif; ?>
</tbody>
</table>
</div>
</section>
<nav class="flex flex-wrap gap-space-sm">
<a class="px-space-md py-space-sm rounded-lg bg-surface-container-lowest text-secondary" href="<?= base_url('/kamar') ?>">Manajemen kamar</a>
<a class="px-space-md py-space-sm rounded-lg bg-surface-container-lowest text-secondary" href="<?= base_url('/penghuni') ?>">Manajemen penghuni</a>
</nav>
</div>
</main>
</div>
</body>
</html>
