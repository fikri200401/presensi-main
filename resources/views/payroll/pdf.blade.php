<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Slip Gaji - {{ $payroll->user->name }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            line-height: 1.4;
            color: #000;
        }
        
        .container {
            width: 100%;
            padding: 20px;
        }
        
        .no-print {
            margin: 20px;
            text-align: center;
        }
        
        .no-print button {
            background-color: #4F46E5;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            margin: 0 5px;
        }
        
        .no-print button:hover {
            background-color: #4338CA;
        }
        
        .no-print .btn-close {
            background-color: #6B7280;
        }
        
        .no-print .btn-close:hover {
            background-color: #4B5563;
        }
        
        @media print {
            .no-print {
                display: none;
            }
            
            body {
                margin: 0;
                padding: 0;
            }
            
            .container {
                padding: 10px;
            }
        }
        
        /* Header Section */
        .header {
            text-align: center;
            margin-bottom: 15px;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
        }
        
        .header h2 {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 3px;
        }
        
        .header .subtitle {
            font-size: 12px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .header .period {
            font-size: 11px;
        }
        
        /* Info Table */
        .info-section {
            margin-bottom: 15px;
        }
        
        .info-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .info-table td {
            padding: 3px 5px;
            vertical-align: top;
        }
        
        .info-table .label {
            width: 30%;
            font-weight: bold;
        }
        
        .info-table .separator {
            width: 5%;
            text-align: center;
        }
        
        .info-table .value {
            width: 65%;
        }
        
        /* Main Table */
        .salary-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        
        .salary-table th,
        .salary-table td {
            border: 1px solid #000;
            padding: 5px;
        }
        
        .salary-table th {
            background-color: #e0e0e0;
            font-weight: bold;
            text-align: center;
        }
        
        .salary-table td {
            vertical-align: top;
        }
        
        .salary-table .label-col {
            width: 50%;
        }
        
        .salary-table .value-col {
            width: 25%;
            text-align: right;
        }
        
        .salary-table .category-header {
            font-weight: bold;
            text-transform: uppercase;
            background-color: #f5f5f5;
        }
        
        .salary-table .item {
            padding-left: 15px;
        }
        
        .salary-table .total-row {
            font-weight: bold;
        }
        
        .salary-table .grand-total {
            font-weight: bold;
            font-size: 12px;
            background-color: #f0f0f0;
        }
        
        /* Summary Section */
        .summary-section {
            margin-top: 15px;
        }
        
        .summary-table {
            width: 60%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        
        .summary-table td {
            padding: 4px 8px;
        }
        
        .summary-table .label {
            width: 50%;
        }
        
        .summary-table .separator {
            width: 5%;
            text-align: center;
        }
        
        .summary-table .value {
            width: 45%;
            text-align: right;
        }
        
        /* Signature Section */
        .signature-section {
            margin-top: 30px;
            width: 100%;
        }
        
        .signature-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .signature-table td {
            width: 50%;
            text-align: center;
            vertical-align: top;
            padding: 5px;
        }
        
        .signature-box {
            margin-bottom: 60px;
        }
        
        .signature-line {
            border-top: 1px solid #000;
            display: inline-block;
            width: 150px;
            margin-top: 50px;
        }
        
        .signature-name {
            font-weight: bold;
            margin-top: 5px;
        }
        
        /* Notes Section */
        .notes {
            margin-top: 20px;
            font-size: 10px;
            font-style: italic;
        }
        
        .text-right {
            text-align: right;
        }
        
        .text-center {
            text-align: center;
        }
        
        .font-bold {
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h2>RAHASIA</h2>
            <div class="subtitle">SLIP GAJI</div>
            <div class="period">PERIODE BULAN {{ strtoupper(\Carbon\Carbon::parse($payroll->periode . '-01')->translatedFormat('F Y')) }}</div>
        </div>
        
        <!-- Employee Info -->
        <div class="info-section">
            <table class="info-table">
                <tr>
                    <td class="label">PT. BPR SYARIAH AL-HIKMAH AMANAH</td>
                    <td class="separator">:</td>
                    <td class="value"></td>
                </tr>
                <tr>
                    <td class="label">JI. Proklamasi No 25 Depok II</td>
                    <td class="separator">:</td>
                    <td class="value"></td>
                </tr>
                <tr>
                    <td class="label"></td>
                    <td class="separator"></td>
                    <td class="value"></td>
                </tr>
                <tr>
                    <td class="label">Nama/Nama Hari Kerja</td>
                    <td class="separator">:</td>
                    <td class="value">{{ $payroll->user->name }}</td>
                </tr>
                <tr>
                    <td class="label">NIK/NIP</td>
                    <td class="separator">:</td>
                    <td class="value">{{ $payroll->user->id }}</td>
                </tr>
                <tr>
                    <td class="label">Status</td>
                    <td class="separator">:</td>
                    <td class="value">Karyawan</td>
                </tr>
                <tr>
                    <td class="label">NPP/OP</td>
                    <td class="separator">:</td>
                    <td class="value">-</td>
                </tr>
                <tr>
                    <td class="label">Pangkat</td>
                    <td class="separator">:</td>
                    <td class="value">-</td>
                </tr>
                <tr>
                    <td class="label">Golongan</td>
                    <td class="separator">:</td>
                    <td class="value">-</td>
                </tr>
                <tr>
                    <td class="label"></td>
                    <td class="separator"></td>
                    <td class="value"></td>
                </tr>
                <tr>
                    <td class="label">Grade</td>
                    <td class="separator">:</td>
                    <td class="value">S</td>
                </tr>
            </table>
        </div>
        
        <!-- Main Salary Table -->
        <table class="salary-table">
            <thead>
                <tr>
                    <th colspan="2">PENDAPATAN</th>
                    <th colspan="2">POTONGAN</th>
                </tr>
            </thead>
            <tbody>
                <!-- Gaji Pokok -->
                <tr>
                    <td class="item">Gaji Pokok</td>
                    <td class="value-col">Rp. {{ number_format($payroll->gaji_pokok, 0, ',', '.') }}</td>
                    <td class="item">Pembayaran Motor</td>
                    <td class="value-col">Rp. 0</td>
                </tr>
                
                <!-- Tunjangan -->
                @if($payroll->tunjangan_transport > 0)
                <tr>
                    <td class="item">Tunjangan Transport</td>
                    <td class="value-col">Rp. {{ number_format($payroll->tunjangan_transport, 0, ',', '.') }}</td>
                    <td class="item">Pemb.Sepeda Motor</td>
                    <td class="value-col">Rp. 0</td>
                </tr>
                @endif
                
                @if($payroll->tunjangan_makan > 0)
                <tr>
                    <td class="item">Tunjangan Makan</td>
                    <td class="value-col">Rp. {{ number_format($payroll->tunjangan_makan, 0, ',', '.') }}</td>
                    <td class="item">Pemb. Listrik Umum</td>
                    <td class="value-col">Rp. 0</td>
                </tr>
                @endif
                
                <tr>
                    <td class="item">Uang Lembur</td>
                    <td class="value-col">Rp. 0</td>
                    <td class="item">PPH 21</td>
                    <td class="value-col">Rp. {{ number_format($payroll->potongan_pph21, 0, ',', '.') }}</td>
                </tr>
                
                @if($payroll->tunjangan_lainnya > 0)
                <tr>
                    <td class="item">Tunjangan Lainnya</td>
                    <td class="value-col">Rp. {{ number_format($payroll->tunjangan_lainnya, 0, ',', '.') }}</td>
                    <td class="item">Asuransi JKTK</td>
                    <td class="value-col">Rp. 0</td>
                </tr>
                @endif
                
                <tr>
                    <td class="item">Insentif</td>
                    <td class="value-col">Rp. 0</td>
                    <td class="item">BPJS Kes</td>
                    <td class="value-col">Rp. {{ number_format($payroll->potongan_bpjs_kesehatan, 0, ',', '.') }}</td>
                </tr>
                
                <tr>
                    <td class="item">Gold</td>
                    <td class="value-col">Rp. 0</td>
                    <td class="item">WFH</td>
                    <td class="value-col">Rp. 0</td>
                </tr>
                
                <tr>
                    <td class="item">Uang GOT</td>
                    <td class="value-col">Rp. 0</td>
                    <td class="item"></td>
                    <td class="value-col"></td>
                </tr>
                
                <tr>
                    <td class="item">Tunjangan IT</td>
                    <td class="value-col">Rp. 0</td>
                    <td class="item"></td>
                    <td class="value-col"></td>
                </tr>
                
                <tr>
                    <td class="item">WFH</td>
                    <td class="value-col">Rp. 0</td>
                    <td class="item"></td>
                    <td class="value-col"></td>
                </tr>
                
                <tr>
                    <td class="item"></td>
                    <td class="value-col"></td>
                    <td class="item"></td>
                    <td class="value-col"></td>
                </tr>
                
                <tr>
                    <td class="item">Total Masuk</td>
                    <td class="value-col font-bold">{{ $payroll->total_hari_hadir }}</td>
                    <td class="item font-bold">JUMLAH POTONGAN</td>
                    <td class="value-col font-bold">Rp. {{ number_format($payroll->total_potongan, 0, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>
        
        <!-- Summary -->
        <div class="summary-section">
            <table class="summary-table">
                <tr>
                    <td class="label">Gaji/Total Pendapatan</td>
                    <td class="separator">:</td>
                    <td class="value">Rp. {{ number_format($payroll->gaji_kotor, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td class="label">Pph.Tunjangan</td>
                    <td class="separator">:</td>
                    <td class="value">Rp. 0</td>
                </tr>
                <tr>
                    <td class="label">Total</td>
                    <td class="separator">:</td>
                    <td class="value font-bold">Rp. {{ number_format($payroll->gaji_kotor, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td class="label">Potongan</td>
                    <td class="separator">:</td>
                    <td class="value">Rp. {{ number_format($payroll->total_potongan, 0, ',', '.') }}</td>
                </tr>
                <tr style="border-bottom: 2px solid #000;">
                    <td class="label"></td>
                    <td class="separator"></td>
                    <td class="value"></td>
                </tr>
                <tr>
                    <td class="label font-bold">Total Tahun {{ \Carbon\Carbon::parse($payroll->periode . '-01')->format('Y') }}</td>
                    <td class="separator">:</td>
                    <td class="value"></td>
                </tr>
                <tr>
                    <td class="label">Jan / Zakat</td>
                    <td class="separator">:</td>
                    <td class="value">Rp. {{ $payroll->periode === date('Y') . '-01' ? number_format($payroll->gaji_bersih, 0, ',', '.') : '0' }}</td>
                </tr>
                <tr>
                    <td class="label">Datang Siang</td>
                    <td class="separator">:</td>
                    <td class="value">{{ $payroll->total_terlambat }} hari</td>
                </tr>
            </table>
        </div>
        
        <!-- Total Section -->
        <table class="salary-table">
            <tr class="grand-total">
                <td class="text-center" style="width: 50%;">
                    Gaji Kotor<br>
                    <strong>Rp. {{ number_format($payroll->gaji_kotor, 0, ',', '.') }}</strong>
                </td>
                <td class="text-center" style="width: 50%;">
                    Gaji Bersih<br>
                    <strong>Rp. {{ number_format($payroll->gaji_bersih, 0, ',', '.') }}</strong>
                </td>
            </tr>
        </table>
        
        <!-- Signature Section -->
        <div class="signature-section">
            <table class="signature-table">
                <tr>
                    <td>
                        <div>Depok, {{ \Carbon\Carbon::parse($payroll->created_at)->format('d F Y') }}</div>
                        <div>Diserahkan Oleh :</div>
                        <div class="signature-box"></div>
                        <div class="signature-line"></div>
                        <div class="signature-name">Arie Wahyuning Tyas</div>
                        <div style="margin-top: 5px;"><strong>Direktur</strong></div>
                    </td>
                    <td>
                        <div>&nbsp;</div>
                        <div>Di terima Oleh :</div>
                        <div class="signature-box"></div>
                        <div class="signature-line"></div>
                        <div class="signature-name">{{ $payroll->user->name }}</div>
                        <div style="margin-top: 5px;">&nbsp;</div>
                    </td>
                </tr>
            </table>
        </div>
        
        <!-- Footer Notes -->
        <div class="notes" style="margin-top: 30px; text-align: center;">
            <p>Note :</p>
        </div>
    </div>
    
    <!-- Print Controls -->
    <div class="no-print">
        <button onclick="window.print()">
            <svg xmlns="http://www.w3.org/2000/svg" style="width: 20px; height: 20px; display: inline-block; vertical-align: middle; margin-right: 5px;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
            </svg>
            Cetak / Save as PDF
        </button>
        <button onclick="window.close()" class="btn-close">
            <svg xmlns="http://www.w3.org/2000/svg" style="width: 20px; height: 20px; display: inline-block; vertical-align: middle; margin-right: 5px;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
            Tutup
        </button>
    </div>
</body>
</html>
