@include('layout.head')
@include('layout.sidebar')

<style>
    .ref-parameter-page .table {
        font-size: 12px;
        white-space: nowrap;
    }

    .ref-parameter-page .table th {
        font-size: 11px;
        font-weight: 700;
        vertical-align: middle;
    }

    .ref-parameter-page .table td {
        vertical-align: middle;
    }

    .ref-parameter-page .group-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 90px;
        padding: 5px 9px;
        border-radius: 999px;
        background: #eef4ff;
        color: #315f9f;
        font-size: 11px;
        font-weight: 700;
    }

    .ref-parameter-page .threshold-normal {
        color: #15803d;
        font-weight: 600;
    }

    .ref-parameter-page .threshold-caution {
        color: #d97706;
        font-weight: 600;
    }

    .ref-parameter-page .modal-label {
        font-size: 12px;
        font-weight: 600;
    }
</style>

<div class="page-content ref-parameter-page">
    <div class="container-xxl">
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row align-items-center">
                            <div class="col">
                                <h4 class="card-title">Komatsu Machine Health Parameter Reference</h4>
                            </div>
                        </div>
                    </div>

                    <div class="card-body pt-0">
                        <div class="table-responsive">
                            <table class="table datatable align-middle" id="datatable_1">
                                <thead class="table-light">
                                    <tr>
                                        <th>Item</th>
                                        <th>Unit</th>
                                        <th>Group</th>
                                        <th>Description</th>
                                        <th>Remark</th>
                                        <th>Portion</th>
                                        <th>Normal Min</th>
                                        <th>Normal Max</th>
                                        <th>Caution Min</th>
                                        <th>Caution Max</th>
                                        <th>Convert</th>
                                        <th>Portion Normal</th>
                                        <th>Portion Caution</th>
                                        <th>Portion Critical</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($data as $item)
                                        <tr>
                                            <td class="fw-semibold">{{ $item->ITEM }}</td>
                                            <td>{{ $item->UNIT }}</td>
                                            <td>
                                                <span class="group-badge">{{ $item->GROUP_ITEM }}</span>
                                            </td>
                                            <td>{{ $item->DESCRIPTION }}</td>
                                            <td>{{ $item->REMARK }}</td>
                                            <td>{{ number_format((float) $item->PORTION, 2) }}</td>
                                            <td class="threshold-normal">{{ number_format((float) $item->MIN_THRESHOLD_NORMAL, 2) }}</td>
                                            <td class="threshold-normal">{{ number_format((float) $item->MAX_THRESHOLD_NORMAL, 2) }}</td>
                                            <td class="threshold-caution">{{ number_format((float) $item->MIN_THRESHOLD_CAUTION, 2) }}</td>
                                            <td class="threshold-caution">{{ number_format((float) $item->MAX_THRESHOLD_CAUTION, 2) }}</td>
                                            <td>{{ $item->CONVERT_VALUE }}</td>
                                            <td>{{ number_format((float) $item->PORTION_NORMAL, 2) }}</td>
                                            <td>{{ number_format((float) $item->PORTION_CAUTION, 2) }}</td>
                                            <td>{{ number_format((float) $item->PORTION_CRITICAL, 2) }}</td>
                                            <td>
                                                <button type="button"
                                                    class="btn btn-outline-primary btn-sm edit-row-btn"
                                                    data-item="{{ $item->ITEM }}"
                                                    data-unit="{{ $item->UNIT }}"
                                                    data-group-item="{{ $item->GROUP_ITEM }}"
                                                    data-description="{{ $item->DESCRIPTION }}"
                                                    data-remark="{{ $item->REMARK }}"
                                                    data-portion="{{ $item->PORTION }}"
                                                    data-min-normal="{{ $item->MIN_THRESHOLD_NORMAL }}"
                                                    data-max-normal="{{ $item->MAX_THRESHOLD_NORMAL }}"
                                                    data-min-caution="{{ $item->MIN_THRESHOLD_CAUTION }}"
                                                    data-max-caution="{{ $item->MAX_THRESHOLD_CAUTION }}"
                                                    data-convert-value="{{ $item->CONVERT_VALUE }}"
                                                    data-portion-normal="{{ $item->PORTION_NORMAL }}"
                                                    data-portion-caution="{{ $item->PORTION_CAUTION }}"
                                                    data-portion-critical="{{ $item->PORTION_CRITICAL }}">
                                                    Edit
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="parameterModal" tabindex="-1" aria-labelledby="parameterModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h5 class="modal-title" id="parameterModalTitle">Edit Machine Health Parameter</h5>
                    <div class="text-muted small">Update parameter, threshold dan portion.</div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form method="POST" action="{{ route('komatsu.ref-mh-parameter.update') }}">
                @csrf
                <input type="hidden" name="OLD_ITEM" id="formOldItem">

                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label modal-label">Item</label>
                            <input type="text" class="form-control" name="ITEM" id="formItem" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label modal-label">Unit</label>
                            <input type="text" class="form-control" name="UNIT" id="formUnit">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label modal-label">Group Item</label>
                            <input type="text" class="form-control" name="GROUP_ITEM" id="formGroupItem">
                        </div>

                        <div class="col-md-8">
                            <label class="form-label modal-label">Description</label>
                            <input type="text" class="form-control" name="DESCRIPTION" id="formDescription">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label modal-label">Remark</label>
                            <input type="text" class="form-control" name="REMARK" id="formRemark">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label modal-label">Portion</label>
                            <input type="number" step="any" class="form-control" name="PORTION" id="formPortion">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label modal-label">Convert Value</label>
                            <input type="number" step="any" class="form-control" name="CONVERT_VALUE" id="formConvertValue">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label modal-label">Normal Min</label>
                            <input type="number" step="any" class="form-control" name="MIN_THRESHOLD_NORMAL" id="formMinNormal">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label modal-label">Normal Max</label>
                            <input type="number" step="any" class="form-control" name="MAX_THRESHOLD_NORMAL" id="formMaxNormal">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label modal-label">Caution Min</label>
                            <input type="number" step="any" class="form-control" name="MIN_THRESHOLD_CAUTION" id="formMinCaution">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label modal-label">Caution Max</label>
                            <input type="number" step="any" class="form-control" name="MAX_THRESHOLD_CAUTION" id="formMaxCaution">
                        </div>

                        <div class="col-md-2">
                            <label class="form-label modal-label">Portion Normal</label>
                            <input type="number" step="any" class="form-control" name="PORTION_NORMAL" id="formPortionNormal">
                        </div>

                        <div class="col-md-2">
                            <label class="form-label modal-label">Portion Caution</label>
                            <input type="number" step="any" class="form-control" name="PORTION_CAUTION" id="formPortionCaution">
                        </div>

                        <div class="col-md-2">
                            <label class="form-label modal-label">Portion Critical</label>
                            <input type="number" step="any" class="form-control" name="PORTION_CRITICAL" id="formPortionCritical">
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
    const modalElement = document.getElementById('parameterModal');
    if (!modalElement) return;

    const editModal = bootstrap.Modal.getOrCreateInstance(modalElement);

    document.addEventListener('click', function (event) {
        const button = event.target.closest('.edit-row-btn');
        if (!button) return;

        document.getElementById('formOldItem').value = button.dataset.item || '';
        document.getElementById('formItem').value = button.dataset.item || '';
        document.getElementById('formUnit').value = button.dataset.unit || '';
        document.getElementById('formGroupItem').value = button.dataset.groupItem || '';
        document.getElementById('formDescription').value = button.dataset.description || '';
        document.getElementById('formRemark').value = button.dataset.remark || '';
        document.getElementById('formPortion').value = button.dataset.portion || '';
        document.getElementById('formMinNormal').value = button.dataset.minNormal || '';
        document.getElementById('formMaxNormal').value = button.dataset.maxNormal || '';
        document.getElementById('formMinCaution').value = button.dataset.minCaution || '';
        document.getElementById('formMaxCaution').value = button.dataset.maxCaution || '';
        document.getElementById('formConvertValue').value = button.dataset.convertValue || '';
        document.getElementById('formPortionNormal').value = button.dataset.portionNormal || '';
        document.getElementById('formPortionCaution').value = button.dataset.portionCaution || '';
        document.getElementById('formPortionCritical').value = button.dataset.portionCritical || '';

        editModal.show();
    });
});
</script>

@include('layout.footer')
