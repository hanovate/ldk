## About Laravel Development Kit

Developers
- Ron Estrada [rvestra@unm.edu](mailto:rvestra@unm.edu)
- Michael Han [mhan1@unm.edu](mailto:mhan1@unm.edu)

## Revelant links

- [APP - Laravel Development Kit (LDK) Dashboard](https://confluence.unm.edu/x/sYZ_BQ)

## Laravel Version Compatibility

 Laravel  | Package
:---------|:----------
 6.x.x    | 1.0.x

## Getting Started
Steps:
1. Load the Package from the Main Composer.JSON File
Now, the“composer.json” file for every Laravel application is present in the root directory.  We need to make our package visible to the application.
 Add the repository to you application composer.json
```bash
"license": "MIT",
  "repositories": [
    {
      "type": "package",
      "package": {
        "name": "unmit/ldk",
        "version": "v1.0.0",
        "source": {
          "url": "UNMIT@vs-ssh.visualstudio.com:v3/UNMIT/laravel-development-kit/laravel-development-kit",
          "type": "git",
          "reference": "v1.0.0"
        }
      }
    }
  ],
  "require": {
```
2.  Add the "composer.json" require call
```bash
,
    "require": {
	...
	,
        "unmit/ldk": "v1.0.0",
```
3.  Add the namespace of our package in “autoload > psr-4”
```bash
    "autoload": {
        "classmap": [
	...
        ],
        "psr-4": {
            "App\\": "app/",
            "Unmit\\ldk\\": "vendor/unmit/ldk/src/",
			....
        }
    },
```

##  User Documentation

[Laravel Development Kit (LDK) Dashboard](http://https://confluence.unm.edu/display/APPGS/APP+-+Laravel+Development+Kit+%28LDK%29+Dashboard "APPS - Laravel Development Kit (LDK) Dashboard")

[Laravel Work Instructions](http://https://confluence.unm.edu/display/TECH/APPS+-+Laravel+Work+Instructions "UNM IT - Laravel Work Instructions")
## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
