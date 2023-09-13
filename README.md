Contao Content Templates
========================

In Contao, the regular content of a page can be made up of different articles, each assigned to different sections of a page layout, and each containing different content elements which in turn can be of different types and contain different settings and CSS classes.

When implementing a website with complex layouts and designs in Contao, it can be helpful for the editors to have boilerplates available for different types of content pages. This extension allows you to create articles and content elements outside the regular site structure as "content templates", containing the appropriate settings for each article and content element and dummy or example texts within the content elements. These content templates can then be applied to any regular page in the site structure. The articles and content elements will then be copied over to the target page.

The extension also allows you to re-apply a content template again to a site. This will not override any actual textual content, but, if configured, will instead update other properties of each article and content element with the ones from the source template. Also any new article or content element, that did not exist in the target, will then be created.

## Applying Content Templates

Within the site structure in the back end, there will be a new global operation called _Apply content template_. When activated, you will have the opportunity to select a regular page in your site structure to which you want to apply a content template to. Once a page has been selected, a pop-up will show allowing you to select the content template. The conent template will be applied after confirming the selection.

By default any previously existing articles will remain in the target page. However, if you want to automatically delete _empty_ articles in the target, the following option can be enabled in the bundle configuration:

```yaml
# config/config.yaml
contao_content_templates:
    delete_empty_articles: true
```

## Re-applying Content Templates

This works the same way as applying content templates. However, if you want the properties of articles or content templates to be updated with the properties of the source template, you first need to explicitly define which properties should be taken into account. This can be done via the bundle configuration. For example:

```yaml
# config/config.yaml
contao_content_templates:
    copy_properties:
        tl_article:
            - customTpl
            - protected
            - groups
            - guests
            - cssID
        tl_content:
            - customTpl
            - protected
            - groups
            - guests
            - size
            - floating
            - fullsize
            - perRow
            - perPage
            - galleryTpl
            - cssID
```
