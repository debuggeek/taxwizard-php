<html>
<head>
    <title>PHP v8js Example</title>
</head>
<body>
<?php
$v8 = new V8Js();
echo $v8->executeString('
      var hello = "Hello ";
      var jsworld = "Javascript World";
      hello + jsworld;
    ');
?>
</body>
</html>

