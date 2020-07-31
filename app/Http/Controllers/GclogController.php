<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\Gclog as GclogResource;
use App\Gclog;
use Illuminate\Support\Facades\Storage;
use App\FileHash;

class GclogController extends Controller
{
    public function index(Request $request)
    {
        if ($request->startdatetime && $request->enddatetime) {
            $collection = Gclog::whereBetween('datetime', [$request->startdatetime, $request->enddatetime])->get();

            return GclogResource::collection($collection);
        }

        if (Gclog::getLastOneMonth() !== null) {
            return GclogResource::collection(Gclog::getLastOneMonth());
        } else {
            return [
                'data' => []
            ];
        }

    
    }

    public function parseGclogs()
    {
        set_time_limit(3600);
        $processed_files = [];
        $failed_files = [];
        $skipped_files = [];
    
        // Getting File List
        $files = Storage::allFiles('logfiles');
    
        if (empty($files)) {
            // Nothing to do if the directory is empty. So returned;
            return [
                "message" => "there are no files to process"
            ];
        };
    
        // Looping through the file list
        foreach ($files as $eachFile) {
            $fileContent = Storage::get($eachFile);
    
            $hash = sha1($fileContent);
    
            if (FileHash::where('file_hash', '=', $hash)->exists()) {
                // The file has been already processed. So skipped to next file
                array_push($skipped_files, $eachFile);
                continue;
            }
    
            if (Gclog::parseLog($fileContent)) {
                array_push($processed_files, $eachFile);
    
                FileHash::create([
                    'file_name' => $eachFile,
                    'file_hash' => $hash
                ]);
            } else {
                array_push($failed_files, $eachFile);
            }
        }
    
        return [
            'message' => 'success',
            'found_files' => $files,
            'processed_files' => $processed_files,
            'failed_files' => $failed_files,
            'skipped_files' => $skipped_files
        ];
        }
}
