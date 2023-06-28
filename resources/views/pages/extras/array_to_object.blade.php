<html>
    <body>
        <form action="{{ route('array_to_object') }}" method="POST">
            @csrf
            <label for="string_to_covert">Enter string to convert</label>
            <br>
            <textarea name="string_to_covert" id="string_to_covert" cols="100" rows="10"></textarea>
            <br><br>
            <input type="submit" value="Convert" name="submit">
        </form>

        @if(isset($result) && !empty($result))
        <div style="margin-top: 20px;width: 80%;word-wrap: break-word;">
            <button onclick="myFunction();" style="cursor: pointer;">Copy text</button>
            <br>
            <textarea cols="100" rows="100" id="myInput">{{ $result.';' }}</textarea>
        </div>
        @endif
    </body>
    <script>
        function myFunction() {
            /* Get the text field */
            var copyText = document.getElementById("myInput");

            /* Select the text field */
            copyText.select();
            copyText.setSelectionRange(0, 99999); /* For mobile devices */

            /* Copy the text inside the text field */
            navigator.clipboard.writeText(copyText.value);

            /* Alert the copied text */
            //alert("Copied the text: " + copyText.value);
        }
    </script>
</html>
