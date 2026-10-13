@include('layout.head')
@include('layout.sidebar')



<style>
    .ref-warning-page .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 16px;
        margin-bottom: 18px;
        padding: 18px 20px;
        background: linear-gradient(135deg, #ffffff 0%, #f7faff 100%);
        border: 1px solid #e6edf7;
        border-radius: 10px;
        box-shadow: 0 10px 30px rgba(15, 23, 42, 0.06);
    }

    .ref-warning-page .page-title {
        margin: 0;
        font-size: 24px;
        font-weight: 800;
        color: #0f172a;
    }

    .ref-warning-page .page-subtitle {
        margin: 4px 0 0;
        color: #64748b;
        font-size: 13px;
    }

    .ref-warning-page .stats-wrap {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
    }

    .ref-warning-page .stat-chip {
        min-width: 140px;
        padding: 10px 14px;
        border-radius: 14px;
        background: #fff;
        border: 1px solid #e5edf7;
    }

    .ref-warning-page .stat-label {
        font-size: 12px;
        color: #64748b;
        margin-bottom: 4px;
    }

    .ref-warning-page .stat-value {
        font-size: 20px;
        font-weight: 800;
        color: #0f172a;
        line-height: 1;
    }

    .ref-warning-page .panel-card {
        border: 1px solid #e6edf7;
        border-radius: 10px;
        background: #fff;
        box-shadow: 0 10px 30px rgba(15, 23, 42, 0.05);
        overflow: hidden;
    }

    .ref-warning-page .panel-card .card-header {
        background: #fff;
        border-bottom: 1px solid #edf2f7;
        padding: 16px 18px;
    }

    .ref-warning-page .panel-card .card-title {
        margin: 0;
        font-size: 16px;
        font-weight: 700;
        color: #0f172a;
    }

    .ref-warning-page .panel-card .card-subtitle {
        margin: 4px 0 0;
        font-size: 12px;
        color: #64748b;
    }

    .ref-warning-page .toolbar {
        display: flex;
        gap: 10px;
        align-items: center;
        flex-wrap: wrap;
    }

    .ref-warning-page .toolbar .form-control,
    .ref-warning-page .toolbar .form-select {
        min-width: 180px;
        border-radius: 12px;
        border-color: #dbe6f3;
        box-shadow: none;
    }

    .ref-warning-page .toolbar .btn {
        border-radius: 12px;
        padding-inline: 14px;
        font-weight: 600;
    }

    .ref-warning-page .warning-table {
        margin: 0;
        width: 100%;
        table-layout: auto;
        vertical-align: middle;
    }

    .ref-warning-page .warning-table th,
    .ref-warning-page .warning-table td {
        white-space: normal !important;
        word-break: break-word;
        overflow-wrap: anywhere;
        vertical-align: top;
    }

    .ref-warning-page .warning-table td {
        font-size: 12px;
        color: #1e293b;
        border-color: #eef3f8;
    }

    .ref-warning-page .warning-table tbody tr:hover {
        background: #f8fbff;
    }

    .ref-warning-page .table-shell {
        max-height: calc(100vh - 290px);
        overflow: auto;
    }

    .ref-warning-page .code-cell {
        font-weight: 700;
        color: #1d4ed8;
        white-space: normal !important;
        word-break: break-word;
        overflow-wrap: anywhere;
        min-width: 110px;
    }

    .ref-warning-page .message-cell {
        min-width: 260px;
        max-width: 320px;
        white-space: normal !important;
        word-break: break-word;
        overflow-wrap: anywhere;
    }

    .ref-warning-page .linked-cell {
        min-width: 320px;
        max-width: 480px;
        color: #475569;
        white-space: normal !important;
        word-break: break-word;
        overflow-wrap: anywhere;
    }

    .message-cell,
    .linked-cell {
        white-space: normal !important;
        word-break: break-word;
    }


    .ref-warning-page .badge-yes,
    .ref-warning-page .badge-no {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 86px;
        padding: 6px 10px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 700;
    }

    .ref-warning-page .badge-yes {
        color: #166534;
        background: #dcfce7;
    }

    .ref-warning-page .badge-no {
        color: #475569;
        background: #e2e8f0;
    }

    .ref-warning-page .color-pill {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 6px 10px;
        border-radius: 999px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        font-size: 12px;
        color: #334155;
        white-space: nowrap;
    }

    .ref-warning-page .color-dot {
        width: 12px;
        height: 12px;
        border-radius: 50%;
        border: 1px solid rgba(15, 23, 42, 0.12);
        flex: 0 0 auto;
    }

    .ref-warning-page .action-btns {
        display: flex;
        gap: 8px;
        align-items: center;
        white-space: nowrap;
    }

    .ref-warning-page .action-btns .btn {
        border-radius: 10px;
        padding: 6px 10px;
        font-size: 12px;
        font-weight: 700;
    }

    .ref-warning-page .modal-content {
        border: 0;
        box-shadow: 0 24px 60px rgba(15, 23, 42, 0.18);
    }

    .ref-warning-page .modal-header,
    .ref-warning-page .modal-footer {
        border-color: #edf2f7;
    }

    .ref-warning-page .form-label {
        font-weight: 700;
        color: #334155;
        font-size: 13px;
        margin-bottom: 6px;
    }

    .ref-warning-page .form-control,
    .ref-warning-page .form-select,
    .ref-warning-page .form-check-input {
        box-shadow: none !important;
    }

    .ref-warning-page .empty-state {
        padding: 48px 24px;
        text-align: center;
        color: #64748b;
    }

    .ref-warning-page .ts-wrapper.multi .ts-control {
        min-height: 110px;
        padding: 10px 12px;
        border-radius: 12px;
        border-color: #dbe6f3;
        box-shadow: none;
    }

    .ref-warning-page .ts-wrapper .item {
        background: #e8f0ff;
        color: #1d4ed8;
        border-radius: 999px;
        padding: 4px 10px;
        font-size: 12px;
        font-weight: 600;
    }
</style>

@php
    $warningRows = collect($rows ?? $data ?? []);
    $linkedOptions = collect($linkedSubErrorOptions ?? []);
    $totalRows = $warningRows->filter(fn($r) => filled($r->CODE))->count();
    $operatorInducedCount = $warningRows->where('OPERATOR_INDUCED', 1)->count();
    $updatedTodayCount = $warningRows->filter(function ($r) {
        if (empty($r->UPDATED_AT)) return false;
        try {
            return \Carbon\Carbon::parse($r->UPDATED_AT)->isToday();
        } catch (\Throwable $e) {
            return false;
        }
    })->count();
@endphp

<div class="page-content ref-warning-page">
    <div class="container-fluid">
        <div class="page-header">
            <div>
                <h1 class="page-title">Caterpillar SN Code Reference</h1>
                <p class="page-subtitle">Data referensi warning code</p>
            </div>
            <div class="stats-wrap">
                <div class="stat-chip">
                    <div class="stat-label">Total Code</div>
                    <div class="stat-value">{{ number_format($totalRows) }}</div>
                </div>
                <div class="stat-chip">
                    <div class="stat-label">Operator Induced</div>
                    <div class="stat-value">{{ number_format($operatorInducedCount) }}</div>
                </div>
                <div class="stat-chip">
                    <div class="stat-label">Updated Today</div>
                    <div class="stat-value">{{ number_format($updatedTodayCount) }}</div>
                </div>
            </div>
        </div>

        <div class="panel-card">
            <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <h4 class="card-title">Reference Data Table</h4>
                    <p class="card-subtitle">Cari, filter, dan edit data warning code langsung dari halaman ini.</p>
                </div>
                <div class="toolbar">
                    <input type="text" id="warningSearch" class="form-control" placeholder="Search code, message, linked sub error...">
                    <select id="operatorFilter" class="form-select">
                        <option value="">All Operator Induced</option>
                        <option value="1">Operator Induced</option>
                        <option value="0">Not Operator Induced</option>
                    </select>
                    <button type="button" class="btn btn-primary" id="addNewBtn">Add New</button>
                </div>
            </div>

            <div class="table-shell">
                <table class="table warning-table" id="warningTable">
                    <thead>
                        <tr>
                            <th style="width: 120px;">Code</th>
                            <th>Error Message</th>
                            <th style="width: 160px;">Operator Induced</th>
                            <th>Linked Sub Error</th>
                            <th style="width: 170px;">Updated At</th>
                            <th style="width: 140px;">Color</th>
                            <th style="width: 170px;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($warningRows as $index => $row)
                            @if(filled($row->CODE) || filled($row->ERROR_MESSAGE))
                                <tr
                                    data-code="{{ strtolower($row->CODE ?? '') }}"
                                    data-message="{{ strtolower($row->ERROR_MESSAGE ?? '') }}"
                                    data-linked="{{ strtolower($row->LINKED_SUB_ERROR ?? '') }}"
                                    data-operator="{{ (string)($row->OPERATOR_INDUCED ?? 0) }}"
                                    data-row-index="{{ $index }}"
                                >
                                    <td class="code-cell">{{ $row->CODE }}</td>
                                    <td class="message-cell">{{ $row->ERROR_MESSAGE }}</td>
                                    <td>
                                        @if((int)($row->OPERATOR_INDUCED ?? 0) === 1)
                                            <span class="badge-yes">Yes</span>
                                        @else
                                            <span class="badge-no">No</span>
                                        @endif
                                    </td>
                                    <td class="linked-cell">{{ $row->LINKED_SUB_ERROR ?: '-' }}</td>
                                    <td>{{ \Carbon\Carbon::parse($row->UPDATED_AT)->format('d M Y H:i') }}</td>
                                    <td>
                                        <span class="color-pill">
                                            <span class="color-dot" style="background: {{ $row->GCOLOR ?: '#ffffff' }};"></span>
                                            {{ $row->GCOLOR ?: 'None' }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="action-btns">
                                            <button type="button" class="btn btn-outline-primary btn-sm edit-row-btn" data-row='@json($row)'>Edit</button>
                                            {{-- <form method="POST" action="{{ route('caterpillar.message-ref.delete', $row->CODE) }}" onsubmit="return confirm('Yakin hapus data ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger btn-sm">Delete</button>
                                            </form> --}}
                                        </div>
                                    </td>
                                </tr>
                            @endif
                        @empty
                            <tr>
                                <td colspan="7">
                                    <div class="empty-state">No data available.</div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade ref-warning-page" id="warningModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl" style="max-height: 95vh; margin: 10px auto;">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h5 class="modal-title" id="warningModalTitle">Edit Warning Code</h5>
                    <div class="text-muted small">Update field reference data di form berikut.</div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="warningForm" method="POST" action="{{ route('caterpillar.message-ref.store') }}">
                @csrf
                <input type="hidden" name="mode" id="formMode" value="create">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Code</label>
                            <input type="text" class="form-control" name="CODE" id="formCode" required>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label">Error Message</label>
                            <input type="text" class="form-control" name="ERROR_MESSAGE" id="formErrorMessage" required>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Operator Induced</label>
                            <select class="form-select" name="OPERATOR_INDUCED" id="formOperatorInduced">
                                <option value="0">No</option>
                                <option value="1">Yes</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Color</label>
                            <input type="text" class="form-control" name="GCOLOR" id="formColor" placeholder="e.g. Tomato">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Linked Sub Error</label>
                            <select class="form-select" name="LINKED_SUB_ERROR[]" id="formLinkedSubError" multiple>
                                @foreach($linkedOptions as $item)
                                    @php
                                        $optValue = $item->ERROR_MESSAGE ?? $item->CODE ?? null;
                                        $optLabel = trim(($item->CODE ?? '') . (!empty($item->ERROR_MESSAGE) ? ' - ' . $item->ERROR_MESSAGE : ''));
                                    @endphp
                                    @if($optValue)
                                        <option value="{{ $optValue }}">{{ $optLabel ?: $optValue }}</option>
                                    @endif
                                @endforeach
                            </select>
                            <div class="form-text">Bisa pilih lebih dari satu fault code. Nilai akan disimpan otomatis sebagai text gabungan.</div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>


<script>
    document.addEventListener('DOMContentLoaded', function () {
        const searchInput = document.getElementById('warningSearch');
        const operatorFilter = document.getElementById('operatorFilter');
        const rows = Array.from(document.querySelectorAll('#warningTable tbody tr[data-row-index]'));
        const addNewBtn = document.getElementById('addNewBtn');
        const warningModalEl = document.getElementById('warningModal');
        const warningModal = new bootstrap.Modal(warningModalEl);
        const modalTitle = document.getElementById('warningModalTitle');
        const formMode = document.getElementById('formMode');
        const formCode = document.getElementById('formCode');
        const formErrorMessage = document.getElementById('formErrorMessage');
        const formOperatorInduced = document.getElementById('formOperatorInduced');
        const formColor = document.getElementById('formColor');
        const formLinkedSubError = document.getElementById('formLinkedSubError');
        const warningForm = document.getElementById('warningForm');

        const linkedSubTom = new TomSelect('#formLinkedSubError', {
            plugins: ['remove_button'],
            create: false,
            persist: false,
            hideSelected: true,
            closeAfterSelect: false,
            maxOptions: 5000
        });

        function parseLinkedSubError(value) {
            if (!value) return [];
            return String(value)
                .split(';')
                .map(function (v) { return v.trim(); })
                .filter(Boolean);
        }

        function setMultiValues(values) {
            linkedSubTom.clear(true);
            (values || []).forEach(function (value) {
                const target = String(value).trim();
                if (!target) return;

                if (linkedSubTom.options[target]) {
                    linkedSubTom.addItem(target, true);
                    return;
                }

                const matched = Object.keys(linkedSubTom.options).find(function (key) {
                    const option = linkedSubTom.options[key];
                    return option && typeof option.text === 'string' && option.text.indexOf(target) !== -1;
                });

                if (matched) {
                    linkedSubTom.addItem(matched, true);
                }
            });
        }

        function applyFilters() {
            const query = (searchInput.value || '').toLowerCase().trim();
            const operatorValue = operatorFilter.value;

            rows.forEach(function (row) {
                const code = row.getAttribute('data-code') || '';
                const message = row.getAttribute('data-message') || '';
                const linked = row.getAttribute('data-linked') || '';
                const operator = row.getAttribute('data-operator') || '';

                const textMatch = !query || code.includes(query) || message.includes(query) || linked.includes(query);
                const operatorMatch = !operatorValue || operator === operatorValue;

                row.style.display = textMatch && operatorMatch ? '' : 'none';
            });
        }

        searchInput.addEventListener('input', applyFilters);
        operatorFilter.addEventListener('change', applyFilters);

        function resetForm() {
            formMode.value = 'create';
            modalTitle.textContent = 'Add Warning Code';
            warningForm.action = "{{ route('caterpillar.message-ref.store') }}";
            formCode.value = '';
            formErrorMessage.value = '';
            formOperatorInduced.value = '0';
            formColor.value = '';
            setMultiValues([]);
            formCode.removeAttribute('readonly');
        }

        addNewBtn.addEventListener('click', function () {
            resetForm();
            warningModal.show();
        });

        document.querySelectorAll('.edit-row-btn').forEach(function (button) {
            button.addEventListener('click', function () {
                const row = JSON.parse(this.getAttribute('data-row'));
                formMode.value = 'edit';
                modalTitle.textContent = 'Edit Warning Code';
                warningForm.action = "{{ route('caterpillar.message-ref.update') }}";
                formCode.value = row.CODE || '';
                formErrorMessage.value = row.ERROR_MESSAGE || '';
                formOperatorInduced.value = String(row.OPERATOR_INDUCED ?? 0);
                formColor.value = row.GCOLOR || '';
                setMultiValues(parseLinkedSubError(row.LINKED_SUB_ERROR || ''));
                formCode.setAttribute('readonly', 'readonly');
                warningModal.show();
            });
        });
    });
</script>

@include('layout.footer')
