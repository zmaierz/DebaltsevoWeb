<!-- <div class="info__docs-item">
    <h3 class="info__docs-item-title">Требования:</h3>
    <a href="engine/templates/media/docs/testPage/2_5373096858589619571.pdf">Тык</a>
</div> -->


<?php

function getPageInfoFileBlock_HTML(?array $files): string {
    $out = "";

    foreach ($files as $file) {
        $out .= '<div class="info__docs-item">';
        $out .= '<h3 class="info__docs-item-title">' . $file['name'] . ':</h3>';
        $out .= '<a href="' . $file["path"] . '">Скачать</a></div>';
    }

    return $out;
}