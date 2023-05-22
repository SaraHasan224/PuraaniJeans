@section('scripts')
    <script>
        $(document).ready(function () {
            App.Users.initializeValidations();
            App.Users.initializeDataTable();
//            $('.select2').select2();
//            $(":input").inputmask();

            $("#passenger_change_password").click(function(){
                $("#password").removeAttr("readonly");
                $("#password").val("");
            })
        })
    </script>
@endsection
