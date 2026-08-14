<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreVoucherRequest;
use App\Http\Requests\UpdateVoucherRequest;
use App\Repositories\VoucherRepository;
use App\Services\VoucherService;
use Illuminate\Http\Request;

class AdminVoucherController extends Controller
{
    protected VoucherRepository $voucherRepository;
    protected VoucherService $voucherService;

    public function __construct(VoucherRepository $voucherRepository, VoucherService $voucherService)
    {
        $this->voucherRepository = $voucherRepository;
        $this->voucherService = $voucherService;
    }

    public function index(Request $request)
    {
        $filters = [
            'search' => $request->get('search'),
            'type' => $request->get('type'),
            'category' => $request->get('category'),
            'status' => $request->get('status'),
            'is_expired' => $request->get('is_expired'),
            'per_page' => $request->get('per_page', 15),
        ];

        $vouchers = $this->voucherRepository->getAll($filters);
        $statistics = $this->voucherRepository->getStatistics();

        return view('admin.vouchers.index', compact('vouchers', 'statistics'));
    }

    public function create()
    {
        return view('admin.vouchers.create');
    }

    public function store(StoreVoucherRequest $request)
    {
        $result = $this->voucherService->createVoucher($request->all(), auth()->id());

        if ($result['success']) {
            return redirect()
                ->route('admin.vouchers.index')
                ->with('success', $result['message']);
        }

        return redirect()
            ->back()
            ->withInput()
            ->with('error', $result['message']);
    }

    public function show($id)
    {
        $voucher = $this->voucherRepository->findById($id);

        if (!$voucher) {
            return redirect()
                ->route('admin.vouchers.index')
                ->with('error', 'Voucher not found');
        }

        return view('admin.vouchers.show', compact('voucher'));
    }

    public function edit($id)
    {
        $voucher = $this->voucherRepository->findById($id);

        if (!$voucher) {
            return redirect()
                ->route('admin.vouchers.index')
                ->with('error', 'Voucher not found');
        }

        return view('admin.vouchers.edit', compact('voucher'));
    }

    public function update(UpdateVoucherRequest $request, $id)
    {
        $result = $this->voucherService->updateVoucher($id, $request->all());

        if ($result['success']) {
            return redirect()
                ->route('admin.vouchers.index')
                ->with('success', $result['message']);
        }

        return redirect()
            ->back()
            ->withInput()
            ->with('error', $result['message']);
    }

    public function destroy($id)
    {
        $result = $this->voucherService->deleteVoucher($id);

        if ($result['success']) {
            return redirect()
                ->route('admin.vouchers.index')
                ->with('success', $result['message']);
        }

        return redirect()
            ->back()
            ->with('error', $result['message']);
    }

    public function toggleStatus($id)
    {
        $result = $this->voucherService->toggleVoucherStatus($id);

        return response()->json($result);
    }

    public function toggleDisplay($id)
    {
        $result = $this->voucherService->toggleVoucherDisplay($id);

        return response()->json($result);
    }
}
