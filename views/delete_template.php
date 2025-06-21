<div class="detail-card">
    <p>Вы уверены, что хотите удалить <?= htmlspecialchars($entity_name_singular) ?>?</p>

    <?php if ($entity == 'product'): ?>
        <p><strong>Название:</strong> <?= htmlspecialchars($item['product_name'] ?? '') ?></p>
        <p><strong>Производитель:</strong> <?= htmlspecialchars($item['manufacturer'] ?? '') ?></p>
    <?php elseif ($entity == 'employee'): ?>
        <p><strong>Имя:</strong> <?= htmlspecialchars($item['first_name'] ?? '') ?> <?= htmlspecialchars($item['last_name'] ?? '') ?></p>
    <?php elseif ($entity == 'client'): ?>
        <p><strong>Имя:</strong> <?= htmlspecialchars($item['first_name'] ?? '') ?> <?= htmlspecialchars($item['last_name'] ?? '') ?></p>
    <?php endif; ?>

    <form action="?entity=<?= htmlspecialchars($entity) ?>&action=delete&id=<?= htmlspecialchars($item[$entity_id_field]) ?>" method="POST">
        <button type="submit" name="confirm_delete" class="button delete">Удалить навсегда</button>
        <a href="index.php?entity=<?= htmlspecialchars($entity) ?>&action=trashed" class="button back">Отмена</a>
    </form>
</div>