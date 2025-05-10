<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;

class KendaraanController extends Controller
{
    public function getDataKendaraan()
    {
        $baseUrl = 'https://tech-test-kominfo-api.fly.dev/v1/bapenda/kendaraan';
        $response = Http::get($baseUrl);

        if ($response->failed()) {
            return response()->json(['error' => 'Failed to fetch data'], 500);
        }
        $data = $response->json();
        return response()->json($data);
    }

    // public function getKendaraanByJenis(Request $request)
    // {
    //     $response = Http::get('https://tech-test-kominfo-api.fly.dev/v1/bapenda/kendaraan');

    //     if ($response->failed()) {
    //         return response()->json(['error' => 'Failed to fetch data'], 500);
    //     }

    //     $data = $response->json();
    //     $jenis = $request->query('jenis_kendaraan');
    //     if ($jenis) {
    //         $data = array_filter($data, function ($item) use ($jenis) {
    //             return isset($item['jenis_kendaraan']) && strtolower($item['jenis_kendaraan']) === strtolower($jenis);
    //         });
    //     }

    //     return ['data' => $data, 'jenis_kendaraan' => $jenis];
    // }
}
