<?php
session_start();

// Protect page (must be logged in)
if (!isset($_SESSION["logged_in"]) || $_SESSION["logged_in"] !== true) {
    header("Location: login.php");
    exit();
}

// Get user data
$user = $_SESSION["user"];

// Available Jobs with info
$jobs = array(
    "Web Developer" => array(
        "image" => "images/web.jpeg",
        "description" => "Develop and maintain websites and web applications.",
        "requirements" => "HTML, CSS, JavaScript, PHP knowledge required.",
        "location" => "Remote / New York, NY"
    ),
    "Graphic Designer" => array(
        "image" => "images/design.jpg",
        "description" => "Create visual content for marketing and digital platforms.",
        "requirements" => "Adobe Photoshop, Illustrator experience required.",
        "location" => "Los Angeles, CA"
    ),
    "Data Analyst" => array(
        "image" => "images/data.jpg",
        "description" => "Analyze and interpret data to support business decisions.",
        "requirements" => "Excel, SQL, and data visualization skills required.",
        "location" => "Chicago, IL"
    )
);

// Initialize applications array if not already
if (!isset($_SESSION["applications"])) {
    $_SESSION["applications"] = array(); // key: job name, value: status
}

// Handle job application
if (isset($_GET["apply"])) {
    $selectedJob = $_GET["apply"];
    if (array_key_exists($selectedJob, $jobs)) {
        $_SESSION["applications"][$selectedJob] = "Pending"; // Add or overwrite with Pending
    }
}

// Generate status messages using switch
function getStatusMessage($status)
{
    switch ($status) {
        case "Pending":
            return "Your application is pending.";
        case "Approved":
            return "Your application has been approved!";
        case "Rejected":
            return "Your application was rejected.";
        default:
            return "You have not applied for any job yet.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Dashboard | Job Application Portal</title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body>

    <header class="header">
        <img src="images/logo_1.png" alt="Logo" class="logo">
        <h1>Job Application Portal</h1>
    </header>

    <nav>
        <ul>
            <li><a href="dashboard.php">Dashboard</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </nav>

    <section class="dashboard-container">

        <!-- LEFT PANEL: PROFILE -->
        <div class="left-panel">
            <h2>Welcome, <?php echo htmlspecialchars($user["name"]); ?> 🎉</h2>

            <?php
            $fileExtension = strtolower(pathinfo($user["profile_pic"], PATHINFO_EXTENSION));
            if (in_array($fileExtension, ["jpg", "jpeg", "png"])) {
                echo "<img src='" . htmlspecialchars($user["profile_pic"]) . "' width='200'>";
            } else {
                echo "<p><a href='" . htmlspecialchars($user["profile_pic"]) . "' target='_blank'>View Uploaded Document</a></p>";
            }
            ?>

            <p><strong>Email:</strong> <?php echo htmlspecialchars($user["email"]); ?></p>
            <p><strong>Address:</strong> <?php echo htmlspecialchars($user["address"]); ?></p>
            <p><strong>Telephone:</strong> <?php echo htmlspecialchars($user["telephone"]); ?></p>
            <p><strong>Gender:</strong> <?php echo htmlspecialchars($user["gender"]); ?></p>
            <p><strong>State:</strong> <?php echo htmlspecialchars($user["state"]); ?></p>
            <p><strong>City:</strong> <?php echo htmlspecialchars($user["city"]); ?></p>

            <h3>Your CV</h3>
            <p>
                <a href="view_file.php?file=<?php echo urlencode($user['cv']); ?>" target="_blank">
                    View CV
                </a>
            </p>

            <hr>

            <h3>Application Status</h3>
            <?php
            if (!empty($_SESSION["applications"])) {
                foreach ($_SESSION["applications"] as $job => $status) {
                    echo "<p><strong>Applied Job:</strong> " . htmlspecialchars($job) . "</p>";
                    echo "<p><strong>Status:</strong> " . getStatusMessage($status) . "</p><hr>";
                }
            } else {
                echo "<p>" . getStatusMessage(null) . "</p>";
            }
            ?>
        </div>

        <!-- RIGHT PANEL: JOBS -->
        <div class="right-panel">
            <h3>Available Job Vacancies</h3>

            <?php
            foreach ($jobs as $job => $info) {
                echo "<div class='job-card'>";
                echo "<h4>" . htmlspecialchars($job) . "</h4>";

                if (!empty($info['image'])) {
                    echo "<img src='" . htmlspecialchars($info['image']) . "' width='250'>";
                }

                echo "<p><strong>Description:</strong> " . htmlspecialchars($info['description']) . "</p>";
                echo "<p><strong>Requirements:</strong> " . htmlspecialchars($info['requirements']) . "</p>";
                echo "<p><strong>Location:</strong> " . htmlspecialchars($info['location']) . "</p>";

                echo "<a href='dashboard.php?apply=" . urlencode($job) . "'>";
                echo "<button>Apply</button>";
                echo "</a>";
                echo "</div><hr>";
            }
            ?>
        </div>

    </section>

    <footer>
        <p>&copy; <?php echo date("Y"); ?> Job Application Portal</p>
    </footer>

</body>

</html>