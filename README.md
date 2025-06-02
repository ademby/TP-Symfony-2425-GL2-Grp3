This is what you need to do after pulling a branch :

(Execute the commented scripts if first time)

```bash
composer install
cd assets && npm install && cd ..
composer run update-sqlite
```
