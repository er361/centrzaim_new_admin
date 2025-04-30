<html>
<head>
    <title>Платеж принят</title>
</head>
<body>
<script>
    window.onload = function () {
        parent.postMessage('paymentFinished', '*');
    }
</script>
</body>
</html>
