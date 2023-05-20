@section('js')
    <script>
        $(document).ready(function () {
            App.Users.initializeValidations();
            App.Users.initializeDataTable();
            App.Users.createUserFormBinding();
            App.Users.editUserFormBinding();
            $('#daterange').daterangepicker({maxDate: new Date()});
            $('#daterange').val('');
            $('.select2').select2({
              // width: '100%'
            });
          $(":input").inputmask();
          App.Helpers.getPhoneInput('create_phone', 'create_country_code', false)
          App.Helpers.getPhoneInput('edit_phone', 'edit_country_code', true);

            var select2Element = $('#js-select2');

            select2Element.select2({
                closeOnSelect : false,
                placeholder : "Select",
                allowHtml: true,
                allowClear: true,
                tags: true,
                theme: 'allColSelect2Container',
                /*containerCssClass: "custom-container",
                dropdownCssClass: "custom-dropdown",*/
            });


            $(".filterLink").click(function(){
                $(".filterDropDownMenu").toggleClass("open");
            });


            $('#merchant_stores').select2({
                allowClear: true,
                closeOnSelect: false,
                minimumResultsForSearch: -1,
            });

        });
    </script>
@endsection
