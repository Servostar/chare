<?php

function create_link($varname, $title, $icon): void
{
    $env = getenv($varname);
    if (empty($env)) {
        return;
    }
    echo '<a href="'.$env.'" class="link '.$icon.'">'.$title.'</a>';
}

create_link("HOME_PAGE", "Home", "home-icon");
create_link("LEGAL_PAGE", "Legal", "legal-icon");
create_link("IMPRESSUM_PAGE", "Impressum", "impressum-icon");