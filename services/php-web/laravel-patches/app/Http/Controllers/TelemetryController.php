<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class TelemetryController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search', '');
        $sortColumn = $request->query('sort', 'recorded_at');
        $sortDirection = $request->query('direction', 'desc');
        $dateFrom = $request->query('date_from');
        $dateTo = $request->query('date_to');
        
        $query = DB::table('telemetry_legacy');
        
        // Поиск
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('source_file', 'ILIKE', "%{$search}%")
                  ->orWhere('voltage', '::text', 'ILIKE', "%{$search}%")
                  ->orWhere('temp', '::text', 'ILIKE', "%{$search}%");
            });
        }
        
        // Фильтр по дате
        if ($dateFrom) {
            $query->where('recorded_at', '>=', $dateFrom);
        }
        if ($dateTo) {
            $query->where('recorded_at', '<=', $dateTo);
        }
        
        // Сортировка
        $allowedColumns = ['id', 'recorded_at', 'voltage', 'temp', 'source_file'];
        $sortColumn = in_array($sortColumn, $allowedColumns) ? $sortColumn : 'recorded_at';
        $sortDirection = strtolower($sortDirection) === 'asc' ? 'asc' : 'desc';
        
        $items = $query->orderBy($sortColumn, $sortDirection)
                      ->paginate(50);
        
        return view('telemetry.index', [
            'items' => $items,
            'search' => $search,
            'sortColumn' => $sortColumn,
            'sortDirection' => $sortDirection,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
        ]);
    }
    
    public function csvList()
    {
        // Получаем список CSV файлов из volume
        $csvFiles = [];
        $csvPath = '/data/csv';
        
        if (is_dir($csvPath)) {
            $files = scandir($csvPath);
            foreach ($files as $file) {
                if (pathinfo($file, PATHINFO_EXTENSION) === 'csv') {
                    $csvFiles[] = [
                        'name' => $file,
                        'path' => $csvPath . '/' . $file,
                        'size' => filesize($csvPath . '/' . $file),
                        'modified' => date('Y-m-d H:i:s', filemtime($csvPath . '/' . $file)),
                    ];
                }
            }
        }
        
        usort($csvFiles, function($a, $b) {
            return strcmp($b['modified'], $a['modified']);
        });
        
        return view('telemetry.csv-list', ['files' => $csvFiles]);
    }
    
    public function csvView(Request $request, string $filename)
    {
        $csvPath = '/data/csv/' . basename($filename);
        
        if (!file_exists($csvPath) || pathinfo($csvPath, PATHINFO_EXTENSION) !== 'csv') {
            abort(404);
        }
        
        $rows = [];
        $headers = [];
        
        if (($handle = fopen($csvPath, 'r')) !== false) {
            $headers = fgetcsv($handle);
            while (($data = fgetcsv($handle)) !== false) {
                $row = [];
                foreach ($headers as $index => $header) {
                    $row[$header] = $data[$index] ?? '';
                }
                $rows[] = $row;
            }
            fclose($handle);
        }
        
        return view('telemetry.csv-view', [
            'filename' => $filename,
            'headers' => $headers,
            'rows' => $rows,
        ]);
    }
}

