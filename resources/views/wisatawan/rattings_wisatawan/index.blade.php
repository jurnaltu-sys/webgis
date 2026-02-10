@extends('layouts.app')

@push('styles')
    <style>
        body {
            background: #f8f9fa;
        }
        .tourism-header {
                background: #185a9d;
            color: #fff;
            border-radius: 1rem;
            box-shadow: 0 2px 16px rgba(67,206,162,0.1);
            padding: 2rem 1.5rem 1rem 1.5rem;
            margin-bottom: 2rem;
            position: relative;
            overflow: hidden;
        }
        .tourism-header .header-bg {
            position: absolute;
            right: 0;
            bottom: 0;
            width: 180px;
            opacity: 0.15;
            z-index: 0;
            pointer-events: none;
        }
        .tourism-header > * {
            position: relative;
            z-index: 1;
        }
        .tourism-header .fa-umbrella-beach {
            font-size: 2.5rem;
            margin-right: 1rem;
        }
        .table thead {
                background: #185a9d;
            color: #fff;
        }
        .onboarding-wisata-card {
            border-radius: 1rem;
            box-shadow: 0 2px 8px rgba(67,206,162,0.08);
            transition: transform 0.15s, box-shadow 0.15s;
        }
        .onboarding-wisata-card .card-img-top {
            height: 140px;
            object-fit: cover;
            border-top-left-radius: 1rem;
            border-top-right-radius: 1rem;
        }
        .onboarding-wisata-card.is-clickable:hover {
            transform: translateY(-4px) scale(1.03);
            box-shadow: 0 8px 24px rgba(24,90,157,0.12);
        }
        .onboarding-wisata-card.is-rated {
                border: 2px solid #185a9d;
            background: #e9fbe5;
        }
        .onboarding-wisata-card.is-unrated {
            border: 2px dashed #b2dfdb;
            background: #f8f9fa;
        }
        .modal-header {
                background: #185a9d;
            color: #fff;
            border-top-left-radius: 0.5rem;
            border-top-right-radius: 0.5rem;
        }
        .modal-footer {
            background: #f8f9fa;
            border-bottom-left-radius: 0.5rem;
            border-bottom-right-radius: 0.5rem;
        }
        .btn-primary, .btn-success, .btn-warning {
            border-radius: 2rem;
        }
        .table-striped tbody tr:nth-of-type(odd) {
            background-color: #f1f8e9;
        }
        .table-striped tbody tr:nth-of-type(even) {
            background-color: #e3f2fd;
        }
        .badge-tourism {
                background: #185a9d;
            color: #fff;
            border-radius: 1rem;
            font-size: 0.9rem;
            padding: 0.4em 1em;
        }
        /* Scrollable modal body for onboarding */
        #interestOnboardingModal .modal-body {
            max-height: 60vh;
            overflow-y: auto;
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
@endpush
@push('scripts')
    <script>
        $(function () {
                        // Prevent quick rating modal after delete
                        var suppressQuickRatingModal = false;

            var $currentCard = null;
            var $quickForm = $('#quickRatingForm');
            var $quickSubmit = $quickForm.closest('.modal-content').find('button[type="submit"]');
            var csrfToken = '{{ csrf_token() }}';
            // Delete ratting from onboarding modal
            $(document).off('submit.rattingsOnboarding').on('submit.rattingsOnboarding', '.js-delete-rating-onboarding', function (event) {
                event.preventDefault();
                var form = this;
                if (!window.confirm('Hapus data ini?')) {
                    return;
                }
                var $card = $(form).closest('.onboarding-wisata-card');
                if ($card.data('deleting')) {
                    return;
                }
                $card.data('deleting', true);
                fetch(form.action, {
                    method: 'DELETE',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    credentials: 'same-origin'
                })
                    .then(function (response) {
                        if (!response.ok && response.status !== 302) {
                            throw new Error('Gagal menghapus ratting.');
                        }
                        return response.text();
                    })
                    .then(function () {
                        $card.removeClass('is-rated').addClass('is-unrated');
                        $(form).remove();
                        suppressQuickRatingModal = true;
                        $('#quickRatingModal').modal('hide');
                        setTimeout(function(){ suppressQuickRatingModal = false; }, 1000);
                            // Hapus baris pada tabel secara realtime
                            var wisataId = $card.data('wisata-id');
                            var $tableBody = $('#rattingsTable tbody');
                            var $row = $tableBody.find('tr').filter(function() {
                                return $(this).find('td').eq(1).text().trim() === $card.data('wisata-nama');
                            });
                            $row.remove();
                            if ($tableBody.find('tr').length === 0) {
                                $tableBody.append('<tr><td colspan="5" class="text-center">Belum ada data.</td></tr>');
                            }
                    })
                    .catch(function (error) {
                        alert(error.message || 'Gagal menghapus ratting.');
                    })
                    .finally(function () {
                        $card.removeData('deleting');
                    });
            });

            function escapeHtml(value) {
                return String(value || '').replace(/[&<>"]/g, function (char) {
                    return {
                        '&': '&amp;',
                        '<': '&lt;',
                        '>': '&gt;',
                        '"': '&quot;'
                    }[char];
                });
            }

            function limitText(value, maxLength) {
                var text = String(value || '').trim();
                if (!text) {
                    return '-';
                }
                if (text.length <= maxLength) {
                    return text;
                }
                return text.substring(0, maxLength) + '...';
            }


            function appendRattingRow(ratting) {
                if (!ratting || !ratting.id) {
                    return;
                }

                var wisataNama = escapeHtml(ratting.wisata_nama || '-');
                var ulasan = escapeHtml(limitText(ratting.ulasan, 60));

                var $tableBody = $('#rattingsTable tbody');
                $tableBody.find('tr td[colspan="5"]').closest('tr').remove();

                // Calculate the next row number
                var nextNo = $tableBody.find('tr').length + 1;

                var rowHtml = ''
                    + '<tr>'
                    + '<td>' + nextNo + '</td>'
                    + '<td>' + wisataNama + '</td>'
                    + '<td>' + ratting.ratting + '</td>'
                    + '<td>' + ulasan + '</td>'
                    + '<td>'
                    + '<a href="' + ratting.edit_url + '" class="btn btn-sm btn-warning mr-1"><i class="fas fa-edit"></i> Edit</a>'
                    + '<form action="' + ratting.destroy_url + '" method="POST" class="d-inline js-delete-rating" onsubmit="return confirm(\'Hapus data ini?\')">'
                    + '<input type="hidden" name="_token" value="' + csrfToken + '">' 
                    + '<input type="hidden" name="_method" value="DELETE">'
                    + '<button type="submit" class="btn btn-sm btn-danger"><i class="fas fa-trash-alt"></i> Hapus</button>'
                    + '</form>'
                    + '</td>'
                    + '</tr>';

                $tableBody.append(rowHtml);
            }

            // Use .off() before .on() to prevent duplicate bindings
            $(document).off('submit.rattings').on('submit.rattings', '.js-delete-rating', function (event) {
                event.preventDefault();

                var form = this;
                if (!window.confirm('Hapus data ini?')) {
                    return;
                }

                var $row = $(form).closest('tr');
                if ($row.data('deleting')) {
                    return;
                }
                $row.data('deleting', true);

                fetch(form.action, {
                    method: 'DELETE',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    credentials: 'same-origin'
                })
                    .then(function (response) {
                        if (!response.ok && response.status !== 302) {
                            throw new Error('Gagal menghapus ratting.');
                        }
                        return response.text();
                    })
                    .then(function () {
                        $row.remove();

                        var $tableBody = $('#rattingsTable tbody');
                        if ($tableBody.find('tr').length === 0) {
                            $tableBody.append('<tr><td colspan="5" class="text-center">Belum ada data.</td></tr>');
                        }
                    })
                    .catch(function (error) {
                        alert(error.message || 'Gagal menghapus ratting.');
                    })
                    .finally(function () {
                        $row.removeData('deleting');
                    });
            });

            $('.js-open-quick-rating').off('click.rattings').on('click.rattings', function () {
                if (suppressQuickRatingModal) {
                    suppressQuickRatingModal = false;
                    return;
                }
                var wisataId = $(this).data('wisata-id');
                var wisataNama = $(this).data('wisata-nama');

                $currentCard = $(this);
                $('#quickRatingWisataId').val(wisataId);
                $('#quickRatingWisataName').val(wisataNama);
                $('#quickRatingUlasan').val('');
                $('#quickRatingValue').val('5');

                $('#quickRatingModal').modal('show');
            });

            $quickForm.off('submit.rattings').on('submit.rattings', function (event) {
                event.preventDefault();

                var form = this;
                var formData = new FormData(form);

                if ($quickForm.data('submitting')) {
                    return;
                }
                $quickForm.data('submitting', true);
                $quickSubmit.prop('disabled', true).text('Menyimpan...');

                fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                    .then(function (response) {
                        if (!response.ok) {
                            return response.json().then(function (data) {
                                var message = (data && data.message) ? data.message : 'Gagal menyimpan ratting.';
                                throw new Error(message);
                            });
                        }
                        return response.json();
                    })
                    .then(function (data) {
                        var wisataId = data && data.wisata_id ? data.wisata_id : null;
                        if (wisataId) {
                            var $target = $currentCard;
                            if (!$target || !$target.length) {
                                $target = $('.js-open-quick-rating[data-wisata-id="' + wisataId + '"]');
                            }
                            if ($target && $target.length) {
                                $target.removeClass('is-unrated').addClass('is-rated');
                                 
                                    var $cardBody = $target.find('.card-body');
                                    if ($cardBody.find('.js-delete-rating-onboarding').length === 0 && data.ratting && data.ratting.destroy_url) {
                                        var deleteFormHtml = ''
                                            + '<form action="' + data.ratting.destroy_url + '" method="POST" class="mt-2 js-delete-rating-onboarding" data-wisata-id="' + wisataId + '" onsubmit="return confirm(\'Hapus data ini?\')">'
                                            + '<input type="hidden" name="_token" value="' + csrfToken + '">' 
                                            + '<input type="hidden" name="_method" value="DELETE">'
                                            + '<button type="submit" class="btn btn-sm btn-danger">Hapus</button>'
                                            + '</form>';
                                        $cardBody.append(deleteFormHtml);
                                    }
                            }
                        }

                        if (data && data.ratting) {
                            appendRattingRow(data.ratting);
                        }

                        $('#quickRatingModal').modal('hide');
                    })
                    .catch(function (error) {
                        alert(error.message || 'Gagal menyimpan ratting.');
                    })
                    .finally(function () {
                        $quickSubmit.prop('disabled', false).text('Simpan');
                        $quickForm.removeData('submitting');
                    });
            });

            // Cek jumlah ratting user dari backend
            var userRattingCount = {{ $userRattingCount ?? 0 }};
            if (userRattingCount < 5) {
                $('#interestOnboardingModal').modal('show');
            } else {
                $('#interestOnboardingModal').modal('hide');
            }

                // Overlay logic: make onboarding modal darker when Ratting modal is shown
                var $onboardingModal = $('#interestOnboardingModal');
                var overlayHtml = '<div class="onboarding-overlay"></div>';
                $('#quickRatingModal').on('show.bs.modal', function () {
                    // Add overlay if not already present
                    if ($onboardingModal.find('.onboarding-overlay').length === 0) {
                        $onboardingModal.find('.modal-content').append(overlayHtml);
                    }
                });
                $('#quickRatingModal').on('hidden.bs.modal', function () {
                    // Remove overlay
                    $onboardingModal.find('.onboarding-overlay').remove();
                });
        });
    </script>
@endpush

@section('content')

    <div class="tourism-header mb-4 d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center">
            <i class="fas fa-umbrella-beach mr-3"></i>
            <div>
                <h1 class="h4 mb-1 font-weight-bold">Favorit &amp; Ratting Wisata Saya</h1>
                <div class="mb-0 small">Kelola ulasan dan ratting destinasi favoritmu untuk pengalaman wisata terbaik!</div>
            </div>
        </div>
        <button type="button" class="btn btn-success btn-lg shadow-sm" data-toggle="modal" data-target="#interestOnboardingModal">
            <i class="fas fa-plus mr-1"></i> Tambah Favorit
        </button>
        <img src="https://images.unsplash.com/photo-1506744038136-46273834b3fb?auto=format&fit=crop&w=400&q=80" class="header-bg d-none d-md-block" alt="Tourism">
    </div>

    <form method="GET" action="{{ route('rattings-wisatawan.index') }}" class="mb-4">
        <div class="input-group input-group-lg shadow-sm">
            <input type="text" name="q" value="{{ $query ?? '' }}" class="form-control" placeholder="Cari destinasi favorit...">
            <div class="input-group-append">
                <button class="btn btn-primary" type="submit"><i class="fas fa-search"></i> Cari</button>
                @if (!empty($query))
                    <a href="{{ route('rattings-wisatawan.index') }}" class="btn btn-danger"><i class="fas fa-times"></i> Reset</a>
                @endif
            </div>
        </div>
    </form>

    <div class="card border-0 shadow-sm mb-4 bg-light">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped mb-0" id="rattingsTable">
                    <thead>
                        <tr>
                            <th style="width: 60px;">No</th>
                            <th><i class="fas fa-map-marker-alt"></i> Wisata</th>
                            <th style="width: 120px;"><i class="fas fa-star text-warning"></i> Ratting</th>
                            <th><i class="fas fa-comment-dots"></i> Ulasan</th>
                            <th style="width: 180px;"><i class="fas fa-cogs"></i> Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($rattings as $item)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $item->wisata?->nama ?? '-' }}</td>
                                <td>{{ $item->ratting }}</td>
                                <td>{{ $item->ulasan ? \Illuminate\Support\Str::limit($item->ulasan, 60) : '-' }}</td>
                                <td>
                                    <a href="{{ route('rattings-wisatawan.edit', $item) }}" class="btn btn-sm btn-warning mr-1"><i class="fas fa-edit"></i> Edit</a>
                                    <form action="{{ route('rattings-wisatawan.destroy', $item) }}" method="POST" class="d-inline js-delete-rating" onsubmit="return confirm('Hapus data ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger"><i class="fas fa-trash-alt"></i> Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">Belum ada data.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if ($rattings->hasPages())
            <div class="card-footer">
                {{ $rattings->links() }}
            </div>
        @endif
    </div>

    <div class="modal fade" id="interestOnboardingModal" tabindex="-1" role="dialog" aria-labelledby="interestOnboardingModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="interestOnboardingModalLabel"><i class="fas fa-heart text-danger mr-2"></i>Pilih Destinasi Favoritmu</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p class="mb-3">Pilih minimal 5 destinasi favorit untuk mendapatkan rekomendasi wisata yang lebih relevan dan personal.</p>
                    <div class="row">
                        @forelse ($wisata as $item)
                            @php
                                $cover = $item->foto->firstWhere('is_cover', 1) ?? $item->foto->first();
                                $imageUrl = $cover ? asset('storage/' . $cover->url) : 'https://via.placeholder.com/600x400?text=No+Image';
                                $isRated = in_array($item->id, $ratedWisataIds ?? [], true);
                            @endphp
                            <div class="col-sm-6 col-md-4 mb-3">
                                <div class="card h-100 onboarding-wisata-card is-clickable {{ $isRated ? 'is-rated' : 'is-unrated' }} js-open-quick-rating" data-wisata-id="{{ $item->id }}" data-wisata-nama="{{ $item->nama }}">
                                    <img src="{{ $imageUrl }}" class="card-img-top" alt="{{ $item->nama }}">
                                    <div class="card-body py-2">
                                        <div class="font-weight-bold mb-1"><i class="fas fa-map-marker-alt text-danger mr-1"></i>{{ $item->nama }}</div>
                                        @if($isRated)
                                            @php
                                                $ratting = \App\Models\Ratting::where('user_id', (int) (session('user.id') ?? session('user_id', 0)))->where('wisata_id', $item->id)->first();
                                            @endphp
                                            @if($ratting)
                                                <form action="{{ route('rattings-wisatawan.destroy', $ratting->id) }}" method="POST" class="mt-2 js-delete-rating-onboarding" data-wisata-id="{{ $item->id }}" onsubmit="return confirm('Hapus data ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger"><i class="fas fa-trash-alt"></i> Hapus</button>
                                                </form>
                                            @endif
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12">
                                <div class="alert alert-info mb-0">Belum ada data wisata.</div>
                            </div>
                        @endforelse
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="quickRatingModal" tabindex="-1" role="dialog" aria-labelledby="quickRatingModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="quickRatingModalLabel"><i class="fas fa-star text-warning mr-2"></i>Tambah/Ubah Ratting</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('rattings-wisatawan.store') }}" method="POST" id="quickRatingForm">
                        @csrf
                        <input type="hidden" name="wisata_id" id="quickRatingWisataId" value="">

                        <div class="form-group">
                            <label for="quickRatingWisataName">Wisata</label>
                            <input type="text" id="quickRatingWisataName" class="form-control bg-light" value="" readonly>
                        </div>

                        <div class="form-group">
                            <label for="quickRatingValue">Ratting</label>
                            <select name="ratting" id="quickRatingValue" class="form-control" required>
                                @for ($i = 1; $i <= 5; $i++)
                                    <option value="{{ $i }}" {{ $i === 5 ? 'selected' : '' }}>{{ $i }}</option>
                                @endfor
                            </select>
                        </div>

                        <div class="form-group mb-0">
                            <label for="quickRatingUlasan">Ulasan (opsional)</label>
                            <textarea name="ulasan" id="quickRatingUlasan" rows="3" class="form-control bg-light"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success" form="quickRatingForm"><i class="fas fa-save"></i> Simpan</button>
                </div>
            </div>
        </div>
    </div>
@endsection

