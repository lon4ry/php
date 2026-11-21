<?php
declare(strict_types=1);

const BYE = 'ПОКА!';
const BYE_TIMES = 3;

echo "ЧЕГО СКАЗАТЬ-ТО ХОТЕЛ, МИЛОК?!\n";

$byeCount = 0;

while (true) {
    echo '> ';
    $line = fgets(STDIN);
    if ($line === false) {
        break;
    }
    $phrase = trim($line);

    // Считаются только отдельные «ПОКА!» подряд; любая другая фраза сбрасывает счётчик
    $byeCount = mb_strtoupper($phrase) === BYE ? $byeCount + 1 : 0;

    if ($byeCount === BYE_TIMES) {
        echo "ДО СВИДАНИЯ, МИЛЫЙ!\n";
        break;
    }

    if (str_ends_with($phrase, '!')) {
        echo 'НЕТ, НИ РАЗУ С ' . random_int(1930, 1950) . " ГОДА!\n";
    } else {
        echo "АСЬ?! ГОВОРИ ГРОМЧЕ, ВНУЧЕК!\n";
    }
}
