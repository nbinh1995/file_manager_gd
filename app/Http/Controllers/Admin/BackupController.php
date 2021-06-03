<?php

namespace App\Http\Controllers\Admin;

use App\Models\Setting;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Support\Str;

class BackupController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $keep_days = Setting::get('keep_days', config('job.keep-days'));
        return view('admins.backup.index', ['active' => 7, 'keep_days' => $keep_days]);
    }

    protected function fileInfo($filePath): array
    {
        $fullPath = $filePath['dirname'] . '/' . $filePath['basename'];
        $file = array();
        $file['filename'] = $filePath['basename'];
        $file['modified'] = Carbon::createFromTimestamp(filemtime($fullPath))->toDateTimeString();
        $file['size'] = filesize($fullPath);
        $file['type'] = Str::contains($filePath['basename'], 'man-') ? 'Manual' : 'Automation';
        return $file;
    }

    public function list()
    {
        $file = preg_replace('/[^a-zA-Z0-9.]/', '-', env('APP_URL'));
        $backupFiles = \Storage::disk('local')->files($file);
        $files = [];
        foreach ($backupFiles as $file) {
            $files[] = $this->fileInfo(pathinfo(storage_path('app') . '/' . $file));
        }
        return \DataTables::of($files)
            ->addIndexColumn()
            ->editColumn('size', function ($file) {
                $size = ceil($file['size'] / 1024);
                return number_format($size, 0, ',', ',') . ' KB';
            })
            ->addColumn('Action', function ($file) {
                return "<div class='text-right'>
                <button class='btn btn-info btn-xs download' data-name='{$file['filename']}' ><i class='fas fa-cloud-download-alt'
                style='pointer-events: none'></i> Download</button>
                <button class='btn btn-danger remove btn-xs' data-name='{$file['filename']}'><i class='far fa-trash-alt'
                style='pointer-events: none'></i> Remove</button>
                </div>";
            })
            ->rawColumns(['Action', 'Checked'])
            ->make();
    }

    public function download(Request $request)
    {
        $file = preg_replace('/[^a-zA-Z0-9.]/', '-', env('APP_URL'));
        $basePath = storage_path("app/{$file}");
        $fullPath = $basePath . '/' . $request->name;

        $headers = array(
            'Content-Type: application/octet-stream'
        );
        if (file_exists($fullPath)) {
            return response()->download($fullPath, $request->name, $headers);
        }
        return response()->json('File does not exist', 404);
    }

    public function destroy(Request $request)
    {
        if ($request->has('name')) {
            try {
                $file = preg_replace('/[^a-zA-Z0-9.]/', '-', env('APP_URL'));
                $basePath = storage_path("app/{$file}");
                if (is_array($request->name)) {
                    foreach ($request->name as $name) {
                        $filePath = $basePath . '/' . $name;
                        \File::delete($filePath);
                    }
                } else {
                    $filePath = $basePath . '/' . $request->name;
                    \File::delete($filePath);
                }

                return response()->json(['code' => 204], 200);
            } catch (\Exception $exception) {
                return response()->json(['code' => 500], 500);
            }
        } else {
            return response()->json(['code' => 500], 500);
        }
    }

    public function backupManual(Request $request)
    {
        // BackupJob::$prefix = 'man-';
        $filename = 'man-' . date('Y-m-d-His') . '.zip';
        \Artisan::call('backup:run', ['--only-db' => true, '--filename' => $filename]);

        return response()->json('backup success', 200);
    }
}
