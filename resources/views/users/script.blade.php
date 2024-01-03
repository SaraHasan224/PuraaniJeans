@section('scripts')
    <script>
        $(document).ready(function () {
            App.Users.initializeValidations();
            App.Users.initializeDataTable();
            $(":input").inputmask();
        })
    </script>
@endsection
