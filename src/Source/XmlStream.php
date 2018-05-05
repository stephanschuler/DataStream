<?php
declare(strict_types=1);

namespace StephanSchuler\DataStream\Source;

use StephanSchuler\DataStream\Provider\ProviderTrait;
use StephanSchuler\DataStream\Runtime\StateBuilder;
use StephanSchuler\DataStream\Scheduler\Task;
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

    protected function __construct(string $fileName, string $xpath)
    {
        StateBuilder::getInstance()->addNode($this);
        $this->fileName = $fileName;
        $this->xpath = $xpath;
    }

    public function provide()
    {
        Scheduler::current()->schedule(function () {

            $reader = new XMLReader();
            $reader->open($this->fileName);

            $iterator = new XMLElementIterator($reader);
            $list = new XMLElementXpathFilter($iterator, $this->xpath);

            /** @var XMLReaderNode $element */
            foreach ($list as $element) {
                yield;
                $this->feedConsumers($element->getSimpleXMLElement());
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