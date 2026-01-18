<?php

class MBLTranslationHeaders extends ArrayIterator
{
    private $map = array();

    public function __construct(array $raw = array())
    {
        if ($raw) {
            $keys = array_keys($raw);
            $this->map = array_combine(array_map('strtolower', $keys), $keys);
            parent::__construct($raw);
        }
    }

    public function normalize($key)
    {
        $k = strtolower($key);

        return isset($this->map[$k]) ? $this->map[$k] : null;
    }

    public function add($key, $val)
    {
        $this->offsetSet($key, $val);

        return $this;
    }

    public function __toString()
    {
        $pairs = array();
        foreach ($this as $key => $val) {
            $pairs[] = trim($key) . ': ' . $val;
        }

        return implode("\n", $pairs);
    }

    public function trimmed($prop)
    {
        return trim($this->__get($prop));
    }

    public function has($key)
    {
        $k = strtolower($key);

        return isset($this->map[$k]);
    }

    public function __get($key)
    {
        return $this->offsetGet($key);
    }

    public function __set($key, $val)
    {
        $this->offsetSet($key, $val);
    }

    public function offsetExists($key): bool
    {
        return !is_null($this->normalize($key));
    }

    #[ReturnTypeWillChange]
    public function offsetGet($key)
    {
        $key = $this->normalize($key);
        if (is_null($key)) {
            return '';
        }

        return parent::offsetGet($key);
    }

    public function offsetSet($key, $value): void
    {
        $k = strtolower($key);
        if (isset($this->map[$k]) && $key !== $this->map[$k]) {
            parent::offsetUnset($this->map[$k]);
        }
        $this->map[$k] = $key;

        parent::offsetSet($key, $value);
    }

    public function offsetUnset($key): void
    {
        $k = strtolower($key);
        if (isset($this->map[$k])) {
            parent::offsetUnset($this->map[$k]);
            unset($this->map[$k]);
        }
    }

    public function export()
    {
        return $this->getArrayCopy();
    }

    public function jsonSerialize()
    {
        return $this->getArrayCopy();
    }

    public function toArray()
    {
        return $this->getArrayCopy();
    }

    public function keys()
    {
        return array_values($this->map);
    }
}
