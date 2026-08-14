<?php

namespace App\Services;

use App\Repositories\VoucherRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Exception;

class VoucherService
{
    protected VoucherRepository $voucherRepository;

    public function __construct(VoucherRepository $voucherRepository)
    {
        $this->voucherRepository = $voucherRepository;
    }

    public function createVoucher(array $data, int $creatorId): array
    {
        try {
            DB::beginTransaction();

            $data['created_by'] = $creatorId;
            $data['is_displayed'] = $data['is_displayed'] ?? true;

            $voucher = $this->voucherRepository->create($data);

            DB::commit();

            return [
                'success' => true,
                'message' => 'Voucher created successfully',
                'data' => $voucher,
            ];
        } catch (Exception $e) {
            DB::rollBack();
            return [
                'success' => false,
                'message' => 'Failed to create voucher: ' . $e->getMessage(),
            ];
        }
    }

    public function updateVoucher(int $id, array $data): array
    {
        try {
            DB::beginTransaction();

            $voucher = $this->voucherRepository->findById($id);

            if (!$voucher) {
                return [
                    'success' => false,
                    'message' => 'Voucher not found',
                ];
            }

            $updated = $this->voucherRepository->update($id, $data);

            DB::commit();

            return [
                'success' => $updated,
                'message' => $updated ? 'Voucher updated successfully' : 'Failed to update voucher',
                'data' => $updated ? $this->voucherRepository->findById($id) : null,
            ];
        } catch (Exception $e) {
            DB::rollBack();
            return [
                'success' => false,
                'message' => 'Failed to update voucher: ' . $e->getMessage(),
            ];
        }
    }

    public function deleteVoucher(int $id): array
    {
        try {
            DB::beginTransaction();

            $voucher = $this->voucherRepository->findById($id);

            if (!$voucher) {
                return [
                    'success' => false,
                    'message' => 'Voucher not found',
                ];
            }

            if ($voucher->thumbnail) {
                Storage::disk('public')->delete($voucher->thumbnail);
            }

            $deleted = $this->voucherRepository->delete($id);

            DB::commit();

            return [
                'success' => $deleted,
                'message' => $deleted ? 'Voucher deleted successfully' : 'Failed to delete voucher',
            ];
        } catch (Exception $e) {
            DB::rollBack();
            return [
                'success' => false,
                'message' => 'Failed to delete voucher: ' . $e->getMessage(),
            ];
        }
    }

    public function toggleVoucherStatus(int $id): array
    {
        try {
            $toggled = $this->voucherRepository->toggleStatus($id);

            return [
                'success' => $toggled,
                'message' => $toggled ? 'Voucher status toggled successfully' : 'Failed to toggle voucher status',
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to toggle voucher status: ' . $e->getMessage(),
            ];
        }
    }

    public function claimVoucher(int $userId, int $voucherId): array
    {
        try {
            DB::beginTransaction();

            $voucher = $this->voucherRepository->findById($voucherId);

            if (!$voucher) {
                return [
                    'success' => false,
                    'message' => 'Voucher not found',
                ];
            }

            if (!$voucher->isActive()) {
                if ($voucher->is_expired) {
                    return [
                        'success' => false,
                        'message' => 'Voucher has expired',
                    ];
                }
                if ($voucher->is_not_started) {
                    return [
                        'success' => false,
                        'message' => 'Voucher is not yet available',
                    ];
                }
                if ($voucher->is_quota_finished) {
                    return [
                        'success' => false,
                        'message' => 'Voucher quota has been finished',
                    ];
                }
                if (!$voucher->is_active) {
                    return [
                        'success' => false,
                        'message' => 'Voucher is not active',
                    ];
                }
            }

            if ($voucher->isClaimedByUser($userId)) {
                return [
                    'success' => false,
                    'message' => 'You have already claimed this voucher',
                ];
            }

            $userVoucher = $this->voucherRepository->claimVoucher($userId, $voucherId);

            if (!$userVoucher) {
                return [
                    'success' => false,
                    'message' => 'Failed to claim voucher',
                ];
            }

            DB::commit();

            return [
                'success' => true,
                'message' => 'Voucher claimed successfully',
                'data' => $userVoucher,
            ];
        } catch (Exception $e) {
            DB::rollBack();
            return [
                'success' => false,
                'message' => 'Failed to claim voucher: ' . $e->getMessage(),
            ];
        }
    }

    public function validateVoucherForCheckout(int $userId, int $voucherId, float $cartTotal): array
    {
        try {
            $voucher = $this->voucherRepository->findById($voucherId);

            if (!$voucher) {
                return [
                    'success' => false,
                    'message' => 'Voucher not found',
                ];
            }

            if (!$voucher->isActive()) {
                if ($voucher->is_expired) {
                    return [
                        'success' => false,
                        'message' => 'Voucher has expired',
                    ];
                }
                if ($voucher->is_not_started) {
                    return [
                        'success' => false,
                        'message' => 'Voucher is not yet available',
                    ];
                }
                if ($voucher->is_quota_finished) {
                    return [
                        'success' => false,
                        'message' => 'Voucher quota has been finished',
                    ];
                }
            }

            if (!$voucher->isClaimedByUser($userId)) {
                return [
                    'success' => false,
                    'message' => 'You need to claim this voucher first',
                ];
            }

            if ($voucher->isUsedByUser($userId)) {
                return [
                    'success' => false,
                    'message' => 'You have already used this voucher',
                ];
            }

            if ($cartTotal < $voucher->minimum_purchase) {
                return [
                    'success' => false,
                    'message' => 'Minimum purchase of Rp ' . number_format($voucher->minimum_purchase, 0, ',', '.') . ' required',
                ];
            }

            $discount = $voucher->calculateDiscount($cartTotal);

            return [
                'success' => true,
                'message' => 'Voucher applied successfully',
                'data' => [
                    'voucher' => $voucher,
                    'discount' => $discount,
                    'type' => $voucher->type,
                ],
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to validate voucher: ' . $e->getMessage(),
            ];
        }
    }

    public function useVoucher(int $userId, int $voucherId): array
    {
        try {
            DB::beginTransaction();

            $used = $this->voucherRepository->markVoucherAsUsed($userId, $voucherId);

            DB::commit();

            return [
                'success' => $used,
                'message' => $used ? 'Voucher used successfully' : 'Failed to use voucher',
            ];
        } catch (Exception $e) {
            DB::rollBack();
            return [
                'success' => false,
                'message' => 'Failed to use voucher: ' . $e->getMessage(),
            ];
        }
    }

    public function toggleVoucherDisplay(int $voucherId): array
    {
        try {
            $voucher = $this->voucherRepository->findById($voucherId);

            if (!$voucher) {
                return [
                    'success' => false,
                    'message' => 'Voucher not found',
                ];
            }

            $voucher->is_displayed = !$voucher->is_displayed;
            $voucher->save();

            return [
                'success' => true,
                'message' => $voucher->is_displayed ? 'Voucher displayed to users' : 'Voucher hidden from users',
                'is_displayed' => $voucher->is_displayed,
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to toggle voucher display: ' . $e->getMessage(),
            ];
        }
    }

    protected function uploadThumbnail($file): string
    {
        $path = $file->store('vouchers', 'public');
        return $path;
    }
}
