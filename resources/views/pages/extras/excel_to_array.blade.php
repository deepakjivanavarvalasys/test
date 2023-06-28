<html>
<body>
<form method="post" action="{{ route('convertExcelToArray') }}" enctype="multipart/form-data">
    @csrf
    <input type="file" name="excel_file">
    <input type="submit" value="Convert">
</form>
</body>
</html>
