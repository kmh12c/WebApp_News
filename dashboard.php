<?php

$loggedIn=false;
if(isset($_COOKIE["NewsAppAccess"]))
{
    $name = $_COOKIE["NewsAppAccess"];
    $cryptedCookie = $_COOKIE["Validate"];
    $cryptedName = crypt($name,"itsrainingtacos");
    if($cryptedCookie == $cryptedName)
        $loggedIn = true;
}

if(!$loggedIn){
    header('Location: index.php');
}

/**************************
*IT'S RAINING TACOS!!!!!!!!!
*
*
***************************/

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


$action="";
if(isset($_POST["action"])){
    $action=$_POST["action"];
}

if($action == "add_story")
    {
        print("Adding");
        $title = $_POST["title"];
        $content = $_POST["content"];
        $email = $_COOKIE["NewsAppAccess"];
        
        $title = htmlentities($link->real_escape_string($title));
        $content = htmlentities($link->real_escape_string($content));
        $result = $link->query("INSERT INTO stories (name,storyText,submitter,approved) VALUES ('$title', '$content', '$email', 0)");

        if(!$result)
            die ('Can\'t add story because: ' . $link->error);
    }

?>
<html lang="en">
<head>
    <title>My News App</title>
    <link href="http://getbootstrap.com/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="http://getbootstrap.com/examples/jumbotron-narrow/jumbotron-narrow.css" rel="stylesheet">
    <link rel="stylesheet" href="../WebApp_News/css/reset.css">
    <link rel="stylesheet" href="../WebApp_News/css/style.css">
    <link href="../WebApp_News/css/dashboard.css" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
    <script src="js/main.js"></script>
    <script>
        $(document).bind('keypress', function(e) { 
                if(e.keyCode == 13) {e.preventDefault();}
        });
    </script>
</head>
<body>
    <div class="myContainer">
        <div class="header">
            <nav>
                <ul class="main-nav nav nav-pills pull-right">
                    <li role="presentation"><a class="cd-signin" href="index.php"> Sign Out</a></li>

                </ul>
            </nav>
            <a href="index.php"><h1 class="text-muted"><span class="glyphicon glyphicon-globe"></span> My News App</h1></a>
        </div>
        <div class="main">
            <h1>Story Manager</h1>
            <div class = "storyManager">
                <h3>Active Stories</h3>
                <table class="table table-hover storyTable">
                    <thead>
                      <tr>
                        <th>Name</th>
                        <th>Submitted By</th>
                        <th>Date</th>
                        <th>Approved By</th>
                        <th>Story</th>
                        <th>Deactivate</th>
                      </tr>
                    </thead>
                        <tr>
                            <td>Test</td>
                            <td>Test</td>
                            <td>Test</td>
                            <td>Test</td>
                            <td class="main-nav">
                                <a class="btn btn-xs btn-primary viewStory cd-signin" href="#0" role="button">View Story</a>
                            </td>
                            <td>
                                <form name="deactivate" action="#"><input type="submit" value="Deactivate" class="btn btn-xs btn-danger"/></form>
                            </td>
                        </tr>
                    <tbody>
                    </tbody>
                </table>

                <h3>Submitted Stories</h3>
                <table class="table table-hover storyTable">
                    <thead>
                      <tr>
                        <th>Name</th>
                        <th>Submitted By</th>
                        <th>Date</th>
                        <th>Story</th>
                        <th>Approve</th>
                      </tr>
                    </thead>
                        <tr>
                            <td>Test</td>
                            <td>Test</td>
                            <td>Test</td>
                            <td class="main-nav">
                                <a class="btn btn-xs btn-primary viewStory cd-signin" href="#0" role="button">View Story</a>
                            </td>
                            <td>
                                <form name="approve" action="#"><input type="submit" value="Approve" class="btn btn-xs btn-success"/></form>
                            </td>
                        </tr>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- https://codyhouse.co/redirect/?resource=login-signup-modal-window -->
        <div class="cd-user-modal"> <!-- this is the entire modal form, including the background -->
            <div class="cd-user-modal-container"> <!-- this is the container wrapper -->
                <div id="cd-login"> <!-- log in form -->
                    <form class="cd-form" method="post" action="#">
                        <p class="fieldset"><input class="full-width has-padding has-border" id="title" name="title" type="text" placeholder="Story Title"></p>
                        <p class="fieldset"><input class="full-width has-padding has-border content" id="content" name="content" type="text" placeholder="Your story goes here."></p>
                        <p class="fieldset"><input class="full-width" type="submit" value="Login"></p>
                    </form>
                </div>
            </div> <!-- cd-user-modal-container -->
        </div> <!-- cd-user-modal -->

        <footer class="footer">
            <p>&copy; Austin Graham and Kayla Holcomb 2016</p>
        </footer>
    </div>
</body>
</html>