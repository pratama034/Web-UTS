<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Manajemen Kamar | KosManager</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&display=swap" rel="stylesheet">
<script src="https://cdn.tailwindcss.com"></script>
<script src="<?= base_url('/public/assets/js/admin-theme.js') ?>"></script>
<link rel="stylesheet" href="<?= base_url('/public/assets/css/style.css') ?>">
<link rel="stylesheet" href="<?= base_url('/public/assets/css/admin.css') ?>">
</head>
<body class="bg-background font-body-md text-on-surface antialiased">
<?php $activeAdminPage = 'manajemen-kamar'; include __DIR__ . '/../layout/sidebar.php'; ?>
<div class="pl-72">
<?php include __DIR__ . '/../layout/admin-header.php'; ?>
<main class="relative pt-16 bg-background w-full px-gutter-md py-gutter-md">
<div class="max-w-7xl mx-auto space-y-space-lg">
<header class="flex flex-wrap items-end justify-between gap-space-md">
<div><h1 class="font-headline-lg text-on-surface">Manajemen Kamar</h1><p class="text-body-md text-on-surface-variant">Status kamar mengikuti penempatan penghuni.</p></div>
<a class="px-space-md py-space-sm rounded-lg bg-secondary text-on-secondary font-label-md" href="<?= base_url('/kamar/tambah') ?>">Tambah Kamar</a>
</header>
<?php $flash = get_flash(); if ($flash !== null): ?>
<div class="p-space-md rounded-xl flex items-center gap-space-sm <?= $flash['type'] === 'error' ? 'bg-error-container text-on-error-container' : 'bg-tertiary-fixed text-on-tertiary-fixed' ?>" role="alert">
    <span class="material-symbols-outlined text-[20px]"><?= $flash['type'] === 'error' ? 'error' : 'check_circle' ?></span>
    <span class="font-label-md"><?= $escape($flash['message']) ?></span>
</div>
<?php endif; ?>
<?php if (!empty($formError)): ?><p class="p-space-md rounded-lg bg-error-container text-on-error-container" role="alert"><?= $escape($formError) ?></p><?php endif; ?>
<section class="bg-surface-container-lowest rounded-xl shadow-sm overflow-hidden">
<div class="overflow-x-auto">
<table class="w-full text-left">
<thead class="bg-surface-container-low text-on-surface-variant"><tr>
<th class="p-space-md font-label-sm">Nomor</th><th class="p-space-md font-label-sm">Kos</th><th class="p-space-md font-label-sm">Tipe</th><th class="p-space-md font-label-sm">Tarif / Bulan</th><th class="p-space-md font-label-sm">Status</th><th class="p-space-md font-label-sm">Penghuni</th><th class="p-space-md font-label-sm">Aksi</th>
</tr></thead>
<tbody class="divide-y divide-surface-container">
<?php foreach ($kamarList as $room): ?>
<tr>
<td class="p-space-md font-headline-sm"><?= $escape($room['nomor_kamar']) ?></td>
<td class="p-space-md"><?= $escape($room['nama_kos']) ?></td>
<td class="p-space-md"><?= $escape($room['tipe']) ?></td>
<td class="p-space-md">Rp <?= $escape(number_format((float) $room['harga_bulanan'], 0, ',', '.')) ?></td>
<td class="p-space-md"><span class="px-2 py-1 rounded-full font-label-sm <?= $room['status'] === 'kosong' ? 'bg-tertiary-fixed text-on-tertiary-fixed' : 'bg-surface-container-high text-on-surface-variant' ?>"><?= $room['status'] === 'kosong' ? 'Kosong' : 'Terisi' ?></span></td>
<td class="p-space-md"><?= $escape($room['nama_penghuni'] ?? 'Belum ada penghuni') ?></td>
<td class="p-space-md"><div class="flex items-center gap-space-xs">
<a class="px-space-sm py-1 rounded bg-surface-container text-secondary" href="<?= base_url('/kamar/ubah?id=' . (int) $room['id']) ?>">Ubah</a>
<form action="<?= base_url('/kamar/hapus') ?>" method="post" onsubmit="return confirm('Hapus kamar ini?')">
<input type="hidden" name="csrf_token" value="<?= $escape(csrf_token()) ?>">
<input type="hidden" name="id" value="<?= (int) $room['id'] ?>">
<button class="px-space-sm py-1 rounded bg-error-container text-on-error-container" type="submit">Hapus</button>
</form>
</div></td>
</tr>
<?php endforeach; ?>
<?php if ($kamarList === []): ?><tr><td class="p-space-lg text-center text-on-surface-variant" colspan="7">Belum ada data kamar.</td></tr><?php endif; ?>
</tbody>
</table>
</div>
</section>
</div>
</main>
</div>
</body>
</html>
