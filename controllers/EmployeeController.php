<?php

class EmployeeController {
    private $conn;
    private $entity_name_singular = 'сотрудник';
    private $entity_name_plural = 'сотрудники';
    private $table_name = 'employees';
    private $id_field = 'employee_id';

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
        $employees = getAllItems($this->conn, $this->table_name, $this->id_field);
        // Поля, отображаемые в списке, согласно схеме employees
        $display_fields = [
            'first_name' => 'Имя',
            'last_name' => 'Фамилия',
            'position' => 'Должность',
            'phone' => 'Телефон'
        ];
        $this->render('list_template', [
            'pageTitle' => 'Список сотрудников',
            'entity' => 'employee',
            'entity_name_singular' => $this->entity_name_singular,
            'entity_name_plural' => $this->entity_name_plural,
            'entity_id_field' => $this->id_field,
            'items' => $employees,
            'display_fields' => $display_fields
        ]);
    }

    public function detailAction() {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            header("Location: index.php?entity=employee&action=list&message_type=error&message=ID сотрудника не указан.");
            exit();
        }

        $employee = getItemById($this->conn, $this->table_name, $this->id_field, $id);
        // Поля, отображаемые в деталях, согласно схеме employees
        $display_fields = [
            'first_name' => 'Имя',
            'last_name' => 'Фамилия',
            'position' => 'Должность',
            'phone' => 'Телефон'
        ];

        $this->render('detail_template', [
            'pageTitle' => 'Детали сотрудника',
            'entity' => 'employee',
            'entity_name_singular' => $this->entity_name_singular,
            'entity_id_field' => $this->id_field,
            'item' => $employee,
            'display_fields' => $display_fields,
            'errors' => $employee ? [] : [['type' => 'error', 'message' => 'Сотрудник не найден.']]
        ]);
    }

    public function createAction() {
        $errors = [];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $first_name = trim($_POST['first_name'] ?? '');
            $last_name = trim($_POST['last_name'] ?? '');
            $position = trim($_POST['position'] ?? '');
            $phone = trim($_POST['phone'] ?? '');

            if (empty($first_name)) {
                $errors[] = ['type' => 'error', 'message' => 'Имя обязательно.'];
            }
            if (empty($last_name)) {
                $errors[] = ['type' => 'error', 'message' => 'Фамилия обязательна.'];
            }
            if (empty($position)) {
                $errors[] = ['type' => 'error', 'message' => 'Должность обязательна.'];
            }

            if (empty($errors)) {
                $data = [
                    'first_name' => $first_name,
                    'last_name' => $last_name,
                    'position' => $position,
                    'phone' => $phone
                ];
                $new_id = createItem($this->conn, $this->table_name, $data);
                if ($new_id) {
                    header("Location: index.php?entity=employee&action=detail&id=" . $new_id . "&message_type=success&message=Сотрудник успешно добавлен.");
                    exit();
                } else {
                    $errors[] = ['type' => 'error', 'message' => 'Ошибка при добавлении сотрудника.'];
                }
            }
        }

        $this->render('form_template', [
            'pageTitle' => 'Добавить сотрудника',
            'entity' => 'employee',
            'entity_name_singular' => $this->entity_name_singular,
            'action_type' => 'create',
            'item' => $_POST ?? [],
            // Поля формы согласно схеме employees
            'form_fields' => [
                'first_name' => ['label' => 'Имя', 'type' => 'text', 'required' => true],
                'last_name' => ['label' => 'Фамилия', 'type' => 'text', 'required' => true],
                'position' => ['label' => 'Должность', 'type' => 'text', 'required' => true],
                'phone' => ['label' => 'Телефон', 'type' => 'text', 'required' => false]
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
            $errors[] = ['type' => 'error', 'message' => 'Сотрудник для редактирования не найден.'];
            $this->render('form_template', [
                'pageTitle' => 'Редактировать сотрудника',
                'entity' => 'employee',
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
            $position = trim($_POST['position'] ?? '');
            $phone = trim($_POST['phone'] ?? '');

            if (empty($first_name)) {
                $errors[] = ['type' => 'error', 'message' => 'Имя обязательно.'];
            }
            if (empty($last_name)) {
                $errors[] = ['type' => 'error', 'message' => 'Фамилия обязательна.'];
            }
            if (empty($position)) {
                $errors[] = ['type' => 'error', 'message' => 'Должность обязательна.'];
            }

            if (empty($errors)) {
                $data = [
                    'first_name' => $first_name,
                    'last_name' => $last_name,
                    'position' => $position,
                    'phone' => $phone
                ];
                $success = updateItem($this->conn, $this->table_name, $this->id_field, $id, $data);
                if ($success) {
                    header("Location: index.php?entity=employee&action=detail&id=" . $id . "&message_type=success&message=Данные сотрудника успешно обновлены.");
                    exit();
                } else {
                    $errors[] = ['type' => 'error', 'message' => 'Ошибка при обновлении данных сотрудника.'];
                }
            }
            $item['first_name'] = $first_name;
            $item['last_name'] = $last_name;
            $item['position'] = $position;
            $item['phone'] = $phone;
        }

        $this->render('form_template', [
            'pageTitle' => 'Редактировать сотрудника',
            'entity' => 'employee',
            'entity_name_singular' => $this->entity_name_singular,
            'action_type' => 'edit',
            'item' => $item,
            // Поля формы согласно схеме employees
            'form_fields' => [
                'first_name' => ['label' => 'Имя', 'type' => 'text', 'required' => true],
                'last_name' => ['label' => 'Фамилия', 'type' => 'text', 'required' => true],
                'position' => ['label' => 'Должность', 'type' => 'text', 'required' => true],
                'phone' => ['label' => 'Телефон', 'type' => 'text', 'required' => false]
            ],
            'errors' => $errors
        ]);
    }

    public function deleteAction() {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            header("Location: index.php?entity=employee&action=list&message_type=error&message=ID сотрудника не указан.");
            exit();
        }

        $item = getItemById($this->conn, $this->table_name, $this->id_field, $id);

        if (!$item) {
            header("Location: index.php?entity=employee&action=list&message_type=error&message=Сотрудник для удаления не найден.");
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_delete'])) {
            $success = deleteItem($this->conn, $this->table_name, $this->id_field, $id);
            if ($success) {
                header("Location: index.php?entity=employee&action=list&message_type=success&message=Сотрудник успешно удален.");
                exit();
            } else {
                header("Location: index.php?entity=employee&action=list&message_type=error&message=Ошибка при удалении сотрудника.");
                exit();
            }
        }

        $this->render('delete_template', [
            'pageTitle' => 'Удалить сотрудника',
            'entity' => 'employee',
            'entity_name_singular' => $this->entity_name_singular,
            'entity_id_field' => 'employee_id',
            'item' => $item,
            'action_url' => 'index.php?entity=employee&action=delete&id=' . htmlspecialchars($id)
        ]);
    }
}