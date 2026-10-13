<?php

namespace App\Http\Controllers;

use App\Models\KomatsuSNRef;
use Illuminate\Http\Request;

class KomatsuSNRefController extends Controller
{
    //
    public function index()
    {
        $data = KomatsuSNRef::all();
        return view('komatsu.sn-ref', compact('data'));
    }

    public function update(Request $request)
    {
        try {

            $request->validate([
                'OLD_SN_CODE' => 'required',
                'SN_CODE'     => 'required',
                'STATUS'      => 'required|in:0,1',
            ]);

            $oldSnCode = trim($request->OLD_SN_CODE);

            // Cek apakah data ada
            $exists = KomatsuSNRef::where('SN_CODE', $oldSnCode)->exists();

            if (!$exists) {
                return redirect()
                    ->back()
                    ->with(
                        'info',
                        'Data SN Code ' . $oldSnCode . ' tidak ditemukan.'
                    );
            }

            // Update langsung berdasarkan SN_CODE
            KomatsuSNRef::where('SN_CODE', $oldSnCode)
                ->update([
                    'SN_CODE'    => trim($request->SN_CODE),
                    'IP_ADDRESS' => $request->IP_ADDRESS,
                    'VHC_ID'     => $request->VHC_ID,
                    'STATUS'     => (int) $request->STATUS,
                ]);

            return redirect()
                ->back()
                ->with(
                    'success',
                    'Data Komatsu SN Code berhasil diperbarui.'
                );

        } catch (\Throwable $e) {

            return redirect()
                ->back()
                ->with(
                    'info',
                    'Gagal memperbarui data: ' . $e->getMessage()
                );
        }
    }
}
