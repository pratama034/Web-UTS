<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Login Pengelola | KosManager</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&display=swap" rel="stylesheet">
<script src="https://cdn.tailwindcss.com"></script>
<script src="<?= base_url('/public/assets/js/admin-theme.js') ?>"></script>
<link rel="stylesheet" href="<?= base_url('/public/assets/css/style.css') ?>">
<link rel="stylesheet" href="<?= base_url('/public/assets/css/admin.css') ?>">
</head>
<body class="bg-surface-container-low min-h-screen flex items-center justify-center p-space-md font-body-md text-on-surface antialiased">
<div class="w-full max-w-md bg-surface-container-lowest rounded-2xl shadow-lg border border-surface-container overflow-hidden">
    <div class="p-space-xl flex flex-col items-center text-center border-b border-surface-container">
        <img alt="KosManager" class="w-12 h-12 rounded-xl object-contain mb-space-sm shadow-sm" src="<?= base_url('/public/assets/images/logo.svg') ?>"/>
        <h1 class="font-headline-lg text-on-surface">KosManager</h1>
        <p class="text-body-sm text-on-surface-variant mt-1">Area Portal Pengelola & Admin Kos</p>
    </div>

    <div class="p-space-xl">
        <?php if (!empty($error)): ?>
            <div class="mb-space-md p-space-md rounded-xl bg-error-container text-on-error-container text-sm flex items-center gap-space-xs" role="alert">
                <span class="material-symbols-outlined text-[18px]">error</span>
                <span><?= $escape($error) ?></span>
            </div>
        <?php endif; ?>

        <form class="flex flex-col gap-space-md" action="<?= base_url('/login') ?>" method="post">
            <input type="hidden" name="csrf_token" value="<?= $escape(csrf_token()) ?>">
            <label class="flex flex-col gap-1">
                <span class="font-label-md text-on-surface">Username</span>
                <div class="relative flex items-center">
                    <input class="w-full p-space-sm pl-10 rounded-lg bg-surface-container-low text-on-surface border border-surface-container focus:outline-none focus:ring-2 focus:ring-secondary text-sm" type="text" name="username" required autofocus placeholder="Masukkan username" value="<?= $escape($username ?? '') ?>">
                    <span class="absolute left-3 material-symbols-outlined text-on-surface-variant text-[18px] pointer-events-none">person</span>
                </div>
            </label>

            <label class="flex flex-col gap-1">
                <span class="font-label-md text-on-surface">Password</span>
                <div class="relative flex items-center">
                    <input class="w-full p-space-sm pl-10 rounded-lg bg-surface-container-low text-on-surface border border-surface-container focus:outline-none focus:ring-2 focus:ring-secondary text-sm" type="password" name="password" required placeholder="Masukkan password">
                    <span class="absolute left-3 material-symbols-outlined text-on-surface-variant text-[18px] pointer-events-none">lock</span>
                </div>
            </label>

            <button class="w-full py-space-sm px-space-md rounded-lg bg-secondary text-on-secondary font-headline-sm hover:opacity-90 transition-opacity mt-space-xs shadow-sm" type="submit">
                Masuk ke Dashboard
            </button>
        </form>

        <div class="mt-space-lg p-space-sm rounded-lg bg-surface-container-low border border-surface-container text-xs text-on-surface-variant flex flex-col gap-1">
            <span class="font-semibold text-on-surface">Akun Demo Pengelola:</span>
            <span>Username: <strong class="text-on-surface font-mono">admin</strong></span>
            <span>Password: <strong class="text-on-surface font-mono">admin123</strong></span>
        </div>

        <div class="mt-space-lg text-center">
            <a class="text-secondary hover:underline text-sm inline-flex items-center gap-1" href="<?= base_url('/') ?>">
                <span class="material-symbols-outlined text-[16px]">arrow_back</span>
                <span>Kembali ke Beranda Publik</span>
            </a>
        </div>
    </div>
</div>
</body>
</html>

