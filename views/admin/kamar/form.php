<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= $isEdit ? 'Ubah' : 'Tambah' ?> Kamar | KosManager</title>
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
<section class="max-w-3xl mx-auto bg-surface-container-lowest p-space-lg rounded-xl shadow-sm">
<a class="text-secondary hover:underline" href="<?= base_url('/kamar') ?>">← Kembali ke kamar</a>
<h1 class="mt-space-md font-headline-lg text-on-surface"><?= $isEdit ? 'Ubah Data Kamar' : 'Tambah Kamar' ?></h1>
<?php if (!empty($formError)): ?><p class="mt-space-md p-space-md rounded-lg bg-error-container text-on-error-container" role="alert"><?= $escape($formError) ?></p><?php endif; ?>
<form class="mt-space-lg flex flex-col gap-space-md" action="<?= base_url($isEdit ? '/kamar/update' : '/kamar') ?>" method="post">
<input type="hidden" name="csrf_token" value="<?= $escape(csrf_token()) ?>">
<?php if ($isEdit): ?><input type="hidden" name="id" value="<?= (int) $kamar->id ?>"><?php endif; ?>
<label class="flex flex-col gap-1"><span class="font-label-md">Properti Kos</span>
<select class="p-space-sm rounded-lg bg-surface-container-low text-on-surface" name="kos_id" required>
<option value="">Pilih kos</option>
<?php foreach ($kosList as $property): ?>
<option value="<?= (int) $property['id'] ?>" <?= (string) $formData['kos_id'] === (string) $property['id'] ? 'selected' : '' ?>><?= $escape($property['nama_kos']) ?></option>
<?php endforeach; ?>
</select></label>
<label class="flex flex-col gap-1"><span class="font-label-md">Nomor Kamar</span><input class="p-space-sm rounded-lg bg-surface-container-low text-on-surface" name="nomor_kamar" required maxlength="50" value="<?= $escape($formData['nomor_kamar']) ?>"></label>
<label class="flex flex-col gap-1"><span class="font-label-md">Tipe Kamar</span><input class="p-space-sm rounded-lg bg-surface-container-low text-on-surface" name="tipe" required maxlength="100" value="<?= $escape($formData['tipe']) ?>"></label>
<label class="flex flex-col gap-1"><span class="font-label-md">Tarif Bulanan (Rp)</span><input class="p-space-sm rounded-lg bg-surface-container-low text-on-surface" name="harga_bulanan" type="number" min="1" step="1000" required value="<?= $escape($formData['harga_bulanan']) ?>"></label>
<p class="text-body-sm text-on-surface-variant">Status tersedia/terisi diatur otomatis berdasarkan data penghuni.</p>
<div class="flex gap-space-sm"><button class="px-space-md py-space-sm rounded-lg bg-secondary text-on-secondary font-label-md" type="submit">Simpan</button><a class="px-space-md py-space-sm rounded-lg bg-surface-container text-on-surface" href="<?= base_url('/kamar') ?>">Batal</a></div>
</form>
</section>
</main>
</div>
</body>
</html>
