@extends('layouts.app')

@section('title', 'Rekomendasi Wisata')

@section('content')
<div class="container-fluid">
    <div class="mb-4">
        <h1 class="h4 mb-1 text-success"><i class="fas fa-star mr-2"></i>Rekomendasi Wisata</h1>
        <p class="text-muted mb-0">Dapatkan rekomendasi wisata berdasarkan kemiripan rating Anda dengan pengguna lain menggunakan metode Collaborative Filtering.</p>
    </div>
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="alert alert-info mb-2 shadow-sm">
                <b><i class="fas fa-user mr-1"></i>{{ $email ?? 'User login' }}:</b><br>
                <b><i class="fas fa-user-friends mr-1"></i>User paling mirip:</b> {{ $topUserEmail ?? '-' }}<br>
                <b><i class="fas fa-percentage mr-1"></i>Nilai Cosine Similarity:</b> {{ number_format($topSimilarity ?? 0, 4) }}
            </div>
        </div>
        <div class="col-md-6 text-md-right text-center">
            <a href="{{ route('wisatawan.rekomendasi.proses') }}" class="btn btn-success mb-2" id="btn-rekomendasi">
                <i class="fas fa-magic mr-1"></i> Dapatkan Rekomendasi
            </a>
            <div id="loading-rekomendasi" class="text-center mb-2" style="display:none">
                <div class="spinner-border text-success" role="status">
                    <span class="sr-only">Loading...</span>
                </div>
                <div>Menjalankan Algoritma Collaborative Filtering (User-Based), mohon tunggu...</div>
            </div>
        </div>
    </div>

   
   
    @if(request()->routeIs('wisatawan.rekomendasi.proses'))
    {{-- Tabel dipindahkan ke bawah --}}
    @php $showDatasetTable = true; @endphp
    @endif
  

    @if(isset($similarities))
    <div class="card mb-4 border-info shadow-sm">
        <div class="card-header bg-info text-white"><b><i class="fas fa-list mr-1"></i>Langkah Perhitungan (Urut)</b></div>
        <div class="card-body">
            <ol>
                <li><b>Rata‑rata rating per user</b> — ditampilkan tabel singkat di bawah.</li>
                <li><b>Centered (rating - rata‑rata)</b> — tabel matriks berisi nilai terpusat per user/item.</li>
                <li><b>Centered Cosine Similarity</b> — daftar similarity user terhadap Anda (urut menurun).</li>
                <li><b>Ambil Top‑N neighbors (k = {{ $k ?? 3 }})</b> — ditampilkan daftar.</li>
                <li><b>Prediksi nilai untuk item tak dinilai</b> — per item tampilkan kontribusi neighbor (sim × centered) dan perhitungan weighted average.</li>
                <li><b>Hasil prediksi</b> — daftar skor prediksi (urut menurun) dan rekomendasi.</li>
            </ol>
        </div>
    </div>

    {{-- Step 1: averages --}}
    <div class="card mb-3">
        <div class="card-header"><b>1) Rata‑rata Rating per User (Dataset + Total & Average)</b></div>
        <div class="card-body p-2 table-responsive">
            <table class="table table-sm table-bordered mb-0">
                <thead>
                    <tr>
                        <th>User</th>
                        @foreach($wisataList as $w)
                            <th class="text-center">{{ $w['nama'] }}</th>
                        @endforeach
                        <th class="text-center">Total</th>
                        <th class="text-center">Average</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($users as $uid => $userEmail)
                    @php $total = 0; $count = 0; @endphp
                    <tr>
                        <td><b>{{ $userEmail }}</b></td>
                        @foreach($wisataList as $w)
                            @php
                                $val = $ratings[$uid][$w['id']] ?? null;
                                if ($val !== null) { $total += $val; $count++; }
                            @endphp
                            <td class="text-center">@if($val === null) - @else {{ $val }} @endif</td>
                        @endforeach
                        <td class="text-center"><b>{{ $total }}</b></td>
                        <td class="text-center"><b>{{ number_format($averages[$uid] ?? ($count?($total/$count):0),4) }}</b></td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Step 2: centered matrix --}}
    <div class="card mb-3">
        <div class="card-header"><b>2) Centered Ratings (rating - average)</b></div>
        <div class="card-body p-2 table-responsive">
            <table class="table table-sm table-bordered mb-0">
                <thead>
                    <tr><th>User</th>
                        @foreach($wisataList as $w) <th>{{ $w['nama'] }}</th> @endforeach
                    </tr>
                </thead>
                <tbody>
                @foreach($normalized as $uid => $row)
                    <tr>
                        <td><b>{{ $users[$uid] ?? $uid }}</b></td>
                        @foreach($wisataList as $w)
                            @php $val = $row[$w['id']] ?? null; @endphp
                            <td class="text-center">@if($val === null) - @else {{ number_format($val,2) }} @endif</td>
                        @endforeach
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Step 3 & 4: similarities sorted and top-N --}}
    <div class="card mb-3">
        <div class="card-header"><b>3) Similarity (Centered Cosine) & 4) Top‑{{ $k ?? 3 }} Neighbors</b></div>
        <div class="card-body p-2">
            <div class="row">
                <div class="col-12">
                    <h6>Similarity terhadap Anda (urut menurun)</h6>
                    
                    @php
                        // Order similarity list to match user ordering used in centered matrix
                        $simListRaw = $similarities ?? [];
                        $simList = [];
                        // use the same user ordering as $normalized / $users
                        $orderedUsers = array_keys($normalized ?? $users ?? []);
                        foreach ($orderedUsers as $uid) {
                            if ($uid == ($userId ?? null)) continue;
                            if (isset($simListRaw[$uid])) {
                                $simList[$uid] = $simListRaw[$uid];
                            }
                        }
                        // include any remaining users not in orderedUsers
                        foreach ($simListRaw as $uid => $s) {
                            if (!isset($simList[$uid]) && $uid != ($userId ?? null)) $simList[$uid] = $s;
                        }
                    @endphp
                    {{-- Per‑user step by step calculations (co-rated items, dot, norms) --}}
                    @php $simIdx = 1; @endphp
                    @foreach($simList as $uid => $s)
                        <div class="mb-2 text-left">
                            <button class="btn btn-link p-0 text-left" type="button" data-toggle="collapse" data-target="#sim-step-{{ $uid }}" aria-expanded="false" aria-controls="sim-step-{{ $uid }}">
                                {{ $simIdx }}. {{ $users[$uid] ?? $uid }}, similarity = {{ number_format($s,4) }}
                            </button>
                            <div class="collapse border rounded p-2 mt-2 text-left" id="sim-step-{{ $uid }}">
                                <div class="small text-muted mb-2">Tampilkan hanya item yang co-rated oleh kedua user (nilai centered tidak null).</div>
                                <table class="table table-sm table-bordered mb-2">
                                    <thead><tr><th>Item</th><th>Centered (Anda)</th><th>Centered ({{ $users[$uid] ?? $uid }})</th><th>Kontribusi (a×b)</th></tr></thead>
                                    <tbody>
                                    @php $dot = 0.0; $normA = 0.0; $normB = 0.0; $sumA = 0.0; $sumB = 0.0; $hasRow=false; $dotTerms = []; $normATerms = []; $normBTerms = []; @endphp
                                    @foreach($allWisataIds as $wid)
                                        @php
                                            $a = $normalized[$userId][$wid] ?? null;
                                            $b = $normalized[$uid][$wid] ?? null;
                                        @endphp
                                        @if($a !== null && $b !== null)
                                            @php
                                                $contrib = $a * $b;
                                                $dot += $contrib;
                                                $sumA += $a;
                                                $sumB += $b;
                                                $normA += $a*$a;
                                                $normB += $b*$b;
                                                $dotTerms[] = '(' . number_format($a,4) . '×' . number_format($b,4) . ')';
                                                $normATerms[] = '(' . number_format($a,4) . ')²';
                                                $normBTerms[] = '(' . number_format($b,4) . ')²';
                                                $hasRow = true;
                                            @endphp
                                            <tr>
                                                <td>{{ $wisatas[$wid] ?? $wid }}</td>
                                                <td class="text-left">{{ number_format($a,4) }}</td>
                                                <td class="text-left">{{ number_format($b,4) }}</td>
                                                <td class="text-left">{{ number_format($contrib,4) }}</td>
                                            </tr>
                                        @endif
                                    @endforeach
                                    @if(!$hasRow)
                                        <tr><td colspan="4" class="text-left text-muted">Tidak ada item co-rated antara Anda dan {{ $users[$uid] ?? $uid }}.</td></tr>
                                    @endif
                                    </tbody>
                                    @if($hasRow)
                                    <tfoot>
                                        <tr class="font-weight-bold">
                                            <td>Total</td>
                                            <td class="text-left">{{ number_format($sumA,4) }}</td>
                                            <td class="text-left">{{ number_format($sumB,4) }}</td>
                                            <td class="text-left">{{ number_format($dot,4) }}</td>
                                        </tr>
                                    </tfoot>
                                    @endif
                                </table>
                                <div class="mb-1">Dot Product (sum a×b) = <b>{{ number_format($dot,4) }}</b></div>
                                @if(!empty($dotTerms))
                                <div class="mb-1">Rincian dot: <small>{{ implode(' + ', $dotTerms) }} = <b>{{ number_format($dot,4) }}</b></small></div>
                                @endif
                                <div class="mb-1"><br>Norm Anda = sum a² = <b>{{ number_format($normA,4) }}</b></div>
                                @if(!empty($normATerms))
                                <div class="mb-1">Rincian a²: <small>{{ implode(' + ', $normATerms) }} = <b>{{ number_format($normA,4) }}</b></small></div>
                                @endif
                                <div class="mb-1"><br>Norm {{ $users[$uid] ?? $uid }} = sum b² = <b>{{ number_format($normB,4) }}</b></div>
                                @if(!empty($normBTerms))
                                <div class="mb-1">Rincian b²: <small>{{ implode(' + ', $normBTerms) }} = <b>{{ number_format($normB,4) }}</b></small></div>
                                @endif
                                <div class="mb-1"><br>Similarity = dot / (√normA × √normB) = <b>{{ number_format($s,4) }}</b></div>
                            </div>
                        </div>
                        @php $simIdx++; @endphp
                    @endforeach
                    {{-- akhir perhitungan detail --}}
                    @php
                        // Build neighbor rank map for display
                        $neighborRanks = [];
                        foreach($neighbors[$userId] ?? [] as $i => $nb) {
                            $neighborRanks[$nb['user']] = $i + 1;
                        }
                    @endphp
                    <table class="table table-sm table-bordered mb-0">
                        <thead><tr><th style="width:5%">No.</th><th>User</th><th style="width:20%">Similarity</th><th style="width:15%">Top‑{{ $k ?? 3 }} Rank</th></tr></thead>
                        <tbody>
                        @php $no = 1; @endphp
                        @foreach($simList as $uid => $s)
                            <tr>
                                <td>{{ $no++ }}</td>
                                <td>{{ $users[$uid] ?? $uid }}</td>
                                <td>{{ number_format($s,4) }}</td>
                                <td>@if(isset($neighborRanks[$uid])) {{ $neighborRanks[$uid] }} @else - @endif</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Step 5: prediction contributions --}}
    <div class="card mb-3">
        <div class="card-header"><b>5) Perhitungan Prediksi (Weighted Average)</b></div>
        <div class="card-body p-2">
            @if(empty($predictions))
                <div class="text-muted">Tidak ada item yang perlu diprediksi (semua sudah dinilai).</div>
            @else
                @foreach($predictions as $wid => $pred)
                    <div class="mb-2">
                        <h6>{{ $wisataList[array_search($wid, array_column($wisataList, 'id'))]['nama'] ?? ($wisatas[$wid] ?? $wid) }} — Prediksi: <span class="font-weight-bold">{{ number_format($pred,2) }}</span></h6>
                        <table class="table table-sm table-bordered mb-1">
                            <thead><tr><th>Neighbor</th><th>Similarity</th><th>Centered rating (nv)</th><th>Kontribusi (Similarity×nv)</th></tr></thead>
                            <tbody>
                            @foreach($predDetails[$wid]['contribs'] ?? [] as $c)
                                @php
                                    // Perhitungan nv: rating - rata-rata
                                    $neighborRating = $ratings[$c['user']][$wid] ?? null;
                                    $neighborAvg = $averages[$c['user']] ?? null;
                                    $nvExplain = ($neighborRating !== null && $neighborAvg !== null)
                                        ? number_format($c['nv'],2) . ' = ' . number_format($neighborRating,2) . ' - ' . number_format($neighborAvg,2)
                                        : number_format($c['nv'],2);
                                @endphp
                                <tr>
                                    <td>{{ $users[$c['user']] ?? $c['user'] }}</td>
                                    <td>{{ number_format($c['sim'],4) }}</td>
                                    <td>
                                        {{ $nvExplain }}
                                        <br>
                                        <small class="text-muted">(nv = rating - rata-rata user)</small>
                                    </td>
                                    <td>{{ number_format($c['contrib'],4) }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                        @php
                            // Penjelasan Numerator
                            $contribs = $predDetails[$wid]['contribs'] ?? [];
                            $numExplain = [];
                            foreach($contribs as $c) {
                                $sim = number_format($c['sim'],4);
                                $nv = number_format($c['nv'],4);
                                $numExplain[] = '(' . $sim.'×'.$nv . ')';
                            }
                            $numFormula = implode(' + ', $numExplain);
                            $numValue = number_format($predDetails[$wid]['num'] ?? 0,4);
                            // Penjelasan Denominator
                            $denExplain = [];
                            foreach($contribs as $c) {
                                $denExplain[] = '|'.number_format($c['sim'],4).'|';
                            }
                            $denFormula = implode(' + ', $denExplain);
                            $denValue = number_format($predDetails[$wid]['den'] ?? 0,4);
                            // Penjelasan predNorm
                            $predNorm = $predDetails[$wid]['predNorm'] ?? 0;
                            $predNormFormula = ($denValue != 0) ? $numValue.' / '.$denValue : '0';
                            // Penjelasan Final
                            $userAvg = $averages[$userId] ?? 0;
                            $finalFormula = number_format($userAvg,4).' + '.number_format($predNorm,4);
                            $finalValue = number_format($predDetails[$wid]['pred'] ?? 0,4);
                        @endphp
                        <div>
                            <b>Penjelasan Perhitungan:</b><br>
                            Numerator = {{ $numFormula }} = <b>{{ $numValue }}</b><br>
                            Denominator = {{ $denFormula }} = <b>{{ $denValue }}</b><br>
                            predNorm = Numerator / Denominator = {{ $predNormFormula }} = <b>{{ number_format($predNorm,4) }}</b><br>
                            Final = average(user) + predNorm = {{ $finalFormula }} = <b>{{ $finalValue }}</b>
                        </div>
                    </div>
                    <br>
                @endforeach

                {{-- Tabel Rekap Hasil Prediksi Tahap 5 --}}
                <div class="card mt-4 mb-2 border-primary">
                    <div class="card-header bg-primary text-white"><b>Rekap Hasil Prediksi (Tahap 5)</b></div>
                    <div class="card-body p-2 table-responsive">
                        <table class="table table-bordered table-sm mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th>Wisata</th>
                                    <th>Skor Prediksi</th>
                                    <th>Rekomendasi?</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($predictions as $wid => $pred)
                                    <tr>
                                        <td>{{ $wisataList[array_search($wid, array_column($wisataList, 'id'))]['nama'] ?? ($wisatas[$wid] ?? $wid) }}</td>
                                        <td>{{ number_format($pred,4) }}</td>
                                        <td>
                                            @if(isset($rekomendasi) && in_array($wisatas[$wid] ?? '', $rekomendasi))
                                                <span class="badge badge-success">Ya</span>
                                            @else
                                                <span class="badge badge-secondary">Tidak</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>
    </div>

    @endif


    {{-- Bagian hasil rekomendasi dihilangkan sesuai permintaan --}}
</div>

@push('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/katex@0.16.8/dist/katex.min.css" integrity="" crossorigin="anonymous">
    <style>
        .card-img-top {
            height: 150px;
            object-fit: cover;
        }
        .shadow-sm {
            box-shadow: 0 2px 6px rgba(0,0,0,0.07);
        }
    </style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.getElementById('btn-rekomendasi').addEventListener('click', function(e) {
        e.preventDefault();
        document.getElementById('loading-rekomendasi').style.display = 'block';
        var url = this.getAttribute('href');
        setTimeout(function() {
            window.location.href = url;
        }, 2000);
    });
</script>
<script src="https://cdn.jsdelivr.net/npm/katex@0.16.8/dist/katex.min.js" integrity="" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/katex@0.16.8/dist/contrib/auto-render.min.js" integrity="" crossorigin="anonymous"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        try {
            renderMathInElement(document.body, {
                // only use the delimiters we used in the template
                delimiters: [
                    {left: "$$", right: "$$", display: true},
                    {left: "\\(", right: "\\)", display: false}
                ]
            });
        } catch (e) {
            console.warn('KaTeX render failed', e);
        }
    });
</script>
@endpush
@endsection
