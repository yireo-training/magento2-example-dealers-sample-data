<?php
declare(strict_types=1);

namespace Yireo\ExampleDealersSampleData\Setup\Patch\Data;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\PatchRevertableInterface;
use Yireo\ExampleDealers\Api\DealerRepositoryInterface;

/**
 * Class SampleData
 * @package Yireo\ExampleDealersSampleData\Setup\Patch\Data
 */
class SampleData implements DataPatchInterface, PatchRevertableInterface
{
    /**
     * @var DealerRepositoryInterface
     */
    private $dealerRepository;

    /**
     * SampleData constructor.
     * @param DealerRepositoryInterface $dealerRepository
     */
    public function __construct(
        DealerRepositoryInterface $dealerRepository
    ) {
        $this->dealerRepository = $dealerRepository;
    }

    /**
     * @return string[]
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * Get aliases (previous names) for the patch.
     *
     * @return string[]
     */
    public function getAliases()
    {
        return [];
    }

    /**
     * @return $this
     */
    public function apply()
    {
        foreach ($this->getSampleData() as $sample) {
            $dealer = $this->dealerRepository->getEmpty();
            $dealer->setName($sample['name']);
            $dealer->setAddress($sample['address']);
            $this->dealerRepository->save($dealer);
        }
    }

    /**
     * @return void
     */
    public function revert()
    {
        foreach ($this->getSampleData() as $sample) {
            /** @var SearchCriteriaBuilder $searchCriteriaBuilder */
            $searchCriteriaBuilder = $this->dealerRepository->getSearchCriteriaBuilder();
            $searchCriteriaBuilder->addFilter('name', $sample['name']);
            $searchCriteriaBuilder->addFilter('address', $sample['address']);
            $searchCriteria = $searchCriteriaBuilder->create();
            $items = $this->dealerRepository->getItems($searchCriteria);
            foreach ($items as $item) {
                $this->dealerRepository->delete($item);
            }
        }
    }

    /**
     * @return array
     */
    private function getSampleData(): array
    {
        return [
            [
                'name' => 'Batman',
                'address' => 'Gotham, USA',
            ],
            [
                'name' => 'Spiderman',
                'address' => 'New York, USA'
            ]
        ];
    }
}
