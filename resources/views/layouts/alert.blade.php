<div id="div-alert" class="row">
    @if(session('success'))
        <div class="col-md-12 alert alert-success alert-dismissible fade show alert-auto-dismiss" role="alert">
            <strong>Success!</strong> {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
        </div>
    @endif
    @if(session('error'))
        <div class="col-md-12 alert alert-danger alert-dismissible fade show alert-auto-dismiss" role="alert">
            <strong>Error!</strong> {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
        </div>
    @endif
    @if(session('info'))
        <div class="col-md-12 alert alert-info alert-dismissible fade show alert-auto-dismiss" role="alert">
            <strong>Info:</strong> {{ session('info') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
        </div>
    @endif
    @if(session('warning'))
        <div class="col-md-12 alert alert-warning alert-dismissible fade show alert-auto-dismiss" role="alert">
            <strong>Warning:</strong> {{ session('warning') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
        </div>
    @endif
</div>
