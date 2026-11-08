<?php

declare(strict_types=1);

namespace KosManager\Models;

final class Kamar
{
    public function __construct(
        public readonly ?int $id,
        public readonly int $kosId,
        public readonly string $nomorKamar,
        public readonly string $tipe,
        public readonly string $hargaBulanan,
        public readonly string $status = 'kosong'
    ) {
    }

    /**
     * @param array<string, mixed> $row
     */
    public static function fromRow(array $row): self
    {
        return new self(
            (int) $row['id'],
            (int) $row['kos_id'],
            (string) $row['nomor_kamar'],
            (string) $row['tipe'],
            (string) $row['harga_bulanan'],
            (string) $row['status']
        );
    }
}
