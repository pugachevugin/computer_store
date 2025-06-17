<?php

?>
        <h2 class="section-title">Детали <?php echo htmlspecialchars($entity_name_singular); ?></h2>
        <div class="detail-card">
            <?php if (!empty($item)): ?>
                <?php foreach ($display_fields as $field_key => $field_name): ?>
                    <p><strong><?php echo htmlspecialchars($field_name); ?>:</strong>
                        <?php
                        // Специальная обработка для цены
                        if ($field_key == 'price') {
                            echo htmlspecialchars(number_format($item[$field_key] ?? 0, 2, ',', ' ')) . ' ₽';
                        } else {
                            echo htmlspecialchars($item[$field_key] ?? 'Н/Д');
                        }
                        ?>
                    </p>
                <?php endforeach; ?>
                <div class="detail-actions"> <a href="index.php?entity=<?php echo htmlspecialchars($entity); ?>&action=edit&id=<?php echo htmlspecialchars($item[$entity_id_field]); ?>" class="button edit">Редактировать</a>
                    <a href="index.php?entity=<?php echo htmlspecialchars($entity); ?>&action=list" class="button back">Назад к списку</a>
          
                </div>
            <?php else: ?>
                <p>Детали <?php echo htmlspecialchars($entity_name_singular); ?> не найдены.</p>
                <div class="button-group back-button"> <a href="index.php?entity=<?php echo htmlspecialchars($entity); ?>&action=list" class="button back">Назад к списку</a>
                </div>
            <?php endif; ?>
        </div>