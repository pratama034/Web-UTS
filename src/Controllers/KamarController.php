<?php

declare(strict_types=1);

namespace KosManager\Controllers;

use InvalidArgumentException;
use KosManager\Models\Kamar;
use KosManager\Repositories\KamarRepository;
use PDOException;
use Throwable;

final class KamarController
{
    public function __construct(private readonly KamarRepository $kamarRepository)
    {
    }

    public function index(): void
    {
        AuthController::checkAuth();

        $kamarList = $this->kamarRepository->findAllWithDetails();
        $escape = self::escape(...);
        require dirname(__DIR__, 2) . '/views/admin/kamar/index.php';
    }

    public function create(): void
    {
        AuthController::checkAuth();

        $this->renderForm();
    }

    public function edit(): void
    {
        AuthController::checkAuth();

        $id = $this->readId($_GET['id'] ?? null);
        $kamar = $id === null ? null : $this->kamarRepository->findById($id);

        if ($kamar === null) {
            $this->notFound();
            return;
        }

        $this->renderForm($kamar);
    }

    public function store(): void
    {
        AuthController::checkAuth();
        AuthController::verifyCsrf('/kamar');

        $input = $this->readInput();

        try {
            $this->kamarRepository->create($this->makeKamar($input));
            set_flash('success', 'Data kamar baru berhasil ditambahkan.');
            $this->redirect('/kamar');
        } catch (InvalidArgumentException $exception) {
            $this->renderForm(null, $exception->getMessage(), $input, 422);
        } catch (PDOException $exception) {
            error_log($exception->__toString());
            $this->renderForm(null, 'Data kamar gagal disimpan karena kendala database.', $input, 422);
        }
    }

    public function update(): void
    {
        AuthController::checkAuth();
        AuthController::verifyCsrf('/kamar');

        $id = $this->readId($_POST['id'] ?? null);

        if ($id === null) {
            $this->notFound();
            return;
        }

        $input = $this->readInput();

        try {
            if (!$this->kamarRepository->update($this->makeKamar($input, $id))) {
                $this->notFound();
                return;
            }

            set_flash('success', 'Data kamar berhasil diperbarui.');
            $this->redirect('/kamar');
        } catch (InvalidArgumentException $exception) {
            $this->renderForm($this->kamarRepository->findById($id), $exception->getMessage(), $input, 422);
        } catch (PDOException $exception) {
            error_log($exception->__toString());
            $this->renderForm(
                $this->kamarRepository->findById($id),
                'Data kamar gagal diperbarui karena kendala database.',
                $input,
                422
            );
        }
    }

    public function delete(): void
    {
        AuthController::checkAuth();
        AuthController::verifyCsrf('/kamar');

        $id = $this->readId($_POST['id'] ?? null);

        if ($id === null) {
            $this->notFound();
            return;
        }

        try {
            $this->kamarRepository->delete($id);
            set_flash('success', 'Data kamar berhasil dihapus.');
        } catch (Throwable $exception) {
            error_log($exception->__toString());
            set_flash('error', 'Kamar tidak dapat dihapus karena sedang ditempati penghuni atau memiliki relasi data.');
        }

        $this->redirect('/kamar');
    }

    /**
     * @param array{kos_id: int, nomor_kamar: string, tipe: string, harga_bulanan: string} $formData
     */
    private function renderForm(?Kamar $kamar = null, ?string $formError = null, array $formData = [], int $status = 200): void
    {
        http_response_code($status);
        $kosList = $this->kamarRepository->findAllKos();
        $formData += [
            'kos_id' => $kamar?->kosId ?? ($kosList[0]['id'] ?? ''),
            'nomor_kamar' => $kamar?->nomorKamar ?? '',
            'tipe' => $kamar?->tipe ?? 'Standar',
            'harga_bulanan' => $kamar?->hargaBulanan ?? '',
        ];
        $isEdit = $kamar !== null;
        $escape = self::escape(...);
        require dirname(__DIR__, 2) . '/views/admin/kamar/form.php';
    }

    /**
     * @return array{kos_id: int, nomor_kamar: string, tipe: string, harga_bulanan: string}
     */
    private function readInput(): array
    {
        return [
            'kos_id' => $this->readId($_POST['kos_id'] ?? null) ?? 0,
            'nomor_kamar' => trim((string) ($_POST['nomor_kamar'] ?? '')),
            'tipe' => trim((string) ($_POST['tipe'] ?? '')),
            'harga_bulanan' => trim((string) ($_POST['harga_bulanan'] ?? '')),
        ];
    }

    /**
     * @param array{kos_id: int, nomor_kamar: string, tipe: string, harga_bulanan: string} $input
     */
    private function makeKamar(array $input, ?int $id = null): Kamar
    {
        return new Kamar(
            $id,
            $input['kos_id'],
            $input['nomor_kamar'],
            $input['tipe'],
            $input['harga_bulanan']
        );
    }

    private function readId(mixed $value): ?int
    {
        $id = filter_var($value, FILTER_VALIDATE_INT);

        return $id !== false && $id > 0 ? $id : null;
    }

    private static function escape(mixed $value): string
    {
        return htmlspecialchars((string) ($value ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
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
