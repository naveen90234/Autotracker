<!-- ===================  Theme Script ==============================  --->
<script src="{{ asset('assets/common/vendors/base/vendor.bundle.base.js') }}"></script>
<script src="{{ asset('assets/common/js/off-canvas.js') }}"></script>
<script src="{{ asset('assets/common/js/hoverable-collapse.js') }}"></script>

<script src="{{ asset('assets/common/js/template.js') }}"></script>
<script src="{{ asset('assets/common/vendors/chart.js/Chart.min.js') }}"></script>
<script src="{{ asset('assets/common/vendors/jquery-bar-rating/jquery.barrating.min.js') }}"></script>
<script src="{{ asset('assets/common/js/dashboard.js') }}"></script>

<!--  =========================  New Script ==============================  -->
<script src="{{ asset('assets/common/js/plugins/bootstrap-datepicker.min.js') }}"></script>
<script src="{{ asset('assets/common/js/plugins/pace.min.js') }}"></script>
<script src="{{ asset('assets/common/js/plugins/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('assets/common/js/plugins/dataTables.bootstrap.min.js') }}"></script>
<script src="{{ asset('assets/common/js/loadingoverlay.min.js') }}"></script>
<script src="{{ asset('assets/common/js/sweetalert.min.js') }}"></script>
<script src="{{ asset('assets/common/js/custom.js') }}"></script>
<script src="{{ asset('assets/common/js/file-upload.js') }}"></script>
<script src="{{ asset('assets/common/js/plugins/jquery.form.min.js') }}"></script>

<script src="{{ asset('assets/common/datatables/js/dataTables.buttons.min.js') }}"></script>
<script src="{{ asset('assets/common/datatables/js/buttons.html5.min.js') }}"></script>
<script src="{{ asset('assets/common/datatables/js/jszip.min.js') }}"></script>
<script src="{{ asset('assets/common/datatables/js/pdfmake.min.js') }}"></script>
<script src="{{ asset('assets/common/datatables/js/vfs_fonts.js') }}"></script>

<script src="{{ asset('assets/common/vendors/select2/select2.min.js') }}"></script>

<script>
    function handleAjaxError(xhr, textStatus, errorThrown) {
        console.log(xhr.status);
        if (xhr.status === 401 || xhr.status === 419) {
            alert("Please refresh the page.");
        } else {
            // console.error("AJAX error:", textStatus, errorThrown);
        }
    }
</script>
@stack('scripts')
