<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Perjanjian Kinerja Tahun {{ $tahun->tahun }}</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('images/logo/logopendis-1.png') }}">
    <style>
        @page {
            size: A4 portrait;
            margin: 1.5cm 2cm 2cm 2cm;
        }

        * {
            box-sizing: border-box;
        }

        body {
            color: #000;
            /* font-family: 'Times New Roman', Times, serif; */
            /* arial */
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11pt;
            line-height: 1.4;
            margin: 0;
            padding: 0;
            width: 100%;
        }

        .page-break {
            page-break-after: always;
            clear: both;
        }

        .center { text-align: center; }
        .justify { text-align: justify; }
        .bold { font-weight: bold; }
        .uppercase { text-transform: uppercase; }

        /* Header Logo */
        .logo {
            display: block;
            height: 90px;
            width: auto;
            margin: 0 auto 2px auto;
        }
        .logo-fallback {
            border: 1px solid #000;
            display: inline-block;
            font-size: 8pt;
            padding: 8px 12px;
            margin: 0 auto 2px auto;
        }

        h1 { font-size: 13pt; margin: 0 0 2px 0; }
        h2 { font-size: 11pt; margin: 0 0 2px 0; }
        h3 { margin: 0; }
        p { margin: 0 0 6px 0; }

        .document-heading { margin-bottom: 16px; }
        .clause { margin-bottom: 8px; text-align: justify; text-indent: 0; }

        /* Tabel Data Pihak */
        .party-table {
            border-collapse: collapse;
            width: 100%;
            margin-bottom: 12px;
        }
        .party-table td {
            border: 0;
            padding: 2px 0;
            vertical-align: top;
        }

        /* Tabel Tanda Tangan */
        .signature-table {
            border-collapse: collapse;
            width: 100%;
            margin-top: 20px;
        }
        .signature-table td {
            border: 0;
            text-align: center;
            vertical-align: top;
            width: 50%;
        }
        .signature-space { height: 50px; }
        .signature-name {
            display: inline-block;
            font-weight: bold;
            border-bottom: 1px solid #000;
            padding-bottom: 1px;
        }
        .stamp-box {
            border: 1px dashed #000;
            display: inline-block;
            font-size: 7.5pt;
            margin: 2px 0;
            padding: 4px 8px;
        }

        /* Tabel Resmi (Lampiran & Anggaran) */
        .attachment-header {
            border-bottom: 2px solid #000;
            margin-bottom: 10px;
            padding-bottom: 4px;
        }

        .official-table {
            border: 1px solid #000;
            border-collapse: collapse;
            font-size: 9.5pt;
            width: 100%;
            margin-top: 6px;
        }
        .official-table th,
        .official-table td {
            border: 1px solid #000;
            padding: 5px 6px;
            vertical-align: top;
        }
        .official-table th {
            background-color: #f1f5f9;
            font-weight: bold;
            text-align: center;
        }

        .budget-table .program-row td { background-color: #f8fafc; font-weight: bold; }
        .budget-table .subactivity .description { padding-left: 15px; }
        .budget-table .total-row td {
            border-top: 2px solid #000;
            font-weight: bold;
            background-color: #f1f5f9;
        }

        .note { font-size: 8pt; font-style: italic; margin-top: 6px; }
    </style>
</head>
<body>

    {{-- HALAMAN 1 --}}
    <div class="document-heading center">
        @if (!empty($logoDataUri))
            <img class="logo" src="{{ $logoDataUri }}" alt="Logo Kementerian Agama">
        @else
            <div class="logo-fallback">KEMENTERIAN AGAMA REPUBLIK INDONESIA</div>
        @endif
        <h3 class="uppercase">PERJANJIAN KINERJA TAHUN {{ $tahun->tahun }}</h3>
    </div>

    <p class="clause">
        Dalam rangka mewujudkan manajemen pemerintahan yang efektif, transparan, dan akuntabel serta berorientasi pada hasil, kami yang bertanda tangan di bawah ini menyatakan telah berkomitmen untuk mencapai target kinerja sebagaimana ditetapkan dalam dokumen Perjanjian Kinerja ini.
    </p>

    <table class="party-table">
        <colgroup>
            <col style="width: 22%;">
            <col style="width: 3%;">
            <col style="width: 75%;">
        </colgroup>
        <tr>
            <td>Nama</td>
            <td>:</td>
            <td class="bold">Abdurachman Assagaf</td>
        </tr>
        <tr>
            <td>Jabatan</td>
            <td>:</td>
            <td>Kepala Kantor Kementerian Agama Kab. Pulau Morotai</td>
        </tr>
        <tr>
            <td colspan="3">Selanjutnya disebut <span class="bold">Pihak Pertama.</span></td>
        </tr>
        <tr><td colspan="3" style="height: 6px;"></td></tr>
        <tr>
            <td>Nama</td>
            <td>:</td>
            <td class="bold">Amar Manaf</td>
        </tr>
        <tr>
            <td>Jabatan</td>
            <td>:</td>
            <td>Kepala Kantor Wilayah Kementerian Agama Provinsi</td>
        </tr>
        <tr>
            <td colspan="3">Selanjutnya disebut <span class="bold">Pihak Kedua.</span></td>
        </tr>
    </table>

    <p class="clause">
        <span class="bold">Pihak Pertama.</span> berjanji akan mewujudkan target kinerja yang seharusnya dicapai sesuai dengan lampiran perjanjian ini, dalam rangka mencapai target kinerja jangka menengah seperti yang telah ditetapkan dalam dokumen perencanaan.
    </p>
    <p class="clause">
        <span class="bold">Pihak Kedua.</span> akan melakukan supervisi dan memberikan dukungan yang diperlukan untuk pencapaian target kinerja tersebut, termasuk melakukan pembinaan dan koordinasi sesuai kewenangan.
    </p>
    <p class="clause">
        Keberhasilan dan kegagalan pencapaian target kinerja tersebut menjadi tanggung jawab <span class="bold">Pihak Pertama.</span> dan akan dievaluasi secara berkala berdasarkan bukti dukung yang sah dan dapat dipertanggungjawabkan.
    </p>

    <table class="signature-table">
        <tr>
            <td></td>
            <td>{{ $kota }}, {{ $tanggalCetak }}</td>
        </tr>
        <tr>
            <td>
                <p class="bold">Pihak Pertama</p>
                <div class="signature-space"></div>
                <p class="signature-name">Abdurachman Assagaf</p><br>
                <span style="font-size: 9.5pt;">NIP. {{ $pihakPertama?->nip ?? '................................' }}</span>
            </td>
            <td>
                <p class="bold">Pihak Kedua</p>
                <div class="signature-space"></div>
                <p class="signature-name">Amar Manaf</p><br>
                <span style="font-size: 9.5pt;">NIP. {{ $pihakKedua?->nip ?? '................................' }}</span>
            </td>
        </tr>
    </table>

    {{-- HALAMAN 2 --}}
    <div class="page-break"></div>

    <div class="attachment-header center">
        <h2 class="bold">LAMPIRAN PERJANJIAN KINERJA</h2>
        <p class="uppercase bold">PERJANJIAN KINERJA TAHUN {{ $tahun->tahun }}</p>
    </div>

    <p style="margin-bottom: 6px;"><strong>Unit Kerja:</strong> {{ $pihakPertama?->sekolah?->nama_sekolah ?? 'Kementerian Agama' }}</p>

    <table class="official-table">
        <colgroup>
            <col style="width: 45%;">
            <col style="width: 40%;">
            <col style="width: 15%;">
        </colgroup>
        <thead>
            <tr>
                <th>No. &amp; Sasaran Kegiatan</th>
                <th>Indikator Kinerja</th>
                <th>Target</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($tahun->sasarans as $sasaran)
                @php $indicatorCount = max($sasaran->indikators->count(), 1); @endphp
                @forelse ($sasaran->indikators as $index => $indikator)
                    <tr>
                        @if ($index === 0)
                            <td rowspan="{{ $indicatorCount }}">
                                <span class="bold">{{ $sasaran->no_urut }}. {{ $sasaran->sasaran_kegiatan }}</span>
                            </td>
                        @endif
                        <td>{{ chr(97 + $index) }}. {{ $indikator->indikator_kinerja }}</td>
                        <td class="center">{{ $indikator->target_default }}{{ $indikator->satuan ? ' ' . $indikator->satuan : '' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td><span class="bold">{{ $sasaran->no_urut }}. {{ $sasaran->sasaran_kegiatan }}</span></td>
                        <td>-</td>
                        <td class="center">-</td>
                    </tr>
                @endforelse
            @empty
                <tr><td colspan="3" class="center">Belum ada sasaran dan indikator.</td></tr>
            @endforelse
        </tbody>
    </table>
    <p class="note">Catatan: Target kinerja mengikuti master indikator yang telah disetujui untuk Tahun Anggaran {{ $tahun->tahun }}.</p>

    {{-- HALAMAN 3 --}}
    <div class="page-break"></div>

    <div class="attachment-header center">
        <h2 class="bold">RINCIAN PAGU ANGGARAN</h2>
        <p class="uppercase bold">PERJANJIAN KINERJA TAHUN {{ $tahun->tahun }}</p>
    </div>

    <table class="official-table budget-table">
        <colgroup>
            <col style="width: 18%;">
            <col style="width: 54%;">
            <col style="width: 28%;">
        </colgroup>
        <thead>
            <tr>
                <th>Kode</th>
                <th>Program / Kegiatan</th>
                <th>Anggaran</th>
            </tr>
        </thead>
        <tbody>
            @php $grandTotal = 0; @endphp
            @forelse ($tahun->masterPrograms as $programIndex => $program)
                <tr class="program-row">
                    <td>{{ $program->kode_program }}</td>
                    <td>{{ $programIndex + 1 }}. {{ $program->nama_program }}</td>
                    <td style="text-align: right;">Rp {{ number_format($program->kegiatans->sum('anggaran'), 0, ',', '.') }}</td>
                </tr>
                @foreach ($program->kegiatans as $kegiatanIndex => $kegiatan)
                    @php $grandTotal += (float) $kegiatan->anggaran; @endphp
                    <tr class="subactivity">
                        <td>{{ $kegiatan->kode_kegiatan }}</td>
                        <td class="description">{{ chr(97 + $kegiatanIndex) }}. {{ $kegiatan->nama_kegiatan }}</td>
                        <td style="text-align: right;">Rp {{ number_format($kegiatan->anggaran, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            @empty
                <tr><td colspan="3" class="center">Belum ada rincian pagu anggaran.</td></tr>
            @endforelse
            <tr class="total-row">
                <td colspan="2" class="center">Jumlah Keseluruhan</td>
                <td style="text-align: right;">Rp {{ number_format($grandTotal, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <!-- Tanda Tangan Halaman 3 Diselaraskan (PIHAK PERTAMA di Kiri, PIHAK KEDUA di Kanan sesuai PDF) -->
    <table class="signature-table">
        <tr>
            <td></td>
            <td>{{ $kota }}, {{ $tanggalCetak }}</td>
        </tr>
        <tr>
            <td>
                <p class="bold">Pihak Pertama</p>
                <div class="signature-space"></div>
                <p class="signature-name">Abdurachman Assagaf</p><br>
                <span style="font-size: 9.5pt;">NIP. {{ $pihakPertama?->nip ?? '................................' }}</span>
            </td>
            <td>
                <p class="bold">Pihak Kedua</p>
                <div class="signature-space"></div>
                <p class="signature-name">Amar Manaf</p><br>
                <span style="font-size: 9.5pt;">NIP. {{ $pihakKedua?->nip ?? '................................' }}</span>
            </td>
        </tr>
    </table>

</body>
</html>
