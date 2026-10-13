@if(session('success'))
<script>
document.addEventListener('DOMContentLoaded', function () {
    Swal.fire({
        title: 'Berhasil',
        text: @json(session('success')),
        icon: 'success',
        confirmButtonText: 'OK',
        confirmButtonColor: '#3085d6'
    });
});
</script>
@endif


@if(session('info'))
<script>
document.addEventListener('DOMContentLoaded', function () {
    Swal.fire({
        title: 'Gagal',
        text: @json(session('info')),
        icon: 'info',
        confirmButtonText: 'OK',
        confirmButtonColor: '#d33'
    });
});
</script>
@endif
