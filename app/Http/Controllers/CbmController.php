<?php

namespace App\Http\Controllers;

use App\Jobs\CbmCsvProcess;
use App\Models\Cbm;
use Illuminate\Http\Request;
use Prophecy\Doubler\Generator\Node\ReturnTypeNode;

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

            $chunks = array_chunk($data, 20);


            $header = [];
            foreach ($chunks as $key => $chunk) {
                // $name = "/tmp{$key}.csv";
                // file_put_contents($path . $name, $chunk);
                // }

                // $files = glob("$path/*.csv");


                // foreach ($files as $key => $file) {
                $data = array_map('str_getcsv', $chunk); 
                if ($key == 0) {
                    $header = $data[0];
                    unset($data[0]);
                }

                CbmCsvProcess::dispatch($data, $header);

                // unlink($file);
            }

            return 'stored';
        };

        return 'upload file';
    }
}
