<?php

declare(strict_types=1);

namespace KosManager\Controllers;

use InvalidArgumentException;
use KosManager\Models\Penghuni;
use KosManager\Repositories\KamarRepository;
use KosManager\Repositories\PenghuniRepository;
use PDOException;
use RuntimeException;
use Throwable;

final class PenghuniController
{
    public function __construct(
        private readonly PenghuniRepository $penghuniRepository,
        private readonly KamarRepository $kamarRepository
    ) {
    }

    public function index(): void
    {
        AuthController::checkAuth();

        $this->renderIndex();
    }

    public function create(): void
    {
        AuthController::checkAuth();

        $this->renderIndex(true);
    }

    public function edit(): void
    {
        AuthController::checkAuth();

        $id = $this->readId($_GET['id'] ?? null);
        $penghuni = $id === null ? null : $this->penghuniRepository->findById($id);

        if ($penghuni === null) {
            $this->notFound();
            return;
        }

        $this->renderIndex(true, null, [
            'id' => $penghuni->id,
            'nama' => $penghuni->nama,
            'no_hp' => $penghuni->noHp,
            'nik' => $penghuni->nik,
            'kamar_id' => $penghuni->kamarId,
        ]);
    }

    public function store(): void
    {
        AuthController::checkAuth();
        AuthController::verifyCsrf('/penghuni');

        $input = $this->readInput();

        try {
            $this->penghuniRepository->create($this->makePenghuni($input));
            set_flash('success', 'Check-in penghuni baru berhasil disimpan dan status kamar diperbarui.');
            $this->redirect('/penghuni');
        } catch (InvalidArgumentException | RuntimeException $exception) {
            $this->renderIndex(true, $exception->getMessage(), $input, 422);
        } catch (PDOException $exception) {
            error_log($exception->__toString());
            $this->renderIndex(true, 'NIK sudah digunakan atau kamar tidak tersedia.', $input, 422);
        }
    }

    public function update(): void
    {
        AuthController::checkAuth();
        AuthController::verifyCsrf('/penghuni');

        $id = $this->readId($_POST['id'] ?? null);

        if ($id === null) {
            $this->notFound();
            return;
        }

        $input = $this->readInput();

        try {
            if (!$this->penghuniRepository->update($this->makePenghuni($input, $id))) {
                $this->notFound();
                return;
            }

            set_flash('success', 'Data penghuni berhasil diperbarui.');
            $this->redirect('/penghuni');
        } catch (InvalidArgumentException | RuntimeException $exception) {
            $input['id'] = $id;
            $this->renderIndex(true, $exception->getMessage(), $input, 422);
        } catch (PDOException $exception) {
            error_log($exception->__toString());
            $input['id'] = $id;
            $this->renderIndex(true, 'NIK sudah digunakan atau kamar tidak tersedia.', $input, 422);
        }
    }

    public function delete(): void
    {
        AuthController::checkAuth();
        AuthController::verifyCsrf('/penghuni');

        $id = $this->readId($_POST['id'] ?? null);

        if ($id === null) {
            $this->notFound();
            return;
        }

        try {
            $this->penghuniRepository->delete($id);
            set_flash('success', 'Checkout penghuni berhasil. Kamar kini berstatus kosong.');
            $this->redirect('/penghuni');
        } catch (Throwable $exception) {
            error_log($exception->__toString());
            set_flash('error', 'Gagal memproses checkout penghuni.');
            $this->redirect('/penghuni');
        }
    }

    private function renderIndex(bool $openForm = false, ?string $formError = null, array $formData = [], int $status = 200): void
    {
        http_response_code($status);
        $residentList = $this->penghuniRepository->findAllWithDetails();
        $availableRooms = $this->kamarRepository->findVacantWithDetails();
        $formData += [
            'id' => null,
            'nama' => '',
            'no_hp' => '',
            'nik' => '',
            'kamar_id' => null,
        ];
        $escape = PublicController::escape(...);
        require dirname(__DIR__, 2) . '/views/admin/penghuni/index.php';
    }

    /**
     * @return array{nama: string, no_hp: string, nik: string, kamar_id: int|null}
     */
    private function readInput(): array
    {
        $roomId = trim((string) ($_POST['kamar_id'] ?? ''));

        return [
            'nama' => trim((string) ($_POST['nama'] ?? '')),
            'no_hp' => trim((string) ($_POST['no_hp'] ?? '')),
            'nik' => trim((string) ($_POST['nik'] ?? '')),
            'kamar_id' => $roomId === '' ? null : ($this->readId($roomId) ?? 0),
        ];
    }

    /**
     * @param array{nama: string, no_hp: string, nik: string, kamar_id: int|null} $input
     */
    private function makePenghuni(array $input, ?int $id = null): Penghuni
    {
        return new Penghuni(
            $id,
            $input['nama'],
            $input['no_hp'],
            $input['nik'],
            $input['kamar_id']
        );
    }

    private function readId(mixed $value): ?int
    {
        $id = filter_var($value, FILTER_VALIDATE_INT);

        return $id !== false && $id > 0 ? $id : null;
    }

    private function redirect(string $location): void
    {
        header('Location: ' . base_url($location), true, 303);
        exit;
    }

    private function notFound(): void
    {
        http_response_code(404);
        echo '404 Not Found';
    }
}