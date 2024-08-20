<?php
require "config.php";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the record number from the request, default to 1
$record_number = isset($_POST['record_number']) ? intval($_POST['record_number']) : 1;

// If you trigger a reset, go back to these hard coded figures (TODO update this to be more dynamic)
if (isset($_GET['reset'])) {
    $update_sql = "UPDATE teams SET clue = 1 WHERE id = 1";
    $result = $conn->execute_query($update_sql);
    $update_sql = "UPDATE teams SET clue = 7 WHERE id = 2";
    $result = $conn->execute_query($update_sql);
    $update_sql = "UPDATE teams SET clue = 14 WHERE id = 3";
    $result = $conn->execute_query($update_sql);
    $update_sql = "UPDATE teams SET clue = 20 WHERE id = 4";
    $result = $conn->execute_query($update_sql);
    $update_sql = "UPDATE teams SET clue = 28 WHERE id = 5";
    $result = $conn->execute_query($update_sql);
    $update_sql = "UPDATE teams SET clue = 14 WHERE id = 11";
    $result = $conn->execute_query($update_sql);
    $update_sql = "UPDATE teams SET clue = 28 WHERE id = 12";
    $result = $conn->execute_query($update_sql);
}

// Lets set up the page with teams and clues etc.
// Retrieve enabled teams from the database
$teams_sql = "SELECT id, name, number, clue FROM teams WHERE state = TRUE";
$teams_result = $conn->query($teams_sql);
$teams = [];
if ($teams_result->num_rows > 0) {
    while($row = $teams_result->fetch_assoc()) {
        $clues_sql = "SELECT id, place, what, text FROM records WHERE id = " .$row['clue'] . ";";
        $clues_result = $conn->query($clues_sql);
        if ($clues_result->num_rows > 0) {
            while($cluerow = $clues_result->fetch_assoc()) {
                array_push($row, array("place" => $cluerow['place'], "text" => $cluerow['text'], "what" => $cluerow['what']));
            }
        }
        $teams[] = $row;
    }
}

// Retrieve the text from the database for the clues
$biggest_clue = 0;
$clues_sql = "SELECT id, what, place, text FROM records;";
$clues_result = $conn->query($clues_sql);
$clues = [];
if ($clues_result->num_rows > 0) {
    while($row = $clues_result->fetch_assoc()) {
        $clues[] = $row;
        if ($row['id'] > $biggest_clue) {
                $biggest_clue = $row['id'];
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['send_text'])) {
    // Send the text using exec command
    foreach ($teams as $team):
        if ($team['0']['what'] == "item") {
                $text = "$TRName Item #" . $team['clue'] . "\n" . $team['0']['text'];
        } else {
                $text = "$TRName Place #" . $team['clue'] . "\n" . $team['0']['text'];
        }
        $command = 'mudslide send ' . $team['number'] . ' "' . addslashes($text) . '"';
        exec($command);
        // Increment the record number
        $update_sql = "UPDATE teams SET clue = ? WHERE id = ?";
        $update_stmt = $conn->prepare($update_sql);
        $new_clue = $team['clue'] +1 ;
        if ($new_clue > $biggest_clue) {
                $new_clue = 1;
        }
        $team_id = $team['id'] ;
        $update_stmt->bind_param("ii", $new_clue, $team_id);
        $update_stmt->execute();
        $update_stmt->close();

    endforeach;

    // Refresh the page with the new record number
    header("Location: " . $_SERVER['PHP_SELF']);
    echo '<form id="nextForm" method="post">
            <input type="hidden" name="record_number" value="' . $record_number . '">
            <input type="hidden" name="send_text" value="1">
          </form>';
    echo '<script>document.getElementById("nextForm").submit();</script>';
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Text Countdown</title>
    <script>
        let countdown;
        let countdownValue = 300; // Default value
        let countdownTimer;

        function startCountdown() {
            if (countdownValue <= 0) {
                document.getElementById('sendTextForm').submit();
            } else {
                countdown = document.getElementById('countdown');
                countdown.innerText = countdownValue;
                countdownValue--;
                countdownTimer = setTimeout(startCountdown, 1000);
            }
        }

        function updateCountdown() {
            countdownValue = parseInt(document.getElementById('countdownInput').value);
            clearTimeout(countdownTimer);
            startCountdown();
        }

        function pauseCountdown() {
            clearTimeout(countdownTimer);
        }

        function resumeCountdown() {
            startCountdown();
        }

        function resetCountdown() {
            countdownValue = parseInt(document.getElementById('countdownInput').value);
            clearTimeout(countdownTimer);
            countdown.innerText = countdownValue;
        }

        window.onload = function() {
            countdown = document.getElementById('countdown');
            document.getElementById('countdownInput').value = countdownValue;
            startCountdown();
        }
    </script>
</head>
<body>
    <h2>Teams</h2>
    <table border="1">
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Clue Num</th>
            <th>Clue</th>
        </tr>
        <?php
        foreach ($teams as $team):
                echo "        <tr>";
                echo "            <td>" . htmlspecialchars($team['id']) . "</td>";
                echo "            <td>" . htmlspecialchars($team['name']) . "</td>";
                $clue = $team['clue'];
                echo "            <td>" . htmlspecialchars($clue) . "</td>";
                echo "            <td>" . htmlspecialchars($team['0']['place']) ." : <br />". htmlspecialchars($team['0']['text'] ) . "</td>";
                echo "        </tr>";
        endforeach;
        ?>
    </table>
    <form id="sendTextForm" method="post">
        <input type="hidden" name="record_number" value="<?php echo $record_number; ?>">
        <input type="hidden" name="send_text" value="1">
    </form>

    <!-- Countdown controls -->
    <div>
        Countdown: <span id="countdown">300</span> seconds
    </div>
    <div>
        <label for="countdownInput">Set Countdown (seconds): </label>
        <input type="number" id="countdownInput" value="300">
        <button type="button" onclick="updateCountdown()">Update Countdown</button>
        <button type="button" onclick="pauseCountdown()">Pause</button>
        <button type="button" onclick="resumeCountdown()">Resume</button>
        <button type="button" onclick="resetCountdown()">Reset</button>
    </div>
    <div>
        <a href="/hunt.php?reset=1">Reset counters to start values</a>
    </div>
</body>
</html>

<?php
$conn->close();
?>

