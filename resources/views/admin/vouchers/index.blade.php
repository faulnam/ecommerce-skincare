@extends('layouts.admin')

@section('page-title', 'Manajemen Voucher')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="fas fa-ticket-alt me-2"></i>Daftar Voucher</span>
        <a href="{{ route('admin.vouchers.create') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus me-1"></i>Tambah Voucher
        </a>
    </div>
    <div class="card-body">
        <!-- Filters -->
        <form action="{{ route('admin.vouchers.index') }}" method="GET" class="row g-3 mb-4">
            <div class="col-md-3">
                <input type="text" class="form-control" name="search" placeholder="Cari voucher..." value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <select class="form-select" name="category">
                    <option value="">Semua Kategori</option>
                    <option value="all" {{ request('category') == 'all' ? 'selected' : '' }}>Semua Kategori</option>
                    <option value="hijab" {{ request('category') == 'hijab' ? 'selected' : '' }}>Hijab</option>
                    <option value="accessories" {{ request('category') == 'accessories' ? 'selected' : '' }}>Accessories</option>
                    <option value="shoes" {{ request('category') == 'shoes' ? 'selected' : '' }}>Shoes</option>
                    <option value="new_arrivals" {{ request('category') == 'new_arrivals' ? 'selected' : '' }}>New Arrivals</option>
                </select>
            </div>
            <div class="col-md-2">
                <select class="form-select" name="type">
                    <option value="">Semua Tipe</option>
                    <option value="fixed" {{ request('type') == 'fixed' ? 'selected' : '' }}>Diskon Tetap</option>
                    <option value="percent" {{ request('type') == 'percent' ? 'selected' : '' }}>Diskon Persentase</option>
                    <option value="cashback" {{ request('type') == 'cashback' ? 'selected' : '' }}>Cashback Coin</option>
                </select>
            </div>
            <div class="col-md-2">
                <select class="form-select" name="status">
                    <option value="">Semua Status</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Aktif</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-outline-primary w-100">
                    <i class="fas fa-search me-1"></i>Filter
                </button>
            </div>
        </form>

        <!-- Vouchers Table -->
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Judul</th>
                        <th>Kode</th>
                        <th>Kategori</th>
                        <th>Tipe</th>
                        <th>Diskon</th>
                        <th>Kuota</th>
                        <th>Status</th>
                        <th>Tampil</th>
                        <th>Berlaku Sampai</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($vouchers as $voucher)
                        <tr>
                            <td>
                                <strong>{{ $voucher->title }}</strong>
                                @if($voucher->description)
                                    <br><small class="text-muted">{{ Str::limit($voucher->description, 50) }}</small>
                                @endif
                            </td>
                            <td>
                                <code class="bg-light p-1 rounded">{{ $voucher->code }}</code>
                                <button onclick="copyToClipboard('{{ $voucher->code }}')" class="btn btn-sm btn-link p-0 ms-1">
                                    <i class="fas fa-copy text-muted"></i>
                                </button>
                            </td>
                            <td>
                                <span class="badge bg-info">
                                    {{ $voucher->category_label }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-{{ $voucher->type === 'fixed' ? 'primary' : ($voucher->type === 'percent' ? 'success' : 'warning') }}">
                                    {{ ucfirst($voucher->type) }}
                                </span>
                            </td>
                            <td>
                                @if($voucher->type === 'fixed')
                                    Rp {{ number_format($voucher->discount_value, 0, ',', '.') }}
                                @elseif($voucher->type === 'percent')
                                    {{ $voucher->discount_value }}%
                                @else
                                    {{ $voucher->cashback_coin }} Coin
                                @endif
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="progress flex-grow-1" style="height: 6px;">
                                        <div class="progress-bar bg-{{ $voucher->quota_percentage > 80 ? 'danger' : 'success' }}" 
                                             style="width: {{ $voucher->quota_percentage }}%"></div>
                                    </div>
                                    <span class="ms-2 small">{{ $voucher->remaining_quota }}/{{ $voucher->quota }}</span>
                                </div>
                            </td>
                            <td>
                                @if($voucher->is_active)
                                    <span class="badge bg-success">Aktif</span>
                                @else
                                    <span class="badge bg-secondary">Nonaktif</span>
                                @endif
                            </td>
                            <td>
                                <button onclick="toggleDisplay({{ $voucher->id }})" class="btn btn-sm btn-{{ $voucher->is_displayed ? 'success' : 'outline-secondary' }}" title="Toggle Display">
                                    <i class="fas fa-{{ $voucher->is_displayed ? 'eye' : 'eye-slash' }}"></i>
                                </button>
                            </td>
                            <td>
                                <small>{{ $voucher->end_date->format('d M Y H:i') }}</small>
                            </td>
                            <td>
                                <div class="btn-group">
                                    <a href="{{ route('admin.vouchers.edit', $voucher->id) }}" class="btn btn-sm btn-warning" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button onclick="toggleStatus({{ $voucher->id }})" class="btn btn-sm btn-{{ $voucher->is_active ? 'secondary' : 'success' }}" title="Toggle Status">
                                        <i class="fas fa-{{ $voucher->is_active ? 'pause' : 'play' }}"></i>
                                    </button>
                                    <form action="{{ route('admin.vouchers.destroy', $voucher->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus voucher ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center py-4">
                                <div class="text-muted">
                                    <i class="fas fa-ticket-alt fa-3x mb-3"></i>
                                    <p>Tidak ada voucher ditemukan</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-between align-items-center mt-4">
            <small class="text-muted">Menampilkan {{ $vouchers->firstItem() }} sampai {{ $vouchers->lastItem() }} dari {{ $vouchers->total() }} voucher</small>
            {{ $vouchers->appends(request()->except('page'))->links('pagination.admin') }}
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(function() {
        alert('Kode berhasil disalin!');
    });
}

function toggleStatus(id) {
    if (confirm('Apakah Anda yakin ingin mengubah status voucher ini?')) {
        fetch(`{{ route('admin.vouchers.toggle-status', ['id' => ':id']) }}`.replace(':id', id), {
            method: 'PATCH',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
            },
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            alert('Error: ' + error);
        });
    }
}

function toggleDisplay(id) {
    if (confirm('Apakah Anda yakin ingin mengubah tampilan voucher ini?')) {
        fetch(`{{ route('admin.vouchers.toggle-display', ['id' => ':id']) }}`.replace(':id', id), {
            method: 'PATCH',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
            },
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            alert('Error: ' + error);
        });
    }
}
</script>
@endpush
