<form action="{{ url('test-record-form')}}" enctype="multipart/form-data" method="post">
    <input type="hidden" name="_token" value="{{ csrf_token() }}">

    <input type="file" name="images">
    <input type="submit" name="submit">

</form>