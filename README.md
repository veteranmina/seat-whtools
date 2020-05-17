![SeAT](http://i.imgur.com/aPPOxSK.png)
# WH-Tools 

## Useful tools aimed at helping manage WH Corporation activities.
Curently this is a W.I.P. based on SeAT found [here](https://github.com/eveseat/seat).

Original plugin - I pulled the stock code from here for just the stock section and to custom code it.

***Important**: seat-whtools is a work in progress and may have some bugs
please do report any findings to seat-slack and report it as an issue and please be patient as I am new to the developer scene*

### Installation

```
php artisan down
composer require veteranmina/contractstock

php artisan vendor:publish --force --all
php artisan migrate

php artisan up
```

### Fittings/Doctine Corporation Stocking
This addon allows corporations to monitor then number of doctrine fits that they have stocked within their corporation contracts, given that the contract title matches the syntax.  *\<shiptype\> \<fitname\>* e.g ***Vexor Ratter One***.  And requires the use of dysath/seat-fitting plugin found [here](https://github.com/dysath/seat-fitting).

Also allows you to see the fit linked with the desired stocking level.

#### Screen Shot
![Stocking](https://i.imgur.com/kzlKHd6.png)