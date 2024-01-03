@section('scripts')
    <script>
        $(document).ready(function () {
            App.Closet.initializeValidations();
            App.Closet.initializeDataTable();
            $(":input").inputmask();
        })
    </script>
@endsection
