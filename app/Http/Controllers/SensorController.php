<?php

namespace App\Http\Controllers;

use App\Models\SensorLog;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class SensorController extends Controller
{
    /* ================= SENSOR INPUT ================= */
    public function store(Request $request)
    {
        SensorLog::create($request->only([
            'temperature', 'humidity', 'flame', 'status'
        ]));

        return response()->json(['success' => true]);
    }

    /* ================= DASHBOARD ================= */
    public function dashboard()
    {
        return view('dashboard');
    }

    public function latest()
    {
        return response()->json(
            SensorLog::latest()->first()
        );
    }

    public function trends()
    {
        return response()->json(
            SensorLog::latest()->take(10)->get()->reverse()->values()
        );
    }

    /* ================= LOGS ================= */
    public function logsPage()
    {
        return view('logs');
    }

    public function logsCritical()
    {
        return response()->json(
            SensorLog::where('status', 'CRITICAL')
                ->latest()
                ->paginate(10)
        );
    }

    /* ================= PDF ================= */
    public function exportPdf()
    {
        $logs = SensorLog::where('status', 'CRITICAL')->latest()->get();
        return Pdf::loadView('report_pdf', compact('logs'))
            ->download('laporan-fire-critical.pdf');
    }
}
