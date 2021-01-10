# Mono-Repo

Idea of the mono-repo is to combine all development work for websites, apis and assets into a single repo to share resources and help keep identity assets inline with each other.

## Targeted Browsers

- IE 10+
- Edge
- Chrome
- Safari
- Firefox

## Contributing

When contributing to the project, please follow best coding practices and standards.

- [GitFlow](https://www.atlassian.com/git/tutorials/comparing-workflows/gitflow-workflow)
- [HTML Styleguide](https://google.github.io/styleguide/htmlcssguide.html)
- [Wordpress PHP Coding Standards](https://make.wordpress.org/core/handbook/best-practices/coding-standards/php/)

## Release + Versioning

Cut a release branch off of develop with the name following the format `release/[PROJECT]/vx.x.yymms-rcx`.
Project is a simple name for the project.

- `wordpress` - for Wordpress

The versioning follows `[MAJOR].[MINOR].[YEAR][MONTH][SPRINT_NUMBER]-rc[RELEASE_CANDIDATE]`.

Release Candidates start with 1 and increment whenever a patch is loaded to production.

Update necessary `package.json` files with the proper release versions and commit to new branch.
Merge release branch into `master` and tag the branch.
Master is then merged down into `dev` to update develop with new versions and patches.

## Resources

- [Editor Config](https://marketplace.visualstudio.com/items?itemName=EditorConfig.EditorConfig)
- [Wordpress Coding Standards](https://github.com/WordPress/WordPress-Coding-Standards)

### Styles (Scss)

- [Scss](https://sass-lang.com/documentation/file.SASS_REFERENCE.html)

### Testing

- [PHPUnit](https://phpunit.de/)

### Linting

- [Prettier](https://prettier.io)
- [PHP Code Sniffer](https://github.com/squizlabs/PHP_CodeSniffer)
- [PHP Code Sniffer Setup](https://javorszky.co.uk/2018/07/30/set-up-phpcs-and-wordpress-extra-coding-standards-and-configure-your-ides-to-use-them/)

### Local Development

- [Docker](https://github.com/chriszarate/docker-compose-wordpress)

### Deployment

- [Docker](https://www.docker.com/)
- [GitLab](https://docs.gitlab.com/ee/ci/yaml/README.html)
