<?php
$files = [
    'resources/views/layouts/customer.blade.php',
    'resources/views/pages/help.blade.php'
];
foreach ($files as $f) {
    if (!file_exists($f)) {
        echo "$f does not exist locally.\n";
        continue;
    }
    $c = file_get_contents($f);
    preg_match_all('/@(if|elseif|else|endif|auth|endauth|guest|endguest|foreach|endforeach|isset|endisset|empty|endempty)\b/', $c, $m);
    $stack = [];
    foreach ($m[0] as $tag) {
        if (in_array($tag, ['@if','@auth','@guest','@foreach','@isset','@empty'])) {
            $stack[] = $tag;
        } elseif (in_array($tag, ['@endif','@endauth','@endguest','@endforeach','@endisset','@endempty'])) {
            $expected = str_replace('end', '', $tag);
            $last = end($stack);
            if ($last === $expected) {
                array_pop($stack);
            } else {
                echo "Mismatch in " . $f . " found " . $tag . " expected end of " . $last . PHP_EOL;
            }
        }
    }
    if (count($stack) > 0) echo "Unclosed in " . $f . " " . implode(', ', $stack) . PHP_EOL;
    else echo $f . " is perfectly balanced!" . PHP_EOL;
}
