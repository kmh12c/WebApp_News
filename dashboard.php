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

$link = new mysqli("localhost","sMove","k4rensMov3rPr0gr@m2o15","newsAppDB");


if ($link->connect_errno) {
    printf("Connect failed: %s\n", $link->connect_error);
    exit();
}

$admin=false;
$email = $_COOKIE["NewsAppAccess"];
$isAdmin = $link->query("SELECT * FROM users WHERE email='$email'");
$num_rows = mysqli_num_rows($isAdmin);
 if ($num_rows > 0){
    $row = $isAdmin->fetch_assoc();
    if($row["admin"]==1){
        $admin=true;
    }
}

if($admin){
    $submittedStories = $link->query("SELECT * FROM stories WHERE approved=0");
}else{
    $submittedStories = $link->query("SELECT * FROM stories WHERE approved=0 and submitter='$email'");
}
if(!$submittedStories){
    die ('Can\'t query users because: ' . $link->error);
}

$num_submitted = mysqli_num_rows($submittedStories);

$approvedStories = $link->query("SELECT * FROM stories WHERE approved=1");
if(!$submittedStories){
    die ('Can\'t query users because: ' . $link->error);
}

$num_approved = mysqli_num_rows($approvedStories);


$action="";
if(isset($_POST["action"])){
    $action=$_POST["action"];
}

if($action == "add_story")
    {
        $title = $_POST["title"];
        $content = $_POST["content"];
        $email = $_COOKIE["NewsAppAccess"];
        
        $title = htmlentities($link->real_escape_string($title));
        $content = htmlentities($link->real_escape_string($content));
        $result = $link->query("INSERT INTO stories (name,storyText,submitter,approved) VALUES ('$title', '$content', '$email', 0)");

        if(!$result)
            die ('Can\'t add story because: ' . $link->error);
        header('Location: dashboard.php');
    }

    if($action == "approve")
    {
        $id = $_POST["id"];
        $email = $_COOKIE["NewsAppAccess"];
        print($id.": Approving");
        $result = $link->query("UPDATE stories set approved=1, approver='$email' where id = '$id'");//THIS SHOULD BE EDIT QUERY

        if(!$result)
            die ('Can\'t add story because: ' . $link->error);
        header('Location: dashboard.php');
    }

    if($action == "deactivate")
    {
        $id = $_POST["id"];
        $email = $_COOKIE["NewsAppAccess"];
        print($id.": Approving");
        $result = $link->query("UPDATE stories set approved=0, approver='' where id = '$id'");//THIS SHOULD BE EDIT QUERY

        if(!$result)
            die ('Can\'t add story because: ' . $link->error);
        header('Location: dashboard.php');
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
                    <li role="presentation"><a class="cd-signin" href="main.php"> Home</a></li>
                    <li role="presentation"><a class="cd-signin" href="logOut.php"> Sign Out</a></li>
                </ul>
            </nav>
            <a href="main.php"><h1 class="text-muted"><span class="glyphicon glyphicon-globe"></span> My News App</h1></a>
        </div>
        <div class="main">
            <h1>Story Manager</h1>
            <div class = "storyManager">
                <?php
                if($admin){
                ?>
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
                    <?php
                        if ($num_approved > 0) 
                        {
                            while( $row = $approvedStories->fetch_assoc())
                            {
                        ?><tr>
                            <td><?php print($row["name"])?></td>
                            <td><?php print($row["submitter"])?></td>
                            <td><?php print($row["submitDate"])?></td>
                            <td><?php print($row["approver"])?></td>
                            <td class="main-nav">
                                <a class="btn btn-xs btn-primary viewStory cd-signin" href="#0" role="button">View Story</a>
                            </td>
                            <td>
                                <form name="deactivate" action="#" method="post">
                                    <input type="submit" value="Deactivate" class="btn btn-xs btn-danger"/>
                                    <input type="hidden" name="id" value = "<?php print($row["id"]);?>"/>
                                    <input type="hidden" name="action" value = "deactivate"/></form>
                            </td>
                        </tr>
                        <?php
                            }
                        }?>
                    <tbody>
                    </tbody>
                </table>
                <?php
                }
                ?>
                <h3>Submitted Stories</h3>
                <table class="table table-hover storyTable">
                    <thead>
                      <tr>
                        <th>Name</th>
                        <th>Submitted By</th>
                        <th>Date</th>
                        <th>Story</th>
                        <?php
                            if($admin){
                        ?>
                        <th>Approve</th>
                        <?php
                            }
                        ?>
                      </tr>
                    </thead>
                    <?php
                        if ($num_submitted > 0) 
                        {
                            while( $row = $submittedStories->fetch_assoc())
                            {
                                ?><tr>
                                    <td><?php print($row["name"])?></td>
                                    <td><?php print($row["submitter"])?></td>
                                    <td><?php print($row["submitDate"])?></td>
                                    <td class="main-nav">
                                        <a class="btn btn-xs btn-primary viewStory cd-signin" href="#0" role="button">View Story</a>
                                    </td>
                                    <?php
                                    if($admin){
                                    ?>
                                    <td>
                                        <form name="approve" action="#" method="post"><input type="submit" value="Approve" class="btn btn-xs btn-success"/>
                                            <input type="hidden" name="id" value = "<?php print($row["id"]);?>"/>
                                            <input type="hidden" name="action" value = "approve"/></form>
                                    </td>
                                    <?php
                                    }
                                    ?>
                                </tr><?php
                            }
                        }
                    ?>
                    <tbody>
                    </tbody>
                </table>
                <div class="main-nav">
                    <a class="btn btn-primary viewStory cd-signin pull-right" href="#0" role="button">Submit A Story</a>
                </div>
            </div>
        </div>

        <!-- https://codyhouse.co/redirect/?resource=login-signup-modal-window -->
        <div class="cd-user-modal"> <!-- this is the entire modal form, including the background -->
            <div class="cd-user-modal-container"> <!-- this is the container wrapper -->
                <div id="cd-login"> <!-- log in form -->
                    <form class="cd-form" method="post" action="#">
                        <p class="fieldset"><input class="full-width has-padding has-border" id="title" name="title" type="text" placeholder="Story Title"></p>
                        <p class="fieldset"><input class="full-width has-padding has-border content" id="content" name="content" type="text" placeholder="Your story goes here."></p>
                        <p class="fieldset"><input type="hidden" name="action" value="add_story"/></p>
                        <p class="fieldset"><input class="full-width" type="submit" value="Submit"></p>
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