<?php
declare(strict_types=1);

const BYE = 'ПОКА!';

echo "ЧЕГО СКАЗАТЬ-ТО ХОТЕЛ, МИЛОК?!\n";

while (true) {
    echo '> ';
    $line = fgets(STDIN);
    if ($line === false) {
        break;
    }
    $phrase = trim($line);

    if (mb_strtoupper($phrase) === BYE) {
        echo "ДО СВИДАНИЯ, МИЛЫЙ!\n";
        break;
    }

    if (str_ends_with($phrase, '!')) {
        echo 'НЕТ, НИ РАЗУ С ' . random_int(1930, 1950) . " ГОДА!\n";
    } else {
        echo "АСЬ?! ГОВОРИ ГРОМЧЕ, ВНУЧЕК!\n";
    }
}
