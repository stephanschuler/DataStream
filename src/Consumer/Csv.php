<?php
declare(strict_types=1);

namespace StephanSchuler\DataStream\Consumer;

use StephanSchuler\DataStream\Runtime\GraphBuilder;

class Csv implements ConsumerInterface
{
    use ConsumerTrait;

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

    protected $name;

    protected function __construct(
        string $fileName,
        string $delimiter = ',',
        string $enclosure = '"',
        string $escape = '\\'
    ) {
        GraphBuilder::getInstance()->addNode($this);
        $this->fileName = $fileName;
        $this->delimiter = $delimiter;
        $this->enclosure = $enclosure;
        $this->escape = $escape;
    }

    public static function createConsumer(
        string $fileName,
        string $delimiter = ',',
        string $enclosure = '"',
        string $escape = '\\'
    ): Echoing {
        $className = get_called_class();
        return new $className($fileName, $delimiter, $enclosure, $escape);
    }

    public function consume($data)
    {
        $fp = fopen($this->fileName, 'a+');
        fputcsv($fp, $data, $this->delimiter, $this->enclosure, $this->escape);
        fclose($fp);
    }
}