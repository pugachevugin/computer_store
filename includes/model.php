<?php


/**
 * Устанавливает соединение с базой данных.
 * @return mysqli Объект соединения с базой данных.
 * @throws Exception Если не удалось подключиться к базе данных.
 */
function connectDB() {
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "computer_shop_db";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        error_log("Connection failed: " . $conn->connect_error);
        throw new Exception("Ошибка подключения к базе данных.");
    }
    $conn->set_charset("utf8mb4");
    return $conn;
}

/**
 * Получает все записи из указанной таблицы.
 * @param mysqli $conn Объект соединения с БД.
 * @param string $table_name Имя таблицы.
 * @param string $id_field Имя поля ID для сортировки.
 * @return array Массив записей.
 */
function getAllItems($conn, $table_name, $id_field) {
    $items = [];
    // ИСПРАВЛЕНИЕ: Изменено DESC на ASC для сортировки по возрастанию ID.
    $sql = "SELECT * FROM " . $table_name . " ORDER BY " . $id_field . " ASC"; 
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $items[] = $row;
        }
    }
    return $items;
}

/**
 * Получает запись по ID из указанной таблицы.
 * @param mysqli $conn Объект соединения с БД.
 * @param string $table_name Имя таблицы.
 * @param string $id_field Имя поля ID.
 * @param int $id Значение ID.
 * @return array|null Запись или null, если не найдено.
 */
function getItemById($conn, $table_name, $id_field, $id) {
    $stmt = $conn->prepare("SELECT * FROM " . $table_name . " WHERE " . $id_field . " = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $item = $result->fetch_assoc();
    $stmt->close();
    return $item;
}

/**
 * Создает новую запись в указанной таблице.
 * @param mysqli $conn Объект соединения с БД.
 * @param string $table_name Имя таблицы.
 * @param array $data Ассоциативный массив данных для вставки.
 * @return int|bool ID новой записи или false в случае ошибки.
 */
function createItem($conn, $table_name, $data) {
    $fields = implode(", ", array_keys($data));
    $placeholders = implode(", ", array_fill(0, count($data), "?"));
    $sql = "INSERT INTO " . $table_name . " (" . $fields . ") VALUES (" . $placeholders . ")";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        error_log("Prepare failed: (" . $conn->errno . ") " . $conn->error);
        return false;
    }

    $types = '';
    $values = [];
    foreach ($data as $key => $value) {
        if ($key == 'price') {
            $types .= 'd';
        } elseif ($key == 'quantity_in_stock') {
            $types .= 'i';
        } else {
            $types .= 's';
        }
        $values[] = &$data[$key];
    }

    $bind_names[] = $types;
    for ($i = 0; $i < count($values); $i++) {
        $bind_names[] = &$values[$i];
    }
    call_user_func_array([$stmt, 'bind_param'], $bind_names);

    if ($stmt->execute()) {
        $new_id = $stmt->insert_id;
        $stmt->close();
        return $new_id;
    } else {
        error_log("Execute failed: (" . $stmt->errno . ") " . $stmt->error);
        $stmt->close();
        return false;
    }
}

/**
 * Обновляет запись в указанной таблице.
 * @param mysqli $conn Объект соединения с БД.
 * @param string $table_name Имя таблицы.
 * @param string $id_field Имя поля ID.
 * @param int $id ID записи для обновления.
 * @param array $data Ассоциативный массив данных для обновления.
 * @return bool True в случае успеха, false в случае ошибки.
 */
function updateItem($conn, $table_name, $id_field, $id, $data) {
    $set_parts = [];
    foreach ($data as $key => $value) {
        $set_parts[] = $key . " = ?";
    }
    $sql = "UPDATE " . $table_name . " SET " . implode(", ", $set_parts) . " WHERE " . $id_field . " = ?";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        error_log("Prepare failed: (" . $conn->errno . ") " . $conn->error);
        return false;
    }

    $types = '';
    $values = [];
    foreach ($data as $key => $value) {
        if ($key == 'price') {
            $types .= 'd';
        } elseif ($key == 'quantity_in_stock') {
            $types .= 'i';
        } else {
            $types .= 's';
        }
        $values[] = &$data[$key];
    }
    $types .= 'i';
    $values[] = &$id;

    $bind_names[] = $types;
    for ($i = 0; $i < count($values); $i++) {
        $bind_names[] = &$values[$i];
    }
    call_user_func_array([$stmt, 'bind_param'], $bind_names);

    if ($stmt->execute()) {
        $stmt->close();
        return true;
    } else {
        error_log("Execute failed: (" . $stmt->errno . ") " . $stmt->error);
        $stmt->close();
        return false;
    }
}