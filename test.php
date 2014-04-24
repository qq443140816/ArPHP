<?php
/**
 * 进程控制
 */
function dump()
{
    for ($i = 0; $i < 20; $i++) :
        echo $i . PHP_EOL;
    endfor;
}

$forkNum = 5;

for ($i = 0; $i < $forkNum; $i++) :
    $pid = pcntl_fork();
    if ($pid === -1)
        die('can not fork');

    if ($pid > 0) :
        $ok_id = pcntl_wait($status);
        echo 'child process pid '. $ok_id . 'ok' . PHP_EOL;
    else :
        echo posix_getpid() . PHP_EOL;
        dump();
    endif;
endfor;
