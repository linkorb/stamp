# Stamp

Stamp helps you to generate common repository files like `README.md`, `LICENSE`, `.gitignore`, `Dockerfile` etc.

## Workflow:

### 1. Load `stamp.yml` configuration:

Stamp looks for a file called `stamp.yml` in your project root (or you can specify one using `-c`). The config defines:

1. Project specific variables (i.e. license, project name, etc)
2. A list of files to be generated. Each file can have a name, template (file or url) and a set of variables, that override the project variables for this file only.

### 2. Run Analyzers

Stamp contains a set of Analyzers that scan your repository for common files (like package.json, .editorconfig, etc) and extract data from them. This data is then passed into the file templates

### 3. Generate files

Stamp loops through all the files defined in `stamp.yml`, loads their template, inserts the collected data, and saves the file to disk.

## stamp.yml example:

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

## Development / debugging:

The `example/` directory contains a collection of common files. While developing analyzers, you can run `./bin/stamp generate -c example/stamp.yml` to run stamp in the context of the `example/` directory.

You can use the following command to debug the data that will be injected into any templates (including the output from the analyzers): 

    ./bin/stamp generate -c example/stamp.yml

## Todo:

* [x] Analyzer for `Dockerfile`: Simply define a variable if it exists.
* [x] Analyzer for `docker-compose.yml`: Import the YAML as-is. Can be used to list defined containers.
* [x] Analyzer for `Makefile`: Import the targets + comments, using the regex in `example/Makefile`
* [x] Analyzer for `bower.json`: Import the JSON as-is. Can be used to list jobs
* [x] Analyzer for `.env.dist`: Import variables, their default values, and comments (line before the variable)
* [x] Analyzer for `.circleci/config.yml`: Import the YAML as-is. Can be used to list jobs
* [x] Analyzer for `.editorconfig`: Simply define a variable if it exists.
* [ ] Analyzer for `schema.xml`: Used to document schema
* [x] Analyzer for `routes`: Used to document routes. Looks for `app/config/routes.yml` (Radvance)
* [x] Analyzer for `routes`: Used to document routes. Uses symfony `bin/console debug:router --format JSON` to import route data
* [ ] Analyzer for `doctrine-schema`: Find a way to load doctrine schema into an array for entity documentation
* [x] Analyzer for `fixtures`: (Haigha). Simply define a variable if it exists.
* [x] Analyzer for `anonymizer.yml`: Simply define a variable if it exists.
* [ ] Analyzer for `github`: Request repository data like contributors, title, etc.
* [ ] Allow to use project variables in template filenames/urls (i.e. to fetch the proper license file)
* [ ] Allow to use either twig or handlebars templates (using `zordius/lightncandy`)
* [ ] Pass on the `output` variable from the Commands for debugging output in the Generator and Analyzers

