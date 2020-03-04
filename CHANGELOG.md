# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

Developers
RVE = Ronald Estrada <rvestra@unm.edu>
MH = Michael Han <mhan1@unm.edu>

## [Unreleased]

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
