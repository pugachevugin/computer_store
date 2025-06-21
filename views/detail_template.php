<div class="detail-card">
    <?php if (!empty($item)): ?>
        <?php foreach ($display_fields as $field_key => $field_name): ?>
            <p>
                <strong><?= htmlspecialchars($field_name) ?>:</strong>
                <?php
                $display_value = $item[$field_key] ?? '';

                if (in_array($field_key, ['price', 'salary'])) {
                    echo number_format((float)$display_value, 2, ',', ' ') . ' ₽';
                } elseif (strpos($field_key, 'date') !== false && $display_value) {
                    echo date('d.m.Y', strtotime($display_value));
                } else {
                    echo htmlspecialchars($display_value);
                }
                ?>
            </p>
        <?php endforeach; ?>

        <div class="button-group">
            <a href="?entity=<?= htmlspecialchars($entity) ?>&action=edit&id=<?= htmlspecialchars($item[$entity_id_field]) ?>"
               class="button edit">Редактировать</a>
            <a href="?entity=<?= htmlspecialchars($entity) ?>&action=softDelete&id=<?= htmlspecialchars($item[$entity_id_field]) ?>"
               class="button delete"
               onclick="return confirm('Переместить в корзину?')">В корзину</a>
            <a href="?entity=<?= htmlspecialchars($entity) ?>&action=list"
               class="button back">Назад к списку</a>
        </div>
    <?php else: ?>
        <p>Детали не найдены</p>
        <div class="button-group">
            <a href="?entity=<?= htmlspecialchars($entity) ?>&action=list" class="button back">Назад к списку</a>
        </div>
    <?php endif; ?>
</div>