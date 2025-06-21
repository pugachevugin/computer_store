<?php
// controllers/Controller.php

require_once __DIR__ . '/../includes/model.php';

class Controller {
    private $conn;
    private $entity_map;

    public function __construct($conn) {
        $this->conn = $conn;
        $this->entity_map = [
            'product' => [
                'table' => 'products',
                'id_field' => 'product_id',
                'singular' => 'товар',
                'plural' => 'товары',
                'fields' => [
                    'product_name' => ['label' => 'Название товара', 'type' => 'text', 'required' => true],
                    'price' => ['label' => 'Цена', 'type' => 'number', 'required' => true, 'step' => '0.01'],
                    'quantity_in_stock' => ['label' => 'Количество на складе', 'type' => 'number', 'required' => true],
                    'manufacturer' => ['label' => 'Производитель', 'type' => 'text', 'required' => false]
                ],
                'display_fields' => [
                    'product_name' => 'Название товара',
                    'manufacturer' => 'Производитель',
                    'price' => 'Цена',
                    'quantity_in_stock' => 'На складе'
                ]
            ],
            'client' => [
                'table' => 'clients',
                'id_field' => 'client_id',
                'singular' => 'клиент',
                'plural' => 'клиенты',
                'fields' => [
                    'first_name' => ['label' => 'Имя', 'type' => 'text', 'required' => true],
                    'last_name' => ['label' => 'Фамилия', 'type' => 'text', 'required' => true],
                    'phone_number' => ['label' => 'Телефон', 'type' => 'text', 'required' => false],
                    'email' => ['label' => 'Email', 'type' => 'email', 'required' => false]
                ],
                'display_fields' => [
                    'first_name' => 'Имя',
                    'last_name' => 'Фамилия',
                    'phone_number' => 'Телефон',
                    'email' => 'Email'
                ]
            ],
            'employee' => [
                'table' => 'employees',
                'id_field' => 'employee_id',
                'singular' => 'сотрудник',
                'plural' => 'сотрудники',
                'fields' => [
                    'first_name' => ['label' => 'Имя', 'type' => 'text', 'required' => true],
                    'last_name' => ['label' => 'Фамилия', 'type' => 'text', 'required' => true],
                    'position' => ['label' => 'Должность', 'type' => 'text', 'required' => true],
                    'hire_date' => ['label' => 'Дата найма', 'type' => 'date', 'required' => true],
                    'salary' => ['label' => 'Зарплата', 'type' => 'number', 'required' => true, 'step' => '0.01']
                ],
                'display_fields' => [
                    'first_name' => 'Имя',
                    'last_name' => 'Фамилия',
                    'position' => 'Должность',
                    'salary' => 'Зарплата'
                ]
            ],
            'purchase' => [
                'table' => 'purchases',
                'id_field' => 'purchase_id',
                'singular' => 'закупка',
                'plural' => 'закупки',
                'fields' => [
                    'product_id' => ['label' => 'ID Товара', 'type' => 'number', 'required' => true],
                    'supplier_id' => ['label' => 'ID Поставщика', 'type' => 'number', 'required' => true],
                    'purchase_date' => ['label' => 'Дата закупки', 'type' => 'date', 'required' => true],
                    'quantity' => ['label' => 'Количество', 'type' => 'number', 'required' => true],
                    'unit_price' => ['label' => 'Цена за ед.', 'type' => 'number', 'required' => true, 'step' => '0.01']
                ],
                'display_fields' => [
                    'product_id' => 'ID Товара',
                    'supplier_id' => 'ID Поставщика',
                    'purchase_date' => 'Дата закупки',
                    'quantity' => 'Количество',
                    'unit_price' => 'Цена за ед.'
                ]
            ],
            'sale' => [
                'table' => 'sales',
                'id_field' => 'sale_id',
                'singular' => 'продажа',
                'plural' => 'продажи',
                'fields' => [
                    'product_id' => ['label' => 'ID Товара', 'type' => 'number', 'required' => true],
                    'client_id' => ['label' => 'ID Клиента', 'type' => 'number', 'required' => true],
                    'employee_id' => ['label' => 'ID Сотрудника', 'type' => 'number', 'required' => true],
                    'sale_date' => ['label' => 'Дата продажи', 'type' => 'date', 'required' => true],
                    'quantity' => ['label' => 'Количество', 'type' => 'number', 'required' => true],
                    'price_per_unit' => ['label' => 'Цена за ед.', 'type' => 'number', 'required' => true, 'step' => '0.01']
                ],
                'display_fields' => [
                    'product_id' => 'ID Товара',
                    'client_id' => 'ID Клиента',
                    'sale_date' => 'Дата продажи',
                    'quantity' => 'Количество',
                    'price_per_unit' => 'Цена за ед.'
                ]
            ],
            'supplier' => [
                'table' => 'suppliers',
                'id_field' => 'supplier_id',
                'singular' => 'поставщик',
                'plural' => 'поставщики',
                'fields' => [
                    'supplier_name' => ['label' => 'Название поставщика', 'type' => 'text', 'required' => true],
                    'phone' => ['label' => 'Телефон', 'type' => 'text', 'required' => false],
                    'contact_person' => ['label' => 'Контактное лицо', 'type' => 'text', 'required' => false]
                ],
                'display_fields' => [
                    'supplier_name' => 'Название поставщика',
                    'phone' => 'Телефон',
                    'contact_person' => 'Контактное лицо'
                ]
            ],
            'supplier_request' => [
                'table' => 'supplier_requests',
                'id_field' => 'request_id',
                'singular' => 'заявка поставщику',
                'plural' => 'заявки поставщикам',
                'fields' => [
                    'supplier_id' => ['label' => 'ID Поставщика', 'type' => 'number', 'required' => true],
                    'request_date' => ['label' => 'Дата заявки', 'type' => 'date', 'required' => true],
                    'request_status' => ['label' => 'Статус заявки', 'type' => 'text', 'required' => true]
                ],
                'display_fields' => [
                    'supplier_id' => 'ID Поставщика',
                    'request_date' => 'Дата заявки',
                    'request_status' => 'Статус'
                ]
            ]
        ];
    }

    private function getCurrentEntityParams($entity_name) {
        if (!isset($this->entity_map[$entity_name])) {
            throw new Exception("Неизвестная сущность: " . htmlspecialchars($entity_name));
        }
        return $this->entity_map[$entity_name];
    }

    private function redirect($entity, $action, $type, $message) {
        header("Location: index.php?entity=$entity&action=$action&message_type=$type&message=" . urlencode($message));
        exit();
    }

    public function listAction($entity_name) {
        $params = $this->getCurrentEntityParams($entity_name);
        $items = getAllItems($this->conn, $params['table'], $params['id_field']);
        return [
            'pageTitle' => $params['plural'],
            'entity' => $entity_name,
            'entity_name_plural' => $params['plural'],
            'entity_id_field' => $params['id_field'],
            'display_fields' => $params['display_fields'],
            'items' => $items
        ];
    }

    public function detailAction($entity_name) {
        $params = $this->getCurrentEntityParams($entity_name);
        $id = $_GET['id'] ?? null;

        if (!$id) {
            $this->redirect($entity_name, 'list', 'error', "ID {$params['singular']} не указан.");
        }

        $item = getItemById($this->conn, $params['table'], $params['id_field'], $id);

        if (!$item) {
            $this->redirect($entity_name, 'list', 'error', "{$params['singular']} с ID {$id} не найден.");
        }

        return [
            'pageTitle' => 'Детали ' . $params['singular'],
            'entity' => $entity_name,
            'entity_id_field' => $params['id_field'],
            'display_fields' => $params['display_fields'],
            'item' => $item
        ];
    }

    public function createAction($entity_name) {
        $params = $this->getCurrentEntityParams($entity_name);
        $item = []; // Empty item for new entry

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [];
            foreach ($params['fields'] as $field_key => $field_props) {
                // Check if field is present in POST data
                if (isset($_POST[$field_key])) {
                    $data[$field_key] = $_POST[$field_key];
                } elseif ($field_props['required']) {
                    // If a required field is missing from POST, redirect with error
                    $this->redirect($entity_name, 'create', 'error', "Поле '{$field_props['label']}' обязательно для заполнения.");
                } else {
                    // For non-required fields not in POST, set to NULL or empty string
                    $data[$field_key] = null; // or ''; depends on your DB schema/preference
                }
            }

            if (createItem($this->conn, $params['table'], $data)) {
                $this->redirect($entity_name, 'list', 'success', "{$params['singular']} успешно добавлен.");
            } else {
                $this->redirect($entity_name, 'create', 'error', "Ошибка при добавлении {$params['singular']}.");
            }
        }

        return [
            'pageTitle' => 'Добавить ' . $params['singular'],
            'entity' => $entity_name,
            'entity_name_singular' => $params['singular'],
            'form_fields' => $params['fields'],
            'action_type' => 'create',
            'item' => $item // Pass empty item for form
        ];
    }

    public function editAction($entity_name) {
        $params = $this->getCurrentEntityParams($entity_name);
        $id = $_GET['id'] ?? null;

        if (!$id) {
            $this->redirect($entity_name, 'list', 'error', "ID {$params['singular']} не указан.");
        }

        $item = getItemById($this->conn, $params['table'], $params['id_field'], $id);

        if (!$item) {
            $this->redirect($entity_name, 'list', 'error', "{$params['singular']} с ID {$id} не найден.");
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [];
            foreach ($params['fields'] as $field_key => $field_props) {
                if (isset($_POST[$field_key])) {
                    $data[$field_key] = $_POST[$field_key];
                } elseif ($field_props['required']) {
                     $this->redirect($entity_name, 'edit', 'error', "Поле '{$field_props['label']}' обязательно для заполнения.");
                } else {
                    $data[$field_key] = null;
                }
            }

            if (updateItem($this->conn, $params['table'], $params['id_field'], $id, $data)) {
                $this->redirect($entity_name, 'list', 'success', "{$params['singular']} успешно обновлен.");
            } else {
                $this->redirect($entity_name, 'edit', 'error', "Ошибка при обновлении {$params['singular']}.");
            }
        }

        return [
            'pageTitle' => 'Редактировать ' . $params['singular'],
            'entity' => $entity_name,
            'entity_name_singular' => $params['singular'],
            'form_fields' => $params['fields'],
            'action_type' => 'edit',
            'item' => $item,
            'entity_id_field' => $params['id_field']
        ];
    }

    public function softDeleteAction($entity_name) {
        $params = $this->getCurrentEntityParams($entity_name);
        $id = $_GET['id'] ?? null;

        if (!$id) {
            $this->redirect($entity_name, 'list', 'error', "ID {$params['singular']} не указан.");
        }

        if (softDeleteItem($this->conn, $params['table'], $params['id_field'], $id)) {
            $this->redirect($entity_name, 'list', 'success', "{$params['singular']} перемещен в корзину.");
        } else {
            $this->redirect($entity_name, 'list', 'error', "Ошибка перемещения {$params['singular']} в корзину.");
        }
    }

    public function restoreAction($entity_name) {
        $params = $this->getCurrentEntityParams($entity_name);
        $id = $_GET['id'] ?? null;

        if (!$id) {
            $this->redirect($entity_name, 'trashed', 'error', "ID {$params['singular']} не указан.");
        }

        if (restoreItem($this->conn, $params['table'], $params['id_field'], $id)) {
            // ИЗМЕНЕНИЕ: Перенаправляем на главный список после восстановления
            $this->redirect($entity_name, 'list', 'success', "{$params['singular']} успешно восстановлен.");
        } else {
            // В случае ошибки остаемся в корзине
            $this->redirect($entity_name, 'trashed', 'error', "Ошибка восстановления {$params['singular']} из корзины.");
        }
    }

    public function deleteAction($entity_name) {
        $params = $this->getCurrentEntityParams($entity_name);
        $id = $_GET['id'] ?? null;

        if (!$id) {
            $this->redirect($entity_name, 'trashed', 'error', "ID {$params['singular']} не указан.");
        }

        $item = getItemById($this->conn, $params['table'], $params['id_field'], $id, true); // Get from trash
        if (!$item) {
            $this->redirect($entity_name, 'trashed', 'error', "{$params['singular']} с ID {$id} не найден.");
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_delete'])) {
            if (deleteItem($this->conn, $params['table'], $params['id_field'], $id)) {
                $this->redirect($entity_name, 'trashed', 'success', "{$params['singular']} окончательно удален.");
            } else {
                $this->redirect($entity_name, 'trashed', 'error', "Ошибка окончательного удаления {$params['singular']}.");
            }
        }

        return [
            'pageTitle' => 'Удалить ' . $params['singular'],
            'entity' => $entity_name,
            'entity_name_singular' => $params['singular'],
            'item' => $item,
            'entity_id_field' => $params['id_field']
        ];
    }

    public function trashedAction($entity_name) {
        $params = $this->getCurrentEntityParams($entity_name);
        $items = getTrashedItems($this->conn, $params['table'], $params['id_field']);
        return [
            'pageTitle' => 'Корзина: ' . $params['plural'],
            'entity' => $entity_name,
            'entity_name_plural' => $params['plural'],
            'entity_id_field' => $params['id_field'],
            'display_fields' => $params['display_fields'],
            'items' => $items
        ];
    }
}