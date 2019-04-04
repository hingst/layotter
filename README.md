# Layotter

**Build fully customizable and easy to use Wordpress websites &ndash; with Layotter, the drag-and-drop page builder for developers!**

As a professional Wordpress developer, you've probably got your own theme boilerplate, a favorite grid system, project structure, &hellip; you name it. So why should your page builder dictate its own HTML or CSS structure? Why should it come with a ton of predesigned modules that won't fit your client's design anyway? We believe it shouldn't. So Layotter doesn't.

## If you like ACF, you'll love Layotter

Layotter is based on [Advanced Custom Fields (ACF)](http://www.advancedcustomfields.com), a very popular Wordpress plugin that lets you create wildly complex forms without having to write any code. Thanks to ACF, building a simple Layotter element takes as little as 15 lines of code:

```php
class Text_Element extends Layotter_Element {
    protected function attributes() {
        $this->title       = 'Text';
        $this->description = 'A very simple text element.';
        $this->icon        = 'font'; // pick an icon from Font Awesome
        $this->field_group = 'group_abc1337'; // your ACF field group
    }
    protected function frontend_view($fields) {
        echo $fields['content']; // what visitors will see
    }
    protected function backend_view($fields) {
        echo $fields['content']; // what editors will see
    }
}
Layotter::register_element('text', 'Text_Element');
```

Read the [installation instructions](http://docs.layotter.com/getting-started/installation/) to get started, or head directly to the [tutorial on how to create an element type](http://docs.layotter.com/basics/element-types/).

## Some more features you'll enjoy

* Super clean, object oriented API
* Full HTML and CSS customization
* Settings filters for programmatic configuration
* Integrates so nicely, your clients will think it's a part of Wordpress
* Works with the Pro and free versions of ACF
* Open source and free to include even in commercial themes