<?php

declare(strict_types=1);

namespace KosManager\Repositories;

use InvalidArgumentException;
use KosManager\Models\Penghuni;
use PDO;
use RuntimeException;
use Throwable;

final class PenghuniRepository
{
    public function __construct(private readonly PDO $connection)
    {
    }

    /**
     * @return list<Penghuni>
     */
    public function findAll(): array
    {
        $statement = $this->connection->query(
            'SELECT id, nama, no_hp, nik, kamar_id
             FROM penghuni
             ORDER BY id DESC'
        );

        return array_map(
            static fn (array $row): Penghuni => Penghuni::fromRow($row),
            $statement->fetchAll()
        );
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function findAllWithDetails(int $limit = 100): array
    {
        if ($limit < 1 || $limit > 500) {
            throw new InvalidArgumentException('Resident result limit must be between 1 and 500.');
        }

        $statement = $this->connection->prepare(
            'SELECT penghuni.id,
                    penghuni.nama,
                    penghuni.no_hp,
                    penghuni.nik,
                    penghuni.kamar_id,
                    kamar.nomor_kamar,
                    kamar.kos_id,
                    kos.nama_kos
             FROM penghuni
             LEFT JOIN kamar ON kamar.id = penghuni.kamar_id
             LEFT JOIN kos ON kos.id = kamar.kos_id
             ORDER BY penghuni.id DESC
             LIMIT :limit'
        );
        $statement->bindValue('limit', $limit, PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetchAll();
    }

    public function findById(int $id): ?Penghuni
    {
        $this->assertValidId($id);
        $statement = $this->connection->prepare(
            'SELECT id, nama, no_hp, nik, kamar_id
             FROM penghuni
             WHERE id = :id'
        );
        $statement->execute(['id' => $id]);
        $row = $statement->fetch();

        return $row === false ? null : Penghuni::fromRow($row);
    }

    public function create(Penghuni $penghuni): int
    {
        $this->assertValidPenghuni($penghuni);

        return $this->transactional(function () use ($penghuni): int {
            if ($penghuni->kamarId !== null) {
                $this->lockRoom($penghuni->kamarId, true);
            }

            $statement = $this->connection->prepare(
                'INSERT INTO penghuni (nama, no_hp, nik, kamar_id)
                 VALUES (:nama, :no_hp, :nik, :kamar_id)'
            );
            $statement->execute($this->values($penghuni));

            if ($penghuni->kamarId !== null) {
                $this->setRoomStatus($penghuni->kamarId, 'terisi');
            }

            return (int) $this->connection->lastInsertId();
        });
    }

    public function update(Penghuni $penghuni): bool
    {
        if ($penghuni->id === null) {
            throw new InvalidArgumentException('An ID is required to update a resident.');
        }

        $this->assertValidId($penghuni->id);
        $this->assertValidPenghuni($penghuni);

        return $this->transactional(function () use ($penghuni): bool {
            $statement = $this->connection->prepare(
                'SELECT kamar_id
                 FROM penghuni
                 WHERE id = :id
                 FOR UPDATE'
            );
            $statement->execute(['id' => $penghuni->id]);
            $current = $statement->fetch();

            if ($current === false) {
                return false;
            }

            $currentRoomId = $current['kamar_id'] === null ? null : (int) $current['kamar_id'];
            $roomIds = array_values(array_unique(array_filter(
                [$currentRoomId, $penghuni->kamarId],
                static fn (?int $roomId): bool => $roomId !== null
            )));
            sort($roomIds, SORT_NUMERIC);

            foreach ($roomIds as $roomId) {
                $this->lockRoom($roomId, $roomId === $penghuni->kamarId && $roomId !== $currentRoomId);
            }

            $update = $this->connection->prepare(
                'UPDATE penghuni
                 SET nama = :nama, no_hp = :no_hp, nik = :nik, kamar_id = :kamar_id
                 WHERE id = :id'
            );
            $values = $this->values($penghuni);
            $values['id'] = $penghuni->id;
            $update->execute($values);

            if ($currentRoomId !== $penghuni->kamarId) {
                if ($currentRoomId !== null) {
                    $this->setRoomStatus($currentRoomId, 'kosong');
                }

                if ($penghuni->kamarId !== null) {
                    $this->setRoomStatus($penghuni->kamarId, 'terisi');
                }
            }

            return true;
        });
    }

    public function delete(int $id): bool
    {
        $this->assertValidId($id);

        return $this->transactional(function () use ($id): bool {
            $statement = $this->connection->prepare(
                'SELECT kamar_id
                 FROM penghuni
                 WHERE id = :id
                 FOR UPDATE'
            );
            $statement->execute(['id' => $id]);
            $resident = $statement->fetch();

            if ($resident === false) {
                return false;
            }

            $roomId = $resident['kamar_id'] === null ? null : (int) $resident['kamar_id'];

            if ($roomId !== null) {
                $this->lockRoom($roomId, false);
            }

            $delete = $this->connection->prepare('DELETE FROM penghuni WHERE id = :id');
            $delete->execute(['id' => $id]);

            if ($roomId !== null) {
                $this->setRoomStatus($roomId, 'kosong');
            }

            return true;
        });
    }

    /**
     * @return array<string, int|string|null>
     */
    private function values(Penghuni $penghuni): array
    {
        return [
            'nama' => trim($penghuni->nama),
            'no_hp' => trim($penghuni->noHp),
            'nik' => $penghuni->nik,
            'kamar_id' => $penghuni->kamarId,
        ];
    }

    private function lockRoom(int $roomId, bool $requireVacant): void
    {
        $statement = $this->connection->prepare(
            'SELECT id, status
             FROM kamar
             WHERE id = :id
             FOR UPDATE'
        );
        $statement->execute(['id' => $roomId]);
        $room = $statement->fetch();

        if ($room === false) {
            throw new RuntimeException('Kamar yang dipilih tidak ditemukan.');
        }

        if ($requireVacant && $room['status'] !== 'kosong') {
            throw new RuntimeException('Penghuni hanya dapat ditempatkan pada kamar kosong.');
        }
    }

    private function setRoomStatus(int $roomId, string $status): void
    {
        $statement = $this->connection->prepare(
            'UPDATE kamar SET status = :status WHERE id = :id'
        );
        $statement->execute([
            'status' => $status,
            'id' => $roomId,
        ]);
    }

    private function assertValidPenghuni(Penghuni $penghuni): void
    {
        if (trim($penghuni->nama) === '' || trim($penghuni->noHp) === '') {
            throw new InvalidArgumentException('Nama dan nomor HP penghuni wajib diisi.');
        }

        if (preg_match('/^\d{16}$/D', $penghuni->nik) !== 1) {
            throw new InvalidArgumentException('NIK harus terdiri dari 16 digit angka.');
        }

        if ($penghuni->kamarId !== null && $penghuni->kamarId < 1) {
            throw new InvalidArgumentException('ID kamar harus berupa bilangan positif.');
        }
    }

    private function assertValidId(int $id): void
    {
        if ($id < 1) {
            throw new InvalidArgumentException('ID penghuni harus berupa bilangan positif.');
        }
    }

    private function transactional(callable $operation): mixed
    {
        $this->connection->beginTransaction();

        try {
            $result = $operation();
            $this->connection->commit();

            return $result;
        } catch (Throwable $exception) {
            if ($this->connection->inTransaction()) {
                $this->connection->rollBack();
            }

            throw $exception;
        }
    }
}
