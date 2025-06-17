<?php
?>
    <h2 class="section-title">Список <?php echo htmlspecialchars($entity_name_plural); ?></h2>
    <?php
    ?>

    <?php if (!empty($items)): ?>
        <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <?php foreach ($display_fields as $field_key => $field_name): ?>
                        <th><?php echo htmlspecialchars($field_name); ?></th>
                    <?php endforeach; ?>
                    <th>Действия</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $item): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($item[$entity_id_field]); ?></td>
                        <?php foreach ($display_fields as $field_key => $field_name): ?>
                            <td>
                                <?php
                                // Специальная обработка для цены в списке
                                if ($field_key == 'price') {
                                    echo htmlspecialchars(number_format($item[$field_key] ?? 0, 2, ',', ' ')) . ' ₽';
                                } else {
                                    echo htmlspecialchars($item[$field_key] ?? 'Н/Д');
                                }
                                ?>
                            </td>
                        <?php endforeach; ?>
                        <td>
                            <div class="button-group">
                                <a href="index.php?entity=<?php echo htmlspecialchars($entity); ?>&action=detail&id=<?php echo htmlspecialchars($item[$entity_id_field]); ?>" class="button view">Просмотр</a>
                                <a href="index.php?entity=<?php echo htmlspecialchars($entity); ?>&action=edit&id=<?php echo htmlspecialchars($item[$entity_id_field]); ?>" class="button edit">Редактировать</a>
                                <?php
                                // Кнопка "Удалить" полностью удалена из этого шаблона, как запрошено.
                                ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p><?php echo htmlspecialchars($entity_name_plural); ?> пока не добавлены.</p>
    <?php endif; ?>