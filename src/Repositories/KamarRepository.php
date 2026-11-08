<?php

declare(strict_types=1);

namespace KosManager\Repositories;

use InvalidArgumentException;
use KosManager\Models\Kamar;
use PDO;

final class KamarRepository
{
    public function __construct(private readonly PDO $connection)
    {
    }

    /**
     * @return list<Kamar>
     */
    public function findAll(): array
    {
        $statement = $this->connection->query(
            'SELECT id, kos_id, nomor_kamar, tipe, harga_bulanan, status
             FROM kamar
             ORDER BY kos_id, nomor_kamar'
        );

        return array_map(
            static fn (array $row): Kamar => Kamar::fromRow($row),
            $statement->fetchAll()
        );
    }

    /**
     * @return list<array{id: int|string, nama_kos: string}>
     */
    public function findAllKos(): array
    {
        $statement = $this->connection->query(
            'SELECT id, nama_kos
             FROM kos
             ORDER BY nama_kos'
        );

        return $statement->fetchAll();
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function findAllWithDetails(): array
    {
        $statement = $this->connection->query(
            'SELECT kamar.id,
                    kamar.kos_id,
                    kamar.nomor_kamar,
                    kamar.tipe,
                    kamar.harga_bulanan,
                    kamar.status,
                    kos.nama_kos,
                    penghuni.nama AS nama_penghuni
             FROM kamar
             INNER JOIN kos ON kos.id = kamar.kos_id
             LEFT JOIN penghuni ON penghuni.kamar_id = kamar.id
             ORDER BY kamar.kos_id, kamar.nomor_kamar'
        );

        return $statement->fetchAll();
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function findVacantWithDetails(): array
    {
        $statement = $this->connection->query(
            'SELECT kamar.id,
                    kamar.kos_id,
                    kamar.nomor_kamar,
                    kamar.tipe,
                    kamar.harga_bulanan,
                    kamar.status,
                    kos.nama_kos
             FROM kamar
             INNER JOIN kos ON kos.id = kamar.kos_id
             WHERE kamar.status = \'kosong\'
             ORDER BY kamar.kos_id, kamar.nomor_kamar'
        );

        return $statement->fetchAll();
    }

    /**
     * @return array{kos: int, kamar: int, kosong: int, terisi: int}
     */
    public function getDashboardStats(): array
    {
        $statement = $this->connection->query(
            'SELECT (SELECT COUNT(*) FROM kos) AS kos,
                    COUNT(*) AS kamar,
                    COALESCE(SUM(kamar.status = \'kosong\'), 0) AS kosong,
                    COALESCE(SUM(kamar.status = \'terisi\'), 0) AS terisi
             FROM kamar'
        );
        $stats = $statement->fetch();

        return [
            'kos' => (int) $stats['kos'],
            'kamar' => (int) $stats['kamar'],
            'kosong' => (int) $stats['kosong'],
            'terisi' => (int) $stats['terisi'],
        ];
    }

    public function findById(int $id): ?Kamar
    {
        $this->assertValidId($id);
        $statement = $this->connection->prepare(
            'SELECT id, kos_id, nomor_kamar, tipe, harga_bulanan, status
             FROM kamar
             WHERE id = :id'
        );
        $statement->execute(['id' => $id]);
        $row = $statement->fetch();

        return $row === false ? null : Kamar::fromRow($row);
    }

    public function create(Kamar $kamar): int
    {
        $this->assertValidKamar($kamar);
        $statement = $this->connection->prepare(
            'INSERT INTO kamar (kos_id, nomor_kamar, tipe, harga_bulanan)
             VALUES (:kos_id, :nomor_kamar, :tipe, :harga_bulanan)'
        );
        $statement->execute($this->values($kamar));

        return (int) $this->connection->lastInsertId();
    }

    public function update(Kamar $kamar): bool
    {
        if ($kamar->id === null) {
            throw new InvalidArgumentException('An ID is required to update a room.');
        }

        $this->assertValidId($kamar->id);
        $this->assertValidKamar($kamar);
        $values = $this->values($kamar);
        $values['id'] = $kamar->id;
        $statement = $this->connection->prepare(
            'UPDATE kamar
             SET kos_id = :kos_id,
                 nomor_kamar = :nomor_kamar,
                 tipe = :tipe,
                 harga_bulanan = :harga_bulanan
             WHERE id = :id'
        );
        $statement->execute($values);

        return $statement->rowCount() > 0 || $this->findById($kamar->id) !== null;
    }

    public function delete(int $id): bool
    {
        $this->assertValidId($id);
        $statement = $this->connection->prepare('DELETE FROM kamar WHERE id = :id');
        $statement->execute(['id' => $id]);

        return $statement->rowCount() > 0;
    }

    /**
     * @return array<string, int|string>
     */
    private function values(Kamar $kamar): array
    {
        return [
            'kos_id' => $kamar->kosId,
            'nomor_kamar' => trim($kamar->nomorKamar),
            'tipe' => trim($kamar->tipe),
            'harga_bulanan' => $kamar->hargaBulanan,
        ];
    }

    private function assertValidKamar(Kamar $kamar): void
    {
        $nomorKamar = trim($kamar->nomorKamar);
        $tipe = trim($kamar->tipe);

        if ($kamar->kosId < 1) {
            throw new InvalidArgumentException('Properti kos wajib dipilih.');
        }

        if (!$this->kosExists($kamar->kosId)) {
            throw new InvalidArgumentException('Properti kos yang dipilih tidak ditemukan.');
        }

        if ($nomorKamar === '') {
            throw new InvalidArgumentException('Nomor kamar wajib diisi.');
        }

        if (mb_strlen($nomorKamar) > 50) {
            throw new InvalidArgumentException('Nomor kamar maksimal 50 karakter.');
        }

        if ($tipe === '') {
            throw new InvalidArgumentException('Tipe kamar wajib diisi.');
        }

        if (mb_strlen($tipe) > 100) {
            throw new InvalidArgumentException('Tipe kamar maksimal 100 karakter.');
        }

        if (!is_numeric($kamar->hargaBulanan)) {
            throw new InvalidArgumentException('Harga bulanan harus berupa angka.');
        }

        $harga = (float) $kamar->hargaBulanan;

        if ($harga <= 0) {
            throw new InvalidArgumentException('Harga bulanan harus lebih dari nol.');
        }

        if ($harga > 9999999999.99) {
            throw new InvalidArgumentException('Harga bulanan melebihi batas maksimum.');
        }

        if ($this->existsByNomorKamar($kamar->kosId, $nomorKamar, $kamar->id)) {
            throw new InvalidArgumentException('Nomor kamar sudah digunakan pada properti kos ini.');
        }
    }

    private function kosExists(int $kosId): bool
    {
        $statement = $this->connection->prepare('SELECT 1 FROM kos WHERE id = :id LIMIT 1');
        $statement->execute(['id' => $kosId]);

        return $statement->fetchColumn() !== false;
    }

    private function existsByNomorKamar(int $kosId, string $nomorKamar, ?int $exceptId = null): bool
    {
        $sql = 'SELECT 1 FROM kamar WHERE kos_id = :kos_id AND nomor_kamar = :nomor_kamar';
        $parameters = [
            'kos_id' => $kosId,
            'nomor_kamar' => $nomorKamar,
        ];

        if ($exceptId !== null) {
            $sql .= ' AND id <> :id';
            $parameters['id'] = $exceptId;
        }

        $statement = $this->connection->prepare($sql . ' LIMIT 1');
        $statement->execute($parameters);

        return $statement->fetchColumn() !== false;
    }

    private function assertValidId(int $id): void
    {
        if ($id < 1) {
            throw new InvalidArgumentException('ID kamar harus berupa bilangan positif.');
        }
    }
}
