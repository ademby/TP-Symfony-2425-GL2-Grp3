This is what you need to do after pulling a branch :
```bash
composer install
cd assets && npm install && cd ..
composer run update-sqlite
```
