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

$publishedStories = $link->query("SELECT * FROM stories WHERE approved=1");
if(!$publishedStories){
    die ('Can\'t query stories because: ' . $link->error);
}

$num_approved = mysqli_num_rows($publishedStories);

?><html lang="en">
<head>
    <title>My News App</title>
    <link href="http://getbootstrap.com/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="http://getbootstrap.com/examples/jumbotron-narrow/jumbotron-narrow.css" rel="stylesheet">
    <link rel="stylesheet" href="css/reset.css">
    <link rel="stylesheet" href="css/style.css">
    <link href="../WebApp_News/css/main.css" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
    <script src="js/main.js"></script>
    <script>
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
            <div class="myContainer">
                <h3>Published Stories</h3>
                <?php
                    if ($num_approved > 0) 
                    {
                        while( $row = $publishedStories->fetch_assoc()){
                    ?>
                    <div class="story">
                        <h4><?php print($row["name"])?></h4>
                        <h5>by <?php print($row["submitter"])?> - <?php print($row["submitDate"])?></h5>
                        <p><?php print($row["storyText"])?></p>
                    </div>
                    <?php
                        }
                    }
                    ?>
            </div>
        </div>
        <footer class="footer">
            <p>&copy; Austin Graham and Kayla Holcomb 2016</p>
        </footer>
    </div>
</body>
</html>