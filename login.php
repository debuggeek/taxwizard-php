<?php

require_once 'autoload.php';
use PHPAuth\Config;
use PHPAuth\Auth;

if (!empty($_POST)):
    $dbh = new PDO("mysql:host=localhost;dbname=phpauth", "root", "root");

    $config = new Config($dbh);
    $auth   = new Auth($dbh, $config);

    if($auth->login($_POST["username"],$_POST["password"])): ?>
        Welcome, <?php echo htmlspecialchars($_POST["username"]); ?>!<br>
        Your email is <?php echo htmlspecialchars($_POST["username"]); ?>.<br>
    <?php else: ?>
        Bad login
    <?php endif; ?>
<?php else: ?>
<form id='login' action='login.php' method='post' accept-charset='UTF-8'>
    <fieldset >
    <legend>Login</legend>
    <input type='hidden' name='submitted' id='submitted' value='1'/>

    <label for='username' >UserName*:</label>
    <input type='text' name='username' id='username'  maxlength="50" />

    <label for='password' >Password*:</label>
    <input type='password' name='password' id='password' maxlength="50" />

    <input type='submit' name='Submit' value='Submit' />

    </fieldset>
</form>
<?php endif; ?>