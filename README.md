# UnPhar
> __PHP Utility tool to extracting a Phar (PHP Archive) file in batch mode.__

## Requirements
```
PHP: 7.1.0
```

## Usage
There is two main folders: `out/` and `phars/`.  
Please put all your `.phar` files into `phars/` folder and let the `out/` folder is empty.

Execute `unphar.php` with your executable php binary.  
```
php unphar.php --override
```
_This example let the program to override existing extracted (out) file._

## Extras
This migt be useful for *nix platform to extract `.phar` file directly with command `phar`. Make sure that file `/usr/bin/phar` is available on your machine.  
```
/usr/bin/phar extract -f YourPharFile.phar /path/to/extraction
```

In real case:  
```
phar extract -f composer.phar .
```

## Credits
Author: [KennFatt](https://github.com/KennFatt)  
