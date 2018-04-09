<?php
//Inside the below php file, I already created an object named '$connection'
require_once '../php/class.CustomMySQLConnection.php';
// Some functions to handle timeout requirements
require_once '../php/functions.session.php';
// Functions to get search results and display
require_once '../php/functions.pagination.php';
// Functions to send email
require_once '../php/functions.sendEmail.php';


    // Get Session variables
    session_start();

    // Check session before loaing page
    checkSessionInMain();

    $error = '';
    
    // Save the idea_id from GET variable
    $idea_id = $_GET['idea_id'];
    $idea_acc_id = $_GET['idea_acc_id']; // acc_id of the username that posted this idea (not comment)

    // Query the database to get the category_id of the above idea_id
    // - cate_id: id of the category that the idea belongs to, needed when insert a comment to database
    // - account email is needed to inform to idea owner when new comment is posted
    $select_cate_id_query = "SELECT categories.cate_id, accounts.email FROM categories INNER JOIN ideas ON categories.cate_id=ideas.categories_cate_id INNER JOIN accounts ON ideas.accounts_acc_id=accounts.acc_id WHERE ideas.idea_id='$idea_id';";

    // Query the db
    $select_cate_id_query_result_set = $connection->executeSELECT($select_cate_id_query);

    //Initialize the variables
    $cate_id='';
    $username='';

    // Check if SQL statements are carried out successfully
    if ( $select_cate_id_query_result_set != false ) {
      $result = mysqli_fetch_assoc($select_cate_id_query_result_set);
      $cate_id = $result['cate_id'];
      $email = $result['email'];
    } else {
      // If cannot query the db for cate_id
      // Then display error and stop the script
      echo "<script type='text/javascript'>alert('There is problem with the database connection. Please try again later!');</script>";
      exit();
    }

    // ## Handle the submit button ##
    if ( isset($_POST['button-create']) ) {

        // acc_id and acc_type of current username that creating the comment
        $comment_acc_id = $_SESSION['login_id'];
        $comment_acc_type = $_SESSION['acc_type'];

        // Get the input data
        $comment = $connection->fixEscapeString($_POST['comment']);

        // SQL command
        $create_comment_query = "INSERT INTO comments VALUES(null, '$comment', '$idea_id','$idea_acc_id' ,'$cate_id', current_timestamp(), '$comment_acc_id');";
        // Insert to the DB
        $create_comment_query_result = $connection->executeSELECT($create_comment_query);

        // Check the query result
        if ($create_comment_query_result == false) {
            // The query fails
            // Display error to inform user
            echo "<script type='text/javascript'>alert('Cannot create comment due to database problem. Please try again!');</script>";
        } else {
            // If the query succeed, send email to user that created the idea
            notifyUserForNewComment($email, $_SESSION['login_user']);
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

	<title>Comments</title>
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

      <!-- Wrapper of <section> and <aside> into 1 container #boxes. -->
      <div id="boxes">
        <section id="main" role="main">
              <div id="content">
                  <!-- Comment bar -->
                  <div id="searchBar">
                      <form name="searchBox" action="" method="post">
                          <input name="comment" type="text" size="50" placeholder="Your comment">
                          <input name="button-create" type="submit" value="Comment">
                      </form>
                  </div>

                  <!-- Display all comments -->
                  <div id="results">
                      <?php
                          // By default, main page loads all the ideas without searching query
                          if( !isset($_GET['query']) ) {
                                // REFERENCE CODE: http://www.phponwebsites.com/2014/04/php-mysql-pagination.html

                                // Determine parameters for pagination
                                $limit = 8; // maximum number of rows per page
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
                                // - acc_type: type of the account that created the comment. If acc_type != 0, meaning that this comment belongs to staff => student cannot see
                                $total_query_statement = "SELECT accounts.acc_type,accounts.username,comments.cm_content,comments.cm_time FROM comments INNER JOIN accounts ON comments.accounts_acc_id=accounts.acc_id WHERE comments.ideas_idea_id=". $idea_id. " ORDER BY comments.cm_time DESC;";

                                $limit_query_statement = "SELECT accounts.acc_type,accounts.username,comments.cm_content,comments.cm_time FROM comments INNER JOIN accounts ON comments.accounts_acc_id=accounts.acc_id WHERE comments.ideas_idea_id=". $idea_id. " ORDER BY comments.cm_time DESC LIMIT $start, $limit;";

                                $limit_result_set = $connection->executeSELECT($limit_query_statement); // => THIS RESULT WILL BE DISPLAYED
                                $total_result_set = $connection->executeSELECT($total_query_statement); // => THIS RESULT IS USED TO DETERMINE THE TOTAL NUMBER OF PAGES NEEDED

                                // FINISHED DATABASE QUERY
                                // Now check if SQL statements are carried out successfully
                                if ( $limit_result_set!=false && $total_result_set!=false ) {
                                    $total_rows = mysqli_num_rows($total_result_set); // count the total rows
                                    $max_page   = ceil( $total_rows/$limit ); // calculate the total number of pages needed
                                    ?>
                                    <table id="table-results">
                                        <thread>
                                            <tr>
                                              <th style="width: 300px;">Comments</th>
                                              <th style="width: 100px;">User</th>
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
                                      // Check if the comment belongs to normal student or not.
                                      // Student account cannot see staff, qa_coordinator or qa_manager comments.
                                      if ( $result['acc_type'] == '0' ) {
                                  ?>
                                          <tr>
                                            <td><?php echo $result['cm_content'] ?></td>
                                            <td style="text-align: center;"><?php echo $result['username'] ?></td>
                                            <td><?php echo $result['cm_time'] ?></td>
                                            <td><a href="">Like</a></td>
                                            <td><a href="">Dislike</a></td>
                                          </tr>
                                  <?php
                                      } elseif ( $idea_acc_id == $_SESSION['login_id']) {
                                         // If comment belongs to staff, but current login account is the one that post the idea, so still display the comment from staff
                                  ?>
                                          <tr>
                                            <td><?php echo $result['cm_content'] ?></td>
                                            <td style="text-align: center;"><?php echo $result['username'] ?></td>
                                            <td><?php echo $result['cm_time'] ?></td>
                                            <td><a href="">Like</a></td>
                                            <td><a href="">Dislike</a></td>
                                          </tr>
                                  <?php                                      
                                      } // if ends
                                    } // closing while loop
                                  ?>
                                        </tbody>
                                    </table>
                      <?php
                                    // Create pagination navigator
                                    if( $max_page > 1 ) {
                                        // If more than 1 page
                                        generatePaginationWithIds($idea_id, $acc_id, $page, $max_page);
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
         <aside>
              <!-- Added sidebar content -->
             <h2>Welcome, <?php echo $_SESSION['login_user'] ?></h2>
             <ul>
                 <li><a href="main.php">VIEW IDEAS</a></li>
                 <li><a href="post.php">POST NEW IDEA</a></li>
                 <li><a href="../php/logout.php">LOGOUT</a></li>
             </ul>
          </aside>
      </div> <!-- end of <div> #boxes -->

      <!-- Footer -->
      <footer>
          <p>Copyright 2017, Bui Quang Huy. All rights reserved.</p>
          <p><small><a href="#">Terms of Service</a> I <a href="#">Privacy</a></small></p>
      </footer> <!-- end of <footer> -->
  </div> <!--! end of #container -->

  <!-- ############################# -->
  <!-- ############################# -->
  <!-- JavaScript and JQuery section -->
  <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js"></script>
  <script>window.jQuery || document.write('<script src="script/libs/jquery-1.6.2.min.js"><\/script>')</script>

  <!-- scripts concatenated and minified via ant build script-->
  <script src="../script/plugins.js"></script>
  <script src="../script/script.js"></script>
  <!-- end scripts-->

  <!-- end of scripts section -->
  <!-- ###################### -->
</body>
</html>
