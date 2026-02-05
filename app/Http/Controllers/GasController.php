<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GasController extends Controller
{
    public function index(Request $request)
    {
        $query = DB::table('gas_volume');
        
        // Filter
        if ($request->shipper) {
            $query->where('shipper', 'LIKE', '%' . $request->shipper . '%');
        }
        if ($request->tahun_dari) {
            $query->where('tahun', '>=', $request->tahun_dari);
        }
        if ($request->tahun_sampai) {
            $query->where('tahun', '<=', $request->tahun_sampai);
        }
        if ($request->bulan) {
            $query->where('bulan', $request->bulan);
        }
        
        $data = $query->orderBy('tahun', 'desc')
                     ->orderBy('bulan', 'desc')
                     ->paginate(20);
        
        // Data untuk filter (semua data tanpa filter)
        $shippers = DB::table('gas_volume')
                     ->select('shipper')
                     ->distinct()
                     ->orderBy('shipper')
                     ->pluck('shipper');
        
        $tahuns = DB::table('gas_volume')
                   ->select('tahun')
                   ->distinct()
                   ->orderBy('tahun', 'desc')
                   ->pluck('tahun');
        
        // Summary statistics (TIDAK TERFILTER)
        $totalVolume = DB::table('gas_volume')->sum('daily_average_mmscfd');
        $totalRecords = DB::table('gas_volume')->count();
        $avgVolume = DB::table('gas_volume')->avg('daily_average_mmscfd');
        
        // FILTERED STATISTICS (untuk komponen yang terfilter)
        $filteredQuery = DB::table('gas_volume');
        
        if ($request->shipper) {
            $filteredQuery->where('shipper', 'LIKE', '%' . $request->shipper . '%');
        }
        if ($request->tahun_dari) {
            $filteredQuery->where('tahun', '>=', $request->tahun_dari);
        }
        if ($request->tahun_sampai) {
            $filteredQuery->where('tahun', '<=', $request->tahun_sampai);
        }
        if ($request->bulan) {
            $filteredQuery->where('bulan', $request->bulan);
        }
        
        // Volume Tertinggi (FILTERED)
        $volumeTertinggi = (clone $filteredQuery)
                            ->orderBy('daily_average_mmscfd', 'desc')
                            ->first();
        
        if (!$volumeTertinggi) {
            $volumeTertinggi = (object)[
                'daily_average_mmscfd' => 0,
                'shipper' => '-',
                'bulan' => '-',
                'tahun' => '-'
            ];
        }
        
        // Volume Terendah (FILTERED)
        $volumeTerendah = (clone $filteredQuery)
                            ->where('daily_average_mmscfd', '>', 0)
                            ->orderBy('daily_average_mmscfd', 'asc')
                            ->first();
        
        if (!$volumeTerendah) {
            $volumeTerendah = (object)[
                'daily_average_mmscfd' => 0,
                'shipper' => '-',
                'bulan' => '-',
                'tahun' => '-'
            ];
        }
        
        return view('dashboard', compact(
            'data', 
            'shippers', 
            'tahuns', 
            'totalVolume', 
            'totalRecords', 
            'avgVolume',
            'volumeTertinggi',
            'volumeTerendah'
        ));
    }
    
    // Chart data with multi-shipper support
    public function chart(Request $request)
    {
        $query = DB::table('gas_volume')
                    ->select(
                        'shipper',
                        'periode', 
                        'tahun', 
                        'bulan', 
                        DB::raw('SUM(daily_average_mmscfd) as total')
                    )
                    ->groupBy('shipper', 'periode', 'tahun', 'bulan');
        
        if ($request->shipper) {
            $shippers = explode(',', $request->shipper);
            $shippers = array_filter($shippers);
            if (!empty($shippers)) {
                $query->whereIn('shipper', $shippers);
            }
        }
        
        if ($request->tahun_dari) {
            $query->where('tahun', '>=', $request->tahun_dari);
        }
        if ($request->tahun_sampai) {
            $query->where('tahun', '<=', $request->tahun_sampai);
        }
        if ($request->bulan) {
            $query->where('bulan', $request->bulan);
        }
        
        $rows = $query->orderBy('tahun')->orderBy('bulan')->get();

        $labels = [];
        $seriesMap = [];

        foreach ($rows as $row) {
            // Label pake "Jan-20" format, bukan periode
            $bulanNama = date('M', mktime(0, 0, 0, (int)$row->bulan, 1));
            $label = $bulanNama . '-' . substr((string)$row->tahun, 2);

            if (!in_array($label, $labels)) {
                $labels[] = $label;
            }
            if (!isset($seriesMap[$row->shipper])) {
                $seriesMap[$row->shipper] = [];
            }
            $seriesMap[$row->shipper][$label] = (float) $row->total;
        }

        $series = [];
        foreach ($seriesMap as $shipper => $dataMap) {
            $values = [];
            foreach ($labels as $label) {
                $values[] = $dataMap[$label] ?? 0;
            }
            $series[] = [
                'name' => $shipper,
                'data' => $values,
            ];
        }

        return response()->json([
            'labels' => $labels,
            'series' => $series,
        ]);
    }
        
    // Top 5 Shipper (FILTERED)
    public function topData(Request $request)
    {
        $query = DB::table('gas_volume')
                    ->select('shipper', DB::raw('SUM(daily_average_mmscfd) as total_volume'))
                    ->groupBy('shipper');
        
        if ($request->shipper) {
            $query->where('shipper', 'LIKE', '%' . $request->shipper . '%');
        }
        if ($request->tahun_dari) {
            $query->where('tahun', '>=', $request->tahun_dari);
        }
        if ($request->tahun_sampai) {
            $query->where('tahun', '<=', $request->tahun_sampai);
        }
        if ($request->bulan) {
            $query->where('bulan', $request->bulan);
        }
        
        $top = $query->orderBy('total_volume', 'desc')
                     ->limit(5)
                     ->get();
        
        return response()->json($top);
    }
    
    // Trend Analysis dengan deteksi anomali
    public function trendAnalysis(Request $request)
    {
        if (!$request->shipper) {
            return response()->json(['error' => 'Shipper harus dipilih'], 400);
        }
        
        $query = DB::table('gas_volume')
                    ->where('shipper', $request->shipper)
                    ->select('tahun', 'bulan', 'periode', 
                             DB::raw('SUM(daily_average_mmscfd) as volume'))
                    ->groupBy('tahun', 'bulan', 'periode')
                    ->orderBy('tahun')
                    ->orderBy('bulan')
                    ->get();
        
        if ($query->count() < 2) {
            return response()->json(['error' => 'Data tidak cukup untuk analisis'], 400);
        }
        
        $trends = [];
        $previousVolume = null;
        
        foreach ($query as $index => $item) {
            if ($previousVolume !== null) {
                $change = $item->volume - $previousVolume;
                $percentChange = $previousVolume != 0 
                    ? ($change / $previousVolume) * 100 
                    : 0;
                
                // Deteksi anomali: perubahan > 20% atau < -20%
                $isAnomaly = abs($percentChange) > 20;
                
                $status = $change > 0 ? 'naik' : ($change < 0 ? 'turun' : 'stabil');
                
                $trends[] = [
                    'periode' => $item->periode,
                    'tahun' => $item->tahun,
                    'bulan' => $item->bulan,
                    'volume' => round($item->volume, 2),
                    'previous_volume' => round($previousVolume, 2),
                    'change' => round($change, 2),
                    'percent_change' => round($percentChange, 2),
                    'status' => $status,
                    'is_anomaly' => $isAnomaly,
                    'anomaly_type' => $isAnomaly 
                        ? ($percentChange > 0 ? 'lonjakan_drastis' : 'penurunan_drastis') 
                        : null
                ];
            }
            
            $previousVolume = $item->volume;
        }
        
        return response()->json([
            'shipper' => $request->shipper,
            'trends' => $trends,
            'total_periods' => count($trends),
            'anomaly_count' => count(array_filter($trends, fn($t) => $t['is_anomaly']))
        ]);
    }
    
    // Comparison Analysis untuk membandingkan antar shipper
    public function comparisonData(Request $request)
    {
        $shippers = $request->shippers; // array of shipper names
        
        if (!$shippers || !is_array($shippers) || count($shippers) < 2) {
            return response()->json(['error' => 'Minimal 2 shipper untuk perbandingan'], 400);
        }
        
        $query = DB::table('gas_volume')
                    ->whereIn('shipper', $shippers)
                    ->select('shipper', 'periode', 'tahun', 'bulan',
                             DB::raw('SUM(daily_average_mmscfd) as volume'))
                    ->groupBy('shipper', 'periode', 'tahun', 'bulan');
        
        if ($request->tahun_dari) {
            $query->where('tahun', '>=', $request->tahun_dari);
        }
        if ($request->tahun_sampai) {
            $query->where('tahun', '<=', $request->tahun_sampai);
        }
        
        $data = $query->orderBy('tahun')
                     ->orderBy('bulan')
                     ->get();
        
        // Group by shipper
        $grouped = $data->groupBy('shipper');
        
        $result = [];
        foreach ($grouped as $shipper => $records) {
            $result[$shipper] = [
                'total_volume' => $records->sum('volume'),
                'avg_volume' => $records->avg('volume'),
                'max_volume' => $records->max('volume'),
                'min_volume' => $records->min('volume'),
                'data_points' => $records->count(),
                'data' => $records->values()
            ];
        }
        
        return response()->json($result);
    }
}