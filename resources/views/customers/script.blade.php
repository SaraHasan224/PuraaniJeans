@section('scripts')
    <script>
        $(document).ready(function () {
            App.Customer.initializeValidations();
            App.Customer.initializeDataTable();
            $(":input").inputmask();
        })
    </script>
@endsection
