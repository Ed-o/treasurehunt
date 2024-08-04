<?php
require "config.php";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Retrieve enabled teams from the database
$teams_sql = "SELECT id, name, number, clue FROM teams WHERE state = TRUE";
$teams_result = $conn->query($teams_sql);
$teams = [];
if ($teams_result->num_rows > 0) {
    while($row = $teams_result->fetch_assoc()) {
        $teams[] = $row;
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['send_text'])) {
    $text = $_POST['send_text'];
    // Send the text using exec command
    foreach ($teams as $team):
        $command = 'mudslide send ' . $team['number'] . ' "' . addslashes($text) . '"';
        exec($command);
    endforeach;

}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manual message</title>
</head>
<body>
    <h2>Teams</h2>
    <table border="1">
        <tr>
            <th>ID</th>
            <th>Name</th>
        </tr>
        <?php
        foreach ($teams as $team):
                echo "        <tr>";
                echo "            <td>" . htmlspecialchars($team['id']) . "</td>";
                echo "            <td>" . htmlspecialchars($team['name']) . "</td>";
                echo "        </tr>";
        endforeach;
        ?>
    </table>
    <form id="sendTextForm" method="post">
        <input type="text" name="send_text" value="">
	<input type="submit" name="submit" value="submit">
    </form>
</body>
</html>

<?php
$conn->close();
?>

