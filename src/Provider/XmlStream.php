<?php
declare(strict_types=1);

namespace StephanSchuler\DataStream\Provider;

use StephanSchuler\DataStream\Runtime\RuntimeState;
use XMLElementIterator;
use XMLElementXpathFilter;
use XMLReader;
use XMLReaderNode;

class XmlStream implements ProviderInterface
{
    use ProviderTrait;

    /**
     * @var string
     */
    protected $fileName;

    /**
     * @var string
     */
    protected $xpath = '/*';

    protected function __construct(string $fileName, string $xpath)
    {
        RuntimeState::getInstance()->addNode($this);
        $this->fileName = $fileName;
        $this->xpath = $xpath;
    }

    public function provide()
    {
        $reader = new XMLReader();
        $reader->open($this->fileName);

        $iterator = new XMLElementIterator($reader);
        $list = new XMLElementXpathFilter($iterator, $this->xpath);
        /** @var XMLReaderNode $element */
        foreach ($list as $element) {
            foreach ($this->consumers as $consumer) {
                $consumer->consume($element->getSimpleXMLElement());
            }
        }

        $reader->close();
    }

    public static function createProvider(string $fileName, string $xpath = '/*'): XmlStream
    {
        $className = get_called_class();
        return new $className($fileName, $xpath);
    }
}