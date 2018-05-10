<?php
declare(strict_types=1);

namespace StephanSchuler\DataStream\Node\Source;

use StephanSchuler\DataStream\Node\Provider\ProviderTrait;
use StephanSchuler\DataStream\Runtime\GraphBuilder;
use StephanSchuler\DataStream\Scheduler\Scheduler;
use XMLElementIterator;
use XMLElementXpathFilter;
use XMLReader;
use XMLReaderNode;

class XmlStream implements SourceInterface
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

    public function __construct(string $fileName, string $xpath)
    {
        GraphBuilder::getInstance()->addNode($this);
        $this->fileName = $fileName;
        $this->xpath = $xpath;
    }

    public function provide()
    {
        Scheduler::globalInstance()->enqueueTask($this, function () {

            $reader = new XMLReader();
            $reader->open($this->fileName);

            $iterator = new XMLElementIterator($reader);
            $list = new XMLElementXpathFilter($iterator, $this->xpath);

            /** @var XMLReaderNode $element */
            foreach ($list as $element) {
                yield;
                $this->feedConsumers(clone $element->getSimpleXMLElement());
            }

            $reader->close();
        });
    }

    public static function createSource(string $fileName, string $xpath = '/*'): XmlStream
    {
        $className = get_called_class();
        return new $className($fileName, $xpath);
    }
}