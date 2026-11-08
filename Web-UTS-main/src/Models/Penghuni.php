<?php

declare(strict_types=1);

namespace KosManager\Models;

final class Penghuni
{
    public function __construct(
        public readonly ?int $id,
        public readonly string $nama,
        public readonly string $noHp,
        public readonly string $nik,
        public readonly ?int $kamarId = null
    ) {
    }

    /**
     * @param array<string, mixed> $row
     */
    public static function fromRow(array $row): self
    {
        return new self(
            (int) $row['id'],
            (string) $row['nama'],
            (string) $row['no_hp'],
            (string) $row['nik'],
            isset($row['kamar_id']) ? (int) $row['kamar_id'] : null
        );
    }
}
