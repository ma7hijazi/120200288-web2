<?php
session_start();

// Our product list
$products = array(
    array('id' => 1, 'name' => 'Smartphone', 'description' => 'Latest model smartphone', 'price' => 799.99, 'category' => 'Electronics'),
    array('id' => 2, 'name' => 'Notebook', 'description' => 'Hardcover lined notebook', 'price' => 8.99, 'category' => 'Office')
);

// Set up our variables
$errors = array();
$submittedData = array();

// Check if there's a success message from last time
if (isset($_SESSION['success_msg'])) {
    $successMessage = $_SESSION['success_msg'];
    unset($_SESSION['success_msg']); // Clear it so it doesn't show again
} else {
    $successMessage = '';
}

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // Get the form data
    $submittedData['name'] = trim($_POST['name'] ?? '');
    $submittedData['description'] = trim($_POST['description'] ?? '');
    $submittedData['price'] = trim($_POST['price'] ?? '');
    $submittedData['category'] = trim($_POST['category'] ?? '');
    
    // Check for errors
    if (empty($submittedData['name'])) $errors['name'] = "Product name is required.";
    if (empty($submittedData['description'])) $errors['description'] = "Description is required.";
    if (empty($submittedData['price'])) {
        $errors['price'] = "Price is required.";
    } else if (!is_numeric($submittedData['price'])) {
        $errors['price'] = "Price must be a number.";
    } else if ($submittedData['price'] < 0) {
        $errors['price'] = "Price can't be negative.";
    }
    if (empty($submittedData['category'])) $errors['category'] = "Please select a category.";
    
    // If no errors, add the product
    if (empty($errors)) {
        // Find the next ID
        $nextId = 1;
        if (!empty($products)) {
            $maxId = 0;
            foreach ($products as $prod) {
                if ($prod['id'] > $maxId) $maxId = $prod['id'];
            }
            $nextId = $maxId + 1;
        }
        
        // Create new product
        $newProduct = array(
            'id' => $nextId,
            'name' => $submittedData['name'],
            'description' => $submittedData['description'],
            'price' => (float)$submittedData['price'],
            'category' => $submittedData['category']
        );
        
        // Add to our list
        $products[] = $newProduct;
        
        // Save success message in session and reload the page
        $_SESSION['success_msg'] = "Great! '" . $newProduct['name'] . "' was added!";
        header("Location: " . $_SERVER['PHP_SELF']); // This reloads the page
        exit(); // Stop the script here
    }
    // If there were errors, we just continue and show them below
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Simple Product Manager</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #fffaf0; padding: 15px; }
        .alert { margin-bottom: 20px; }
        .form-label { font-weight: 500; }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="text-center border-bottom pb-2 mb-3">Simple Product Manager</h1>

        <!-- Success Message -->
        <?php if (!empty($successMessage)): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <strong>Success!</strong> <?php echo $successMessage; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <!-- Error Message -->
        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>Please fix:</strong> There were some problems with your input.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="row">
            <!-- Form Column -->
            <div class="col-md-5">
                <h4>Add Product</h4>
                <form method="post">
                    <div class="mb-2">
                        <label class="form-label">Name:</label>
                        <input type="text" name="name" class="form-control <?php if(isset($errors['name'])) echo 'is-invalid'; ?>" value="<?php echo htmlspecialchars($submittedData['name'] ?? ''); ?>">
                        <?php if(isset($errors['name'])): ?>
                            <div class="invalid-feedback"><?php echo $errors['name']; ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-2">
                        <label class="form-label">Description:</label>
                        <textarea name="description" class="form-control <?php if(isset($errors['description'])) echo 'is-invalid'; ?>" rows="2"><?php echo htmlspecialchars($submittedData['description'] ?? ''); ?></textarea>
                        <?php if(isset($errors['description'])): ?>
                            <div class="invalid-feedback"><?php echo $errors['description']; ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-2">
                        <label class="form-label">Price ($):</label>
                        <input type="number" step="0.01" name="price" class="form-control <?php if(isset($errors['price'])) echo 'is-invalid'; ?>" value="<?php echo htmlspecialchars($submittedData['price'] ?? ''); ?>">
                        <?php if(isset($errors['price'])): ?>
                            <div class="invalid-feedback"><?php echo $errors['price']; ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Category:</label>
                        <select name="category" class="form-select <?php if(isset($errors['category'])) echo 'is-invalid'; ?>">
                            <option value="">-- Choose --</option>
                            <option value="Electronics" <?php if (isset($submittedData['category']) && $submittedData['category'] == 'Electronics') echo 'selected'; ?>>Electronics</option>
                            <option value="Home" <?php if (isset($submittedData['category']) && $submittedData['category'] == 'Home') echo 'selected'; ?>>Home</option>
                            <option value="Clothing" <?php if (isset($submittedData['category']) && $submittedData['category'] == 'Clothing') echo 'selected'; ?>>Clothing</option>
                            <option value="Office" <?php if (isset($submittedData['category']) && $submittedData['category'] == 'Office') echo 'selected'; ?>>Office</option>
                        </select>
                        <?php if(isset($errors['category'])): ?>
                            <div class="invalid-feedback"><?php echo $errors['category']; ?></div>
                        <?php endif; ?>
                    </div>

                    <button type="submit" class="btn btn-success">Add Product</button>
                </form>
            </div>

            <!-- Table Column -->
            <div class="col-md-7">
                <h4>Current Products</h4>
                <?php if (!empty($products)): ?>
                    <div class="table-responsive">
                        <table class="table table-sm table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Description</th>
                                    <th>Price</th>
                                    <th>Category</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($products as $item): ?>
                                    <tr>
                                        <td><?php echo $item['id']; ?></td>
                                        <td><?php echo htmlspecialchars($item['name']); ?></td>
                                        <td><?php echo htmlspecialchars($item['description']); ?></td>
                                        <td>$<?php echo number_format($item['price'], 2); ?></td>
                                        <td><?php echo htmlspecialchars($item['category']); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-muted">No products in inventory.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
