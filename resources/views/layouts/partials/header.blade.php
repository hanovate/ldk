<!-- Header -->
<div id="header" class="row">
    <div class="col-10">
        <img id="header-logo" src="img/unm-mono-white.svg" />
        <span id="header-text">{{config('unm-it-app.title.name')}}</span>

    </div>
    <div class="col-2">
        <div id="current-user">
            <i class="fa fa-user-circle-o"></i><span>{{-- cas()->user() --}}</span>
        </div>
    </div>
</div>
<!-- END Header -->
