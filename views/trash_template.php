<div class="container">
    <h2 class="section-title">Корзина: <?php echo htmlspecialchars($entity_name_plural); ?></h2>
    <div class="actions">
        <a href="index.php?entity=<?php echo htmlspecialchars($entity); ?>&action=list" class="button back">Назад к списку</a>
    </div>
    <?php if (!empty($items)): ?>
        <table class="data-table">
            <thead>
                <tr>
                    <th>№</th>
                    <?php foreach ($display_fields as $field_key => $field_name): ?>
                        <th><?php echo htmlspecialchars($field_name); ?></th>
                    <?php endforeach; ?>
                    <th>Действия</th>
                </tr>
            </thead>
            <tbody>
                <?php $rowNumber = 1; ?>
                <?php foreach ($items as $item): ?>
                    <tr>
                        <td><?= $rowNumber++ ?></td>
                        <?php foreach ($display_fields as $field_key => $field_name): ?>
                            <td>
                                <?php
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
                                <a href="index.php?entity=<?php echo htmlspecialchars($entity); ?>&action=restore&id=<?php echo htmlspecialchars($item[$entity_id_field]); ?>" class="button primary">Восстановить</a>
                                <form action="index.php?entity=<?php echo htmlspecialchars($entity); ?>&action=delete&id=<?php echo htmlspecialchars($item[$entity_id_field]); ?>" method="POST" style="display:inline;">
                                    <button type="submit" name="confirm_delete" class="button delete" onclick="return confirm('Вы уверены, что хотите окончательно удалить этот элемент? Это действие необратимо!');">Удалить навсегда</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>Корзина пуста.</p>
    <?php endif; ?>

    <?php
    // Удален блок:
    // if (isset($_GET['message'])):
    //     <div class="message <?php echo htmlspecialchars($_GET['message_type'] ?? 'info'); ">
    //         <?php echo htmlspecialchars($_GET['message']);
    //     </div>
    // endif;
    ?>
</div>