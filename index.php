<?php

$pageTitle = 'Компьютерный магазин'; 
$errors = []; 

require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/model.php';

require_once __DIR__ . '/controllers/ProductController.php';
require_once __DIR__ . '/controllers/ClientController.php';
require_once __DIR__ . '/controllers/EmployeeController.php';

$base_url = str_replace('index.php', '', $_SERVER['SCRIPT_NAME']);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?> - Компьютерный магазин</title>
    <link rel="stylesheet" href="<?php echo htmlspecialchars($base_url); ?>style.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
</head>
<body>
    <header>
        <h1>Компьютерный магазин</h1>
        <nav>
            <ul>
                <li><a href="index.php?entity=product&action=list" class="<?php echo ($entity ?? '') == 'product' ? 'active' : ''; ?>">Товары</a></li>
                <li><a href="index.php?entity=client&action=list" class="<?php echo ($entity ?? '') == 'client' ? 'active' : ''; ?>">Клиенты</a></li>
                <li><a href="index.php?entity=employee&action=list" class="<?php echo ($entity ?? '') == 'employee' ? 'active' : ''; ?>">Сотрудники</a></li>
            </ul>
        </nav>
    </header>

    <main>
    <?php

    $entity = $_GET['entity'] ?? 'product'; 
    $action = $_GET['action'] ?? 'list'; 

    $controller = null;


    switch ($entity) {
        case 'product':
            $controller = new ProductController($conn);
            break;
        case 'client':
            $controller = new ClientController($conn);
            break;
        case 'employee':
            $controller = new EmployeeController($conn);
            break;
        default:
            header("Location: index.php?entity=product&action=list");
            exit();
    }

    $actionMethod = $action . 'Action';

    if (method_exists($controller, $actionMethod)) {
        $controller->$actionMethod();
    } else {
        header("Location: index.php?entity={$entity}&action=list");
        exit();
    }

    $conn->close();
    ?>
    </main>

    <footer>
        <p>&copy; <?php echo date("Y"); ?> Компьютерный магазин. Все права защищены.</p>
    </footer>
    <script src="<?php echo htmlspecialchars($base_url); ?>public/js/script.js"></script>
</body>
</html>