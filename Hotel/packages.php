<?php
include("db.php");

// Fetch all packages
$packages = mysqli_query($conn, "SELECT * FROM packages");
?>
<h2>Select a Package</h2>
<form method="GET" action="hotels.php">
    <select name="pid" onchange="this.form.submit()">
        <option value="">-- Choose Package --</option>
        <?php while($p = mysqli_fetch_assoc($packages)) { ?>
            <option value="<?= $p['id'] ?>"><?= $p['title'] ?></option>
        <?php } ?>
    </select>
</form>
