<?php

namespace App\Http\Controllers;

use App\Models\AnimalGroup;
use App\Models\FinancialLog;
use App\Models\Mustahiq;
use App\Models\Period;
use Illuminate\Http\Request;
use OpenSpout\Writer\Common\Creator\WriterEntityFactory;
use OpenSpout\Common\Entity\Row;

class ReportController extends Controller
{
    public function exportExcel()
    {
        $activePeriod = Period::where('is_active', true)->firstOrFail();
        $fileName = "LPJ_Qurban_{$activePeriod->name}.xlsx";
        $filePath = storage_path("app/public/{$fileName}");

        $writer = WriterEntityFactory::createXLSXWriter();
        $writer->openToFile($filePath);

        // --- SHEET 1: RINGKASAN KEUANGAN ---
        $sheet = $writer->getCurrentSheet();
        $sheet->setName('Keuangan (RAB)');
        
        $writer->addRow(WriterEntityFactory::createRowFromArray(['LAPORAN KEUANGAN QURBAN ' . $activePeriod->name]));
        $writer->addRow(WriterEntityFactory::createRowFromArray([]));
        $writer->addRow(WriterEntityFactory::createRowFromArray(['Tgl Transaksi', 'Keterangan', 'Kategori', 'Tipe', 'Jumlah']));

        $logs = FinancialLog::where('period_id', $activePeriod->id)->orderBy('transaction_date')->get();
        foreach ($logs as $log) {
            $writer->addRow(WriterEntityFactory::createRowFromArray([
                $log->transaction_date->format('Y-m-d'),
                $log->description,
                $log->category,
                $log->type,
                $log->amount
            ]));
        }

        // --- SHEET 2: DATA HEWAN & MUDHOHI ---
        $newSheet = $writer->addNewSheetAndMakeItCurrent();
        $newSheet->setName('Data Hewan');
        $writer->addRow(WriterEntityFactory::createRowFromArray(['DATA HEWAN QURBAN & MUDHOHI']));
        $writer->addRow(WriterEntityFactory::createRowFromArray([]));
        $writer->addRow(WriterEntityFactory::createRowFromArray(['Nama Hewan', 'Tipe', 'Status Jagal', 'Daftar Mudhohi']));

        $animals = AnimalGroup::with('mudhohis')->where('period_id', $activePeriod->id)->get();
        foreach ($animals as $animal) {
            $mudhohiNames = $animal->mudhohis->pluck('name')->implode(', ');
            $writer->addRow(WriterEntityFactory::createRowFromArray([
                $animal->name,
                $animal->type,
                $animal->status,
                $mudhohiNames
            ]));
        }

        // --- SHEET 3: DISTRIBUSI DAGING ---
        $newSheet = $writer->addNewSheetAndMakeItCurrent();
        $newSheet->setName('Distribusi Daging');
        $writer->addRow(WriterEntityFactory::createRowFromArray(['DATA PENERIMA DAGING (MUSTAHIQ)']));
        $writer->addRow(WriterEntityFactory::createRowFromArray([]));
        $writer->addRow(WriterEntityFactory::createRowFromArray(['Nama', 'Kode Kupon', 'Alamat', 'Sesi', 'Status Ambil']));

        $mustahiqs = Mustahiq::with('distributionSession')->where('period_id', $activePeriod->id)->get();
        foreach ($mustahiqs as $m) {
            $writer->addRow(WriterEntityFactory::createRowFromArray([
                $m->name,
                $m->coupon_code,
                $m->address,
                $m->distributionSession?->name,
                $m->status === 'Received' ? 'Sudah' : 'Belum'
            ]));
        }

        $writer->close();

        return response()->download($filePath)->deleteFileAfterSend(true);
    }
}
