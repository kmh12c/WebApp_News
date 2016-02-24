<?php

    $loggedIn=false;
    if(isset($_COOKIE["NewsAppAccess"]))
    {
        $name = $_COOKIE["NewsAppAccess"];
        $cryptedCookie = $_COOKIE[$name];
        $cryptedName = crypt($name,"itsrainingtacos");
        if($cryptedCookie == $cryptedName)
            $loggedIn = true;
    }

    if($loggedIn){
        header('Location: dashboard.php');
    }

    /**************************
    *
    * Database Connections
    *
    ***************************/
    $link = new mysqli("localhost","root","","newsAppDB");

    if ($link->connect_errno) {
        printf("Connect failed: %s\n", $link->connect_error);
        exit();
    }

    /*setcookie("NewsAppAccess","",time()-3600);*/
    /**************************
    *
    * Database interactions
    *
    ***************************/

    if(isset($_REQUEST["action"]))
        $action = $_REQUEST["action"];
    else
        $action = "none";

    $message = "";

    if($action == "add_user")
    {
        $fname = $_POST["fname"];
        $lname = $_POST["lname"];
        $email = $_POST["email"];
        $password = $_POST["password"];
        
        $fname = htmlentities($link->real_escape_string($fname));
        $lname = htmlentities($link->real_escape_string($lname));
        $email = htmlentities($link->real_escape_string($email));
        $password = htmlentities($link->real_escape_string($password));
        $password = crypt ($password,"itsrainingtacos");
        $result = $link->query("INSERT INTO users (firstName,lastName,email,phrase,admin) VALUES ('$fname', '$lname', '$email', '$password', 0)");

        $cookieValue = crypt($email,"itsrainingtacos");
        setcookie("NewsAppAccess", $email, time()+180);  /* expire in 1 hour 3600*/
        setcookie($email, $cookieValue, time()+180);  /* expire in 1 hour */
        $loggedIn = true;

        if(!$result)
            die ('Can\'t add user because: ' . $link->error);
        else{
            header('Location: dashboard.php');
        }
    }
    elseif ($action == "login") {
        $email = $_POST["email"];
        $password = $_POST["password"];
        
        $email = htmlentities($link->real_escape_string($email));
        $password = htmlentities($link->real_escape_string($password));
        
        $password = crypt ($password,"itsrainingtacos");
        
        $result = $link->query("SELECT * FROM users WHERE email='$email'");
        if(!$result)
            die ('Can\'t query users because: ' . $link->error);

        $num_rows = mysqli_num_rows($result);
        print($num_rows);
        if ($num_rows > 0) 
        {
          $row = $result->fetch_assoc();
          if($row["phrase"] == $password)
          {
            $cookieValue = crypt($email,"itsrainingtacos");
            setcookie("NewsAppAccess", $email, time()+180);  /* expire in 1 hour 3600*/
            setcookie($email, $cookieValue, time()+180);  /* expire in 1 hour */
            $loggedIn = true;
            header('Location: dashboard.php');
          }
          else
            $message = "Password for user $email incorrect!";
        }
        else {
          // do something else
          $message = "No user $email found!";
        }
    }
?>
<html lang="en">
<head>
    <title>My News App</title>
    <link href="http://getbootstrap.com/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="http://getbootstrap.com/examples/jumbotron-narrow/jumbotron-narrow.css" rel="stylesheet">
    <link rel="stylesheet" href="css/reset.css">
    <link rel="stylesheet" href="css/style.css">
    <link href="../WebApp_News/css/frontpg.css" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
    <script src="js/main.js"></script>
    <script>

        $(document).bind('keypress', function(e) { 
                if(e.keyCode == 13) {e.preventDefault();}
        });

        function validateSignUp() {
            var a = document.forms["signUp"]["fname"].value;
            if (a == null || a == "") {
                alert("First name must be filled out");
                return false;
            }

            var b = document.forms["signUp"]["lname"].value;
            if (b == null || b == "") {
                alert("Last name must be filled out");
                return false;
            }

            var c = document.forms["signUp"]["email"].value;
            if (c == null || c == "" || !c.includes("@")) {
                alert("Please enter a valid email address.");
                return false;
            }

            var d = document.forms["signUp"]["password"].value;
            if (d == null || d == "") {
                alert("Please enter a password");
                return false;
            }

            var e = document.forms["signUp"]["password2"].value;
            if (e == null || e == "" || e != d) {
                alert("Please make sure your passwords match");
                return false;
            }
        }

        function validateSignIn() {
        
            var c = document.forms["signIn"]["email"].value;
            if (c == null || c == "" || !c.includes("@")) {
                alert("Please enter a valid email address.");
                return false;
            }

            var d = document.forms["signIn"]["password"].value;
            if (d == null || d == "") {
                alert("Please enter a password");
                return false;
            }
        }
    </script>
</head>
<body>
    <div class="myContainer">
        <div class="header">
            <nav>
                <ul class="main-nav nav nav-pills pull-right">
                    <li role="presentation"><a class="cd-signin" href="#0"> Log In or Join Us!</a></li>

                </ul>
            </nav>
            <h1 class="text-muted"><span class="glyphicon glyphicon-globe"></span> My News App</h1>
        </div>
        <div class="main">
            <div class="container">
                <div class="text main-nav">
                    <h1>World's Best News App!*</h1> 
                    <h6>*Voted on by such a small percentage of the population that it doesn't even count.</h6>
                    <p class="lead "></p>
                    <p><a class="btn btn-lg btn-success cd-signin" href="#0" role="button">Sign Up Today!</a></p>
                </div>
            </div>
        </div>

        <!-- https://codyhouse.co/redirect/?resource=login-signup-modal-window -->
        <div class="cd-user-modal"> <!-- this is the entire modal form, including the background -->
            <div class="cd-user-modal-container"> <!-- this is the container wrapper -->
                <ul class="cd-switcher">
                    <li><a href="#0">Sign In</a></li>
                    <li><a href="#0">New Account</a></li>
                </ul>
                <div id="cd-login"> <!-- log in form -->
                    <form class="cd-form" name="signIn" onsubmit="return validateSignIn()" method="post" action="#">
                        <p class="fieldset"><input class="full-width has-padding has-border" id="email" name="email" type="email" placeholder="E-mail"></p>
                        <p class="fieldset"><input class="full-width has-padding has-border" id="password" name="password" type="password"  placeholder="Password"></p>
                        <p class="fieldset"><input class="full-width" type="submit" value="Login"></p>
                        <input type="hidden" name="action" value="login">
                    </form>
                </div>
                <div id="cd-signup"> <!-- sign up form -->
                    <form class="cd-form" name="signUp" onsubmit="return validateSignUp()" method="post" action="#">
                        <p class="fieldset"><input class="full-width has-padding has-border" id="fname" name="fname" type="text" placeholder="First Name"></p>
                        <p class="fieldset"><input class="full-width has-padding has-border" id="lname" name="lname" type="text" placeholder="Last Name"></p>
                        <p class="fieldset"><input class="full-width has-padding has-border" id="email" name="email" type="email" placeholder="E-mail"></p>
                        <p class="fieldset"><input class="full-width has-padding has-border" id="password" name="password" type="password"  placeholder="Password"></p>
                        <p class="fieldset"><input class="full-width has-padding has-border" id="password2" name="password2" type="password"  placeholder="Retype Password"></p>
                        <p class="fieldset"><input class="full-width has-padding" type="submit" value="Create Account"></p>
                        <input type="hidden" name="action" value="add_user">
                    </form>
                </div>
            </div> <!-- cd-user-modal-container -->
        </div> <!-- cd-user-modal -->

        <div class="row marketing">
            <div class="col-lg-6">
                <h4>Sample Story</h4>
                <p>Donec id elit non mi porta gravida at eget metus. Maecenas faucibus mollis interdum.</p>
                <h4>Sample Story</h4>
                <p>Morbi leo risus, porta ac consectetur ac, vestibulum at eros. Cras mattis consectetur purus sit amet fermentum.</p>
                <h4>Sample Story</h4>
                <p>Maecenas sed diam eget risus varius blandit sit amet non magna.</p>
            </div>
            <div class="col-lg-6">
                <h4>Sample Story</h4>
                <p>Donec id elit non mi porta gravida at eget metus. Maecenas faucibus mollis interdum.</p>
                <h4>Sample Story</h4>
                <p>Morbi leo risus, porta ac consectetur ac, vestibulum at eros. Cras mattis consectetur purus sit amet fermentum.</p>
                <h4>Sample Story</h4>
                <p>Maecenas sed diam eget risus varius blandit sit amet non magna.</p>
            </div>
        </div>
        <footer class="footer">
            <p>&copy; Austin Graham and Kayla Holcomb 2016</p>
        </footer>
    </div>
</body>
</html>