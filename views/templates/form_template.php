<?php

$formType = $action_type ?? 'create'; // This line correctly handles undefined $action_type
$action_url = "index.php?entity=" . htmlspecialchars($entity) . "&action=" . htmlspecialchars($formType);

$item = $item ?? [];

if ($formType == 'edit' && isset($entity_id_field) && isset($item[$entity_id_field])) {
    $action_url .= "&id=" . htmlspecialchars($item[$entity_id_field]);
}
?>
        <h2 class="section-title"><?php echo ($formType == 'edit') ? 'Редактировать ' . htmlspecialchars($entity_name_singular) : 'Добавить новый ' . htmlspecialchars($entity_name_singular); ?></h2>
        <div class="form-container">
            <?php // Сообщения и ошибки обрабатываются в index.php ?>

            <form action="<?php echo htmlspecialchars($action_url); ?>" method="POST">
                <?php foreach ($form_fields as $field_key => $field_props): ?>
                    <div class="form-group">
                        <label for="<?php echo htmlspecialchars($field_key); ?>"><?php echo htmlspecialchars($field_props['label']); ?>:</label>
                        <input
                            type="<?php echo htmlspecialchars($field_props['type']); ?>"
                            id="<?php echo htmlspecialchars($field_key); ?>"
                            name="<?php echo htmlspecialchars($field_key); ?>"
                            value="<?php echo htmlspecialchars($item[$field_key] ?? ''); ?>"
                            <?php echo $field_props['required'] ? 'required' : ''; ?>
                            <?php echo isset($field_props['step']) ? 'step="' . htmlspecialchars($field_props['step']) . '"' : ''; ?>
                        >
                    </div>
                <?php endforeach; ?>
                <button type="submit" class="button primary"><?php echo ($formType == 'edit') ? 'Обновить' : 'Добавить'; ?> <?php echo htmlspecialchars($entity_name_singular); ?></button>
                <a href="index.php?entity=<?php echo htmlspecialchars($entity); ?>&action=list" class="button back">Отмена</a>
            </form>
        </div>