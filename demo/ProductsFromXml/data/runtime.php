<?php

use StephanSchuler\DataStream\Consumer\Echoing;
use StephanSchuler\DataStream\Consumer\Remember;
use StephanSchuler\DataStream\Runtime\Runtime;
use StephanSchuler\DataStream\Runtime\StateBuilder;
use StephanSchuler\DataStream\Source\XmlStream;
use StephanSchuler\DataStream\Transport\Filter;
use StephanSchuler\DataStream\Transport\Mapper;
use StephanSchuler\DataStream\Transport\Merger;
use StephanSchuler\DataStream\Transport\XpathSplitter;

return Runtime::defineNetwork(function (StateBuilder $runtime) {

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

    (function (StateBuilder $runtime) use (&$products, &$productNames, &$lastProduct, &$variants) {

        $runtime
            ->setNodeLabel($products, 'Products')
            ->setNodeLabel($productNames, 'Product Name')
            ->setNodeLabel($lastProduct, 'Latest Product')
            ->setNodeLabel($variants, 'Variants');

    })($runtime);

});