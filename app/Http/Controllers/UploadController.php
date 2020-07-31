<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use ZipArchive;
use App\FileHash;
use App\Gclog;

class UploadController extends Controller
{
    public function __invoke(Request $request)
    {
        $processedRecords = 0;

        $file = $request->file('file');

        if (!$request->file('file')->isValid()) {
            return [
                'message' => 'file is not valid'
            ];
        }


        try {
            Storage::deleteDirectory('tmp');
            $tmpFilepath = "../storage/app/" . $file->store('tmp');
            $zip = new ZipArchive;
            $res = $zip->open($tmpFilepath);
            if ($res === TRUE) {
              $zip->extractTo('../storage/app/tmp/extracted_data');
              $zip->close();
            } else {
                return [
                    'message' => 'invalid zip archive'
                ];
            }

        } catch (\Throwable $th) {
            return [
                'message' => 'error in file extracting'
            ];
        }

        set_time_limit(3600);
        $processed_files = [];
        $failed_files = [];
        $skipped_files = [];
        $found_files = [];

        $fileList = Storage::allFiles('tmp/extracted_data');

        if (empty($fileList)) {
            // Nothing to do if the directory is empty. So returned;
            return [
                "message" => "there are no files to process"
            ];
        };

        
        foreach ($fileList as $eachFile) {

            array_push($found_files, basename($eachFile));

            $fileContent = Storage::get($eachFile);
    
            $hash = sha1($fileContent);
    
            if (FileHash::where('file_hash', '=', $hash)->exists()) {
                // The file has been already processed. So skipped to next file
                array_push($skipped_files, basename($eachFile));
                continue;
            }

            $result = Gclog::parseLog($fileContent);
    
            if ($result !== false) {
                $processedRecords = $processedRecords + $result;
                array_push($processed_files, basename($eachFile));
    
                FileHash::create([
                    'file_name' => basename($eachFile),
                    'file_hash' => $hash
                ]);
            } else {
                array_push($failed_files, basename($eachFile));
            }
        }

        Storage::deleteDirectory('tmp');

        return [
            'message' => 'success',
            'found_files' => $found_files,
            'processed_files' => $processed_files,
            'failed_files' => $failed_files,
            'skipped_files' => $skipped_files,
            'processed_records' => $processedRecords . " records"
        ];
    }
}
