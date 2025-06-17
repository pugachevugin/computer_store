<?php

class ProductController {
    private $conn;
    private $entity_name_singular = 'товар';
    private $entity_name_plural = 'товары';
    private $table_name = 'products';
    private $id_field = 'product_id';

    public function __construct($conn) {
        $this->conn = $conn;
    }

    private function render($template_name, $data = []) {
        global $pageTitle, $errors;

        if (isset($data['pageTitle'])) {
            $pageTitle = $data['pageTitle'];
            unset($data['pageTitle']);
        }

        if (isset($data['errors']) && is_array($data['errors'])) {
            $errors = array_merge($errors, $data['errors']);
            unset($data['errors']);
        }

        extract($data);
        include __DIR__ . '/../views/templates/' . $template_name . '.php';
    }

    public function listAction() {
        $products = getAllItems($this->conn, $this->table_name, $this->id_field);
        $display_fields = [
            'product_name' => 'Название',
            'manufacturer' => 'Производитель',
            'price' => 'Цена',
            'quantity_in_stock' => 'Количество на складе',
        ];
        $this->render('list_template', [
            'pageTitle' => 'Список товаров',
            'entity' => 'product',
            'entity_name_singular' => $this->entity_name_singular,
            'entity_name_plural' => $this->entity_name_plural,
            'entity_id_field' => $this->id_field,
            'items' => $products,
            'display_fields' => $display_fields
        ]);
    }

    public function detailAction() {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            header("Location: index.php?entity=product&action=list&message_type=error&message=ID товара не указан.");
            exit();
        }

        $product = getItemById($this->conn, $this->table_name, $this->id_field, $id);
        $display_fields = [
            'product_name' => 'Название',
            'manufacturer' => 'Производитель',
            'price' => 'Цена',
            'quantity_in_stock' => 'Количество на складе',
        ];

        $this->render('detail_template', [
            'pageTitle' => 'Детали товара',
            'entity' => 'product',
            'entity_name_singular' => $this->entity_name_singular,
            'entity_id_field' => $this->id_field,
            'item' => $product,
            'display_fields' => $display_fields,
            'errors' => $product ? [] : [['type' => 'error', 'message' => 'Товар не найден.']]
        ]);
    }

    public function createAction() {
        $errors = [];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $product_name = trim($_POST['product_name'] ?? '');
            $manufacturer = trim($_POST['manufacturer'] ?? '');
            $price = filter_var($_POST['price'] ?? '', FILTER_VALIDATE_FLOAT, FILTER_NULL_ON_FAILURE);
            $quantity_in_stock = filter_var($_POST['quantity_in_stock'] ?? '', FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);

            if (empty($product_name)) {
                $errors[] = ['type' => 'error', 'message' => 'Название товара обязательно.'];
            }
            if (empty($manufacturer)) {
                $errors[] = ['type' => 'error', 'message' => 'Производитель обязателен.'];
            }
            if ($price === null || $price < 0) {
                $errors[] = ['type' => 'error', 'message' => 'Цена должна быть неотрицательным числом.'];
            }
            if ($quantity_in_stock === null || $quantity_in_stock < 0) {
                $errors[] = ['type' => 'error', 'message' => 'Количество на складе должно быть неотрицательным целым числом.'];
            }

            if (empty($errors)) {
                $data = [
                    'product_name' => $product_name,
                    'manufacturer' => $manufacturer,
                    'price' => $price,
                    'quantity_in_stock' => $quantity_in_stock,
                ];
                $new_id = createItem($this->conn, $this->table_name, $data);
                if ($new_id) {
                    // Теперь перенаправляем на список, так как страницы деталей и удаления нет
                    header("Location: index.php?entity=product&action=list&message_type=success&message=Товар успешно добавлен.");
                    exit();
                } else {
                    $errors[] = ['type' => 'error', 'message' => 'Ошибка при добавлении товара.'];
                }
            }
        }

        $this->render('form_template', [
            'pageTitle' => 'Добавить товар',
            'entity' => 'product',
            'entity_name_singular' => $this->entity_name_singular,
            'action_type' => 'create',
            'entity_id_field' => $this->id_field,
            'item' => $_POST ?? [],
            'form_fields' => [
                'product_name' => ['label' => 'Название товара', 'type' => 'text', 'required' => true],
                'manufacturer' => ['label' => 'Производитель', 'type' => 'text', 'required' => true],
                'price' => ['label' => 'Цена', 'type' => 'number', 'required' => true, 'step' => '0.01'],
                'quantity_in_stock' => ['label' => 'Количество на складе', 'type' => 'number', 'required' => true],
            ],
            'errors' => $errors
        ]);
    }

    public function editAction() {
        $id = $_GET['id'] ?? null;
        $item = null;
        $errors = [];

        if ($id) {
            $item = getItemById($this->conn, $this->table_name, $this->id_field, $id);
        }

        if (!$item) {
            $errors[] = ['type' => 'error', 'message' => 'Товар для редактирования не найден.'];
            $this->render('form_template', [
                'pageTitle' => 'Редактировать товар',
                'entity' => 'product',
                'entity_name_singular' => $this->entity_name_singular,
                'action_type' => 'edit',
                'entity_id_field' => $this->id_field,
                'item' => [],
                'form_fields' => [],
                'errors' => $errors
            ]);
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $product_name = trim($_POST['product_name'] ?? '');
            $manufacturer = trim($_POST['manufacturer'] ?? '');
            $price = filter_var($_POST['price'] ?? '', FILTER_VALIDATE_FLOAT, FILTER_NULL_ON_FAILURE);
            $quantity_in_stock = filter_var($_POST['quantity_in_stock'] ?? '', FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);

            if (empty($product_name)) {
                $errors[] = ['type' => 'error', 'message' => 'Название товара обязательно.'];
            }
            if (empty($manufacturer)) {
                $errors[] = ['type' => 'error', 'message' => 'Производитель обязателен.'];
            }
            if ($price === null || $price < 0) {
                $errors[] = ['type' => 'error', 'message' => 'Цена должна быть неотрицательным числом.'];
            }
            if ($quantity_in_stock === null || $quantity_in_stock < 0) {
                $errors[] = ['type' => 'error', 'message' => 'Количество на складе должно быть неотрицательным целым числом.'];
            }

            if (empty($errors)) {
                $data = [
                    'product_name' => $product_name,
                    'manufacturer' => $manufacturer,
                    'price' => $price,
                    'quantity_in_stock' => $quantity_in_stock,
                ];
                $success = updateItem($this->conn, $this->table_name, $this->id_field, $id, $data);
                if ($success) {
                    // Теперь перенаправляем на список, так как страницы деталей и удаления нет
                    header("Location: index.php?entity=product&action=list&message_type=success&message=Товар успешно обновлен.");
                    exit();
                } else {
                    $errors[] = ['type' => 'error', 'message' => 'Ошибка при обновлении товара.'];
                }
            }
            $item['product_name'] = $product_name;
            $item['manufacturer'] = $manufacturer;
            $item['price'] = $_POST['price'] ?? $item['price'];
            $item['quantity_in_stock'] = $_POST['quantity_in_stock'] ?? $item['quantity_in_stock'];
        }

        $this->render('form_template', [
            'pageTitle' => 'Редактировать товар',
            'entity' => 'product',
            'entity_name_singular' => $this->entity_name_singular,
            'action_type' => 'edit',
            'entity_id_field' => $this->id_field,
            'item' => $item,
            'form_fields' => [
                'product_name' => ['label' => 'Название товара', 'type' => 'text', 'required' => true],
                'manufacturer' => ['label' => 'Производитель', 'type' => 'text', 'required' => true],
                'price' => ['label' => 'Цена', 'type' => 'number', 'required' => true, 'step' => '0.01'],
                'quantity_in_stock' => ['label' => 'Количество на складе', 'type' => 'number', 'required' => true],
            ],
            'errors' => $errors
        ]);
    }
    // Метод deleteAction() полностью удален, как запрошено.
    // public function deleteAction() { ... }
}