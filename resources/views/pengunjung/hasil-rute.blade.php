@extends("template-pengunjung")
@section("title", "Hasil Rute Terpendek - Pariwisata Humbahas")
@section("body")
    <section id="hasil-rute" class="services section light-background">
        <div class="container mb-4 mt-5" data-aos="fade-up">
            <div class="row">
                <div class="card py-5 shadow-sm">
                    <div class="container">
                        <h2 class="fw-bold text-center mb-4">Hasil Rute Terpendek</h2>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="info-rute">
                                    <div class="row mb-3">
                                        <div class="col-4"><strong>Lokasi Awal</strong></div>
                                        <div class="col-1">:</div>
                                        <div class="col-7" id="namaLokasiAwal">
                                            {{ $namaLokasiAwal }}
                                            <br><small class="text-muted">({{ number_format($lokasiAwal["latitude"], 6) }},
                                                {{ number_format($lokasiAwal["longitude"], 6) }})</small>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-4"><strong>Lokasi Tujuan</strong></div>
                                        <div class="col-1">:</div>
                                        <div class="col-7">
                                            {{ $wisataTujuan->nama_wisata }}
                                            <br><small class="text-muted">({{ number_format($wisataTujuan->latitude, 6) }},
                                                {{ number_format($wisataTujuan->longitude, 6) }})</small>
                                        </div>
                                    </div>
                                    {{-- <div class="row mb-3">
                                        <div class="col-4"><strong>Titik Transit Terdekat</strong></div>
                                        <div class="col-1">:</div>
                                        <div class="col-7">{{ $hasilRute["wisata_awal"]->nama_wisata }}
                                            ({{ number_format($hasilRute["jarak_ke_wisata_awal"], 2) }} km dari Anda)</div>
                                    </div> --}}
                                    {{-- <div class="row mb-3">
                                        <div class="col-4"><strong>Jarak</strong></div>
                                        <div class="col-1">:</div>
                                        <div class="col-7" id="jarakTempuh">
                                            {{ number_format($hasilRute["jarak_total"], 2) }} km </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-4"><strong>Waktu Tempuh</strong></div>
                                        <div class="col-1">:</div>
                                        <div class="col-7" id="waktuTempuh">{{ $hasilRute["waktu_tempuh"] }}</div>
                                    </div> --}}
                                </div>
                            </div>
                        </div>

                        <!-- Rute Alternatif Section -->
                        @if (isset($hasilRute["semua_rute_alternatif"]) && count($hasilRute["semua_rute_alternatif"]) > 1)
                            <div class="row mb-4">
                                <div class="col-12">
                                    <div class="card border-0 shadow-sm">
                                        <div class="card-header bg-gradient-primary text-white">
                                            <h5 class="mb-0 d-flex align-items-center text-white">
                                                <i class="fas fa-route me-2"></i>
                                                Rute Alternatif Yang Tersedia
                                                <span
                                                    class="badge bg-light text-primary ms-2">{{ count($hasilRute["semua_rute_alternatif"]) }}
                                                    Pilihan</span>
                                            </h5>
                                            <small class="text-light">Pilih rute yang sesuai dengan preferensi perjalanan
                                                Anda</small>
                                        </div>
                                        <div class="card-body p-0">
                                            <div class="table-responsive">
                                                <table class="table table-hover mb-0" id="tabelRuteAlternatif">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th class="text-center" width="5%">#</th>
                                                            <th width="15%">
                                                                <i class="fas fa-ruler text-success"></i> Jarak
                                                            </th>
                                                            <th width="15%">
                                                                <i class="fas fa-clock text-warning"></i> Waktu
                                                            </th>
                                                            <th width="25%">
                                                                <i class="fas fa-route text-secondary"></i> Destinasi yang
                                                                Dilalui
                                                            </th>
                                                            {{-- <th width="10%">
                                                                <i class="fas fa-star text-warning"></i> Tingkat
                                                            </th> --}}
                                                            <th width="10%" class="text-center">Aksi</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($hasilRute["semua_rute_alternatif"] as $index => $rute)
                                                            <tr class="rute-row {{ $index === 0 ? "table-primary" : "" }}"
                                                                data-rute-index="{{ $index }}"
                                                                data-rute-color="{{ $rute["warna_rute"] ?? "#007bff" }}">
                                                                <td class="text-center fw-bold">
                                                                    <div class="rute-badge"
                                                                        style="background-color: {{ $rute["warna_rute"] ?? "#007bff" }};">
                                                                        {{ $rute["nomor_rute"] }}
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <strong
                                                                        class="text-success">{{ number_format($rute["jarak_rute"], 1) }}
                                                                        km</strong>
                                                                    @if ($index === 0)
                                                                        <br><small class="text-muted">Terdekat</small>
                                                                    @else
                                                                        <br><small class="text-muted">
                                                                            +{{ number_format($rute["jarak_rute"] - $hasilRute["semua_rute_alternatif"][0]["jarak_rute"], 1) }}
                                                                            km
                                                                        </small>
                                                                    @endif
                                                                </td>
                                                                <td>
                                                                    <strong
                                                                        class="text-warning">{{ $rute["waktu_rute"] }} 🚗/🏍️</strong>
                                                                </td>
                                                                <td>
                                                                    @if (!empty($rute["semua_destinasi_dilalui"]))
                                                                        <div class="destinasi-list">
                                                                            @foreach ($rute["semua_destinasi_dilalui"] as $destinasi)
                                                                                @php
                                                                                    $badgeClass =
                                                                                        $destinasi["posisi"] === "awal"
                                                                                            ? "bg-success"
                                                                                            : ($destinasi["posisi"] ===
                                                                                            "tujuan"
                                                                                                ? "bg-danger"
                                                                                                : "bg-info");
                                                                                    $icon =
                                                                                        $destinasi["posisi"] === "awal"
                                                                                            ? "fa-play"
                                                                                            : ($destinasi["posisi"] ===
                                                                                            "tujuan"
                                                                                                ? "fa-flag-checkered"
                                                                                                : "fa-map-marker-alt");
                                                                                    $titleText =
                                                                                        $destinasi["posisi"] === "awal"
                                                                                            ? "Titik Awal Perjalanan"
                                                                                            : ($destinasi["posisi"] ===
                                                                                            "tujuan"
                                                                                                ? "Destinasi Tujuan Akhir"
                                                                                                : "Destinasi Transit/Singgah");
                                                                                @endphp
                                                                                <small
                                                                                    class="badge {{ $badgeClass }} me-1 mb-1"
                                                                                    title="{{ $titleText }}: {{ $destinasi["nama"] }} (Urutan ke-{{ $destinasi["urutan"] }})"
                                                                                    data-bs-toggle="tooltip">
                                                                                    <i class="fas {{ $icon }}"></i>
                                                                                    {{ $destinasi["urutan"] }}.
                                                                                    {{ $destinasi["nama"] }}
                                                                                </small>
                                                                                @if (!$loop->last && $destinasi["posisi"] !== "tujuan")
                                                                                    <i class="fas fa-arrow-right text-muted mx-1"
                                                                                        style="font-size: 10px;"></i>
                                                                                @endif
                                                                            @endforeach
                                                                        </div>
                                                                    @else
                                                                        <small class="text-muted">Tidak ada data
                                                                            rute</small>
                                                                    @endif
                                                                </td>
                                                                {{-- <td>
                                                                    @php
                                                                        $tingkat =
                                                                            $rute["tingkat_kemudahan"] ?? "Sedang";
                                                                        $badgeClass =
                                                                            $tingkat === "Mudah"
                                                                                ? "bg-success"
                                                                                : ($tingkat === "Sedang"
                                                                                    ? "bg-warning"
                                                                                    : "bg-danger");
                                                                    @endphp
                                                                    <span class="badge {{ $badgeClass }}">
                                                                        @if ($tingkat === "Mudah")
                                                                            <i class="fas fa-smile"></i>
                                                                        @elseif($tingkat === "Sedang")
                                                                            <i class="fas fa-meh"></i>
                                                                        @else
                                                                            <i class="fas fa-frown"></i>
                                                                        @endif
                                                                        {{ $tingkat }}
                                                                    </span>
                                                                </td> --}}
                                                                <td class="text-center">
                                                                    <button
                                                                        class="btn btn-outline-primary btn-sm pilih-rute-btn"
                                                                        data-rute-index="{{ $index }}"
                                                                        title="Pilih rute ini">
                                                                        <i class="fas fa-eye"></i>
                                                                    </button>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>

                                            <!-- Info Footer -->
                                            <div class="card-footer bg-light">
                                                <div class="row text-center">
                                                    <div class="col-md-2">
                                                        <small class="text-muted">
                                                            <i class="fas fa-info-circle"></i>
                                                            {{ count($hasilRute["semua_rute_alternatif"]) }} rute ditemukan
                                                        </small>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <small class="text-success">
                                                            <i class="fas fa-check-circle"></i>
                                                            Terdekat:
                                                            {{ number_format($hasilRute["semua_rute_alternatif"][0]["jarak_rute"], 1) }}
                                                            km
                                                        </small>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <small class="text-warning">
                                                            <i class="fas fa-clock"></i>
                                                            Tercepat:
                                                            {{ $hasilRute["semua_rute_alternatif"][0]["waktu_rute"] }}
                                                        </small>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <small class="text-primary">
                                                            <i class="fas fa-map-marked-alt"></i>
                                                            Total
                                                            {{ array_sum(array_column($hasilRute["semua_rute_alternatif"], "jumlah_transit")) + count($hasilRute["semua_rute_alternatif"]) * 2 }}
                                                            destinasi tersedia
                                                        </small>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <small class="text-info" id="ruteAktifInfo">
                                                            <i class="fas fa-route"></i>
                                                            Rute 1 sedang aktif
                                                        </small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        {{-- Card Rute Terpendek Terpilih --}}
                        @if (isset($hasilRute["semua_rute_alternatif"]) && count($hasilRute["semua_rute_alternatif"]) > 0)
                            @php
                                $ruteTerpendek = $hasilRute["semua_rute_alternatif"][0]; // Ambil rute pertama (terpendek)
                            @endphp
                            <div class="row mb-4">
                                <div class="col-12">
                                    <div class="card border-0 shadow-sm bg-gradient-light">
                                        <div class="card-header bg-primary text-white">
                                            <h5 class="mb-0 d-flex align-items-center text-white">
                                                <i class="fas fa-crown me-2"></i>
                                                Rute Terpendek yang Direkomendasikan
                                            </h5>
                                            <small class="text-light">Detail lengkap rute tercepat menuju destinasi
                                                Anda</small>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <!-- Kolom Kiri: Info Utama -->
                                                <div class="col-md-6">
                                                    <div class="info-box mb-3 p-3 bg-light rounded">
                                                        <h6 class="fw-bold text-primary mb-3">
                                                            <i class="fas fa-info-circle"></i> Informasi Rute
                                                        </h6>
                                                        <div class="row mb-2">
                                                            <div class="col-5"><i class="fas fa-hashtag text-muted"></i>
                                                                <strong>Nomor Rute</strong>
                                                            </div>
                                                            <div class="col-1">:</div>
                                                            <div class="col-6">
                                                                <span class="badge"
                                                                    style="background-color: {{ $ruteTerpendek["warna_rute"] ?? "#007bff" }}; font-size: 14px;">
                                                                    Rute {{ $ruteTerpendek["nomor_rute"] }}
                                                                </span>
                                                            </div>
                                                        </div>
                                                        <div class="row mb-2">
                                                            <div class="col-5"><i class="fas fa-ruler text-muted"></i>
                                                                <strong>Jarak Tempuh</strong>
                                                            </div>
                                                            <div class="col-1">:</div>
                                                            <div class="col-6">
                                                                <span class="text-success fw-bold fs-5">
                                                                    {{ number_format($ruteTerpendek["jarak_rute"], 2) }} km
                                                                </span>
                                                            </div>
                                                        </div>
                                                        <div class="row mb-2">
                                                            <div class="col-5"><i class="fas fa-clock text-muted"></i>
                                                                <strong>Waktu Tempuh</strong>
                                                            </div>
                                                            <div class="col-1">:</div>
                                                            <div class="col-6">
                                                                <span class="text-warning fw-bold fs-5">
                                                                    {{ $ruteTerpendek["waktu_rute"] }} 🚗/🏍️
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Kolom Kanan: Destinasi Dilalui -->
                                                <div class="col-md-6">
                                                    <div class="route-path-box p-3 bg-light rounded">
                                                        <h6 class="fw-bold text-primary mb-3">
                                                            <i class="fas fa-map-marked-alt"></i> Jalur Perjalanan
                                                        </h6>
                                                        @if (!empty($ruteTerpendek["semua_destinasi_dilalui"]))
                                                            <div class="route-timeline">
                                                                @foreach ($ruteTerpendek["semua_destinasi_dilalui"] as $index => $destinasi)
                                                                    <div class="timeline-item d-flex mb-3">
                                                                        <div class="timeline-marker me-3">
                                                                            @if ($destinasi["posisi"] === "awal")
                                                                                <div class="marker-circle bg-success"
                                                                                    title="Titik Awal">
                                                                                    <i class="fas fa-play text-white"></i>
                                                                                </div>
                                                                            @elseif($destinasi["posisi"] === "tujuan")
                                                                                <div class="marker-circle bg-danger"
                                                                                    title="Tujuan Akhir">
                                                                                    <i
                                                                                        class="fas fa-flag-checkered text-white"></i>
                                                                                </div>
                                                                            @else
                                                                                <div class="marker-circle bg-info"
                                                                                    title="Transit">
                                                                                    <i
                                                                                        class="fas fa-map-marker-alt text-white"></i>
                                                                                </div>
                                                                            @endif
                                                                            @if (!$loop->last)
                                                                                <div class="timeline-line"></div>
                                                                            @endif
                                                                        </div>
                                                                        <div class="timeline-content flex-grow-1">
                                                                            <div class="fw-bold">
                                                                                {{ $destinasi["urutan"] }}.
                                                                                {{ $destinasi["nama"] }}
                                                                            </div>
                                                                            <small class="text-muted">
                                                                                @if ($destinasi["posisi"] === "awal")
                                                                                    <i class="fas fa-info-circle"></i>
                                                                                    Titik
                                                                                    awal perjalanan
                                                                                @elseif($destinasi["posisi"] === "tujuan")
                                                                                    <i class="fas fa-info-circle"></i>
                                                                                    Destinasi tujuan akhir
                                                                                @else
                                                                                    <i class="fas fa-info-circle"></i>
                                                                                    Titik
                                                                                    transit/singgah
                                                                                @endif
                                                                            </small>
                                                                        </div>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        @else
                                                            <p class="text-muted mb-0">
                                                                <i class="fas fa-exclamation-circle"></i> Data jalur tidak
                                                                tersedia
                                                            </p>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Peta -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <!-- Toggle Controls -->
                                <div class="d-flex justify-content-between align-items-center mb-3 toggle-header">
                                    <h5 class="mb-0">Peta Rute</h5>
                                    <div class="d-flex gap-3">
                                        <!-- Toggle Jenis Rute -->
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="toggleJenisRute" checked>
                                            <label class="form-check-label" for="toggleJenisRute">
                                                <i class="fas fa-route me-1"></i>
                                                Dengan Transit
                                            </label>
                                        </div>

                                        <!-- Toggle Destinasi -->
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="toggleDestinasi" checked>
                                            <label class="form-check-label" for="toggleDestinasi">
                                                <i class="fas fa-map-marker-alt me-1"></i>
                                                Semua Destinasi
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <!-- Kontrol Rute Alternatif -->
                                <div class="row mb-3">
                                    <div class="col-12">
                                        <div class="kontrol-rute-alternatif" id="kontrolRuteAlternatif">
                                            <button class="btn btn-rute-alternatif btn-outline-primary btn-sm active"
                                                id="btnRute1" data-rute="1">
                                                <i class="fas fa-route"></i> Rute 1 (Transit)
                                            </button>
                                            <button class="btn btn-rute-alternatif btn-outline-success btn-sm"
                                                id="btnRute2" data-rute="2">
                                                <i class="fas fa-route"></i> Rute API MAPS (Langsung)
                                            </button>
                                            <button class="btn btn-rute-alternatif btn-outline-warning btn-sm"
                                                id="btnRute3" data-rute="3" style="display: none;">
                                                <i class="fas fa-route"></i> Rute via Pematang Siantar
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div class="peta-container">
                                    <div id="peta"
                                        style="height: 500px; border: 2px solid #ddd; border-radius: 8px;">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Legend Peta -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="legend-peta p-3 bg-light rounded">
                                    <h6 class="fw-bold mb-3">Keterangan Peta:</h6>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="legend-item mb-2">
                                                <div class="legend-marker me-2" style="background-color: #28a745;"></div>
                                                <small>{{ $namaLokasiAwal }}</small>
                                            </div>
                                            <div class="legend-item mb-2">
                                                <div class="legend-marker me-2" style="background-color: #007bff;"></div>
                                                <small>Titik Awal Rute</small>
                                            </div>
                                            <div class="legend-item mb-2">
                                                <div class="legend-marker me-2" style="background-color: #dc3545;"></div>
                                                <small>Tujuan Akhir</small>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="legend-item mb-2">
                                                <div class="legend-marker me-2" style="background-color: #ffc107;"></div>
                                                <small>Destinasi Unggulan Lainnya</small>
                                            </div>
                                            <div class="legend-item mb-2">
                                                <div class="legend-marker me-2" style="background-color: #6c757d;"></div>
                                                <small>Destinasi Wisata Lainnya</small>
                                            </div>
                                            <div class="legend-item mb-2">
                                                <div class="legend-line me-2"
                                                    style="background-color: #007bff; height: 3px; width: 20px;"></div>
                                                <small>Jalur Rute 1 (Transit)</small>
                                            </div>
                                            <div class="legend-item mb-2">
                                                <div class="legend-line me-2"
                                                    style="background-color: #28a745; height: 3px; width: 20px;"></div>
                                                <small>Jalur Rute 2 (Langsung)</small>
                                            </div>
                                            <div class="legend-item mb-2" id="legendRute3" style="display: none;">
                                                <div class="legend-line me-2"
                                                    style="background-color: #ffc107; height: 3px; width: 20px;"></div>
                                                <small>Jalur Rute 3 (Via Pematang Siantar)</small>
                                            </div>
                                            @if (isset($hasilRute["semua_rute_alternatif"]) && count($hasilRute["semua_rute_alternatif"]) > 3)
                                                @foreach (array_slice($hasilRute["semua_rute_alternatif"], 3, 3) as $index => $rute)
                                                    <div class="legend-item mb-2">
                                                        <div class="legend-line me-2"
                                                            style="background-color: {{ $rute["warna_rute"] ?? "#6c757d" }}; height: 3px; width: 20px;">
                                                        </div>
                                                        <small>Jalur Rute {{ $index + 4 }}
                                                            @if ($rute["jumlah_transit"] === 0)
                                                                (Langsung)
                                                            @else
                                                                ({{ $rute["jumlah_transit"] }} Transit)
                                                            @endif
                                                        </small>
                                                    </div>
                                                @endforeach
                                            @endif
                                        </div>
                                    </div>
                                    <div class="mt-2">
                                        <small class="text-muted">
                                            <i class="fas fa-info-circle"></i>
                                            Klik marker destinasi lain untuk membuat rute baru.
                                            Gunakan toggle di atas untuk menampilkan/menyembunyikan destinasi lain.
                                        </small>
                                        <br>
                                        <small class="text-muted" id="infoRuteAktif">
                                            <i class="fas fa-route text-primary"></i> Rute 1 (Transit) sedang aktif
                                        </small>
                                        <br>
                                        <small class="text-muted" id="counterDestinasi">
                                            (Memuat data destinasi...)
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tombol Kembali -->
                        <div class="row">
                            <div class="col-12 text-end">
                                <a href="{{ route("pengunjung.wisata") }}" class="btn btn-primary">
                                    <i class="bi bi-arrow-left"></i> Kembali Ke Daftar Wisata
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Toast Notification Container -->
    <div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 9999;">
        <div id="toastNotif" class="toast align-items-center text-white border-0" role="alert" aria-live="assertive"
            aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body" id="toastMessage">
                    <!-- Pesan akan diisi via JavaScript -->
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"
                    aria-label="Close"></button>
            </div>
        </div>
    </div>
@endsection

@push("style")
    <style>
        /* Styling untuk card rute terpendek */
        .bg-gradient-light {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        }

        /* Timeline styling */
        .route-timeline {
            position: relative;
        }

        .timeline-item {
            position: relative;
        }

        .timeline-marker {
            position: relative;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .marker-circle {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
            z-index: 2;
            position: relative;
        }

        .timeline-line {
            width: 3px;
            flex-grow: 1;
            background: linear-gradient(to bottom, #dee2e6 0%, #adb5bd 100%);
            margin: 5px 0;
            min-height: 30px;
        }

        .timeline-content {
            padding-top: 8px;
        }

        /* Info box styling */
        .info-box {
            border-left: 4px solid #007bff;
        }

        .route-path-box {
            border-left: 4px solid #28a745;
            max-height: 400px;
            overflow-y: auto;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .marker-circle {
                width: 32px;
                height: 32px;
                font-size: 14px;
            }

            .timeline-line {
                min-height: 20px;
            }
        }
    </style>
@endpush

@push("script")
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

    <script type="text/javascript">
        // ==========================================
        // VARIABEL GLOBAL
        // ==========================================
        let markersDestinasiLain = [];
        let ruteAktif = 1; // 1 = rute dengan transit, 2 = rute langsung, 3 = rute via Pematang Siantar
        let garisRute1 = null; // Garis untuk rute 1 (transit)
        let garisRute2 = null; // Garis untuk rute 2 (langsung)
        let garisRute3 = null; // Garis untuk rute 3 (via Pematang Siantar)
        let infoRute1 = null; // Info jarak dan waktu untuk rute 1
        let infoRute2 = null; // Info jarak dan waktu untuk rute 2
        let infoRute3 = null; // Info jarak dan waktu untuk rute 3

        // Data rute alternatif
        let semuaRuteAlternatif = @json($hasilRute["semua_rute_alternatif"] ?? []);
        let garisSemuaRute = {}; // Menyimpan semua garis rute
        let ruteAlternatifAktif = 0; // Index rute alternatif yang sedang aktif

        // ==========================================
        // FUNGSI TOAST NOTIFICATION
        // ==========================================
        function tampilkanToast(pesan, tipe = 'info') {
            const toastEl = document.getElementById('toastNotif');
            const toastBody = document.getElementById('toastMessage');

            // Set pesan
            toastBody.innerHTML = pesan;

            // Set warna berdasarkan tipe
            toastEl.classList.remove('bg-danger', 'bg-warning', 'bg-success', 'bg-info');
            switch (tipe) {
                case 'error':
                    toastEl.classList.add('bg-danger');
                    break;
                case 'warning':
                    toastEl.classList.add('bg-warning');
                    break;
                case 'success':
                    toastEl.classList.add('bg-success');
                    break;
                default:
                    toastEl.classList.add('bg-info');
            }

            // Tampilkan toast
            const toast = new bootstrap.Toast(toastEl, {
                autohide: true,
                delay: 5000 // 5 detik
            });
            toast.show();
        }

        // ==========================================
        // INISIALISASI DOCUMENT READY
        // ==========================================
        $(document).ready(function() {
            // Inisialisasi awal: Tampilkan data database di info rute
            inisialisasiInfoRuteAwal();

            inisialisasiPeta();

            // Event handler untuk toggle destinasi
            $('#toggleDestinasi').change(function() {
                toggleSemuaDestinasi($(this).is(':checked'));
            });

            // Event handler untuk toggle jenis rute
            $('#toggleJenisRute').change(function() {
                toggleJenisRute($(this).is(':checked'));
            });

            // Event handler untuk tombol rute alternatif
            $('#btnRute1').click(function() {
                pilihRuteAlternatif(1);
            });

            $('#btnRute2').click(function() {
                pilihRuteAlternatif(2);
            });

            $('#btnRute3').click(function() {
                pilihRuteAlternatif(3);
            });

            // Event handler untuk tabel rute alternatif
            $('.pilih-rute-btn').click(function() {
                const ruteIndex = $(this).data('rute-index');
                pilihRuteAlternatifDariTabel(ruteIndex);
            });

            // Event handler untuk hover row tabel
            $('.rute-row').hover(
                function() {
                    const ruteIndex = $(this).data('rute-index');
                    highlightRuteDiPeta(ruteIndex, true);
                },
                function() {
                    const ruteIndex = $(this).data('rute-index');
                    highlightRuteDiPeta(ruteIndex, false);
                }
            );

            // Inisialisasi Bootstrap tooltips untuk destinasi badges
            $('[data-bs-toggle="tooltip"]').tooltip({
                container: 'body',
                placement: 'top',
                trigger: 'hover'
            });
        });

        // ==========================================
        // FUNGSI UTILITY
        // ==========================================

        // Fungsi untuk inisialisasi info rute saat halaman loading
        function inisialisasiInfoRuteAwal() {
            // Pastikan data awal dari database ditampilkan
            const dataAsliJarak = "{{ number_format($hasilRute["jarak_total"], 2) }} km";
            const dataAsliWaktu = "{{ $hasilRute["waktu_tempuh"] }}";

            // Update jarak dan waktu di info panel dengan data database menggunakan ID spesifik
            const jarakElement = $('#jarakTempuh');
            const waktuElement = $('#waktuTempuh');

            // Validasi elemen ditemukan
            if (jarakElement.length === 0) {
                console.error('Element #jarakTempuh tidak ditemukan!');
                return;
            }
            if (waktuElement.length === 0) {
                console.error('Element #waktuTempuh tidak ditemukan!');
                return;
            }

            jarakElement.text(dataAsliJarak);
            waktuElement.text(dataAsliWaktu);

            console.log('Inisialisasi awal: Data dari database ditampilkan');
            console.log('Jarak awal:', dataAsliJarak);
            console.log('Waktu awal:', dataAsliWaktu);
            console.log('Element #jarakTempuh:', jarakElement.text());
            console.log('Element #waktuTempuh:', waktuElement.text());
        }

        // Fungsi untuk update counter destinasi
        function updateCounterDestinasi() {
            const totalDestinasi = markersDestinasiLain.length;
            let visibleDestinasi = 0;

            markersDestinasiLain.forEach(marker => {
                if (window.petaGlobal && window.petaGlobal.hasLayer(marker)) {
                    visibleDestinasi++;
                }
            });

            if (totalDestinasi === 0) {
                $('#counterDestinasi').html('<i class="fas fa-info-circle text-muted"></i> Memuat data destinasi...');
            } else if (visibleDestinasi === 0) {
                $('#counterDestinasi').html(
                    `<i class="fas fa-map-marker-alt text-muted"></i> ${totalDestinasi} destinasi tersedia (semua disembunyikan)`
                );
            } else {
                $('#counterDestinasi').html(
                    `<i class="fas fa-map-marker-alt text-danger"></i> ${visibleDestinasi} dari ${totalDestinasi} destinasi ditampilkan`
                );
            }
        }

        // ==========================================
        // FUNGSI KONTROL RUTE
        // ==========================================

        // Fungsi untuk toggle jenis rute (dengan transit atau langsung)
        function toggleJenisRute(denganTransit) {
            const label = $('label[for="toggleJenisRute"]');
            const kontrolRute = $('#kontrolRuteAlternatif');

            if (denganTransit) {
                label.html('<i class="fas fa-route me-1"></i>Dengan Transit');
                kontrolRute.show();
                // Aktifkan rute 1 (transit) secara default
                if (ruteAktif !== 1) {
                    pilihRuteAlternatif(1);
                }
            } else {
                label.html('<i class="fas fa-route me-1"></i>Rute Langsung');
                kontrolRute.hide();
                // Langsung aktifkan rute 2 (langsung)
                if (ruteAktif !== 2) {
                    pilihRuteAlternatif(2);
                }
            }
        }

        // Fungsi untuk memilih rute alternatif
        function pilihRuteAlternatif(nomorRute) {
            // Cek apakah data rute sudah tersedia (hanya untuk garis peta)
            if (nomorRute === 1 && !garisRute1) {
                console.log('Data rute 1 belum tersedia, menunggu...');
                $('#infoRuteAktif').html('<i class="fas fa-spinner fa-spin text-primary"></i> Memuat rute 1...');
                setTimeout(() => pilihRuteAlternatif(nomorRute), 500);
                return;
            }

            if (nomorRute === 2 && !garisRute2) {
                console.log('Data rute 2 belum tersedia, menunggu...');
                $('#infoRuteAktif').html('<i class="fas fa-spinner fa-spin text-success"></i> Memuat rute 2...');
                setTimeout(() => pilihRuteAlternatif(nomorRute), 500);
                return;
            }

            if (nomorRute === 3 && !garisRute3) {
                console.log('Data rute 3 belum tersedia, menunggu...');
                $('#infoRuteAktif').html('<i class="fas fa-spinner fa-spin text-warning"></i> Memuat rute 3...');
                setTimeout(() => pilihRuteAlternatif(nomorRute), 500);
                return;
            }

            ruteAktif = nomorRute;

            // Update tombol aktif
            $('#btnRute1').removeClass('active btn-primary').addClass('btn-outline-primary');
            $('#btnRute2').removeClass('active btn-success').addClass('btn-outline-success');
            $('#btnRute3').removeClass('active btn-warning').addClass('btn-outline-warning');

            if (nomorRute === 1) {
                $('#btnRute1').addClass('active btn-primary').removeClass('btn-outline-primary');
                $('#infoRuteAktif').html('<i class="fas fa-route text-primary"></i> Rute 1 (Transit) sedang aktif');
            } else if (nomorRute === 2) {
                $('#btnRute2').addClass('active btn-success').removeClass('btn-outline-success');
                $('#infoRuteAktif').html('<i class="fas fa-route text-success"></i> Rute 2 (Langsung) sedang aktif');
            } else if (nomorRute === 3) {
                $('#btnRute3').addClass('active btn-warning').removeClass('btn-outline-warning');
                $('#infoRuteAktif').html(
                    '<i class="fas fa-route text-warning"></i> Rute 3 (Via Pematang Siantar) sedang aktif');
            }

            // Tampilkan/sembunyikan garis rute
            tampilkanRuteAktif();

            // Update info rute di UI - SELALU dipanggil untuk update tampilan
            updateInfoRute();
        }

        // Fungsi untuk menampilkan rute yang aktif
        function tampilkanRuteAktif() {
            const peta = window.petaGlobal;

            if (!peta) return;

            // Sembunyikan semua garis rute
            if (garisRute1 && peta.hasLayer(garisRute1)) {
                peta.removeLayer(garisRute1);
            }
            if (garisRute2 && peta.hasLayer(garisRute2)) {
                peta.removeLayer(garisRute2);
            }
            if (garisRute3 && peta.hasLayer(garisRute3)) {
                peta.removeLayer(garisRute3);
            }

            // Tampilkan rute yang aktif
            if (ruteAktif === 1 && garisRute1) {
                if (garisRute1 instanceof L.LayerGroup) {
                    // Jika layer group, tambahkan semua layer
                    garisRute1.eachLayer(layer => {
                        if (!peta.hasLayer(layer)) {
                            layer.addTo(peta);
                        }
                    });
                } else {
                    // Jika single layer
                    garisRute1.addTo(peta);
                }
            } else if (ruteAktif === 2 && garisRute2) {
                garisRute2.addTo(peta);
            } else if (ruteAktif === 3 && garisRute3) {
                garisRute3.addTo(peta);
            }
        }

        // Fungsi untuk update info rute di UI
        function updateInfoRute() {
            // Data asli dari database untuk rute 1 (transit)
            const dataAsliJarak = "{{ number_format($hasilRute["jarak_total"], 2) }} km";
            const dataAsliWaktu = "{{ $hasilRute["waktu_tempuh"] }}";

            // Update jarak dan waktu di info panel menggunakan ID yang spesifik
            const jarakElement = $('#jarakTempuh');
            const waktuElement = $('#waktuTempuh');

            // Validasi elemen ditemukan
            if (jarakElement.length === 0) {
                console.error('Element #jarakTempuh tidak ditemukan dalam updateInfoRute!');
                return;
            }
            if (waktuElement.length === 0) {
                console.error('Element #waktuTempuh tidak ditemukan dalam updateInfoRute!');
                return;
            }

            console.log(`=== UPDATE INFO RUTE ${ruteAktif} ===`);
            console.log('Data database - Jarak:', dataAsliJarak);
            console.log('Data database - Waktu:', dataAsliWaktu);

            if (ruteAktif === 1) {
                // Rute 1 (Transit) - SELALU gunakan data asli dari database
                jarakElement.text(dataAsliJarak);
                waktuElement.text(dataAsliWaktu);
                console.log('Menampilkan data rute 1 (transit) dari database');

            } else if (ruteAktif === 2) {
                // Rute 2 (Langsung) - gunakan data dari API jika tersedia, fallback ke database
                console.log('Mencoba menampilkan data rute 2 dari API...');
                console.log('infoRute2 data:', infoRute2);

                let jarakUpdated = false;
                let waktuUpdated = false;

                // Update JARAK untuk rute 2
                if (infoRute2 && infoRute2.jarak && infoRute2.jarak !== 0) {
                    let jarakFormatted;
                    if (typeof infoRute2.jarak === 'number') {
                        jarakFormatted = `${number_format(infoRute2.jarak, 2)} km`;
                    } else if (typeof infoRute2.jarak === 'string') {
                        jarakFormatted = infoRute2.jarak;
                    }

                    if (jarakFormatted) {
                        jarakElement.text(jarakFormatted);
                        jarakUpdated = true;
                        console.log('Jarak rute 2 diupdate dari API:', jarakFormatted);
                    }
                }

                // Update WAKTU untuk rute 2
                if (infoRute2 && infoRute2.waktu && infoRute2.waktu !== 'Menghitung...' && infoRute2.waktu !== 0) {
                    let waktuFormatted;

                    if (typeof infoRute2.waktu === 'string') {
                        // Format string asli dari API
                        waktuFormatted = infoRute2.waktu;
                    } else if (typeof infoRute2.waktu === 'number') {
                        // Konversi dari menit ke format readable
                        if (infoRute2.waktu >= 60) {
                            const jam = Math.floor(infoRute2.waktu / 60);
                            const sisaMenit = Math.round(infoRute2.waktu % 60);
                            waktuFormatted = sisaMenit > 0 ? `${jam} jam ${sisaMenit} menit` : `${jam} jam`;
                        } else {
                            waktuFormatted = `${Math.round(infoRute2.waktu)} menit`;
                        }
                    }

                    if (waktuFormatted) {
                        waktuElement.text(waktuFormatted);
                        waktuUpdated = true;
                        console.log('Waktu rute 2 diupdate dari API:', waktuFormatted);
                    }
                }

                // Fallback ke data database jika API tidak memberikan data
                if (!jarakUpdated) {
                    jarakElement.text(dataAsliJarak);
                    console.log('Jarak rute 2 fallback ke database:', dataAsliJarak);
                }

                if (!waktuUpdated) {
                    waktuElement.text(dataAsliWaktu);
                    console.log('Waktu rute 2 fallback ke database:', dataAsliWaktu);
                }

                console.log('Data rute 2 selesai diproses');
            } else if (ruteAktif === 3) {
                // Rute 3 (Via Pematang Siantar) - gunakan data dari API jika tersedia, fallback ke database
                console.log('Mencoba menampilkan data rute 3 dari API...');
                console.log('infoRute3 data:', infoRute3);

                let jarakUpdated = false;
                let waktuUpdated = false;

                // Update JARAK untuk rute 3
                if (infoRute3 && infoRute3.jarak && infoRute3.jarak !== 0) {
                    let jarakFormatted;
                    if (typeof infoRute3.jarak === 'number') {
                        jarakFormatted = `${number_format(infoRute3.jarak, 2)} km`;
                    } else if (typeof infoRute3.jarak === 'string') {
                        jarakFormatted = infoRute3.jarak;
                    }

                    if (jarakFormatted) {
                        jarakElement.text(jarakFormatted);
                        jarakUpdated = true;
                        console.log('Jarak rute 3 diupdate dari API:', jarakFormatted);
                    }
                }

                // Update WAKTU untuk rute 3
                if (infoRute3 && infoRute3.waktu && infoRute3.waktu !== 'Menghitung...' && infoRute3.waktu !== 0) {
                    let waktuFormatted;

                    if (typeof infoRute3.waktu === 'string') {
                        // Format string asli dari API
                        waktuFormatted = infoRute3.waktu;
                    } else if (typeof infoRute3.waktu === 'number') {
                        // Konversi dari menit ke format readable
                        if (infoRute3.waktu >= 60) {
                            const jam = Math.floor(infoRute3.waktu / 60);
                            const sisaMenit = Math.round(infoRute3.waktu % 60);
                            waktuFormatted = sisaMenit > 0 ? `${jam} jam ${sisaMenit} menit` : `${jam} jam`;
                        } else {
                            waktuFormatted = `${Math.round(infoRute3.waktu)} menit`;
                        }
                    }

                    if (waktuFormatted) {
                        waktuElement.text(waktuFormatted);
                        waktuUpdated = true;
                        console.log('Waktu rute 3 diupdate dari API:', waktuFormatted);
                    }
                }

                // Fallback ke data database jika API tidak memberikan data
                if (!jarakUpdated) {
                    jarakElement.text(dataAsliJarak);
                    console.log('Jarak rute 3 fallback ke database:', dataAsliJarak);
                }

                if (!waktuUpdated) {
                    waktuElement.text(dataAsliWaktu);
                    console.log('Waktu rute 3 fallback ke database:', dataAsliWaktu);
                }

                console.log('Data rute 3 selesai diproses');
            }

            // Log hasil akhir yang ditampilkan
            console.log('=== HASIL AKHIR TAMPILAN ===');
            console.log('Jarak yang ditampilkan:', jarakElement.text());
            console.log('Waktu yang ditampilkan:', waktuElement.text());
            console.log(`Info rute ${ruteAktif} update selesai`);
        }

        // Fungsi helper untuk format number
        function number_format(number, decimals) {
            return parseFloat(number).toFixed(decimals);
        }

        // ==========================================
        // FUNGSI RUTE ALTERNATIF
        // ==========================================

        // Fungsi untuk memilih rute alternatif dari tabel
        function pilihRuteAlternatifDariTabel(ruteIndex) {
            console.log('Memilih rute alternatif index:', ruteIndex);

            // Update rute aktif
            ruteAlternatifAktif = ruteIndex;

            // Update tampilan tabel
            updateTampilanTabelRute(ruteIndex);

            // Update info panel dengan data rute yang dipilih
            updateInfoRuteAlternatif(ruteIndex);

            // Tampilkan rute di peta
            tampilkanRuteAlternatifDiPeta(ruteIndex);

            // Update info footer
            updateInfoRuteAktif(ruteIndex);
        }

        // Fungsi untuk update tampilan tabel rute
        function updateTampilanTabelRute(ruteIndex) {
            // Hapus highlight dari semua row
            $('.rute-row').removeClass('table-primary table-success table-warning');

            // Highlight row yang dipilih
            $(`.rute-row[data-rute-index="${ruteIndex}"]`).addClass('table-primary');

            // Update button state
            $('.pilih-rute-btn').removeClass('btn-primary').addClass('btn-outline-primary');
            $(`.pilih-rute-btn[data-rute-index="${ruteIndex}"]`).removeClass('btn-outline-primary').addClass('btn-primary');
        }

        // Fungsi untuk update info panel dengan data rute alternatif
        function updateInfoRuteAlternatif(ruteIndex) {
            if (!semuaRuteAlternatif[ruteIndex]) return;

            const rute = semuaRuteAlternatif[ruteIndex];
            const jarakElement = $('#jarakTempuh');
            const waktuElement = $('#waktuTempuh');

            // Update dengan data rute yang dipilih
            jarakElement.text(`${number_format(rute.jarak_rute, 2)} km`);
            waktuElement.text(rute.waktu_rute);

            console.log(`Info panel diupdate dengan rute ${ruteIndex + 1}:`, {
                jarak: rute.jarak_rute,
                waktu: rute.waktu_rute
            });
        }

        // Fungsi untuk menampilkan rute alternatif di peta
        function tampilkanRuteAlternatifDiPeta(ruteIndex) {
            const peta = window.petaGlobal;
            if (!peta || !semuaRuteAlternatif[ruteIndex]) return;

            // Sembunyikan semua rute
            sembunyikanSemuaRuteDiPeta();

            // Tampilkan rute yang dipilih jika sudah digambar
            if (garisSemuaRute[ruteIndex]) {
                const garisRute = garisSemuaRute[ruteIndex];

                if (Array.isArray(garisRute)) {
                    // Jika array of layers
                    garisRute.forEach(garis => {
                        if (garis && !peta.hasLayer(garis)) {
                            garis.addTo(peta);
                        }
                    });
                } else if (garisRute instanceof L.LayerGroup) {
                    // Jika layer group
                    garisRute.addTo(peta);
                } else {
                    // Jika single layer
                    garisRute.addTo(peta);
                }

                console.log(`Rute ${ruteIndex + 1} ditampilkan di peta`);
            } else {
                console.log(`Rute ${ruteIndex + 1} belum digambar, akan dibuat...`);
                // Gambar rute jika belum ada
                gambarRuteAlternatifBaru(ruteIndex);
            }
        }

        // Fungsi untuk menyembunyikan semua rute di peta
        function sembunyikanSemuaRuteDiPeta() {
            const peta = window.petaGlobal;
            if (!peta) return;

            // Sembunyikan rute lama
            if (garisRute1 && peta.hasLayer(garisRute1)) {
                peta.removeLayer(garisRute1);
            }
            if (garisRute2 && peta.hasLayer(garisRute2)) {
                peta.removeLayer(garisRute2);
            }

            // Sembunyikan semua rute alternatif
            Object.values(garisSemuaRute).forEach(garisRute => {
                if (Array.isArray(garisRute)) {
                    garisRute.forEach(garis => {
                        if (garis && peta.hasLayer(garis)) {
                            peta.removeLayer(garis);
                        }
                    });
                } else if (garisRute instanceof L.LayerGroup) {
                    if (peta.hasLayer(garisRute)) {
                        peta.removeLayer(garisRute);
                    }
                } else if (garisRute && peta.hasLayer(garisRute)) {
                    peta.removeLayer(garisRute);
                }
            });
        }

        // Fungsi untuk highlight rute saat hover
        function highlightRuteDiPeta(ruteIndex, highlight) {
            const peta = window.petaGlobal;
            if (!peta || !garisSemuaRute[ruteIndex]) return;

            const garisRute = garisSemuaRute[ruteIndex];
            const opacity = highlight ? 1.0 : 0.8;
            const weight = highlight ? 6 : 4;

            if (Array.isArray(garisRute)) {
                garisRute.forEach(garis => {
                    if (garis && garis.setStyle) {
                        garis.setStyle({
                            opacity: opacity,
                            weight: weight
                        });
                    }
                });
            } else if (garisRute && garisRute.setStyle) {
                garisRute.setStyle({
                    opacity: opacity,
                    weight: weight
                });
            }
        }

        // Fungsi untuk update info rute aktif
        function updateInfoRuteAktif(ruteIndex) {
            const rute = semuaRuteAlternatif[ruteIndex];
            if (!rute) return;

            const jenisRute = rute.jumlah_transit === 0 ? 'Langsung' : `Dengan ${rute.jumlah_transit} Transit`;
            $('#ruteAktifInfo').html(`
                <i class="fas fa-route" style="color: ${rute.warna_rute};"></i>
                Rute ${rute.nomor_rute} (${jenisRute}) sedang aktif
            `);

            // Update info di legend juga
            $('#infoRuteAktif').html(`
                <i class="fas fa-route" style="color: ${rute.warna_rute};"></i>
                Rute ${rute.nomor_rute} (${jenisRute}) sedang aktif
            `);
        }

        // Fungsi untuk gambar rute alternatif baru
        function gambarRuteAlternatifBaru(ruteIndex) {
            const rute = semuaRuteAlternatif[ruteIndex];
            if (!rute || !rute.jalur) return;

            console.log(`Menggambar rute alternatif ${ruteIndex + 1}:`, rute);

            // Request data koordinat untuk rute alternatif
            $.ajax({
                url: '{{ route("api.rute-alternatif") }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    rute_index: ruteIndex,
                    jalur: rute.jalur
                },
                success: function(response) {
                    if (response.success && response.koordinat_rute) {
                        gambarRuteAlternatifDiPeta(ruteIndex, response.koordinat_rute, rute.warna_rute);
                    }
                },
                error: function(xhr, status, error) {
                    console.error(`Gagal memuat data rute ${ruteIndex + 1}:`, error);
                    // Fallback: buat garis lurus
                    buatGarisLurusFallback(ruteIndex, rute);
                }
            });
        }

        // Fungsi untuk gambar rute alternatif di peta dengan data koordinat
        function gambarRuteAlternatifDiPeta(ruteIndex, koordinatRute, warnaRute) {
            const peta = window.petaGlobal;
            if (!peta || !koordinatRute || koordinatRute.length < 2) return;

            console.log(
                `Menggambar rute alternatif ${ruteIndex + 1} di peta dengan ${koordinatRute.length} titik mengikuti jalan`
            );

            const garisRute = [];
            let segmenSelesai = 0;
            const totalSegmen = koordinatRute.length - 1;

            // Gambar rute antar titik secara berurutan menggunakan routing API
            for (let i = 0; i < koordinatRute.length - 1; i++) {
                const asal = koordinatRute[i];
                const tujuan = koordinatRute[i + 1];

                // Gunakan API routing untuk mendapatkan rute mengikuti jalan
                $.ajax({
                    url: '{{ route("api.rute-jalan") }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        koordinat_awal: asal,
                        koordinat_tujuan: tujuan
                    },
                    success: function(response) {
                        segmenSelesai++;

                        if (response.success && response.koordinat_rute) {
                            // Gambar garis mengikuti jalan sebenarnya
                            const garis = L.polyline(response.koordinat_rute, {
                                color: warnaRute || '#007bff',
                                weight: 4,
                                opacity: 0.8,
                                smoothFactor: 1
                            });

                            // Tambahkan tooltip dengan informasi detail
                            let tooltipText = `Rute ${ruteIndex + 1}: ${asal.nama} → ${tujuan.nama}`;
                            if (response.jarak) {
                                tooltipText += `<br>Jarak: ${response.jarak} km`;
                            }
                            if (response.durasi) {
                                tooltipText += `<br>Waktu: ${response.durasi} menit`;
                            }
                            if (response.fallback) {
                                tooltipText += '<br><small>(Rute perkiraan)</small>';
                            }

                            garis.bindTooltip(tooltipText, {
                                permanent: false,
                                sticky: true
                            });

                            garisRute.push(garis);
                        } else {
                            // Fallback: gunakan garis lurus jika API gagal
                            console.warn(
                                `Gagal mendapatkan rute jalan untuk segmen ${i + 1}, menggunakan garis lurus`
                            );
                            const koordinatGaris = [
                                [asal.lat, asal.lng],
                                [tujuan.lat, tujuan.lng]
                            ];

                            const garis = L.polyline(koordinatGaris, {
                                color: warnaRute || '#007bff',
                                weight: 4,
                                opacity: 0.8,
                                smoothFactor: 1,
                                dashArray: '5, 10' // Garis putus-putus untuk menandakan fallback
                            });

                            garis.bindTooltip(
                                `Rute ${ruteIndex + 1}: ${asal.nama} → ${tujuan.nama} (garis lurus)`, {
                                    permanent: false,
                                    sticky: true
                                });

                            garisRute.push(garis);
                        }

                        // Tambahkan marker transit jika ada (hanya sekali per titik)
                        if (i > 0 && i < koordinatRute.length - 1 && segmenSelesai === i + 1) {
                            const markerTransit = L.marker([asal.lat, asal.lng], {
                                icon: L.divIcon({
                                    className: 'marker-transit-alternatif',
                                    html: `<div style="background-color: ${warnaRute || '#ffc107'}; color: white; border-radius: 50%; width: 15px; height: 15px; display: flex; align-items: center; justify-content: center; font-size: 10px; border: 2px solid white; box-shadow: 0 1px 2px rgba(0,0,0,0.3);"><i class="bi bi-circle-fill"></i></div>`,
                                    iconSize: [15, 15],
                                    iconAnchor: [7, 7]
                                })
                            });

                            markerTransit.bindPopup(
                                `<b>Transit: ${asal.nama}</b><br><small>Rute Alternatif ${ruteIndex + 1}</small>`
                            );
                            garisRute.push(markerTransit);
                        }

                        // Jika semua segmen selesai, simpan garis rute
                        if (segmenSelesai === totalSegmen) {
                            garisSemuaRute[ruteIndex] = garisRute;
                            console.log(
                                `Rute alternatif ${ruteIndex + 1} berhasil digambar di peta dengan routing`);
                        }
                    },
                    error: function() {
                        segmenSelesai++;
                        console.warn(
                            `Error mendapatkan rute jalan untuk segmen ${i + 1}, menggunakan garis lurus`);

                        // Fallback: gunakan garis lurus
                        const koordinatGaris = [
                            [asal.lat, asal.lng],
                            [tujuan.lat, tujuan.lng]
                        ];

                        const garis = L.polyline(koordinatGaris, {
                            color: warnaRute || '#007bff',
                            weight: 4,
                            opacity: 0.8,
                            smoothFactor: 1,
                            dashArray: '5, 10' // Garis putus-putus untuk menandakan fallback
                        });

                        garis.bindTooltip(
                            `Rute ${ruteIndex + 1}: ${asal.nama} → ${tujuan.nama} (garis lurus)`, {
                                permanent: false,
                                sticky: true
                            });

                        garisRute.push(garis);

                        // Tambahkan marker transit jika ada (hanya sekali per titik)
                        if (i > 0 && i < koordinatRute.length - 1 && segmenSelesai === i + 1) {
                            const markerTransit = L.marker([asal.lat, asal.lng], {
                                icon: L.divIcon({
                                    className: 'marker-transit-alternatif',
                                    html: `<div style="background-color: ${warnaRute || '#ffc107'}; color: white; border-radius: 50%; width: 15px; height: 15px; display: flex; align-items: center; justify-content: center; font-size: 10px; border: 2px solid white; box-shadow: 0 1px 2px rgba(0,0,0,0.3);"><i class="bi bi-circle-fill"></i></div>`,
                                    iconSize: [15, 15],
                                    iconAnchor: [7, 7]
                                })
                            });

                            markerTransit.bindPopup(
                                `<b>Transit: ${asal.nama}</b><br><small>Rute Alternatif ${ruteIndex + 1}</small>`
                            );
                            garisRute.push(markerTransit);
                        }

                        // Jika semua segmen selesai, simpan garis rute
                        if (segmenSelesai === totalSegmen) {
                            garisSemuaRute[ruteIndex] = garisRute;
                            console.log(
                                `Rute alternatif ${ruteIndex + 1} berhasil digambar di peta (dengan fallback)`
                            );
                        }
                    }
                });
            }
        }

        // Fungsi fallback untuk membuat garis lurus
        function buatGarisLurusFallback(ruteIndex, rute) {
            console.log(`Membuat garis lurus fallback untuk rute ${ruteIndex + 1}`);

            // Implementasi fallback sederhana
            // Akan diimplementasi jika diperlukan
        }

        // ==========================================
        // FUNGSI INISIALISASI PETA
        // ==========================================
        function inisialisasiPeta() {
            // Tampilkan loading indicator
            $('#peta').html(
                '<div class="d-flex justify-content-center align-items-center h-100"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Memuat peta...</span></div><span class="ms-2">Memuat peta dan rute...</span></div>'
            );

            // Data dari server
            const lokasiAwal = {
                lat: {{ $lokasiAwal["latitude"] }},
                lng: {{ $lokasiAwal["longitude"] }}
            };

            const wisataAwal = {
                id: {{ $hasilRute["wisata_awal"]->id_wisata }},
                lat: {{ $hasilRute["wisata_awal"]->latitude }},
                lng: {{ $hasilRute["wisata_awal"]->longitude }},
                nama: "{{ $hasilRute["wisata_awal"]->nama_wisata }}"
            };

            const wisataTujuan = {
                lat: {{ $wisataTujuan->latitude }},
                lng: {{ $wisataTujuan->longitude }},
                nama: "{{ $wisataTujuan->nama_wisata }}"
            };

            const jalurWisata = @json($hasilRute["jalur"]);

            // Hapus loading dan inisialisasi peta
            setTimeout(function() {
                $('#peta').empty();

                // Inisialisasi peta
                const peta = L.map('peta').setView([wisataAwal.lat, wisataAwal.lng], 10);

                // Simpan referensi peta secara global
                window.petaGlobal = peta;

                // Tambahkan tile layer
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '© OpenStreetMap contributors'
                }).addTo(peta);

                // Marker untuk lokasi awal (posisi pengguna)
                const markerLokasiAwal = L.marker([lokasiAwal.lat, lokasiAwal.lng], {
                    icon: L.divIcon({
                        className: 'marker-lokasi-awal',
                        html: '<div style="background-color: #28a745; color: white; border-radius: 50%; width: 20px; height: 20px; display: flex; align-items: center; justify-content: center; font-size: 12px; border: 2px solid white; box-shadow: 0 2px 4px rgba(0,0,0,0.3);"><i class="bi bi-geo-alt-fill"></i></div>',
                        iconSize: [20, 20],
                        iconAnchor: [10, 10]
                    })
                }).addTo(peta);
                markerLokasiAwal.bindPopup('<b>{{ $namaLokasiAwal }}</b>').openPopup();

                // Marker untuk wisata awal terdekat
                const markerWisataAwal = L.marker([wisataAwal.lat, wisataAwal.lng], {
                    icon: L.divIcon({
                        className: 'marker-wisata-awal',
                        html: '<div style="background-color: #007bff; color: white; border-radius: 50%; width: 25px; height: 25px; display: flex; align-items: center; justify-content: center; font-size: 12px; border: 2px solid white; box-shadow: 0 2px 4px rgba(0,0,0,0.3);"><i class="bi bi-flag-fill"></i></div>',
                        iconSize: [25, 25],
                        iconAnchor: [12, 12]
                    })
                }).addTo(peta);
                markerWisataAwal.bindPopup('<b>Titik Awal: ' + wisataAwal.nama +
                    '</b><br>Jarak dari lokasi Anda: {{ number_format($hasilRute["jarak_ke_wisata_awal"], 2) }} km'
                );

                // Marker untuk wisata tujuan
                const markerWisataTujuan = L.marker([wisataTujuan.lat, wisataTujuan.lng], {
                    icon: L.divIcon({
                        className: 'marker-wisata-tujuan',
                        html: '<div style="background-color: #dc3545; color: white; border-radius: 50%; width: 25px; height: 25px; display: flex; align-items: center; justify-content: center; font-size: 12px; border: 2px solid white; box-shadow: 0 2px 4px rgba(0,0,0,0.3);"><i class="bi bi-geo-alt-fill"></i></div>',
                        iconSize: [25, 25],
                        iconAnchor: [12, 12]
                    })
                }).addTo(peta);
                markerWisataTujuan.bindPopup('<b>Tujuan: ' + wisataTujuan.nama + '</b>');

                // Dapatkan koordinat rute dari server
                dapatkanKoordinatRute(jalurWisata, peta, lokasiAwal, wisataAwal, wisataTujuan);

                // Muat dan tampilkan semua destinasi wisata lainnya
                muatSemuaDestinasi(peta, wisataAwal, wisataTujuan);

                // Inisialisasi default rute aktif setelah semua data dimuat
                setTimeout(() => {
                    // Inisialisasi semua rute alternatif jika ada
                    if (semuaRuteAlternatif && semuaRuteAlternatif.length > 0) {
                        console.log('Memulai inisialisasi rute alternatif:', semuaRuteAlternatif.length,
                            'rute');
                        pilihRuteAlternatifDariTabel(0); // Aktifkan rute pertama

                        // Pre-load rute alternatif lainnya secara background
                        for (let i = 1; i < Math.min(semuaRuteAlternatif.length, 5); i++) {
                            setTimeout(() => {
                                gambarRuteAlternatifBaru(i);
                            }, i * 500); // Load bertahap untuk mengurangi beban
                        }
                    } else {
                        // Fallback ke sistem lama jika tidak ada rute alternatif
                        pilihRuteAlternatif(1); // Default ke rute 1 (transit)
                    }
                }, 1500);
            }, 500);
        }

        // ==========================================
        // FUNGSI KOORDINAT DAN RUTE
        // ==========================================
        function dapatkanKoordinatRute(jalurWisata, peta, lokasiAwal, wisataAwal, wisataTujuan) {
            $.ajax({
                url: '{{ route("api.rute-data") }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    jalur: jalurWisata
                },
                success: function(koordinatRute) {
                    gambarRuteDiPeta(peta, lokasiAwal, wisataAwal, wisataTujuan, koordinatRute);
                },
                error: function() {
                    console.error('Gagal mendapatkan data koordinat rute');
                    // Gambar rute langsung jika gagal
                    const ruteLangsung = [
                        [wisataAwal.lat, wisataAwal.lng],
                        [wisataTujuan.lat, wisataTujuan.lng]
                    ];
                    gambarGarisRute(peta, ruteLangsung, '#007bff');
                }
            });
        }

        // ==========================================
        // FUNGSI DESTINASI WISATA
        // ==========================================

        // Fungsi untuk memuat dan menampilkan semua destinasi wisata
        function muatSemuaDestinasi(peta, wisataAwal, wisataTujuan) {
            $.ajax({
                url: '/api/wisata',
                method: 'GET',
                success: function(dataWisata) {
                    console.log('Data semua wisata loaded:', dataWisata);
                    tampilkanSemuaDestinasi(peta, dataWisata, wisataAwal, wisataTujuan);
                },
                error: function(xhr, status, error) {
                    console.error('Error loading semua wisata data:', error);
                }
            });
        }

        // Fungsi untuk menampilkan semua destinasi di peta
        function tampilkanSemuaDestinasi(peta, dataWisata, wisataAwal, wisataTujuan) {
            // Hapus markers lama jika ada
            hapusSemuaMarkersDestinasi(peta);

            dataWisata.forEach(function(wisata) {
                // Skip jika ini adalah wisata awal atau tujuan (sudah ditampilkan)
                if (wisata.id_wisata == wisataAwal.id || wisata.id_wisata == wisataTujuan.id_wisata) {
                    return;
                }

                if (wisata.latitude && wisata.longitude) {
                    // Tentukan warna marker berdasarkan destinasi unggulan
                    const warnaMarker = wisata.destinasi_unggulan == 1 ? '#ffc107' : '#6c757d';
                    const iconMarker = wisata.destinasi_unggulan == 1 ? 'star' : 'map-marker-alt';
                    const ukuranMarker = wisata.destinasi_unggulan == 1 ? 20 : 18;

                    const marker = L.marker([wisata.latitude, wisata.longitude], {
                        icon: L.divIcon({
                            className: 'marker-destinasi-lain',
                            html: `<div style="background-color: ${warnaMarker}; color: white; border-radius: 50%; width: ${ukuranMarker}px; height: ${ukuranMarker}px; display: flex; align-items: center; justify-content: center; font-size: 10px; border: 2px solid white; box-shadow: 0 2px 4px rgba(0,0,0,0.3); opacity: 0.8;"><i class="fas fa-${iconMarker}"></i></div>`,
                            iconSize: [ukuranMarker, ukuranMarker],
                            iconAnchor: [ukuranMarker / 2, ukuranMarker / 2]
                        })
                    }).addTo(peta);

                    // Popup dengan informasi wisata
                    const popupContent = `
                        <div class="wisata-popup-hasil" style="min-width: 180px;">
                            <h6 class="fw-bold mb-2" style="font-size: 14px;">${wisata.nama_wisata}</h6>
                            ${wisata.destinasi_unggulan == 1 ? '<span class="badge bg-warning text-dark mb-2" style="font-size: 10px;">Destinasi Unggulan</span><br>' : ''}
                            <small class="text-muted" style="font-size: 11px;"><i class="fas fa-map-marker-alt"></i> ${wisata.lokasi || 'Lokasi tidak tersedia'}</small><br>
                            <small class="text-muted" style="font-size: 11px;"><i class="fas fa-clock"></i> ${wisata.jam_operasional || 'Jam operasional tidak tersedia'}</small><br>
                            <small class="text-muted" style="font-size: 11px;"><i class="fas fa-ticket-alt"></i> ${wisata.harga_tiket || 'Harga tiket tidak tersedia'}</small>
                            <div class="mt-2">
                                <button class="btn btn-sm btn-outline-primary" style="font-size: 10px; padding: 3px 8px;" onclick="buatRuteKe('${wisata.id_wisata}', '${wisata.nama_wisata}')">
                                    <i class="fas fa-route"></i> Rute ke Sini
                                </button>
                            </div>
                        </div>
                    `;

                    marker.bindPopup(popupContent, {
                        maxWidth: 250,
                        className: 'custom-popup-hasil'
                    });

                    // Simpan marker ke array global
                    markersDestinasiLain.push(marker);
                }
            });

            // Update counter di legend jika ada markers
            if (markersDestinasiLain.length > 0) {
                updateCounterDestinasi();
            }
        }

        // Fungsi untuk menghapus semua markers destinasi
        function hapusSemuaMarkersDestinasi(peta) {
            markersDestinasiLain.forEach(marker => {
                peta.removeLayer(marker);
            });
            markersDestinasiLain = [];
        }

        // Fungsi untuk toggle tampilan semua destinasi
        function toggleSemuaDestinasi(tampilkan) {
            const peta = window.petaGlobal; // Menggunakan referensi peta global
            const toggleLabel = $('label[for="toggleDestinasi"]');

            if (tampilkan) {
                // Muat ulang data destinasi jika belum ada
                if (markersDestinasiLain.length === 0) {
                    // Tampilkan loading pada label
                    toggleLabel.html('<i class="fas fa-spinner fa-spin me-1"></i>Memuat Destinasi...');

                    $.ajax({
                        url: '/api/wisata',
                        method: 'GET',
                        success: function(dataWisata) {
                            const wisataAwal = {
                                id: {{ $hasilRute["wisata_awal"]->id_wisata }}
                            };
                            const wisataTujuan = {
                                id_wisata: {{ $wisataTujuan->id_wisata }}
                            };
                            tampilkanSemuaDestinasi(peta, dataWisata, wisataAwal, wisataTujuan);

                            // Update label setelah selesai
                            toggleLabel.html('<i class="fas fa-eye-slash me-1"></i>Sembunyikan Destinasi Lain');
                            updateCounterDestinasi();
                        },
                        error: function(xhr, status, error) {
                            console.error('Error loading wisata data:', error);
                            toggleLabel.html('<i class="fas fa-exclamation-triangle me-1"></i>Gagal Memuat');

                            // Reset toggle jika error
                            setTimeout(() => {
                                $('#toggleDestinasi').prop('checked', false);
                                toggleLabel.html(
                                    '<i class="fas fa-map-marker-alt me-1"></i>Tampilkan Semua Destinasi'
                                );
                                updateCounterDestinasi();
                            }, 2000);
                        }
                    });
                } else {
                    // Tampilkan markers yang sudah ada
                    markersDestinasiLain.forEach(marker => {
                        marker.addTo(peta);
                    });

                    // Update label toggle
                    toggleLabel.html('<i class="fas fa-eye-slash me-1"></i>Sembunyikan Destinasi Lain');
                    updateCounterDestinasi();
                }
            } else {
                // Sembunyikan semua markers destinasi
                markersDestinasiLain.forEach(marker => {
                    peta.removeLayer(marker);
                });

                // Update label toggle
                toggleLabel.html('<i class="fas fa-map-marker-alt me-1"></i>Tampilkan Semua Destinasi');
                updateCounterDestinasi();
            }
        }

        // ==========================================
        // FUNGSI GAMBAR RUTE DI PETA
        // ==========================================
        function gambarRuteDiPeta(peta, lokasiAwal, wisataAwal, wisataTujuan, koordinatRute) {
            // ==========================================
            // RUTE 1: Dengan Transit (Rute Original)
            // ==========================================
            console.log('Membuat Rute 1 (Transit)...');

            // Garis dari lokasi awal ke wisata terdekat
            dapatkanDanGambarRuteJalan(peta, lokasiAwal, wisataAwal, '#28a745', 'Jalur ke titik awal', function(garis,
                info) {
                // Callback untuk rute 1 - hanya untuk keperluan peta
                console.log('Garis dari lokasi awal ke wisata terdekat sudah dibuat (untuk peta saja)');
            });

            // Garis rute utama antar wisata untuk rute 1
            if (koordinatRute.length > 1) {
                gambarRuteAntarWisata(peta, koordinatRute, 1); // Parameter 1 untuk rute transit
            } else if (koordinatRute.length === 1) {
                dapatkanDanGambarRuteJalan(peta, wisataAwal, wisataTujuan, '#007bff', 'Jalur utama rute 1', function(garis,
                    info) {
                    garisRute1 = garis;
                    if (info) {
                        console.log('Raw data dari API untuk Rute 1 (hanya untuk peta):', info);

                        // Untuk rute 1, kita tetap gunakan data dari database
                        // Data API hanya untuk keperluan gambar peta
                        console.log('Rute 1 menggunakan data dari database, bukan dari API routing');
                    }
                    // Sembunyikan rute 1 jika bukan rute aktif
                    if (ruteAktif !== 1) {
                        peta.removeLayer(garis);
                    }
                });
            }

            // ==========================================
            // RUTE 2: Langsung (Tanpa Transit)
            // ==========================================
            console.log('Membuat Rute 2 (Langsung)...');

            // Rute langsung dari lokasi awal ke tujuan akhir
            dapatkanDanGambarRuteJalan(peta, lokasiAwal, wisataTujuan, '#28a745', 'Jalur langsung rute 2', function(garis,
                info) {
                garisRute2 = garis;
                if (info) {
                    console.log('Raw data dari API untuk Rute 2:', info);

                    // Simpan data rute 2 untuk tampilan alternatif (opsional)
                    // Hanya untuk keperluan peta, bukan untuk mengganti data asli
                    let jarakLangsung = 0;
                    let waktuLangsung = 0;

                    if (info.jarak) {
                        if (typeof info.jarak === 'string' && info.jarak.includes('km')) {
                            jarakLangsung = parseFloat(info.jarak.replace(' km', '').replace(',', '.')) || 0;
                        } else if (typeof info.jarak === 'number') {
                            jarakLangsung = info.jarak;
                        }
                    }

                    if (info.durasi) {
                        // Simpan format asli dari API, jangan konversi ke menit
                        if (typeof info.durasi === 'string') {
                            // Gunakan format asli dari API
                            waktuLangsung = info.durasi;
                        } else if (typeof info.durasi === 'number') {
                            // Jika API memberikan angka (dalam menit), konversi ke format string
                            if (info.durasi >= 60) {
                                const jam = Math.floor(info.durasi / 60);
                                const sisaMenit = Math.round(info.durasi % 60);
                                waktuLangsung = sisaMenit > 0 ? `${jam} jam ${sisaMenit} menit` : `${jam} jam`;
                            } else {
                                waktuLangsung = `${Math.round(info.durasi)} menit`;
                            }
                        }
                    }


                    // Simpan untuk keperluan display rute langsung
                    infoRute2 = {
                        jarak: jarakLangsung,
                        waktu: waktuLangsung
                    };

                    console.log('infoRute2 final (hanya untuk rute langsung):', infoRute2);
                }
                // Sembunyikan rute 2 secara default, tampilkan hanya jika aktif
                if (ruteAktif !== 2) {
                    peta.removeLayer(garis);
                }
                console.log('Rute 2 (Langsung) selesai dibuat');
            });

            // ==========================================
            // RUTE 3: Via Pematang Siantar
            // ==========================================
            console.log('Membuat Rute 3 (Via Pematang Siantar)...');

            // Koordinat Pematang Siantar
            const pematangSiantar = {
                lat: 2.9676002181287195,
                lng: 99.06843670021658,
                nama: 'Pematang Siantar'
            };

            // Cari rute yang via Pematang Siantar dari data rute alternatif
            const ruteViaPematang = semuaRuteAlternatif.find(rute => rute.via_pematang_siantar === true);

            if (ruteViaPematang) {
                console.log('Data rute via Pematang Siantar ditemukan:', ruteViaPematang);

                // Tampilkan tombol rute 3 dan legend
                $('#btnRute3').show();
                $('#legendRute3').show();

                // Gambar rute dari lokasi awal ke Pematang Siantar
                console.log('🟡 SEGMEN 1: Lokasi Awal → Pematang Siantar');
                console.log('   Dari:', lokasiAwal);
                console.log('   Ke:', pematangSiantar);

                dapatkanDanGambarRuteJalan(peta, lokasiAwal, pematangSiantar, '#ffc107', 'Jalur ke Pematang Siantar',
                    function(garis1, info1) {
                        console.log('✅ SEGMEN 1 SELESAI:', info1);

                        // Gambar rute dari Pematang Siantar ke tujuan
                        console.log('🟡 SEGMEN 2: Pematang Siantar → Tujuan');
                        console.log('   Dari:', pematangSiantar);
                        console.log('   Ke:', wisataTujuan);

                        dapatkanDanGambarRuteJalan(peta, pematangSiantar, wisataTujuan, '#ffc107',
                            'Jalur dari Pematang Siantar ke tujuan',
                            function(garis2, info2) {
                                console.log('✅ SEGMEN 2 SELESAI:', info2);
                                // Buat layer group untuk rute 3
                                garisRute3 = L.layerGroup([garis1, garis2]);

                                // Tambahkan marker untuk Pematang Siantar
                                const markerPematang = L.marker([pematangSiantar.lat, pematangSiantar.lng], {
                                    icon: L.divIcon({
                                        className: 'marker-transit',
                                        html: '<div style="background-color: #ffc107; color: black; border-radius: 50%; width: 20px; height: 20px; display: flex; align-items: center; justify-content: center; font-size: 12px; border: 2px solid white; box-shadow: 0 2px 4px rgba(0,0,0,0.3);"><i class="bi bi-geo-fill"></i></div>',
                                        iconSize: [20, 20],
                                        iconAnchor: [10, 10]
                                    })
                                }).addTo(peta).bindPopup('<b>Transit: Pematang Siantar</b>');

                                // Tambahkan marker ke layer group
                                garisRute3.addLayer(markerPematang);

                                // Hitung total jarak dan waktu
                                let totalJarak = 0;
                                let totalWaktu = 0;

                                if (info1 && info1.jarak) {
                                    totalJarak += typeof info1.jarak === 'number' ? info1.jarak : parseFloat(info1
                                        .jarak);
                                }
                                if (info2 && info2.jarak) {
                                    totalJarak += typeof info2.jarak === 'number' ? info2.jarak : parseFloat(info2
                                        .jarak);
                                }

                                if (info1 && info1.durasi) {
                                    totalWaktu += typeof info1.durasi === 'number' ? info1.durasi : parseFloat(info1
                                        .durasi);
                                }
                                if (info2 && info2.durasi) {
                                    totalWaktu += typeof info2.durasi === 'number' ? info2.durasi : parseFloat(info2
                                        .durasi);
                                }

                                // Format waktu
                                let waktuFormatted;
                                if (totalWaktu >= 60) {
                                    const jam = Math.floor(totalWaktu / 60);
                                    const sisaMenit = Math.round(totalWaktu % 60);
                                    waktuFormatted = sisaMenit > 0 ? `${jam} jam ${sisaMenit} menit` : `${jam} jam`;
                                } else {
                                    waktuFormatted = `${Math.round(totalWaktu)} menit`;
                                }

                                // Simpan info untuk rute 3
                                infoRute3 = {
                                    jarak: totalJarak,
                                    waktu: waktuFormatted
                                };

                                console.log('infoRute3 final:', infoRute3);
                                console.log('Rute 3 (Via Pematang Siantar) selesai dibuat');

                                // Sembunyikan rute 3 secara default
                                if (ruteAktif !== 3) {
                                    peta.removeLayer(garisRute3);
                                }
                            }
                        );
                    }
                );
            } else {
                console.log('Tidak ada rute via Pematang Siantar dalam data');
                $('#btnRute3').hide();
            }

            // Sesuaikan viewport untuk menampilkan seluruh rute
            sesuaikanViewportPeta(peta, lokasiAwal, wisataAwal, wisataTujuan, koordinatRute);
        }

        function gambarRuteAntarWisata(peta, koordinatRute, nomorRute = 1) {
            console.log(`Membuat rute antar wisata untuk Rute ${nomorRute}...`);

            let garisRute = [];

            // Gambar rute antar titik wisata secara berurutan
            for (let i = 0; i < koordinatRute.length - 1; i++) {
                const asal = koordinatRute[i];
                const tujuan = koordinatRute[i + 1];

                dapatkanDanGambarRuteJalan(peta, asal, tujuan, '#007bff', `Jalur ${i + 1} rute ${nomorRute}`, function(
                    garis, info) {
                    garisRute.push(garis);

                    // Jika ini adalah garis terakhir, buat layer group
                    if (i === koordinatRute.length - 2) {
                        if (nomorRute === 1) {
                            // Buat layer group untuk rute 1
                            garisRute1 = L.layerGroup(garisRute);
                            console.log('Rute 1 (Transit) layer group selesai dibuat');
                        }
                    }
                });

                // Tambahkan marker untuk titik transit (kecuali titik awal dan akhir)
                if (i > 0 && nomorRute === 1) { // Hanya tampilkan transit untuk rute 1
                    L.marker([asal.lat, asal.lng], {
                        icon: L.divIcon({
                            className: 'marker-transit',
                            html: '<div style="background-color: #ffc107; color: black; border-radius: 50%; width: 15px; height: 15px; display: flex; align-items: center; justify-content: center; font-size: 10px; border: 2px solid white; box-shadow: 0 1px 2px rgba(0,0,0,0.3);"><i class="bi bi-circle-fill"></i></div>',
                            iconSize: [15, 15],
                            iconAnchor: [7, 7]
                        })
                    }).addTo(peta).bindPopup('<b>Transit: ' + asal.nama + '</b>');
                }
            }
        }

        function dapatkanDanGambarRuteJalan(peta, koordinatAsal, koordinatTujuan, warna, keterangan, callback = null,
            retryCount = 0) {
            // Log request untuk debugging
            console.log('🔄 Request rute jalan (attempt ' + (retryCount + 1) + '):', {
                dari: koordinatAsal,
                ke: koordinatTujuan,
                warna: warna,
                keterangan: keterangan
            });

            // Hitung jarak estimasi untuk menentukan timeout
            const jarakEstimasi = hitungJarakHaversine(
                koordinatAsal.lat, koordinatAsal.lng,
                koordinatTujuan.lat, koordinatTujuan.lng
            );

            // Timeout dinamis: 15 detik untuk jarak < 50km, 20 detik untuk >= 50km
            const timeoutDuration = jarakEstimasi < 50 ? 15000 : 20000;
            console.log(`   Jarak estimasi: ${jarakEstimasi.toFixed(2)} km, Timeout: ${timeoutDuration/1000}s`);

            // Panggil API untuk mendapatkan rute jalan sebenarnya
            $.ajax({
                url: '{{ route("api.rute-jalan") }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    koordinat_awal: koordinatAsal,
                    koordinat_tujuan: koordinatTujuan
                },
                timeout: timeoutDuration,
                success: function(response) {
                    console.log('✅ Response rute jalan berhasil:', response);

                    if (response.success && response.koordinat_rute && response.koordinat_rute.length > 0) {
                        console.log(
                            `✅ Menggambar rute mengikuti jalan (${response.koordinat_rute.length} titik)`);

                        // Gambar garis mengikuti jalan sebenarnya
                        const garis = L.polyline(response.koordinat_rute, {
                            color: warna,
                            weight: 4,
                            opacity: 0.8,
                            smoothFactor: 1
                        }).addTo(peta);

                        // Jika backend mengirim fallback, tampilkan warning
                        if (response.fallback === true) {
                            tampilkanToast(
                                '<i class="fas fa-info-circle me-2"></i>' +
                                '<strong>Info Rute</strong><br>' +
                                'Rute ditampilkan sebagai perkiraan (garis lurus).',
                                'warning'
                            );
                        }

                        // Tambahkan tooltip dengan informasi jarak dan durasi
                        let tooltipText = keterangan;
                        if (response.jarak) {
                            tooltipText += `<br>Jarak: ${response.jarak} km`;
                        }
                        if (response.durasi) {
                            tooltipText += `<br>Waktu: ${response.durasi} menit`;
                        }
                        if (response.fallback) {
                            tooltipText += '<br><small>(Rute perkiraan)</small>';
                        }

                        garis.bindTooltip(tooltipText, {
                            permanent: false,
                            sticky: true
                        });

                        // Panggil callback jika ada
                        if (callback) {
                            callback(garis, {
                                jarak: response.jarak,
                                durasi: response.durasi,
                                fallback: response.fallback
                            });
                        }
                    } else {
                        // Response tidak valid, gunakan garis lurus
                        console.warn('⚠️ Response tidak valid, menggunakan garis lurus:', response);
                        gambarGarisLurusFallback(peta, koordinatAsal, koordinatTujuan, warna, keterangan,
                            callback);
                    }
                },
                error: function(xhr, status, error) {
                    // Log error detail
                    console.error('❌ AJAX Error (attempt ' + (retryCount + 1) + '):', {
                        status: status,
                        error: error,
                        response: xhr.responseText,
                        statusCode: xhr.status
                    });

                    // Retry logic: coba lagi jika timeout atau network error (max 2 kali retry)
                    if ((status === 'timeout' || xhr.status === 0) && retryCount < 2) {
                        console.warn('⚠️ Mencoba ulang request (' + (retryCount + 2) + '/3)...');

                        // Tunggu 1 detik sebelum retry
                        setTimeout(function() {
                            dapatkanDanGambarRuteJalan(peta, koordinatAsal, koordinatTujuan, warna,
                                keterangan, callback, retryCount + 1);
                        }, 1000);
                        return;
                    }

                    // Tampilkan notifikasi ke user
                    if (xhr.status === 0) {
                        console.error(
                            '❌ Network Error: Tidak bisa terhubung ke server. Periksa koneksi internet.');
                    } else if (xhr.status === 500) {
                        console.error('❌ Server Error: API routing mengalami masalah.');
                    } else if (status === 'timeout') {
                        console.error('❌ Timeout: Request memakan waktu terlalu lama setelah ' + (retryCount +
                            1) + ' percobaan.');
                    }

                    // Fallback: gambar garis lurus
                    console.warn('⚠️ Menggunakan garis lurus sebagai fallback');
                    gambarGarisLurusFallback(peta, koordinatAsal, koordinatTujuan, warna, keterangan, callback);
                }
            });
        }

        // Fungsi helper untuk menggambar garis lurus sebagai fallback
        function gambarGarisLurusFallback(peta, koordinatAsal, koordinatTujuan, warna, keterangan, callback) {
            console.log('📏 Menggambar garis lurus fallback');

            // Tampilkan peringatan ke user
            tampilkanToast(
                '<i class="fas fa-exclamation-triangle me-2"></i>' +
                '<strong>Rute Tidak Tersedia</strong><br>' +
                'Menampilkan garis lurus estimasi. Rute jalan sebenarnya sedang tidak tersedia.',
                'warning'
            );

            const ruteLangsung = [
                [koordinatAsal.lat, koordinatAsal.lng],
                [koordinatTujuan.lat, koordinatTujuan.lng]
            ];

            // Gambar dengan dash pattern untuk menunjukkan ini bukan rute sebenarnya
            const garis = L.polyline(ruteLangsung, {
                color: warna,
                weight: 4,
                opacity: 0.6,
                smoothFactor: 1,
                dashArray: '10, 10' // Garis putus-putus
            }).addTo(peta);

            // Tooltip warning
            garis.bindTooltip(keterangan +
                '<br><small class="text-danger">⚠️ Garis lurus (estimasi)<br>Rute jalan sebenarnya tidak tersedia</small>', {
                    permanent: false,
                    sticky: true
                });

            // Hitung jarak lurus menggunakan Haversine
            const jarakLurus = hitungJarakHaversine(
                koordinatAsal.lat, koordinatAsal.lng,
                koordinatTujuan.lat, koordinatTujuan.lng
            );

            // Panggil callback dengan estimasi
            if (callback) {
                callback(garis, {
                    jarak: jarakLurus,
                    durasi: Math.round(jarakLurus / 40 * 60), // Estimasi 40 km/jam
                    fallback: true
                });
            }
        }

        // Fungsi untuk menghitung jarak Haversine
        function hitungJarakHaversine(lat1, lon1, lat2, lon2) {
            const R = 6371; // Radius bumi dalam km
            const dLat = (lat2 - lat1) * Math.PI / 180;
            const dLon = (lon2 - lon1) * Math.PI / 180;
            const a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
                Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                Math.sin(dLon / 2) * Math.sin(dLon / 2);
            const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
            return R * c;
        }

        function sesuaikanViewportPeta(peta, lokasiAwal, wisataAwal, wisataTujuan, koordinatRute) {
            const semuaTitik = [
                [lokasiAwal.lat, lokasiAwal.lng],
                [wisataAwal.lat, wisataAwal.lng],
                [wisataTujuan.lat, wisataTujuan.lng]
            ];

            koordinatRute.forEach(koordinat => {
                semuaTitik.push([koordinat.lat, koordinat.lng]);
            });

            const grup = new L.featureGroup(semuaTitik.map(titik => L.marker(titik)));
            peta.fitBounds(grup.getBounds().pad(0.1));
        }

        function gambarGarisRute(peta, koordinat, warna, tooltip) {
            const garis = L.polyline(koordinat, {
                color: warna,
                weight: 4,
                opacity: 0.8,
                smoothFactor: 1
            }).addTo(peta);

            if (tooltip) {
                garis.bindTooltip(tooltip);
            }

            return garis;
        }

        // ==========================================
        // FUNGSI GLOBAL DAN CALLBACK
        // ==========================================

        // Fungsi global untuk membuat rute ke destinasi lain
        function buatRuteKe(idWisata, namaWisata) {
            // Konfirmasi pengguna
            if (confirm(`Apakah Anda ingin membuat rute baru ke ${namaWisata}?`)) {
                // Redirect ke halaman cari rute
                const baseUrl = '{{ route("pengunjung.cari-rute") }}';
                window.location.href = `${baseUrl}?tujuan=${idWisata}`;
            }
        }
    </script>

    <style>
        /* Info Rute Section */
        .info-rute {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            border-left: 4px solid #007bff;
        }

        /* Leaflet Container */
        .leaflet-container {
            font-family: inherit;
        }

        /* Marker Styling */
        .marker-lokasi-awal,
        .marker-wisata-awal,
        .marker-wisata-tujuan,
        .marker-transit,
        .marker-destinasi-lain {
            border: none !important;
            background: transparent !important;
        }

        .marker-destinasi-lain:hover {
            transform: scale(1.1);
            transition: transform 0.2s ease;
        }

        .marker-destinasi-lain {
            animation: markerFadeIn 0.5s ease-in-out;
        }

        @keyframes markerFadeIn {
            from {
                opacity: 0;
                transform: scale(0.5);
            }

            to {
                opacity: 0.8;
                transform: scale(1);
            }
        }

        /* Peta Container */
        .peta-container {
            position: relative;
        }

        .peta-loading {
            background-color: #f8f9fa;
            border: 2px dashed #dee2e6;
            border-radius: 8px;
            color: #6c757d;
        }

        /* Legend Styling */
        .legend-peta {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 6px;
            border: 1px solid #dee2e6;
        }

        .legend-item {
            display: flex;
            align-items: center;
            margin-bottom: 8px;
        }

        .legend-marker {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            margin-right: 8px;
            border: 1px solid white;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.3);
        }

        .legend-line {
            width: 20px;
            height: 3px;
            margin-right: 8px;
            border-radius: 1px;
        }

        /* Toggle Switch Styling */
        .toggle-header {
            transition: all 0.3s ease;
        }

        .toggle-header:hover {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 8px;
        }

        .form-check.form-switch {
            padding-left: 2.5em;
            min-height: 1.5rem;
        }

        .form-check.form-switch .form-check-input {
            width: 2em;
            margin-left: -2.5em;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='-4 -4 8 8'%3e%3ccircle r='3' fill='rgba%28255,255,255,1%29'/%3e%3c/svg%3e");
            background-position: left center;
            border-radius: 2em;
            transition: background-position .15s ease-in-out;
        }

        .form-check-input:checked {
            background-color: #28a745;
            border-color: #28a745;
        }

        .form-check-input:focus {
            border-color: #86b7fe;
            outline: 0;
            box-shadow: 0 0 0 0.25rem rgba(40, 167, 69, 0.25);
        }

        .form-check-label {
            font-weight: 500;
            color: #495057;
            cursor: pointer;
            user-select: none;
        }

        .form-check-label:hover {
            color: #28a745;
            transition: color 0.2s ease;
        }

        /* Leaflet Tooltip Styling */
        .leaflet-tooltip {
            background-color: rgba(0, 0, 0, 0.8) !important;
            color: white !important;
            border: none !important;
            border-radius: 4px !important;
            padding: 6px 10px !important;
            font-size: 12px !important;
        }

        .leaflet-tooltip-left:before {
            border-left-color: rgba(0, 0, 0, 0.8) !important;
        }

        .leaflet-tooltip-right:before {
            border-right-color: rgba(0, 0, 0, 0.8) !important;
        }

        /* Popup Styling */
        .custom-popup-hasil .leaflet-popup-content-wrapper {
            border-radius: 6px;
            box-shadow: 0 3px 6px rgba(0, 0, 0, 0.15);
        }

        .custom-popup-hasil .leaflet-popup-content {
            margin: 10px;
        }

        .wisata-popup-hasil .btn {
            transition: all 0.2s ease;
        }

        .wisata-popup-hasil .btn:hover {
            transform: scale(1.05);
        }

        /* Rute Alternatif Button Styling */
        .kontrol-rute-alternatif {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 10px;
            border: 1px solid #dee2e6;
        }

        .btn-rute-alternatif {
            transition: all 0.3s ease;
            font-weight: 500;
            border-radius: 6px;
        }

        .btn-rute-alternatif:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .btn-rute-alternatif.active {
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }

        .btn-rute-alternatif i {
            margin-right: 5px;
        }

        /* Info Rute Aktif Styling */
        #infoRuteAktif {
            font-weight: 500;
            padding: 5px 10px;
            border-radius: 4px;
            background-color: rgba(0, 123, 255, 0.1);
            border: 1px solid rgba(0, 123, 255, 0.2);
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .info-rute .row {
                font-size: 14px;
            }

            .legend-peta .row {
                flex-direction: column;
            }

            #peta {
                height: 300px !important;
            }

            .kontrol-rute-alternatif {
                padding: 8px;
            }

            .btn-rute-alternatif {
                font-size: 12px;
                padding: 6px 12px;
            }
        }

        /* ==========================================
                                   RUTE ALTERNATIF TABLE STYLING
                                   ========================================== */
        /* Gradient Header untuk Card Rute Alternatif */
        .bg-gradient-primary {
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%) !important;
        }

        /* Tabel Rute Alternatif */
        #tabelRuteAlternatif {
            border-collapse: separate;
            border-spacing: 0;
        }

        #tabelRuteAlternatif thead th {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-bottom: 2px solid #dee2e6;
            font-weight: 600;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 12px 8px;
            vertical-align: middle;
        }

        #tabelRuteAlternatif tbody tr {
            transition: all 0.3s ease;
            cursor: pointer;
        }

        #tabelRuteAlternatif tbody tr:hover {
            background-color: #f8f9fa !important;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        #tabelRuteAlternatif tbody tr.table-primary {
            background-color: rgba(0, 123, 255, 0.1) !important;
            border-left: 4px solid #007bff;
        }

        #tabelRuteAlternatif tbody td {
            padding: 12px 8px;
            vertical-align: middle;
            border-bottom: 1px solid #e9ecef;
            font-size: 13px;
        }

        /* Badge Nomor Rute */
        .rute-badge {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 14px;
            margin: 0 auto;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }

        /* Transit List Styling */
        .transit-list {
            max-height: 60px;
            overflow-y: auto;
        }

        /* Destinasi List Styling */
        .destinasi-list {
            max-height: 80px;
            overflow-y: auto;
            line-height: 1.4;
        }

        .destinasi-list .badge {
            font-size: 10px;
            padding: 4px 6px;
            margin: 1px;
            white-space: nowrap;
            display: inline-flex;
            align-items: center;
            gap: 2px;
        }

        .destinasi-list .badge i {
            font-size: 8px;
        }

        .destinasi-list .fa-arrow-right {
            opacity: 0.6;
            margin: 0 2px;
        }

        /* Responsif untuk destinasi list */
        @media (max-width: 768px) {
            .destinasi-list {
                max-height: 100px;
            }

            .destinasi-list .badge {
                font-size: 9px;
                padding: 3px 5px;
                margin-bottom: 2px;
                display: block;
                width: 100%;
                text-align: left;
            }

            .destinasi-list .fa-arrow-right {
                display: none;
            }
        }

        /* Button Pilih Rute */
        .pilih-rute-btn {
            transition: all 0.3s ease;
            border-radius: 20px;
            padding: 5px 12px;
        }

        .pilih-rute-btn:hover {
            transform: scale(1.1);
            box-shadow: 0 2px 8px rgba(0, 123, 255, 0.3);
        }

        .pilih-rute-btn.btn-primary {
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
            border: none;
        }

        /* Animation untuk Row Selection */
        @keyframes selectedRowPulse {
            0% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.02);
            }

            100% {
                transform: scale(1);
            }
        }

        .rute-row.table-primary {
            animation: selectedRowPulse 0.5s ease-in-out;
        }

        /* Responsif untuk Tabel Rute Alternatif */
        @media (max-width: 992px) {
            #tabelRuteAlternatif {
                font-size: 12px;
            }

            .rute-badge {
                width: 25px;
                height: 25px;
                font-size: 12px;
            }
        }
    </style>
@endpush
