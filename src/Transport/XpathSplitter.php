<?php
declare(strict_types=1);

namespace StephanSchuler\DataStream\Transport;

use SimpleXMLElement;

class XpathSplitter implements TransportInterface, GeneratorInterface
{
    use SplitterTrait;

    public static function createTransport(string $xpath): XpathSplitter
    {
        $splitter = function (SimpleXMLElement $data) use ($xpath) {
            foreach ($data->xpath($xpath) as $variant) {
                yield $variant;
            }
        };
        $className = get_called_class();
        return new $className($splitter);
    }
}