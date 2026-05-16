<?php

namespace App\Http\Controllers;

use App\Models\Delay;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    // Renders the main dashboard view
    public function index()
    {
        return view('dashboard');
    }

    // --- HELPER: Apply Date Filters to all queries ---
    private function applyFilters($query, Request $request)
    {
        if ($request->filled('start')) {
            $query->whereDate('delivery_date', '>=', $request->start);
        }
        if ($request->filled('end')) {
            $query->whereDate('delivery_date', '<=', $request->end);
        }
        return $query;
    }

    // 1. Equipment Pie Chart
    public function equipment(Request $request)
    {
        $data = $this->applyFilters(Delay::query(), $request)
            ->select('eqpt', DB::raw('SUM(delay_duration) as total'))
            ->groupBy('eqpt')
            ->pluck('total', 'eqpt');

        return response()->json($data);
    }

    // 2. Sub-Equipment Pie Chart
    public function subEquipment(Request $request)
    {
        $query = $this->applyFilters(Delay::query(), $request);
        
        // If a user clicks an equipment slice, filter the sub-equipment chart!
        if ($request->filled('eqpt')) {
            $query->where('eqpt', $request->eqpt);
        }

        $data = $query->select('sub_eqpt', DB::raw('SUM(delay_duration) as total'))
            ->groupBy('sub_eqpt')
            ->pluck('total', 'sub_eqpt');

        return response()->json($data);
    }

    // 3. Shop Code Bar Chart
    public function shop(Request $request)
    {
        $data = $this->applyFilters(Delay::query(), $request)
            ->select('shop_code', DB::raw('SUM(delay_duration) as total'))
            ->groupBy('shop_code')
            ->orderByDesc('total')
            ->pluck('total', 'shop_code');

        return response()->json($data);
    }

    // 4. Monthly Delay Trend Bar Chart
    public function monthly(Request $request)
    {
        $data = $this->applyFilters(Delay::query(), $request)
            ->select(
                DB::raw("DATE_FORMAT(delivery_date, '%b %Y') as month_year"),
                DB::raw('SUM(delay_duration) as total')
            )
            ->groupBy('month_year')
            ->orderBy(DB::raw("MIN(delivery_date)")) // Keep the months in chronological order
            ->pluck('total', 'month_year');

        return response()->json($data);
    }

    // 5. Cumulative Delay Line Chart
    public function cumulative(Request $request)
    {
        $data = $this->applyFilters(Delay::query(), $request)
            ->select(
                DB::raw("DATE_FORMAT(delivery_date, '%m-%d') as date"),
                DB::raw('MAX(cumulative_delay) as cum')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return response()->json($data);
    }

    // 6. Continue Status Donut Chart
    public function continueStats(Request $request)
    {
        // Dynamically group the data to catch any variations (Y, y, Yes, N, n, No)
        $stats = $this->applyFilters(Delay::query(), $request)
            ->select('continue_y_n', DB::raw('count(*) as count'))
            ->groupBy('continue_y_n')
            ->pluck('count', 'continue_y_n');

        $yes = 0;
        $no = 0;

        foreach ($stats as $key => $val) {
            // Check if the key matches any affirmative word
            if (in_array(trim(strtoupper($key)), ['Y', 'YES', '1', 'TRUE'])) {
                $yes += $val;
            } else {
                $no += $val;
            }
        }

        return response()->json(['Y' => $yes, 'N' => $no]);
    }

    // 7. Paginated Data Table (For the API Explorer tab)
    public function stats(Request $request)
    {
        $query = $this->applyFilters(Delay::query(), $request);

        // Quick search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('eqpt', 'like', "%{$search}%")
                  ->orWhere('sub_eqpt', 'like', "%{$search}%")
                  ->orWhere('shop_code', 'like', "%{$search}%");
            });
        }

        $paginator = $query->orderBy('delivery_date', 'desc')->paginate(15);

        return response()->json([
            'data' => $paginator->items(),
            'total' => $paginator->total(),
            'pages' => $paginator->lastPage(),
            'page' => $paginator->currentPage(),
        ]);
    }

    // 8. CSV Export
    public function exportCsv(Request $request)
    {
        $query = $this->applyFilters(Delay::query(), $request);
        $data = $query->orderBy('delivery_date', 'asc')->get();

        $csvData = "id,eqpt,sub_eqpt,shop_code,delay_duration,cumulative_delay,delivery_date,continue_y_n\n";

        foreach($data as $row) {
            $csvData .= "{$row->id},{$row->eqpt},{$row->sub_eqpt},{$row->shop_code},{$row->delay_duration},{$row->cumulative_delay},{$row->delivery_date},{$row->continue_y_n}\n";
        }

        return response($csvData)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="delay_export.csv"');
    }

    // 9. Demo Module Data
    public function demoStats()
    {
        // Fetch everything directly from your new sample_demo table
        $data = DB::table('sample_demo')->get();
        
        return response()->json($data);
    }
}