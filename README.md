This is what you need to do after pulling a branch :

(Execute the commented scripts if first time)

```bash
#rm composer.lock
#composer update
#cd assets && npm install && cd ..
composer run update-sqlite
```
WinCmd
```bash
#del composer.lock
#composer update
#cd assets && npm install && cd ..
composer run update-sqlite
```
