@include('include/header')
@include('include/sidebar')
<link rel="stylesheet" href="{{ asset('assets/vendors/select2/select2.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/vendors/select2-bootstrap-theme/select2-bootstrap.min.css') }}">

<style>
    .add-field {
        display: flex;
        justify-content: end;
    }

    .add-field a {
        height: 36px;
        border-radius: 50px;
        line-height: 17px;
    }

    .hide {
        display: none;
    }

    .radius-50 {
        border-radius: 50px;
    }

    .page-title-main {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }

    span.select2.select2-container.select2-container--default {
        width: 100% !important;
    }

    .select2-container--default .select2-selection--multiple {
        border-radius: 0px !important;
        border: 1px solid #e3e7ed !important;
    }

    .selection .select2-selection {
        height: 40px;
    }

    .select2-container--default .select2-selection--single .select2-selection__clear {
        display: none;
    }
    
    #sortableTable {
        tbody tr:hover {
            cursor: grab;
        }
    }
</style>
<!-- Begin Page Content -->
<div class="main-panel">
    <div class="content-wrapper">
        <div class="page-title-main">
            <h5 class="mb-0 font-weight-bold">Form Group Management</h5>
            <div class="page-rightbtns">
                <div class="add-field">
                    @can('form-group-create')
                        <a class="btn btn-primary btn-rounded btn-fw btn-sm addModal"
                            href="{{ route('form-group.create') }}"><i class="mdi mdi-plus"> </i>Add
                            Form Group</a>
                    @endcan
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-body compact-view">
                <div class="row">
                    <input type="hidden" name="form_id" value="{{ $form_id }}" id="form_id">
                    <div class="col-12">
                        <div class="wmd-view-topscroll">
                            <div class="scroll-div1">
                            </div>
                        </div>
                        <div class="wmd-view">
                            <div class="scroll-div2">
                                <span id="resp"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
         </div>
    </div>
    <div class="row" style='margin-top: 25px;'>
        <pre id='toastrOptions'></pre>
    </div>
    @include('formGroup._partial.create')
    @include('formGroup._partial.view_modal')
    @include('include/footer')

    <script>
        var _CSRF_TOKEN = '{{ csrf_token() }}';
        var editData = "{{ route('form-group.edit', 'id') }}";
        var _DELETE_URL = "{{ route('form-group.destroy', 'id') }}";
        var updateFormGroupUrl = "{{ route('update-formGroup-order') }}";
        var _FORM_GROUP_LIST = "{{ url('form-group-list') }}";
    </script>
