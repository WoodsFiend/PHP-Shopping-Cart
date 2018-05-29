
<?php
    session_start();
    include('header.html');
    include('connectionSQL.php');
    
    echo "<article>";
    echo "<div class=\"textBack\" align=\"left\" style=\"float:left\">";
    echo "<h1>Add Product</h1><br><br><br>";
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $gamename = strip_tags($_POST['gamename']);
        $price = strip_tags($_POST['price']);
        $gamedesc = nl2br(addslashes(strip_tags($_POST['gamedesc'])));
        $console = $_POST['console'];
        $genre = $_POST['genre'];
        $imagename = $_FILES['image']['name'];
        $imagetmp = $_FILES['image']['tmp_name'];

        // Upload image to the web server & create query
        if ($_FILES['image']['name'] != '') {
            $imageurl = 'images/games/' . $imagename;
            echo "<p>$imagename is being uploaded.</p>";
            if (move_uploaded_file($_FILES['image']['tmp_name'], "images/games/{$_FILES['image']['name']}")) {
                echo "<p>$imageurl uploaded.</p>";
            } else {
                print '<p style="color: red;">Your file could not be uploaded because: ';

                // Print a message based upon the error:
                switch ($_FILES['image']['error']) {
                    case 1:
                        print 'The file exceeds the upload_max_filesize setting in php.ini';
                        break;
                    case 2:
                        print 'The file exceeds the MAX_FILE_SIZE setting in the HTML form';
                        break;
                    case 3:
                        print 'The file was only partially uploaded';
                        break;
                    case 4:
                        print 'No file was uploaded';
                        break;
                    case 6:
                        print 'The temporary folder does not exist.';
                        break;
                    default:
                        print 'Something unforeseen happened.';
                        break;
                }
            }
            $gamequery = "INSERT INTO games (name, description, image, price, console_name) VALUES ('$gamename', '$gamedesc', '$imageurl', '$price', '$console')";
        } else {
            echo "<p>No image uploaded.</p>";
            $gamequery = "INSERT INTO games (name, description, price, console_name) VALUES ('$gamename', '$gamedesc', '$price', '$console')";
        }

        // Add the new game to the database
        @mysqli_query($link, $gamequery);

        // Retrieve the game_id of the new game
        $gameidquery = "SELECT LAST_INSERT_ID()";
        $gameidresult = @mysqli_query($link, $gameidquery);
        $gameidrow = mysqli_fetch_row($gameidresult);
        $gameid = $gameidrow[0];

        // Add the game genres to the database
        foreach ($genre as &$value) {
            $genrequery = "INSERT INTO game_genres values('$gameid', '$value')";
            @mysqli_query($link, $genrequery);
        }

        echo "<p>Successfully added game # $gameid</p>";
        echo "<p><a href=\"addProducts.php\">Add another product?</a></p>";
    }
    echo "</div>";
    echo "<article>";
?>

<?php
include('footer.php');
	
?>