<?php

class UrlCrypt
{
    private $key;

    public function __construct($key = '')
    {
        $this->key = $key;
    }

    public function encode(int $id): string
    {
        $result1 = base_convert($id + 4321, 10, 35);
        $result2 = base_convert($id + 521, 10, 18);
        $result3 = base_convert($id + 234, 10, 16);
        $result4 = base_convert($id + 1904, 10, 33);
        $result5 = base_convert($id + 826, 10, 16);

        return $result1 . 'z' . $result2 . $result3 . 'y' . $result4 . 'z' . $result5;
    }

    public function decode(string $target): int
    {
        $probe1 = strpos($target, 'z');
        $probe2 = explode('z', $target);
        if ($probe1 !== false && count($probe2) === 3) {
            $probe3 = base_convert($probe2[0], 35, 10) - 4321;
            if ($target === $this->encode($probe3)) {
                $id = $probe3;
            }
        }

        return $id ?? 0;
    }
}