<?php declare(strict_types=1);

namespace Yireo\ExampleDealersSampleData\Setup\Patch\Data;

use Magento\Framework\Api\Filter;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Magento\Framework\Setup\ModuleDataSetupInterface;
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
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;

    /**
     * SampleData constructor.
     * @param DealerRepositoryInterface $dealerRepository
     * @param ModuleDataSetupInterface $moduleDataSetup
     */
    public function __construct(
        DealerRepositoryInterface $dealerRepository,
        ModuleDataSetupInterface $moduleDataSetup,
    ) {
        $this->dealerRepository = $dealerRepository;
        $this->moduleDataSetup = $moduleDataSetup;
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
        $this->moduleDataSetup->getConnection()->startSetup();

        foreach ($this->getSampleData() as $sample) {
            $dealer = $this->dealerRepository->getEmpty();
            $dealer->setName($sample['name']);
            $dealer->setAddress($sample['address']);
            $dealer->setDescription('');
            $this->dealerRepository->save($dealer);
        }

        $this->moduleDataSetup->getConnection()->endSetup();
        return $this;
    }

    /**
     * @return void
     */
    public function revert()
    {
        $this->moduleDataSetup->getConnection()->startSetup();

        foreach ($this->getSampleData() as $sample) {
            /** @var SearchCriteriaBuilder $searchCriteriaBuilder */
            $searchCriteriaBuilder = $this->dealerRepository->getSearchCriteriaBuilder();
            $searchCriteriaBuilder->addFilter(new Filter(['field' => 'id', 'value' => $sample['id']]));
            $searchCriteria = $searchCriteriaBuilder->create();
            $items = $this->dealerRepository->getItems($searchCriteria);
            foreach ($items as $item) {
                $this->dealerRepository->delete($item);
            }
        }

        $this->moduleDataSetup->getConnection()->endSetup();
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
