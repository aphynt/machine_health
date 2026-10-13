@include('layout.head')
@include('layout.sidebar')

<style>
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
        color: #eb5a06;
        background: #f0eae2;
    }
</style>

<div class="page-content ref-warning-page">
    <div class="container-xxl">
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row align-items-center">
                            <div class="col">
                                <h4 class="card-title">Komatsu SN Code Reference</h4>
                            </div>
                        </div>
                    </div>

                    <div class="card-body pt-0">
                        <div class="table-responsive">
                            <table class="table datatable" id="datatable_1">
                                <thead class="table-light">
                                    <tr>
                                        <th>SN Code</th>
                                        <th>IP Address</th>
                                        <th>Equipment ID</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($data as $item)
                                        <tr>
                                            <td>{{ $item->SN_CODE }}</td>
                                            <td>{{ $item->IP_ADDRESS }}</td>
                                            <td>{{ $item->VHC_ID }}</td>
                                            <td>
                                                @if ($item->STATUS == 1)
                                                    <span class="badge-yes">Checked</span>
                                                @else
                                                    <span class="badge-no">Need to be Checked</span>
                                                @endif
                                            </td>
                                            <td>
                                                <button type="button"
                                                    class="btn btn-outline-primary btn-sm edit-row-btn"
                                                    data-sn-code="{{ $item->SN_CODE }}"
                                                    data-ip-address="{{ $item->IP_ADDRESS }}"
                                                    data-vhc-id="{{ $item->VHC_ID }}"
                                                    data-status="{{ $item->STATUS }}">
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

<div class="modal fade" id="warningModal" tabindex="-1" aria-labelledby="warningModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h5 class="modal-title" id="warningModalTitle">Edit Komatsu SN Code</h5>
                    <div class="text-muted small">Update SN Code reference data.</div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form id="warningForm" method="POST" action="{{ route('komatsu.sn-ref.update') }}">
                @csrf
                <input type="hidden" name="OLD_SN_CODE" id="formOldSnCode">

                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">SN Code</label>
                            <input type="text" class="form-control" name="SN_CODE" id="formSnCode" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">IP Address</label>
                            <input type="text" class="form-control" name="IP_ADDRESS" id="formIpAddress">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Equipment ID</label>
                            <input type="text" class="form-control" name="VHC_ID" id="formVhcId">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Status</label>
                            <select class="form-select" name="STATUS" id="formStatus">
                                <option value="0">Need to be Checked</option>
                                <option value="1">Checked</option>
                            </select>
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
    const modalElement = document.getElementById('warningModal');
    if (!modalElement) return;

    const editModal = bootstrap.Modal.getOrCreateInstance(modalElement);
    const formOldSnCode = document.getElementById('formOldSnCode');
    const formSnCode = document.getElementById('formSnCode');
    const formIpAddress = document.getElementById('formIpAddress');
    const formVhcId = document.getElementById('formVhcId');
    const formStatus = document.getElementById('formStatus');

    document.addEventListener('click', function (event) {
        const button = event.target.closest('.edit-row-btn');
        if (!button) return;

        formOldSnCode.value = button.dataset.snCode || '';
        formSnCode.value = button.dataset.snCode || '';
        formIpAddress.value = button.dataset.ipAddress || '';
        formVhcId.value = button.dataset.vhcId || '';
        formStatus.value = button.dataset.status || '0';

        editModal.show();
    });
});
</script>

@if(session('success'))
<script>
document.addEventListener('DOMContentLoaded', function () {
    Swal.fire({
        title: 'Berhasil',
        text: @json(session('success')),
        icon: 'success',
        confirmButtonText: 'OK'
    });
});
</script>
@endif

@if(session('error'))
<script>
document.addEventListener('DOMContentLoaded', function () {
    Swal.fire({
        title: 'Gagal',
        text: @json(session('error')),
        icon: 'error',
        confirmButtonText: 'OK'
    });
});
</script>
@endif

@include('layout.footer')
