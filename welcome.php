<?php
include('session.php');
// DB connection setup
$servername = "localhost";
$username = "root";
$password = "onelasalle"; // Set your password if needed
$dbname = "db2";
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle absence_slip status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_id']) && isset($_POST['new_status'])) {
    $id = intval($_POST['update_id']);
    $new_status = $_POST['new_status'];
    
    // Validate status values
    if (in_array($new_status, ['accepted', 'rejected'])) {
        $stmt = $conn->prepare("UPDATE absence_slip SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $new_status, $id);
        $stmt->execute();
        $stmt->close();
    }
}

// Fetch all table names
$tables = [];
$table_result = $conn->query("SHOW TABLES");
while ($row = $table_result->fetch_array()) {
    $tables[] = $row[0];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="css/admin_style.css">
    <style>
        .action-buttons {
            display: flex;
            gap: 5px;
            flex-wrap: wrap;
        }
        
        .action-btn {
            padding: 6px 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 12px;
            font-weight: bold;
            display: flex;
            align-items: center;
            gap: 4px;
            transition: all 0.3s ease;
            min-width: 70px;
            justify-content: center;
        }
        
        .action-btn.accept {
            background-color: #28a745;
            color: white;
        }
        
        .action-btn.accept:hover {
            background-color: #218838;
            transform: translateY(-1px);
        }
        
        .action-btn.reject {
            background-color: #dc3545;
            color: white;
        }
        
        .action-btn.reject:hover {
            background-color: #c82333;
            transform: translateY(-1px);
        }
        
        .status-badge.status-accepted {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .status-badge.status-rejected {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .status-badge.status-pending {
            background-color: #fff3cd;
            color: #856404;
            border: 1px solid #ffeaa7;
        }
        
        .status-badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }
    </style>
</head>
<body>
    <div class="dashboard">
        <!-- Header -->
        <header class="header">
            <h1><i class="fas fa-database"></i> <span>Database View</span></h1>
            <div class="user-menu">
                <div class="user-info">
                    <div class="user-avatar">
                        <?php echo substr($login_session, 0, 1); ?>
                    </div>
                    <div class="user-name"><?php echo $login_session; ?></div>
                </div>
                <a href="logout.php" class="logout-btn">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Sign Out</span>
                </a>
            </div>
        </header>

        <!-- Main Content -->
        <main class="content">
            <?php foreach ($tables as $table): ?>
                <div class="table-card">
                    <div class="table-card-header">
                        <h2 class="table-card-title">
                            <i class="fas fa-table"></i> 
                            <?php echo htmlspecialchars($table); ?>
                        </h2>
                    </div>
                    <div class="table-card-content">
                        <?php
                        $result = $conn->query("SELECT * FROM `$table`");
                        if ($result && $result->num_rows > 0):
                            echo "<table class='data-table sortable-table' id='table-" . htmlspecialchars($table) . "'><thead><tr>";
                            
                            // Print table headers
                            $first_row = $result->fetch_assoc();
                            $column_index = 0;
                            foreach (array_keys($first_row) as $col) {
                                echo "<th data-column='" . $column_index . "'>" . htmlspecialchars($col) . "</th>";
                                $column_index++;
                            }
                            
                            // Add Action column for absence_slip
                            if ($table === 'absence_slip') {
                                echo "<th>Action</th>";
                            }
                            
                            echo "</tr></thead><tbody>";
                            
                            // Rewind and display all rows
                            $result->data_seek(0);
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr>";
                                foreach ($row as $key => $val) {
                                    // Special handling for status field
                                    if (strtolower($key) === 'status') {
                                        $status_lower = strtolower($val);
                                        $status_class = 'status-' . $status_lower;
                                        $icon = '';
                                        
                                        switch($status_lower) {
                                            case 'accepted':
                                                $icon = '<i class="fas fa-check-circle"></i>';
                                                break;
                                            case 'rejected':
                                                $icon = '<i class="fas fa-times-circle"></i>';
                                                break;
                                            case 'pending':
                                            default:
                                                $icon = '<i class="fas fa-clock"></i>';
                                                $status_class = 'status-pending';
                                                break;
                                        }
                                        
                                        echo "<td><span class='status-badge " . $status_class . "'>" . $icon . " " . htmlspecialchars($val) . "</span></td>";
                                    } else {
                                        echo "<td>" . htmlspecialchars($val) . "</td>";
                                    }
                                }
                                
                                // Action column for absence_slip
                                if ($table === 'absence_slip') {
                                    echo "<td>";
                                    $current_status = strtolower($row['status']);
                                    
                                    if ($current_status == 'pending') {
                                        echo '<div class="action-buttons">
                                                <form method="POST" style="display: inline;">
                                                    <input type="hidden" name="update_id" value="' . intval($row['id']) . '">
                                                    <input type="hidden" name="new_status" value="accepted">
                                                    <button type="submit" class="action-btn accept">
                                                        <i class="fas fa-check"></i> Accept
                                                    </button>
                                                </form>
                                                <form method="POST" style="display: inline;">
                                                    <input type="hidden" name="update_id" value="' . intval($row['id']) . '">
                                                    <input type="hidden" name="new_status" value="rejected">
                                                    <button type="submit" class="action-btn reject">
                                                        <i class="fas fa-times"></i> Reject
                                                    </button>
                                                </form>
                                              </div>';
                                    } else {
                                        $icon = $current_status == 'accepted' ? '<i class="fas fa-check-circle"></i>' : '<i class="fas fa-times-circle"></i>';
                                        $status_text = ucfirst($current_status);
                                        $status_class = 'status-' . $current_status;
                                        echo '<span class="status-badge ' . $status_class . '">' . $icon . ' ' . $status_text . '</span>';
                                    }
                                    echo "</td>";
                                }
                                
                                echo "</tr>";
                            }
                            
                            echo "</tbody></table>";
                        else:
                            echo "<div class='empty-state'>
                                    <i class='fas fa-database'></i>
                                    <p>No records found in $table.</p>
                                  </div>";
                        endif;
                        ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </main>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize all tables
            document.querySelectorAll('.sortable-table th').forEach(headerCell => {
                if (!headerCell.textContent.includes('Action')) {  // Skip the Action column
                    headerCell.addEventListener('click', () => {
                        const table = headerCell.closest('table');
                        const columnIndex = parseInt(headerCell.dataset.column);
                        const currentIsAscending = headerCell.classList.contains('sort-asc');
                        
                        // Remove sort classes from all headers in this table
                        table.querySelectorAll('th').forEach(th => {
                            th.classList.remove('sort-asc', 'sort-desc');
                        });
                        
                        // Add appropriate sort class
                        if (currentIsAscending) {
                            headerCell.classList.add('sort-desc');
                        } else {
                            headerCell.classList.add('sort-asc');
                        }
                        
                        // Sort the table
                        sortTableByColumn(table, columnIndex, !currentIsAscending);
                    });
                }
            });
        });

        function sortTableByColumn(table, columnIndex, ascending = true) {
            const tbody = table.querySelector('tbody');
            const rows = Array.from(tbody.querySelectorAll('tr'));
            
            // Sort rows based on cell content in the specified column
            const sortedRows = rows.sort((rowA, rowB) => {
                let cellA = rowA.querySelectorAll('td')[columnIndex].textContent.trim();
                let cellB = rowB.querySelectorAll('td')[columnIndex].textContent.trim();
                
                // Check if the values are numbers
                const numA = parseFloat(cellA);
                const numB = parseFloat(cellB);
                
                if (!isNaN(numA) && !isNaN(numB)) {
                    return ascending ? numA - numB : numB - numA;
                } else {
                    // For text comparison, use localeCompare for proper string comparison
                    return ascending 
                        ? cellA.localeCompare(cellB) 
                        : cellB.localeCompare(cellA);
                }
            });
            
            // Remove existing rows
            while (tbody.firstChild) {
                tbody.removeChild(tbody.firstChild);
            }
            
            // Add sorted rows back to the table
            sortedRows.forEach(row => tbody.appendChild(row));
        }
    </script>
</body>
</html>
<?php $conn->close(); ?>