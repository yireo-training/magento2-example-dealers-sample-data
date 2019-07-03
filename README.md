# Example Dealers - Sample Data
To uninstall the sample data again, run:

    bin/magento example_dealers:list 
    bin/magento module:uninstall --non-composer Yireo_ExampleDealersSampleData
    bin/magento module:disable Yireo_ExampleDealersSampleData
    bin/magento setup:upgrade
    bin/magento example_dealers:list 

To re-add the sample data:

    bin/magento module:enable Yireo_ExampleDealersSampleData
    bin/magento setup:upgrade
    bin/magento example_dealers:list 