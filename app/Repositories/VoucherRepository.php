<?php

namespace App\Repositories;

use App\Models\Voucher;
use App\Models\UserVoucher;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class VoucherRepository
{
    public function getAll(array $filters = []): LengthAwarePaginator
    {
        $query = Voucher::query()->with('creator');

        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('title', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('code', 'like', '%' . $filters['search'] . '%');
            });
        }

        if (!empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        if (!empty($filters['category'])) {
            $query->where('category', $filters['category']);
        }

        if (!empty($filters['status'])) {
            if ($filters['status'] === 'active') {
                $query->where('is_active', true);
            } elseif ($filters['status'] === 'inactive') {
                $query->where('is_active', false);
            }
        }

        if (!empty($filters['is_expired'])) {
            if ($filters['is_expired'] === '1') {
                $query->where('end_date', '<', now());
            } elseif ($filters['is_expired'] === '0') {
                $query->where('end_date', '>=', now());
            }
        }

        $query->orderBy('created_at', 'desc');

        return $query->paginate($filters['per_page'] ?? 15);
    }

    public function getActiveVouchers(): Collection
    {
        return Voucher::where('is_active', true)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->whereColumn('quota', '>', 'used')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getActiveVouchersForFrontend(int $limit = 6): Collection
    {
        return Voucher::where('is_active', true)
            ->where('is_displayed', true)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->whereColumn('quota', '>', 'used')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    public function findById(int $id): ?Voucher
    {
        return Voucher::with('creator')->find($id);
    }

    public function findByCode(string $code): ?Voucher
    {
        return Voucher::where('code', $code)->first();
    }

    public function findBySlug(string $slug): ?Voucher
    {
        return Voucher::where('slug', $slug)->first();
    }

    public function create(array $data): Voucher
    {
        return Voucher::create($data);
    }

    public function update(int $id, array $data): bool
    {
        $voucher = $this->findById($id);
        if (!$voucher) {
            return false;
        }

        return $voucher->update($data);
    }

    public function delete(int $id): bool
    {
        $voucher = $this->findById($id);
        if (!$voucher) {
            return false;
        }

        return $voucher->delete();
    }

    public function toggleStatus(int $id): bool
    {
        $voucher = $this->findById($id);
        if (!$voucher) {
            return false;
        }

        $voucher->is_active = !$voucher->is_active;
        return $voucher->save();
    }

    public function getUserVouchers(int $userId): Collection
    {
        return UserVoucher::with('voucher')
            ->where('user_id', $userId)
            ->whereNotNull('claimed_at')
            ->where('is_used', false)
            ->whereHas('voucher', function ($q) {
                $q->where('is_active', true)
                    ->where('start_date', '<=', now())
                    ->where('end_date', '>=', now());
            })
            ->get();
    }

    public function claimVoucher(int $userId, int $voucherId): ?UserVoucher
    {
        $voucher = $this->findById($voucherId);
        
        if (!$voucher || !$voucher->isActive() || $voucher->isClaimedByUser($userId)) {
            return null;
        }

        $userVoucher = UserVoucher::firstOrCreate([
            'user_id' => $userId,
            'voucher_id' => $voucherId,
        ]);

        if (!$userVoucher->claimed_at) {
            $userVoucher->markAsClaimed();
        }

        return $userVoucher;
    }

    public function getStatistics(): array
    {
        return [
            'total' => Voucher::count(),
            'active' => Voucher::where('is_active', true)->count(),
            'total_claimed' => UserVoucher::whereNotNull('claimed_at')->count(),
            'total_used' => UserVoucher::where('is_used', true)->count(),
        ];
    }

    public function getAvailableForCheckout(int $userId, float $cartTotal): Collection
    {
        return Voucher::where('is_active', true)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->whereHas('userVouchers', function ($q) use ($userId) {
                $q->where('user_id', $userId)
                  ->whereNotNull('claimed_at')
                  ->where('is_used', false);
            })
            ->where('minimum_purchase', '<=', $cartTotal)
            ->get();
    }

    public function getAllClaimedVouchersForCheckout(int $userId): Collection
    {
        return Voucher::where('is_active', true)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->whereHas('userVouchers', function ($q) use ($userId) {
                $q->where('user_id', $userId)
                  ->whereNotNull('claimed_at')
                  ->where('is_used', false);
            })
            ->get();
    }

    public function markVoucherAsUsed(int $userId, int $voucherId): bool
    {
        $userVoucher = UserVoucher::where('user_id', $userId)
            ->where('voucher_id', $voucherId)
            ->whereNotNull('claimed_at')
            ->where('is_used', false)
            ->first();

        if (!$userVoucher) {
            return false;
        }

        $userVoucher->markAsUsed();
        return true;
    }

    public function isVoucherClaimedByUser(int $userId, int $voucherId): bool
    {
        return UserVoucher::where('user_id', $userId)
            ->where('voucher_id', $voucherId)
            ->whereNotNull('claimed_at')
            ->exists();
    }
}
