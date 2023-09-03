<div class="row error_division">
    <!-- ERRORS -->
    @if(is_string($errors))
        <div class="col-md-12 alert alert-danger fade  {{ (Session::has('errors') ? 'show' : '') }}" role="alert">
            <b>Error(s)!</b><br>
            {{ $errors }}  <br>
        </div>
    @elseif($errors->any())
        <div class="col-md-12 alert alert-danger fade  {{ (Session::has('errors') ? 'show' : '') }}" role="alert">
            <b>Error(s)!</b><br>
            @foreach ($errors->all() as $error)
                <div>{{$error}}</div>
            @endforeach
        </div>
    @endif

    @if (Session::has('error'))
        <div class="col-md-12 alert alert-danger fade  {{ (Session::has('errors') ? 'show' : '') }}" role="alert">
            <b>Error(s)!</b><br>
            @foreach (Session::get('errors') as $error)
                {{ $error }}  <br>
            @endforeach
        </div>
    @endif
<!-- WARNINGS -->

    @if (Session::has('warning_msg'))
        <div class="col-md-12 alert alert-warning fade show" role="alert">
            <b>Warning:</b>
            {{ Session::get('warning_msg') }}
        </div>
    @endif

<!-- INFO -->

    @if (Session::has('info_msg'))
        <div class="col-md-12 alert alert-primary fade show" role="alert">
            <b>Info:</b>
            {{ Session::get('info_msg') }}
        </div>
    @endif

<!-- SUCCESS -->
    @if (Session::has('success'))
        <div class="col-md-12 alert alert-success fade show" role="alert">
            <b>Success:</b>

            {!! Session::get('success') !!}
        </div>
    @endif

    <div class="col-md-12">
        <div class="alert alert-success" style="display: none;">
            <span class="message"></span>
        </div>
        <div class="alert alert-danger" style="display: none;">
            <span class="message"></span>
        </div>
    </div>
    <div class="col-md-12">
        <div class="alert" id="activation_msg_div" style="display: none;">
            <span id="activation_res_message"></span>
        </div>
    </div>
</div>