# Stamp

Stamp helps you to generate common repository files like `README.md`, `LICENSE`, `.gitignore`, `Dockerfile` etc.

Stamp looks for a file called `stamp.yml` in your project root, and uses it to generate a list of files listed in there.

Each file can have a name, template (file or url) and a set of variables, that can be used in the template.

Here's a simple example `stamp.yml` file:

```yml
variables:
  project:
    title: Hello world

files:
  README.md:
    template: stamp/README.md.twig
    variables:
      title: Hello world
      blocks:
        - "@doc/intro.md"
        - "@doc/installation.md"

  LICENSE.md:
    template: https://raw.githubusercontent.com/IQAndreas/markdown-licenses/master/mit.md
  
  CONTRIBUTING.md:
    template: https://raw.githubusercontent.com/gitlabhq/gitlabhq/master/CONTRIBUTING.md
```

Simply type `stamp generate` (or `vendor/bin/stamp generate`) in the root of your project, and the listed files will be (re)generated based on their templates.

Using URLs as templates allow you to manage your templates in one location, making it easy to update your projects based on updated templates.

When the template files end in `.twig`, Stamp will use Twig to process the template based on the variables defined on the file and globally on the project.

