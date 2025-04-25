<?php

function generateFooterCode(?array $pagesList): string {
    $out = "";

    foreach ($pagesList[0] as $category) {
        $out .= '<div class="rigth-block__menu-content-block">';
        $out .= "<h3><a href=\"/" . $category['url'] . "\">" . $category['name'] . "</a></h3>";
        
        foreach ($category["pages"] as $page) {
            $out .= "<h4><a href=\"/" . $category['url'] . "/?page=" . $page['alias'] . "\">" . $page['name'] . "</a></h4>";
        }

        $out .= '</div>';
    }

    return $out;
}

?>