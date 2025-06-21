<?php
// includes/model.php

function connectDB() {
    $host = 'localhost';
    $dbname = 'computer_store';
    $username = 'root'; // Ваше имя пользователя MySQL
    $password = '';     // Ваш пароль MySQL
    $charset = 'utf8mb4';

    $dsn = "mysql:host=$host;dbname=$dbname;charset=$charset";
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];

    try {
        return new PDO($dsn, $username, $password, $options);
    } catch (PDOException $e) {
        throw new Exception("Ошибка подключения к базе данных: " . $e->getMessage());
    }
}

function getAllItems($conn, $tableName, $idField, $includeDeleted = false) {
    // Check if 'deleted_at' column exists in the table
    $stmt = $conn->query("SHOW COLUMNS FROM $tableName LIKE 'deleted_at'");
    $hasDeletedColumn = ($stmt->rowCount() > 0);

    $sql = "SELECT * FROM $tableName";
    if (!$includeDeleted && $hasDeletedColumn) {
        $sql .= " WHERE deleted_at IS NULL";
    }
    $sql .= " ORDER BY $idField DESC";

    return $conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);
}

function getItemById($conn, $tableName, $idField, $id, $includeDeleted = false) {
    $stmt = $conn->query("SHOW COLUMNS FROM $tableName LIKE 'deleted_at'");
    $hasDeletedColumn = ($stmt->rowCount() > 0);

    $sql = "SELECT * FROM $tableName WHERE $idField = :id";
    if (!$includeDeleted && $hasDeletedColumn) {
        $sql .= " AND deleted_at IS NULL";
    }

    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function createItem($conn, $tableName, $data) {
    $fields = implode(', ', array_keys($data));
    $placeholders = ':' . implode(', :', array_keys($data));

    $sql = "INSERT INTO $tableName ($fields) VALUES ($placeholders)";
    $stmt = $conn->prepare($sql);

    foreach ($data as $key => $value) {
        $stmt->bindValue(":$key", $value);
    }

    return $stmt->execute();
}

function updateItem($conn, $tableName, $idField, $id, $data) {
    $setParts = [];
    foreach ($data as $key => $value) {
        $setParts[] = "$key = :$key";
    }
    $setClause = implode(', ', $setParts);

    $sql = "UPDATE $tableName SET $setClause WHERE $idField = :id";
    $stmt = $conn->prepare($sql);

    foreach ($data as $key => $value) {
        $stmt->bindValue(":$key", $value);
    }
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);

    return $stmt->execute();
}

function softDeleteItem($conn, $tableName, $idField, $id) {
    $sql = "UPDATE $tableName SET deleted_at = NOW() WHERE $idField = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    return $stmt->execute();
}

function restoreItem($conn, $tableName, $idField, $id) {
    $sql = "UPDATE $tableName SET deleted_at = NULL WHERE $idField = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    return $stmt->execute();
}

function deleteItem($conn, $tableName, $idField, $id) {
    $sql = "DELETE FROM $tableName WHERE $idField = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    return $stmt->execute();
}

function getTrashedItems($conn, $tableName, $idField) {
    $stmt = $conn->query("SHOW COLUMNS FROM $tableName LIKE 'deleted_at'");
    $hasDeletedColumn = ($stmt->rowCount() > 0);

    if (!$hasDeletedColumn) {
        // If there's no 'deleted_at' column, this table doesn't support soft delete
        return [];
    }

    $sql = "SELECT * FROM $tableName WHERE deleted_at IS NOT NULL ORDER BY deleted_at DESC";
    return $conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);
}