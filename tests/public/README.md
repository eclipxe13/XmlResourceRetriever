# About public resources

This folder is used to perform tests, is the public root folder for PHP intetnal web server.

```shell
php -S 127.0.0.1:8999 -t tests/public/
```

As I was unable to create complex structures for XSD and XSLT files I had taken from Mexico SAT Government:

- http://www.sat.gob.mx/sitio_internet/cfd/3/cfdv33.xsd
- http://www.sat.gob.mx/sitio_internet/cfd/3/cadenaoriginal_3_3/cadenaoriginal_3_3.xslt

I got the list of full urls to download in the file `sat-urls.txt`.
To get a local copy you must perform:

```shell
rm -rf www.sat.gob.mx
wget -q -r -i sat-urls.txt
find -type f -name "*.x*" -exec sed -i 's#http://www.sat.gob.mx/sitio_internet#http://localhost:8999/www.sat.gob.mx/sitio_internet#g' "{}" \;
```

To update `sat-urls.txt` you can do:

```shell
rm sat-urls.txt
echo http://www.sat.gob.mx/sitio_internet/cfd/3/cfdv33.xsd >> sat-urls.txt
wget -O - -q sat-urls.txt http://www.sat.gob.mx/sitio_internet/cfd/3/cfdv33.xsd \
  | grep -o -P 'http://www.sat.gob.mx/\S*?xsd' >> sat-urls.txt
echo http://www.sat.gob.mx/sitio_internet/cfd/3/cadenaoriginal_3_3/cadenaoriginal_3_3.xslt >> sat-urls.txt
wget -O - -q sat-urls.txt http://www.sat.gob.mx/sitio_internet/cfd/3/cadenaoriginal_3_3/cadenaoriginal_3_3.xslt \
  | grep -o -P 'http://www.sat.gob.mx/\S*?xslt' >> sat-urls.txt
```
