<?php
declare(strict_types=1);

namespace StephanSchuler\DataStream\Node\Source;

use StephanSchuler\DataStream\Node\Provider\ProviderTrait;
use StephanSchuler\DataStream\Runtime\GraphBuilder;
use StephanSchuler\DataStream\Scheduler\Scheduler;

class Csv implements SourceInterface
{
    use ProviderTrait;

    /**
     * @var string
     */
    protected $fileName;

    /**
     * @var string
     */
    protected $delimiter;

    /**
     * @var string
     */
    protected $enclosure;

    /**
     * @var string
     */
    protected $escape;

    protected function __construct(string $fileName, string $delimiter, string $enclosure, string $escape)
    {
        GraphBuilder::getInstance()->addNode($this);
        $this->fileName = $fileName;
        $this->delimiter = $delimiter;
        $this->enclosure = $enclosure;
        $this->escape = $escape;
    }

    public function provide()
    {
        Scheduler::globalInstance()->schedule(function () {
            $fp = fopen($this->fileName, 'r');
            while ($line = fgetcsv($fp, 0, $this->delimiter, $this->enclosure, $this->escape)) {
                yield;
                $this->feedConsumers($line);
            }
            fclose($fp);
        });
    }

    public static function createSource(
        string $fileName,
        string $delimiter = ',',
        string $enclosure = '"',
        string $escape = '\\'
    ): Csv {
        $className = get_called_class();
        return new $className($fileName, $delimiter, $enclosure, $escape);
    }
}