README
======

[![Build Status](https://secure.travis-ci.org/arcanacode/ContentBundle.png?branch=master)](http://travis-ci.org/arcanacode/ContentBundle)
[![Latest Stable Version](https://poser.pugx.org/arcanacode/content-bundle/version.png)](https://packagist.org/packages/arcanacode/content-bundle)
[![Total Downloads](https://poser.pugx.org/arcanacode/content-bundle/d/total.png)](https://packagist.org/packages/arcanacode/content-bundle)

License here.

What is Arcana Content Bundle?
-----------------

Arcana Content Bundle allows administrators to edit texts directly in the page, not some separate Admin section.

Requirements
------------

* Symfony 2.4.x
* See also the `require` section of [composer.json](composer.json)

Installation
------------

Bundle can be installed via Composer.
You can find this bundle on packagist: https://packagist.org/packages/arcanacode/content-bundle

<pre>
<code>
// composer.json
{
    // ...
    require: {
        // ..
        "arcanacode/content-bundle": "dev-master"

    }
}
</code>
</pre>

Then, you can install the new dependencies by running Composer's update command from the directory where your composer.json file is located:

<pre>
<code>
    php composer.phar update
</code>
</pre>

You have to add this bundle to `AppKernel.php` register bundles method, so that Symfony can use it.
<pre>
// in AppKernel::registerBundles()
$bundles = array(
    // ...
    new Arcana\Bundle\ContentBundle\ArcanaContentBundle(),
);
</pre>

In your `config.yml` you must add this bundle to jms_di_extra.

<pre>
jms_di_extra:
    locations:
        bundles: [ ArcanaContentBundle ]
</pre>

Next add bundle to the 'app/config/routing.yml' file.
<pre>
arcana_content:
    resource: "@ArcanaContentBundle/Controller/"
    type:     annotation
</pre>

In base template file include stylesheets and javascripts for only users with ROLE_ADMIN
<pre>
{% if is_granted('ROLE_ADMIN') %}
    {% stylesheets filter="cssrewrite"
        "bundles/arcanacontent/vendor/raptor/raptor-custom-front-end.min.css"
    %}
        <link rel="stylesheet" href="{{ asset_url }}" />
    {% endstylesheets %}
{% endif %}
{% if is_granted('ROLE_ADMIN') %}
    {% javascripts
        "bundles/arcanacontent/vendor/raptor/raptor.custom.min.js"
        "bundles/arcanacontent/js/manager.js"
    %}
        <script src="{{ asset_url }}"></script>
    {% endjavascripts %}

    <script>
        window.arcana_content_manager.contentSaveUrl = '{{ path('arcana_content_save') }}';
    </script>
{% endif %}
</pre>

In your security.yml file restrict access for arcana_content_save path only to administrators.
<pre>
access_control:
    //..
    - { path: ^/save, roles: ROLE_ADMIN }
</pre>

Finally run app/console doctrine:schema:update --force to create 'content' table in your database.

Usage
-------------

In order to use the bundle, all texts you want to be able to edit, must be added to template with content filter in such format:
<pre>
{{ 'default value' | content('name', {options}) }}
</pre>
'default value' is the default text, that will appear if no content is found in database.
'name' is the name of content in the content table.
'options' - array of options (listed below).
Example:
<pre>
{{ 'Welcome to the Arcana Content Bundle!' | content('default_page_title', { editable_separately: true, type: 'plaintext' }) }}
</pre>

Contributing
------------

Pull requests are welcome.

Description here.

Running Symfony2 Tests
----------------------

Description here.
