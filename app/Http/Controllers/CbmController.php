<?php

namespace App\Http\Controllers;

use App\Jobs\CbmCsvProcess;
use Illuminate\Support\Facades\Bus;

class CbmController extends Controller
{
    public function index()
    {
        return view('upload_file');
    }

    public function upload()
    {
        if (request()->has('mycsv')) {
            $data = file(request()->mycsv);

            $chunks = array_chunk($data, 1000);

            $header = [];
            $batch = Bus::batch([])-> dispatch();
            foreach ($chunks as $key => $chunk) {

                $data = array_map('str_getcsv', $chunk);
                if ($key == 0) {
                    $header = $data[0];
                    unset($data[0]);
                }
                $batch->add(new CbmCsvProcess($data,$header));
                CbmCsvProcess::dispatch($data, $header);
            }

            return $batch;
        };

        return 'upload file';
    }

    public function batch()
    {
        $batchId = request('id');
        return Bus::findBatch($batchId);
    }
}
