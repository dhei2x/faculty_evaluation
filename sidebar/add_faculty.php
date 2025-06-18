 ess = "Faculty added successfully!";
    } catch (PDOException $e) {
        $errors[] = "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Faculty</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="p-8 bg-gray-50">
    <div class="max-w-lg mx-auto bg-white p-6 rounded shadow">
        <h2 class="text-2xl font-bold mb-4">Add Faculty</h2>
        <?php if ($success): ?>
            <div class="text-green-600 mb-4"><?= $success ?></div>
        <?php endif; ?>
        <?php foreach ($errors as $error): ?>
            <div class="text-red-600 mb-2"><?= $error ?></div>
        <?php endforeach; ?>
        <form method="POST">
            <input name="username" placeholder="Username" class="block w-full p-2 border rounded mb-3" required>
            <input name="email" type="email" placeholder="Email" class="block w-full p-2 border rounded mb-3" required>
            <input name="password" type="password" placeholder="Password" class="block w-full p-2 border rounded mb-3" required>
            <input name="full_name" placeholder="Full Name" class="block w-full p-2 border rounded mb-3" required>
            <input name="department" placeholder="Department" class="block w-full p-2 border rounded mb-3" required>
            <input name="position" placeholder="Position" class="block w-full p-2 border rounded mb-3" required>
            <button class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Add Faculty</button>
            <a href="students.php" class="ml-2 text-gray-600">Cancel</a>
        </form>
    </div>
</body>
</html>
