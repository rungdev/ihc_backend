@extends('Layouts.main_layout')

@push('css')
    <link rel="stylesheet" href="//code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
    <link rel="stylesheet" href="https://unpkg.com/bootstrap-table@1.22.1/dist/bootstrap-table.min.css">
    <link rel="stylesheet" href="{{asset('assets/css/views/Shelf/index.css')}}">
@endpush

@section('content')
<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">
            <!-- start page title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0">ชั้นวางสินค้า</h4>
                    </div>
                </div>
            </div>
            <!-- end page title -->

            <!-- Grids in modals -->
            <div class="row">
                <div class="col-lg-12">
                    <div class="card" id="orderList">
                        <div class="card-header border border-dashed border-end-0 border-start-0 border-top-0">
                            <div class="row align-items-center gy-3">
                                <div class="col-sm">
                                    <h5 class="card-title mb-0">ชั้นวางสินค้า</h5>
                                </div>
                            </div>
                        </div>
                        <div class="card-body pt-4">
                            <div class="row border border-dashed border-end-0 border-start-0 border-top-0 pb-2 mb-2">
                                <div class="col-3">
                                    <label class="form-label" for="name_th">ชื่อชั้นวาง (TH) <span class="text-red">*</span></label>
                                    <input type="text" class="form-control" name="name_th" id="name_th" value="" placeholder="ชื่อชั้นวาง (TH)" required>
                                </div>
                                <div class="col-3">
                                    <label class="form-label" for="name_th">ชื่อชั้นวาง (EN) <span class="text-red">*</span></label>
                                    <input type="text" class="form-control" name="name_th" id="name_th" value="" placeholder="ชื่อชั้นวาง (EN)" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-6">
                                    <input id="city">
  Powered by <a href="http://geonames.org">geonames.org</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script')
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>
    <script src="assets/js/views/Product/supplier.js?v={{time()}}"></script>
    <script>
    var countries = [
        { value: 'Andorra', data: 'AD' },
        { value: 'Zimbabwe', data: 'ZZ' }
    ];

    $( "#city" ).autocomplete({
      source: function( request, response ) {
        $.ajax({
          url: "http://gd.geobytes.com/AutoCompleteCity",
          dataType: "jsonp",
          data: {
            q: request.term
          },
          success: function( data ) {
            response( data );
          }
        });
      },
      minLength: 3,
      select: function( event, ui ) {
        log( ui.item ?
          "Selected: " + ui.item.label :
          "Nothing selected, input was " + this.value);
      },
      open: function() {
        $( this ).removeClass( "ui-corner-all" ).addClass( "ui-corner-top" );
      },
      close: function() {
        $( this ).removeClass( "ui-corner-top" ).addClass( "ui-corner-all" );
      }
    });
    </script>
@endpush


