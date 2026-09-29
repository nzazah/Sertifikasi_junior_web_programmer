<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['user_id', 'address', 'payment_proof', 'status', 'total'])]
class Order extends Model
{
    public const STATUSES = [
        'menunggu_bayar',
        'menunggu_konfirmasi',
        'diproses',
        'dikirim',
        'selesai',
        'dibatalkan',
    ];

    public static function statusLabels(): array
    {
        return [
            'menunggu_bayar' => 'Menunggu bayar',
            'menunggu_konfirmasi' => 'Menunggu konfirmasi',
            'diproses' => 'Diproses',
            'dikirim' => 'Dikirim',
            'selesai' => 'Selesai',
            'dibatalkan' => 'Dibatalkan',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function number(): string
    {
        return 'ORD-' . str_pad($this->id, 5, '0', STR_PAD_LEFT);
    }

    /** Bukti bayar hanya boleh diunggah saat pesanan masih menunggu bayar. */
    public function canUploadProof(): bool
    {
        return $this->status === 'menunggu_bayar';
    }

    /** Status berikutnya yang boleh dipilih penjual dari status sekarang. */
    public function allowedTransitions(): array
    {
        return match ($this->status) {
            'menunggu_bayar' => ['dibatalkan'],
            'menunggu_konfirmasi' => ['diproses', 'dibatalkan'],
            'diproses' => ['dikirim', 'dibatalkan'],
            'dikirim' => ['selesai'],
            default => [],
        };
    }

    public function statusLabel(): string
    {
        return self::statusLabels()[$this->status] ?? $this->status;
    }

    public function statusBadge(): string
    {
        return match ($this->status) {
            'menunggu_bayar' => 'bg-amber-50 text-amber-700',
            'menunggu_konfirmasi' => 'bg-orange-50 text-orange-700',
            'diproses' => 'bg-blue-50 text-blue-700',
            'dikirim' => 'bg-indigo-50 text-indigo-700',
            'selesai' => 'bg-green-50 text-green-700',
            'dibatalkan' => 'bg-red-50 text-red-700',
            default => 'bg-gray-100 text-gray-700',
        };
    }
}