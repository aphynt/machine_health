<?php

namespace App\Http\Controllers;

use App\Models\KomatsuRefMhParameter;
use Illuminate\Http\Request;

class KomatsuRefMHParameterController extends Controller
{
    //
    public function index()
    {
        $data = KomatsuRefMhParameter::orderBy('GROUP_ITEM')
            ->orderBy('ITEM')
            ->get();

        return view('komatsu.ref-mh-parameter', compact('data'));
    }

    public function update(Request $request)
    {
        try {
            $request->validate([
                'OLD_ITEM' => 'required',
                'ITEM' => 'required',
                'UNIT' => 'nullable',
                'GROUP_ITEM' => 'nullable',
                'DESCRIPTION' => 'nullable',
                'REMARK' => 'nullable',
                'PORTION' => 'nullable|numeric',
                'MIN_THRESHOLD_NORMAL' => 'nullable|numeric',
                'MAX_THRESHOLD_NORMAL' => 'nullable|numeric',
                'MIN_THRESHOLD_CAUTION' => 'nullable|numeric',
                'MAX_THRESHOLD_CAUTION' => 'nullable|numeric',
                'CONVERT_VALUE' => 'nullable|numeric',
                'PORTION_NORMAL' => 'nullable|numeric',
                'PORTION_CAUTION' => 'nullable|numeric',
                'PORTION_CRITICAL' => 'nullable|numeric',
            ]);

            $oldItem = trim($request->OLD_ITEM);

            $exists = KomatsuRefMhParameter::where('ITEM', $oldItem)->exists();

            if (!$exists) {
                return back()->with('info', 'Data parameter ' . $oldItem . ' tidak ditemukan.');
            }

            KomatsuRefMhParameter::where('ITEM', $oldItem)->update([
                'ITEM' => trim($request->ITEM),
                'UNIT' => $request->UNIT,
                'GROUP_ITEM' => $request->GROUP_ITEM,
                'DESCRIPTION' => $request->DESCRIPTION,
                'REMARK' => $request->REMARK,
                'PORTION' => $request->PORTION,
                'MIN_THRESHOLD_NORMAL' => $request->MIN_THRESHOLD_NORMAL,
                'MAX_THRESHOLD_NORMAL' => $request->MAX_THRESHOLD_NORMAL,
                'MIN_THRESHOLD_CAUTION' => $request->MIN_THRESHOLD_CAUTION,
                'MAX_THRESHOLD_CAUTION' => $request->MAX_THRESHOLD_CAUTION,
                'CONVERT_VALUE' => $request->CONVERT_VALUE,
                'PORTION_NORMAL' => $request->PORTION_NORMAL,
                'PORTION_CAUTION' => $request->PORTION_CAUTION,
                'PORTION_CRITICAL' => $request->PORTION_CRITICAL,
            ]);

            return back()->with('success', 'Komatsu Machine Health Parameter berhasil diperbarui.');
        } catch (\Throwable $e) {
            return back()->with('info', 'Gagal memperbarui data: ' . $e->getMessage());
        }
    }
}
