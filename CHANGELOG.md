# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

Developers
RVE = Ronald Estrada <rvestra@unm.edu>
MH = Michael Han <mhan1@unm.edu>

## [Unreleased]

## [0.1.10] - 2020-04-03 RVE
### Fixed
- Issue 1630 - empty column left out getSqlSelectItems()


## [0.1.9] - 2020-04-01 MH
### Fixed
- OauthClient helper

## [0.1.8] - 2020-04-01 - RVE
### Added
- OauthClient Helper
### Removed
- ApiModel


## [0.1.7] - 2020-03-26 - RVE
### Fixed
- Issue 1317 - package calls with ORM inaccurate results
- EnterpriseBasePckgModel how using OCI8 php

## [0.1.6] - 2020-03-19 - MH
### Updated
- formalize https verify vars via app-extra.php config var in OAuth middleware

## [0.1.5] - 2020-03-18 - MH
### Added
- accommodation for the TLS and certificate setup to use coa.unm.edu endpoint from the app itself

## [0.1.4] - 2020-03-05 - MH
### Added
- updated resources/* with latest components and files

## [0.1.3] - 2020-03-04 - MH
### Fixed
- add '.' for session variable names in APIOAuthHandler.php

## [0.1.2] - 2020-03-04 - RVE
### Added
- laravel-frontend package (ReactJS components) into ldk
- Unit tests of BusinessObjectItems, AbstractBusinessObject, EnterpriseBasePackage, and Payload

## [0.1.1] - 2020-02-07 - MH
### Fixed
- the keys of nameToColumnName & nameToBusinessName in AbstractBusinessObject were being hydrated with business names (i.g. High School Code) rather than the (identifying) names (e.g. hs_code)

## [0.1.0] - 2020-01-22 - RVE
### Added
- Merged all abstract and base components into this repo
