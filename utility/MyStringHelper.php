<?php
class MyStringHelper {
    public static function extname($string) {
        preg_match('/^(?:\.*[^.]+\.*)+(\.[^.]+)$/u', basename($string), $matches);
        $extname = isset($matches[1]) ? $matches[1] : '';

        return $extname;
    }

    public static function ext($string, $newext = '') {
        if (in_array($string, array('.', '..'))) {
            return $string;
        }

        if ($newext !== '') {
            $newext = preg_match('/^\./u', $newext) ? $newext : ('.' . $newext);
        }

        $extname = self::extname($string);

        return ($extname !== '') ? substr($string, 0, strrpos($string, $extname)) . $newext : $string . $newext;
    }

    protected static function pathmap_explode($string) {
        $head = dirname($string);
        $tail = basename($string);

        if ($head === $string) {
            return array($string);
        }

        if ($head === '.' || $tail === '/') {
            return array($tail);
        }

        if ($head === '/') {
            return array($head, $tail);
        }

        return array_merge(self::pathmap_explode($head), array($tail));
    }

    protected static function pathmap_partial($string, $n) {
        $dirs = self::pathmap_explode(dirname($string));

        if ($n > 0) {
            $partial_dirs = array_slice($dirs, 0, $n);
        } elseif ($n < 0) {
            $partial_dirs = array_reverse(array_slice(array_reverse($dirs), 0, -$n));
        } else {
            $partial_dirs = '.';
        }

        return join($partial_dirs, DIRECTORY_SEPARATOR);
    }

    protected static function pathmap_replace($string, $patterns) {
        $result = $string;
        $pairs = explode(';', $patterns);

        foreach ($pairs as $pair) {
            list($pattern, $replacement) = explode(',', $pair);
            $pattern = '/' . str_replace('/', '\\/', $pattern) . '/u';

            if ($replacement) {
                $result = preg_replace($pattern, $replacement, $result, 1);
            } else {
                $result = preg_replace($pattern, '', $result, 1);
            }
        }

        return $result;
    }

    public static function pathmap($string, $spec = null) {
        if ($spec === null) {
            return $string;
        }

        $result = '';
        preg_match_all('/%\{[^}]*\}-?\d*[sdpfnxX%]|%-?\d+d|%.|[^%]+/u', $spec, $frags);

        foreach ($frags[0] as $frag) {
            if ($frag === '%f') {
                $result .= basename($string);
            } elseif ($frag === '%n') {
                $result .= self::ext(basename($string));
            } elseif ($frag === '%d') {
                $result .= dirname($string);
            } elseif ($frag === '%x') {
                $result .= self::extname($string);
            } elseif ($frag === '%X') {
                $result .= self::ext($string);
            } elseif ($frag === '%p') {
                $result .= $string;
            } elseif ($frag === '%s') {
                $result .= DIRECTORY_SEPARATOR;
            } elseif ($frag === '%-') {
                // do nothing
            } elseif ($frag === '%%') {
                $result .= "%";
            } elseif (preg_match('/%(-?\d+)d/u', $frag, $matches)) {
                $result .= self::pathmap_partial($frag, (int) $matches[1]);
            } elseif (preg_match('/^%\{([^}]*)\}(\d*[dpfnxX])/u', $frag, $matches)) {
                $patterns = $matches[1];
                $operator = $matches[2];
                $result .= self::pathmap_replace(self::pathmap($string, '%' . $operator), $patterns);
            } elseif (preg_match('/^%/u', $frag)) {
                throw new InvalidArgumentException("Unknown pathmap specifier {$frag} in '{$spec}'");
            } else {
                $result .= $frag;
            }
        }

        return $result;
    }
}
