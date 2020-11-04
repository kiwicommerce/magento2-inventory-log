# We're not maintaining this extension, if you need any support please contact us at hello@kiwicommerce.co.uk

## Magento 2 - Inventory Log by KiwiCommerce

### Overview
- Keep the log of product inventory for every single transaction. Ex. Product import, Order placed, Insert/Update operation through queries and lot more.
- Admin knows when and why the inventory changed.
- This extension keeps a log of inventory when
	- Order placed from frontend, admin and API
	- Product import
	- Manually product inventory change by admin
	- Directly inventory insert update in a database
	- Product created using API
	- Credit Memo generation
	- Order fail and Cancel
### **Installation**
 
 1. Composer Installation
      - Navigate to your Magento root folder<br />
            `cd path_to_the_magento_root_directory`<br />
      - Then run the following command<br />
          `composer require kiwicommerce/module-inventory-log`<br/>
      - Make sure that composer finished the installation without errors.

 2. Command Line Installation
      - Backup your web directory and database.
      - Download Inventory Log installation package from <a href="https://github.com/kiwicommerce/magento2-inventory-log/releases/download/v1.0.1/kiwicommerce-inventory-log-v101.zip">here</a>.
      - Upload contents of the Inventory Log installation package to your Magento root directory.
      - Navigate to your Magento root folder<br />
          `cd path_to_the_magento_root_directory`<br />
      - Then run the following command<br />
          `php bin/magento module:enable KiwiCommerce_InventoryLog`<br />
      - Log out from the backend and log in again.
   
- After install the extension, run the following command <br/>
          `php bin/magento setup:upgrade`<br />
          `php bin/magento setup:di:compile`<br />
          `php bin/magento setup:static-content:deploy`<br />
          `php bin/magento cache:flush`
          
Find More details on <a href="https://kiwicommerce.co.uk/extensions/magento2-inventory-log/" target="_blank">KiwiCommerce</a>

## Features

### Navigate to Products > Catalog

Navigate to Product > Catalog and Click on edit link of the product

<img src="https://kiwicommerce.co.uk/wp-content/uploads/2018/05/Product-View-Original.png"/><br/>

### Product Inventory log grid
Click on *Inventory log* link and you shall have respective product inventory log grid.

<img src="https://kiwicommerce.co.uk/wp-content/uploads/2018/05/Inventory-Grid-Original.png"/><br/>


### Configuration

You need to follow this path. Stores > Configuration > KIWICOMMERCE EXTENSIONS > Inventory Log

<img src="https://kiwicommerce.co.uk/wp-content/uploads/2018/05/Store-Config-Original.png"/> <br/>

## Constraints
- Database user must have to get the create trigger permission in order to use this extension.
- After enabling disabled extension using the command, admin/user will have to enable the extension again from the store configuration

## Contribution
Well unfortunately there is no formal way to contribute, we would encourage you to feel free and contribute by:
 
  - Creating bug reports, issues or feature requests on <a target="_blank" href="https://github.com/kiwicommerce/magento2-inventory-log/issues">Github</a>
  - Submitting pull requests for improvements.
    
We love answering questions or doubts simply ask us in issue section. We're looking forward to hearing from you!
 
  - Follow us <a href="https://twitter.com/KiwiCommerce">@KiwiCommerce</a>
  - <a href="mailto:support@kiwicommerce.co.uk">Email Us</a>
  - Have a look at our <a href="https://kiwicommerce.co.uk/docs/inventory-log/">documentation</a> 

