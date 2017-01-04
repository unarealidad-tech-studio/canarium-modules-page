# Page

Enables Page functionality on Canarium. The page module allows administrators to view, create, update, and delete pages in the website. Each page can have a slug to make SEO friendly URLs.

# Installation

Install via composer: 

`composer require unarealidad/canarium-modules-page dev-master`

Add `Page` to your Appmaster's `config/application.config.php` or your Appinstance's `config/instance.config.php` under the key `modules`

Go to your Appinstance directory and run the following to update your database:

`./doctrine-module orm:schema-tool:update --force`

# Configuration

_None_

# Exposed Pages

URL | Template | Access | Description
----- | ----- | ----- | ----- | -----
/admin/page | admin/index.phtml | Admin | Displays the page CRUD management page.


\* All template locations are relative to the Appinstance root's /public/templates/page/. Sample templates are provided in the module's view/ directory.

# Additional Customization

## Created Page Template

The template for the created pages can be overriden by creating `/public/templates/page/page/index.phtml` in your appinstance directory. See the sample template in `view/page/page/index.phtml`.`

# Exposed Services
_None_
