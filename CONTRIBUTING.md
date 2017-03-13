## Setup

1. `$ git clone git@github.com:Astoundify/wp-job-manager-regions.git && cd
	 wp-job-manager-regions`

## Making Changes

`master` branch is always what is currently deployed on WordPress.org, which means nothing should be committed directly to this branch.

### Fixing an Issue or Adding a Feature

1. All commits should relate to an existing issue on Github.
2. Create a new based off the current release branch related to the issue number. For example `issue/123`
3. Add your changes.
4. Open a Pull Request against the next release branch.
5. Assign at least one reviewer other than yourself to the Pull Request.
6. Once reviewed the reviewer can merge the feature in to the `release/x.x.x` branch.

### Creating a Release

1. Create a `release/x.x.x` branch off of master.
2. Add features or fix bugs. See sections below.
3. Assign at least one reviewer other than yourself to the Pull Request.
4. Once reviewed the reviewer can merge the release in to the `master` branch.

## Versioning

[Semantic Versioning](http://semver.org/) is used. Any release that makes a
change that is not a regression from the previously release should be a minor
release. 
