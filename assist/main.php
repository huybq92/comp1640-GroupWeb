<?php
//REFERENCE CODE for pagination section:
// http://www.techumber.com/simple-pagination-with-php-mysql/

//Inside the below php file, I already created an object named '$connection'
require_once '../php/class.CustomMySQLConnection.php';
// Some functions to handle timeout requirements
require_once '../php/functions.session.php';
// Functions to get search results and display
require_once '../php/functions.pagination.php';

    // Get Session variables
    session_start();

    // Check session before loaing page
    checkSessionInMain();

    //

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
                      <?php
                          // By default, main page loads all the ideas without searching query
                          if( !isset($_GET['query']) ) {
                                // REFERENCE CODE: http://www.phponwebsites.com/2014/04/php-mysql-pagination.html

                                // Determine parameters for pagination
                                $limit = 5; // maximum number of rows per page
                                $page  = 1; // just initialize, value doesn't matter

                                if(!isset($_GET['page'])) {
                                    // if no page is specified, start with the first page
                                    $page  = 1;
                                    $start = 0; // the index of the first row of the current page
                                } else {
                                    // if page number is specified, the start index is calculated as below
                                    $page = $_GET['page'];
                                    $start = $limit*($page-1);
                                }

                                // Execute SQL statements
                                // - username: username that posted the idea
                                // - acc_type: type of the username that posted the idea.

                                $total_query_statement = "SELECT accounts.username,accounts.acc_id,ideas.idea_id,ideas.idea_title,ideas.idea_content,ideas.idea_time,ideas.idea_filename,ideas.idea_anony FROM ideas INNER JOIN accounts ON accounts.acc_id=ideas.accounts_acc_id ORDER BY ideas.idea_time DESC;";

                                $limit_query_statement = "SELECT accounts.username,accounts.acc_id,ideas.idea_id,ideas.idea_title,ideas.idea_content,ideas.idea_time,ideas.idea_filename,ideas.idea_anony FROM ideas INNER JOIN accounts ON accounts.acc_id=ideas.accounts_acc_id ORDER BY ideas.idea_time DESC LIMIT $start, $limit;";

                                $limit_result_set = $connection->executeSELECT($limit_query_statement); // => THIS RESULT WILL BE DISPLAYED
                                $total_result_set = $connection->executeSELECT($total_query_statement); // => THIS RESULT IS USED TO DETERMINE THE TOTAL NUMBER OF PAGES NEEDED

                                // Check if SQL statements are carried out successfully
                                if ( $limit_result_set!=false && $total_result_set!=false ) {
                                    $total_rows = mysqli_num_rows($total_result_set); // count the total rows
                                    $max_page   = ceil( $total_rows/$limit ); // calculate the total number of pages needed
                                    ?>
                                    <table id="table-results">
                                        <thread>
                                            <tr>
                                              <th style="width: 100px;">User</th>
                                              <th style="width: 150px;">Idea</th>
                                              <th style="width: 300px;">Content</th>
                                              <th style="width: 170px;">Time</th>
                                              <th style="width: 50px;"> </th>
                                              <th style="width: 50px;"> </th>
                                            </tr>
                                        </thread>
                                        <tbody>

                                  <?php
                                    // Iterate through result set to display results
                                    while( $result = mysqli_fetch_assoc($limit_result_set) )
                                    {
                                  ?>
                                          <tr>
                                      <?php
                                        if ($result['idea_anony'] == '0') {
                                        // not an anonymous post
                                      ?>
                                            <td style="text-align: center;"><?php echo $result['username'] ?></td>
                                      <?php
                                        } else {
                                          // hide name for anonymous post
                                      ?>
                                            <td style="text-align: center;"></td>
                                      <?php
                                        }
                                      ?>
                                            <td><?php echo $result['idea_title'] ?></td>
                                            <td><?php echo $result['idea_content'] ?></td>
                                            <td><?php echo $result['idea_time'] ?></td>
                                      <?php
                                        if ($result['idea_filename'] == "") {
                                        // No file name
                                      ?>
                                            <td></td>
                                      <?php
                                        } else {
                                        // There is file name
                                      ?>
                                            <td><a href="<?php echo "uploads/". $result['username']. "/". $result['idea_filename'] ?>">File</a></td>
                                      <?php
                                        }
                                      ?>
                                            <td><a href="<?php echo "comments.php?idea_id=". $result['idea_id']. "&idea_acc_id=". $result['acc_id'] ?>">Comments</a></td>
                                          </tr>
                                  <?php
                                    } // closing while loop
                                  ?>
                                        </tbody>
                                    </table>
                      <?php
                                    // Create pagination navigator
                                    if( $max_page > 1 ) {
                                        // If more than 1 page
                                        generatePagination($page, $max_page);
                                    }
                                } else {
                                  // Database returns false for the execution
                                  echo 'Cannot connect to Database. Please try again!';
                                }
                          }
                      ?>
                  </div>
              </div>
         </section>

         <!-- Right Menu -->
         <aside>
              <!-- Added sidebar content -->
             <h2>Welcome, <?php echo $_SESSION['login_user'] ?></h2>
             <ul>
                 <li><a href="main.php">VIEW IDEAS</a></li>
                 <li><a href="post.php">POST NEW IDEA</a></li>
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
