<?php
$pageTitle = 'Компьютерный магазин';
require_once __DIR__ . '/includes/model.php';
require_once __DIR__ . '/controllers/Controller.php';

try {
    $conn = connectDB();
} catch (Exception $e) {
    die("<h1>Ошибка подключения к базе данных</h1><p>" . htmlspecialchars($e->getMessage()) . "</p>");
}

$entity = $_GET['entity'] ?? 'product';
$action = $_GET['action'] ?? 'list';

$controller = new Controller($conn);
$actionMethod = $action . 'Action';

$view_data = [];
$message = $_GET['message'] ?? '';
$message_type = $_GET['message_type'] ?? '';

try {
    if (method_exists($controller, $actionMethod)) {
        $view_data = $controller->$actionMethod($entity);
        $pageTitle = $view_data['pageTitle'] ?? $pageTitle;
    } else {
        header("Location: index.php?entity=$entity&action=list&message_type=error&message=Неизвестное действие");
        exit();
    }
} catch (Exception $e) {
    $error = "Произошла ошибка: " . htmlspecialchars($e->getMessage());
}

$base_url = str_replace('index.php', '', $_SERVER['SCRIPT_NAME']);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?> | Компьютерный магазин</title>
    <link rel="stylesheet" href="<?= htmlspecialchars($base_url) ?>style.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
</head>
<body>
    <header>
        <h1>Компьютерный магазин</h1>
        <nav>
            <ul>
                <li><a href="index.php?entity=product&action=list" 
                       class="<?= ($_GET['entity'] ?? '') == 'product' ? 'active' : '' ?>">Товары</a></li>
                <li><a href="index.php?entity=client&action=list" 
                       class="<?= ($_GET['entity'] ?? '') == 'client' ? 'active' : '' ?>">Клиенты</a></li>
                <li><a href="index.php?entity=employee&action=list" 
                       class="<?= ($_GET['entity'] ?? '') == 'employee' ? 'active' : '' ?>">Сотрудники</a></li>
            </ul>
        </nav>
    </header>
    
    <main>
        <?php if ($message): ?>
            <p class="message <?= htmlspecialchars($message_type) ?>"><?= htmlspecialchars(urldecode($message)) ?></p>
        <?php endif; ?>
        
        <?php if (!empty($error)): ?>
            <p class="message error"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>
        
        <?php
        if (!empty($view_data)) {
            extract($view_data);
            // Определение шаблона на основе действия
            $templateFile = match($action) {
                'create', 'edit' => 'form',
                'trashed' => 'trash',
                'delete' => 'delete',
                default => $action
            };
            $templatePath = __DIR__ . '/views/' . $templateFile . '_template.php';
            if (file_exists($templatePath)) {
                require $templatePath;
            } else {
                echo '<p class="message error">Шаблон не найден: ' . htmlspecialchars($templatePath) . '</p>';
            }
        }
        ?>
    </main>
    
    <footer>
        <p>&copy; <?= date("Y") ?> Компьютерный магазин. Все права защищены.</p>
    </footer>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const purgeButtons = document.querySelectorAll('a.button.delete');
            purgeButtons.forEach(button => {
                button.onclick = function() {
                    return confirm('Вы уверены? Запись будет удалена безвозвратно!');
                };
            });
        });
    </script>
</body>
</html>