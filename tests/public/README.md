This folder is used to perform tests, is the public root folder for PHP intetnal web server.

```shell
php -S 127.0.0.1:8999 -t tests/public/
```

As I was unable to create complex structures for XSD and XSLT files, I had taken from
Mexico SAT Goverment site base urls:

- http://www.sat.gob.mx/sitio_internet/cfd/3/cfdv33.xsd
- http://www.sat.gob.mx/sitio_internet/cfd/3/cadenaoriginal_3_3/cadenaoriginal_3_3.xslt

I got the list of full urls to download in the file `sat-urls.txt`.
To get a local copy you must perform:

```shell
rm -rf www.sat.gob.mx
wget -r -i urls.txt
find -type f -name "*.x*" -exec sed -i 's/www.sat.gob.mx\/sitio_internet/localhost:8999\/www.sat.gob.mx\/sitio_internet/g' "{}" \;
```



