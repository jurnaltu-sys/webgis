@extends('layouts.app')

@push('styles')
    <style>
        /* Scrollable modal for Interest-based Onboarding */
        #interestOnboardingModal .modal-dialog {
            max-width: 900px;
        }
        #interestOnboardingModal .modal-content {
            max-height: 80vh;
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }
        #interestOnboardingModal .modal-body {
            overflow-y: auto;
            max-height: 60vh;
        }
        .onboarding-wisata-card {
            height: 100%;
        }
        .onboarding-wisata-card .card-img-top {
            height: 160px;
            object-fit: cover;
        }
        .onboarding-wisata-card.is-clickable {
            cursor: pointer;
            transition: transform 0.15s ease, box-shadow 0.15s ease;
        }
        .onboarding-wisata-card.is-clickable:hover {
            transform: translateY(-2px);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }
        .onboarding-wisata-card.is-rated {
            border-color: #28a745;
            box-shadow: 0 0 0 1px rgba(40, 167, 69, 0.35);
        }
        .onboarding-wisata-card.is-rated .card-body {
            background: rgba(40, 167, 69, 0.08);
        }
        .onboarding-wisata-card.is-unrated {
            border-color: #ced4da;
            background: #f8f9fa;
        }
            /* Overlay for onboarding modal when Ratting modal is shown */
            #interestOnboardingModal .onboarding-overlay {
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0,0,0,0.5);
                z-index: 1051;
                pointer-events: none;
            }
    </style>
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
                    + '<a style="display:none" href="' + ratting.show_url + '" class="btn btn-sm btn-info">Detail</a> '
                    + '<a  href="' + ratting.edit_url + '" class="btn btn-sm btn-warning">Edit</a> '
                    + '<form  action="' + ratting.destroy_url + '" method="POST" class="d-inline js-delete-rating" onsubmit="return confirm(\'Hapus data ini?\')">'
                    + '<input type="hidden" name="_token" value="' + csrfToken + '">'
                    + '<input type="hidden" name="_method" value="DELETE">'
                    + '<button style="display:none" type="submit" class="btn btn-sm btn-danger">Hapus</button>'
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
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h4 mb-0">Ratting Saya</h1>
        <div class="d-flex align-items-center">
            <button type="button" class="btn btn-primary mr-2" data-toggle="modal" data-target="#interestOnboardingModal">
                Tambah
            </button>
            <!--
            <a href="{{ route('rattings-wisatawan.create') }}" class="btn btn-primary">Tambah</a>
            -->
        </div>
    </div>

    <form method="GET" action="{{ route('rattings-wisatawan.index') }}" class="mb-3">
        <div class="input-group">
            <input type="text" name="q" value="{{ $query ?? '' }}" class="form-control" placeholder="Cari...">
            <div class="input-group-append">
                <button class="btn btn-outline-secondary" type="submit">Cari</button>
                @if (!empty($query))
                    <a href="{{ route('rattings-wisatawan.index') }}" class="btn btn-outline-danger">Reset</a>
                @endif
            </div>
        </div>
    </form>

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped mb-0" id="rattingsTable">
                    <thead class="thead-dark">
                        <tr>
                            <th style="width: 80px;">No</th>
                            <th>Wisata</th>
                            <th style="width: 120px;">Ratting</th>
                            <th>Ulasan</th>
                            <th style="width: 200px;">Aksi</th>
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
                                    <a style="display:none" href="{{ route('rattings-wisatawan.show', $item) }}" class="btn btn-sm btn-info">Detail</a>

                                    <a href="{{ route('rattings-wisatawan.edit', $item) }}" class="btn btn-sm btn-warning">Edit</a>
                                    <form action="{{ route('rattings-wisatawan.destroy', $item) }}" method="POST" class="d-inline js-delete-rating" onsubmit="return confirm('Hapus data ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button style="display:none" type="submit" class="btn btn-sm btn-danger">Hapus</button>
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
                    <h5 class="modal-title" id="interestOnboardingModalLabel">Interest-based Onboarding</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p class="mb-3">Silakan pilih 5  untuk mendapatkan rekomendasi wisata yang lebih relevan.</p>
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
                                        <div class="font-weight-bold">{{ $item->nama }}</div>
                                        @if($isRated)
                                            @php
                                                $ratting = \App\Models\Ratting::where('user_id', (int) (session('user.id') ?? session('user_id', 0)))->where('wisata_id', $item->id)->first();
                                            @endphp
                                            @if($ratting)
                                                <form action="{{ route('rattings-wisatawan.destroy', $ratting->id) }}" method="POST" class="mt-2 js-delete-rating-onboarding" data-wisata-id="{{ $item->id }}" onsubmit="return confirm('Hapus data ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
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
                    <h5 class="modal-title" id="quickRatingModalLabel">Ratting</h5>
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
                            <input type="text" id="quickRatingWisataName" class="form-control" value="" readonly>
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
                            <textarea name="ulasan" id="quickRatingUlasan" rows="3" class="form-control"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" form="quickRatingForm">Simpan</button>
                </div>
            </div>
        </div>
    </div>
@endsection

