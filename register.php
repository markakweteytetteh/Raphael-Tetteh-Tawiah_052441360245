<?php
session_start();

$message = "";
$values = [
    "name" => "",
    "email" => "",
    "address" => "",
    "telephone" => "",
    "gender" => "",
    "city" => "",
    "state" => ""
];

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Collect & sanitize input
    $values["name"] = trim($_POST["name"]);
    $values["email"] = trim($_POST["email"]);
    $rawPassword = trim($_POST["password"]);
    $confirm_password = trim($_POST["confirm_password"]);
    $values["address"] = trim($_POST["address"]);
    $values["telephone"] = trim($_POST["telephone"]);
    $values["gender"] = trim($_POST["gender"]);
    $values["city"] = trim($_POST["city"]);
    $values["state"] = trim($_POST["state"]);

    $uploadDir = "uploads/";

    // ---------------- PROFILE PICTURE ----------------
    $picName = basename($_FILES["document"]["name"]);
    $picTmp = $_FILES["document"]["tmp_name"];
    $picError = $_FILES["document"]["error"];
    $picSize = $_FILES["document"]["size"];
    $picType = strtolower(pathinfo($picName, PATHINFO_EXTENSION));
    $allowedPicTypes = ["jpg", "jpeg", "png", "pdf"];
    $newPicName = time() . "_pic_" . $picName;
    $targetPic = $uploadDir . $newPicName;

    // ---------------- CV FILE ----------------
    $cvName = basename($_FILES["cv"]["name"]);
    $cvTmp = $_FILES["cv"]["tmp_name"];
    $cvError = $_FILES["cv"]["error"];
    $cvSize = $_FILES["cv"]["size"];
    $cvType = strtolower(pathinfo($cvName, PATHINFO_EXTENSION));
    $allowedCVTypes = ["pdf", "doc", "docx"];
    $newCVName = time() . "_cv_" . $cvName;
    $targetCV = $uploadDir . $newCVName;

    // ---------------- VALIDATION ----------------
    $errors = [];

    // Required fields
    foreach ($values as $field => $val) {
        if (empty($val)) {
            $errors[$field] = ucfirst($field) . " is required.";
        }
    }

    // Email
    if (!empty($values["email"]) && !filter_var($values["email"], FILTER_VALIDATE_EMAIL)) {
        $errors["email"] = "Invalid email format.";
    }

    // Passwords
    if (!empty($rawPassword) && $rawPassword !== $confirm_password) {
        $errors["password"] = "Passwords do not match.";
    }

    // Telephone
    if (!empty($values["telephone"]) && !preg_match("/^[0-9]{10,15}$/", $values["telephone"])) {
        $errors["telephone"] = "Telephone must be 10–15 digits.";
    }

    // Gender validation using SWITCH
    switch ($values["gender"]) {
        case "male":
        case "female":
        case "other":
            // valid
            break;
        default:
            $errors["gender"] = "Please select a valid gender.";
    }

    // File upload errors using SWITCH
    switch ($picError) {
        case UPLOAD_ERR_OK:
            if (!in_array($picType, $allowedPicTypes)) {
                $errors["document"] = "Profile picture must be JPG, JPEG, PNG or PDF.";
            }
            if ($picSize > 2000000) {
                $errors["document"] = "Profile picture exceeds 2MB.";
            }
            break;
        case UPLOAD_ERR_NO_FILE:
            $errors["document"] = "Please upload a profile picture.";
            break;
        default:
            $errors["document"] = "Error uploading profile picture.";
    }

    switch ($cvError) {
        case UPLOAD_ERR_OK:
            if (!in_array($cvType, $allowedCVTypes)) {
                $errors["cv"] = "CV must be PDF, DOC, or DOCX.";
            }
            if ($cvSize > 3000000) {
                $errors["cv"] = "CV exceeds 3MB.";
            }
            break;
        case UPLOAD_ERR_NO_FILE:
            $errors["cv"] = "Please upload a CV.";
            break;
        default:
            $errors["cv"] = "Error uploading CV.";
    }

    // If no errors, save session and move files
    if (empty($errors)) {

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        if (move_uploaded_file($picTmp, $targetPic) && move_uploaded_file($cvTmp, $targetCV)) {

            $hashedPassword = password_hash($rawPassword, PASSWORD_DEFAULT);

            $_SESSION["user"] = [
                "name" => $values["name"],
                "email" => $values["email"],
                "password" => $hashedPassword,
                "address" => $values["address"],
                "telephone" => $values["telephone"],
                "gender" => $values["gender"],
                "city" => $values["city"],
                "state" => $values["state"],
                "profile_pic" => $targetPic,
                "cv" => $targetCV
            ];

            $_SESSION["success_message"] = "Registration successful! Please log in.";
            header("Location: login.php");
            exit();

        } else {
            $message = "<p class='error'>File upload failed.</p>";
        }

    } else {
        // Prepare error message
        $message = "<ul class='error'>";
        foreach ($errors as $err) {
            $message .= "<li>$err</li>";
        }
        $message .= "</ul>";
    }

}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Register | Job Application Portal</title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body>

    <header class="header">
        <img src="images/logo_1.png" alt="Logo" class="logo">
        <h1>Job Application Portal</h1>
    </header>

    <nav>
        <ul>
            <li><a href="index.php">Home</a></li>
            <li><a href="register.php">Register</a></li>
            <li><a href="login.php">Login</a></li>
        </ul>
    </nav>

    <section>
        <h2>User Registration</h2>

            <?php echo $message; ?>

        <form method="POST" enctype="multipart/form-data">

            <label>Full Name</label>
            <input type="text" name="name" value="<?php echo htmlspecialchars($values['name']); ?>" required>

            <label>Email Address</label>
            <input type="email" name="email" value="<?php echo htmlspecialchars($values['email']); ?>" required>

            <label>Password</label>
            <input type="password" name="password" required>

            <label>Confirm Password</label>
            <input type="password" name="confirm_password" required>

            <label>Address</label>
            <input type="text" name="address" value="<?php echo htmlspecialchars($values['address']); ?>" required>

            <label>Telephone</label>
            <input type="text" name="telephone" value="<?php echo htmlspecialchars($values['telephone']); ?>" required>

            <label>Gender</label>
            <select name="gender" required>
                <option value="">Select Gender</option>
                <option value="male" <?php if ($values['gender'] == "male")
                    echo "selected"; ?>>Male</option>
                <option value="female" <?php if ($values['gender'] == "female")
                    echo "selected"; ?>>Female</option>
                <option value="other" <?php if ($values['gender'] == "other")
                    echo "selected"; ?>>Other</option>
            </select>

            <label>State</label>
            <input type="text" name="state" value="<?php echo htmlspecialchars($values['state']); ?>" required>

            <label>City</label>
            <input type="text" name="city" value="<?php echo htmlspecialchars($values['city']); ?>" required>

            <label>Profile / Passport Picture</label>
            <input type="file" name="document" required>

            <label>Upload CV (PDF/DOC/DOCX)</label>
            <input type="file" name="cv" required>

            <input type="submit" value="Register">

        </form>
    </section>

    <footer>
        <p>&copy; <?php echo date("Y"); ?> Job Application Portal</p>
    </footer>

</body>

</html>