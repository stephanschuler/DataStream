<?php

use StephanSchuler\DataStream\Node\Consumer\Echoing;
use StephanSchuler\DataStream\Node\Consumer\Remember;
use StephanSchuler\DataStream\Runtime\GraphBuilder;
use StephanSchuler\DataStream\Runtime\Process;
use StephanSchuler\DataStream\Node\Source\XmlStream;
use StephanSchuler\DataStream\Node\Transport\Filter;
use StephanSchuler\DataStream\Node\Transport\Mapper;
use StephanSchuler\DataStream\Node\Transport\Merger;
use StephanSchuler\DataStream\Node\Transport\XpathSplitter;

return Process::define(function (GraphBuilder $runtime) {

    /***************************************************************
     * Define Provider
     *
     * @var XmlStream $products
     **************************************************************/

    $products = XmlStream::createSource($runtime->getSetting('filename'), '/root/products/product');


    /***************************************************************
     * Define Consumers
     *
     * @var Remember $lastProduct
     * @var XpathSplitter $variants
     * @var Filter $onlyGreen
     * @var Mapper $productNames
     * @var Mapper $merger
     **************************************************************/

    $lastProduct = Remember::createConsumer();

    $variants = XpathSplitter::createTransport('variants/variant');

    $onlyGreen = Filter::createTransport(function (SimpleXMLElement $data) {
        return !!$data->xpath('attribute[@type="color" and text()="green"]');
    });

    $productNames = Mapper::createTransport(function (SimpleXMLElement $data) {
        return (string)current($data->xpath('name'));
    });

    $merger = Merger::createTransport();


    /***************************************************************
     * Wire them
     **************************************************************/

    $lastProduct->providedBy($products);
    $variants->providedBy($products);
    $onlyGreen->providedBy($variants);

    $productNames->providedBy($products);

    $merger->providedBy($products);
    $merger->providedBy($variants);
    $merger->providedBy($onlyGreen);
    $merger->providedBy($productNames);


    /***************************************************************
     * Add some Echoers: In-Place definition and wiring
     **************************************************************/

    $products->consumedBy(Echoing::createConsumer('Product'));

    $variants->consumedBy(Echoing::createConsumer('Variant'));
    $onlyGreen->consumedBy(Echoing::createConsumer('Only Green'));
    $productNames->consumedBy(Echoing::createConsumer('Product Names'));

    $merger->consumedBy(Echoing::createConsumer('Wo ist der Merger?'));


    /***************************************************************
     * Add some labels
     **************************************************************/

    (function (GraphBuilder $runtime) use (&$products, &$productNames, &$lastProduct, &$variants) {

        $runtime
            ->setNodeLabel($products, 'Products')
            ->setNodeLabel($productNames, 'Product Name')
            ->setNodeLabel($lastProduct, 'Latest Product')
            ->setNodeLabel($variants, 'Variants');

    })($runtime);

});