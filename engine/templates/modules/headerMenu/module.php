<?php

    function getHeaderMenu(?array $menuData): string {
        $out = "";
        foreach ($menuData[0] as $category) {
            $out .= '<li class="header-menu__li-sub1"><a href="/' . $category["url"] . '">' . $category["name"] . '</a>';
            if (count($category["pages"]) == 0)
                $out .= '</li>';
            else {
                $out .= '<ul class="header-menu__sub header-menu__ul-sub1">';
                foreach ($category["pages"] as $page) {
                    $out .= '<a href="/' . $category["url"] . '/' . $page["alias"] . '"><li>' . $page["name"] . '</li></a>';
                }
                $out .= '</ul></li>';
            }
        }
        return $out;
    }

?>