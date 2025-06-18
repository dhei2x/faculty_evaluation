<?php
function renderStatsCard($title, $value, $iconHtml, $extraClasses = '') {
    ?>
    <div class="border-2 border-gray-300 <?php echo htmlspecialchars($extraClasses); ?>">
        <div class="p-6 text-center">
            <div class="flex flex-col items-center">
                <h3 class="text-3xl font-bold text-gray-800 mb-2"><?php echo htmlspecialchars($value); ?></h3>
                <div class="flex items-center gap-2 text-gray-600">
                    <?php echo $iconHtml; ?>
                    <p class="text-sm"><?php echo htmlspecialchars($title); ?></p>
                </div>
            </div>
        </div>
    </div>
    <?php
}