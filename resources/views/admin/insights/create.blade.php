@extends('layouts.admin')

@section('title', 'Tambah Insight')
@section('page-title', 'Tambah Insight')

@section('content')
    <div class="card">
        <div class="card-header">
            <i class="fas fa-plus me-2"></i>Tambah Insight Baru
        </div>
        <div class="card-body">
            <form action="{{ route('admin.insights.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="row">
                    <div class="col-md-8">
                        <div class="mb-3">
                            <label class="form-label">Judul Insight *</label>
                            <input type="text" name="title" class="form-control @error('title') is-invalid @enderror"
                                value="{{ old('title') }}" required>
                            @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Excerpt / Ringkasan (Opsional)</label>
                            <textarea name="excerpt" class="form-control @error('excerpt') is-invalid @enderror"
                                rows="2">{{ old('excerpt') }}</textarea>
                            @error('excerpt') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <div class="d-flex justify-content-between items-center mb-2">
                                <label class="form-label mb-0">Konten Utama *</label>
                                <!-- The Trigger Button -->
                                <div>
                                    <button type="button" class="btn btn-sm btn-outline-secondary me-2"
                                        data-bs-toggle="modal" data-bs-target="#productLinkModal">
                                        <i class="fas fa-box me-1"></i> Sisipkan Link Produk
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-info" data-bs-toggle="modal"
                                        data-bs-target="#articleLinkModal">
                                        <i class="fas fa-file-alt me-1"></i> Sisipkan Link Artikel
                                    </button>
                                </div>
                            </div>
                            <textarea id="contentEditor" name="content"
                                class="form-control @error('content') is-invalid @enderror" rows="10"
                                required>{{ old('content') }}</textarea>
                            @error('content') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Penulis</label>
                            <input type="text" name="author" class="form-control @error('author') is-invalid @enderror"
                                value="{{ old('author', 'LUMINA Team') }}">
                            @error('author') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Status *</label>
                            <!-- Added id="statusSelect" here -->
                            <select name="status" id="statusSelect"
                                class="form-select @error('status') is-invalid @enderror" required>
                                <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                                <option value="published" {{ old('status', 'published') == 'published' ? 'selected' : '' }}>
                                    Published</option>
                                <option value="scheduled" {{ old('status') == 'scheduled' ? 'selected' : '' }}>Scheduled
                                </option>
                            </select>
                            @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <!-- NEW: Specific Date & Time Picker -->
                        <div class="mb-3 d-none" id="publishDateContainer">
                            <label class="form-label">Tanggal & Jam Publish *</label>
                            <input type="datetime-local" name="published_at"
                                class="form-control @error('published_at') is-invalid @enderror"
                                value="{{ old('published_at') }}">
                            @error('published_at') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Gambar Utama</label>
                            <div class="mb-2 d-none" id="imagePreviewContainer">
                                <img id="imagePreview" src="#" alt="Preview" class="img-thumbnail"
                                    style="max-height: 150px;">
                            </div>
                            <input type="file" name="image" id="imageInput"
                                class="form-control @error('image') is-invalid @enderror" accept="image/*">
                            @error('image') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Alt Gambar</label>
                            <input type="text" name="alt_image"
                                class="form-control @error('alt_image') is-invalid @enderror"
                                value="{{ old('alt_image') }}">
                            @error('alt_image') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Slug</label>
                            <input type="text" name="slug" id="slugInput"
                                class="form-control @error('slug') is-invalid @enderror" value="{{ old('slug') }}">
                            <small class="text-muted">Otomatis terisi dari title, bisa diedit manual.</small>
                            @error('slug') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Meta Title</label>
                            <input type="text" name="meta_title" id="metaTitleInput" maxlength="60"
                                class="form-control @error('meta_title') is-invalid @enderror"
                                value="{{ old('meta_title') }}">
                            <small class="text-muted">Otomatis terisi dari title, bisa diedit manual. max 60 karakter.</small>
                            @error('meta_title') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Meta Description</label>
                            <input type="text" name="meta_description" id="metaDescriptionInput" maxlength="160"
                                class="form-control @error('meta_description') is-invalid @enderror"
                                value="{{ old('meta_description') }}">
                            <small class="text-muted">Otomatis terisi dari excerpt, bisa diedit manual. max 160 karakter.</small>
                            @error('meta_description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="d-grid mt-4">
                            <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>Simpan
                                Insight</button>
                            <button type="button" class="btn btn-info mt-2 text-white" id="previewBtn"><i
                                    class="fas fa-eye me-2"></i>Preview Insight</button>
                            <a href="{{ route('admin.insights.index') }}" class="btn btn-light mt-2">Batal</a>
                        </div>
                    </div>
                </div>

            </form>
        </div>
    </div>
    <!-- Product Linker Modal -->
    <div class="modal fade" id="productLinkModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-search me-2"></i>Cari & Sisipkan Produk</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="text" id="productSearchInput" class="form-control mb-3"
                        placeholder="Ketik nama produk (min. 2 huruf)...">
                    <!-- Search Results -->
                    <div id="productSearchResults" class="list-group">
                        <!-- Results injected here via JS -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Article Linker Modal -->
    <div class="modal fade" id="articleLinkModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-file-alt me-2"></i>Cari & Sisipkan Artikel</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="text" id="articleSearchInput" class="form-control mb-3"
                        placeholder="Ketik judul artikel (min. 2 huruf)...">
                    <!-- Search Results -->
                    <div id="articleSearchResults" class="list-group">
                        <!-- Results injected here via JS -->
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Preview Modal -->
    <div class="modal fade" id="previewModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-eye me-2"></i>Preview Insight</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-5">
                    <h1 id="previewTitle" class="mb-3 fw-bold"></h1>
                    <p id="previewAuthorDate" class="text-muted mb-4 border-bottom pb-2"></p>
                    <div id="previewImageContainerModal" class="mb-4 d-none text-center">
                        <img id="previewImageModal" src="" alt="Featured Image" class="img-fluid rounded shadow-sm"
                            style="max-height: 500px; object-fit: cover;">
                    </div>
                    <div id="previewExcerpt" class="lead mb-4 fst-italic text-secondary"></div>
                    <div id="previewContent" class="insight-content"></div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup Preview</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    {{-- LOAD tiny MCE --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.8.3/tinymce.min.js" referrerpolicy="origin"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const titleInput = document.querySelector('input[name="title"]');
            const excerptInput = document.querySelector('textarea[name="excerpt"]');
            const metaTitleInput = document.getElementById('metaTitleInput');
            const metaDescriptionInput = document.getElementById('metaDescriptionInput');
            const slugInput = document.getElementById('slugInput');
            let slugEdited = false;
            let metaTitleEdited = false;
            let metaDescriptionEdited = false;

            slugInput.addEventListener('input', () => slugEdited = true);
            metaTitleInput.addEventListener('input', () => metaTitleEdited = true);
            metaDescriptionInput.addEventListener('input', () => metaDescriptionEdited = true);

            titleInput.addEventListener('input', function () {
                if (slugEdited) return;
                slugInput.value = this.value
                    .toLowerCase()
                    .trim()
                    .replace(/[^a-z0-9\s-]/g, '')
                    .replace(/\s+/g, '-')
                    .replace(/-+/g, '-')
                    .substring(0, 60);
            });

            titleInput.addEventListener('input', function () {
                if (metaTitleEdited) return;
                metaTitleInput.value = this.value
                    .toLowerCase()
                    .trim()
                    .replace(/[^a-z0-9\s-]/g, '')
                    .replace(/\s+/g, '-')
                    .replace(/-+/g, '-')
                    .substring(0, 60);
            });

            excerptInput.addEventListener('input', function () {
                if (metaDescriptionEdited) return;
                metaDescriptionInput.value = this.value
                    .toLowerCase()
                    .trim()
                    .replace(/[^a-z0-9\s-]/g, '')
                    .replace(/\s+/g, '-')
                    .replace(/-+/g, '-')
                    .substring(0, 160);
            });
            

        });
    </script>
    <script>
        // Initialize the Rich Text Editor
        tinymce.init({
            selector: '#contentEditor', // This targets your existing textarea ID
            height: 500,
            menubar: false,
            plugins: 'advlist autolink lists link image charmap preview anchor searchreplace visualblocks code fullscreen insertdatetime media table code help wordcount',
            toolbar: 'undo redo | blocks | bold italic textcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | removeformat | code',
            block_formats: 'Paragraph=p;Heading 2=h2;Heading 3=h3;Heading 4=h4;Heading 5=h5;Heading 6=h6;Preformatted=pre',
            content_style: 'body { font-family: -apple-system, BlinkMacSystemFont, San Francisco, Segoe UI, Roboto, Helvetica, sans-serif; font-size: 14px; }',
            link_default_target: '_blank',

            // This ensures the hidden textarea is updated before the form submits
            setup: function (editor) {
                editor.on('change', function () {
                    editor.save();
                });
                editor.on('NodeChange', function (e) {
                    if (e.element.tagName === 'A') {
                        e.element.classList.add('text-rose-500', 'font-semibold', 'hover:underline');
                        if (!e.element.hasAttribute('target')) {
                            e.element.setAttribute('target', '_blank');
                        }
                    }
                });
            }
        });
    </script>
    <script>
        // ==========================================
        // 1. Existing Image Preview Logic
        // ==========================================
        document.getElementById('imageInput').addEventListener('change', function (e) {
            const reader = new FileReader();
            reader.onload = function (e) {
                document.getElementById('imagePreview').src = e.target.result;
                document.getElementById('imagePreviewContainer').classList.remove('d-none');
            }
            if (this.files && this.files[0]) {
                reader.readAsDataURL(this.files[0]);
            }
        });

        // ==========================================
        // 2. Scheduling UI Logic
        // ==========================================
        const statusSelect = document.getElementById('statusSelect');
        const publishDateContainer = document.getElementById('publishDateContainer');

        function handlePublishFields() {
            if (statusSelect.value === 'scheduled') {
                publishDateContainer.classList.remove('d-none');
            } else {
                publishDateContainer.classList.add('d-none');
            }
        }

        statusSelect.addEventListener('change', handlePublishFields);
        handlePublishFields();

        // ==========================================
        // 3. Product Linker Logic
        // ==========================================

        const searchInput = document.getElementById('productSearchInput');
        const searchResults = document.getElementById('productSearchResults');
        const contentEditor = document.getElementById('contentEditor');
        let searchTimeout;

        searchInput.addEventListener('input', function () {
            clearTimeout(searchTimeout);
            const query = this.value.trim();

            if (query.length < 2) {
                searchResults.innerHTML = '<div class="text-muted text-center py-3">Ketik untuk mencari produk...</div>';
                return;
            }

            searchResults.innerHTML = '<div class="text-center py-3"><i class="fas fa-spinner fa-spin text-primary"></i> Mencari...</div>';

            searchTimeout = setTimeout(() => {
                fetch(`{{ route('admin.insights.search-products') }}?q=${encodeURIComponent(query)}`)
                    .then(response => response.json())
                    .then(products => {
                        searchResults.innerHTML = '';

                        if (products.length === 0) {
                            searchResults.innerHTML = '<div class="text-danger text-center py-3">Produk tidak ditemukan.</div>';
                            return;
                        }

                        products.forEach(product => {
                            const btn = document.createElement('button');
                            btn.type = 'button';
                            btn.className = 'list-group-item list-group-item-action d-flex justify-content-between align-items-center';
                            btn.innerHTML = `<span>${product.name}</span> <i class="fas fa-plus-circle text-primary"></i>`;

                            btn.addEventListener('click', () => {
                                const linkHtml = `<a href="${product.url}" target="_blank" class="text-rose-500 font-semibold hover:underline">${product.name}</a>&nbsp;`;

                                if (typeof tinymce !== 'undefined' && tinymce.get('contentEditor')) {
                                    tinymce.get('contentEditor').insertContent(linkHtml);
                                } else {
                                    insertAtCursor(contentEditor, linkHtml);
                                }

                                bootstrap.Modal.getInstance(document.getElementById('productLinkModal')).hide();
                                searchInput.value = '';
                                searchResults.innerHTML = '';
                            });

                            searchResults.appendChild(btn);
                        });
                    });
            }, 300);
        });

        // ==========================================
        // 3.5. Article Linker Logic
        // ==========================================
        const articleSearchInput = document.getElementById('articleSearchInput');
        const articleSearchResults = document.getElementById('articleSearchResults');
        let articleSearchTimeout;

        articleSearchInput.addEventListener('input', function () {
            clearTimeout(articleSearchTimeout);
            const query = this.value.trim();

            if (query.length < 2) {
                articleSearchResults.innerHTML = '<div class="text-muted text-center py-3">Ketik untuk mencari artikel...</div>';
                return;
            }

            articleSearchResults.innerHTML = '<div class="text-center py-3"><i class="fas fa-spinner fa-spin text-info"></i> Mencari...</div>';

            articleSearchTimeout = setTimeout(() => {
                fetch(`{{ route('admin.insights.search-insights') }}?q=${encodeURIComponent(query)}`)
                    .then(response => response.json())
                    .then(insights => {
                        articleSearchResults.innerHTML = '';

                        if (insights.length === 0) {
                            articleSearchResults.innerHTML = '<div class="text-danger text-center py-3">Artikel tidak ditemukan.</div>';
                            return;
                        }

                        insights.forEach(insight => {
                            const btn = document.createElement('button');
                            btn.type = 'button';
                            btn.className = 'list-group-item list-group-item-action d-flex justify-content-between align-items-center';

                            let statusBadge = '';
                            if (insight.status === 'published') statusBadge = '<span class="badge bg-success">Published</span>';
                            else if (insight.status === 'scheduled') statusBadge = `<span class="badge bg-warning text-dark">Scheduled (${new Date(insight.published_at).toLocaleDateString()})</span>`;
                            else statusBadge = '<span class="badge bg-secondary">Draft</span>';

                            btn.innerHTML = `<div><span class="fw-bold">${insight.title}</span><br>${statusBadge}</div> <i class="fas fa-plus-circle text-info"></i>`;

                            btn.addEventListener('click', () => {
                                let shouldInsert = true;

                                // Cross-checking scheduled date
                                if (insight.status === 'draft' || insight.status === 'scheduled') {
                                    const currentStatus = document.getElementById('statusSelect').value;
                                    const currentDateRaw = document.querySelector('input[name="published_at"]').value;
                                    const currentDate = (currentStatus === 'scheduled' && currentDateRaw) ? new Date(currentDateRaw) : new Date();
                                    const targetDate = new Date(insight.published_at || new Date().getTime() + 86400000); // Draft is assumed future

                                    if (targetDate > currentDate) {
                                        shouldInsert = confirm("Peringatan: Artikel tujuan dijadwalkan tayang LEBIH LAMBAT dari artikel ini atau masih Draft. \n\nLink akan diubah menjadi teks biasa hingga artikel tujuan tayang untuk menghindari error 404. \n\nYakin ingin menyisipkan?");
                                    } else if (insight.status === 'draft') {
                                        shouldInsert = confirm("Peringatan: Artikel tujuan masih berstatus Draft. Link akan menjadi teks biasa hingga artikel tersebut di-publish. Lanjut?");
                                    }
                                }

                                if (shouldInsert) {
                                    const linkHtml = `<a href="${insight.url}" target="_blank" class="text-rose-500 font-semibold hover:underline">${insight.title}</a>&nbsp;`;

                                    if (typeof tinymce !== 'undefined' && tinymce.get('contentEditor')) {
                                        tinymce.get('contentEditor').insertContent(linkHtml);
                                    } else {
                                        insertAtCursor(contentEditor, linkHtml);
                                    }

                                    bootstrap.Modal.getInstance(document.getElementById('articleLinkModal')).hide();
                                    articleSearchInput.value = '';
                                    articleSearchResults.innerHTML = '';
                                }
                            });

                            articleSearchResults.appendChild(btn);
                        });
                    });
            }, 300);
        });

        // Helper function to insert text at the exact cursor position
        function insertAtCursor(myField, myValue) {
            if (myField.selectionStart || myField.selectionStart === 0) {
                var startPos = myField.selectionStart;
                var endPos = myField.selectionEnd;
                myField.value = myField.value.substring(0, startPos)
                    + myValue
                    + myField.value.substring(endPos, myField.value.length);

                myField.selectionStart = startPos + myValue.length;
                myField.selectionEnd = startPos + myValue.length;
            } else {
                // Repaired: Re-added the correct fallback logic here
                myField.value += myValue;
            }
            myField.focus();
        }

        // ==========================================
        // 4. Preview Modal Logic
        // ==========================================
        document.getElementById('previewBtn').addEventListener('click', function () {
            const title = document.querySelector('input[name="title"]').value || 'Judul Belum Diisi';
            const excerpt = document.querySelector('textarea[name="excerpt"]').value || '';
            const author = document.querySelector('input[name="author"]').value || 'LUMINA Team';

            let content = '';
            if (typeof tinymce !== 'undefined' && tinymce.get('contentEditor')) {
                content = tinymce.get('contentEditor').getContent();
            } else {
                content = document.getElementById('contentEditor').value;
            }

            document.getElementById('previewTitle').innerText = title;

            const imagePreviewSrc = document.getElementById('imagePreview').src;
            const previewImageModal = document.getElementById('previewImageModal');
            const previewImageContainerModal = document.getElementById('previewImageContainerModal');

            if (imagePreviewSrc && imagePreviewSrc !== window.location.href && !imagePreviewSrc.endsWith('#')) {
                previewImageModal.src = imagePreviewSrc;
                previewImageContainerModal.classList.remove('d-none');
            } else {
                previewImageContainerModal.classList.add('d-none');
            }

            document.getElementById('previewAuthorDate').innerText = `Oleh: ${author}`;
            document.getElementById('previewExcerpt').innerText = excerpt;
            document.getElementById('previewContent').innerHTML = content;

            const modal = new bootstrap.Modal(document.getElementById('previewModal'));
            modal.show();
        });
    </script>
@endpush