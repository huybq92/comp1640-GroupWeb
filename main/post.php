<?php
// Inside the below php file, I already created an object named '$connection'
require_once '../php/class.CustomMySQLConnection.php';
// Some functions to handle timeout requirements
require_once '../php/functions.session.php';
// Functions to send email
require_once '../php/functions.sendEmail.php';

    // Get Session variables
    session_start();

    // Check session before loaing page
    checkSessionInMain();

    $error = '*Note: only JPG,PNG,PDF or TXT files are allowed!'; // holds message to inform users

    // To check if fail uploading succeed
    $isThereUploadFile = true;
    $isFileUploaded = false;

    // Initialize here to make them global
    $file_name = "";
    $target_file = ""; 

    // Query db to get the category list
    $category_list_query  = "SELECT cate_id,cate_name FROM categories;";
    $category_list_query_result = $connection->executeSELECT($category_list_query);
    if ( $category_list_query_result == FALSE) {
      //If cannot get category list due to db issue, redirect to error.php and display error
      //$error = $connection->getDbConnectionError();
      //exit();
    }

    // ## HANDLE THE SUBMIT BUTTON ##
    if(isset($_POST['button-create'])) {

      // First of all, user must agree with the Terms and Conditions
      if (isset($_POST['term_agreement']) && $_POST['term_agreement'] == 'Yes') {

        // Handle the file uploading process
        // *NOTE: even if user choose no file, the variable $_FILES[] still NOT empty
        // REFERENCE: https://www.startutorial.com/articles/view/php_file_upload_tutorial_part_1
        if (!empty($_FILES['fileToUpload'])) {
            // If there is file to upload

            $file_name = $_FILES['fileToUpload']['name'];
            $tmp_filename = $_FILES['fileToUpload']['tmp_name'];

            $target_dir = "uploads/" . $_SESSION['login_user'] . "/";
            $target_file = $target_dir . basename($file_name);

            $uploading_error = $_FILES['fileToUpload']['error'];
            $file_size = $_FILES['fileToUpload']['size'];

            $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            $allowed_ext = array("jpg","jpeg","pdf","txt","doc","docx");

            // Validate the selected file
            switch ($uploading_error) {
                //REFERENCE: https://www.startutorial.com/articles/view/php_file_upload_tutorial_part_1
                case UPLOAD_ERR_OK:
                    if ( $file_size > 2097152 ) {
                        $error='Uploading file cannot be bigger than 2MB!';
                    } elseif ( !in_array($file_ext, $allowed_ext) ) {
                        $error='File type not allowed!';
                    } elseif ( file_exists($target_file) ) {
                        $error='Sorry, file already exists';
                    } else {
                        // Validation succeeds, then moving temp file to target_dir
                        if (move_uploaded_file($tmp_filename, $target_file)) {
                            $isFileUploaded = true;
                        } else {
                            $error='Sorry, uploading your file is failed';
                        }
                    }  
                    break;
                case UPLOAD_ERR_INI_SIZE:
                    $error = 'The uploaded file exceeds the upload_max_filesize directive in php.ini.';
                    break;
                case UPLOAD_ERR_FORM_SIZE:
                    $error = 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.';
                    break;
                case UPLOAD_ERR_PARTIAL:
                    $error = 'The uploaded file was only partially uploaded.';
                    break;
                case UPLOAD_ERR_NO_FILE:
                    $error = 'No file was uploaded.';
                    $isThereUploadFile = false;
                    break;
                case UPLOAD_ERR_NO_TMP_DIR:
                    $error = 'Missing a temporary folder. Introduced in PHP 4.3.10 and PHP 5.0.3.';
                    break;
                case UPLOAD_ERR_CANT_WRITE:
                    $error = 'Failed to write file to disk. Introduced in PHP 5.1.0.';
                    break;
                case UPLOAD_ERR_EXTENSION:
                    $error = 'File upload stopped by extension. Introduced in PHP 5.2.0.';
                    break;
                default:
                    $error = 'Unknown error';
                    break;
            } // switch ends            
        }
        // If no file to upload, move on

        // Handle the data inserting process    
        if ($isThereUploadFile == false) {
            // No file to upload, just insert data

            $idea_title = $connection->fixEscapeString($_POST['idea_title']);
            $idea_content = htmlspecialchars($_POST['idea_content']);
            $cate_id = $_POST['category'];
            $user_id = $_SESSION['login_id'];
            $idea_anony = 0;
            if (isset($_POST['post_anonymously']) && $_POST['post_anonymously'] == 'Yes') {
              // If user tick anonymously checkbox
              $idea_anony = 1;
            }

            // No file to upload => blank the last field when inserting
            $create_idea_query = "INSERT INTO ideas VALUES(null, '$idea_title', '$idea_content', current_timestamp(), $idea_anony, '$user_id', '$cate_id', '');";
            
            // Insert to the DB
            $create_idea_query_result = $connection->executeSELECT($create_idea_query);

            // Check result
            if ($create_idea_query_result != false) {
                // If SQL statement is carried out successfully
                // Notify QA Coordinator via email
                notifyQACoordinator();

                // Then redirect to main page
                header('Location:main.php', true, 301);
                exit(); // stop script
            } else {
                // If failed to insert, display error
                $error = 'Could not create due to database problem!';
            }


        } else {
            // $isThereUploadFile == true

            if ($isFileUploaded == true) {
                // And the uploading succeeded, start inserting data

                $idea_title = $connection->fixEscapeString($_POST['idea_title']);
                $idea_content = htmlspecialchars($_POST['idea_content']);
                $cate_id = $_POST['category'];
                $user_id = $_SESSION['login_id'];
                $idea_anony = false;
                if (isset($_POST['post_anonymously']) && $_POST['post_anonymously'] == 'Yes') {
                  // If user tick anonymously checkbox
                  $idea_anony = true;
                }

                // SQL command
                $create_idea_query = "INSERT INTO ideas VALUES(null, '$idea_title', '$idea_content', current_timestamp(), $idea_anony, '$user_id', '$cate_id', '$file_name');";

                // Insert to the DB
                $create_idea_query_result = $connection->executeSELECT($create_idea_query);

                // Check result
                if ($create_idea_query_result != false) {
                    // SQL statement is carried out successfully
                    // Then redirect to main page
                    header('Location:main.php', true, 301);
                    exit();
                } else {
                    // If failed to insert, display error and unlink uploaded file
                    $error = 'Could not create due to database problem!';
                    unlink($target_file);
                }

            }

        }

      } else {
          // user haven't tick the checkbox to agrre the Terms and Condtions
          $error = 'Please agree to the Terms and Conditions';
      }  

    }// end of submit button handler
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

	<title>Post new idea</title>
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

      <!-- Wrapper of <section> and <aside> into 1 container #boxes -->
      <div id="boxes">
        <section id="main" role="main">
              <div id="content">
                  <h1>Post a new idea to the university</h1>
                  <div id="create_post">
                      <form name="create_form" action="" method="post" enctype="multipart/form-data">
                        <p>Title 
                          <br>
                          <input
                          type        = "text"
                          name        = "idea_title"
                          maxlength   = "500"
                          size        = "49"
                          required>
                        </p>
                        <p>Idea <br><textarea rows="3" cols="50" name="idea_content"></textarea></p>
                        <p>Category                        
                        <select name="category">
                            <?php
                                // Iterate through $category_list_query_result to list all categories
                                while( $result = mysqli_fetch_assoc($category_list_query_result) )
                                {
                            ?>
                                <option value="<?php echo $result['cate_id']?>"><?php echo $result['cate_name']?></option> 
                            <?php
                                } // Closing while loop
                            ?>
                        </select></p>

                        <p>Upload file to support your idea:
                          <br><input type="file" name="fileToUpload" id="fileToUpload">
                        </p>

                        <p><input type="checkbox" name="post_anonymously" value="Yes"> Post anonymously</p>
                        <p><input type="checkbox" name="term_agreement" value="Yes"> I understand and agree to the Terms and Conditions</p>

                        <p><?php echo $error; ?></p>

                        <p><input type="submit" value="POST NEW IDEA" name="button-create"><p>
                      </form>
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
      </div> 
      <!-- end of <div> #boxes -->

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
