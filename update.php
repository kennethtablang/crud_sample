<?php
// ============================================================
// STEP 1: Connect to the database
// ============================================================
require_once 'db.php';

// ============================================================
// STEP 2: Get the student's ID from the URL
// When the user clicks "Edit" in index.php, the link looks like:
//   update.php?id=5
// $_GET['id'] reads that number from the URL.
// If there is no id, we send the user back to the list.
// ============================================================
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($id <= 0) {
    // (int) converts the value to a whole number.
    // If someone visits update.php with no id, redirect them away.
    header('Location: index.php');
    exit();
}

// ============================================================
// STEP 3: Fetch the existing student record from the database
// We need their current data so we can pre-fill the form.
// ============================================================
$stmt = $conn->prepare("SELECT * FROM students WHERE id = ?");
$stmt->bind_param('i', $id);   // 'i' = integer
$stmt->execute();
$result = $stmt->get_result();
$student = $result->fetch_assoc(); // fetch_assoc() returns one row as an array
$stmt->close();

// If no student was found with that ID, go back to the list
if (!$student) {
    header('Location: index.php');
    exit();
}

// ============================================================
// STEP 4: Pre-fill our variables with the student's current data
// These will be used to populate the form fields below.
// If the form was submitted with errors, they'll hold the new
// (invalid) values instead so the user sees what they typed.
// ============================================================
$firstname = $student['firstname'];
$lastname  = $student['lastname'];
$email     = $student['email'];
$course    = $student['course'];
$year      = $student['year'];

$error = '';

// ============================================================
// STEP 5: Handle the form submission (POST request)
// ============================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // --------------------------------------------------------
    // STEP 6: Collect and sanitise the new values from the form
    // --------------------------------------------------------
    $firstname = trim($_POST['firstname'] ?? '');
    $lastname  = trim($_POST['lastname']  ?? '');
    $email     = trim($_POST['email']     ?? '');
    $course    = trim($_POST['course']    ?? '');
    $year      = trim($_POST['year']      ?? '');

    // --------------------------------------------------------
    // STEP 7: Validate — same rules as add.php
    // --------------------------------------------------------
    if (empty($firstname) || empty($lastname) || empty($email) || empty($course) || empty($year)) {
        $error = 'All fields are required. Please fill in every field.';

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';

    } elseif (!in_array($year, ['1', '2', '3', '4'])) {
        $error = 'Year level must be 1, 2, 3, or 4.';

    } else {
        // --------------------------------------------------------
        // STEP 8: Run the UPDATE query using a prepared statement
        // We update only the row whose id matches our $id variable.
        // --------------------------------------------------------
        $stmt = $conn->prepare(
            "UPDATE students
             SET firstname = ?, lastname = ?, email = ?, course = ?, year = ?
             WHERE id = ?"
        );
        // Bind order must match the ? placeholders above:
        //   s = firstname, s = lastname, s = email, s = course, i = year, i = id
        $stmt->bind_param('ssssii', $firstname, $lastname, $email, $course, $year, $id);

        if ($stmt->execute()) {
            // SUCCESS — redirect back to the student list
            header('Location: index.php');
            exit();
        } else {
            $error = 'Could not update the student. The email may already be in use.';
        }

        $stmt->close();
    }
}
?>

<?php require_once 'header.php'; ?>

    <!-- ===== MAIN CONTENT ===== -->
    <main class="container my-4">

        <h2 class="mb-3">Edit Student</h2>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <!-- ============================================================
             STEP 9: The edit form
             We send the id back in the URL (?id=...) so this page
             knows WHICH student to update when the form is submitted.
             ============================================================ -->
        <form action="update.php?id=<?= $id ?>" method="POST">

            <!-- First Name -->
            <div class="mb-3">
                <label for="firstname" class="form-label">First Name</label>
                <input
                    type="text"
                    id="firstname"
                    name="firstname"
                    class="form-control"
                    value="<?= htmlspecialchars($firstname) ?>"
                    required
                >
            </div>

            <!-- Last Name -->
            <div class="mb-3">
                <label for="lastname" class="form-label">Last Name</label>
                <input
                    type="text"
                    id="lastname"
                    name="lastname"
                    class="form-control"
                    value="<?= htmlspecialchars($lastname) ?>"
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
                    value="<?= htmlspecialchars($email) ?>"
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
                    value="<?= htmlspecialchars($course) ?>"
                    required
                >
            </div>

            <!-- Year Level -->
            <div class="mb-3">
                <label for="year" class="form-label">Year Level</label>
                <select id="year" name="year" class="form-select" required>
                    <option value="">-- Select Year --</option>
                    <?php for ($y = 1; $y <= 4; $y++): ?>
                        <option value="<?= $y ?>" <?= ($year == $y) ? 'selected' : '' ?>>
                            Year <?= $y ?>
                        </option>
                    <?php endfor; ?>
                </select>
            </div>

            <!-- Action Buttons -->
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-warning">Update Student</button>
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
