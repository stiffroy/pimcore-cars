<?php

namespace App\Service;

use App\Helper\PropertyKeyHelper;
use Pimcore\Model\Asset;
use Pimcore\Model\DataObject\Car;
use Pimcore\Model\DataObject\Data\QuantityValue;
use Pimcore\Model\DataObject\QuantityValue\Unit;
use Pimcore\Model\Element\Service;
use Symfony\Component\Serializer\SerializerInterface;

class ImportService
{
    private const ASSET_LOCATION = 'var/assets';
    private const PARENT_ID = 1;

    public function __construct(private SerializerInterface $serializer)
    {
    }

    public function importCarsFromAsset(int $assetId): void
    {
        $assetContent = $this->getCsvContent($assetId);
        $infos = $this->serializer->decode($assetContent, 'csv');
        $this->convertToCarsAndSave($infos);
    }

    private function getCsvContent(int $assetId): string
    {
        $asset = Asset::getById($assetId);
        $assetPath = self::ASSET_LOCATION . $asset->getFullPath();

        return file_get_contents($assetPath);
    }

    private function convertToCarsAndSave(array $carInfos): void
    {
        foreach ($carInfos as $carInfo) {
            $car = $this->createCarObjects($carInfo);
            $car->setKey(Service::getValidKey($car->getArticleNumber(), 'object'))
                ->setPublished(true)
                ->save();
        }
    }

    private function createCarObjects(array $data): Car
    {
        $car = Car::getByArticleNumber($data[PropertyKeyHelper::ARTICLE_NUMBER], ['limit' => 1]) ?? new Car();

        $car->setParentId(self::PARENT_ID)
            ->setArticleNumber(trim($data[PropertyKeyHelper::ARTICLE_NUMBER]))
            ->setManufacturer(trim($data[PropertyKeyHelper::MANUFACTURER]))
            ->setModel(trim($data[PropertyKeyHelper::MODEL]))
            ->setDescription(trim($data[PropertyKeyHelper::DESCRIPTION_EN]), PropertyKeyHelper::LANGUAGE_ENGLISH)
            ->setDescription(trim($data[PropertyKeyHelper::DESCRIPTION_DE]), PropertyKeyHelper::LANGUAGE_GERMAN)
            ->setCylinders((int) trim($data[PropertyKeyHelper::CYLINDERS]))
            ->setCapacity((float) trim($data[PropertyKeyHelper::CAPACITY]))
            ->setHorsepower($this->convertToQuantityValue($data[PropertyKeyHelper::HORSEPOWER]))
            ->setProductionYear((int) trim($data[PropertyKeyHelper::PRODUCTION_YEAR]))
        ;

        return $car;
    }

    private function convertToQuantityValue(string $value): QuantityValue
    {
        return new QuantityValue((float) $value, $this->createOrGetUnit());
    }

    private function createOrGetUnit(): Unit
    {
        $unit = Unit::getByAbbreviation('kw');

        if (!$unit) {
            $unit = new Unit();
            $unit->setAbbreviation('kw')
                ->setLongname('Kilowatt')
                ->save();
        }

        return $unit;
    }
}
