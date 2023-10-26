$("select").select2({ theme: "bootstrap-5" });
$('input[name="daterange"]').daterangepicker({
    locale: { format: 'DD/M/Y' },
    autoUpdateInput: false, 
});

var daterang    = $('#daterange').val();
var search      = $('#searchInput').val();
var statusID    = $('#idStatus').val();
var paymentID   = $('#idPayment').val();

function ajaxRequest(params) {
    params.data.daterang    = daterang;
    params.data.search      = search;
    params.data.status      = statusID;
    params.data.payment     = paymentID;
    params.data.group_id    = group_id;
    $.get('api/backoffice/orderLists' + '?' + $.param(params.data)).then(function(res) {
        // console.log(JSON.parse(res));
        params.success(res);
    })
}

function setSearch() {
    daterang    = $('#daterange').val();
    search      = $('#searchInput').val();
    statusID    = $('#idStatus').val();
    paymentID   = $('#idPayment').val();
}

$('body').on('click', '#btn-filter', function () {
    setSearch()
    $('#orderTable').bootstrapTable('refresh');
})

$('input[name="daterange"]').on('apply.daterangepicker', function(ev, picker) {
    $(this).val(picker.startDate.format('DD/MM/YYYY') + ' - ' + picker.endDate.format('DD/MM/YYYY'));
});