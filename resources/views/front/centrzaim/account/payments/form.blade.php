<html>
<head>
    <title>Оплачиваем...</title>
</head>
<body>
<form action="{{ $url }}" method="{{ $method }}" name="paymentForm">
    @foreach($fields as $fieldName => $fieldValue)
        <input type="hidden" name="{{ $fieldName }}" value="{{ $fieldValue }}"/>
    @endforeach
</form>

<script>
    window.onload = function () {
        document.paymentForm.submit();
    }
</script>
</body>
</html>
