<?php
declare(strict_types=1);

namespace StephanSchuler\DataStream\Transport;

use StephanSchuler\DataStream\Provider\ProviderInterface;
use StephanSchuler\DataStream\Provider\ProviderTrait;

trait TransportTrait
{
    use ProviderTrait;

    /**
     * @var ProviderInterface[]
     */
    protected $provider = [];

    public function providedBy(ProviderInterface $provider)
    {
        /** @var TransportInterface $this */
        $provider->consumedBy($this);
        $this->provider[] = $provider;
    }

    public function provide()
    {
        foreach ($this->provider as $provider) {
            $provider->provide();
        }
    }

}