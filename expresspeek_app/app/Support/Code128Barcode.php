<?php

namespace App\Support;

use InvalidArgumentException;

final class Code128Barcode
{
    /**
     * Code 128 module-width patterns indexed by symbol value.
     *
     * Values 0-105 contain six alternating bar/space widths. The stop
     * character (106) contains seven widths.
     */
    private const PATTERNS = [
        '212222', '222122', '222221', '121223', '121322', '131222',
        '122213', '122312', '132212', '221213', '221312', '231212',
        '112232', '122132', '122231', '113222', '123122', '123221',
        '223211', '221132', '221231', '213212', '223112', '312131',
        '311222', '321122', '321221', '312212', '322112', '322211',
        '212123', '212321', '232121', '111323', '131123', '131321',
        '112313', '132113', '132311', '211313', '231113', '231311',
        '112133', '112331', '132131', '113123', '113321', '133121',
        '313121', '211331', '231131', '213113', '213311', '213131',
        '311123', '311321', '331121', '312113', '312311', '332111',
        '314111', '221411', '431111', '111224', '111422', '121124',
        '121421', '141122', '141221', '112214', '112412', '122114',
        '122411', '142112', '142211', '241211', '221114', '413111',
        '241112', '134111', '111242', '121142', '121241', '114212',
        '124112', '124211', '411212', '421112', '421211', '212141',
        '214121', '412121', '111143', '111341', '131141', '114113',
        '114311', '411113', '411311', '113141', '114131', '311141',
        '411131', '211412', '211214', '211232', '2331112',
    ];

    /**
     * Generate a self-contained, vector Code 128 barcode.
     *
     * Numeric strings with an even number of digits use compact Code Set C.
     * Other printable ASCII values use Code Set B.
     */
    public static function svg(string $value, int $height = 52, int $moduleWidth = 2): string
    {
        if ($value === '') {
            throw new InvalidArgumentException('A barcode value is required.');
        }

        if ($moduleWidth < 1 || $height < 1) {
            throw new InvalidArgumentException('Barcode dimensions must be positive.');
        }

        $symbols = self::symbols($value);
        $checksum = $symbols[0];

        foreach (array_slice($symbols, 1) as $index => $symbol) {
            $checksum += $symbol * ($index + 1);
        }

        $symbols[] = $checksum % 103;
        $symbols[] = 106;

        $quietModules = 10;
        $contentModules = array_sum(array_map(
            static fn (int $symbol): int => array_sum(array_map('intval', str_split(self::PATTERNS[$symbol]))),
            $symbols
        ));
        $width = ($contentModules + ($quietModules * 2)) * $moduleWidth;
        $x = $quietModules * $moduleWidth;
        $rectangles = [];

        foreach ($symbols as $symbol) {
            foreach (str_split(self::PATTERNS[$symbol]) as $index => $moduleCount) {
                $barWidth = ((int) $moduleCount) * $moduleWidth;

                if ($index % 2 === 0) {
                    $rectangles[] = sprintf(
                        '<rect x="%d" y="0" width="%d" height="%d"/>',
                        $x,
                        $barWidth,
                        $height
                    );
                }

                $x += $barWidth;
            }
        }

        return sprintf(
            '<svg xmlns="http://www.w3.org/2000/svg" width="%d" height="%d" viewBox="0 0 %d %d" role="img" aria-label="Code 128 barcode for %s"><rect width="100%%" height="100%%" fill="#fff"/><g fill="#000">%s</g></svg>',
            $width,
            $height,
            $width,
            $height,
            htmlspecialchars($value, ENT_QUOTES | ENT_XML1, 'UTF-8'),
            implode('', $rectangles)
        );
    }

    /**
     * @return list<int>
     */
    private static function symbols(string $value): array
    {
        if (ctype_digit($value) && strlen($value) % 2 === 0) {
            $symbols = [105]; // Start C

            foreach (str_split($value, 2) as $pair) {
                $symbols[] = (int) $pair;
            }

            return $symbols;
        }

        $symbols = [104]; // Start B

        foreach (str_split($value) as $character) {
            $ascii = ord($character);

            if ($ascii < 32 || $ascii > 126) {
                throw new InvalidArgumentException('Code 128 supports printable ASCII characters only.');
            }

            $symbols[] = $ascii - 32;
        }

        return $symbols;
    }
}
