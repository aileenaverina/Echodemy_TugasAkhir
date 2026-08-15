<?php

namespace App\Http\Controllers;

use App\Models\Wilayah;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WilayahController extends Controller
{
    public function provinsi(): JsonResponse
    {
        return response()->json(
            Wilayah ::provinsi()->orderBy('nama')->get(['kode', 'nama'])
        );
    }

    public function children(string $kode): JsonResponse
    {
        return response()->json(
            Wilayah::childrenOf($kode)->orderBy('nama')->get(['kode', 'nama'])
        );
    }
}
