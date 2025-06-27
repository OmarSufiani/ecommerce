<?php
session_start();
require '../db.php';

if (!isset($_SESSION['username'])) {
    header("Location: ../index.php");
    exit();
}

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $role = $_POST['role'];
    $password = !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_BCRYPT) : null;

    if (empty($username) || empty($email) || empty($role)) {
        $message = "Error: All fields except password are required.";
    } else {
        if ($id > 0) {
            if ($password) {
                $sql = "UPDATE users SET username = ?, email = ?, role = ?, password = ? WHERE id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ssssi", $username, $email, $role, $password, $id);
            } else {
                $sql = "UPDATE users SET username = ?, email = ?, role = ? WHERE id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("sssi", $username, $email, $role, $id);
            }
            if ($stmt->execute()) {
                $message = "✅ User updated successfully.";
            } else {
                $message = "❌ Update failed: " . $stmt->error;
            }
            $stmt->close();
        } else {
            if (!$password) {
                $message = "Error: Password is required for new users.";
            } else {
                $sql = "INSERT INTO users (username, password, email, role) VALUES (?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ssss", $username, $password, $email, $role);
                if ($stmt->execute()) {
                    $message = "✅ User added successfully.";
                } else {
                    $message = "❌ Insert failed: " . $stmt->error;
                }
                $stmt->close();
            }
        }
    }
}

$users = $conn->query("SELECT * FROM users ORDER BY id ASC");
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>User Management</title>
<style>
  /* ====== Reset & base ====== */
  *, *::before, *::after {
    box-sizing: border-box;
  }

  body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background: linear-gradient(135deg, #74ebd5 0%, #9face6 100%);
    margin: 0;
    padding: 40px 20px 60px;
    color: #2c3e50;
    min-height: 100vh;
    display: flex;
    justify-content: center;
  }

  .container {
    width: 100%;
    max-width: 960px;
    background: #fff;
    border-radius: 15px;
    padding: 40px 50px 50px;
    box-shadow: 0 15px 40px rgba(0,0,0,0.15);
  }

  h1, h2 {
    font-weight: 700;
    color: #34495e;
    text-align: center;
    margin-bottom: 30px;
    text-transform: uppercase;
    letter-spacing: 2px;
  }

  /* ====== Message Box ====== */
  .message {
    max-width: 960px;
    margin: 0 auto 30px;
    padding: 16px 24px;
    border-radius: 8px;
    font-weight: 700;
    font-size: 1.1rem;
    text-align: center;
    box-shadow: 0 3px 10px rgba(0,0,0,0.1);
    user-select: none;
  }
  .message.success {
    background-color: #2ecc71;
    color: #fff;
  }
  .message.error {
    background-color: #e74c3c;
    color: #fff;
  }
  .message:empty {
    display: none;
  }

  /* ====== Table ====== */
  table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0 14px;
    margin-bottom: 50px;
  }

  thead tr {
    background-color: #2980b9;
    color: #ecf0f1;
    border-radius: 12px;
  }

  thead th {
    padding: 18px 15px;
    font-weight: 700;
    text-align: left;
    font-size: 1rem;
    letter-spacing: 1px;
  }

  tbody tr {
    background: #fff;
    box-shadow: 0 8px 20px rgb(0 0 0 / 0.07);
    border-radius: 12px;
    transition: transform 0.25s ease, box-shadow 0.25s ease;
  }

  tbody tr:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 30px rgb(0 0 0 / 0.12);
  }

  tbody td {
    padding: 18px 15px;
    font-size: 0.95rem;
    color: #34495e;
  }

  /* Buttons */
  a.edit-btn, a.add-btn {
    display: inline-block;
    padding: 8px 18px;
    border-radius: 30px;
    font-weight: 600;
    text-decoration: none;
    transition: background-color 0.3s ease, color 0.3s ease;
    cursor: pointer;
    font-size: 0.9rem;
  }

  a.edit-btn {
    background-color: #3498db;
    color: #fff;
    margin-right: 10px;
    box-shadow: 0 3px 6px rgba(52,152,219,0.4);
  }
  a.edit-btn:hover {
    background-color: #2980b9;
    box-shadow: 0 5px 12px rgba(41,128,185,0.6);
  }

  a.add-btn {
    background-color: #27ae60;
    color: #fff;
    box-shadow: 0 3px 6px rgba(39,174,96,0.4);
  }
  a.add-btn:hover {
    background-color: #1e8449;
    box-shadow: 0 5px 12px rgba(30,132,73,0.6);
  }

  /* ====== Form ====== */
  #userFormContainer {
    max-width: 600px;
    margin: 0 auto 30px;
    padding: 40px 35px 35px;
    border-radius: 20px;
    box-shadow: 0 10px 35px rgba(0,0,0,0.1);
    background: linear-gradient(145deg, #f0f4f8, #d9e2ec);
    display: none;
  }

  #formTitle {
    margin-bottom: 25px;
    font-weight: 700;
    text-align: center;
    letter-spacing: 1.5px;
    color: #34495e;
  }

  form label {
    display: block;
    margin-bottom: 8px;
    font-weight: 700;
    color: #34495e;
    letter-spacing: 0.5px;
    font-size: 1rem;
  }

  form input[type="text"],
  form input[type="email"],
  form input[type="password"],
  form select {
    width: 100%;
    padding: 12px 18px;
    margin-bottom: 25px;
    border: 2px solid #bdc3c7;
    border-radius: 12px;
    font-size: 1rem;
    transition: border-color 0.3s ease, box-shadow 0.3s ease;
    font-weight: 600;
    color: #2c3e50;
  }
  form input[type="text"]:focus,
  form input[type="email"]:focus,
  form input[type="password"]:focus,
  form select:focus {
    border-color: #2980b9;
    box-shadow: 0 0 8px #2980b9aa;
    outline: none;
  }

  #passwordNote {
    font-weight: 400;
    font-size: 0.85rem;
    color: #7f8c8d;
    margin-left: 6px;
  }

  button[type="submit"] {
    width: 100%;
    background-color: #2980b9;
    border: none;
    padding: 16px;
    font-size: 1.2rem;
    color: white;
    font-weight: 700;
    border-radius: 50px;
    cursor: pointer;
    box-shadow: 0 6px 14px rgba(41,128,185,0.5);
    transition: background-color 0.3s ease, box-shadow 0.3s ease;
  }

  button[type="submit"]:hover {
    background-color: #1f6391;
    box-shadow: 0 8px 20px rgba(31,99,145,0.7);
  }

  #cancelBtn {
    margin-top: 15px;
    background: #e74c3c;
    border: none;
    color: #fff;
    padding: 14px;
    font-weight: 700;
    font-size: 1.05rem;
    border-radius: 50px;
    cursor: pointer;
    box-shadow: 0 6px 14px rgba(231,76,60,0.5);
    width: 100%;
    transition: background-color 0.3s ease, box-shadow 0.3s ease;
  }

  #cancelBtn:hover {
    background-color: #c0392b;
    box-shadow: 0 8px 20px rgba(192,57,43,0.7);
  }

  /* ====== Responsive ====== */
  @media (max-width: 720px) {
    .container {
      padding: 30px 25px 40px;
    }

    table thead {
      display: none;
    }

    table, tbody, tr, td {
      display: block;
      width: 100%;
    }

    tr {
      margin-bottom: 20px;
      box-shadow: 0 6px 20px rgba(0,0,0,0.1);
      border-radius: 18px;
      background: #fff;
      padding: 20px 25px;
    }

    td {
      padding: 10px 0;
      position: relative;
      padding-left: 50%;
      border: none;
      border-bottom: 1px solid #eee;
      font-size: 1rem;
    }

    td:last-child {
      border-bottom: 0;
    }

    td:before {
      position: absolute;
      top: 14px;
      left: 20px;
      width: 45%;
      padding-right: 10px;
      white-space: nowrap;
      font-weight: 700;
      color: #2980b9;
      content: attr(data-label);
      font-size: 0.9rem;
    }

    a.edit-btn, a.add-btn {
      font-size: 1rem;
      padding: 10px 20px;
      border-radius: 40px;
    }
  }
</style>
</head>
<body>

<div class="container">
  <h1>User Management</h1>

  <?php if ($message): ?>
    <div class="message <?php echo strpos($message, 'Error') !== false ? 'error' : 'success' ?>">
      <?php echo htmlspecialchars($message); ?>
    </div>
  <?php endif; ?>

  <!-- Add User button on top -->
  <div style="text-align: right; margin-bottom: 25px;">
    <a href="#" class="add-btn" id="addUserBtnTop">+ Add New User</a>
     <a href="index.php" class="add-btn" >Go back</a>
  </div>

  <table>
    <thead>
      <tr>
        <th>ID</th>
        <th>Username</th>
        <th>Email</th>
        <th>Role</th>
        <th style="min-width: 140px;">Actions</th>
      </tr>
    </thead>
    <tbody>
    <?php while ($user = $users->fetch_assoc()): ?>
      <tr>
        <td data-label="ID"><?php echo $user['id'] ?></td>
        <td data-label="Username"><?php echo htmlspecialchars($user['username']) ?></td>
        <td data-label="Email"><?php echo htmlspecialchars($user['email']) ?></td>
        <td data-label="Role"><?php echo ucfirst($user['role']) ?></td>
        <td data-label="Actions">
          <a href="#" class="edit-btn" 
             data-id="<?php echo $user['id'] ?>"
             data-username="<?php echo htmlspecialchars($user['username'], ENT_QUOTES) ?>"
             data-email="<?php echo htmlspecialchars($user['email'], ENT_QUOTES) ?>"
             data-role="<?php echo $user['role'] ?>"
             >Edit</a>
        </td>
      </tr>
    <?php endwhile; ?>
    </tbody>
  </table>

  <div id="userFormContainer">
    <h2 id="formTitle">Add New User</h2>
    <form method="POST" action="" id="userForm" autocomplete="off">
      <input type="hidden" name="id" id="userId" value="0">
      <label for="username">Username</label>
      <input id="username" type="text" name="username" placeholder="Enter username" required>

      <label for="email">Email</label>
      <input id="email" type="email" name="email" placeholder="Enter email address" required>

      <label for="password">
        Password <span id="passwordNote">(required)</span>
      </label>
      <input id="password" type="password" name="password" placeholder="Enter password" required>

      <label for="role">Role</label>
      <select id="role" name="role" required>
        <option value="user">User</option>
        <option value="admin">Admin</option>
      </select>

      <button type="submit">Submit</button>
      <button type="button" id="cancelBtn">Cancel</button>
    </form>
  </div>
</div>

<script>
  const formContainer = document.getElementById('userFormContainer');
  const formTitle = document.getElementById('formTitle');
  const userForm = document.getElementById('userForm');
  const userIdInput = document.getElementById('userId');
  const usernameInput = document.getElementById('username');
  const emailInput = document.getElementById('email');
  const passwordInput = document.getElementById('password');
  const roleSelect = document.getElementById('role');
  const passwordNote = document.getElementById('passwordNote');
  const cancelBtn = document.getElementById('cancelBtn');

  // Show form for adding new user
  function showAddUserForm() {
    formTitle.textContent = 'Add New User';
    userIdInput.value = 0;
    usernameInput.value = '';
    emailInput.value = '';
    passwordInput.value = '';
    passwordInput.required = true;
    passwordNote.textContent = '(required)';
    roleSelect.value = 'user';
    formContainer.style.display = 'block';
    usernameInput.focus();
    window.scrollTo({top: formContainer.offsetTop - 20, behavior: 'smooth'});
  }

  // Show form for editing user with data filled in
  function showEditUserForm(id, username, email, role) {
    formTitle.textContent = `Edit User #${id}`;
    userIdInput.value = id;
    usernameInput.value = username;
    emailInput.value = email;
    passwordInput.value = '';
    passwordInput.required = false;
    passwordNote.textContent = '(leave blank to keep current)';
    roleSelect.value = role;
    formContainer.style.display = 'block';
    usernameInput.focus();
    window.scrollTo({top: formContainer.offsetTop - 20, behavior: 'smooth'});
  }

  // Hide the form
  function hideForm() {
    formContainer.style.display = 'none';
  }

  // Attach event listeners to Edit buttons
  document.querySelectorAll('.edit-btn').forEach(btn => {
    btn.addEventListener('click', e => {
      e.preventDefault();
      const id = btn.getAttribute('data-id');
      const username = btn.getAttribute('data-username');
      const email = btn.getAttribute('data-email');
      const role = btn.getAttribute('data-role');
      showEditUserForm(id, username, email, role);
    });
  });

  // Add user buttons
  document.getElementById('addUserBtnTop').addEventListener('click', e => {
    e.preventDefault();
    showAddUserForm();
  });

  // Cancel button hides the form
  cancelBtn.addEventListener('click', e => {
    e.preventDefault();
    hideForm();
  });
</script>

</body>
</html>
