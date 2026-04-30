<?php
// ============================================================
// STEP 1: Connect to the database
// We need $conn so we can INSERT the new student later
// ============================================================
require_once 'db.php';

// ============================================================
// STEP 2: Set up a variable to hold any error/success messages
// This starts as an empty string — we'll fill it if needed
// ============================================================
$error   = '';
$success = '';

// ============================================================
// STEP 3: Check if the form was submitted
// When the user clicks "Save", the browser sends a POST request.
// $_SERVER['REQUEST_METHOD'] tells us HOW the page was loaded.
// 'GET'  = user just visited the page (show the empty form)
// 'POST' = user submitted the form (process the data)
// ============================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // --------------------------------------------------------
    // STEP 4: Collect and clean the submitted values
    // trim() removes extra spaces from the beginning and end.
    // htmlspecialchars() prevents basic XSS attacks by converting
    // special characters like < > & into safe HTML entities.
    // --------------------------------------------------------
    $firstname = trim($_POST['firstname'] ?? '');
    $lastname  = trim($_POST['lastname']  ?? '');
    $email     = trim($_POST['email']     ?? '');
    $course    = trim($_POST['course']    ?? '');
    $year      = trim($_POST['year']      ?? '');

    // --------------------------------------------------------
    // STEP 5: Basic validation
    // Make sure none of the fields are empty before saving.
    // If a field IS empty, we set an error message and stop.
    // --------------------------------------------------------
    if (empty($firstname) || empty($lastname) || empty($email) || empty($course) || empty($year)) {
        $error = 'All fields are required. Please fill in every field.';

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        // filter_var() checks whether the email looks valid (e.g. has @ and a domain)
        $error = 'Please enter a valid email address.';

    } elseif (!in_array($year, ['1', '2', '3', '4'])) {
        // Make sure year is 1, 2, 3, or 4 — nothing else
        $error = 'Year level must be 1, 2, 3, or 4.';

    } else {
        // --------------------------------------------------------
        // STEP 6: Prepare the SQL INSERT statement
        // We use a PREPARED STATEMENT instead of putting the
        // values directly into the SQL string. This protects
        // against SQL Injection attacks.
        //
        // The ? marks are placeholders — mysqli will safely
        // replace them with the real values in the next step.
        // --------------------------------------------------------
        $stmt = $conn->prepare(
            "INSERT INTO students (firstname, lastname, email, course, year)
             VALUES (?, ?, ?, ?, ?)"
        );

        // --------------------------------------------------------
        // STEP 7: Bind the real values to the placeholders
        // "ssssi" means:
        //   s = string (firstname)
        //   s = string (lastname)
        //   s = string (email)
        //   s = string (course)
        //   i = integer (year)
        // --------------------------------------------------------
        $stmt->bind_param('ssssi', $firstname, $lastname, $email, $course, $year);

        // --------------------------------------------------------
        // STEP 8: Execute the statement (actually run the INSERT)
        // If it works, redirect back to the student list.
        // If it fails, show an error (e.g. duplicate email).
        // --------------------------------------------------------
        if ($stmt->execute()) {
            // SUCCESS — go back to index.php
            // We use header() to redirect, then exit() to stop
            // the rest of this script from running.
            header('Location: index.php');
            exit();
        } else {
            // Something went wrong (e.g. duplicate email address)
            $error = 'Could not save the student. The email may already be in use.';
        }

        // Always close the statement when done
        $stmt->close();
    }
}
?>

<?php
// ============================================================
// STEP 9: Load the shared header (navbar + <head> + Bootstrap)
// ============================================================
require_once 'header.php';
?>

    <!-- ===== MAIN CONTENT ===== -->
    <main class="container my-4">

        <h2 class="mb-3">Add New Student</h2>

        <?php if (!empty($error)): ?>
            <!-- Show a red error banner if something went wrong -->
            <div class="alert alert-danger">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($success)): ?>
            <!-- Show a green success banner (reserved for future use) -->
            <div class="alert alert-success">
                <?= htmlspecialchars($success) ?>
            </div>
        <?php endif; ?>

        <!-- ============================================================
             STEP 10: The HTML form
             action="add.php" — send the data back to THIS same file
             method="POST"    — use POST so the data isn't visible in the URL
             ============================================================ -->
        <form action="add.php" method="POST">

            <!-- First Name -->
            <div class="mb-3">
                <label for="firstname" class="form-label">First Name</label>
                <input
                    type="text"
                    id="firstname"
                    name="firstname"
                    class="form-control"
                    placeholder="e.g. Juan"
                    value="<?= htmlspecialchars($firstname ?? '') ?>"
                    required
                >
                <!-- value="..." re-fills the field if the form had an error,
                     so the user doesn't have to retype everything. -->
            </div>

            <!-- Last Name -->
            <div class="mb-3">
                <label for="lastname" class="form-label">Last Name</label>
                <input
                    type="text"
                    id="lastname"
                    name="lastname"
                    class="form-control"
                    placeholder="e.g. Dela Cruz"
                    value="<?= htmlspecialchars($lastname ?? '') ?>"
                    required
                >
            </div>

            <!-- Email -->
            <div class="mb-3">
                <label for="email" class="form-label">Email Address</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    class="form-control"
                    placeholder="e.g. juan@email.com"
                    value="<?= htmlspecialchars($email ?? '') ?>"
                    required
                >
            </div>

            <!-- Course -->
            <div class="mb-3">
                <label for="course" class="form-label">Course</label>
                <input
                    type="text"
                    id="course"
                    name="course"
                    class="form-control"
                    placeholder="e.g. BS Computer Science"
                    value="<?= htmlspecialchars($course ?? '') ?>"
                    required
                >
            </div>

            <!-- Year Level -->
            <div class="mb-3">
                <label for="year" class="form-label">Year Level</label>
                <select id="year" name="year" class="form-select" required>
                    <option value="">-- Select Year --</option>
                    <?php
                    // Loop through 1 to 4 and mark the previously selected option
                    for ($y = 1; $y <= 4; $y++):
                        $selected = (isset($year) && $year == $y) ? 'selected' : '';
                    ?>
                        <option value="<?= $y ?>" <?= $selected ?>>Year <?= $y ?></option>
                    <?php endfor; ?>
                </select>
            </div>

            <!-- Action Buttons -->
            <div class="d-flex gap-2">
                <!-- Save button submits the form -->
                <button type="submit" class="btn btn-success">Save Student</button>

                <!-- Cancel button goes back to the list without saving -->
                <a href="index.php" class="btn btn-secondary">Cancel</a>
            </div>

        </form>

    </main>
    <!-- ===== END MAIN CONTENT ===== -->

    <!-- ===== FOOTER ===== -->
    <footer class="bg-dark text-white text-center py-3">
        <small>Student CRUD App &copy; 2025</small>
    </footer>
    <!-- ===== END FOOTER ===== -->

</body>
</html>
