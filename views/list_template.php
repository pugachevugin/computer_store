<div class="button-group">
    <a href="?entity=<?= htmlspecialchars($entity) ?>&action=create" class="button add">Добавить</a>
    <a href="?entity=<?= htmlspecialchars($entity) ?>&action=trashed" class="button trash">Корзина</a>
</div>

<?php if (!empty($items)): ?>
    <table class="data-table">
        <thead>
            <tr>
                <th>№</th> <?php foreach ($display_fields as $field_key => $field_name): ?>
                    <th><?= htmlspecialchars($field_name) ?></th>
                <?php endforeach; ?>
                <th>Действия</th>
            </tr>
        </thead>
        <tbody>
            <?php $rowNumber = 1; // Initialize row counter ?>
            <?php foreach ($items as $item): ?>
                <tr>
                    <td><?= $rowNumber++ ?></td> <?php foreach ($display_fields as $field_key => $field_name): ?>
                        <td>
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
                        </td>
                    <?php endforeach; ?>
                    <td class="action-buttons">
                        <div class="button-group">
                            <a href="?entity=<?= htmlspecialchars($entity) ?>&action=detail&id=<?= htmlspecialchars($item[$entity_id_field]) ?>"
                               class="button view">Просмотр</a>
                            <a href="?entity=<?= htmlspecialchars($entity) ?>&action=edit&id=<?= htmlspecialchars($item[$entity_id_field]) ?>"
                               class="button edit">Редактировать</a>
                            <a href="?entity=<?= htmlspecialchars($entity) ?>&action=softDelete&id=<?= htmlspecialchars($item[$entity_id_field]) ?>"
                               class="button delete"
                               onclick="return confirm('Переместить в корзину?')">Удалить</a>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <p>Нет данных для отображения</p>
<?php endif; ?>