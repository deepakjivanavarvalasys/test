<html>
    <body>
        <form action="{{ route('extract_number') }}" method="POST">
            @csrf
            <label for="string_to_extract">Enter string to convert</label>
            <br>
            <textarea name="string_to_extract" id="string_to_extract" cols="100" rows="10"></textarea>
            <br><br>
            <input type="submit" value="Convert" name="submit">
        </form>

        @if(isset($result) && !empty($result))
        <div style="margin-top: 50px;width: 80%;word-wrap: break-word;">
            Count => {{ count($result) }}
        </div>
        <div style="margin-top: 20px;width: 80%;word-wrap: break-word;">
            {{ implode(',', $result) }}
        </div>
        @endif
    </body>
</html>
