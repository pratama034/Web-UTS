<header class="fixed top-0 left-72 right-0 h-16 bg-surface-container-lowest/90 backdrop-blur-xl z-40 shadow-[0_1px_8px_rgba(0,0,0,0.04)]">
<div class="h-16 w-full px-gutter-md flex items-center justify-between gap-space-md">
<a class="flex items-center gap-space-xs text-on-surface hover:text-secondary" href="<?= base_url('/admin/dashboard') ?>">
<span class="material-symbols-outlined text-secondary" aria-hidden="true">home_work</span>
<span class="font-headline-sm">KosManager</span>
</a>
<nav class="flex items-center gap-space-sm" aria-label="Aksi cepat">
<a class="px-space-md py-space-sm rounded-lg bg-secondary text-on-secondary font-label-md hover:bg-secondary-container" href="<?= base_url('/kamar/tambah') ?>">Tambah Kamar</a>
<a class="px-space-md py-space-sm rounded-lg bg-surface-container-low text-on-surface font-label-md hover:bg-surface-container" href="<?= base_url('/penghuni/tambah') ?>">Check-in Penghuni</a>
<a class="px-space-md py-space-sm rounded-lg bg-error-container text-on-error-container font-label-md inline-flex items-center gap-1 hover:opacity-90 transition-opacity" href="<?= base_url('/logout') ?>" title="Keluar dari sesi admin">
<span class="material-symbols-outlined text-[16px]" aria-hidden="true">logout</span>
<span>Keluar</span>
</a>
</nav>
</div>
</header>
