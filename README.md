# Backend <===> Fronted SQL Converter #
=====================================

### Requires
* PHP 5.6 >
### Installation
 run: ``` composer install -o ```
 
### Using
 Due to the organization of relations between the tables, 
 it is recommended to run the synchronization in the order specified below:
 
 ```
    
    /usr/bin/php /var/www/atlas-beta/sync/bin/sync -t shop
    
    /usr/bin/php /var/www/atlas-beta/sync/bin/sync -t brand
    
    /usr/bin/php /var/www/atlas-beta/sync/bin/sync -t category
    
    /usr/bin/php /var/www/atlas-beta/sync/bin/sync -t tag
    
    /usr/bin/php /var/www/atlas-beta/sync/bin/sync -t product
    
    /usr/bin/php /var/www/atlas-beta/sync/bin/sync -t price
    
    /usr/bin/php /var/www/atlas-beta/sync/bin/sync -t buy
    
    /usr/bin/php /var/www/atlas-beta/sync/bin/sync -t banner
    
    /usr/bin/php /var/www/atlas-beta/sync/bin/sync -t delivery
    
    /usr/bin/php /var/www/atlas-beta/sync/bin/sync -t document
    
    /usr/bin/php /var/www/atlas-beta/sync/bin/sync -t payment
    
    /usr/bin/php /var/www/atlas-beta/sync/bin/sync -t region

    // or using this for completely load all entities:
    
    /usr/bin/php /var/www/atlas-beta/sync/bin/sync -t all
```
    
##### _-t is equal to --target_
 
### Testing
```
   phpunit
    
```