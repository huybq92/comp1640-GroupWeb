<?php
//REFERENCE CODE for pagination section:
// http://www.techumber.com/simple-pagination-with-php-mysql/

//Inside the below php file, I already created an object named '$connection'
require_once '../php/class.CustomMySQLConnection.php';
// Some functions to handle timeout requirements
require_once '../php/functions.session.php';

    // Get Session variables
    session_start();

    // Check session before loading page
    checkSessionInMain();
    $closure_date='';
    $final_closure_date='';

    $settings_query = "SELECT set_value FROM settings WHERE set_id=1;";
    $settings_query_2 = "SELECT set_value FROM settings WHERE set_id=2;";

    $result_set = $connection->executeSELECT($settings_query);
    $result_set_2 = $connection->executeSELECT($settings_query_2);

    // Check if the query succeeds
    if ( $result_set != false && $result_set_2 != false ) {
        // Then save the retrieved data to variables
        $result = mysqli_fetch_assoc($result_set);
        $result_2 = mysqli_fetch_assoc($result_set_2);
        $closure_date = $result['set_value'];
        $final_closure_date = $result_2['set_value'];
    } else {
        echo "<script type='text/javascript'>alert('There is problem with the database connection. Please try again later!');</script>";
        exit();
    }

    // ## BUTTON HANDLER ##
    // Button update closure time
    if(isset($_POST['update_closure'])) {

        // Get the input data
        $new_closure_date = $connection->fixEscapeString($_POST['txt_closure']);

        // SQL command
        $update_closure_date_query = "UPDATE settings SET set_value='$new_closure_date' WHERE set_id=1";
        // Insert to the DB
        $update_closure_date_query_result = $connection->executeSELECT($update_closure_date_query);

        // Check the query result
        if ($update_closure_date_query_result == false) {
            // The query fails
            // Display error to inform user
            echo "<script type='text/javascript'>alert('Cannot update time due to database problem. Please try again!');</script>";
        } else {
            echo "<script type='text/javascript'>alert('Updated successfully');</script>";
            header("Refresh:0"); // refresh the page to update change time
        }
    }

    // Button update final closure time
    if(isset($_POST['update_final_closure'])) {

        // Get the input data
        $new_final_closure_date = $connection->fixEscapeString($_POST['txt_final_closure']);

        // SQL command
        $update_final_closure_date_query = "UPDATE settings SET set_value='$new_final_closure_date' WHERE set_id=2";
        // Insert to the DB
        $update_final_closure_date_query_result = $connection->executeSELECT($update_final_closure_date_query);

        // Check the query result
        if ($update_final_closure_date_query_result == false) {
            // The query fails
            // Display error to inform user
            echo "<script type='text/javascript'>alert('Cannot update time due to database problem. Please try again!');</script>";
        } else {
            echo "<script type='text/javascript'>alert('Updated successfully');</script>";
            header("Refresh:0"); // refresh the page to update change time
        }
    }

?>
<!DOCTYPE HTML>
<!--MOST OF CODE IN THIS FILE IS FROM: https://multimedia.journalism.berkeley.edu/tutorials/css-layout/ -->
<!--[if lt IE 7]> <html class="no-js ie6 oldie" lang="en"> <![endif]-->
<!--[if IE 7]>    <html class="no-js ie7 oldie" lang="en"> <![endif]-->
<!--[if IE 8]>    <html class="no-js ie8 oldie" lang="en"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang="en"> <!--<![endif]-->
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

	<title>Main</title>
	<meta name="description" content="">
	<meta name="author" content="">

	<meta name="viewport" content="width=device-width,initial-scale=1">

	<link rel="stylesheet" href="../css/main.css">

	<script src="script/libs/modernizr-2.0.6.min.js"></script>
</head>
<body>

  <!-- This is the starter structure that comes from html5boilerplate.com -->
  <!-- This #container contains the entire page -->
  <div id="container">
      <!-- Header -->
      <header>
          <div id="headerimg">
            <img src="../img/tmc-logo2.png" width="400" height="110" alt="TMC_logo">
          </div>
          <h1>Bui Quang Huy</h1>
      </header>
      <!-- end of header -->

      <!-- Wrapper of <section> and <aside>. The purpose is to make the heights of them equal by using JS & JQuery -->
      <div id="boxes">
        <section id="main" role="main">
              <div id="content">
                  <div id="results">
                      <h1> Closure date for new idea </h1>
                      <br>
                      Current: <?php echo $closure_date ?>
                      <form name="closure" action="" method="post">
                          <input name="txt_closure" type="text" size="30" placeholder="New closure date">
                          <input name="update_closure" type="submit" value="Update">
                      </form>
                      <br>
                      Please put new time in format: YYYY-mm-dd HH:MM:ss

                      <br><br>
                      <h1> Final closure date for logging in </h1>
                      <br>
                      Current: <?php echo $final_closure_date ?>
                      <form name="final_closure" action="" method="post">
                          <input name="txt_final_closure" type="text" size="30" placeholder="New final closure date">
                          <input name="update_final_closure" type="submit" value="Update">
                      </form>
                      <br>
                      Please put new time in format: YYYY-mm-dd HH:MM:ss
                  </div>
              </div>
         </section>

         <!-- Right Menu -->
         <aside>
              <!-- Added sidebar content -->
             <h2>Welcome, <?php echo $_SESSION['login_user'] ?></h2>
             <ul>
                 <li><a href="main.php">VIEW IDEAS</a></li>
                 <li><a href="settings.php">SETTINGS</a></li>
                 <li><a href="../php/logout.php">LOGOUT</a></li>
             </ul>
          </aside>
      </div>
      <!-- end of <div> #boxes -->

      <!-- Footer -->
      <footer>
          <p>Copyright 2017, Bui Quang Huy. All rights reserved.</p>
          <p><small><a href="#">Terms of Service</a> I <a href="#">Privacy</a></small></p>
      </footer> <!-- end of <footer> -->
  </div>
  <!--! end of #container -->

  <!-- ############################# -->
  <!-- JavaScript and JQuery section -->
  <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js"></script>
  <script>window.jQuery || document.write('<script src="script/libs/jquery-1.6.2.min.js"><\/script>')</script>

  <!-- scripts concatenated and minified via ant build script-->
  <script src="../script/plugins.js"></script>
  <script src="../script/script.js"></script>
  <!-- end scripts-->

  <!-- Validate search inputs -->
  <script type="text/javascript" charset="utf-8">
    function validate() {
        var string = document.forms["searchBox"]["query"].value;
        // If the search box is empty, stop POST by returning False
        // Otherwise, return True
        if (string == "") {
            alert("Please enter something to search!");
            return false;
        } else {
          return true;
        }
    }
  </script>
  <!-- end of scripts section -->
  <!-- ###################### -->
</body>
</html>
