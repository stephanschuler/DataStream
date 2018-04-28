<?php
declare(strict_types=1);

namespace StephanSchuler\DataStream\Provider;

use ArrayIterator;
use Iterator;

class Sequence implements ProviderInterface
{
    use ProviderTrait;

    /**
     * @var Iterator
     */
    protected $source;

    protected function __construct($source)
    {
        $this->source = $source;
    }

    public function provide()
    {
        foreach ($this->source as $element) {
            foreach ($this->consumers as $consumer) {
                $consumer->consume($element);
            }
        }
    }

    public static function createProvider(Iterator $source): Sequence
    {
        $className = get_called_class();
        return new $className($source);
    }

    public static function createProviderFromArray(array $source): Sequence
    {
        $callable = [get_called_class(), 'createProvider'];
        return $callable(new ArrayIterator($source));
    }

    public static function createProviderOf(...$source): Sequence
    {
        $callable = [get_called_class(), 'createProviderFromArray'];
        return $callable($source);
    }
}