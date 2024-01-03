<script>
//    $( document ).ready(function() {
        App.Constants.BASE_URL = "{{ url('/') }}";
        App.Constants.API_HOST = "{{ url('/') }}";
        App.Constants.CSRF_TOKEN = "{{ csrf_token() }}";
        App.Constants.ENV = "{{config('app.env')}}"
        App.Constants.IMGIX_BASE_PATH = "{{config('app.IMGIX_BASE_PATH')}}"
//    });
</script>
