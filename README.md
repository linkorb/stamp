# Stamp

Stamp generate files by applying data to templates.

## Use-cases:

* Generate common repository files like README.md, LICENSE, .gitignore, etc (by combining stamp with [metaculous](https://github.com/linkorb/metaculous))
* Static site generator
* Documentation generator

## Installation

    composer require linkorb/stamp --dev

## Usage

    vendor/bin/stamp --help

## How does it work?

When you run `stamp generate`, Stamp will look for it's configuration in a file called `stamp.yaml` in the current directory. You can also pass a specify config file using `-c`.

Stamp will then loop through the `templates` defined in the config file, and use the template files defined by the `src` key, and generate the file defined by the `dest` key.

By specifying an `items` key, one template may be applied multiple times, resulting in multiple output files.

By specifying a `variables` key, the variables at the template level will get merged with the project level variables before being passed to the template, allowing you to override/add variables at the template level. 

## stamp.yml example:

Here's a simple example `stamp.yml` file:

```yml
variables:
  title: Hello world
  license: mit

templates:
  - src: stamp/README.md.twig
    dest: README.md
    variables:
      title: Hello world README file

  - src: https://raw.githubusercontent.com/IQAndreas/markdown-licenses/master/{{ license }}.md
    dest: LICENSE
  
  - src: https://raw.githubusercontent.com/gitlabhq/gitlabhq/master/CONTRIBUTING.md
    dest: CONTRIBUTING.md
```

Simply type `stamp generate` (or `vendor/bin/stamp generate`) in the root of your project, and the listed files will be (re)generated based on their templates.

Using URLs as templates allow you to manage your templates in one location (a git repository), making it easy to update your projects based on updated templates.

Stamp supports multiple template languages/engines, which will be used based on the template file (src) file extension:

* `.twig`: Use the [Twig](https://twig.symfony.com/) template language
* `.hbs`, `.handlebars`: Use the [Handlebars](https://handlebarsjs.com/) template language (powered by [LightnCandy](https://github.com/zordius/lightncandy))
* `.mustache`: Use the [Mustache](https://mustache.github.io/) template language (powered by [LightnCandy](https://github.com/zordius/lightncandy))

## Variables and functions

Stamp is using the [LinkORB Loader](https://github.com/linkorb/loader) library for loading the stamp.yaml file.

It therefor supports all features related to variables, includes and functions that the loader does.

## Development / debugging:

The `examples/` directory contains an example configuration (`stamp.yaml`) and template files.

## License

MIT. Please refer to the [license file](LICENSE) for details.

## Brought to you by the LinkORB Engineering team

<img src="http://www.linkorb.com/d/meta/tier1/images/linkorbengineering-logo.png" width="200px" /><br />
Check out our other projects at [linkorb.com/engineering](http://www.linkorb.com/engineering).

Btw, we're hiring!


