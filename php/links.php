<?php

function create_link($varname, $title): void
{
    $env = getenv($varname);
    if (empty($env)) {
        return;
    }
    echo '<a href="'.$env.'" class="link">'.$title.'</a>';
}

create_link("HOME_PAGE", "Home");
create_link("LEGAL_PAGE", "Legal");
create_link("IMPRESSUM_PAGE", "Impressum");