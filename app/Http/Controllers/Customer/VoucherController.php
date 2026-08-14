<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\ClaimVoucherRequest;
use App\Repositories\VoucherRepository;
use App\Services\VoucherService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class VoucherController extends Controller
{
    protected VoucherRepository $voucherRepository;
    protected VoucherService $voucherService;

    public function __construct(VoucherRepository $voucherRepository, VoucherService $voucherService)
    {
        $this->voucherRepository = $voucherRepository;
        $this->voucherService = $voucherService;
    }

    public function index()
    {
        $vouchers = $this->voucherRepository->getActiveVouchersForFrontend(12);
        
        $userVouchers = [];
        if (auth()->check()) {
            $userVouchers = $this->voucherRepository->getUserVouchers(auth()->id());
        }

        return view('customer.vouchers.index', compact('vouchers', 'userVouchers'));
    }

    public function claim(ClaimVoucherRequest $request): JsonResponse
    {
        if (!auth()->check()) {
            return response()->json([
                'success' => false,
                'message' => 'Silakan login terlebih dahulu untuk klaim voucher',
                'redirect' => route('login'),
            ], 401);
        }

        $result = $this->voucherService->claimVoucher(auth()->id(), $request->voucher_id);

        return response()->json($result, $result['success'] ? 200 : 400);
    }

    public function myVouchers()
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $userVouchers = $this->voucherRepository->getUserVouchers(auth()->id());

        return view('customer.vouchers.my-vouchers', compact('userVouchers'));
    }

    public function getAvailableForCheckout(Request $request): JsonResponse
    {
        if (!auth()->check()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 401);
        }

        $cartTotal = (float) $request->get('cart_total', 0);
        $vouchers = $this->voucherRepository->getAvailableForCheckout(auth()->id(), $cartTotal);

        return response()->json([
            'success' => true,
            'data' => $vouchers,
        ]);
    }

    public function getAllClaimedVouchers(Request $request): JsonResponse
    {
        if (!auth()->check()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 401);
        }

        $vouchers = $this->voucherRepository->getAllClaimedVouchersForCheckout(auth()->id());

        return response()->json([
            'success' => true,
            'data' => $vouchers,
        ]);
    }

    public function validate(Request $request): JsonResponse
    {
        if (!auth()->check()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 401);
        }

        $request->validate([
            'code' => 'required|string',
            'cart_total' => 'required|numeric|min:0',
        ]);

        $byId = $request->boolean('by_id');

        // Find voucher by code or by ID
        if ($byId) {
            $voucher = $this->voucherRepository->findById((int) $request->code);
        } else {
            $voucher = $this->voucherRepository->findByCode($request->code);
        }

        if (!$voucher) {
            return response()->json([
                'success' => false,
                'message' => 'Kode voucher tidak valid',
            ], 400);
        }

        // Auto-claim voucher if not yet claimed (user must claim before using)
        $alreadyClaimed = $this->voucherRepository->isVoucherClaimedByUser(auth()->id(), $voucher->id);
        if (!$alreadyClaimed) {
            $claimResult = $this->voucherService->claimVoucher(auth()->id(), $voucher->id);
            if (!$claimResult['success']) {
                return response()->json([
                    'success' => false,
                    'message' => $claimResult['message'],
                ], 400);
            }
        }

        // Validate voucher for checkout
        $result = $this->voucherService->validateVoucherForCheckout(
            auth()->id(),
            $voucher->id,
            (float) $request->cart_total
        );

        if ($result['success']) {
            $discount = $result['data']['discount'] ?? 0;
            $result['data'] = [
                'voucher' => $voucher,
                'discount' => $discount,
                'discount_value' => $discount,
                'already_claimed' => $alreadyClaimed,
            ];
        }

        return response()->json($result, $result['success'] ? 200 : 400);
    }
}
