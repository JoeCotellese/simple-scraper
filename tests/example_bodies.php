<?php 
namespace Ramonztro\SimpleScraper;
class ExampleBodies
{
    public $example1 = <<<EOT
        <!DOCTYPE html>
        <html lang="en-US" prefix="og: http://ogp.me/ns#">
        <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <title>This is a test title</title>
        <meta name="description" content="This is a test description"/>
        <link rel="canonical" href="https://www.example.com/canonical" />
        <link rel="publisher" href="https://www.example.com/publisher"/>
        <meta property="og:locale" content="en_US" />
        <meta property="og:type" content="website" />
        <meta property="og:title" content="This is an OG title" />
        <meta property="og:description" content="This is an OG description" />
        <meta property="og:url" content="https://www.example.com/" />
        <meta property="og:site_name" content="OG Sitename" />
        <meta property="og:image" content="https://www.example.com/example_image.png" />
        <meta property="og:image:secure_url" content="https://www.example.com/example_image.png" />
        <meta property="og:image:width" content="100" />
        <meta property="og:image:height" content="200" />
        <meta name="twitter:card" content="summary_large_image" />
        <meta name="twitter:description" content="Twitter card description" />
        <meta name="twitter:title" content="Twitter title" />
        <meta name="twitter:site" content="@TwitterSite" />
        <meta name="twitter:image" content="https://i1.wp.com/www.getsharey.com/example_image.png" />
        <meta name="twitter:creator" content="@TwitterCreator" />
        </head>
        <body>
        </body>
        </html>
EOT;

}