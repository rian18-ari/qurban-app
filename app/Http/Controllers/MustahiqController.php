<?php

namespace App\Http\Controllers;

use App\Models\DistributionSession;
use App\Models\Mustahiq;
use App\Models\Period;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use OpenSpout\Reader\Common\Creator\ReaderEntityFactory;
use Illuminate\Support\Str;

class MustahiqController extends Controller
{
    public function index()
    {
        $activePeriod = Period::where('is_active', true)->firstOrFail();
        $mustahiqs = Mustahiq::with('distributionSession')
            ->where('period_id', $activePeriod->id)
            ->paginate(15);

        return Inertia::render('Mustahiqs/Index', [
            'mustahiqs' => $mustahiqs,
            'activePeriod' => $activePeriod,
        ]);
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv',
        ]);

        $activePeriod = Period::where('is_active', true)->firstOrFail();
        $filePath = $request->file('file')->getRealPath();
        
        $reader = ReaderEntityFactory::createReaderFromFile($filePath);
        $reader->open($filePath);

        $dataRows = [];
        foreach ($reader->getSheetIterator() as $sheet) {
            foreach ($sheet->getRowIterator() as $index => $row) {
                if ($index === 1) continue; // Skip header
                $cells = $row->getCells();
                
                $dataRows[] = [
                    'name' => $cells[0]->getValue(),
                    'nik' => $cells[1]?->getValue(), // We can still read NIK from excel if they have it
                    'address' => $cells[2]?->getValue(),
                    'phone_number' => $cells[3]?->getValue(),
                ];
            }
        }
        $reader->close();

        return DB::transaction(function () use ($dataRows, $activePeriod) {
            $sessions = DistributionSession::where('period_id', $activePeriod->id)
                ->orderBy('id', 'asc')
                ->get();

            if ($sessions->isEmpty()) {
                return redirect()->back()->withErrors(['file' => 'Buat sesi antrean terlebih dahulu sebelum import.']);
            }

            $currentSessionIndex = 0;
            $currentSession = $sessions[$currentSessionIndex];
            $currentSessionCount = Mustahiq::where('distribution_session_id', $currentSession->id)->count();

            foreach ($dataRows as $rowData) {
                while ($currentSessionCount >= $currentSession->quota) {
                    $currentSessionIndex++;
                    if ($currentSessionIndex >= $sessions->count()) {
                        break 2;
                    }
                    $currentSession = $sessions[$currentSessionIndex];
                    $currentSessionCount = Mustahiq::where('distribution_session_id', $currentSession->id)->count();
                }

                // Auto-generate Coupon Code if NIK is empty, or use NIK prefix
                $randomStr = strtoupper(Str::random(4));
                $couponCode = "QRB-" . $activePeriod->id . "-" . $randomStr . "-" . Str::padLeft($currentSessionCount + 1, 3, '0');

                Mustahiq::create([
                    'period_id' => $activePeriod->id,
                    'distribution_session_id' => $currentSession->id,
                    'name' => $rowData['name'],
                    'coupon_code' => $rowData['nik'] ?: $couponCode, // Use NIK as coupon code if exists, otherwise generate
                    'address' => $rowData['address'],
                    'phone_number' => $rowData['phone_number'],
                ]);

                $currentSessionCount++;
            }

            return redirect()->route('mustahiqs.index')->with('success', 'Data mustahiq berhasil di-import.');
        });
    }
}
