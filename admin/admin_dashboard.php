<?php
/* ---------- admin_dashboard.php ---------- */
session_start();
require '../db_connect.php';

/* SIMPLE ADMIN CHECK (adjust as needed) */
if (!isset($_SESSION['user_id']) || empty($_SESSION['is_admin'])) {
  header('Location: admin_login.php'); exit();
}

$itemsRes = $conn->query("SELECT items.*, users.name AS uploader_name 
                          FROM items 
                          JOIN users ON users.id = items.user_id 
                          ORDER BY items.created_at DESC");

/* Fetch users for Manage‑Users tab */
$usersRes = $conn->query("SELECT id, name, email, phone, points, created_at 
                          FROM users ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin Panel – ReWear</title>
<style>
/* === BASIC ADMIN STYLES === */
body{font-family:'Segoe UI',sans-serif;margin:0;background:#f4f6f8}
.top-bar{background:#24292e;color:#fff;padding:12px 24px;display:flex;justify-content:space-between;align-items:center}
.top-bar a{color:#fff;text-decoration:none;margin-left:12px;font-weight:500}

.nav-tabs{display:flex;gap:10px;padding:12px;background:#fff;border-bottom:2px solid #e0e0e0}
.nav-tabs button{flex:1;padding:10px;border:none;border-radius:6px;background:#e0e0e0;cursor:pointer;font-weight:600}
.nav-tabs button.active{background:#1e8e3e;color:#fff}

.tab-content{padding:20px}

.card{display:flex;align-items:center;background:#fff;margin-bottom:15px;padding:15px;border-radius:10px;box-shadow:0 1px 4px rgba(0,0,0,.08)}
.avatar{width:60px;height:60px;border-radius:50%;background:#ddd url('assets/images/default_avatar.png') center/cover no-repeat;margin-right:20px}
.details{flex:1}
.actions{display:flex;flex-direction:column;gap:6px}
.actions button{border:none;padding:6px 12px;border-radius:4px;cursor:pointer;font-weight:600}
.actions .approve{background:#28a745;color:#fff}
.actions .reject{background:#e74c3c;color:#fff}
/* hide non‑active tab panes */
.pane{display:none}
.pane.active{display:block}

/* simple responsive */
@media(max-width:700px){
  .card{flex-direction:column;align-items:flex-start}
  .avatar{margin-bottom:10px}
  .actions{flex-direction:row}
}
</style>
</head>
<body>

<header class="top-bar">
  <h1>Admin Panel</h1>
  <div>
    <a href="rewear.php">Home</a>
    <a href="admin_logout.php">Logout</a>
  </div>
</header>

<!-- NAV TABS -->
<div class="nav-tabs">
  <button class="tab-btn active" data-target="usersPane">Manage Users</button>
  <button class="tab-btn" data-target="ordersPane">Manage Orders</button>
  <button class="tab-btn" data-target="listingsPane">Manage Listings</button>
</div>

<!-- TAB PANES -->
<div class="tab-content">

  <!-- ===== USERS PANE ===== -->
  <div id="usersPane" class="pane active">
    <?php while($u = $usersRes->fetch_assoc()): ?>
      <div class="card">
        <div class="avatar"></div>
        <div class="details">
          <p><strong><?= htmlspecialchars($u['name']) ?></strong></p>
          <p>Email: <?= htmlspecialchars($u['email']) ?> | Phone: <?= htmlspecialchars($u['phone']?:'N/A') ?></p>
          <p>Points: <?= $u['points'] ?> | Joined: <?= date('d M, Y', strtotime($u['created_at'])) ?></p>
        </div>
        <div class="actions">
          <button class="approve">Action 1</button>
          <button class="reject">Action 2</button>
        </div>
      </div>
    <?php endwhile; ?>
  </div>

  <!-- ===== ORDERS / SWAPS PANE (placeholder) ===== -->
  <div id="ordersPane" class="pane">
    <p>Orders / Swap management coming soon.</p>
  </div>
<!-- ===== LISTINGS PANE ===== -->
<div id="listingsPane" class="pane">
  <?php while($item = $itemsRes->fetch_assoc()): ?>
    <div class="card">
      <div class="avatar" style="background-image:url('<?= htmlspecialchars($item['image_path']) ?>'); background-size:cover;"></div>
      <div class="details">
        <p><strong><?= htmlspecialchars($item['title']) ?></strong> (<?= htmlspecialchars($item['category']) ?>)</p>
        <p>Uploader: <?= htmlspecialchars($item['uploader_name']) ?> | Added: <?= date('d M, Y', strtotime($item['created_at'])) ?></p>
        <p>Status: <strong><?= ucfirst($item['status']) ?></strong></p>
      </div>
      <?php if ($item['status'] === 'pending'): ?>
      <div class="actions">
        <form method="POST" action="admin_action_listing.php" style="display:inline;">
          <input type="hidden" name="item_id" value="<?= $item['id'] ?>">
          <input type="hidden" name="action" value="approved">
          <button class="approve">Approve</button>
        </form>
        <form method="POST" action="admin_action_listing.php" style="display:inline;">
          <input type="hidden" name="item_id" value="<?= $item['id'] ?>">
          <input type="hidden" name="action" value="rejected">
          <button class="reject">Reject</button>
        </form>
      </div>
      <?php endif; ?>
    </div>
  <?php endwhile; ?>
</div>



</div>

<!-- JS TAB SWITCHING -->
<script>
document.querySelectorAll('.tab-btn').forEach(btn=>{
  btn.addEventListener('click',()=>{
    // highlight tab
    document.querySelectorAll('.tab-btn').forEach(b=>b.classList.remove('active'));
    btn.classList.add('active');
    // show pane
    document.querySelectorAll('.pane').forEach(p=>p.classList.remove('active'));
    document.getElementById(btn.dataset.target).classList.add('active');
  });
});
</script>

</body>
</html>
