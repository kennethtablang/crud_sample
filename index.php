<?php
// ============================================================
// STEP 2: Connect to the database
// We include db.php which already has our $conn variable ready
// ============================================================
require_once 'db.php';

// ============================================================
// STEP 3: Fetch all students from the database
// We write a SQL query and run it using $conn
// ============================================================
$sql    = "SELECT * FROM students ORDER BY id DESC";
$result = $conn->query($sql);
?>

<?php
// ============================================================
// Include header.php — this loads our <head>, stylesheets,
// and navbar so we don't have to repeat them on every page
// ============================================================
require_once 'header.php';
?>

    <!-- ===== MAIN CONTENT ===== -->
    <main class="container my-4">

        <h2 class="mb-3">Student List</h2>

        <!-- ============================================================
             STEP 4: Display the students in a table
             We check first if there are any results, then loop through
             ============================================================ -->

        <?php if ($result->num_rows > 0): ?>

            <!-- There ARE students — show the table -->
            <table class="table table-bordered table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Email</th>
                        <th>Course</th>
                        <th>Year</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
    
                    <?php
                    $counter = 1; // This is just a row counter for display (#)
                    while ($row = $result->fetch_assoc()):
                    ?>
                        <tr>
                            <td><?= $counter++ ?></td>
                            <td><?= htmlspecialchars($row['firstname']) ?></td>
                            <td><?= htmlspecialchars($row['lastname']) ?></td>
                            <td><?= htmlspecialchars($row['email']) ?></td>
                            <td><?= htmlspecialchars($row['course']) ?></td>
                            <td><?= htmlspecialchars($row['year']) ?></td>
                            <td>
                                <!-- Edit button — passes the student's ID to update.php -->
                                <a href="update.php?id=<?= $row['id'] ?>" class="btn btn-warning btn-sm">Edit</a>

                                <!-- Delete button — passes the student's ID to delete.php -->
                                <a href="delete.php?id=<?= $row['id'] ?>" class="btn btn-danger btn-sm"
                                onclick="return confirm('Are you sure you want to delete this student?')">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>

                </tbody>
            </table>

        <?php else: ?>

            <!-- There are NO students yet — show a friendly message -->
            <div class="alert alert-info">
                No students found. <a href="add.php">Add the first one!</a>
            </div>

        <?php endif; ?>

    </main>
    <!-- ===== END MAIN CONTENT ===== -->

    <!-- ===== FOOTER ===== -->
    <footer class="bg-dark text-white text-center py-3">
        <small>Student CRUD App &copy; 2025</small>
    </footer>
    <!-- ===== END FOOTER ===== -->

</body>
</html>
