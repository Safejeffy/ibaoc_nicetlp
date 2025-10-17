<?php
require_once 'config/Database.php';
require_once 'models/City.php';

// Initialize database connection
$database = new Database();
$db = $database->connect();

// Initialize city object
$city = new City($db);

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'add') {
            $city->name = $_POST['name'];
            $city->description = $_POST['description'];
            $city->create();
        } elseif ($_POST['action'] === 'edit') {
            $city->id = $_POST['id'];
            $city->name = $_POST['name'];
            $city->description = $_POST['description'];
            $city->update();
        }
    }
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

// Get city details if editing
$currentCity = null;
if (isset($_GET['id'])) {
    $city->id = $_GET['id'];
    if ($city->read_single()) {
        $currentCity = [
            'id' => $city->id,
            'name' => $city->name,
            'description' => $city->description
        ];
    }
}

// Get all cities
$result = $city->read();
$cities = $result->fetchAll(PDO::FETCH_ASSOC);
?>
<!-- City management fragment - styled for NiceAdmin/Bootstrap -->
<div class="card">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="card-title mb-0">Cities</h5>
            <a href="?add=true" class="btn btn-primary btn-sm">Add New City</a>
        </div>

        <div class="row">
            <div class="col-md-5">
                <div class="list-group">
                    <?php foreach($cities as $cityItem): ?>
                        <a href="?id=<?php echo $cityItem['id']; ?>" class="list-group-item list-group-item-action <?php echo (isset($_GET['id']) && $_GET['id'] == $cityItem['id']) ? 'active' : ''; ?>">
                            <?php echo htmlspecialchars($cityItem['name']); ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="col-md-7">
                <?php if($currentCity): ?>
                    <?php if(isset($_GET['edit'])): ?>
                        <h6>Edit City</h6>
                        <form method="POST">
                            <input type="hidden" name="action" value="edit">
                            <input type="hidden" name="id" value="<?php echo $currentCity['id']; ?>">

                            <div class="mb-3">
                                <label for="name" class="form-label">City Name</label>
                                <input type="text" id="name" name="name" class="form-control" required value="<?php echo htmlspecialchars($currentCity['name']); ?>">
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea id="description" name="description" class="form-control" rows="4" required><?php echo htmlspecialchars($currentCity['description']); ?></textarea>
                            </div>

                            <button type="submit" class="btn btn-success btn-sm">Update City</button>
                            <a href="?id=<?php echo $currentCity['id']; ?>" class="btn btn-secondary btn-sm ms-2">Cancel</a>
                        </form>
                    <?php else: ?>
                        <h5 class="mb-1"><?php echo htmlspecialchars($currentCity['name']); ?></h5>
                        <p class="mb-2"><?php echo nl2br(htmlspecialchars($currentCity['description'])); ?></p>
                        <a href="?id=<?php echo $currentCity['id']; ?>&amp;edit=true" class="btn btn-outline-primary btn-sm">Edit</a>
                    <?php endif; ?>
                <?php elseif(isset($_GET['add'])): ?>
                    <h6>Add New City</h6>
                    <form method="POST">
                        <input type="hidden" name="action" value="add">

                        <div class="mb-3">
                            <label for="name" class="form-label">City Name</label>
                            <input type="text" id="name" name="name" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea id="description" name="description" class="form-control" rows="4" required></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary btn-sm">Add City</button>
                        <a href="<?php echo $_SERVER['PHP_SELF']; ?>" class="btn btn-secondary btn-sm ms-2">Cancel</a>
                    </form>
                <?php else: ?>
                    <h6>Welcome to City Management</h6>
                    <p>Select a city from the list to view details or click "Add New City" to create a new entry.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

