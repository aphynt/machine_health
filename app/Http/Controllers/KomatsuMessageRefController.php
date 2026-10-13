<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Carbon;

class KomatsuMessageRefController extends Controller
{

    public function index(Request $request)
    {
        $rows = DB::table('dbo.REF_WARNING_CODE')
            ->select([
                'CODE',
                'ERROR_MESSAGE',
                'OPERATOR_INDUCED',
                'LINKED_SUB_ERROR',
                'UPDATED_AT',
                'GCOLOR',
            ])
            ->orderBy('CODE')
            ->get();

        $linkedSubErrorOptions = DB::select("
            SET NOCOUNT ON;
            EXEC [dbo].[APP_GET_LIST_REF_KOM_FAULT_CODE]
        ");

        return view('komatsu.message-ref', [
            'rows' => $rows,
            'linkedSubErrorOptions' => $linkedSubErrorOptions,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'CODE' => [
                'required',
                'string',
                'max:50',
                Rule::unique('dbo.REF_WARNING_CODE', 'CODE'),
            ],
            'ERROR_MESSAGE' => ['required', 'string', 'max:255'],
            'OPERATOR_INDUCED' => ['required', 'boolean'],
            'LINKED_SUB_ERROR' => ['nullable', 'array'],
            'LINKED_SUB_ERROR.*' => ['nullable', 'string'],
            'GCOLOR' => ['nullable', 'string', 'max:50'],
        ]);

        DB::table('dbo.REF_WARNING_CODE')->insert([
            'CODE' => trim($validated['CODE']),
            'ERROR_MESSAGE' => trim($validated['ERROR_MESSAGE']),
            'OPERATOR_INDUCED' => (int) $validated['OPERATOR_INDUCED'],
            'LINKED_SUB_ERROR' => $this->joinLinkedSubErrors($validated['LINKED_SUB_ERROR'] ?? []),
            'UPDATED_AT' => Carbon::now(),
            'GCOLOR' => $this->nullableTrim($validated['GCOLOR'] ?? null),
        ]);

        return redirect()
            ->route('komatsu.message-ref')
            ->with('success', 'Data berhasil ditambahkan.');
    }

    public function update(Request $request)
    {
        DB::table('dbo.REF_WARNING_CODE')
            ->where('CODE', trim($request->CODE))
            ->update([
                'ERROR_MESSAGE' => trim($request->ERROR_MESSAGE),
                'OPERATOR_INDUCED' => (int) $request->OPERATOR_INDUCED,
                'LINKED_SUB_ERROR' => $this->joinLinkedSubErrors($request->LINKED_SUB_ERROR ?? []),
                'UPDATED_AT' => now(),
                'GCOLOR' => $this->nullableTrim($request->GCOLOR ?? null),
            ]);

        return redirect()
            ->route('komatsu.message-ref')
            ->with('success', 'Data berhasil diupdate.');
    }

    public function destroy(string $code)
    {
        DB::table('dbo.REF_WARNING_CODE')
            ->where('CODE', $code)
            ->delete();

        return redirect()
            ->route('komatsu.message-ref')
            ->with('success', 'Data berhasil dihapus.');
    }

    private function joinLinkedSubErrors(array $values): ?string
    {
        $clean = collect($values)
            ->map(fn ($v) => trim((string) $v))
            ->filter()
            ->unique()
            ->values()
            ->all();

        return empty($clean) ? null : implode('; ', $clean);
    }

    private function nullableTrim(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $trimmed = trim($value);
        return $trimmed === '' ? null : $trimmed;
    }
}
