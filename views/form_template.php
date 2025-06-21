<?php
$formType = $action_type ?? 'create';
$action_url = "index.php?entity=" . htmlspecialchars($entity) . "&action=" . htmlspecialchars($formType);

$item = $item ?? []; // Ensure $item is always an array

if ($formType == 'edit' && isset($entity_id_field) && isset($item[$entity_id_field])) {
    $action_url .= "&id=" . htmlspecialchars($item[$entity_id_field]);
}
?>
<div class="form-container">
    <form action="<?= htmlspecialchars($action_url) ?>" method="POST">
        <?php foreach ($form_fields as $field_key => $field_props):
            $value = $item[$field_key] ?? '';
            $type = $field_props['type'] ?? 'text';
            $required = $field_props['required'] ? 'required' : '';
            $step = $field_props['step'] ?? '';
        ?>
            <div class="form-group">
                <label for="<?= htmlspecialchars($field_key) ?>">
                    <?= htmlspecialchars($field_props['label']) ?>:
                </label>

                <?php if ($type == 'textarea'): ?>
                    <textarea id="<?= htmlspecialchars($field_key) ?>" name="<?= htmlspecialchars($field_key) ?>" <?= $required ?>><?= htmlspecialchars($value) ?></textarea>
                <?php else: ?>
                    <input type="<?= htmlspecialchars($type) ?>" id="<?= htmlspecialchars($field_key) ?>" name="<?= htmlspecialchars($field_key) ?>"
                           value="<?= htmlspecialchars($value) ?>" <?= $required ?> <?= $step ? "step='" . htmlspecialchars($step) . "'" : '' ?>>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>

        <div class="button-group">
            <button type="submit" class="button primary">
                <?= ($formType == 'edit') ? 'Обновить' : 'Добавить' ?>
                <?= htmlspecialchars($entity_name_singular) ?>
            </button>
            <a href="index.php?entity=<?= htmlspecialchars($entity) ?>&action=list"
               class="button back">
                Отмена
            </a>
        </div>
    </form>
</div>