<div class="{{$viewClass['form-group']}}">
    <label class="{{$viewClass['label']}} control-label">{{$label}}</label>
    <div class="{{$viewClass['field']}}">
        <div class="card card-outline card-secondary no-margin">
            <!-- /.card-header -->
            <div class="card-body">
                {!! $value !!}&nbsp;
            </div><!-- /.card-body -->
        </div>

        @include('admin::form.help-block')

    </div>
</div>