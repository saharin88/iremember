<?php

arch('No debugging calls are used')
    ->expect(['dd', 'dump', 'ray', 'print_r', 'var_dump'])
    ->not->toBeUsed();
