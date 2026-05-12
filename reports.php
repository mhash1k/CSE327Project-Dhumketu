<?php
include 'check_login.php';
include 'db.php';
include 'header.php';

/*
 * SQL Feature: HAVING clause demonstration
 * Shows classes with more than a certain number of students
 */

?>

<h2>Class Reports</h2>

<a href="index.php">Back to Dashboard</a>
<br>

<h3>Classes with 2+ Students</h3>
<table>
    <tr>
        <th>Class Name</th>
        <th>Student Count</th>
    </tr>
    <?php
    /*
     * SQL Feature: GROUP BY with HAVING
     * Filters grouped results to show only classes with student count >= 2
     */
    $sql = "SELECT c.class_name, COUNT(s.student_id) as student_count 
            FROM classes c 
            INNER JOIN students s ON c.class_id = s.class_id 
            GROUP BY c.class_id, c.class_name 
            HAVING COUNT(s.student_id) >= 2 
            ORDER BY student_count DESC";
    
    $result = $conn->query($sql);
    
    if ($result && $result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>".$row['class_name']."</td>
                    <td>".$row['student_count']."</td>
                  </tr>";
        }
    } else {
        echo "<tr><td colspan='2'>No classes found with 2+ students</td></tr>";
    }
    ?>
</table>

<h3>Using VIEW: Student Information</h3>
<table>
    <tr>
        <th>Student Name</th>
        <th>Roll Number</th>
        <th>Class</th>
        <th>Section</th>
    </tr>
    <?php
    /*
     * SQL Feature: Using a VIEW
     * v_student_info simplifies the query by encapsulating the JOIN logic
     */
    $view_result = $conn->query("SELECT * FROM v_student_info LIMIT 10");
    
    if ($view_result && $view_result->num_rows > 0) {
        while($row = $view_result->fetch_assoc()) {
            echo "<tr>
                    <td>".$row['student_name']."</td>
                    <td>".$row['roll_number']."</td>
                    <td>".$row['class_name']."</td>
                    <td>".$row['section_name']."</td>
                  </tr>";
        }
    } else {
        echo "<tr><td colspan='4'>No data available</td></tr>";
    }
    ?>
</table>

<h3>Students with Incomplete Information (IS NULL)</h3>
<table>
    <tr>
        <th>Student ID</th>
        <th>Name</th>
        <th>Missing</th>
    </tr>
    <?php
    /*
     * SQL Feature: IS NULL
     * Finds students with missing class or section assignments
     */
    $null_query = $conn->query("SELECT student_id, name, class_id, section_id 
                                FROM students 
                                WHERE class_id IS NULL OR section_id IS NULL");
    
    if ($null_query && $null_query->num_rows > 0) {
        while($row = $null_query->fetch_assoc()) {
            $missing = [];
            if($row['class_id'] == NULL) $missing[] = 'Class';
            if($row['section_id'] == NULL) $missing[] = 'Section';
            
            echo "<tr>
                    <td>".$row['student_id']."</td>
                    <td>".$row['name']."</td>
                    <td>".implode(', ', $missing)."</td>
                  </tr>";
        }
    } else {
        echo "<tr><td colspan='3'>All students have complete information</td></tr>";
    }
    ?>
</table>

<h3>Unique Classes with Students (DISTINCT)</h3>
<ul>
<?php
/*
 * SQL Feature: DISTINCT
 * Lists unique class names that have at least one student
 */
$distinct_query = $conn->query("SELECT DISTINCT c.class_name 
                                 FROM classes c 
                                 INNER JOIN students s ON c.class_id = s.class_id 
                                 ORDER BY c.class_name");

if ($distinct_query && $distinct_query->num_rows > 0) {
    while($row = $distinct_query->fetch_assoc()) {
        echo "<li>".$row['class_name']."</li>";
    }
} else {
    echo "<li>No classes found</li>";
}
?>
</ul>

<br>
<a href="index.php">Back to Dashboard</a>

<?php include 'footer.php'; ?>
