<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class KendaraanController extends Controller
{
    private function normalizeJenis($value)
    {
        return match (strtolower($value)) {
            'motor', 'sepeda motor' => 'sepeda motor',
            'mobil' => 'mobil',
            default => strtolower($value)
        };
    }

    public function getDataKendaraan(Request $request)
    {
        $baseUrl = 'https://tech-test-kominfo-api.fly.dev/v1/bapenda/kendaraan';
        $response = Http::get($baseUrl);

        if ($response->failed()) {
            return response()->json(['error' => 'Failed to fetch data'], 500);
        }

        $jenisKendaraan = $this->normalizeJenis($request->query('jenis_kendaraan'));
        $statusPembayaran = $request->query('status_pembayaran');

        $data = $response->json()['data'];

        if (!empty($jenisKendaraan)) {
            $filtered = collect($data)->filter(function ($item) use ($jenisKendaraan) {
                $matchJenis = $jenisKendaraan
                    ? strtolower($item['jenis_kendaraan'] ?? '') === strtolower($jenisKendaraan)
                    : true;

                return $matchJenis;
            })->values();

            $newData = response()->json([
                'status' => $response->status(),
                'data' => $filtered,
                'message' => 'success get data'
            ]);

            return $newData;
        }

        if (!empty($statusPembayaran)) {
            $filtered = collect($data)->filter(function ($item) use ($statusPembayaran) {
                $matchStatus = match ($statusPembayaran) {
                    'terbayar' => !empty($item['tgl_pembayaran']),
                    'belum_bayar' => empty($item['tgl_pembayaran']),
                    default => true
                };

                return $matchStatus;
            })->values();

            $newData = response()->json([
                'status' => $response->status(),
                'data' => $filtered,
                'message' => 'success get data'
            ]);

            return $newData;
        }

        return response()->json([
            'status' => $response->status(),
            'data' => $data,
            'message' => 'Success get data kendaraan'
        ]);
    }

    public function getRekapPajak()
    {
        $baseUrl = 'https://tech-test-kominfo-api.fly.dev/v1/bapenda/kendaraan';
        $response = Http::get($baseUrl);

        if ($response->failed()) {
            return response()->json(['error' => 'Failed to fetch data'], 500);
        }

        $data = $response->json()['data'];

        $filterTerbayar = collect($data)->filter(function ($item) {
            return !empty($item['tgl_pembayaran']);
        })->values();
        $filterBelumBayar = collect($data)->filter(function ($item) {
            return empty($item['tgl_pembayaran']);
        })->values();

        $totalPajakTerbayar = $filterTerbayar->sum('nominal_pajak');
        $totalPajakBelumBayar = $filterBelumBayar->sum('nominal_pajak');

        $newData = response()->json([
            'status' => $response->status(),
            'statusPajakTerbayar' => $totalPajakTerbayar,
            'statusPajakBelumBayar' => $totalPajakBelumBayar
        ]);

        return $newData;
    }
}
