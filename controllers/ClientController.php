<?php

class ClientController {
    private $conn;
    private $entity_name_singular = 'клиент';
    private $entity_name_plural = 'клиенты';
    private $table_name = 'clients';
    private $id_field = 'client_id';

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
        $clients = getAllItems($this->conn, $this->table_name, $this->id_field);
        // Исправленные поля согласно схеме clients
        $display_fields = [
            'first_name' => 'Имя',
            'last_name' => 'Фамилия',
            'phone' => 'Телефон',
            'email' => 'Email'
        ];
        $this->render('list_template', [
            'pageTitle' => 'Список клиентов',
            'entity' => 'client',
            'entity_name_singular' => $this->entity_name_singular,
            'entity_name_plural' => $this->entity_name_plural,
            'entity_id_field' => $this->id_field,
            'items' => $clients,
            'display_fields' => $display_fields
        ]);
    }

    public function detailAction() {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            header("Location: index.php?entity=client&action=list&message_type=error&message=ID клиента не указан.");
            exit();
        }

        $client = getItemById($this->conn, $this->table_name, $this->id_field, $id);
        // Исправленные поля согласно схеме clients
        $display_fields = [
            'first_name' => 'Имя',
            'last_name' => 'Фамилия',
            'phone' => 'Телефон',
            'email' => 'Email'
        ];

        $this->render('detail_template', [
            'pageTitle' => 'Детали клиента',
            'entity' => 'client',
            'entity_name_singular' => $this->entity_name_singular,
            'entity_id_field' => $this->id_field,
            'item' => $client,
            'display_fields' => $display_fields,
            'errors' => $client ? [] : [['type' => 'error', 'message' => 'Клиент не найден.']]
        ]);
    }

    public function createAction() {
        $errors = [];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $first_name = trim($_POST['first_name'] ?? '');
            $last_name = trim($_POST['last_name'] ?? '');
            $phone = trim($_POST['phone'] ?? '');
            $email = trim($_POST['email'] ?? '');

            if (empty($first_name)) {
                $errors[] = ['type' => 'error', 'message' => 'Имя обязательно.'];
            }
            if (empty($last_name)) {
                $errors[] = ['type' => 'error', 'message' => 'Фамилия обязательна.'];
            }
            if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = ['type' => 'error', 'message' => 'Некорректный формат Email.'];
            }

            if (empty($errors)) {
                $data = [
                    'first_name' => $first_name,
                    'last_name' => $last_name,
                    'phone' => $phone,
                    'email' => $email
                ];
                $new_id = createItem($this->conn, $this->table_name, $data);
                if ($new_id) {
                    header("Location: index.php?entity=client&action=detail&id=" . $new_id . "&message_type=success&message=Клиент успешно добавлен.");
                    exit();
                } else {
                    $errors[] = ['type' => 'error', 'message' => 'Ошибка при добавлении клиента.'];
                }
            }
        }

        $this->render('form_template', [
            'pageTitle' => 'Добавить клиента',
            'entity' => 'client',
            'entity_name_singular' => $this->entity_name_singular,
            'action_type' => 'create',
            'item' => $_POST ?? [],
            // Исправленные поля формы согласно схеме clients
            'form_fields' => [
                'first_name' => ['label' => 'Имя', 'type' => 'text', 'required' => true],
                'last_name' => ['label' => 'Фамилия', 'type' => 'text', 'required' => true],
                'phone' => ['label' => 'Телефон', 'type' => 'text', 'required' => false],
                'email' => ['label' => 'Email', 'type' => 'email', 'required' => false]
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
            $errors[] = ['type' => 'error', 'message' => 'Клиент для редактирования не найден.'];
            $this->render('form_template', [
                'pageTitle' => 'Редактировать клиента',
                'entity' => 'client',
                'entity_name_singular' => $this->entity_name_singular,
                'action_type' => 'edit',
                'item' => [],
                'form_fields' => [],
                'errors' => $errors
            ]);
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $first_name = trim($_POST['first_name'] ?? '');
            $last_name = trim($_POST['last_name'] ?? '');
            $phone = trim($_POST['phone'] ?? '');
            $email = trim($_POST['email'] ?? '');

            if (empty($first_name)) {
                $errors[] = ['type' => 'error', 'message' => 'Имя обязательно.'];
            }
            if (empty($last_name)) {
                $errors[] = ['type' => 'error', 'message' => 'Фамилия обязательна.'];
            }
            if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = ['type' => 'error', 'message' => 'Некорректный формат Email.'];
            }

            if (empty($errors)) {
                $data = [
                    'first_name' => $first_name,
                    'last_name' => $last_name,
                    'phone' => $phone,
                    'email' => $email
                ];
                $success = updateItem($this->conn, $this->table_name, $this->id_field, $id, $data);
                if ($success) {
                    header("Location: index.php?entity=client&action=detail&id=" . $id . "&message_type=success&message=Данные клиента успешно обновлены.");
                    exit();
                } else {
                    $errors[] = ['type' => 'error', 'message' => 'Ошибка при обновлении данных клиента.'];
                }
            }
            $item['first_name'] = $first_name;
            $item['last_name'] = $last_name;
            $item['phone'] = $phone;
            $item['email'] = $email;
        }

        $this->render('form_template', [
            'pageTitle' => 'Редактировать клиента',
            'entity' => 'client',
            'entity_name_singular' => $this->entity_name_singular,
            'action_type' => 'edit',
            'item' => $item,
            // Исправленные поля формы согласно схеме clients
            'form_fields' => [
                'first_name' => ['label' => 'Имя', 'type' => 'text', 'required' => true],
                'last_name' => ['label' => 'Фамилия', 'type' => 'text', 'required' => true],
                'phone' => ['label' => 'Телефон', 'type' => 'text', 'required' => false],
                'email' => ['label' => 'Email', 'type' => 'email', 'required' => false]
            ],
            'errors' => $errors
        ]);
    }

    public function deleteAction() {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            header("Location: index.php?entity=client&action=list&message_type=error&message=ID клиента не указан.");
            exit();
        }

        $item = getItemById($this->conn, $this->table_name, $this->id_field, $id);

        if (!$item) {
            header("Location: index.php?entity=client&action=list&message_type=error&message=Клиент для удаления не найден.");
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_delete'])) {
            $success = deleteItem($this->conn, $this->table_name, $this->id_field, $id);
            if ($success) {
                header("Location: index.php?entity=client&action=list&message_type=success&message=Клиент успешно удален.");
                exit();
            } else {
                header("Location: index.php?entity=client&action=list&message_type=error&message=Ошибка при удалении клиента.");
                exit();
            }
        }

        $this->render('delete_template', [
            'pageTitle' => 'Удалить клиента',
            'entity' => 'client',
            'entity_name_singular' => $this->entity_name_singular,
            'entity_id_field' => 'client_id', // Указываем ID поля для клиента
            'item' => $item,
            'action_url' => 'index.php?entity=client&action=delete&id=' . htmlspecialchars($id)
        ]);
    }
}